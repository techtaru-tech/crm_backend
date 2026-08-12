<?php

declare(strict_types=1);

return [
    'headers' => [
        'id'             => 'ID',
        'first_name'     => 'First Name',
        'last_name'      => 'Last Name',
        'email'          => 'Email',
        'phone'          => 'Phone',
        'source'         => 'Source',
        'status'         => 'Status',
        'pipeline_stage' => 'Pipeline Stage',
        'score'          => 'Score',
        'assigned_to'    => 'Assigned To',
        'tags'           => 'Tags',
        'created_at'     => 'Created At',
    ],

    // Abort messages surfaced from /export/download when the signed URL
    // is rejected.  These bubble up to the buyer's browser as the body
    // of an HTTP 401/404/403 response (or the Filament error toast on
    // XHR) so they must be translated.
    'abort_invalid_signature'  => 'Invalid or expired download link.',
    'abort_file_not_found'     => 'Export file not found.',
    'abort_tenant_mismatch'    => 'Export does not belong to your workspace.',
];
