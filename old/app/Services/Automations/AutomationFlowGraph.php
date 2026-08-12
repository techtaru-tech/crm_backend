<?php

declare(strict_types=1);

namespace App\Services\Automations;

use App\Models\Automation;
use App\Models\AutomationStep;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Converts between an Automation (trigger + ordered automation_steps) and the
 * node-graph the visual Flow Builder works with — WITHOUT changing the
 * execution engine. automation_steps stay the single source of truth; node
 * positions live in the presentation-only automations.nodes_layout column,
 * which RunAutomation never reads.
 *
 * MVP contract: a single LINEAR chain (the engine is linear — a failed
 * condition stops the run; there is no fork), with exactly one trigger node at
 * the head. True branching is a future, additive engine extension.
 *
 * Normalized graph shape (the Livewire/Drawflow bridge maps the canvas export
 * to/from this — keeping the JS library swappable and this layer unit-testable):
 *   [
 *     'nodes' => [
 *       ['id'=>'n1','kind'=>'trigger'|'action'|'condition'|'delay','config'=>[...],'x'=>..,'y'=>..],
 *       ...
 *     ],
 *     'edges'    => [ ['from'=>'n1','to'=>'n2'], ... ],
 *     'viewport' => ['x'=>0,'y'=>0,'zoom'=>1],
 *   ]
 */
class AutomationFlowGraph
{
    private const DELAY_UNITS = ['minutes', 'hours', 'days'];

    /**
     * Persist a node graph onto the automation: the trigger node -> the
     * automation's trigger columns, the linear chain -> ordered
     * automation_steps, node positions -> nodes_layout. Fully atomic.
     *
     * @throws InvalidArgumentException on any invalid or unsupported graph.
     */
    public function apply(Automation $automation, array $graph): void
    {
        $nodes = $this->indexNodes($graph['nodes'] ?? []);
        $out   = $this->normalizeEdges($graph['edges'] ?? []);

        $trigger = $this->extractTrigger($nodes);
        $ordered = $this->orderChain($trigger['id'], $nodes, $out);

        foreach ($ordered as $node) {
            $this->validateStepNode($node);
        }

        $triggerType = (string) ($trigger['config']['trigger_type'] ?? '');
        if (! array_key_exists($triggerType, Automation::TRIGGERS)) {
            throw new InvalidArgumentException("Unknown trigger type: {$triggerType}");
        }
        $triggerConfig = (array) ($trigger['config']['trigger_config'] ?? []);

        $layout = [
            'trigger'  => $this->pos($trigger),
            'steps'    => array_map(fn (array $n) => $this->pos($n), $ordered),
            'viewport' => $this->normalizeViewport($graph['viewport'] ?? []),
        ];

        DB::transaction(function () use ($automation, $triggerType, $triggerConfig, $ordered, $layout) {
            $automation->forceFill([
                'trigger_type'   => $triggerType,
                'trigger_config' => $triggerConfig,
                'nodes_layout'   => $layout,
            ])->save();

            $automation->steps()->delete();

            foreach ($ordered as $i => $node) {
                $automation->steps()->create([
                    'type'       => $node['kind'],
                    'config'     => $this->stepConfig($node),
                    'sort_order' => $i,
                ]);
            }
        });
    }

    /**
     * Build the node graph for the canvas from the automation's trigger +
     * ordered steps, restoring saved positions (or an auto-layout when absent).
     */
    public function toGraph(Automation $automation): array
    {
        $layout  = (array) ($automation->nodes_layout ?? []);
        $stepPos = (array) ($layout['steps'] ?? []);

        $triggerPos = (array) ($layout['trigger'] ?? []);
        $nodes = [[
            'id'     => 'trigger',
            'kind'   => 'trigger',
            'config' => [
                'trigger_type'   => $automation->trigger_type,
                'trigger_config' => (array) ($automation->trigger_config ?? []),
            ],
            'x' => (int) ($triggerPos['x'] ?? 40),
            'y' => (int) ($triggerPos['y'] ?? 120),
        ]];
        $edges  = [];
        $prevId = 'trigger';

        foreach ($automation->steps()->orderBy('sort_order')->get() as $i => $step) {
            $id  = 'step_' . $step->id;
            $pos = (array) ($stepPos[$i] ?? []);
            $nodes[] = [
                'id'     => $id,
                'kind'   => $step->type,
                'config' => (array) ($step->config ?? []),
                'x'      => (int) ($pos['x'] ?? 40 + ($i + 1) * 260),
                'y'      => (int) ($pos['y'] ?? 120),
            ];
            $edges[] = ['from' => $prevId, 'to' => $id];
            $prevId  = $id;
        }

        return [
            'nodes'    => $nodes,
            'edges'    => $edges,
            'viewport' => $this->normalizeViewport((array) ($layout['viewport'] ?? [])),
        ];
    }

