<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;

class ApiDocsController extends \App\Http\Controllers\Controller
{
    public function index(): Response
    {
        $spec = $this->buildSpec();
        return response(view('api.docs', compact('spec'))->render(), 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function buildSpec(): string
    {
        // API version prefix is sourced from config so the docs auto-
        // update when LEADHUB_API_VERSION is bumped (no code edit
        // required).  Defaults to 'v1' so installs that haven't seen
        // the new env line yet keep working unchanged.
        $apiVersion = (string) config('leadhub.api_version', 'v1');
        $base       = url('/api/' . $apiVersion);

        $idParam = ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']];

        $paginationParams = [
            ['name' => 'per_page', 'in' => 'query', 'description' => 'Items per page (max 100)', 'schema' => ['type' => 'integer', 'default' => 15, 'maximum' => 100]],
            ['name' => 'page',     'in' => 'query', 'description' => 'Page number',              'schema' => ['type' => 'integer', 'default' => 1]],
        ];

        $stdResponses = [
            '401' => ['description' => 'Unauthorized — invalid or missing API key', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
            '403' => ['description' => 'Forbidden — API key lacks required scope',  'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
            '404' => ['description' => 'Not found',                                 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
            '422' => ['description' => 'Validation error',                          'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ValidationError']]]],
            '429' => ['description' => 'Rate limit exceeded'],
        ];

        $spec = [
            'openapi' => '3.1.0',
            'info' => [
                'title'       => 'LeadHub REST API',
                'version'     => config('leadhub.version', '1.0.0'),
                'description' => implode("\n\n", [
                    'Full-featured leads management REST API.',
                    '## Base URL & Versioning',
                    sprintf(
                        '**The current API version is `%s`. Every documented endpoint below is rooted at `%s/...`.**',
                        $apiVersion,
                        $base,
                    ),
                    sprintf(
                        'Always prepend `/api/%s/` to the paths shown in this document. For example, the leads index endpoint listed as `/leads` is reachable at `%s/leads`.',
                        $apiVersion,
                        $base,
                    ),
                    'A small set of resources (currently `/api/leads`) are also exposed without the version prefix as a convenience for no-code platforms (Pabbly Connect, Zapier, Make, n8n) that don\'t follow HTTP 308 redirects on POST requests. Both forms accept the same payload and resolve to the same controller — the versioned form is the canonical recommendation.',
                    sprintf(
                        'When a future v2 ships, the operator bumps `LEADHUB_API_VERSION` in `.env` and this banner, the Swagger "Servers" base URL, and every Try-It-Out call updates automatically. Until then this page shows `%s`.',
                        $apiVersion,
                    ),
                    '## Authentication',
                    'All endpoints (except `/health`) require an `Authorization: Bearer <api_key>` header.',
                    'API keys are scoped — each key only grants access to specific resources and actions.',
                    '## Scopes',
                    '| Scope | Description |',
                    '|-------|-------------|',
                    '| `read:leads` | Read leads |',
                    '| `write:leads` | Create and update leads |',
                    '| `delete:leads` | Delete leads |',
                    '| `read:pipelines` | Read pipelines and stages |',
                    '| `write:pipelines` | Create, update, delete pipelines and stages |',
                    '| `read:tags` | Read tags |',
                    '| `write:tags` | Create and delete tags |',
                    '| `read:users` | Read users |',
                    '| `write:users` | Create & update users |',
                    '| `delete:users` | Delete users |',
                    '| `read:forms` | Read forms |',
                    '| `write:forms` | Create, update & submit forms |',
                    '| `delete:forms` | Delete forms |',
                    '| `read:automations` | Read automations |',
                    '| `write:automations` | Create & update automations |',
                    '| `delete:automations` | Delete automations |',
                    '| `read:integrations` | Read integrations |',
                    '| `write:integrations` | Create, update & delete integrations |',
                    '## Pagination',
                    'Paginated endpoints return a `meta` object with `current_page`, `last_page`, `per_page`, and `total`.',
                    '## Outbound Webhooks',
                    'Configure webhooks in Settings → Outbound Webhooks. Events: `lead.created`, `lead.updated`, `lead.deleted`, `lead.stage_changed`, `form.submitted`, `automation.triggered`.',
                    'Payloads are signed with `X-LeadHub-Signature: sha256=<hmac>` using your webhook secret.',
                ]),
                'contact' => ['name' => 'LeadHub Support', 'url' => 'https://leadhub.io/support'],
                'license' => ['name' => 'Commercial', 'url' => 'https://leadhub.io/license'],
            ],
            'servers' => [['url' => $base, 'description' => "LeadHub API {$apiVersion}"]],
            'security' => [['BearerAuth' => []]],
            'tags' => [
                ['name' => 'System',      'description' => 'Health & status checks'],
                ['name' => 'Leads',       'description' => 'Lead management (create, read, update, delete, tag)'],
                ['name' => 'Pipelines',   'description' => 'Pipeline and stage management'],
                ['name' => 'Tags',        'description' => 'Tag management'],
                ['name' => 'Users',       'description' => 'User listing'],
                ['name' => 'Forms',       'description' => 'Form listing and public submissions'],
                ['name' => 'Automations',   'description' => 'Automation listing'],
                ['name' => 'Integrations', 'description' => 'CRM & marketing integration management (30+ types)'],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => ['type' => 'http', 'scheme' => 'bearer', 'bearerFormat' => 'LeadHub API Key'],
                ],
                'schemas' => [
                    'Lead' => [
                        'type' => 'object',
                        'properties' => [
                            'id'               => ['type' => 'integer', 'readOnly' => true],
                            'first_name'       => ['type' => 'string'],
                            'last_name'        => ['type' => 'string', 'nullable' => true],
                            'email'            => ['type' => 'string', 'format' => 'email', 'nullable' => true],
                            'phone'            => ['type' => 'string', 'nullable' => true],
                            'source'           => ['type' => 'string', 'example' => 'facebook'],
                            'status'           => ['type' => 'string', 'enum' => ['new', 'contacted', 'qualified', 'converted', 'lost'], 'default' => 'new'],
                            'is_starred'       => ['type' => 'boolean', 'default' => false],
                            'lead_score'       => ['type' => 'integer', 'readOnly' => true],
                            'pipeline_id'      => ['type' => 'integer', 'nullable' => true],
                            'pipeline_stage_id'=> ['type' => 'integer', 'nullable' => true],
                            'assigned_user_id' => ['type' => 'integer', 'nullable' => true],
                            'custom_fields'    => [
                                'type' => 'object',
                                'additionalProperties' => true,
                                'description' => 'Free-form key/value object. Each key is a custom-field name (string), each value is whatever you stored on create/update. Returned exactly as written.',
                                'example' => ['platform' => 'Facebook', 'utm_source' => 'fb-leads', 'budget' => 5000],
                            ],
                            'notes'            => ['type' => 'string', 'nullable' => true],
                            'tags'             => ['type' => 'array', 'readOnly' => true, 'items' => ['$ref' => '#/components/schemas/Tag']],
                            'created_at'       => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
                            'updated_at'       => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
                        ],
                    ],
                    'LeadWrite' => [
                        'type' => 'object',
                        'required' => ['first_name'],
                        'properties' => [
                            'first_name'        => ['type' => 'string', 'example' => 'Jane'],
                            'last_name'         => ['type' => 'string', 'example' => 'Smith'],
                            'email'             => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                            'phone'             => ['type' => 'string', 'example' => '+14155552671'],
                            'source'            => ['type' => 'string', 'example' => 'api'],
                            'status'            => ['type' => 'string', 'enum' => ['new', 'contacted', 'qualified', 'converted', 'lost']],
                            'is_starred'        => ['type' => 'boolean'],
                            'pipeline_id'       => ['type' => 'integer'],
                            'pipeline_stage_id' => ['type' => 'integer'],
                            'assigned_user_id'  => ['type' => 'integer'],
                            'custom_fields'     => [
                                'type' => 'object',
                                'additionalProperties' => true,
                                'description' => 'Send as a nested JSON object with whatever keys you want. Each key is a custom-field name; each value is what you want stored. Sent as JSON in the request body — not form-encoded. From Pabbly Connect, set the Action body Type to "Raw" / "JSON" and include `custom_fields` as a nested object (NOT as `custom_fields.platform` flat-dot keys). Example body: { "first_name": "Jane", "email": "jane@example.com", "custom_fields": { "platform": "Facebook", "campaign": "summer-2026" } }',
                                'example' => ['platform' => 'Facebook', 'campaign' => 'summer-2026', 'budget' => 5000],
                            ],
                            'notes'             => ['type' => 'string'],
                            'tags'              => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Tag names to apply'],
                        ],
                    ],
                    'Pipeline' => [
                        'type' => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer', 'readOnly' => true],
                            'name'        => ['type' => 'string'],
                            'description' => ['type' => 'string', 'nullable' => true],
                            'is_default'  => ['type' => 'boolean'],
                            'stages'      => ['type' => 'array', 'readOnly' => true, 'items' => ['$ref' => '#/components/schemas/PipelineStage']],
                            'created_at'  => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
                        ],
                    ],
                    'PipelineWrite' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name'        => ['type' => 'string', 'example' => 'Sales Pipeline'],
                            'description' => ['type' => 'string'],
                            'is_default'  => ['type' => 'boolean'],
                        ],
                    ],
                    'PipelineStage' => [
                        'type' => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer', 'readOnly' => true],
                            'pipeline_id' => ['type' => 'integer', 'readOnly' => true],
                            'name'        => ['type' => 'string'],
                            'sort_order'  => ['type' => 'integer'],
                            'color'       => ['type' => 'string', 'nullable' => true, 'example' => '#3b82f6'],
                        ],
                    ],
                    'StageWrite' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name'       => ['type' => 'string', 'example' => 'Discovery'],
                            'sort_order' => ['type' => 'integer'],
                            'color'      => ['type' => 'string', 'example' => '#3b82f6'],
                        ],
                    ],
                    'Tag' => [
                        'type' => 'object',
                        'properties' => [
                            'id'    => ['type' => 'integer', 'readOnly' => true],
                            'name'  => ['type' => 'string'],
                            'color' => ['type' => 'string', 'nullable' => true],
                        ],
                    ],
                    'TagWrite' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name'  => ['type' => 'string', 'example' => 'Hot Lead'],
                            'color' => ['type' => 'string', 'example' => '#ef4444'],
                        ],
                    ],
                    'User' => [
                        'type' => 'object',
                        'properties' => [
                            'id'         => ['type' => 'integer', 'readOnly' => true],
                            'name'       => ['type' => 'string'],
                            'email'      => ['type' => 'string', 'format' => 'email'],
                            'role'       => ['type' => 'string', 'enum' => ['owner', 'admin', 'agent', 'viewer']],
                            'created_at' => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
                        ],
                    ],
                    'Form' => [
                        'type' => 'object',
                        'properties' => [
                            'id'     => ['type' => 'integer', 'readOnly' => true],
                            'name'   => ['type' => 'string'],
                            'slug'   => ['type' => 'string'],
                            'active' => ['type' => 'boolean'],
                            'fields' => ['type' => 'array', 'items' => ['type' => 'object']],
                        ],
                    ],
                    'Automation' => [
                        'type' => 'object',
                        'properties' => [
                            'id'           => ['type' => 'integer', 'readOnly' => true],
                            'name'         => ['type' => 'string'],
                            'trigger_type' => ['type' => 'string'],
                            'enabled'      => ['type' => 'boolean'],
                            'created_at'   => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
                        ],
                    ],
                    'PaginatedResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                            'meta' => [
                                'type' => 'object',
                                'properties' => [
                                    'current_page' => ['type' => 'integer'],
                                    'last_page'    => ['type' => 'integer'],
                                    'per_page'     => ['type' => 'integer'],
                                    'total'        => ['type' => 'integer'],
                                    'from'         => ['type' => 'integer'],
                                    'to'           => ['type' => 'integer'],
                                ],
                            ],
                        ],
                    ],
                    'Error' => [
                        'type' => 'object',
                        'properties' => [
                            'status'  => ['type' => 'string', 'example' => 'error'],
                            'message' => ['type' => 'string', 'example' => 'Unauthenticated.'],
                        ],
                    ],
                    'ValidationError' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'example' => 'The given data was invalid.'],
                            'errors'  => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
                        ],
                    ],
                    'WebhookEvent' => [
                        'type' => 'object',
                        'description' => 'Webhook payload delivered to your endpoint via HTTP POST.',
                        'properties' => [
                            'event'     => ['type' => 'string', 'enum' => ['lead.created', 'lead.updated', 'lead.deleted', 'lead.stage_changed', 'form.submitted', 'automation.triggered'], 'example' => 'lead.created'],
                            'tenant_id' => ['type' => 'integer'],
                            'timestamp' => ['type' => 'string', 'format' => 'date-time', 'description' => 'ISO 8601 timestamp of when the event occurred'],
                            'data'      => ['type' => 'object', 'description' => 'Event-specific data (lead fields, form submission, automation details, etc.)'],
                        ],
                    ],
                ],
            ],
            'paths' => array_merge(
                $this->systemPaths($stdResponses),
                $this->leadPaths($idParam, $paginationParams, $stdResponses),
                $this->pipelinePaths($idParam, $paginationParams, $stdResponses),
                $this->stagePaths($idParam, $stdResponses),
                $this->tagPaths($idParam, $paginationParams, $stdResponses),
                $this->userPaths($paginationParams, $stdResponses),
                $this->formPaths($idParam, $paginationParams, $stdResponses),
                $this->automationPaths($paginationParams, $stdResponses),
                $this->integrationPaths($idParam, $paginationParams, $stdResponses),
                $this->webhookDocs(),
            ),
        ];

        return json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function systemPaths(array $std): array
    {
        return [
            '/health' => [
                'get' => [
                    'operationId' => 'health',
                    'summary'     => 'API Health Check',
                    'description' => 'Returns the API version and server timestamp. No authentication required.',
                    'security'    => [],
                    'tags'        => ['System'],
                    'responses'   => [
                        '200' => [
                            'description' => 'API is healthy',
                            'content'     => ['application/json' => ['schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'status'    => ['type' => 'string', 'example' => 'ok'],
                                    'version'   => ['type' => 'string', 'example' => '1.0.0'],
                                    'timestamp' => ['type' => 'string', 'format' => 'date-time'],
                                ],
                            ]]],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function leadPaths(array $idParam, array $pageParams, array $std): array
    {
        return [
            '/leads' => [
                'get' => [
                    'operationId' => 'leads.index',
                    'summary'     => 'List Leads',
                    'description' => 'Returns a paginated list of leads. **Scope required:** `read:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => array_merge([
                        ['name' => 'search',      'in' => 'query', 'description' => 'Full-text search on name, email, phone', 'schema' => ['type' => 'string']],
                        ['name' => 'source',      'in' => 'query', 'description' => 'Filter by lead source',    'schema' => ['type' => 'string']],
                        ['name' => 'status',      'in' => 'query', 'description' => 'Filter by status',         'schema' => ['type' => 'string', 'enum' => ['new', 'contacted', 'qualified', 'converted', 'lost']]],
                        ['name' => 'pipeline_id', 'in' => 'query', 'description' => 'Filter by pipeline ID',   'schema' => ['type' => 'integer']],
                        ['name' => 'stage_id',    'in' => 'query', 'description' => 'Filter by stage ID',      'schema' => ['type' => 'integer']],
                        ['name' => 'tag',         'in' => 'query', 'description' => 'Filter by tag name',      'schema' => ['type' => 'string']],
                        ['name' => 'starred',     'in' => 'query', 'description' => 'Only starred leads',      'schema' => ['type' => 'boolean']],
                        ['name' => 'sort',        'in' => 'query', 'description' => 'Sort field',              'schema' => ['type' => 'string', 'enum' => ['created_at', 'updated_at', 'lead_score', 'first_name', 'last_name', 'email'], 'default' => 'created_at']],
                        ['name' => 'direction',   'in' => 'query', 'description' => 'Sort direction',          'schema' => ['type' => 'string', 'enum' => ['asc', 'desc'], 'default' => 'desc']],
                    ], $pageParams),
                    'responses' => [
                        '200' => ['description' => 'Paginated leads', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/Lead']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'], '429' => $std['429'],
                    ],
                ],
                'post' => [
                    'operationId' => 'leads.store',
                    'summary'     => 'Create Lead',
                    'description' => 'Creates a new lead. Triggers lead_created automations and outbound webhooks. **Scope required:** `write:leads`',
                    'tags'        => ['Leads'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LeadWrite']]]],
                    'responses' => [
                        '201' => ['description' => 'Lead created', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/Lead']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'], '429' => $std['429'],
                    ],
                ],
            ],
            '/leads/{id}' => [
                'get' => [
                    'operationId' => 'leads.show',
                    'summary'     => 'Get Lead',
                    'description' => 'Returns a single lead by ID including tags and activities. **Scope required:** `read:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => [$idParam],
                    'responses' => [
                        '200' => ['description' => 'Lead', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/Lead']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'leads.update',
                    'summary'     => 'Update Lead',
                    'description' => 'Updates fields on a lead. All fields are optional. **Scope required:** `write:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LeadWrite']]]],
                    'responses' => [
                        '200' => ['description' => 'Updated lead'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'leads.destroy',
                    'summary'     => 'Delete Lead',
                    'description' => 'Permanently deletes a lead. **Scope required:** `delete:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => [$idParam],
                    'responses' => [
                        '200' => ['description' => 'Lead deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
            '/leads/{id}/tags' => [
                'post' => [
                    'operationId' => 'leads.tags.attach',
                    'summary'     => 'Attach Tags to Lead',
                    'description' => 'Attaches one or more tags to a lead by name (creates tags if they don\'t exist). **Scope required:** `write:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => [$idParam],
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => [
                            'type'       => 'object',
                            'required'   => ['tags'],
                            'properties' => ['tags' => ['type' => 'array', 'items' => ['type' => 'string'], 'example' => ['Hot Lead', 'VIP']]],
                        ]]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tags attached'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
            '/leads/{id}/tags/{tag}' => [
                'delete' => [
                    'operationId' => 'leads.tags.detach',
                    'summary'     => 'Detach Tag from Lead',
                    'description' => 'Removes a single tag from a lead by tag name or ID. **Scope required:** `write:leads`',
                    'tags'        => ['Leads'],
                    'parameters'  => [
                        $idParam,
                        ['name' => 'tag', 'in' => 'path', 'required' => true, 'description' => 'Tag name or ID', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tag detached'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function pipelinePaths(array $idParam, array $pageParams, array $std): array
    {
        return [
            '/pipelines' => [
                'get' => [
                    'operationId' => 'pipelines.index',
                    'summary'     => 'List Pipelines',
                    'description' => 'Returns a paginated list of all pipelines with their stages. **Scope required:** `read:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => $pageParams,
                    'responses' => [
                        '200' => ['description' => 'Paginated pipelines', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/Pipeline']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'pipelines.store',
                    'summary'     => 'Create Pipeline',
                    'description' => 'Creates a new pipeline. **Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PipelineWrite']]]],
                    'responses' => [
                        '201' => ['description' => 'Pipeline created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/pipelines/{id}' => [
                'get' => [
                    'operationId' => 'pipelines.show',
                    'summary'     => 'Get Pipeline',
                    'description' => 'Returns a single pipeline with all stages. **Scope required:** `read:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$idParam],
                    'responses' => [
                        '200' => ['description' => 'Pipeline', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/Pipeline']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'pipelines.update',
                    'summary'     => 'Update Pipeline',
                    'description' => '**Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PipelineWrite']]]],
                    'responses' => [
                        '200' => ['description' => 'Pipeline updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'pipelines.destroy',
                    'summary'     => 'Delete Pipeline',
                    'description' => '**Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$idParam],
                    'responses' => [
                        '200' => ['description' => 'Pipeline deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function stagePaths(array $idParam, array $std): array
    {
        $pipelineId = ['name' => 'pipeline_id', 'in' => 'path', 'required' => true, 'description' => 'Pipeline ID', 'schema' => ['type' => 'integer']];
        return [
            '/pipelines/{pipeline_id}/stages' => [
                'get' => [
                    'operationId' => 'stages.index',
                    'summary'     => 'List Stages',
                    'description' => 'Returns all stages for a pipeline ordered by sort_order. **Scope required:** `read:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$pipelineId],
                    'responses' => [
                        '200' => ['description' => 'Stage list', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/PipelineStage']]]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'post' => [
                    'operationId' => 'stages.store',
                    'summary'     => 'Create Stage',
                    'description' => 'Adds a stage to a pipeline. **Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$pipelineId],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/StageWrite']]]],
                    'responses' => [
                        '201' => ['description' => 'Stage created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/pipelines/{pipeline_id}/stages/{id}' => [
                'put' => [
                    'operationId' => 'stages.update',
                    'summary'     => 'Update Stage',
                    'description' => '**Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$pipelineId, $idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/StageWrite']]]],
                    'responses' => [
                        '200' => ['description' => 'Stage updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'stages.destroy',
                    'summary'     => 'Delete Stage',
                    'description' => '**Scope required:** `write:pipelines`',
                    'tags'        => ['Pipelines'],
                    'parameters'  => [$pipelineId, $idParam],
                    'responses' => [
                        '200' => ['description' => 'Stage deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function tagPaths(array $idParam, array $pageParams, array $std): array
    {
        return [
            '/tags' => [
                'get' => [
                    'operationId' => 'tags.index',
                    'summary'     => 'List Tags',
                    'description' => 'Returns a paginated list of all tags. **Scope required:** `read:tags`',
                    'tags'        => ['Tags'],
                    'parameters'  => array_merge([
                        ['name' => 'search', 'in' => 'query', 'description' => 'Search tags by name', 'schema' => ['type' => 'string']],
                    ], $pageParams),
                    'responses' => [
                        '200' => ['description' => 'Paginated tags', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/Tag']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'tags.store',
                    'summary'     => 'Create Tag',
                    'description' => '**Scope required:** `write:tags`',
                    'tags'        => ['Tags'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TagWrite']]]],
                    'responses' => [
                        '201' => ['description' => 'Tag created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/tags/{id}' => [
                'delete' => [
                    'operationId' => 'tags.destroy',
                    'summary'     => 'Delete Tag',
                    'description' => 'Deletes a tag and detaches it from all leads. **Scope required:** `write:tags`',
                    'tags'        => ['Tags'],
                    'parameters'  => [$idParam],
                    'responses' => [
                        '200' => ['description' => 'Tag deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function userPaths(array $pageParams, array $std): array
    {
        $idParam   = ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']];
        $writeSchema = [
            'type'       => 'object',
            'properties' => [
                'name'     => ['type' => 'string', 'example' => 'Jane Smith'],
                'email'    => ['type' => 'string', 'format' => 'email'],
                'password' => ['type' => 'string', 'format' => 'password', 'minLength' => 8],
                'timezone' => ['type' => 'string', 'example' => 'America/New_York'],
                'role'     => ['type' => 'string', 'enum' => ['manager', 'member']],
            ],
        ];
        return [
            '/users' => [
                'get' => [
                    'operationId' => 'users.index',
                    'summary'     => 'List Users',
                    'description' => 'Returns a paginated list of all users in the tenant. **Scope required:** `read:users`',
                    'tags'        => ['Users'],
                    'parameters'  => $pageParams,
                    'responses'   => [
                        '200' => ['description' => 'Paginated users', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/User']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'users.store',
                    'summary'     => 'Create User',
                    'description' => 'Creates a new user in the tenant. **Scope required:** `write:users`',
                    'tags'        => ['Users'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => array_merge($writeSchema, ['required' => ['name', 'email', 'password']])]]],
                    'responses'   => [
                        '201' => ['description' => 'User created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/users/{id}' => [
                'get' => [
                    'operationId' => 'users.show',
                    'summary'     => 'Get User',
                    'description' => '**Scope required:** `read:users`',
                    'tags'        => ['Users'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'User', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/User']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'users.update',
                    'summary'     => 'Update User',
                    'description' => '**Scope required:** `write:users`',
                    'tags'        => ['Users'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => $writeSchema]]],
                    'responses'   => [
                        '200' => ['description' => 'User updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'users.destroy',
                    'summary'     => 'Delete User',
                    'description' => '**Scope required:** `delete:users`',
                    'tags'        => ['Users'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'User deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function formPaths(array $idParam, array $pageParams, array $std): array
    {
        $writeSchema = [
            'type'       => 'object',
            'properties' => [
                'name'              => ['type' => 'string', 'example' => 'Contact Us'],
                'title'             => ['type' => 'string'],
                'description'       => ['type' => 'string'],
                'submit_label'      => ['type' => 'string', 'example' => 'Send'],
                'thank_you_message' => ['type' => 'string'],
                'redirect_url'      => ['type' => 'string', 'format' => 'uri'],
                'pipeline_id'       => ['type' => 'integer', 'nullable' => true],
                'pipeline_stage_id' => ['type' => 'integer', 'nullable' => true],
                'active'            => ['type' => 'boolean'],
                'multi_step'        => ['type' => 'boolean'],
            ],
        ];
        return [
            '/forms' => [
                'get' => [
                    'operationId' => 'forms.index',
                    'summary'     => 'List Forms',
                    'description' => 'Returns a paginated list of all lead capture forms. **Scope required:** `read:forms`',
                    'tags'        => ['Forms'],
                    'parameters'  => $pageParams,
                    'responses'   => [
                        '200' => ['description' => 'Paginated forms', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/Form']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'forms.store',
                    'summary'     => 'Create Form',
                    'description' => 'Creates a new lead capture form. **Scope required:** `write:forms`',
                    'tags'        => ['Forms'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => array_merge($writeSchema, ['required' => ['name']])]]],
                    'responses'   => [
                        '201' => ['description' => 'Form created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/forms/{id}' => [
                'get' => [
                    'operationId' => 'forms.show',
                    'summary'     => 'Get Form',
                    'description' => 'Returns a single form with all its fields. **Scope required:** `read:forms`',
                    'tags'        => ['Forms'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Form with fields', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/Form']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'forms.update',
                    'summary'     => 'Update Form',
                    'description' => '**Scope required:** `write:forms`',
                    'tags'        => ['Forms'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => $writeSchema]]],
                    'responses'   => [
                        '200' => ['description' => 'Form updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'forms.destroy',
                    'summary'     => 'Delete Form',
                    'description' => '**Scope required:** `delete:forms`',
                    'tags'        => ['Forms'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Form deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
            '/forms/{id}/submissions' => [
                'post' => [
                    'operationId' => 'forms.submit',
                    'summary'     => 'Submit Form',
                    'description' => 'Submits a form and optionally creates a lead. Fires `form.submitted` webhook. **Scope required:** `write:forms`',
                    'tags'        => ['Forms'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => [
                        'type'       => 'object',
                        'required'   => ['fields'],
                        'properties' => [
                            'fields' => ['type' => 'object', 'description' => 'Key-value map of field names to values', 'example' => ['first_name' => 'Jane', 'email' => 'jane@example.com']],
                        ],
                    ]]]],
                    'responses'   => [
                        '201' => ['description' => 'Submission recorded', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['submission_id' => ['type' => 'integer']]], 'message' => ['type' => 'string']]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
            ],
        ];
    }

    private function automationPaths(array $pageParams, array $std): array
    {
        $idParam     = ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']];
        $triggers    = ['lead_created', 'lead_stage_changed', 'lead_assigned', 'tag_added', 'lead_score_threshold'];
        $writeSchema = [
            'type'       => 'object',
            'properties' => [
                'name'           => ['type' => 'string', 'example' => 'Welcome Sequence'],
                'description'    => ['type' => 'string'],
                'enabled'        => ['type' => 'boolean'],
                'trigger_type'   => ['type' => 'string', 'enum' => $triggers],
                'trigger_config' => ['type' => 'object', 'description' => 'Trigger-specific settings (e.g. stage_id, tag_name, score_threshold)'],
            ],
        ];
        return [
            '/automations' => [
                'get' => [
                    'operationId' => 'automations.index',
                    'summary'     => 'List Automations',
                    'description' => 'Returns a paginated list of all automations. **Scope required:** `read:automations`',
                    'tags'        => ['Automations'],
                    'parameters'  => $pageParams,
                    'responses'   => [
                        '200' => ['description' => 'Paginated automations', 'content' => ['application/json' => ['schema' => ['allOf' => [['$ref' => '#/components/schemas/PaginatedResponse'], ['properties' => ['data' => ['items' => ['$ref' => '#/components/schemas/Automation']]]]]]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'automations.store',
                    'summary'     => 'Create Automation',
                    'description' => 'Creates a new automation. **Scope required:** `write:automations`',
                    'tags'        => ['Automations'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => array_merge($writeSchema, ['required' => ['name', 'trigger_type']])]]],
                    'responses'   => [
                        '201' => ['description' => 'Automation created'],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/automations/{id}' => [
                'get' => [
                    'operationId' => 'automations.show',
                    'summary'     => 'Get Automation',
                    'description' => '**Scope required:** `read:automations`',
                    'tags'        => ['Automations'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Automation'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'automations.update',
                    'summary'     => 'Update Automation',
                    'description' => '**Scope required:** `write:automations`',
                    'tags'        => ['Automations'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => $writeSchema]]],
                    'responses'   => [
                        '200' => ['description' => 'Automation updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'automations.destroy',
                    'summary'     => 'Delete Automation',
                    'description' => '**Scope required:** `delete:automations`',
                    'tags'        => ['Automations'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Automation deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function integrationPaths(array $idParam, array $pageParams, array $std): array
    {
        $integrationSchema = [
            'type' => 'object',
            'properties' => [
                'id'             => ['type' => 'integer', 'readOnly' => true],
                'type'           => ['type' => 'string', 'example' => 'hubspot', 'description' => 'Integration type key (see /integrations/types)'],
                'name'           => ['type' => 'string', 'example' => 'Production HubSpot'],
                'label'          => ['type' => 'string', 'readOnly' => true, 'example' => 'HubSpot CRM'],
                'enabled'        => ['type' => 'boolean'],
                'status'         => ['type' => 'string', 'enum' => ['connected', 'disconnected', 'error'], 'readOnly' => true],
                'field_mapping'  => ['type' => 'object', 'nullable' => true],
                'filter_config'  => ['type' => 'object', 'nullable' => true],
                'last_synced_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true, 'readOnly' => true],
                'created_at'     => ['type' => 'string', 'format' => 'date-time', 'readOnly' => true],
            ],
        ];

        $writeSchema = [
            'type'       => 'object',
            'required'   => ['type', 'name'],
            'properties' => [
                'type'          => ['type' => 'string', 'example' => 'hubspot', 'description' => 'Integration type — see GET /integrations/types for available types'],
                'name'          => ['type' => 'string', 'example' => 'Production HubSpot'],
                'enabled'       => ['type' => 'boolean', 'default' => false],
                'config'        => ['type' => 'object', 'description' => 'Integration credentials/settings (stored encrypted). See integration type for required keys.'],
                'field_mapping' => ['type' => 'object', 'description' => 'Custom lead-to-integration field mapping'],
                'filter_config' => ['type' => 'object', 'description' => 'Conditions to filter which leads sync to this integration'],
            ],
        ];

        return [
            '/integrations/types' => [
                'get' => [
                    'operationId' => 'integrations.types',
                    'summary'     => 'List Integration Types',
                    'description' => 'Returns all 30+ supported integration types with labels and descriptions. **Scope required:** `read:integrations`',
                    'tags'        => ['Integrations'],
                    'responses'   => [
                        '200' => ['description' => 'Integration types', 'content' => ['application/json' => ['schema' => [
                            'properties' => ['data' => ['type' => 'array', 'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'type'        => ['type' => 'string'],
                                    'label'       => ['type' => 'string'],
                                    'description' => ['type' => 'string'],
                                ],
                            ]]],
                        ]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
            ],
            '/integrations' => [
                'get' => [
                    'operationId' => 'integrations.index',
                    'summary'     => 'List Integrations',
                    'description' => 'Returns a paginated list of configured integrations. **Scope required:** `read:integrations`',
                    'tags'        => ['Integrations'],
                    'parameters'  => array_merge([
                        ['name' => 'type',    'in' => 'query', 'description' => 'Filter by integration type',   'schema' => ['type' => 'string']],
                        ['name' => 'enabled', 'in' => 'query', 'description' => 'Filter by enabled status',    'schema' => ['type' => 'boolean']],
                        ['name' => 'status',  'in' => 'query', 'description' => 'Filter by connection status', 'schema' => ['type' => 'string', 'enum' => ['connected', 'disconnected', 'error']]],
                    ], $pageParams),
                    'responses'   => [
                        '200' => ['description' => 'Paginated integrations', 'content' => ['application/json' => ['schema' => [
                            'allOf' => [
                                ['$ref' => '#/components/schemas/PaginatedResponse'],
                                ['properties' => ['data' => ['items' => $integrationSchema]]],
                            ],
                        ]]]],
                        '401' => $std['401'], '403' => $std['403'],
                    ],
                ],
                'post' => [
                    'operationId' => 'integrations.store',
                    'summary'     => 'Create Integration',
                    'description' => 'Adds a new CRM/marketing integration. **Scope required:** `write:integrations`',
                    'tags'        => ['Integrations'],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => $writeSchema]]],
                    'responses'   => [
                        '201' => ['description' => 'Integration created', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => $integrationSchema]]]]],
                        '401' => $std['401'], '403' => $std['403'], '422' => $std['422'],
                    ],
                ],
            ],
            '/integrations/{id}' => [
                'get' => [
                    'operationId' => 'integrations.show',
                    'summary'     => 'Get Integration',
                    'description' => '**Scope required:** `read:integrations`',
                    'tags'        => ['Integrations'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Integration', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => $integrationSchema]]]]],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
                'put' => [
                    'operationId' => 'integrations.update',
                    'summary'     => 'Update Integration',
                    'description' => 'Updates an integration. Only provided fields are changed. **Scope required:** `write:integrations`',
                    'tags'        => ['Integrations'],
                    'parameters'  => [$idParam],
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => $writeSchema]]],
                    'responses'   => [
                        '200' => ['description' => 'Integration updated'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'], '422' => $std['422'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'integrations.destroy',
                    'summary'     => 'Delete Integration',
                    'description' => '**Scope required:** `write:integrations`',
                    'tags'        => ['Integrations'],
                    'parameters'  => [$idParam],
                    'responses'   => [
                        '200' => ['description' => 'Integration deleted'],
                        '401' => $std['401'], '403' => $std['403'], '404' => $std['404'],
                    ],
                ],
            ],
        ];
    }

    private function webhookDocs(): array
    {
        return [
            '/x-webhooks' => [
                'get' => [
                    'operationId' => 'webhooks.events',
                    'summary'     => 'Outbound Webhook Events Reference',
                    'description' => implode("\n\n", [
                        'This virtual endpoint documents outbound webhook events emitted by LeadHub.',
                        'Configure webhooks at **Settings → Outbound Webhooks** in the admin panel.',
                        '## Signature Verification',
                        'Every request includes `X-LeadHub-Signature: sha256=<hmac>`. Verify using:',
                        '```',
                        'hash_hmac("sha256", $rawBody, $secret) === $signature',
                        '```',
                        '## Events',
                        '| Event | Trigger |',
                        '|-------|---------|',
                        '| `lead.created` | New lead is created |',
                        '| `lead.updated` | Any lead field changes |',
                        '| `lead.deleted` | Lead is deleted |',
                        '| `lead.stage_changed` | Lead moves to a different pipeline stage |',
                        '| `form.submitted` | A lead capture form receives a submission |',
                        '| `automation.triggered` | An automation run completes |',
                        '## Filters',
                        'Webhooks can be filtered by `source`, `status`, `pipeline_id`, `pipeline_stage_id`, `assigned_user_id`, `tags`. A webhook fires only if ALL filters match. The `tags` filter fires when the lead has at least one of the specified tag names.',
                        '## Retry Policy',
                        'Failed deliveries are retried up to 5 times with exponential backoff (2m, 4m, 8m, 16m, 32m). All attempts are logged and viewable in Settings → Webhook Deliveries.',
                    ]),
                    'security' => [],
                    'tags'     => ['System'],
                    'responses' => [
                        '200' => [
                            'description' => 'Webhook payload shape',
                            'content'     => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/WebhookEvent']]],
                        ],
                    ],
                ],
            ],
        ];
    }
}
