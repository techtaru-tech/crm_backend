<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| ApiKey model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/api_key.<key>').
*/

return [
    // ─── SCOPES ────────────────────────────────────
    'scope_read_leads'         => 'Read Leads',
    'scope_write_leads'        => 'Write Leads',
    'scope_delete_leads'       => 'Delete Leads',
    'scope_read_pipelines'     => 'Read Pipelines',
    'scope_write_pipelines'    => 'Write Pipelines',
    'scope_read_tags'          => 'Read Tags',
    'scope_write_tags'         => 'Write Tags',
    'scope_read_users'         => 'Read Users',
    'scope_write_users'        => 'Create & Update Users',
    'scope_delete_users'       => 'Delete Users',
    'scope_read_forms'         => 'Read Forms',
    'scope_write_forms'        => 'Create, Update & Submit Forms',
    'scope_delete_forms'       => 'Delete Forms',
    'scope_read_automations'   => 'Read Automations',
    'scope_write_automations'  => 'Create & Update Automations',
    'scope_delete_automations' => 'Delete Automations',
    'scope_read_integrations'  => 'Read Integrations',
    'scope_write_integrations' => 'Create, Update & Delete Integrations',
];
