<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — OutboundWebhookResource translation strings
|--------------------------------------------------------------------------
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'आउटबाउंड Webhooks',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'आउटबाउंड Webhook',
    'plural_model_label'                => 'आउटबाउंड Webhooks',

    // ─── Table columns ─────────────────────────────────────────────────
    'col_name'                          => 'नाम',
    'col_url'                           => 'URL',
    'col_event'                         => 'इवेंट',
    'col_events'                        => 'इवेंट्स',
    'col_enabled'                       => 'सक्षम',
    'col_created'                       => 'निर्मित',
    'col_status'                        => 'स्थिति',

    // ─── Form: webhook configuration ───────────────────────────────────
    'webhook_name'                      => 'Webhook नाम',
    'webhook_name_placeholder'          => 'जैसे: नए लीड पर Slack को सूचित करें',
    'endpoint_url'                      => 'एंडपॉइंट URL',
    'endpoint_url_placeholder'          => 'https://your-endpoint.com/webhook',
    'trigger_events'                    => 'ट्रिगर इवेंट्स',
    'signing_secret'                    => 'साइनिंग सीक्रेट',
    'signing_secret_helper'             => 'HMAC-SHA256 हस्ताक्षर के लिए उपयोग किया जाता है। यदि खाली छोड़ा जाए तो स्वतः उत्पन्न।',
    'payload_filters'                   => 'पेलोड फ़िल्टर (वैकल्पिक)',
    'payload_filters_helper'            => 'JSON ऑब्जेक्ट जो यह फ़िल्टर करता है कि कौन से इवेंट्स इस Webhook को ट्रिगर करते हैं। केवल तभी ट्रिगर होता है जब सभी फ़िल्टर मेल खाते हैं। सभी इवेंट्स प्राप्त करने के लिए खाली छोड़ दें। समर्थित कुंजियाँ: source, status, pipeline_id, pipeline_stage_id, assigned_user_id, tags. tags फ़िल्टर तब ट्रिगर होता है जब लीड में कम से कम एक मेल खाने वाला टैग हो। उदाहरण: {"source":["facebook","api"],"status":["new"],"tags":["Hot Lead"]}',
    'payload_filters_placeholder'       => '{"source":["facebook"],"status":["new"]}',
    'enabled'                           => 'सक्षम',

    // ─── Table columns ─────────────────────────────────────────────────
    'deliveries'                        => 'डिलीवरी',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_send_test'                  => 'टेस्ट भेजें',
    'action_view_deliveries'            => 'डिलीवरी देखें',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'अभी तक कोई Webhooks नहीं',
    'empty_description'                 => 'रीयल-टाइम इवेंट सूचनाएँ प्राप्त करने के लिए एक आउटबाउंड Webhook जोड़ें।',

    // ─── Delivery Log page ─────────────────────────────────────────────
    'delivery_log_title_prefix'         => 'डिलीवरी लॉग: ',
    'action_back_to_webhooks'           => 'Webhooks पर वापस जाएँ',
    'col_http'                          => 'HTTP',
    'col_latency'                       => 'विलंबता',
    'col_attempts'                      => 'प्रयास',
    'col_sent'                          => 'भेजा गया',
    'action_payload'                    => 'पेलोड',
    'sent_payload_modal_prefix'         => 'भेजा गया पेलोड — ',
    'modal_close'                       => 'बंद करें',
    'action_response'                   => 'प्रतिक्रिया',
    'response_body_modal_prefix'        => 'प्रतिक्रिया मुख्य भाग — HTTP ',
    'no_response_body'                  => 'कोई प्रतिक्रिया मुख्य भाग रिकॉर्ड नहीं किया गया।',
    'action_retry'                      => 'पुनः प्रयास करें',
    'log_empty_heading'                 => 'अभी तक कोई डिलीवरी नहीं',
    'log_empty_description'             => 'ट्रिगर होने के बाद टेस्ट डिलीवरी और लाइव इवेंट यहाँ दिखाई देंगे।',
    'new_webhook'                       => 'नया Webhook',

    // ─── Delivery log: stats cards ─────────────────────────────────────
    'webhook_title_prefix'              => 'Webhook: :name',
    'stat_total_deliveries'             => 'कुल डिलीवरी',
    'stat_successful'                   => 'सफल',
    'stat_failed'                       => 'विफल',

    // ─── Test delivery payload body ────────────────────────────────────
    'test_delivery_body'                => 'यह LeadHub से एक टेस्ट डिलीवरी है।',

    // ─── Delivery status badges ────────────────────────────────────────
    'status_success'                    => 'सफलता',
    'status_failed'                     => 'विफल',
    'status_retrying'                   => 'पुनः प्रयास',

    // ─── Latency suffix ────────────────────────────────────────────────
    'latency_ms_suffix'                 => 'मि.से.',

    // ─── Event badge labels ────────────────────────────────────────────
    'event_test'                        => 'टेस्ट',
    'event_lead_created'                => 'लीड बनाया गया',
    'event_lead_updated'                => 'लीड अपडेट किया गया',
    'event_lead_deleted'                => 'लीड हटाया गया',
    'event_lead_stage_changed'          => 'लीड चरण बदला',
    'event_form_submitted'              => 'फ़ॉर्म जमा किया गया',
    'event_automation_triggered'        => 'ऑटोमेशन ट्रिगर किया गया',
];
