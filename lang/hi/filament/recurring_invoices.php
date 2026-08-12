<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — RecurringInvoiceResource अनुवाद स्ट्रिंग्स
|--------------------------------------------------------------------------
|
| Tenant "आवर्ती इनवॉइस / देय राशि" CRUD।
| __('filament/recurring_invoices.<key>') के माध्यम से उपयोग किया जाता है।
|
*/

return [

    'nav_label'           => 'आवर्ती इनवॉइस',
    'model_label'         => 'आवर्ती इनवॉइस',
    'plural_model_label'  => 'आवर्ती इनवॉइस',

    // Form
    'section_schedule'      => 'आवर्ती शेड्यूल',
    'section_schedule_desc' => 'एक नियमित मासिक या वार्षिक शुल्क। LeadHub हर रन तिथि पर एक वास्तविक इनवॉइस बनाता है और जब वह देय होती है तो आपको याद दिलाता है।',
    'field_lead'            => 'सदस्य / ग्राहक',
    'field_company'         => 'कंपनी (वैकल्पिक)',
    'field_title'           => 'विवरण',
    'field_amount'          => 'राशि',
    'field_currency'        => 'मुद्रा',
    'field_interval'        => 'बिल हर',
    'interval_month'        => 'महीना',
    'interval_year'         => 'वर्ष',
    'field_anchor_day'      => 'महीने का बिलिंग दिन',
    'field_anchor_day_help' => 'वैकल्पिक। 1-28। अगली रन तिथि हर अवधि में इस दिन पर सेट हो जाती है।',
    'field_next_run_date'   => 'अगली रन तिथि',
    'field_due_days'        => 'देय के बाद (दिन)',
    'field_due_days_help'   => 'प्रत्येक इनवॉइस उत्पन्न होने के कितने दिन बाद वह देय होती है।',
    'field_auto_send'       => 'प्रत्येक इनवॉइस स्वतः भेजें',
    'field_auto_send_help'  => 'प्रत्येक उत्पन्न इनवॉइस को ड्राफ़्ट के बजाय भेजा गया के रूप में चिह्नित करें।',
    'field_active'          => 'सक्रिय',
    'field_notes'           => 'नोट्स',

    // Table
    'col_title'    => 'विवरण',
    'col_member'   => 'सदस्य',
    'col_amount'   => 'राशि',
    'col_interval' => 'अंतराल',
    'col_next_run' => 'अगली रन',
    'col_active'   => 'सक्रिय',
];
