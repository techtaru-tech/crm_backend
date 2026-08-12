<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| FeatureRequest model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/feature_request.<key>').
*/

return [
    // ─── STATUSES ──────────────────────────────────
    'status_open'        => 'Open',
    'status_planned'     => 'Planned',
    'status_in_progress' => 'In Progress',
    'status_shipped'     => 'Shipped',
    'status_closed'      => 'Closed',
];
