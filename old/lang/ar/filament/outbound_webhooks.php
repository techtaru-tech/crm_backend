<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — OutboundWebhookResource translation strings
|--------------------------------------------------------------------------
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Webhooks الصادرة',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Webhook صادر',
    'plural_model_label'                => 'Webhooks الصادرة',

    // ─── Table columns ─────────────────────────────────────────────────
    'col_name'                          => 'الاسم',
    'col_url'                           => 'URL',
    'col_event'                         => 'الحدث',
    'col_events'                        => 'الأحداث',
    'col_enabled'                       => 'مُفعَّل',
    'col_created'                       => 'تاريخ الإنشاء',
    'col_status'                        => 'الحالة',

    // ─── Form: webhook configuration ───────────────────────────────────
    'webhook_name'                      => 'اسم Webhook',
    'webhook_name_placeholder'          => 'مثلاً: إشعار Slack عند عميل محتمل جديد',
    'endpoint_url'                      => 'URL نقطة النهاية',
    'endpoint_url_placeholder'          => 'https://your-endpoint.com/webhook',
    'trigger_events'                    => 'أحداث الإطلاق',
    'signing_secret'                    => 'سر التوقيع',
    'signing_secret_helper'             => 'يُستخدَم لتوقيع HMAC-SHA256. يُنشَأ تلقائياً إذا تُرك فارغاً.',
    'payload_filters'                   => 'مرشحات الحمولة (اختياري)',
    'payload_filters_helper'            => 'كائن JSON لتصفية الأحداث التي تُطلِق هذا Webhook. لا يُطلَق إلا عند تطابق جميع المرشحات. اتركه فارغاً لاستقبال جميع الأحداث. المفاتيح المدعومة: source، status، pipeline_id، pipeline_stage_id، assigned_user_id، tags. يُطلَق مرشح tags عندما يحمل العميل المحتمل وسماً واحداً مطابقاً على الأقل. مثال: {"source":["facebook","api"],"status":["new"],"tags":["Hot Lead"]}',
    'payload_filters_placeholder'       => '{"source":["facebook"],"status":["new"]}',
    'enabled'                           => 'مُفعَّل',

    // ─── Table columns ─────────────────────────────────────────────────
    'deliveries'                        => 'عمليات التسليم',

    // ─── Actions ───────────────────────────────────────────────────────
    'action_send_test'                  => 'إرسال اختبار',
    'action_view_deliveries'            => 'عرض عمليات التسليم',

    // ─── Empty state ───────────────────────────────────────────────────
    'empty_heading'                     => 'لا توجد Webhooks بعد',
    'empty_description'                 => 'أضف Webhook صادراً لتلقي إشعارات الأحداث في الوقت الفعلي.',

    // ─── Delivery Log page ─────────────────────────────────────────────
    'delivery_log_title_prefix'         => 'سجل التسليم: ',
    'action_back_to_webhooks'           => 'العودة إلى Webhooks',
    'col_http'                          => 'HTTP',
    'col_latency'                       => 'زمن الاستجابة',
    'col_attempts'                      => 'المحاولات',
    'col_sent'                          => 'مُرسَل',
    'action_payload'                    => 'الحمولة',
    'sent_payload_modal_prefix'         => 'الحمولة المرسَلة — ',
    'modal_close'                       => 'إغلاق',
    'action_response'                   => 'الاستجابة',
    'response_body_modal_prefix'        => 'محتوى الاستجابة — HTTP ',
    'no_response_body'                  => 'لم يُسجَّل محتوى استجابة.',
    'action_retry'                      => 'إعادة المحاولة',
    'log_empty_heading'                 => 'لا توجد عمليات تسليم بعد',
    'log_empty_description'             => 'ستظهر عمليات التسليم الاختبارية والأحداث المباشرة هنا بمجرد إطلاقها.',
    'new_webhook'                       => 'Webhook جديد',

    // ─── Delivery log: stats cards ─────────────────────────────────────
    'webhook_title_prefix'              => 'Webhook: :name',
    'stat_total_deliveries'             => 'إجمالي عمليات التسليم',
    'stat_successful'                   => 'الناجحة',
    'stat_failed'                       => 'الفاشلة',

    // ─── Test delivery payload body ────────────────────────────────────
    'test_delivery_body'                => 'هذا تسليم اختباري من LeadHub.',

    // ─── Delivery status badges ────────────────────────────────────────
    'status_success'                    => 'نجاح',
    'status_failed'                     => 'فشل',
    'status_retrying'                   => 'إعادة المحاولة',

    // ─── Latency suffix ────────────────────────────────────────────────
    'latency_ms_suffix'                 => 'م.ث',

    // ─── Event badge labels ────────────────────────────────────────────
    'event_test'                        => 'اختبار',
    'event_lead_created'                => 'تم إنشاء عميل محتمل',
    'event_lead_updated'                => 'تم تحديث عميل محتمل',
    'event_lead_deleted'                => 'تم حذف عميل محتمل',
    'event_lead_stage_changed'          => 'تغيرت مرحلة العميل المحتمل',
    'event_form_submitted'              => 'تم إرسال نموذج',
    'event_automation_triggered'        => 'تم إطلاق الأتمتة',
];
