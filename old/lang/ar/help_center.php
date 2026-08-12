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

    'search_placeholder'   => 'ابحث في مركز المساعدة…',

    // --- No-match state ---------------------------------------------------
    'no_match_title'       => 'لا توجد نتائج',
    'no_match_body'        => 'لا شيء عن «:term». جرّب كلمات أقل، أو تواصل مع الدعم — سنضيفه إلى الوثائق.',

    // --- Result count (Laravel pluralization) ----------------------------
    'result_count'         => '{1} :count نتيجة عن «:term»|[2,*] :count نتائج عن «:term»',

    // --- Contact-support footer ------------------------------------------
    'support_title'        => 'هل ما زلت عالقًا؟',
    'support_body_html'    => 'راسل <a href=":mailto" style="color:#4f46e5;font-weight:600;text-decoration:underline;">:email</a> بما تحاول فعله — نقرأ كل رسالة.',

];
