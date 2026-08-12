<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

/**
 * STARTER CONTENT — buyers replace this from the admin panel.
 * English defaults intentionally kept literal; the SA editor lets
 * buyers paste any locale's content as plain text.
 *
 * Seeds placeholder About / Terms / Privacy / Contact pages so a
 * brand-new install has legally-reasonable stub content linkable
 * from footer and navbar.  Idempotent — updates existing rows on
 * slug match, so re-running doesn't nuke the operator's edits
 * (uses firstOrCreate so content_html is preserved on re-runs).
 */
class StaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'slug'             => 'about',
                'title'            => 'About',
                'meta_description' => 'Learn more about the team behind this platform.',
                'excerpt'          => 'Who we are and what we believe in.',
                'content_html'     => '<h2>Our mission</h2><p>We build tools that help small teams run sales operations like a much bigger one — without the enterprise price tag or implementation overhead.</p><p>This site is running on a self-hosted lead-management platform we designed for operators who ship fast, value their data, and prefer to own their infrastructure.</p><h2>How to reach us</h2><p>Use the contact page or send us a line via our support channel — we read every message.</p>',
                'show_in_nav'      => true,
                'show_in_footer'   => true,
                'nav_order'        => 10,
            ],
            [
                'slug'             => 'terms',
                'title'            => 'Terms of Service',
                'meta_description' => 'Terms & conditions for using this service.',
                'excerpt'          => 'The rules of engagement.',
                'content_html'     => '<p><em>Last updated: ' . now()->format('F Y') . '</em></p><h2>1. Acceptance</h2><p>By accessing or using this service, you agree to be bound by these Terms. If you do not agree, you may not use the service.</p><h2>2. Use of the service</h2><p>You agree to use the service in compliance with all applicable laws and not to abuse, disrupt, or interfere with its operation.</p><h2>3. Accounts</h2><p>You are responsible for maintaining the confidentiality of your account credentials and for all activity under your account.</p><h2>4. Content ownership</h2><p>You retain ownership of any data you upload. We do not claim ownership of your content.</p><h2>5. Termination</h2><p>We may suspend or terminate access for violations of these Terms.</p><h2>6. Changes</h2><p>We may update these Terms. Continued use of the service after changes constitutes acceptance.</p><p><strong>Note:</strong> This is a placeholder. Replace with your legally-reviewed Terms before launching to customers.</p>',
                'show_in_nav'      => false,
                'show_in_footer'   => true,
                'nav_order'        => 80,
            ],
            [
                'slug'             => 'privacy',
                'title'            => 'Privacy Policy',
                'meta_description' => 'How we handle your personal data.',
                'excerpt'          => 'Your data, your rights.',
                'content_html'     => '<p><em>Last updated: ' . now()->format('F Y') . '</em></p><h2>What we collect</h2><p>Account info (name, email), usage metadata, and any content you add to the platform.</p><h2>How we use it</h2><p>To operate the service, respond to support requests, improve the product, and send service-related emails.</p><h2>Third parties</h2><p>We only share data with third-party processors strictly necessary to deliver the service (e.g., email providers). We do not sell your data.</p><h2>Your rights</h2><p>You may request a copy of your data, ask us to delete it, or update it at any time — from within your workspace or by contacting us.</p><h2>Retention</h2><p>Active data is retained while your account is active. Deleted data is purged within 30 days unless legally required to retain longer.</p><p><strong>Note:</strong> This is a placeholder. Replace with your legally-reviewed Privacy Policy before launching to customers, especially if you operate in GDPR / CCPA jurisdictions.</p>',
                'show_in_nav'      => false,
                'show_in_footer'   => true,
                'nav_order'        => 85,
            ],
            [
                'slug'             => 'contact',
                'title'            => 'Contact',
                'meta_description' => 'Get in touch with our team.',
                'excerpt'          => 'We would love to hear from you.',
                'content_html'     => '<h2>Get in touch</h2><p>Questions, feedback, or want a demo? Drop us a line.</p><p><strong>Email:</strong> hello@example.com<br><strong>Response time:</strong> usually within one business day.</p><p>For urgent production issues on an existing account, please use the support channel in your workspace dashboard.</p><p><em>Replace this page with your actual contact details or embed a form here.</em></p>',
                'show_in_nav'      => true,
                'show_in_footer'   => true,
                'nav_order'        => 20,
            ],
        ];

        foreach ($defaults as $page) {
            StaticPage::firstOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, ['published' => true, 'published_at' => now()])
            );
        }
    }
}
