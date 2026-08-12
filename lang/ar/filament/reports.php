<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Tenant Reports — shared Filament strings (ar)
|------------------------------------------------------------
| Accessed via __('filament/reports.<key>').
*/

return [
    // Report titles + navigation labels
    'agent_performance_title'      => 'تقرير أداء الوكيل',
    'agent_performance_nav'        => 'أداء الوكيل',
    'automation_stats_title'       => 'إحصاءات الأتمتة',
    'automation_stats_nav'         => 'إحصاءات الأتمتة',
    'form_analytics_title'         => 'تحليلات النماذج',
    'form_analytics_nav'           => 'تحليلات النماذج',
    'lead_volume_title'            => 'تقرير حجم العملاء المحتملين',
    'lead_volume_nav'              => 'حجم العملاء المحتملين',
    'pipeline_funnel_title'        => 'قمع خط الأنابيب',
    'pipeline_funnel_nav'          => 'قمع خط الأنابيب',
    'response_time_title'          => 'تقرير زمن الاستجابة',
    'response_time_nav'            => 'زمن الاستجابة',
    'source_performance_title'     => 'تقرير أداء المصدر',
    'source_performance_nav'       => 'أداء المصدر',

    // Common export actions
    'export_csv'                   => 'تصدير CSV',
    'export_pdf'                   => 'تصدير PDF',

    // Section / chart headings inside individual report views
    'agent_performance_section'    => 'أداء الوكيل',
    'automation_performance'       => 'أداء الأتمتة',
    'sequence_performance'         => 'أداء التسلسل',
    'submission_trend'             => 'اتجاه الإرسالات',
    'top_forms'                    => 'أعلى النماذج',
    'breakdown'                    => 'التفصيل',
    'pipeline_funnel_section'      => 'قمع خط الأنابيب',
    'stage_details'                => 'تفاصيل المرحلة',
    'response_time_distribution'   => 'توزيع زمن الاستجابة',
    'distribution_breakdown'       => 'تفصيل التوزيع',
    'lead_volume_by_source'        => 'حجم العملاء المحتملين حسب المصدر',
    'source_breakdown'             => 'تفصيل المصدر',

    // ─── Form Analytics view ──
    'total_submissions_label'      => 'إجمالي الإرسالات: :count',
    'fa_form_name_col'             => 'اسم النموذج',
    'fa_status_col'                => 'الحالة',
    'fa_submissions_col'           => 'الإرسالات',
    'fa_chart_dataset_label'       => 'الإرسالات',
    'fa_status_active'             => 'نشط',
    'fa_status_inactive'           => 'غير نشط',
    'fa_no_submissions_in_period'  => 'لا توجد إرسالات نموذج في هذه الفترة',

    // ─── Agent Performance view ──
    'ap_section_sub'               => 'يعرض الرسم البياني المصغّر العملاء المحتملين المعيّنين أسبوعياً (آخر 4 أسابيع)',
    'ap_agent_col'                 => 'الوكيل',
    'ap_assigned_col'              => 'المعيّنون',
    'ap_won_col'                   => 'الفائزون',
    'ap_win_rate_col'              => 'معدّل الفوز',
    'ap_avg_response_col'          => 'متوسط الاستجابة',
    'ap_avg_close_col'             => 'متوسط الإغلاق',
    'ap_activities_col'            => 'الأنشطة',
    'ap_trend_col'                 => 'الاتجاه',
    'ap_no_agents_in_period'       => 'لم يتم العثور على وكلاء لهذه الفترة',

    // ─── Automation Stats view ──
    'as_automation_col'            => 'الأتمتة',
    'as_trigger_col'               => 'المُحفّز',
    'as_status_col'                => 'الحالة',
    'as_total_runs_col'            => 'إجمالي التشغيلات',
    'as_success_runs_col'          => 'التشغيلات الناجحة',
    'as_success_rate_col'          => 'معدّل النجاح',
    'as_avg_run_time_col'          => 'متوسط وقت التشغيل',
    'as_badge_active'              => 'نشط',
    'as_badge_disabled'            => 'معطّل',
    'as_no_runs_in_period'         => 'لا توجد تشغيلات أتمتة في هذه الفترة',

    // ─── Email Sequences view ──
    'es_no_sequences'              => 'لا توجد تسلسلات بعد.',
    'es_sequence_col'              => 'التسلسل',
    'es_status_col'                => 'الحالة',
    'es_enrolled_col'              => 'المسجّلون',
    'es_completed_col'             => 'المكتمل',
    'es_replied_col'               => 'الذين ردّوا',
    'es_reply_rate_col'            => 'معدّل الرد',
    'es_open_rate_col'             => 'معدّل الفتح',
    'es_status_active'             => 'نشط',
    'es_status_paused'             => 'متوقّف مؤقتاً',
    'es_status_draft'              => 'مسودّة',
    'es_status_archived'           => 'مؤرشف',

    // ─── Lead Volume view ──
    'lv_source_label'              => 'المصدر',
    'lv_source_placeholder'        => 'مثل facebook، api…',
    'lv_group_by_label'            => 'تجميع حسب',
    'lv_group_by_day'              => 'اليوم',
    'lv_group_by_week'             => 'الأسبوع',
    'lv_group_by_month'            => 'الشهر',
    'lv_total_pill'                => 'الإجمالي: :count عميل محتمل',
    'lv_leads_over_time'           => 'العملاء المحتملون عبر الوقت',
    'lv_source_separator'          => '· المصدر: :source',
    'lv_chart_dataset_label'       => 'العملاء المحتملون',
    'lv_period_col'                => 'الفترة',
    'lv_leads_col'                 => 'العملاء المحتملون',
    'lv_no_data_in_period'         => 'لا توجد بيانات لهذه الفترة',

    // ─── Pipeline Funnel view ──
    'pf_pipeline_label'            => 'خط الأنابيب',
    'pf_leads_suffix'              => ':count عميل محتمل',
    'pf_dropoff_suffix'            => '↓ :pct% انخفاض',
    'pf_top_of_funnel'             => 'قمة القمع',
    'pf_stage_col'                 => 'المرحلة',
    'pf_lead_count_col'            => 'عدد العملاء المحتملين',
    'pf_drop_off_col'              => 'التسرّب',
    'pf_avg_days_in_stage_col'     => 'متوسط الأيام في المرحلة',
    'pf_days_suffix'               => ':count يوم',
    'pf_no_pipeline_data'          => 'لا توجد بيانات خط أنابيب متاحة. أنشئ خط أنابيب وعيّن عملاء محتملين إلى المراحل.',

    // ─── Response Time view ──
    'rt_source_label'              => 'المصدر',
    'rt_source_placeholder'        => 'مثل facebook، الموقع الإلكتروني…',
    'rt_agent_label'               => 'الوكيل',
    'rt_all_agents'                => 'كل الوكلاء',
    'rt_total_leads_analysed'      => 'إجمالي العملاء المحتملين المُحلَّلين',
    'rt_median_response_time'      => 'الوسيط لزمن الاستجابة',
    'rt_p90_label'                 => 'المئين 90',
    'rt_filters_active'            => 'المرشّحات النشطة:',
    'rt_filter_source_label'       => 'المصدر:',
    'rt_filter_agent_label'        => 'الوكيل:',
    'rt_chart_dataset_label'       => 'العملاء المحتملون',
    'rt_time_bucket_col'           => 'فئة الوقت',
    'rt_leads_col'                 => 'العملاء المحتملون',
    'rt_percent_of_total_col'      => '٪ من الإجمالي',

    // ─── Source Performance view ──
    'sp_chart_dataset_label'       => 'العملاء المحتملون',
    'sp_section_sub'               => 'يقارن عمود الاتجاه بالفترة السابقة المكافئة',
    'sp_source_col'                => 'المصدر',
    'sp_total_leads_col'           => 'إجمالي العملاء المحتملين',
    'sp_vs_prev_period_col'        => 'مقابل الفترة السابقة',
    'sp_converted_col'             => 'المحوّلون',
    'sp_conversion_rate_col'       => 'نسبة التحويل %',
    'sp_avg_score_col'             => 'متوسط الدرجة',
    'sp_no_data_in_period'         => 'لا توجد بيانات لهذه الفترة',

    // ─── Shared duration / unit suffixes ─────────────────────────────
    'duration_minutes_short'       => ':n د',
    'duration_hours_short'         => ':n س',
    'duration_seconds_short'       => ':n ث',
    'duration_days_short'          => ':n ي',

    // Histogram bucket labels (Response Time report).
    'rt_bucket_under_5m'           => '< 5 د',
    'rt_bucket_5_15m'              => '5-15 د',
    'rt_bucket_15_60m'             => '15-60 د',
    'rt_bucket_1_4h'               => '1-4 س',
    'rt_bucket_4_24h'              => '4-24 س',
    'rt_bucket_over_24h'           => '> 24 س',

    // Automation Stats — fallback humanised trigger label.
    'as_trigger_humanised_fallback' => ':label',

    // Integration Sync Logs — fallback humanised event label.
    'integration_sync_event_fallback' => ':label',
];
