<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin / Super-Admin अधिसूचना स्ट्रिंग्स (Filament टोस्ट)
|--------------------------------------------------------------------------
|
| ये Filament\Notifications\Notification::make()->title(__('notifications.X'))
| के माध्यम से tenant-admin और super-admin Filament पृष्ठों पर दिखाए जाने वाले
| संक्षिप्त फीडबैक संदेश हैं।
|
| खरीदार इस फ़ाइल को संपादित करके या इसे lang/<locale>/notifications.php में
| कॉपी करके और मानों का अनुवाद करके पाठ का अनुवाद या अनुकूलन कर सकते हैं।
|
| प्लेसहोल्डर Laravel की :placeholder परंपरा का उपयोग करते हैं; मान
| __('notifications.key', ['placeholder' => $value]) के माध्यम से पास करें।
|
*/

return [

    // --- सामान्य अनुमति / नहीं मिला -------------------------------------
    'no_tenant_found'                      => 'कोई tenant नहीं मिला।',
    'no_workspace_active'                  => 'वर्तमान में कोई कार्यक्षेत्र सक्रिय नहीं है।',
    'no_workspace_context'                 => 'कार्यक्षेत्र संदर्भ नहीं मिला।',
    'permission_denied'                    => 'अनुमति अस्वीकृत',
    'permission_required'                  => 'आपके पास अनुमति नहीं है।',
    'no_remove_permission'                 => 'आपके पास टीम के सदस्यों को हटाने की अनुमति नहीं है।',
    'no_suspend_permission'                => 'आपके पास टीम के सदस्यों को निलंबित करने की अनुमति नहीं है।',
    'cannot_remove_self'                   => 'आप स्वयं को नहीं हटा सकते।',
    'cannot_suspend_self'                  => 'आप स्वयं को निलंबित नहीं कर सकते।',
    'admin_only_suspend_admin'             => 'केवल एक एडमिन ही दूसरे एडमिन को निलंबित कर सकता है।',
    'user_not_in_workspace'                => 'इस कार्यक्षेत्र में उपयोगकर्ता नहीं मिला।',
    'no_owner_for_workspace'               => 'इस कार्यक्षेत्र के लिए कोई स्वामी नहीं मिला',

    // --- सेटिंग्स सहेजी गईं ---------------------------------------------
    'profile_updated'                      => 'प्रोफ़ाइल सफलतापूर्वक अपडेट की गई।',
    'password_changed'                     => 'पासवर्ड सफलतापूर्वक बदला गया।',
    'branding_saved'                       => 'ब्रांडिंग सेटिंग्स सहेजी गईं।',
    'email_saved'                          => 'ईमेल सेटिंग्स सहेजी गईं।',
    'general_saved'                        => 'सामान्य सेटिंग्स सहेजी गईं।',
    'messaging_saved'                      => 'मैसेजिंग सेटिंग्स सहेजी गईं।',
    'realtime_saved'                       => 'रीयलटाइम सेटिंग्स सहेजी गईं।',
    'security_saved'                       => 'इस कार्यक्षेत्र के लिए सुरक्षा सेटिंग्स सहेजी गईं।',
    'storage_saved'                        => 'भंडारण सेटिंग्स सहेजी गईं।',
    'availability_saved'                   => 'उपलब्धता सहेजी गई',

    // --- सहेजने / कनेक्ट करने की सामान्य विफलताएँ (:error के साथ) -------
    'save_failed'                          => 'सहेजने में विफल: :error',
    'test_failed_error'                    => 'परीक्षण विफल: :error',
    'storage_test_failed'                  => 'भंडारण परीक्षण विफल: :error',
    'storage_test_ok'                      => 'भंडारण कनेक्शन काम कर रहा है।',

    // --- सत्र / 2FA -----------------------------------------------------
    'session_revoked'                      => 'सत्र रद्द किया गया।',
    'all_other_sessions_revoked'           => 'अन्य सभी सत्र रद्द किए गए।',
    'cannot_revoke_current_session'        => 'आप अपना वर्तमान सत्र रद्द नहीं कर सकते।',
    'twofa_enabled'                        => 'दो-कारक प्रमाणीकरण सक्षम!',
    'twofa_disabled'                       => 'दो-कारक प्रमाणीकरण अक्षम।',
    'twofa_invalid_code'                   => 'अमान्य सत्यापन कोड। कृपया पुनः प्रयास करें।',
    'twofa_recovery_regenerated'           => 'रिकवरी कोड पुनर्जनित।',
    'twofa_generate_secret_first'          => 'कृपया पहले एक रहस्य उत्पन्न करें।',

    // --- ऑनबोर्डिंग / विविध एडमिन --------------------------------------
    'onboarding_revisit_hint'              => 'आप सेटिंग्स → टीम से किसी भी समय ऑनबोर्डिंग पर वापस आ सकते हैं।',
    'preset_loaded'                        => 'प्रीसेट लोड किया गया। लागू करने के लिए सहेजें पर क्लिक करें।',

    // --- API कुंजियाँ ---------------------------------------------------
    'api_key_revoked'                      => 'API कुंजी रद्द की गई।',

    // --- कनेक्शन / Webhooks --------------------------------------------
    'connection_ok'                        => 'कनेक्शन सफल!',
    'connection_failed'                    => 'कनेक्शन विफल!',
    'test_delivered_http'                  => 'परीक्षण वितरित — HTTP :status',
    'test_failed_http'                     => 'परीक्षण विफल — HTTP :status',
    'delivery_requeued'                    => 'वितरण पुनः प्रयास के लिए कतारबद्ध।',
    'webhook_requeued'                     => 'Webhook प्रसंस्करण के लिए पुनः कतारबद्ध',
    'webhook_source_missing'               => 'पुनः प्रयास नहीं किया जा सकता: स्रोत कनेक्शन नहीं मिला',

    // --- मीटिंग / बुकिंग ------------------------------------------------
    'booking_cancelled'                    => 'बुकिंग रद्द',
    'booking_completed'                    => 'पूर्ण के रूप में चिह्नित',
    'booking_no_show'                      => 'अनुपस्थित के रूप में चिह्नित',

    // --- लाइसेंस --------------------------------------------------------
    'license_required'                     => 'कृपया अपनी लाइसेंस कुंजी दर्ज करें।',

    // --- डोमेन ----------------------------------------------------------
    'domain_saved'                         => 'डोमेन सहेजा गया। सत्यापन पृष्ठभूमि में चल रहा है।',
    'domain_verification_retriggered'      => 'डोमेन सत्यापन पुनः ट्रिगर किया गया।',
    'domain_already_taken'                 => 'यह डोमेन पहले से ही किसी अन्य कार्यक्षेत्र में पंजीकृत है।',

    // --- Tenant / सीट सीमाएँ / प्रतिरूपण -------------------------------
    'seat_limit_updated'                   => 'सीट सीमा :limit पर अपडेट की गई।',
    'already_impersonating'                => 'पहले से प्रतिरूपण कर रहे हैं — पहले वर्तमान सत्र रोकें',

    // --- मॉड्यूल (SA) ---------------------------------------------------
    'module_enabled'                       => 'मॉड्यूल सक्षम: :name',
    'module_disabled'                      => 'मॉड्यूल अक्षम: :name',
    'module_deleted'                       => 'मॉड्यूल हटाया गया: :name',
    'module_enable_failed'                 => 'सक्षम करना विफल',
    'module_disable_failed'                => 'अक्षम करना विफल',
    'module_delete_failed'                 => 'हटाना विफल',
    'module_install_failed'                => 'स्थापना विफल',
    'module_caches_cleared'                => 'मॉड्यूल कैश साफ़ किए गए।',
    'module_regenerate_failed'             => 'पुनर्जनन विफल',
    'module_upload_zip_first'              => 'कृपया पहले एक zip अपलोड करें।',

    // --- सामान्य फ़ॉलबैक (SafeAction trait) ----------------------------
    'something_went_wrong'                 => 'कुछ गलत हो गया',

    // --- अधिसूचना केंद्र (livewire/notification-center) -----------------
    'panel_title'                          => 'अधिसूचनाएँ',
    'mark_all_read'                        => 'सभी को पढ़ा हुआ चिह्नित करें',
    'aria_label'                           => 'अधिसूचनाएँ',
    'dismiss'                              => 'खारिज करें',
    'empty_state'                          => 'अभी तक कोई अधिसूचना नहीं',
    'load_more'                            => 'और लोड करें',

    /*
    |--------------------------------------------------------------------------
    | उपयोगकर्ता-मुखी अधिसूचना पाठ (app/Notifications/*.php)
    |--------------------------------------------------------------------------
    |
    | कतारबद्ध Notification कक्षाओं से उपयोगकर्ता को प्रदर्शित स्ट्रिंग्स —
    | toMail() विषय पंक्तियाँ, शीर्षक, मुख्य पंक्तियाँ, क्रिया बटन लेबल,
    | toDatabase() संदेश जो घंटी आइकन ड्रॉपडाउन में दिखाए जाते हैं, और
    | प्रसारण पुश शीर्षक। प्लेसहोल्डर :placeholder परंपरा का उपयोग करते हैं।
    |
    */

    // --- साझा बटन लेबल / फ़ॉलबैक ---------------------------------------
    'btn_view_lead'                        => 'लीड देखें',
    'system_fallback'                      => 'सिस्टम',

    // --- ExportFailedNotification ---------------------------------------
    'export_failed_mail_subject'           => 'लीड निर्यात विफल',
    'export_failed_mail_headline'          => 'निर्यात विफल',
    'export_failed_mail_line_intro'        => 'दुर्भाग्य से आपका लीड निर्यात पूरा नहीं हो सका।',
    'export_failed_mail_line_reason'       => '<strong>कारण:</strong> :reason',
    'export_failed_mail_line_retry'        => 'कृपया पुनः प्रयास करें। यदि समस्या बनी रहती है, तो अपने व्यवस्थापक से संपर्क करें।',
    'export_failed_db_message'             => 'आपका लीड निर्यात विफल: :error',

    // --- IntegrationSyncFailedNotification ------------------------------
    'integration_sync_failed_broadcast_title' => ':lead पर :integration के लिए सिंक विफल',
    'integration_sync_failed_push_title'   => 'एकीकरण सिंक विफल',
    'integration_sync_failed_mail_subject' => 'एकीकरण सिंक विफल: :integration',
    'integration_sync_failed_mail_headline' => 'एकीकरण सिंक विफल',
    'integration_sync_failed_mail_line_intro' => 'आपके एक लीड के लिए एकीकरण सिंक विफल हो गया।',
    'integration_sync_failed_mail_line_integration' => '<strong>एकीकरण:</strong> :integration',
    'integration_sync_failed_mail_line_lead' => '<strong>लीड:</strong> :name',
    'integration_sync_failed_mail_line_error' => '<strong>त्रुटि:</strong> :error',

    // --- LeadAssignedNotification ---------------------------------------
    'lead_assigned_broadcast_title'        => 'लीड सौंपा गया: :name',
    'lead_assigned_push_title'             => 'लीड आपको सौंपा गया',
    'lead_assigned_mail_subject'           => 'लीड आपको सौंपा गया',
    'lead_assigned_mail_headline'          => 'लीड आपको सौंपा गया',
    'lead_assigned_mail_line_intro'        => 'एक लीड आपको सौंपा गया है।',
    'lead_assigned_mail_line_lead'         => '<strong>लीड:</strong> :name',
    'lead_assigned_mail_line_assigned_by'  => '<strong>सौंपा गया द्वारा:</strong> :name',

    // --- LeadAutomationNotification -------------------------------------
    'automation_push_title'                => 'स्वचालन ट्रिगर',
    'automation_mail_subject'              => 'स्वचालन ट्रिगर: :message',
    'automation_mail_headline'             => 'स्वचालन ट्रिगर',
    'automation_mail_line_intro'           => 'आपकी पाइपलाइन में एक लीड के लिए एक स्वचालन ट्रिगर किया गया।',
    'automation_mail_line_lead'            => '<strong>लीड:</strong> :name',
    'automation_mail_line_message'         => '<strong>संदेश:</strong> :message',

    // --- LeadReceivedNotification ---------------------------------------
    'lead_received_broadcast_title'        => 'नया लीड: :name',
    'lead_received_push_title'             => 'नया लीड प्राप्त',
    'lead_received_mail_subject'           => 'नया लीड: :name',
    'lead_received_mail_headline'          => 'नया लीड प्राप्त',
    'lead_received_mail_line_intro'        => 'आपकी पाइपलाइन में एक नया लीड प्राप्त हुआ है।',
    'lead_received_mail_line_name'         => '<strong>नाम:</strong> :name',
    'lead_received_mail_line_email'        => '<strong>ईमेल:</strong> :email',
    'lead_received_mail_line_phone'        => '<strong>फ़ोन:</strong> :phone',
    'lead_received_mail_line_source'       => '<strong>स्रोत:</strong> :source',

    // --- LeadStageChangedNotification -----------------------------------
    'lead_stage_changed_broadcast_title'   => 'लीड :stage पर स्थानांतरित: :name',
    'lead_stage_changed_push_title'        => 'लीड चरण बदला',
    'lead_stage_changed_push_body'         => ':name :stage पर स्थानांतरित',
    'lead_stage_changed_mail_subject'      => 'लीड चरण अपडेट',
    'lead_stage_changed_mail_headline'     => 'लीड चरण अपडेट',
    'lead_stage_changed_mail_line_intro'   => 'एक लीड एक नए पाइपलाइन चरण पर स्थानांतरित हो गया है।',
    'lead_stage_changed_mail_line_lead'    => '<strong>लीड:</strong> :name',
    'lead_stage_changed_mail_line_from'    => '<strong>से:</strong> :stage',
    'lead_stage_changed_mail_line_to'      => '<strong>तक:</strong> :stage',

    // --- LeadTaskReminderNotification -----------------------------------
    'task_reminder_db_message'             => 'कार्य अनुस्मारक: :title',
    'task_reminder_mail_subject'           => 'कार्य अनुस्मारक: :title',
    'task_reminder_mail_greeting'          => 'कार्य अनुस्मारक',
    'task_reminder_mail_line_intro'        => 'आपके पास एक आगामी कार्य है:',
    'task_reminder_mail_line_lead'         => 'संबंधित लीड: :name',
    'task_reminder_mail_line_due'          => 'देय: :due',
    'task_reminder_mail_line_priority'     => 'प्राथमिकता: :priority',
    'task_reminder_mail_line_description'  => 'विवरण: :description',
    'task_reminder_mail_line_outro'        => ':app का उपयोग करने के लिए धन्यवाद।',
    'task_reminder_due_no_date'            => 'कोई तिथि नहीं',
    'task_reminder_lead_fallback'          => 'एक लीड',
    'task_reminder_priority_normal'        => 'सामान्य',

    // --- LeadsExportReady -----------------------------------------------
    'export_ready_db_message'              => 'आपका लीड निर्यात डाउनलोड के लिए तैयार है।',
    'export_ready_push_title'              => 'निर्यात तैयार',
    'export_ready_push_body'               => 'आपका लीड निर्यात (:filename) डाउनलोड के लिए तैयार है।',
    'export_ready_mail_subject'            => 'आपका निर्यात तैयार है: :filename',
    'export_ready_mail_headline'           => 'निर्यात डाउनलोड के लिए तैयार',
    'export_ready_mail_line_intro'         => 'आपका लीड निर्यात तैयार किया गया है और डाउनलोड के लिए तैयार है।',
    'export_ready_mail_line_file'          => '<strong>फ़ाइल:</strong> :filename',
    'export_ready_btn_download'            => 'निर्यात डाउनलोड करें',

    // --- MarketplaceTemplateInstalledNotification -----------------------
    'marketplace_template_installed_message' => ':installer ने आपका ":template" टेम्पलेट (:type) स्थापित किया।',

    // --- TenantDataExportReady ------------------------------------------
    'tenant_data_export_ready_message'     => '":name" के लिए आपका डेटा निर्यात तैयार है। डाउनलोड लिंक 24 घंटे में समाप्त हो जाएगा।',

    // --- InvoiceDueReminderNotification ---------------------------------
    'invoice_due_reminder_db_message'         => 'इनवॉइस :number जल्द ही देय है।',
    'invoice_due_reminder_customer_fallback'  => 'आपका ग्राहक',
    'invoice_due_reminder_due_no_date'        => 'कोई तिथि नहीं',
    'invoice_due_reminder_mail_subject'       => 'इनवॉइस :number जल्द ही देय है',
    'invoice_due_reminder_mail_greeting'      => 'इनवॉइस देय अनुस्मारक',
    'invoice_due_reminder_mail_line_intro'    => 'आपकी एक इनवॉइस अपनी देय तिथि के निकट है:',
    'invoice_due_reminder_mail_line_number'   => 'इनवॉइस: :number',
    'invoice_due_reminder_mail_line_customer' => 'ग्राहक: :name',
    'invoice_due_reminder_mail_line_amount'   => 'राशि: :amount',
    'invoice_due_reminder_mail_line_due'      => 'देय: :due',
    'invoice_due_reminder_btn_view'           => 'इनवॉइस देखें',
    'invoice_due_reminder_mail_line_outro'    => ':app का उपयोग करने के लिए धन्यवाद।',
];
