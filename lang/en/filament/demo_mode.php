<?php

declare(strict_types=1);

return [
    'updates_blocked' => 'Demo: cannot apply update packages.',

    // Distinct Backups page guards (one key per call site).
    'backups_create_method_blocked'  => 'Demo: cannot create backups (would dump the live demo DB and files).',
    'backups_delete_method_blocked'  => 'Demo: cannot delete backup archives.',
    'backups_restore_method_blocked' => 'Demo: cannot restore backups (would overwrite the live demo DB).',
    'backups_create_action_blocked'  => 'Demo: cannot create backups.',
    'backups_restore_action_blocked' => 'Demo: cannot restore backups.',
    'backups_delete_action_blocked'  => 'Demo: cannot delete backups.',

    'gdpr_anonymize_blocked' => 'Demo: GDPR anonymize is disabled.',
    'gdpr_erase_blocked'     => 'Demo: GDPR erase is disabled.',

    'checkout_blocked'       => 'Demo: cannot start a real checkout.',
    'impersonation_disabled' => 'Demo: impersonation is disabled.',
    'test_email_blocked'     => 'Demo: cannot send test emails to arbitrary recipients.',
];
