<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Tenant Reports — shared Filament strings (hi)
|------------------------------------------------------------
| Accessed via __('filament/reports.<key>').
*/

return [
    // Report titles + navigation labels
    'agent_performance_title'      => 'एजेंट प्रदर्शन रिपोर्ट',
    'agent_performance_nav'        => 'एजेंट प्रदर्शन',
    'automation_stats_title'       => 'स्वचालन आँकड़े',
    'automation_stats_nav'         => 'स्वचालन आँकड़े',
    'form_analytics_title'         => 'फॉर्म विश्लेषण',
    'form_analytics_nav'           => 'फॉर्म विश्लेषण',
    'lead_volume_title'            => 'लीड वॉल्यूम रिपोर्ट',
    'lead_volume_nav'              => 'लीड वॉल्यूम',
    'pipeline_funnel_title'        => 'पाइपलाइन फ़नल',
    'pipeline_funnel_nav'          => 'पाइपलाइन फ़नल',
    'response_time_title'          => 'प्रतिक्रिया समय रिपोर्ट',
    'response_time_nav'            => 'प्रतिक्रिया समय',
    'source_performance_title'     => 'स्रोत प्रदर्शन रिपोर्ट',
    'source_performance_nav'       => 'स्रोत प्रदर्शन',

    // Common export actions
    'export_csv'                   => 'CSV निर्यात करें',
    'export_pdf'                   => 'PDF निर्यात करें',

    // Section / chart headings inside individual report views
    'agent_performance_section'    => 'एजेंट प्रदर्शन',
    'automation_performance'       => 'स्वचालन प्रदर्शन',
    'sequence_performance'         => 'सीक्वेंस प्रदर्शन',
    'submission_trend'             => 'सबमिशन ट्रेंड',
    'top_forms'                    => 'शीर्ष फॉर्म',
    'breakdown'                    => 'विवरण',
    'pipeline_funnel_section'      => 'पाइपलाइन फ़नल',
    'stage_details'                => 'चरण विवरण',
    'response_time_distribution'   => 'प्रतिक्रिया समय वितरण',
    'distribution_breakdown'       => 'वितरण विवरण',
    'lead_volume_by_source'        => 'स्रोत के अनुसार लीड वॉल्यूम',
    'source_breakdown'             => 'स्रोत विवरण',

    // ─── Form Analytics view ──
    'total_submissions_label'      => 'कुल सबमिशन: :count',
    'fa_form_name_col'             => 'फॉर्म का नाम',
    'fa_status_col'                => 'स्थिति',
    'fa_submissions_col'           => 'सबमिशन',
    'fa_chart_dataset_label'       => 'सबमिशन',
    'fa_status_active'             => 'सक्रिय',
    'fa_status_inactive'           => 'निष्क्रिय',
    'fa_no_submissions_in_period'  => 'इस अवधि में कोई फॉर्म सबमिशन नहीं',

    // ─── Agent Performance view ──
    'ap_section_sub'               => 'स्पार्कलाइन साप्ताहिक असाइन की गई लीड दिखाती है (पिछले 4 सप्ताह)',
    'ap_agent_col'                 => 'एजेंट',
    'ap_assigned_col'              => 'असाइन की गई',
    'ap_won_col'                   => 'जीती गई',
    'ap_win_rate_col'              => 'जीत दर',
    'ap_avg_response_col'          => 'औसत प्रतिक्रिया',
    'ap_avg_close_col'             => 'औसत क्लोज़',
    'ap_activities_col'            => 'गतिविधियाँ',
    'ap_trend_col'                 => 'रुझान',
    'ap_no_agents_in_period'       => 'इस अवधि के लिए कोई एजेंट नहीं मिला',

    // ─── Automation Stats view ──
    'as_automation_col'            => 'स्वचालन',
    'as_trigger_col'               => 'ट्रिगर',
    'as_status_col'                => 'स्थिति',
    'as_total_runs_col'            => 'कुल रन',
    'as_success_runs_col'          => 'सफल रन',
    'as_success_rate_col'          => 'सफलता दर',
    'as_avg_run_time_col'          => 'औसत रन समय',
    'as_badge_active'              => 'सक्रिय',
    'as_badge_disabled'            => 'अक्षम',
    'as_no_runs_in_period'         => 'इस अवधि में कोई स्वचालन रन नहीं',

    // ─── Email Sequences view ──
    'es_no_sequences'              => 'अभी तक कोई सीक्वेंस नहीं।',
    'es_sequence_col'              => 'सीक्वेंस',
    'es_status_col'                => 'स्थिति',
    'es_enrolled_col'              => 'नामांकित',
    'es_completed_col'             => 'पूर्ण',
    'es_replied_col'               => 'जवाब दिया',
    'es_reply_rate_col'            => 'जवाब दर',
    'es_open_rate_col'             => 'ओपन दर',
    'es_status_active'             => 'सक्रिय',
    'es_status_paused'             => 'रुका हुआ',
    'es_status_draft'              => 'मसौदा',
    'es_status_archived'           => 'संग्रहीत',

    // ─── Lead Volume view ──
    'lv_source_label'              => 'स्रोत',
    'lv_source_placeholder'        => 'जैसे facebook, api…',
    'lv_group_by_label'            => 'इसके अनुसार समूहित करें',
    'lv_group_by_day'              => 'दिन',
    'lv_group_by_week'             => 'सप्ताह',
    'lv_group_by_month'            => 'महीना',
    'lv_total_pill'                => 'कुल: :count लीड',
    'lv_leads_over_time'           => 'समय के साथ लीड',
    'lv_source_separator'          => '· स्रोत: :source',
    'lv_chart_dataset_label'       => 'लीड',
    'lv_period_col'                => 'अवधि',
    'lv_leads_col'                 => 'लीड',
    'lv_no_data_in_period'         => 'इस अवधि के लिए कोई डेटा नहीं',

    // ─── Pipeline Funnel view ──
    'pf_pipeline_label'            => 'पाइपलाइन',
    'pf_leads_suffix'              => ':count लीड',
    'pf_dropoff_suffix'            => '↓ :pct% ड्रॉप-ऑफ',
    'pf_top_of_funnel'             => 'फ़नल का शीर्ष',
    'pf_stage_col'                 => 'चरण',
    'pf_lead_count_col'            => 'लीड की संख्या',
    'pf_drop_off_col'              => 'ड्रॉप-ऑफ',
    'pf_avg_days_in_stage_col'     => 'चरण में औसत दिन',
    'pf_days_suffix'               => ':count दिन',
    'pf_no_pipeline_data'          => 'कोई पाइपलाइन डेटा उपलब्ध नहीं। एक पाइपलाइन बनाएँ और लीड को चरणों में असाइन करें।',

    // ─── Response Time view ──
    'rt_source_label'              => 'स्रोत',
    'rt_source_placeholder'        => 'जैसे facebook, website…',
    'rt_agent_label'               => 'एजेंट',
    'rt_all_agents'                => 'सभी एजेंट',
    'rt_total_leads_analysed'      => 'कुल विश्लेषित लीड',
    'rt_median_response_time'      => 'मध्यिका प्रतिक्रिया समय',
    'rt_p90_label'                 => '90वाँ पर्सेंटाइल',
    'rt_filters_active'            => 'सक्रिय फ़िल्टर:',
    'rt_filter_source_label'       => 'स्रोत:',
    'rt_filter_agent_label'        => 'एजेंट:',
    'rt_chart_dataset_label'       => 'लीड',
    'rt_time_bucket_col'           => 'समय बकेट',
    'rt_leads_col'                 => 'लीड',
    'rt_percent_of_total_col'      => 'कुल का %',

    // ─── Source Performance view ──
    'sp_chart_dataset_label'       => 'लीड',
    'sp_section_sub'               => 'रुझान कॉलम समकक्ष पूर्ववर्ती अवधि से तुलना करता है',
    'sp_source_col'                => 'स्रोत',
    'sp_total_leads_col'           => 'कुल लीड',
    'sp_vs_prev_period_col'        => 'पिछली अवधि बनाम',
    'sp_converted_col'             => 'परिवर्तित',
    'sp_conversion_rate_col'       => 'रूपांतरण %',
    'sp_avg_score_col'             => 'औसत स्कोर',
    'sp_no_data_in_period'         => 'इस अवधि के लिए कोई डेटा नहीं',

    // ─── Shared duration / unit suffixes ─────────────────────────────
    'duration_minutes_short'       => ':n मि',
    'duration_hours_short'         => ':n घं',
    'duration_seconds_short'       => ':n से',
    'duration_days_short'          => ':n दि',

    // Histogram bucket labels (Response Time report).
    'rt_bucket_under_5m'           => '< 5 मि',
    'rt_bucket_5_15m'              => '5-15 मि',
    'rt_bucket_15_60m'             => '15-60 मि',
    'rt_bucket_1_4h'               => '1-4 घं',
    'rt_bucket_4_24h'              => '4-24 घं',
    'rt_bucket_over_24h'           => '> 24 घं',

    // Automation Stats — fallback humanised trigger label.
    'as_trigger_humanised_fallback' => ':label',

    // Integration Sync Logs — fallback humanised event label.
    'integration_sync_event_fallback' => ':label',
];
