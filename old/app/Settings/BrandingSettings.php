<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BrandingSettings extends Settings
{
    public string $app_name        = 'LeadHub';
    public string $primary_color   = '#4f46e5';
    public string $accent_color    = '#06b6d4';
    public ?string $logo_url       = null;
    public ?string $favicon_url    = null;
    public string $login_bg_color  = '#f3f4f6';
    public ?string $login_bg_image = null;
    public string $email_sender_name = 'LeadHub';
    public string $email_from      = '';
    public string $footer_text     = '';

    /* ---------- Email header / footer branding ----------
       Per operator request: every outbound email (welcome,
       invitation, password-reset) needs a header + footer that
       inherit branding colors configurable in one place — with
       either a solid colour or a 2-stop linear gradient.

       email_header_style = 'solid' | 'gradient'
       email_header_color_primary   = first stop / solid colour
       email_header_color_secondary = second stop (gradient only)
       email_header_gradient_angle  = degrees, e.g. 135
       email_footer_color           = footer background
       email_footer_text_color      = footer text (contrast) */
    public string  $email_header_style           = 'solid';
    public string  $email_header_color_primary   = '#4f46e5';
    public ?string $email_header_color_secondary = '#6366f1';
    public int     $email_header_gradient_angle  = 135;
    public string  $email_footer_color           = '#f9fafb';
    public string  $email_footer_text_color      = '#6b7280';

    public static function group(): string
    {
        return 'branding';
    }

    /**
     * Resolve the CSS `background` value for the email header.
     * Used by BrandedMailable → layout.blade so one string is
     * emitted regardless of whether the operator picked solid or
     * gradient.
     */
    public function resolveEmailHeaderBackground(): string
    {
        if ($this->email_header_style === 'gradient' && filled($this->email_header_color_secondary)) {
            return sprintf(
                'linear-gradient(%ddeg, %s 0%%, %s 100%%)',
                max(0, min(360, $this->email_header_gradient_angle)),
                $this->email_header_color_primary,
                $this->email_header_color_secondary,
            );
        }
        return $this->email_header_color_primary;
    }
}
