<?php

declare(strict_types=1);

return [

    // ─── التخطيط / الترويسة ─────────────────────────────────────────
    'page_title'           => 'فاتورة :number',
    'invoice_label_prefix' => 'فاتورة :number',
    'from_quote_prefix'    => 'من عرض سعر :number',
    'amount_due'           => 'المبلغ المستحق',

    // ─── خانات البيانات الوصفية ────────────────────────────────────
    'issued'               => 'تاريخ الإصدار',
    'due'                  => 'تاريخ الاستحقاق',
    'bill_to'              => 'فوترة إلى',

    // عناوين الأقسام
    'items_heading'        => 'البنود',

    // ─── جدول البنود ────────────────────────────────────────────────
    'col_item'             => 'البند',
    'col_qty'              => 'الكمية',
    'col_unit'             => 'الوحدة',
    'col_total'            => 'الإجمالي',
    'subtotal'             => 'المجموع الفرعي',
    'discount'             => 'الخصم',
    'tax_label'            => 'الضريبة (:rate٪)',
    'total'                => 'الإجمالي',
    'paid'                 => 'مدفوعة',

    // ─── شارة الدفع / التذييل ──────────────────────────────────────
    'paid_in_full'         => 'مدفوعة بالكامل',
    'marked_paid_on'       => 'تم تعليمها كمدفوعة في :date',
    'download_pdf'         => 'تنزيل PDF',
    'secured_link_suffix'  => 'رابط آمن — :app',

    // ─── صفحة الدفع ────────────────────────────────────────────────
    'paid_title_suffix'    => 'فاتورة :number — مدفوعة',
    'paid_heading_paid'    => 'تم استلام الدفعة',
    'paid_heading_thanks'  => 'شكرًا لك',
    'paid_invoice_label'   => 'فاتورة :number',
    'paid_total_label'     => 'الإجمالي',
    'paid_received_body'   => 'تم استلام دفعتك. سيُرسَل إليك إيصال عبر البريد الإلكتروني.',
    'paid_pending_body'    => 'تجري معالجة دفعتك حاليًا. ستُحدَّث هذه الصفحة بمجرد استلام التأكيد.',
    'paid_download_pdf'    => 'تنزيل PDF',

    // ─── تسميات شارات الحالة ────────────────────────────────────────
    'status_draft'         => 'مسودة',
    'status_sent'          => 'مُرسَلة',
    'status_partial'       => 'مدفوعة جزئيًا',
    'status_overdue'       => 'متأخرة',
    'status_paid'          => 'مدفوعة',
    'status_cancelled'     => 'مُلغاة',
    'status_refunded'      => 'مُستردة',
];
