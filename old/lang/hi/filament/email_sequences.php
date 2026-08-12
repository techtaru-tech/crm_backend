<?php

declare(strict_types=1);

return [

    'nav_label'          => 'ईमेल अनुक्रम',

    'model_label'        => 'ईमेल अनुक्रम',
    'plural_model_label' => 'ईमेल अनुक्रम',

    'status'             => 'स्थिति',

    'item_label_step_prefix' => 'चरण — ',
    'item_label_day_short'   => 'दि',
    'item_label_hour_short'  => 'घं',
    'item_label_no_subject'  => '(कोई विषय नहीं)',

    'sequence_name'      => 'अनुक्रम का नाम',
    'description'        => 'विवरण',

    'stop_on_reply'      => 'लीड के उत्तर देने पर रोकें',
    'stop_on_reply_help' => 'जब लीड से एक आवक ईमेल दर्ज की जाती है तो स्वतः नामांकन रद्द करें।',
    'stop_on_won'        => 'लीड जीतने पर रोकें',
    'stop_on_won_help'   => 'जब लीड की स्थिति "जीती" हो जाए तो स्वतः नामांकन रद्द करें।',

    'steps_description'  => 'ईमेल ऊपर से नीचे भेजे जाते हैं। विलंब पिछले चरण से मापा जाता है (या पहले चरण के लिए नामांकन से)।',
    'add_step'           => 'चरण जोड़ें',
    'delay_days'         => 'विलंब (दिन)',
    'delay_hours'        => 'विलंब (घंटे)',
    'load_template'      => 'टेम्पलेट से लोड करें',
    'load_template_help' => 'नीचे विषय और मुख्य भाग भरने के लिए एक सहेजा गया ईमेल टेम्पलेट चुनें। लोड करने के बाद भी आप उन्हें संपादित कर सकते हैं।',
    'subject'            => 'विषय',
    'subject_help'       => 'प्लेसहोल्डर: {first_name}, {last_name}, {company}, {email}',
    'body'               => 'मुख्य भाग',
    'body_help'          => 'प्लेसहोल्डर: {first_name}, {last_name}, {company}, {email}',

    'filter_label_status' => 'स्थिति',

    'col_name'           => 'नाम',
    'col_status'         => 'स्थिति',
    'col_steps'          => 'चरण',
    'col_active_enroll'  => 'सक्रिय नामांकन',
    'col_completed'      => 'पूर्ण',
    'col_created'        => 'बनाया गया',

    'preview'            => 'पूर्वावलोकन',
    'preview_modal_heading' => 'पूर्वावलोकन: :name',
    'preview_description' => 'टोकन प्रतिस्थापन नमूना डेटा के साथ दिखाया गया — {first_name}=जेन, {last_name}=डो, {company}=Acme Inc, {email}=jane@acme.com।',
    'preview_close'      => 'बंद करें',
    'send_test'          => 'परीक्षण भेजें',
    'send_test_to'       => 'परीक्षण भेजें',
    'which_step'         => 'कौन सा चरण?',
    'duplicate'          => 'डुप्लीकेट',

    'notif_step_not_found'        => 'चरण नहीं मिला।',
    'notif_test_email_sent'       => ':email को परीक्षण ईमेल भेजा गया',
    'notif_test_email_failed'     => 'भेजना विफल: :error',
    'notif_sequence_duplicated'   => 'अनुक्रम डुप्लीकेट हो गया।',
    'notif_duplicate_failed'      => 'अनुक्रम डुप्लीकेट नहीं किया जा सका।',

    'enrollments_relation_title'  => 'नामांकन',
    'col_lead'                    => 'लीड',
    'col_email'                   => 'ईमेल',
    'col_step'                    => 'चरण',
    'col_next_send'               => 'अगला भेजना',
    'col_next_send_at'            => 'अगला भेजने का समय',
    'col_enrolled_at'             => 'नामांकित',

    'preview_delay_label'         => 'विलंब',
    'preview_sample_lead'         => 'नमूना लीड के साथ पूर्वावलोकन',
    'preview_no_steps'            => 'अभी तक कोई चरण परिभाषित नहीं। संपादन पृष्ठ पर चरण जोड़ें।',

    'preview_delay_immediate'     => 'तत्काल',
    'test_send_step_option_label' => 'चरण :step — ',
    'test_subject_prefix'         => '[परीक्षण] :subject',
    'preview_sample_first_name'   => 'जेन',
    'preview_sample_last_name'    => 'डो',
    'preview_sample_company_name' => 'Acme Inc',
    'preview_sample_email'        => 'jane@acme.com',

    'option_status_draft'          => 'ड्राफ़्ट',
    'option_status_active'         => 'सक्रिय',
    'option_status_paused'         => 'रोका गया',
    'option_enrollment_active'     => 'सक्रिय',
    'option_enrollment_completed'  => 'पूर्ण',
    'option_enrollment_replied'    => 'उत्तर दिया',
    'option_enrollment_unenrolled' => 'नामांकन रद्द',

    'status_draft'                 => 'ड्राफ़्ट',
    'status_active'                => 'सक्रिय',
    'status_paused'                => 'रोका गया',

    'duplicate_copy_suffix'        => '(कॉपी)',

    'delay_days_short'             => 'दि',
    'delay_hours_short'            => 'घं',
];
