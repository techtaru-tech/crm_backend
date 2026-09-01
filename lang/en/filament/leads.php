<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | LeadResource translation strings
 |--------------------------------------------------------------------------
 |
 | All user-facing strings used by LeadResource and its Pages + Relation
 | Managers. Keys are snake_case English. Grouped by logical UI section.
 | Accessed via __('filament/leads.<key>').
 |
 */

return [
    // ─── Navigation ───
    'nav_label'                     => 'All Leads',

    // ─── Model labels ───
    'model_label'                   => 'Lead',
    'plural_model_label'            => 'Leads',

    // ─── Global search ───
    'search_result_fallback'        => 'Lead #:id',
    'search_result_email'           => 'Email',
    'search_result_source'          => 'Source',
    'search_result_status'          => 'Status',
    'search_result_score'           => 'Score',
    'search_result_score_value'     => ':score pts',

    // ─── Form: contact info ───
    'first_name'                    => 'First Name',
    'last_name'                     => 'Last Name',
    'email'                         => 'Email',
    'phone'                         => 'Phone',
    'company'                       => 'Company',
    'company_name'                  => 'Company Name',
    'domain'                        => 'Domain',
    'industry'                      => 'Industry',

    // ─── Form: lead details ───
    'source'                        => 'Source',
    'custom_source_label'           => 'Custom source name (e.g. Walk-in, Exhibition)',
    'status'                        => 'Status',
    'assigned_to'                   => 'Assigned To',
    'pipeline'                      => 'Pipeline',
    'stage'                         => 'Stage',
    'stage_needs_pipeline'      => 'Select a pipeline first',
    'stage_placeholder'         => 'Select a stage',
    'stage_needs_pipeline_help' => 'Stages belong to a pipeline — choose a Pipeline above to pick a Stage.',
    'score'                         => 'Score',
    'starred'                       => 'Starred',
    'lead_notes'                    => 'Notes',

    // ─── Form: deal ───
    'deal_value'                    => 'Deal Value',
    'currency'                      => 'Currency',
    'expected_close_date'           => 'Expected Close Date',
    'lost_reason'                   => 'Lost Reason',

    // ─── Form: additional info ───
    'source_reference_id'           => 'Source Reference ID',
    'last_contacted'                => 'Last Contacted',

    // ─── Form: attribution ───
    'attribution_description'       => 'Captured from the form or widget that created this lead.',
    'utm_source'                    => 'UTM Source',
    'utm_medium'                    => 'UTM Medium',
    'utm_campaign'                  => 'UTM Campaign',
    'utm_content'                   => 'UTM Content',
    'utm_term'                      => 'UTM Term',
    'landing_page'                  => 'Landing Page',
    'referrer'                      => 'Referrer',
    'custom_fields_description'     => 'Tenant-defined fields. Configure in Settings → Custom Fields.',

    // ─── Table columns ───
    'name'                          => 'Name',
    'expected_close'                => 'Expected Close',
    'tags'                          => 'Tags',
    'assigned'                      => 'Assigned',
    'dup'                           => 'Dup',
    'waiting_on'                    => 'Waiting On',
    'created_at'                    => 'Created At',

    // ─── Filters ───
    'filter_label_source'           => 'Source',
    'filter_label_status'           => 'Status',
    'tag'                           => 'Tag',
    'starred_only'                  => 'Starred only',
    'not_starred'                   => 'Not starred',
    'duplicates_only'               => 'Duplicates Only',
    'waiting_us'                    => 'Us (they replied)',
    'waiting_them'                  => 'Them (we reached out)',
    'waiting_new'                   => 'New (no contact)',
    'created_from'                  => 'Created From',
    'created_until'                 => 'Created Until',
    'min_score'                     => 'Min Score',
    'min_deal_value'                => 'Min Deal Value',
    'max_deal_value'                => 'Max Deal Value',

    // ─── Row actions ───
    'tooltip_unstar'                => 'Unstar',
    'tooltip_star_this_lead'        => 'Star this lead',
    'tooltip_view_lead'             => 'View Lead',
    'tooltip_edit'                  => 'Edit',
    'tooltip_delete'                => 'Delete',
    'view_detail_action_label'      => 'View detail',

    // ─── Bulk actions ───
    'bulk_assign_agent'             => 'Assign Agent',
    'bulk_assign_to'                => 'Assign To',
    'bulk_leads_assigned'           => 'Leads assigned.',
    'bulk_change_status'            => 'Change Status',
    'bulk_status_updated'           => 'Status updated.',
    'bulk_add_tag'                  => 'Add Tag',
    'bulk_tag_added'                => 'Tag added to selected leads.',
    'bulk_remove_tag'               => 'Remove Tag',
    'bulk_tag_removed'              => 'Tag removed from selected leads.',
    'bulk_move_to_stage'            => 'Move to Stage',
    'bulk_leads_moved'              => 'Leads moved to stage.',
    'bulk_export_csv'               => 'Export Selected (CSV)',
    'bulk_export_queued'            => 'Export queued — download link coming shortly.',
    'bulk_run_automation'           => 'Run Automation',
    'bulk_select_automation'        => 'Select Automation',
    'bulk_enroll_in_sequence'       => 'Enroll in Sequence',
    'bulk_sequence'                 => 'Sequence',
    'bulk_automation_queued'        => 'Automation queued for :count lead(s).',
    'bulk_enrolled_skipped'         => 'Enrolled :added lead(s). Skipped :skipped already enrolled.',

    // ─── Empty state ───
    'empty_heading'                 => 'No leads yet',
    'empty_description'             => 'Capture your first lead by importing a CSV, building an embeddable form, or creating one by hand.',
    'empty_add_lead'                => 'Add a lead',
    'empty_import_csv'              => 'Import from CSV',
    'empty_build_form'              => 'Build a capture form',

    // ─── View page: header actions ───
    'add_line_item'                 => 'Add Line Item',
    'product'                       => 'Product',
    'item_name'                     => 'Item Name',
    'unit_price'                    => 'Unit Price',
    'discount_percent'              => 'Discount %',
    'line_item_added'               => 'Line item added.',

    'create_task'                   => 'Create Task',
    'task_title'                    => 'Task Title',
    'description'                   => 'Description',
    'due_at'                        => 'Due At',
    'priority'                      => 'Priority',
    'reminder_at'                   => 'Reminder At',
    'reminder_help'                 => 'Defaults to one hour before due time.',
    'task_created'                  => 'Task created.',

    'send_email'                    => 'Send Email',
    'load_template'                 => 'Load from template',
    'load_template_help'            => 'Pick a saved email template to fill the subject and body. You can still edit them before sending.',
    'attachments'                   => 'Attachments',
    'no_email_address'              => 'Lead has no email address.',
    'email_log_mode_title'          => 'Email queued, but outbound mail is in log mode.',
    'email_log_mode_body'           => 'SMTP is not configured — the message will be written to storage/logs/laravel.log only. Visit Settings → Email to configure SMTP and start delivering.',
    'email_queued'                  => 'Email queued for delivery.',

    'call_lead'                     => 'Call',
    'call_modal_heading'            => 'Start a call',
    'call_modal_description'        => 'Start a call to :phone? Your own phone will ring first, then bridge to the lead.',
    'call_now'                      => 'Call now',
    'call_no_phone'                 => 'Your user profile has no phone number — cannot bridge the call.',
    'call_failed_to_start'          => 'Call failed to start — check Messaging → Voice settings.',
    'call_initiated'                => 'Call initiated — answer your phone.',
    'call_failed_prefix'            => 'Call failed: ',

    'send_message'                  => 'Send Message',
    'conversation_count'            => 'Conversation (:count)',
    'channel'                       => 'Channel',
    'message'                       => 'Message',
    'no_phone_number'               => 'Lead has no phone number.',
    'message_queued'                => 'Message queued for delivery.',

    'log_call'                      => 'Log Call',
    'inbound'                       => 'Inbound',
    'outbound'                      => 'Outbound',
    'outcome_connected'             => 'Connected',
    'outcome_voicemail'             => 'Left Voicemail',
    'outcome_no_answer'             => 'No Answer',
    'outcome_not_interested'        => 'Not Interested',
    'outcome_callback'              => 'Requested Callback',
    'call_logged'                   => 'Call logged.',
    'duration'                      => 'Duration',
    'duration_minutes_suffix'       => 'min',
    'outcome'                       => 'Outcome',

    'add_note'                      => 'Add Note',
    'mention_label'                 => 'Mention team members (type @ to search)',
    'mention_placeholder'           => 'e.g. @jane',
    'mention_help'                  => 'Separate multiple mentions with commas.',
    'note'                          => 'Note',
    'note_body_help'                => 'Use @name to mention team members.',
    'note_added'                    => 'Note added.',

    'move_stage'                    => 'Move Stage',
    'lead_moved_to_stage'           => 'Lead moved to new stage.',

    'assign'                        => 'Assign',
    'lead_assigned'                 => 'Lead assigned.',

    'enroll_in_sequence'            => 'Enroll in Sequence',
    'sequence'                      => 'Sequence',
    'already_enrolled'              => 'Lead is already enrolled in this sequence.',
    'lead_enrolled'                 => 'Lead enrolled in sequence.',

    'apply_tags'                    => 'Tags',
    'tags_updated'                  => 'Tags updated.',

    'star'                          => 'Star',
    'unstar'                        => 'Unstar',

    'more'                          => 'More',

    'create_quote'                  => 'Create Quote',
    'create_invoice'                => 'Create Invoice',

    'enrich_with_ai'                => 'Enrich with AI',
    're_enrich_with_ai'             => 'Re-enrich with AI',
    're_enrich_modal_heading'       => 'Re-enrich Lead',
    're_enrich_modal_description'   => 'This lead has already been enriched. Running enrichment again will overwrite company, industry, and location data.',
    'enrich_no_email'               => 'Lead has no email — cannot enrich without an email address.',
    'enrich_queued'                 => 'Enrichment queued. Data will appear shortly.',

    'ai_draft_email'                => 'AI Draft Email',
    'email_intent'                  => 'Email Intent',
    'intent_introduction'           => 'Initial Introduction',
    'intent_follow_up'              => 'Follow-up',
    'intent_proposal'               => 'Proposal / Next Steps',
    'intent_re_engage'              => 'Re-engage Cold Lead',
    'intent_closing'                => 'Closing / Confirmation',
    'additional_context'            => 'Additional context (optional)',
    'additional_context_placeholder'=> 'e.g. They saw our pricing page yesterday...',
    'ai_draft_failed'               => 'Could not generate draft. Ensure OpenAI API key is configured.',
    'ai_draft_generated'            => 'Draft Generated',
    'subject_label'                 => 'Subject',

    'merge_lead'                    => 'Merge Lead',
    'merge_into_label'              => 'Merge this lead into (keep as primary)',
    'merge_into_help'               => 'The primary lead will be kept. This lead will be archived.',
    'merge_primary_not_found'       => 'Primary lead not found.',
    'merge_success'                 => 'Leads merged. Redirecting to primary lead…',
    'merge_option_format'           => ':name — :email (matched on :field)',
    'no_email'                      => 'no email',

    'export_data'                   => 'Export Data',

    'send_portal_link'              => 'Send Portal Link',
    'portal_link_heading'           => 'Send portal login link',
    'portal_link_description'       => 'Sends a 30-minute magic link to the lead\'s email so they can view their own deal status + upload documents via the customer portal.',
    'portal_link_sent_prefix'       => 'Portal link sent to ',
    'portal_link_failed_prefix'     => 'Failed to send: ',

    'gdpr_anonymize'                => 'GDPR Anonymize',
    'gdpr_anonymize_heading'        => 'Anonymize Lead',
    'gdpr_anonymize_description'    => 'Replaces all PII with placeholders but keeps the lead record + aggregated stats (deal value, status, pipeline, source, tags). Attachments, emails, messages, notes and page views are deleted. Activity rows are kept with metadata stripped.',
    'gdpr_anonymize_confirm'        => 'Yes, anonymize',
    'gdpr_anonymized_success'       => 'Lead anonymized. Aggregated stats preserved.',

    'gdpr_erase'                    => 'GDPR Erase',
    'gdpr_erase_heading'            => 'GDPR Erase Lead',
    'gdpr_erase_description'        => 'Permanently deletes every trace of this lead — activities, notes, tasks, attachments, emails, messages, web-page views, deal line items, and email-sequence enrollments. This cannot be undone.',
    'gdpr_erase_confirm'            => 'Yes, permanently erase',
    'gdpr_erase_success'            => 'Lead data permanently erased.',

    'attachment_uploaded'           => 'Attachment(s) uploaded.',
    'attachment_deleted'            => 'Attachment deleted.',
    'line_item_removed'             => 'Line item removed.',

    // ─── Edit page ───
    'full_detail_view'              => 'Full Detail View',

    // ─── List page ───
    'kanban_board'                  => 'Kanban Board',
    'save_filters'                  => 'Save Filters',
    'view_name'                     => 'View Name',
    'view_name_placeholder'         => 'e.g. My hot leads this week',
    'email_alerts'                  => 'Email me when new leads match',
    'email_alerts_help'             => 'Hourly check — you will get a digest when new leads match this filter.',
    'share_with_team'               => 'Share with team',
    'share_with_team_help'          => 'Everyone in your workspace can load this view.',
    'filter_view_saved'             => 'Filter view saved',
    'filter_view_saved_as'          => 'Saved as ":name".',
    'filter_view_loaded'            => 'Loaded ":name"',

    'saved_views'                   => 'Saved Views',
    'no_saved_views_yet'            => 'No saved views yet. Apply filters and click "Save Filters" to create one.',
    'select_saved_view'             => 'Select a saved view',
    'saved_view_not_found'          => 'Saved view not found.',
    'placeholder_empty_label'       => '',

    'delete_view'                   => 'Delete View',
    'no_saved_views_to_delete'      => 'No saved views to delete.',
    'select_view_to_delete'         => 'Select view to delete',
    'saved_view_deleted'            => 'Saved view deleted.',

    'export_current_filters'        => 'Export (Current Filters)',
    'export_queued_with_link'       => 'Export queued — you\'ll get a download link shortly.',

    'import_from_crm'               => 'Import from CRM',
    'import_modal_heading'          => 'Import leads from another CRM',
    'import_modal_description'      => 'Upload a CSV exported from HubSpot, Pipedrive, Salesforce, or any generic CSV. Auto-detects the vendor when set to "Auto-detect".',
    'source_crm'                    => 'Source CRM',
    'auto_detect_option'            => 'Auto-detect from CSV headers',
    'source_crm_help'               => 'Auto-detect sniffs the header row. Pick a specific vendor if auto-detection misses your format.',
    'csv_file'                      => 'CSV file',
    'csv_file_help'                 => 'Up to 20MB. First row must be column headers.',
    'no_workspace_context'          => 'No workspace context — please reload.',
    'no_file_uploaded'              => 'No file uploaded.',
    'csv_import_complete'           => 'CSV import complete',

    // ─── CSV import notification body lines ───
    'import_body_imported_count'    => ':count leads imported from :vendor.',
    'import_body_duplicate_count'    => ':count rows already existed (skipped as duplicates).',

    'import_body_skipped_count'     => ':count rows skipped (no email or phone).',
    'import_body_batch_errors'      => ':count batch error(s) — see logs.',

    // ─── Relation: Emails ───
    'emails_title'                  => 'Emails',
    'from'                          => 'From',
    'body_text'                     => 'Body (text)',
    'subject'                       => 'Subject',
    'sent'                          => 'Sent',
    'opened'                        => 'Opened',
    'clicked'                       => 'Clicked',
    'received'                      => 'Received',
    'direction'                     => 'Direction',
    'email_modal_default'           => 'Email',
    'body'                          => 'Body',

    // ─── Relation: Messages ───
    'messages_title'                => 'Messages',
    'channel_whatsapp'              => 'WhatsApp',
    'channel_sms'                   => 'SMS',
    'channel_telegram'              => 'Telegram',
    'channel_viber'                 => 'Viber',
    'status_sent'                   => 'Sent',
    'status_delivered'              => 'Delivered',
    'status_read'                   => 'Read',
    'status_failed'                 => 'Failed',
    'media_url'                     => 'Media URL',
    'message_modal'                 => 'Message',
    'message_status'                => 'Status',
    'sent_at'                       => 'Sent At',

    // ─── Relation: Tasks ───
    'tasks_title'                   => 'Tasks',
    'due'                           => 'Due',
    'done'                          => 'Done',
    'mark_complete'                 => 'Mark Complete',
    'mark_incomplete'               => 'Mark Incomplete',
    'reminder_help_short'           => 'Defaults to one hour before due.',

    // ─── Relation: Deal items ───
    'line_items_title'              => 'Line Items',
    'discount'                      => 'Discount',
    'quantity'                      => 'Quantity',
    'total'                         => 'Total',

    // ─── Relation: Sequence enrollments ───
    'email_sequences_title'         => 'Email Sequences',
    'step'                          => 'Step',
    'next_send'                     => 'Next Send',
    'unenroll'                      => 'Unenroll',
    'lead_unenrolled'               => 'Lead unenrolled.',
    'sequence_status'               => 'Status',
    'enrolled_at'                   => 'Enrolled At',
    'unenroll_reason_manual'        => 'Manually unenrolled',
    // Wave BB: persisted reasons written by LeadObserver (won/converted),
    // ProcessEmailSequences (missing data / no email), and LeadEmail
    // (inbound reply).  Translated at write-time so the column matches
    // the active locale at the moment of insertion.
    'unenroll_reason_converted'     => 'Lead marked as converted',
    'unenroll_reason_won'           => 'Lead marked as won',
    'unenroll_reason_missing_data'  => 'Missing sequence or lead',
    'unenroll_reason_no_email'      => 'Lead has no email address',
    'unenroll_reason_replied'       => 'Lead replied',
    // Wave BB: placeholders written into LeadActivity.metadata.i18n_params
    // by LeadObserver when an old/new pipeline stage is missing
    // (stage_none_placeholder) or no assignee is set
    // (unassigned_placeholder).
    'stage_none_placeholder'        => 'None',
    'unassigned_placeholder'        => 'Unassigned',

    // ─── Relation: Page views ───
    'web_activity_title'            => 'Web Activity',
    'viewed'                        => 'Viewed',
    'page_path'                     => 'Path',
    'page_title'                    => 'Title',
    'page_utm_source'               => 'UTM Source',

    // ─── View page: subheadings + wire:confirm strings ───
    'view_quotes_heading'           => 'Quotes',
    'view_invoices_heading'         => 'Invoices',
    'confirm_delete_attachment'     => 'Are you sure you want to delete this attachment?',
    'confirm_remove_line_item'      => 'Remove this line item?',

    // ─── View page: Blade view labels & section headings ───
    'section_contact_info'          => 'Contact Info',
    'section_attachments'           => 'Attachments',
    'section_line_items'            => 'Line Items',
    'section_quotes_invoices'       => 'Quotes & Invoices',
    'section_internal_notes'        => 'Internal Notes',
    'section_call_history'          => 'Call History',
    'section_web_activity'          => 'Web Activity',
    'section_activity_timeline'     => 'Activity Timeline',
    'section_custom_fields'         => 'Custom Fields',
    'section_email_sequences'       => 'Email Sequences',
    'section_ai_coaching'           => 'AI Coaching',
    'section_conversations'         => 'Conversations',
    'view_name_label'               => 'Name',
    'view_email_label'              => 'Email',
    'view_phone_label'              => 'Phone',
    'view_source_label'             => 'Source',
    'view_status_label'             => 'Status',
    'view_lead_score_label'         => 'Lead Score',
    'view_pts_unit'                 => '/ 100 pts',
    'view_no_name'                  => '(no name)',
    'view_dash'                     => '—',
    'view_why_this_score'           => 'Why this score?',
    'view_default_rule_name'        => 'Rule',
    'view_pts_suffix'               => 'pts',
    'view_assigned_to_label'        => 'Assigned To',
    'view_pipeline_stage_label'     => 'Pipeline Stage',
    'view_tags_label'               => 'Tags',
    'view_company_label'            => 'Company',
    'view_job_title_label'          => 'Job Title',
    'view_industry_size_label'      => 'Industry / Size',
    'view_employees_suffix'         => 'employees',
    'view_country_label'            => 'Country',
    'view_linkedin_label'           => 'LinkedIn',
    'view_linkedin_view_profile'    => 'View Profile →',
    'view_ai_enriched_label'        => 'AI Enriched',
    'view_created_label'            => 'Created',
    'view_inbox_in'                 => 'In',
    'view_inbox_out'                => 'Out',
    'view_inbox_status_prefix'      => 'Status:',

    // ─── Lead view: unified inbox channel labels (Pass 22) ───
    // Used by resources/views/filament/resources/leads/view.blade.php
    // alongside the existing channel_* keys (whatsapp, sms, telegram,
    // viber).  These cover the email and webchat values produced when
    // the inbox merges LeadEmail/LeadMessage rows.
    'channel_email'                 => 'Email',
    'channel_webchat'               => 'Web Chat',

    // ─── Lead view: unified inbox status pill (Pass 22) ───
    // Map of the inbox-level status flags computed inline in
    // leads/view.blade.php (opened/clicked/bounced/sent etc.) so the
    // pill text follows the active locale, not a raw ucfirst() form.
    'inbox_status_opened'           => 'Opened',
    'inbox_status_clicked'          => 'Clicked',
    'inbox_status_bounced'          => 'Bounced',
    'inbox_status_sent'             => 'Sent',
    'inbox_status_delivered'        => 'Delivered',
    'inbox_status_read'             => 'Read',
    'inbox_status_failed'           => 'Failed',
    'inbox_status_pending'          => 'Pending',
    'view_open_conversation_view'   => 'Open full conversation view',
    'view_step_label'               => 'Step',
    'view_next_label'               => '· next :time',
    'view_completed_label'          => '· completed :time',
    'view_no_attachments_yet'       => 'No attachments yet.',
    'view_attachment_download_title' => 'Download',
    'view_attachment_delete_title'  => 'Delete',
    'view_uploading'                => 'Uploading...',
    'view_save_attachments'         => 'Save Attachments',
    'view_table_item'               => 'Item',
    'view_table_qty'                => 'Qty',
    'view_table_unit_price'         => 'Unit Price',
    'view_table_discount'           => 'Discount',
    'view_table_total'              => 'Total',
    'view_table_sku_prefix'         => 'SKU:',
    'view_table_total_label'        => 'Total:',
    'view_remove_item_title'        => 'Remove',
    'view_no_line_items'            => 'No line items yet. Click "Add Line Item" above to attach products or services to this deal.',
    'view_no_quotes_invoices'       => 'No quotes or invoices yet. Use the "More → Create Quote / Create Invoice" actions in the header.',
    'view_invoice_due_label'        => 'Due :date',
    // Status badges for the embedded Quote / Invoice / Call lists on the
    // lead view sidebar. Translator-first lookup with ucfirst() fallback
    // for unknown statuses keeps custom enum extensions intelligible.
    'view_quote_status_draft'       => 'Draft',
    'view_quote_status_sent'        => 'Sent',
    'view_quote_status_accepted'    => 'Accepted',
    'view_quote_status_declined'    => 'Declined',
    'view_quote_status_expired'     => 'Expired',
    'view_quote_status_converted'   => 'Converted',
    'view_invoice_status_draft'     => 'Draft',
    'view_invoice_status_sent'      => 'Sent',
    'view_invoice_status_partial'   => 'Partial',
    'view_invoice_status_overdue'   => 'Overdue',
    'view_invoice_status_paid'      => 'Paid',
    'view_invoice_status_cancelled' => 'Cancelled',
    'view_invoice_status_refunded'  => 'Refunded',
    'view_call_status_completed'    => 'Completed',
    'view_call_status_failed'       => 'Failed',
    'view_call_status_canceled'     => 'Canceled',
    'view_call_status_no_answer'    => 'No answer',
    'view_call_status_busy'         => 'Busy',
    'view_note_system'              => 'System',
    'view_note_mentioned_label'     => 'Mentioned:',
    'view_no_internal_notes'        => 'No internal notes yet.',
    'view_call_agent_default'       => 'Agent',
    'view_call_ai_summary'          => 'AI summary',
    'view_utm_prefix'               => 'utm:',
    'view_activity_by'              => 'by :name',
    'view_no_activity_yet'          => 'No activity recorded yet.',
    'view_custom_yes'               => 'Yes',
    'view_custom_no'                => 'No',
    'view_media_attachment'         => '[media attachment]',

    // ─── Conversation "last reply by" column (lead table) ──
    'conversation_last_by_us'       => 'Us',
    'conversation_last_by_them'     => 'Them',
    'conversation_last_by_new'      => 'New',

    // ─── Badge fallback labels (Wave A — Filament badge formatStateUsing)
    // These keys back the formatStateUsing() callbacks on TextColumn badges
    // across LeadResource and its RelationManagers.  Where the column maps
    // to a typed enum (e.g. App\Enums\LeadStatus), the enum's label()
    // method takes precedence and these keys serve as the raw-string
    // fallback for legacy DB values that bypass the cast.

    // Lead status — mirrors enums/lead_status.php plus the legacy
    // 'converted' alias kept by the H7 enum docblock.
    'status_new'                    => 'New',
    'status_contacted'              => 'Contacted',
    'status_qualified'              => 'Qualified',
    'status_won'                    => 'Won',
    'status_converted'              => 'Converted',
    'status_lost'                   => 'Lost',

    // Direction badges (Messages relation manager).  Brand-neutral.
    'direction_inbound'             => 'Inbound',
    'direction_outbound'            => 'Outbound',

    // Email direction badges — kept distinct from message direction so
    // translators can adapt phrasing per channel without coupling.
    'email_direction_inbound'       => 'Inbound',
    'email_direction_outbound'      => 'Outbound',

    // Message status badges — distinct key prefix from generic 'status_*'
    // to keep MessagesRelationManager translations independent of lead
    // status keys.
    'message_status_sent'           => 'Sent',
    'message_status_delivered'      => 'Delivered',
    'message_status_read'           => 'Read',
    'message_status_failed'         => 'Failed',

    // Task priority badges (TasksRelationManager).
    'task_priority_urgent'          => 'Urgent',
    'task_priority_high'            => 'High',
    'task_priority_normal'          => 'Normal',
    'task_priority_low'             => 'Low',

    // Email-sequence enrollment status badges.
    'enrollment_status_active'      => 'Active',
    'enrollment_status_completed'   => 'Completed',
    'enrollment_status_replied'     => 'Replied',
    'enrollment_status_unenrolled'  => 'Unenrolled',

    // GDPR anonymization labels written to DB rows during erasure so the
    // placeholders match the operator's active locale at the moment of
    // anonymization.
    'gdpr_anonymous'                => 'Anonymous',
    'gdpr_task_label'               => 'Task #:id',

    // Phase 1 lead-funnel fields
    'city' => 'City',
    'assigned_team' => 'Assigned Team',
    'assigned_team_help' => 'The team that owns this lead. Independent of which rep currently holds it.',
    'next_follow_up' => 'Next Follow-up',
    'meeting_generic_label' => 'Meeting',
    'section_history' => 'Stage & Ownership History',
    'history_stage' => 'Stage changes',
    'history_assignment' => 'Ownership changes',
    'history_none' => 'None',
    'history_unassigned' => 'Unassigned',
    'history_system' => 'System',
];
