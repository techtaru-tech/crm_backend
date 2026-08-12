{{-- Visual Automation Flow Builder (2.0). A Drawflow canvas over the existing
     automation engine: it loads the node graph from AutomationFlowGraph::toGraph
     and, on Save, posts the normalized graph back to the tested saveGraph()
     server method (which writes the SAME automation_steps the classic editor
     does). Vendored Drawflow — no build step, shared-hosting safe. --}}
<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('vendor/drawflow/drawflow.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filament/automation-builder.css') }}">

    <div class="lh-flow-wrap" wire:ignore>
        <div class="lh-flow-toolbar">
            <span class="lh-flow-palette-label">{{ __('flow_builder.add_step') }}</span>
            <button type="button" class="lh-flow-btn" data-add="action">{{ __('flow_builder.add_action') }}</button>
            <button type="button" class="lh-flow-btn" data-add="condition">{{ __('flow_builder.add_condition') }}</button>
            <button type="button" class="lh-flow-btn" data-add="delay">{{ __('flow_builder.add_delay') }}</button>
            <button type="button" class="lh-flow-btn lh-flow-btn--ghost" id="lh-flow-delete">{{ __('flow_builder.delete_selected') }}</button>
            <span class="lh-flow-spacer"></span>
            <button type="button" class="lh-flow-btn lh-flow-btn--primary" id="lh-flow-save">{{ __('flow_builder.save_flow') }}</button>
        </div>

        <div class="lh-flow-stage">
            <div id="lh-drawflow" class="lh-drawflow"></div>

            <aside class="lh-flow-inspector" id="lh-flow-inspector" hidden>
                <h3 class="lh-flow-inspector-title">{{ __('flow_builder.step_settings') }}</h3>
                <div id="lh-flow-inspector-body"></div>
                <p class="lh-flow-hint">{{ __('flow_builder.inspector_hint') }}</p>
            </aside>
        </div>
    </div>

    <script src="{{ asset('vendor/drawflow/drawflow.min.js') }}"></script>
    <script>
        (function () {
            const initialGraph = @js($this->graph);
            const catalog = @js($catalog);
            const strings = @js([
                'when'      => __('flow_builder.summary_when'),
                'doStep'    => __('flow_builder.summary_do'),
                'ifStep'    => __('flow_builder.summary_if'),
                'wait'      => __('flow_builder.summary_wait'),
                'elseStop'  => __('flow_builder.summary_else_stop'),
                'trigger'   => __('flow_builder.field_trigger'),
                'action'    => __('flow_builder.field_action'),
                'condition' => __('flow_builder.field_condition'),
                'value'     => __('flow_builder.field_value'),
                'amount'    => __('flow_builder.field_amount'),
                'unit'      => __('flow_builder.field_unit'),
                'minutes'   => __('flow_builder.unit_minutes'),
                'hours'     => __('flow_builder.unit_hours'),
                'days'      => __('flow_builder.unit_days'),
            ]);

            function ready(fn) {
                if (document.readyState !== 'loading') { fn(); }
                else { document.addEventListener('DOMContentLoaded', fn); }
            }

            ready(function () {
                const host = document.getElementById('lh-drawflow');
                if (!host || typeof Drawflow === 'undefined') { return; }

                const editor = new Drawflow(host);
                editor.reroute = true;
                editor.start();

                const labelFor = (map, key) => (map && map[key]) ? map[key] : (key || '—');

                function summary(kind, config) {
                    config = config || {};
                    if (kind === 'trigger') {
                        return strings.when + ' ' + labelFor(catalog.triggers, config.trigger_type);
                    }
                    if (kind === 'action') {
                        return strings.doStep + ' ' + labelFor(catalog.actions, config.action_type);
                    }
                    if (kind === 'condition') {
                        const v = (config.value !== undefined && config.value !== '') ? (' = ' + config.value) : '';
                        // A condition is a GATE: matches -> continue, else the run stops.
                        return strings.ifStep + ' ' + labelFor(catalog.conditions, config.type) + v + ' ' + strings.elseStop;
                    }
                    if (kind === 'delay') {
                        return strings.wait + ' ' + (config.amount || 1) + ' ' + (config.unit || 'minutes');
                    }
                    return kind;
                }

                function nodeHtml(kind, config) {
                    const safe = (s) => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
                    return '<div class="lh-node lh-node--' + kind + '">'
                        + '<div class="lh-node-kind">' + kind + '</div>'
                        + '<div class="lh-node-body">' + safe(summary(kind, config)) + '</div>'
                        + '</div>';
                }

                const idMap = {};

                // ---- import the saved graph -------------------------------
                (initialGraph.nodes || []).forEach(function (n) {
                    const inputs = n.kind === 'trigger' ? 0 : 1;
                    const dfId = editor.addNode(
                        n.kind, inputs, 1, n.x || 40, n.y || 120,
                        'lh-flow-node', { kind: n.kind, config: n.config || {} },
                        nodeHtml(n.kind, n.config || {})
                    );
                    idMap[n.id] = dfId;
                });
                (initialGraph.edges || []).forEach(function (e) {
                    if (idMap[e.from] && idMap[e.to]) {
                        try { editor.addConnection(idMap[e.from], idMap[e.to], 'output_1', 'input_1'); } catch (_) {}
                    }
                });

                // ---- palette -------------------------------------------------
                function defaultConfig(kind) {
                    if (kind === 'action') { return { action_type: Object.keys(catalog.actions)[0] }; }
                    if (kind === 'condition') { return { type: Object.keys(catalog.conditions)[0], value: '' }; }
                    if (kind === 'delay') { return { amount: 1, unit: 'hours' }; }
                    return {};
                }
                document.querySelectorAll('[data-add]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const kind = btn.getAttribute('data-add');
                        const cfg = defaultConfig(kind);
                        const x = 120 + Math.round(Math.random() * 120);
                        const y = 240 + Math.round(Math.random() * 120);
                        editor.addNode(kind, 1, 1, x, y, 'lh-flow-node', { kind: kind, config: cfg }, nodeHtml(kind, cfg));
                    });
                });

                // ---- inspector (per-node config) -----------------------------
                const inspector = document.getElementById('lh-flow-inspector');
                const inspectorBody = document.getElementById('lh-flow-inspector-body');
                let selectedId = null;

                function hideInspector() { inspector.hidden = true; inspectorBody.innerHTML = ''; }

                document.getElementById('lh-flow-delete').addEventListener('click', function () {
                    if (selectedId) { editor.removeNodeId('node-' + selectedId); selectedId = null; hideInspector(); }
                });

                function refreshNodeHtml(dfId, kind, config) {
                    const el = document.querySelector('#node-' + dfId + ' .lh-node-body');
                    if (el) { el.textContent = summary(kind, config); }
                }

                function selectField(label, options, value, onChange) {
                    const wrap = document.createElement('label');
                    wrap.className = 'lh-flow-field';
                    wrap.innerHTML = '<span>' + label + '</span>';
                    const sel = document.createElement('select');
                    Object.keys(options).forEach(function (k) {
                        const o = document.createElement('option');
                        o.value = k; o.textContent = options[k];
                        if (k === value) { o.selected = true; }
                        sel.appendChild(o);
                    });
                    sel.addEventListener('change', function () { onChange(sel.value); });
                    wrap.appendChild(sel);
                    return wrap;
                }

                function textField(label, value, onChange, type) {
                    const wrap = document.createElement('label');
                    wrap.className = 'lh-flow-field';
                    wrap.innerHTML = '<span>' + label + '</span>';
                    const input = document.createElement('input');
                    input.type = type || 'text';
                    input.value = (value === undefined || value === null) ? '' : value;
                    input.addEventListener('input', function () { onChange(input.value); });
                    wrap.appendChild(input);
                    return wrap;
                }

                function openInspector(dfId) {
                    const node = editor.getNodeFromId(dfId);
                    if (!node) { return; }
                    const kind = node.data.kind;
                    const config = Object.assign({}, node.data.config || {});
                    selectedId = dfId;
                    inspectorBody.innerHTML = '';

                    function commit() {
                        editor.updateNodeDataFromId(dfId, { kind: kind, config: config });
                        refreshNodeHtml(dfId, kind, config);
                    }

                    if (kind === 'trigger') {
                        inspectorBody.appendChild(selectField(strings.trigger, catalog.triggers, config.trigger_type, function (v) { config.trigger_type = v; commit(); }));
                    } else if (kind === 'action') {
                        inspectorBody.appendChild(selectField(strings.action, catalog.actions, config.action_type, function (v) { config.action_type = v; commit(); }));
                    } else if (kind === 'condition') {
                        inspectorBody.appendChild(selectField(strings.condition, catalog.conditions, config.type, function (v) { config.type = v; commit(); }));
                        inspectorBody.appendChild(textField(strings.value, config.value, function (v) { config.value = v; commit(); }));
                    } else if (kind === 'delay') {
                        inspectorBody.appendChild(textField(strings.amount, config.amount, function (v) { config.amount = parseInt(v || '1', 10); commit(); }, 'number'));
                        inspectorBody.appendChild(selectField(strings.unit, { minutes: strings.minutes, hours: strings.hours, days: strings.days }, config.unit || 'minutes', function (v) { config.unit = v; commit(); }));
                    }
                    inspector.hidden = false;
                }

                editor.on('nodeSelected', function (id) { openInspector(id); });

                // ---- save ----------------------------------------------------
                document.getElementById('lh-flow-save').addEventListener('click', function () {
                    const data = (editor.export().drawflow.Home.data) || {};
                    const nodes = [];
                    const edges = [];
                    Object.keys(data).forEach(function (dfId) {
                        const nd = data[dfId];
                        nodes.push({
                            id: 'n' + dfId,
                            kind: (nd.data && nd.data.kind) || 'action',
                            config: (nd.data && nd.data.config) || {},
                            x: Math.round(nd.pos_x || 0),
                            y: Math.round(nd.pos_y || 0),
                        });
                        const outs = (nd.outputs && nd.outputs.output_1 && nd.outputs.output_1.connections) || [];
                        outs.forEach(function (c) { edges.push({ from: 'n' + dfId, to: 'n' + c.node }); });
                    });
                    const viewport = { x: editor.canvas_x || 0, y: editor.canvas_y || 0, zoom: editor.zoom || 1 };
                    @this.saveGraph({ nodes: nodes, edges: edges, viewport: viewport });
                });
            });
        })();
    </script>
</x-filament-panels::page>
