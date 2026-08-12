<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LandingPageResource translation strings
|--------------------------------------------------------------------------
|
| Labels, helper texts, placeholders and tab/section copy for the
| Landing Pages resource at /admin/landing-pages.
| Consumed via __('filament/landing_pages.<key>').
|
| Keys are snake_case; grouped by purpose-comment headers.
|
*/

return [

    // ─── Navigation ──────────────────────────────────────────────────
    'nav_label'                         => 'Landing Pages',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Landing Page',
    'plural_model_label'                => 'Landing Pages',

    // ─── Top-level tabs ─────────────────────────────────────────────
    'landing_page'                   => 'Landing Page',
    'tab_basics'                     => 'Basics',
    'tab_sections'                   => 'Sections',
    'tab_appearance'                 => 'Appearance',
    'tab_integration'                => 'Integration',
    'tab_advanced'                   => 'Advanced',

    // ─── Basics tab ────────────────────────────────────────────────
    'internal_name'                  => 'Internal Name',
    'slug'                           => 'Slug',
    'slug_help_with_workspace'       => 'Used in the public URL: :url',
    'slug_help_generic'              => 'Used in the public URL: /{workspace}/{slug}',
    'browser_title'                  => 'Browser Title',
    'meta_description'               => 'Meta Description',
    'og_image_url'                   => 'Open Graph Image URL',
    'favicon_url'                    => 'Favicon URL',
    'status'                         => 'Status',

    // ─── Sections tab — repeater ───────────────────────────────────
    'add_section'                    => 'Add Section',
    'new_section'                    => 'New Section',
    'section_type'                   => 'Section Type',
    'visible'                        => 'Visible',

    // ─── HERO section ──────────────────────────────────────────────
    'hero_eyebrow'                   => 'Eyebrow (small tag)',
    'headline'                       => 'Headline',
    'subheadline'                    => 'Subheadline',
    'cta_label'                      => 'CTA Label',
    'cta_url'                        => 'CTA URL',
    'image_url'                      => 'Image URL',
    'alignment'                      => 'Alignment',
    'background'                     => 'Background',

    // ─── FEATURES section ─────────────────────────────────────────
    'feature_items'                  => 'Feature Items',
    'icon_key_optional'              => 'Icon key (optional)',
    'feature_title'                  => 'Title',
    'feature_body'                   => 'Body',

    // ─── FORM section ─────────────────────────────────────────────
    'form'                           => 'Form',
    'success_message'                => 'Success Message',

    // ─── TESTIMONIALS section ─────────────────────────────────────
    'testimonials'                   => 'Testimonials',
    'avatar_url'                     => 'Avatar URL',
    'testimonial_quote'              => 'Quote',
    'testimonial_author'             => 'Author',
    'testimonial_role'               => 'Role',

    // ─── CTA section ─────────────────────────────────────────────
    'body'                           => 'Body',
    'button_label'                   => 'Button Label',
    'button_url'                     => 'Button URL',

    // ─── GALLERY section ─────────────────────────────────────────
    'images'                         => 'Images',
    'image_url_placeholder'          => 'https://example.com/image.jpg',
    'caption'                        => 'Caption',

    // ─── PRICING section ─────────────────────────────────────────
    'plans'                          => 'Plans',
    'features_one_per_line'          => 'Features (one per line)',
    'highlight_this_plan'            => 'Highlight this plan',
    'plan_name'                      => 'Name',
    'plan_price'                     => 'Price',
    'plan_interval'                  => 'Interval',
    'plan_cta_label'                 => 'CTA Label',
    'plan_cta_url'                   => 'CTA URL',
    'plan_cta_default'               => 'Choose plan',

    // ─── FAQ section ─────────────────────────────────────────────
    'q_and_a'                        => 'Q & A',
    'faq_question'                   => 'Question',
    'faq_answer'                     => 'Answer',

    // ─── HTML section ────────────────────────────────────────────
    'raw_html'                       => 'Raw HTML',
    'raw_html_help'                  => 'Admin-only. Script and event-handler attributes are stripped. Put tracking snippets in the Advanced tab instead.',

    // ─── Appearance tab ──────────────────────────────────────────
    'primary_color'                  => 'Primary Color',
    'background_color'               => 'Background Color',
    'font_family'                    => 'Font Family',

    // ─── Integration tab ─────────────────────────────────────────
    'linked_form'                    => 'Linked Form',
    'linked_form_help'               => 'Used for the default form section and fallback submit handler.',
    'pipeline'                       => 'Pipeline',
    'stage'                          => 'Stage',
    'redirect_on_submit'             => 'Redirect URL after submit',

    // ─── Advanced tab ────────────────────────────────────────────
    'custom_css'                     => 'Custom CSS',
    'custom_css_help'                => 'Injected into <style> before the page body. Admin-only.',
    'custom_js'                      => 'Custom JS (tracking snippets)',
    'custom_js_help'                 => 'Injected just before </body>. Admin-only — runs on every visitor.',

    // ─── Table columns ───────────────────────────────────────────
    'name'                           => 'Name',
    'slug_copied'                    => 'Slug copied',
    'views'                          => 'Views',
    'conversions'                    => 'Conversions',
    'created'                        => 'Created',

    // ─── Row actions ─────────────────────────────────────────────
    'preview'                        => 'Preview',
    'duplicate'                      => 'Duplicate',

    // ─── Use-Template header action ──────────────────────────────
    'use_template'                   => 'Use Template',
    'template'                       => 'Template',
    'page_name'                      => 'Page Name',
    'template_not_found'             => 'Template not found',
    'template_created_title'         => 'Landing page created from template',
    'template_created_body'          => 'Edit the page to customize content.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'            => 'Draft',
    'option_status_published'        => 'Published',
    'option_status_archived'         => 'Archived',
    'option_align_left'              => 'Left',
    'option_align_center'            => 'Center',
    'option_bg_gradient'             => 'Gradient',
    'option_bg_solid'                => 'Solid',
    'option_bg_image'                => 'Image',
    'option_palette_indigo'          => 'Indigo',
    'option_palette_gray'            => 'Gray',
    'option_palette_white'           => 'White',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                   => 'Draft',
    'status_published'               => 'Published',
    'status_archived'                => 'Archived',

    // ─── Font fallback option ──────────────────────────────────────
    'font_system_default'            => 'System Default',

    // ─── Plan interval short suffix (pricing section default) ──────
    'plan_interval_short_mo'         => 'mo',
];
