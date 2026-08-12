<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Help Center page — translation strings
|--------------------------------------------------------------------------
|
| UI chrome for the tenant-side Help Center page (Filament).  Article
| titles and bodies are currently defined in HelpCenterPage::articles()
| as a PHP array on the Page class — they remain there because each
| article maps to functionality buyers will customise heavily, and
| splitting them across lang files prematurely creates churn.  This
| file covers the page chrome (search box, empty-state, results
| count, footer call-to-action) so the surrounding view contains zero
| hardcoded English.
|
*/

return [

    'search_placeholder'   => 'Search the help center…',

    // --- No-match state ---------------------------------------------------
    'no_match_title'       => 'No matches',
    'no_match_body'        => 'Nothing for ":term". Try fewer words, or contact support — we\'ll add it to the docs.',

    // --- Result count (Laravel pluralization) ----------------------------
    'result_count'         => '{1} :count result for ":term"|[2,*] :count results for ":term"',

    // --- Contact-support footer ------------------------------------------
    'support_title'        => 'Still stuck?',
    'support_body_html'    => 'Email <a href=":mailto" style="color:#4f46e5;font-weight:600;text-decoration:underline;">:email</a> with what you\'re trying to do — we read every message.',

];
