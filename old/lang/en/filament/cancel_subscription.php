<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| CancelSubscription — Filament tenant strings
|------------------------------------------------------------
| Accessed via __('filament/cancel_subscription.<key>').
*/

return [
    'title'                          => 'Cancel subscription',
    'heading'                        => 'We\'re sorry to see you go',
    'subheading'                     => 'Help us improve — tell us why you\'re cancelling. Your data stays safe for 30 days after access ends.',

    // Form section
    'why_section_description'        => 'Only the operator sees this, and only as part of aggregate retention reports.',
    'field_reason_label'             => 'Reason for cancelling',
    'feedback_label'                 => 'Anything else you\'d like us to know? (optional)',
    'feedback_placeholder'           => 'Be candid — every honest answer helps us build something better.',

    // Retention offers
    'retention_too_expensive_title'  => 'Stay with 50% off for 3 months',
    'retention_too_expensive_body'   => 'Email support@yourcompany.com with the subject "Retention offer" and we\'ll apply a 50% discount to your next 3 invoices. Same plan, lower price.',
    'retention_not_using_title'      => 'Pause instead of cancelling',
    'retention_not_using_body'       => 'We can pause your subscription for up to 90 days — your data stays put and you\'re not charged. Email support@yourcompany.com to pause.',
    'retention_missing_feature_title'=> 'Tell us what\'s missing',
    'retention_missing_feature_body' => 'Many roadmap items came from cancellation feedback. Reply to your renewal invoice with the missing feature — we ship 3-5 customer-requested features per month.',
    'retention_default_title'        => 'Before you go…',
    'retention_default_body'         => 'Reply to your latest invoice with what would have made you stay. We read every one.',

    // Cancel notifications
    'no_workspace_title'             => 'No workspace context.',
    'cancelled_title'                => 'Subscription cancelled',
    'cancelled_body'                 => 'Access continues until :date. We\'ve sent a confirmation to your inbox.',

    // ─── Final action row ───
    'keep_subscription'              => '← Keep my subscription',
    'cancel_subscription'            => 'Cancel my subscription',
    'footer_hint'                    => "You'll keep access through the end of your current billing period. After that the workspace becomes read-only and is fully deleted after 30 days unless you reactivate.",
];
