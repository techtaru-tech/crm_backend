<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| InvoiceResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/invoices.<key>').
*/

return [

    // ----- Model labels -----
    'model_label'        => 'فاتورة',
    'plural_model_label' => 'الفواتير',

    // ----- Navigation -----
    'nav_label'        => 'الفواتير',
    'tabs_outer'       => 'فاتورة',

    // ----- Filter labels -----
    'filter_label_status' => 'الحالة',

    // ----- Tabs -----
    'tab_info'         => 'المعلومات',
    'tab_line_items'   => 'البنود',
    'tab_totals'       => 'الإجماليات',

    // ----- Info -----
    'lead'             => 'العميل المحتمل',
    'company'          => 'الشركة',
    'issued'           => 'تاريخ الإصدار',
    'due_date'         => 'تاريخ الاستحقاق',

    // ----- Items -----
    'add_item'         => 'إضافة بند',
    'product'          => 'المنتج',
    'unit_price'       => 'سعر الوحدة',
    'discount_percent' => 'نسبة الخصم %',
    'line_total'       => 'إجمالي البند',
    'line_total_placeholder' => 'تلقائي',
    'line_item_default_label' => 'بند جديد',

    // ----- Totals -----
    'tax_rate'         => 'نسبة الضريبة',
    'additional_discount' => 'خصم إضافي',

    // ----- Table -----
    'col_number'       => 'الرقم',
    'col_lead'         => 'العميل المحتمل',
    'col_paid'         => 'المدفوع',
    'col_due'          => 'المستحق',

    // ----- Row actions -----
    'send'             => 'إرسال',
    'download_pdf'     => 'تحميل PDF',
    'record_payment'   => 'تسجيل دفعة',
    'mark_as_paid'     => 'تعليم كمدفوع',
    'cancel'           => 'إلغاء',
    'refund'           => 'استرداد',
    'refund_modal_heading' => 'تعليم الفاتورة كمستردة',
    'refund_modal_description' => 'يُسجّل هذا الاسترداد في دفتر المدفوعات ويضبط حالة الفاتورة إلى مستردة. لا يُسترد المبلغ تلقائيًا عبر بوابة الدفع.',
    'more'             => 'المزيد',
    'mark_as_sent'     => 'تعليم كمُرسَل',

    // ----- Record Payment form -----
    'external_reference' => 'مرجع خارجي',

    // ----- Sub-page actions -----
    'public_link'      => 'الرابط العام',

    // ----- Notifications -----
    'notify_no_email'           => 'العميل المحتمل ليس لديه بريد إلكتروني.',
    'notify_sent'               => 'تم إرسال الفاتورة.',
    'notify_send_failed_prefix' => 'فشل: ',
    'notify_payment_recorded'   => 'تم تسجيل الدفعة.',
    'notify_marked_paid'        => 'تم تعليم الفاتورة كمدفوعة.',
    'notify_cancelled'          => 'تم إلغاء الفاتورة.',
    'notify_refunded'           => 'تم استرداد الفاتورة.',
    'notify_bulk_marked_sent'   => 'تم تعليم الفواتير كمُرسَلة.',
    'notify_invoice_saved'      => 'تم حفظ الفاتورة.',

    // ----- Payments relation manager -----
    'payments_relation_title'   => 'المدفوعات',
    'col_amount'                => 'المبلغ',
    'col_method'                => 'الطريقة',
    'col_received_at'           => 'تاريخ الاستلام',
    'col_reference'             => 'المرجع',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'       => 'مسودة',
    'option_status_sent'        => 'مُرسَلة',
    'option_status_partial'     => 'جزئية',
    'option_status_paid'        => 'مدفوعة',
    'option_status_overdue'     => 'متأخرة',
    'option_status_cancelled'   => 'ملغاة',
    'option_status_refunded'    => 'مستردة',
    'option_gateway_manual'     => 'يدوي (تحويل بنكي)',
    'option_payment_succeeded'  => 'نجح',
    'option_payment_pending'    => 'قيد الانتظار',
    'option_payment_failed'     => 'فشل',
    'option_payment_refunded'   => 'مسترد',

    // ─── Form field labels ─────────────────────────────────────────
    'field_currency_label'      => 'العملة',
    'field_status_label'        => 'الحالة',
    'field_notes_label'         => 'ملاحظات',
    'field_item_name_label'     => 'الاسم',
    'field_item_description_label' => 'الوصف',
    'field_item_quantity_label' => 'الكمية',
    'field_subtotal_label'      => 'المجموع الفرعي',
    'field_tax_amount_label'    => 'مبلغ الضريبة',
    'field_total_label'         => 'الإجمالي',
    'field_amount_paid_label'   => 'المبلغ المدفوع',

    // ─── Record-payment form field labels ──────────────────────────
    'field_gateway_label'       => 'بوابة الدفع',
    'field_amount_label'        => 'المبلغ',
    'field_payment_status_label' => 'حالة الدفع',
    'field_paid_at_label'       => 'تاريخ الدفع',

    // ─── Table column labels ───────────────────────────────────────
    'col_total'                 => 'الإجمالي',
    'col_status'                => 'الحالة',
    'col_created_at'            => 'تاريخ الإنشاء',

    // ─── Payments relation manager labels ──────────────────────────
    'field_payment_currency_label' => 'العملة',
    'col_gateway'               => 'بوابة الدفع',
    'col_payment_status'        => 'الحالة',
    'col_paid_at'               => 'تاريخ الدفع',
    'col_payment_created_at'    => 'تاريخ الإنشاء',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'              => 'مسودة',
    'status_sent'               => 'مُرسَلة',
    'status_partial'            => 'جزئية',
    'status_overdue'            => 'متأخرة',
    'status_paid'               => 'مدفوعة',
    'status_cancelled'          => 'ملغاة',
    'status_refunded'           => 'مستردة',

    // ─── Payment gateway badge labels (brand names left literal) ───
    'payment_gateway_stripe'    => 'Stripe',
    'payment_gateway_paypal'    => 'PayPal',
    'payment_gateway_razorpay'  => 'Razorpay',
    'payment_gateway_paystack'  => 'Paystack',
    'payment_gateway_manual'    => 'يدوي',

    // ─── Payment status badge labels ───────────────────────────────
    'payment_status_pending'    => 'قيد الانتظار',
    'payment_status_succeeded'  => 'نجح',
    'payment_status_failed'     => 'فشل',
    'payment_status_refunded'   => 'مسترد',
];
