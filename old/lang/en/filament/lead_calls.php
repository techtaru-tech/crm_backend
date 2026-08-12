<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadCallResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/lead_calls.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'   => 'Call History',

    // ----- Model labels (rendered in breadcrumbs / page titles) -----
    'model_label'        => 'Call',
    'plural_model_label' => 'Calls',

    // ----- Infolist -----
    'lead'        => 'Lead',
    'agent'       => 'Agent',
    'from'        => 'From',
    'to'          => 'To',
    'duration'    => 'Duration',
    'started'     => 'Started',
    'recording'   => 'Recording',
    'ai_summary'  => 'AI Summary',
    'transcription' => 'Transcription',

    // ----- Table -----
    'col_when'      => 'When',
    'col_direction' => 'Direction',
    'col_status'    => 'Status',

    // ----- Filters -----
    'filter_agent'         => 'Agent',
    'filter_label_direction' => 'Direction',
    'filter_label_status'  => 'Status',

    // ─── Select options ────────────────────────────────────────────
    'option_inbound'      => 'Inbound',
    'option_outbound'     => 'Outbound',
    'option_initiated'    => 'Initiated',
    'option_ringing'      => 'Ringing',
    'option_in_progress'  => 'In progress',
    'option_completed'    => 'Completed',
    'option_busy'         => 'Busy',
    'option_failed'       => 'Failed',
    'option_no_answer'    => 'No answer',
    'option_canceled'     => 'Canceled',

    // ─── Infolist fallback strings ────────────────────────────────
    'fallback_unknown'       => '(unknown)',
    'fallback_not_available' => '(not available)',

    // ─── Direction / status labels (infolist Placeholder content) ──
    'direction_inbound'   => 'Inbound',
    'direction_outbound'  => 'Outbound',
    'status_initiated'    => 'Initiated',
    'status_ringing'      => 'Ringing',
    'status_in_progress'  => 'In progress',
    'status_completed'    => 'Completed',
    'status_busy'         => 'Busy',
    'status_failed'       => 'Failed',
    'status_no_answer'    => 'No answer',
    'status_canceled'     => 'Canceled',

    // ─── Recording player (resources/views/filament/resources/lead-calls/recording-player.blade.php) ──
    'recording_unsupported' => 'Your browser does not support audio playback.',
    'recording_download'    => 'Download MP3',

];
