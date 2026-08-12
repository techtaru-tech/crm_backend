<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — OnboardingWizard translation strings
|--------------------------------------------------------------------------
|
| Labels, descriptions, helper text, and notification copy for the
| first-run onboarding wizard at /admin/onboarding.
| Consumed via __('filament/onboarding_wizard.<key>').
|
*/

return [

    // ─── Page header ──────────────────────────────────────────────────
    'title_prefix'                  => 'Welcome to :app',

    // ─── Workspace slug adjusted notice ────────────────────────────────
    'slug_adjusted_title'           => 'Your workspace slug was adjusted',
    'workspace_renamed_body'        => 'The name ":requested" was already taken, so we assigned ":assigned" instead. Your public landing pages will live under :url/{page-slug}.',

    // ─── Step 1: Workspace ─────────────────────────────────────────────
    'step_workspace'                => 'Workspace',
    'step_workspace_description'    => 'Name your workspace and pick a brand color',
    'workspace_name'                => 'Workspace Name',
    'workspace_name_helper'         => 'This shows up in the sidebar and in outgoing emails.',
    'primary_color'                 => 'Primary Brand Color',
    'primary_color_helper'          => 'Used for buttons, links, and accents across your workspace.',
    'company_tagline'               => 'Tagline (optional)',
    'company_tagline_helper'        => 'Shown on your public forms and email signatures.',

    // ─── Step 2: Branding ──────────────────────────────────────────────
    'step_branding'                 => 'Branding',
    'step_branding_description'     => 'Upload your company logo',
    'company_logo'                  => 'Company Logo',
    'company_logo_helper'           => 'PNG or SVG, under 2 MB. Skip this step if you want to add it later.',

    // ─── Step 3: Team ──────────────────────────────────────────────────
    'step_team'                     => 'Team',
    'step_team_description'         => 'Invite your teammates',
    'team_invitations'              => 'Team Members to Invite',
    'team_invitations_helper'       => 'Leave empty if you want to invite teammates later from Settings → Team.',
    'add_teammate'                  => 'Add Teammate',
    'label_role'                    => 'Role',

    // ─── Step 4: Lead Sources ──────────────────────────────────────────
    'step_lead_sources'             => 'Lead Sources',
    'step_lead_sources_description' => 'Name your first lead source',
    'first_lead_source'             => 'First Lead Source',
    'first_lead_source_helper'      => "We'll create one default source so you can start capturing leads right away.",
    'default_lead_source'           => 'Website',

    // ─── Submit / Header actions ───────────────────────────────────────
    'finish_setup'                  => 'Finish Setup',
    'skip_for_now'                  => 'Skip for now',

    // ─── Invite error notification ─────────────────────────────────────
    'invite_failed_prefix'          => 'Could not invite ',

    // ─── Success notification ──────────────────────────────────────────
    'welcome_title'                 => 'Welcome aboard!',
    'welcome_body_invited'          => '{1} :count invitation sent. You\'re all set.|[2,*] :count invitations sent. You\'re all set.',
    'welcome_body_no_invites'       => "You're all set. Let's find your first lead.",

    // ─── Blade view — hero block ──────────────────────────────────────
    'hero_title'                    => "Let's set up your workspace",
    'hero_subtitle'                 => 'This takes about 2 minutes. You can skip any step and come back later.',

    // ─── Select options ────────────────────────────────────────────
    'option_role_manager'           => 'Manager',
    'option_role_member'            => 'Member',

    // ─── Field labels ───────────────────────────────────────────────
    'field_email_label'             => 'Email address',

    // ─── Field placeholders ─────────────────────────────────────────
    'placeholder_teammate_email'    => 'teammate@company.com',
];
