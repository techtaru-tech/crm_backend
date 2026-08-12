<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| TeamSettingsPage — Filament tenant strings
|------------------------------------------------------------
*/

return [
    'title'                          => 'टीम और सीट प्रबंधन',
    'navigation_label'               => 'टीम और सीट',

    // Notifications - member ops
    'member_removed'                 => ':name को इस वर्कस्पेस से हटा दिया गया।',
    'member_suspended_title'         => ':name निलंबित।',
    'member_suspended_body'          => 'वे अब साइन इन नहीं कर सकते।',
    'member_reactivated'             => ':name पुनः सक्रिय।',

    // Reset password
    'reset_link_sent_title'          => 'रीसेट लिंक भेजा गया',
    'reset_link_sent_body'           => ':email को एक मिनट के भीतर एक ईमेल प्राप्त होगा। लिंक 60 मिनट तक मान्य।',
    'reset_link_failed_title'        => 'रीसेट लिंक नहीं भेजा जा सका',

    // Invite action
    'action_invite'                  => 'टीम सदस्य को आमंत्रित करें',
    'invite_email_label'             => 'ईमेल',
    'invite_email_placeholder'       => 'teammate@company.com',
    'invite_role_label'              => 'भूमिका',
    'invite_role_manager'            => 'मैनेजर',
    'invite_role_member'             => 'सदस्य',
    'invite_failed_title'            => 'आमंत्रण नहीं भेजा जा सका',
    'invite_sent_title'              => 'आमंत्रण भेजा गया',
    'invite_sent_body'               => ':email को ईमेल किया गया।',

    // Adjust seats action
    'action_adjust_seats'            => 'सीट सीमा समायोजित करें',
    'max_seats_label'                => 'अधिकतम सीट',

    // ─── Blade view — role permissions banner ─────────────────────────
    'role_permissions_title'         => 'भूमिका अनुमतियाँ',
    'role_permissions_lede'          => 'टीम के सदस्यों के लिए दो स्तर। दोनों टेनेंट-दायरे वाले हैं — वे इस वर्कस्पेस के बाहर का डेटा कभी नहीं देखते।',
    'role_card_manager_title'        => 'मैनेजर',
    'role_card_manager_desc'         => 'पूर्ण लीड्स + पाइपलाइन + ऑटोमेशन + फ़ॉर्म। टीम को आमंत्रित और निलंबित कर सकते हैं। प्रशासकों को हटा नहीं सकते।',
    'role_card_member_title'         => 'सदस्य',
    'role_card_member_desc'          => 'मानक उपयोगकर्ता। लीड्स के साथ काम करें, फ़ॉर्म और रिपोर्ट देखें। कोई टीम प्रबंधन नहीं, कोई सेटिंग्स नहीं।',

    // ─── Blade view — seat usage card ─────────────────────────────────
    'seat_usage_title'               => 'सीट उपयोग',
    'seat_usage_subtitle'            => ':max में से :used सीट उपयोग में',
    'seat_count_suffix'              => 'उपलब्ध',
    'seat_plan_prefix'               => 'प्लान:',
    'seat_limit_msg'                 => 'सीट सीमा पूरी हुई। नए उपयोगकर्ताओं को आमंत्रित करने के लिए किसी सदस्य को निलंबित या हटाएँ — या अपने प्लान को अपग्रेड करें।',

    // ─── Blade view — members table ───────────────────────────────────
    'members_title'                  => 'टीम के सदस्य',
    'col_name'                       => 'नाम',
    'col_email'                      => 'ईमेल',
    'col_role'                       => 'भूमिका',
    'col_status'                     => 'स्थिति',
    'col_actions'                    => 'क्रियाएँ',
    'row_self_suffix'                => '(आप)',
    'status_suspended'               => 'निलंबित',
    'status_active'                  => 'सक्रिय',
    'action_reset_password'          => 'पासवर्ड रीसेट करें',
    'action_reset_password_title'    => 'पासवर्ड रीसेट ईमेल भेजें',
    'action_unsuspend'               => 'निलंबन हटाएँ',
    'action_suspend'                 => 'निलंबित करें',
    'action_remove'                  => 'हटाएँ',
    'empty_no_members_html'          => 'अभी तक कोई टीम सदस्य नहीं।  आरंभ करने के लिए ऊपर <strong>टीम सदस्य को आमंत्रित करें</strong> क्रिया का उपयोग करें।',

    // ─── Blade view — wire:confirm prompts ────────────────────────────
    'confirm_reset_password'         => ':email को पासवर्ड रीसेट लिंक भेजें?',
    'confirm_suspend'                => ':name को निलंबित करें?  निलंबन हटने तक उनकी पहुँच समाप्त रहेगी।',
    'confirm_remove'                 => 'इस वर्कस्पेस से :name को हटाएँ?',

    // ─── Role badges (Pass 22) ────────────────────────────────────────
    'role_admin'                     => 'एडमिन',
    'role_owner'                     => 'स्वामी',
    'role_manager'                   => 'मैनेजर',
    'role_member'                    => 'सदस्य',
    'role_user'                      => 'उपयोगकर्ता',

    // ─── Role change (inline editor) ──────────────────────────────────
    'change_role_label'              => 'भूमिका बदलें',
    'role_changed'                   => ':name की भूमिका बदलकर :role कर दी गई।',
    'role_invalid'                   => 'अमान्य भूमिका।',
    'role_cannot_change_self'        => 'आप अपनी स्वयं की भूमिका नहीं बदल सकते।',
    'role_admin_only'                => 'केवल एक एडमिन ही किसी एडमिन की भूमिका बदल सकता है।',
    'role_admin_not_editable'        => 'एडमिन भूमिकाएँ वर्कस्पेस स्वामी द्वारा प्रबंधित की जाती हैं, यहाँ से नहीं।',

    // ─── Seat-card plan labels (Pass 22) ──────────────────────────────
    'plan_free'                      => 'मुफ्त',
    'plan_starter'                   => 'स्टार्टर',
    'plan_pro'                       => 'प्रो',
    'plan_business'                  => 'बिज़नेस',
    'plan_enterprise'                => 'एंटरप्राइज़',
    'plan_trial'                     => 'ट्रायल',
];
