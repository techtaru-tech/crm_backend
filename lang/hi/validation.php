<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Validation Language Lines (Hindi)
|--------------------------------------------------------------------------
|
| Devanagari Hindi (hi) translation of the Laravel framework
| `validation.php` lines.  Consumed every time a `FormRequest`,
| Filament form or Validator instance produces an error message.
|
| Keys mirror Laravel 12.x — translators only update right-hand side
| strings; do NOT rename keys.  The `custom` and `attributes` blocks
| are intentionally left as the upstream template so buyers can
| populate them with project-specific copy.
*/

return [

    'accepted'             => ':attribute फ़ील्ड को स्वीकार किया जाना चाहिए।',
    'accepted_if'          => ':attribute फ़ील्ड को स्वीकार किया जाना चाहिए जब :other का मान :value हो।',
    'active_url'           => ':attribute फ़ील्ड एक मान्य URL होना चाहिए।',
    'after'                => ':attribute फ़ील्ड :date के बाद की तारीख होनी चाहिए।',
    'after_or_equal'       => ':attribute फ़ील्ड :date के बाद या उसके बराबर की तारीख होनी चाहिए।',
    'alpha'                => ':attribute फ़ील्ड में केवल अक्षर होने चाहिए।',
    'alpha_dash'           => ':attribute फ़ील्ड में केवल अक्षर, संख्याएँ, डैश और अंडरस्कोर होने चाहिए।',
    'alpha_num'            => ':attribute फ़ील्ड में केवल अक्षर और संख्याएँ होनी चाहिए।',
    'any_of'               => ':attribute फ़ील्ड अमान्य है।',
    'array'                => ':attribute फ़ील्ड एक सरणी होनी चाहिए।',
    'ascii'                => ':attribute फ़ील्ड में केवल एकल-बाइट अल्फ़ान्यूमेरिक वर्ण और प्रतीक होने चाहिए।',
    'before'               => ':attribute फ़ील्ड :date से पहले की तारीख होनी चाहिए।',
    'before_or_equal'      => ':attribute फ़ील्ड :date से पहले या उसके बराबर की तारीख होनी चाहिए।',
    'between'              => [
        'array'   => ':attribute फ़ील्ड में :min और :max के बीच आइटम होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :min और :max किलोबाइट के बीच होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :min और :max के बीच होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :min और :max वर्णों के बीच होनी चाहिए।',
    ],
    'boolean'              => ':attribute फ़ील्ड सत्य या असत्य होनी चाहिए।',
    'can'                  => ':attribute फ़ील्ड में अनधिकृत मान है।',
    'confirmed'            => ':attribute फ़ील्ड की पुष्टि मेल नहीं खाती।',
    'contains'             => ':attribute फ़ील्ड में आवश्यक मान गुम है।',
    'current_password'     => 'पासवर्ड गलत है।',
    'date'                 => ':attribute फ़ील्ड एक मान्य तारीख होनी चाहिए।',
    'date_equals'          => ':attribute फ़ील्ड :date के बराबर की तारीख होनी चाहिए।',
    'date_format'          => ':attribute फ़ील्ड को :format प्रारूप से मेल खाना चाहिए।',
    'decimal'              => ':attribute फ़ील्ड में :decimal दशमलव स्थान होने चाहिए।',
    'declined'             => ':attribute फ़ील्ड को अस्वीकार किया जाना चाहिए।',
    'declined_if'          => ':attribute फ़ील्ड को अस्वीकार किया जाना चाहिए जब :other का मान :value हो।',
    'different'            => ':attribute फ़ील्ड और :other अलग-अलग होने चाहिए।',
    'digits'               => ':attribute फ़ील्ड :digits अंकों की होनी चाहिए।',
    'digits_between'       => ':attribute फ़ील्ड :min और :max अंकों के बीच होनी चाहिए।',
    'dimensions'           => ':attribute फ़ील्ड में अमान्य छवि आयाम हैं।',
    'distinct'             => ':attribute फ़ील्ड में डुप्लिकेट मान है।',
    'doesnt_contain'       => ':attribute फ़ील्ड में निम्न में से कोई भी नहीं होना चाहिए: :values।',
    'doesnt_end_with'      => ':attribute फ़ील्ड निम्न में से किसी के साथ समाप्त नहीं होनी चाहिए: :values।',
    'doesnt_start_with'    => ':attribute फ़ील्ड निम्न में से किसी से शुरू नहीं होनी चाहिए: :values।',
    'email'                => ':attribute फ़ील्ड एक मान्य ईमेल पता होना चाहिए।',
    'encoding'             => ':attribute फ़ील्ड :encoding में एन्कोडेड होनी चाहिए।',
    'ends_with'            => ':attribute फ़ील्ड निम्न में से किसी के साथ समाप्त होनी चाहिए: :values।',
    'enum'                 => 'चयनित :attribute अमान्य है।',
    'exists'               => 'चयनित :attribute अमान्य है।',
    'extensions'           => ':attribute फ़ील्ड में निम्न में से एक एक्सटेंशन होना चाहिए: :values।',
    'file'                 => ':attribute फ़ील्ड एक फ़ाइल होनी चाहिए।',
    'filled'               => ':attribute फ़ील्ड में एक मान होना चाहिए।',
    'gt'                   => [
        'array'   => ':attribute फ़ील्ड में :value से अधिक आइटम होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :value किलोबाइट से अधिक होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से अधिक होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :value वर्णों से अधिक होनी चाहिए।',
    ],
    'gte'                  => [
        'array'   => ':attribute फ़ील्ड में :value आइटम या अधिक होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :value किलोबाइट से अधिक या उसके बराबर होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से अधिक या उसके बराबर होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :value वर्णों से अधिक या उसके बराबर होनी चाहिए।',
    ],
    'hex_color'            => ':attribute फ़ील्ड एक मान्य हेक्साडेसिमल रंग होना चाहिए।',
    'image'                => ':attribute फ़ील्ड एक छवि होनी चाहिए।',
    'in'                   => 'चयनित :attribute अमान्य है।',
    'in_array'             => ':attribute फ़ील्ड :other में मौजूद होनी चाहिए।',
    'in_array_keys'        => ':attribute फ़ील्ड में निम्न में से कम से कम एक कुंजी होनी चाहिए: :values।',
    'integer'              => ':attribute फ़ील्ड एक पूर्णांक होनी चाहिए।',
    'ip'                   => ':attribute फ़ील्ड एक मान्य IP पता होना चाहिए।',
    'ipv4'                 => ':attribute फ़ील्ड एक मान्य IPv4 पता होना चाहिए।',
    'ipv6'                 => ':attribute फ़ील्ड एक मान्य IPv6 पता होना चाहिए।',
    'json'                 => ':attribute फ़ील्ड एक मान्य JSON स्ट्रिंग होनी चाहिए।',
    'list'                 => ':attribute फ़ील्ड एक सूची होनी चाहिए।',
    'lowercase'            => ':attribute फ़ील्ड लोअरकेस होनी चाहिए।',
    'lt'                   => [
        'array'   => ':attribute फ़ील्ड में :value से कम आइटम होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :value किलोबाइट से कम होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से कम होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :value वर्णों से कम होनी चाहिए।',
    ],
    'lte'                  => [
        'array'   => ':attribute फ़ील्ड में :value से अधिक आइटम नहीं होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :value किलोबाइट से कम या उसके बराबर होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से कम या उसके बराबर होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :value वर्णों से कम या उसके बराबर होनी चाहिए।',
    ],
    'mac_address'          => ':attribute फ़ील्ड एक मान्य MAC पता होना चाहिए।',
    'max'                  => [
        'array'   => ':attribute फ़ील्ड में :max से अधिक आइटम नहीं होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :max किलोबाइट से अधिक नहीं होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :max से अधिक नहीं होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :max वर्णों से अधिक नहीं होनी चाहिए।',
    ],
    'max_digits'           => ':attribute फ़ील्ड :max अंकों से अधिक नहीं होनी चाहिए।',
    'mimes'                => ':attribute फ़ील्ड इस प्रकार की फ़ाइल होनी चाहिए: :values।',
    'mimetypes'            => ':attribute फ़ील्ड इस प्रकार की फ़ाइल होनी चाहिए: :values।',
    'min'                  => [
        'array'   => ':attribute फ़ील्ड में कम से कम :min आइटम होने चाहिए।',
        'file'    => ':attribute फ़ील्ड कम से कम :min किलोबाइट होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड कम से कम :min होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड कम से कम :min वर्णों की होनी चाहिए।',
    ],
    'min_digits'           => ':attribute फ़ील्ड में कम से कम :min अंक होने चाहिए।',
    'missing'              => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
    'missing_if'           => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए जब :other का मान :value हो।',
    'missing_unless'       => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए जब तक कि :other का मान :value न हो।',
    'missing_with'         => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए जब :values मौजूद हो।',
    'missing_with_all'     => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए जब :values मौजूद हों।',
    'multiple_of'          => ':attribute फ़ील्ड :value का गुणक होनी चाहिए।',
    'not_in'               => 'चयनित :attribute अमान्य है।',
    'not_regex'            => ':attribute फ़ील्ड का प्रारूप अमान्य है।',
    'numeric'              => ':attribute फ़ील्ड एक संख्या होनी चाहिए।',
    'password'             => [
        'letters'       => ':attribute फ़ील्ड में कम से कम एक अक्षर होना चाहिए।',
        'mixed'         => ':attribute फ़ील्ड में कम से कम एक बड़ा अक्षर और एक छोटा अक्षर होना चाहिए।',
        'numbers'       => ':attribute फ़ील्ड में कम से कम एक संख्या होनी चाहिए।',
        'symbols'       => ':attribute फ़ील्ड में कम से कम एक प्रतीक होना चाहिए।',
        'uncompromised' => 'दिया गया :attribute एक डेटा लीक में प्रकट हुआ है। कृपया एक अलग :attribute चुनें।',
    ],
    'present'              => ':attribute फ़ील्ड मौजूद होनी चाहिए।',
    'present_if'           => ':attribute फ़ील्ड मौजूद होनी चाहिए जब :other का मान :value हो।',
    'present_unless'       => ':attribute फ़ील्ड मौजूद होनी चाहिए जब तक कि :other का मान :value न हो।',
    'present_with'         => ':attribute फ़ील्ड मौजूद होनी चाहिए जब :values मौजूद हो।',
    'present_with_all'     => ':attribute फ़ील्ड मौजूद होनी चाहिए जब :values मौजूद हों।',
    'prohibited'           => ':attribute फ़ील्ड निषिद्ध है।',
    'prohibited_if'        => ':attribute फ़ील्ड निषिद्ध है जब :other का मान :value हो।',
    'prohibited_if_accepted' => ':attribute फ़ील्ड निषिद्ध है जब :other स्वीकार किया जाता है।',
    'prohibited_if_declined' => ':attribute फ़ील्ड निषिद्ध है जब :other अस्वीकार किया जाता है।',
    'prohibited_unless'    => ':attribute फ़ील्ड निषिद्ध है जब तक कि :other :values में न हो।',
    'prohibits'            => ':attribute फ़ील्ड :other को मौजूद होने से रोकती है।',
    'regex'                => ':attribute फ़ील्ड का प्रारूप अमान्य है।',
    'required'             => ':attribute फ़ील्ड आवश्यक है।',
    'required_array_keys'  => ':attribute फ़ील्ड में निम्न के लिए प्रविष्टियाँ होनी चाहिए: :values।',
    'required_if'          => ':attribute फ़ील्ड आवश्यक है जब :other का मान :value हो।',
    'required_if_accepted' => ':attribute फ़ील्ड आवश्यक है जब :other स्वीकार किया जाता है।',
    'required_if_declined' => ':attribute फ़ील्ड आवश्यक है जब :other अस्वीकार किया जाता है।',
    'required_unless'      => ':attribute फ़ील्ड आवश्यक है जब तक कि :other :values में न हो।',
    'required_with'        => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद हो।',
    'required_with_all'    => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद हों।',
    'required_without'     => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद न हो।',
    'required_without_all' => ':attribute फ़ील्ड आवश्यक है जब :values में से कोई भी मौजूद न हो।',
    'same'                 => ':attribute फ़ील्ड :other से मेल खानी चाहिए।',
    'size'                 => [
        'array'   => ':attribute फ़ील्ड में :size आइटम होने चाहिए।',
        'file'    => ':attribute फ़ील्ड :size किलोबाइट की होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :size होनी चाहिए।',
        'string'  => ':attribute फ़ील्ड :size वर्णों की होनी चाहिए।',
    ],
    'starts_with'          => ':attribute फ़ील्ड निम्न में से किसी से शुरू होनी चाहिए: :values।',
    'string'               => ':attribute फ़ील्ड एक स्ट्रिंग होनी चाहिए।',
    'timezone'             => ':attribute फ़ील्ड एक मान्य समय क्षेत्र होनी चाहिए।',
    'unique'               => ':attribute पहले से ही लिया जा चुका है।',
    'uploaded'             => ':attribute अपलोड करने में विफल।',
    'uppercase'            => ':attribute फ़ील्ड अपरकेस होनी चाहिए।',
    'url'                  => ':attribute फ़ील्ड एक मान्य URL होना चाहिए।',
    'ulid'                 => ':attribute फ़ील्ड एक मान्य ULID होनी चाहिए।',
    'uuid'                 => ':attribute फ़ील्ड एक मान्य UUID होनी चाहिए।',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [],

];
