<?php

declare(strict_types=1);

return [

    'receipt' => [
        'doc_title'          => 'إيصال :number',
        'title'              => 'إيصال',
        'issued'             => 'تاريخ الإصدار :date',
        'from'               => 'من',
        'billed_to'          => 'إلى',
        'anonymized'         => '<مُجهَّل>',
        'gdpr_anonymized'    => 'تم حذف مساحة العمل بموجب المادة 17 من GDPR. تم الاحتفاظ بالسجل الضريبي.',
        'vat_label'          => 'VAT/GST: :number',
        'th_description'     => 'الوصف',
        'th_plan'            => 'الخطة',
        'th_amount'          => 'المبلغ',
        'subscription_via'   => 'دفعة اشتراك عبر :gateway',
        'ref'                => 'مرجع: :ref',
        'total_paid'         => 'الإجمالي المدفوع',
        'payment_method'     => 'طريقة الدفع',
        'auto_footer'        => 'تم إنشاء هذا الإيصال تلقائيًا. للاستفسارات الضريبية، تواصل مع دعم :company.',

        'gateway_stripe'     => 'Stripe',
        'gateway_paypal'     => 'PayPal',
        'gateway_razorpay'   => 'Razorpay',
        'gateway_paystack'   => 'Paystack',
        'gateway_manual'     => 'يدوي',
    ],

    'invoice' => [
        'doc_title'     => 'فاتورة :number',
        'label'         => 'فاتورة',
        'issued'        => 'تاريخ الإصدار :date',
        'due'           => 'الاستحقاق :date',
        'paid_stamp'    => 'مدفوع',
        'from'          => 'من',
        'bill_to'       => 'إلى',
        'th_item'       => 'البند',
        'th_qty'        => 'الكمية',
        'th_unit'       => 'الوحدة (:currency)',
        'th_total'      => 'الإجمالي (:currency)',
        'subtotal'      => 'المجموع الفرعي',
        'discount'      => 'الخصم',
        'tax'           => 'الضريبة (:rate%)',
        'grand_total'   => 'الإجمالي العام',
        'paid'          => 'مدفوع',
        'amount_due'    => 'المبلغ المستحق',
        'generated'     => 'تم الإنشاء :date · :app',
    ],

    'quote' => [
        'doc_title'          => 'عرض سعر :number',
        'label'              => 'عرض سعر',
        'issued'             => 'تاريخ الإصدار :date',
        'valid_until'        => 'صالح حتى :date',
        'from'               => 'من',
        'to'                 => 'إلى',
        'introduction'       => 'مقدمة',
        'th_item'            => 'البند',
        'th_qty'             => 'الكمية',
        'th_unit'            => 'الوحدة (:currency)',
        'th_total'           => 'الإجمالي (:currency)',
        'subtotal'           => 'المجموع الفرعي',
        'discount'           => 'الخصم',
        'tax'                => 'الضريبة (:rate%)',
        'grand_total'        => 'الإجمالي العام',
        'terms_conditions'   => 'الشروط والأحكام',
        'signed_by'          => 'بواسطة :name في :date (IP :ip)',
        'signed_label'       => 'موقَّع',
        'generated'          => 'تم الإنشاء :date · :app',
    ],

    'report' => [
        'exported'      => 'تم التصدير: :time',
        'analytics'     => 'تحليلات :app',
        'period'        => 'الفترة: :from — :to',
        'filters'       => 'المرشحات:',
        'no_data'       => 'لا توجد بيانات متاحة للفترة المحددة.',
        'footer'        => 'تم الإنشاء بواسطة :brand · :time · إجمالي الصفوف: :count',
    ],

];
