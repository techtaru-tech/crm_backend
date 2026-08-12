<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| LeadStatus enum — translatable case labels
|------------------------------------------------------------
| Accessed via __('enums/lead_status.<case_value>').
*/

return [
    'new'       => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'won'       => 'Won',
    'lost'      => 'Lost',

    // Non-canonical statuses that legacy data / buyer custom values
    // can still produce.  Surfaced in chart labels (LeadStatusChart)
    // via translator-first resolution before falling back to ucfirst().
    'nurturing' => 'Nurturing',
    'proposal'  => 'Proposal',
];
