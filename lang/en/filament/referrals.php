<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| ReferralsPage — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/referrals.<key>').
*/

return [

    // ----- Navigation / page header -----
    'nav_label' => 'Referrals',
    'title'     => 'Referrals',

    // ----- Section headings -----
    'your_referral_link'                => 'Your Referral Link',
    'workspaces_youve_referred'         => "Workspaces You've Referred",

    // ----- Buttons -----
    'copy'                              => 'Copy',
    'copied'                            => 'Copied',

    // ----- Section subtitles -----
    'share_subtitle'                    => "Share with anyone — we'll credit any workspace that signs up through your link.",

    // ----- Stat tiles -----
    'stat_referred'                     => 'Referred',
    'stat_on_trial'                     => 'On Trial',
    'stat_paying'                       => 'Paying',

    // ----- Empty state -----
    'empty_no_referrals'                => 'No referrals yet. Share your link to get started.',

    // ----- Table columns -----
    'col_name'                          => 'Name',
    'col_plan'                          => 'Plan',
    'col_status'                        => 'Status',
    'col_signed_up'                     => 'Signed up',

    // ----- Plan badge labels (Pass 22) -----
    // Used by referrals-page.blade.php to translate the plan slug shown
    // for each referred workspace. Unknown future plans fall back to
    // ucfirst() in the view.
    'plan_free'                         => 'Free',
    'plan_starter'                      => 'Starter',
    'plan_pro'                          => 'Pro',
    'plan_business'                     => 'Business',
    'plan_enterprise'                   => 'Enterprise',
    'plan_trial'                        => 'Trial',
];