    // ---- internals --------------------------------------------------------

    /** @return array<string, array> id => node */
    private function indexNodes(array $nodes): array
    {
        $indexed = [];
        foreach ($nodes as $node) {
            $id = (string) ($node['id'] ?? '');
            if ($id === '') {
                throw new InvalidArgumentException('Every node needs an id.');
            }
            $indexed[$id] = [
                'id'     => $id,
                'kind'   => (string) ($node['kind'] ?? ''),
                'config' => (array) ($node['config'] ?? []),
                'x'      => $node['x'] ?? 0,
                'y'      => $node['y'] ?? 0,
            ];
        }
        if ($indexed === []) {
            throw new InvalidArgumentException('The flow is empty — add a trigger to start.');
        }
        return $indexed;
    }

    /** @return array<string, string> from => to (MVP: at most one out-edge per node) */
    private function normalizeEdges(array $edges): array
    {
        $out = [];
        foreach ($edges as $edge) {
            $from = (string) ($edge['from'] ?? '');
            $to   = (string) ($edge['to'] ?? '');
            if ($from === '' || $to === '') {
                continue;
            }
            if (isset($out[$from])) {
                throw new InvalidArgumentException('Branching is not supported yet — each step may have only one next step.');
            }
            $out[$from] = $to;
        }
        return $out;
    }

    private function extractTrigger(array $nodes): array
    {
        $triggers = array_values(array_filter($nodes, fn (array $n) => $n['kind'] === 'trigger'));
        if (count($triggers) !== 1) {
            throw new InvalidArgumentException('A flow must have exactly one trigger.');
        }
        return $triggers[0];
    }

    /**
     * Walk the single chain from the trigger and return the ordered step nodes
     * (trigger excluded). Guards against loops, forks, orphans and a stray
     * second trigger.
     *
     * @return array<int, array>
     */
    private function orderChain(string $triggerId, array $nodes, array $out): array
    {
        $ordered = [];
        $seen    = [$triggerId => true];
        $current = $out[$triggerId] ?? null;

        while ($current !== null) {
            if (! isset($nodes[$current])) {
                throw new InvalidArgumentException("An edge points to an unknown node: {$current}");
            }
            if (isset($seen[$current])) {
                throw new InvalidArgumentException('The flow contains a loop.');
            }
            if ($nodes[$current]['kind'] === 'trigger') {
                throw new InvalidArgumentException('A flow can only have one trigger, at the start.');
            }
            $seen[$current] = true;
            $ordered[]      = $nodes[$current];
            $current        = $out[$current] ?? null;
        }

        if (count($seen) !== count($nodes)) {
            throw new InvalidArgumentException('Every step must be connected to the trigger in a single chain.');
        }

        return $ordered;
    }

    private function validateStepNode(array $node): void
    {
        switch ($node['kind']) {
            case 'action':
                $type = (string) ($node['config']['action_type'] ?? '');
                if (! array_key_exists($type, AutomationStep::ACTION_TYPES)) {
                    throw new InvalidArgumentException("Unknown action type: {$type}");
                }
                break;
            case 'condition':
                $type = (string) ($node['config']['type'] ?? '');
                if (! array_key_exists($type, AutomationStep::CONDITION_TYPES)) {
                    throw new InvalidArgumentException("Unknown condition type: {$type}");
                }
                break;
            case 'delay':
                $unit = (string) ($node['config']['unit'] ?? 'minutes');
                if (! in_array($unit, self::DELAY_UNITS, true)) {
                    throw new InvalidArgumentException("Unknown delay unit: {$unit}");
                }
                if ((int) ($node['config']['amount'] ?? 0) < 1) {
                    throw new InvalidArgumentException('A delay must be at least 1.');
                }
                break;
            default:
                throw new InvalidArgumentException("Unsupported node kind: {$node['kind']}");
        }
    }

    private function stepConfig(array $node): array
    {
        if ($node['kind'] === 'delay') {
            return [
                'amount' => (int) ($node['config']['amount'] ?? 1),
                'unit'   => (string) ($node['config']['unit'] ?? 'minutes'),
            ];
        }
        return $node['config'];
    }

    private function pos(array $node): array
    {
        return ['x' => (int) ($node['x'] ?? 0), 'y' => (int) ($node['y'] ?? 0)];
    }

    private function normalizeViewport(array $viewport): array
    {
        return [
            'x'    => (float) ($viewport['x'] ?? 0),
            'y'    => (float) ($viewport['y'] ?? 0),
            'zoom' => (float) ($viewport['zoom'] ?? 1),
        ];
    }
}
