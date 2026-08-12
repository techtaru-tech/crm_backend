<?php

declare(strict_types=1);

return [

    'model_label'        => 'इनवॉइस',
    'plural_model_label' => 'इनवॉइस',

    'nav_label'        => 'इनवॉइस',
    'tabs_outer'       => 'इनवॉइस',

    'filter_label_status' => 'स्थिति',

    'tab_info'         => 'जानकारी',
    'tab_line_items'   => 'पंक्ति आइटम',
    'tab_totals'       => 'कुल',

    'lead'             => 'लीड',
    'company'          => 'कंपनी',
    'issued'           => 'जारी',
    'due_date'         => 'देय तिथि',

    'add_item'         => 'आइटम जोड़ें',
    'product'          => 'उत्पाद',
    'unit_price'       => 'इकाई मूल्य',
    'discount_percent' => 'छूट %',
    'line_total'       => 'पंक्ति कुल',
    'line_total_placeholder' => 'स्वतः',
    'line_item_default_label' => 'नया आइटम',

    'tax_rate'         => 'कर दर',
    'additional_discount' => 'अतिरिक्त छूट',

    'col_number'       => 'संख्या',
    'col_lead'         => 'लीड',
    'col_paid'         => 'भुगतान',
    'col_due'          => 'देय',

    'send'             => 'भेजें',
    'download_pdf'     => 'PDF डाउनलोड करें',
    'record_payment'   => 'भुगतान दर्ज करें',
    'mark_as_paid'     => 'भुगतान के रूप में चिह्नित करें',
    'cancel'           => 'रद्द करें',
    'refund'           => 'रिफ़ंड',
    'refund_modal_heading' => 'इनवॉइस को रिफ़ंड के रूप में चिह्नित करें',
    'refund_modal_description' => 'यह भुगतान खाते में रिफ़ंड दर्ज करता है और इनवॉइस की स्थिति रिफ़ंड पर सेट करता है। यह गेटवे के माध्यम से स्वतः रिफ़ंड नहीं करता है।',
    'more'             => 'अधिक',
    'mark_as_sent'     => 'भेजे गए के रूप में चिह्नित करें',

    'external_reference' => 'बाहरी संदर्भ',

    'public_link'      => 'सार्वजनिक लिंक',

    'notify_no_email'           => 'लीड का कोई ईमेल नहीं है।',
    'notify_sent'               => 'इनवॉइस भेजा गया।',
    'notify_send_failed_prefix' => 'विफल: ',
    'notify_payment_recorded'   => 'भुगतान दर्ज किया गया।',
    'notify_marked_paid'        => 'इनवॉइस भुगतान के रूप में चिह्नित किया गया।',
    'notify_cancelled'          => 'इनवॉइस रद्द किया गया।',
    'notify_refunded'           => 'इनवॉइस रिफ़ंड किया गया।',
    'notify_bulk_marked_sent'   => 'इनवॉइस भेजे गए के रूप में चिह्नित किए गए।',
    'notify_invoice_saved'      => 'इनवॉइस सहेजा गया।',

    'payments_relation_title'   => 'भुगतान',
    'col_amount'                => 'राशि',
    'col_method'                => 'विधि',
    'col_received_at'           => 'प्राप्त समय',
    'col_reference'             => 'संदर्भ',

    'option_status_draft'       => 'ड्राफ़्ट',
    'option_status_sent'        => 'भेजा गया',
    'option_status_partial'     => 'आंशिक',
    'option_status_paid'        => 'भुगतान',
    'option_status_overdue'     => 'अतिदेय',
    'option_status_cancelled'   => 'रद्द',
    'option_status_refunded'    => 'रिफ़ंड',
    'option_gateway_manual'     => 'मैनुअल (बैंक स्थानांतरण)',
    'option_payment_succeeded'  => 'सफल',
    'option_payment_pending'    => 'लंबित',
    'option_payment_failed'     => 'विफल',
    'option_payment_refunded'   => 'रिफ़ंड',

    'field_currency_label'      => 'मुद्रा',
    'field_status_label'        => 'स्थिति',
    'field_notes_label'         => 'नोट्स',
    'field_item_name_label'     => 'नाम',
    'field_item_description_label' => 'विवरण',
    'field_item_quantity_label' => 'मात्रा',
    'field_subtotal_label'      => 'उप-योग',
    'field_tax_amount_label'    => 'कर राशि',
    'field_total_label'         => 'कुल',
    'field_amount_paid_label'   => 'भुगतान की गई राशि',

    'field_gateway_label'       => 'गेटवे',
    'field_amount_label'        => 'राशि',
    'field_payment_status_label' => 'भुगतान स्थिति',
    'field_paid_at_label'       => 'भुगतान समय',

    'col_total'                 => 'कुल',
    'col_status'                => 'स्थिति',
    'col_created_at'            => 'बनाया गया',

    'field_payment_currency_label' => 'मुद्रा',
    'col_gateway'               => 'गेटवे',
    'col_payment_status'        => 'स्थिति',
    'col_paid_at'               => 'भुगतान समय',
    'col_payment_created_at'    => 'बनाया गया',

    'status_draft'              => 'ड्राफ़्ट',
    'status_sent'               => 'भेजा गया',
    'status_partial'            => 'आंशिक',
    'status_overdue'            => 'अतिदेय',
    'status_paid'               => 'भुगतान',
    'status_cancelled'          => 'रद्द',
    'status_refunded'           => 'रिफ़ंड',

    'payment_gateway_stripe'    => 'Stripe',
    'payment_gateway_paypal'    => 'PayPal',
    'payment_gateway_razorpay'  => 'Razorpay',
    'payment_gateway_paystack'  => 'Paystack',
    'payment_gateway_manual'    => 'मैनुअल',

    'payment_status_pending'    => 'लंबित',
    'payment_status_succeeded'  => 'सफल',
    'payment_status_failed'     => 'विफल',
    'payment_status_refunded'   => 'रिफ़ंड',
];
