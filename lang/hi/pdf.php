<?php

declare(strict_types=1);

return [

    'receipt' => [
        'doc_title'          => 'रसीद :number',
        'title'              => 'रसीद',
        'issued'             => 'जारी :date',
        'from'               => 'से',
        'billed_to'          => 'बिल किया गया',
        'anonymized'         => '<anonymized>',
        'gdpr_anonymized'    => 'GDPR अनुच्छेद 17 के तहत कार्यक्षेत्र हटाया गया। कर रिकॉर्ड बरकरार।',
        'vat_label'          => 'VAT/GST: :number',
        'th_description'     => 'विवरण',
        'th_plan'            => 'प्लान',
        'th_amount'          => 'राशि',
        'subscription_via'   => ':gateway के माध्यम से सब्सक्रिप्शन भुगतान',
        'ref'                => 'संदर्भ: :ref',
        'total_paid'         => 'कुल भुगतान',
        'payment_method'     => 'भुगतान विधि',
        'auto_footer'        => 'यह रसीद स्वचालित रूप से उत्पन्न की गई थी। कर प्रश्नों के लिए, :company सपोर्ट से संपर्क करें।',

        'gateway_stripe'     => 'Stripe',
        'gateway_paypal'     => 'PayPal',
        'gateway_razorpay'   => 'Razorpay',
        'gateway_paystack'   => 'Paystack',
        'gateway_manual'     => 'मैन्युअल',
    ],

    'invoice' => [
        'doc_title'     => 'इनवॉइस :number',
        'label'         => 'इनवॉइस',
        'issued'        => 'जारी :date',
        'due'           => 'देय :date',
        'paid_stamp'    => 'भुगतान किया गया',
        'from'          => 'से',
        'bill_to'       => 'बिल को',
        'th_item'       => 'आइटम',
        'th_qty'        => 'मात्रा',
        'th_unit'       => 'इकाई (:currency)',
        'th_total'      => 'कुल (:currency)',
        'subtotal'      => 'उप-योग',
        'discount'      => 'छूट',
        'tax'           => 'कर (:rate%)',
        'grand_total'   => 'कुल योग',
        'paid'          => 'भुगतान किया गया',
        'amount_due'    => 'देय राशि',
        'generated'     => 'उत्पन्न :date · :app',
    ],

    'quote' => [
        'doc_title'          => 'कोटेशन :number',
        'label'              => 'कोटेशन',
        'issued'             => 'जारी :date',
        'valid_until'        => 'मान्य तक :date',
        'from'               => 'से',
        'to'                 => 'को',
        'introduction'       => 'परिचय',
        'th_item'            => 'आइटम',
        'th_qty'             => 'मात्रा',
        'th_unit'            => 'इकाई (:currency)',
        'th_total'           => 'कुल (:currency)',
        'subtotal'           => 'उप-योग',
        'discount'           => 'छूट',
        'tax'                => 'कर (:rate%)',
        'grand_total'        => 'कुल योग',
        'terms_conditions'   => 'नियम और शर्तें',
        'signed_by'          => ':name द्वारा :date को (IP :ip)',
        'signed_label'       => 'हस्ताक्षरित',
        'generated'          => 'उत्पन्न :date · :app',
    ],

    'report' => [
        'exported'      => 'निर्यात किया गया: :time',
        'analytics'     => ':app एनालिटिक्स',
        'period'        => 'अवधि: :from — :to',
        'filters'       => 'फ़िल्टर:',
        'no_data'       => 'चयनित अवधि के लिए कोई डेटा उपलब्ध नहीं।',
        'footer'        => ':brand द्वारा उत्पन्न · :time · कुल पंक्तियाँ: :count',
    ],

];
