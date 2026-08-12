<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| EmailSequenceResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/email_sequences.<key>').
*/

return [

    // ----- Navigation -----
    'nav_label'          => 'Email Sequences',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'        => 'Email Sequence',
    'plural_model_label' => 'Email Sequences',

    // ----- Form fields -----
    'status'             => 'Status',

    // ----- itemLabel template strings -----
    'item_label_step_prefix' => 'Step — ',
    'item_label_day_short'   => 'd',
    'item_label_hour_short'  => 'h',
    'item_label_no_subject'  => '(no subject)',

    // ----- Sequence Info -----
    'sequence_name'      => 'Sequence Name',
    'description'        => 'Description',

    // ----- Behavior -----
    'stop_on_reply'      => 'Stop when lead replies',
    'stop_on_reply_help' => 'Unenroll automatically when an inbound email from the lead is recorded.',
    'stop_on_won'        => 'Stop when lead is won',
    'stop_on_won_help'   => 'Unenroll automatically when the lead’s status becomes "won".',

    // ----- Steps -----
    'steps_description'  => 'Emails are sent top-to-bottom. Delay is measured from the previous step (or from enrollment for the first step).',
    'add_step'           => 'Add Step',
    'delay_days'         => 'Delay (days)',
    'delay_hours'        => 'Delay (hours)',
    'load_template'      => 'Load from template',
    'load_template_help' => 'Pick a saved email template to fill the subject and body below. You can still edit them after loading.',
    'subject'            => 'Subject',
    'subject_help'       => 'Placeholders: {first_name}, {last_name}, {company}, {email}',
    'body'               => 'Body',
    'body_help'          => 'Placeholders: {first_name}, {last_name}, {company}, {email}',

    // ----- Filter labels -----
    'filter_label_status' => 'Status',

    // ----- Table -----
    'col_name'           => 'Name',
    'col_status'         => 'Status',
    'col_steps'          => 'Steps',
    'col_active_enroll'  => 'Active Enrollments',
    'col_completed'      => 'Completed',
    'col_created'        => 'Created',

    // ----- Row actions -----
    'preview'            => 'Preview',
    'preview_modal_heading' => 'Preview: :name',
    'preview_description' => 'Token substitution shown with sample data — {first_name}=Jane, {last_name}=Doe, {company}=Acme Inc, {email}=jane@acme.com.',
    'preview_close'      => 'Close',
    'send_test'          => 'Send Test',
    'send_test_to'       => 'Send test to',
    'which_step'         => 'Which step?',
    'duplicate'          => 'Duplicate',

    // ----- Notifications -----
    'notif_step_not_found'        => 'Step not found.',
    'notif_test_email_sent'       => 'Test email sent to :email',
    'notif_test_email_failed'     => 'Send failed: :error',
    'notif_sequence_duplicated'   => 'Sequence duplicated.',
    'notif_duplicate_failed'      => 'Could not duplicate sequence.',

    // ----- Enrollments relation manager -----
    'enrollments_relation_title'  => 'Enrollments',
    'col_lead'                    => 'Lead',
    'col_email'                   => 'Email',
    'col_step'                    => 'Step',
    'col_next_send'               => 'Next Send',
    'col_next_send_at'            => 'Next Send At',
    'col_enrolled_at'             => 'Enrolled At',

    // ----- Preview view -----
    'preview_delay_label'         => 'Delay',
    'preview_sample_lead'         => 'Preview with sample lead',
    'preview_no_steps'            => 'No steps defined yet. Add steps on the edit page.',

    // ----- Preview / test send micro-strings -----
    'preview_delay_immediate'     => 'immediate',
    'test_send_step_option_label' => 'Step :step — ',
    'test_subject_prefix'         => '[TEST] :subject',
    'preview_sample_first_name'   => 'Jane',
    'preview_sample_last_name'    => 'Doe',
    'preview_sample_company_name' => 'Acme Inc',
    'preview_sample_email'        => 'jane@acme.com',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'          => 'Draft',
    'option_status_active'         => 'Active',
    'option_status_paused'         => 'Paused',
    'option_enrollment_active'     => 'Active',
    'option_enrollment_completed'  => 'Completed',
    'option_enrollment_replied'    => 'Replied',
    'option_enrollment_unenrolled' => 'Unenrolled',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                 => 'Draft',
    'status_active'                => 'Active',
    'status_paused'                => 'Paused',

    // ─── Duplicate action copy ─────────────────────────────────────
    'duplicate_copy_suffix'        => '(Copy)',

    // ─── Delay format short tokens (preview) ───────────────────────
    'delay_days_short'             => 'd',
    'delay_hours_short'            => 'h',
];
