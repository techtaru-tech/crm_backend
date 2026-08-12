<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SourceAttributionReport — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/source_attribution_report.<key>').
*/

return [

    // ----- Navigation / page header -----
    'nav_label' => 'Source Attribution',
    'title'     => 'Source Attribution Report',

    // ----- Section headings -----
    'leads_grouped_by_utm' => 'Leads grouped by UTM Source & Campaign',
    'section_sub'          => 'Leads without UTM tracking are grouped under "(direct)"',

    // ----- Table / cells -----
    'col_utm_source'       => 'UTM Source',
    'col_utm_campaign'     => 'UTM Campaign',
    'col_total_leads'      => 'Total Leads',
    'col_won'              => 'Won',
    'col_conversion_rate'  => 'Conversion %',
    'col_total_won_value'  => 'Total Won $',
    'empty'                => 'No data for this period',
];
