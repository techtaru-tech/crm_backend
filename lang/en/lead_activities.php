<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Lead activity timeline strings
|--------------------------------------------------------------------------
|
| Human-readable descriptions for the entries that appear in a lead's
| activity timeline. The canonical English string is also stored on the
| `lead_activities.description` column for audit, search, and legacy
| rendering. New rows additionally persist `i18n_key` + `i18n_params`
| in the `metadata` JSON column so the view can call __() at render time
| and switch language without rewriting historical data.
|
| Placeholders use Laravel's :placeholder convention; pass values via
| __('lead_activities.key', ['placeholder' => $value]).
|
*/

return [
    'lead_created'     => 'Lead created from :source',
    'status_changed'   => 'Status changed from :from to :to',
    'stage_moved'      => 'Moved from :from to :to',
    'email_sent'       => 'Email sent: :subject',
    'email_received'   => 'Email received: :subject',
    'call_logged'      => 'Call logged (:direction, :duration min, :outcome)',
    'note_added'       => 'Internal note added',
    'tag_applied'      => 'Tag applied: :tag',
    'tag_removed'      => 'Tag removed: :tag',
    'assigned'         => 'Assigned to :to',
    'score_changed'    => 'Score changed from :from to :to',
    'imported'         => 'Imported from file: :filename',
    'booking_made'     => ':guest booked ":meeting" on :when',
    'call_transcribed' => 'Call transcribed and summarized by AI.',

    // Meeting activity types (spec §10)
    'meeting_scheduled' => 'Meeting scheduled: :meeting :when',
    'meeting_rescheduled' => 'Meeting rescheduled: :meeting :when',
    'meeting_cancelled' => 'Meeting cancelled: :meeting',
];
