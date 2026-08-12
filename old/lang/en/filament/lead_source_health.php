<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Lead Source Health report — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/lead_source_health.<key>').
*/

return [

    // ----- Navigation / page header -----
    'nav_label' => 'Source Health',
    'title'     => 'Lead Source Health',

    // ----- Section headings -----
    'source_connections' => 'Source Connections',
    'section_sub'        => 'Live health metrics for every inbound lead source.',

    // ----- Table / cells -----
    'empty'              => 'No lead source connections configured.',
    'col_name'           => 'Name',
    'col_source'         => 'Source',
    'col_status'         => 'Status',
    'col_last_received'  => 'Last received',
    'col_24h'            => '24h',
    'col_7d'             => '7d',
    'col_30d'            => '30d',
    'col_success_7d'     => 'Success (7d)',
    'col_last_error'     => 'Last Error',
    'col_actions'        => 'Actions',
    'never'              => 'Never',
    'no_data'            => 'No data',
    'action_edit'        => 'Edit',

    // ----- Status badge labels -----
    'status_connected'    => 'Connected',
    'status_disconnected' => 'Disconnected',
    'status_error'        => 'Error',
    'status_unknown'      => 'Unknown',
    'status_paused'       => 'Paused',
];
