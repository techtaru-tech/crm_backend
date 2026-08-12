<?php

declare(strict_types=1);

return [
    'calendar_oauth' => [
        'consent_rejected'       => 'प्रदाता ने सहमति अस्वीकार कर दी: :error',
        'no_authorization_code'  => 'प्रदाता से कोई प्राधिकरण कोड प्राप्त नहीं हुआ।',
        'token_exchange_failed'  => 'प्राधिकरण कोड का आदान-प्रदान नहीं किया जा सका। अपने services कॉन्फ़िगरेशन में :provider क्लाइंट क्रेडेंशियल्स की जाँच करें।',
        'connection_save_failed' => 'कनेक्शन सहेजा नहीं जा सका। पुनः प्रयास करें।',
        'connected_success'      => ':provider कैलेंडर :email के रूप में कनेक्ट किया गया।',
    ],

    'recaptcha' => [
        'verification_failed'       => 'reCAPTCHA सत्यापन विफल रहा। कृपया पृष्ठ को रिफ़्रेश करें और पुनः प्रयास करें।',
        'verification_failed_short' => 'reCAPTCHA सत्यापन विफल रहा। कृपया रिफ़्रेश करें और पुनः प्रयास करें।',
    ],

    'invoice_payment' => [
        'gateway_not_supported' => 'चयनित गेटवे अभी इनवॉइस भुगतान का समर्थन नहीं करता है। कृपया कोई अन्य चुनें।',
        'start_failed'          => 'भुगतान आरंभ नहीं किया जा सका। कृपया कोई अन्य विधि आज़माएँ।',
    ],

    'password_setup' => [
        'invalid_or_expired' => 'यह सेटअप लिंक अमान्य है या समाप्त हो चुका है। नया लिंक प्राप्त करने के लिए साइन-इन पृष्ठ पर "पासवर्ड भूल गए" लिंक का उपयोग करें।',
    ],

    'coupon' => [
        'prefix'       => 'कूपन: ',
        'invalid_code' => 'अमान्य कोड।',
    ],

    'oauth' => [
        'no_token_url'             => ':type के लिए कोई टोकन URL नहीं',
        'token_exchange_failed'    => 'टोकन आदान-प्रदान विफल (:status): :body',
        'salesforce_invalid_url'   => 'Salesforce instance_url *.salesforce.com या *.force.com पर होना चाहिए (प्राप्त :host)।',
        'salesforce_safety_failed' => 'Salesforce instance_url सुरक्षा जाँच में विफल: :error',
        'salesforce_token_failed'  => 'Salesforce टोकन आदान-प्रदान विफल (:status): :body',
        'meta_token_failed'        => 'Meta टोकन आदान-प्रदान विफल: :body',
        'meta_no_access_token'     => 'Meta से कोई access_token नहीं मिला',
    ],
];
