<?php

declare(strict_types=1);

return [

    // --- التنقل --------------------------------------------------------
    'nav_label'  => 'نطاق مخصص',

    // --- عنوان الصفحة ---------------------------------------------------
    'page_title' => 'نطاق مخصص',

    // --- وصف القسم -------------------------------------------
    'section_description' => 'وجِّه نطاقك المخصص إلى هذه المنصة. أضف سجل CNAME يشير إلى: :host',

    // --- تسميات الحقول --------------------------------------------------
    'custom_domain'             => 'نطاق مخصص',
    'custom_domain_placeholder' => 'leads.yourcompany.com',
    'custom_domain_helper'      => 'أدخل نطاقك دون https://. ثم أضف سجل CNAME: leads.yourcompany.com → :host',

    // --- إجراءات الرأس ------------------------------------------------
    'reverify_dns' => 'إعادة التحقق من DNS',
    'save_domain'  => 'حفظ النطاق',

    // ─── عرض Blade — بطاقة تعليمات DNS ────────────────────────────────
    'current_domain_label'      => 'النطاق الحالي:',
    'status_verified'           => 'تم التحقق',
    'status_pending'            => 'بانتظار التحقق',
    'dns_record_lede'           => 'أضف سجل DNS التالي إلى نطاقك:',
    'col_type'                  => 'النوع',
    'col_name'                  => 'الاسم',
    'col_value'                 => 'القيمة',
    'dns_txt_hint_html'         => 'أو أضف سجل TXT: <code class="ds-code">_leadhub-verify.:domain</code> → <code class="ds-code">:token</code>',
    'dns_propagation_hint'      => 'قد يستغرق انتشار DNS حتى ٢٤ ساعة. يعمل التحقق تلقائياً في الخلفية.',
    'dns_record_type_cname'     => 'CNAME',

];
