<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Validation Language Lines (Arabic)
|--------------------------------------------------------------------------
|
| Modern Standard Arabic (ar) translation of the Laravel framework
| `validation.php` lines.  Consumed every time a `FormRequest`,
| Filament form or Validator instance produces an error message.
|
| Keys mirror Laravel 12.x — translators only update right-hand side
| strings; do NOT rename keys.  The `custom` and `attributes` blocks
| are intentionally left as the upstream template so buyers can
| populate them with project-specific copy.
*/

return [

    'accepted'             => 'يجب قبول حقل :attribute.',
    'accepted_if'          => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url'           => 'يجب أن يكون حقل :attribute عنوان URL صالحًا.',
    'after'                => 'يجب أن يكون حقل :attribute تاريخًا بعد :date.',
    'after_or_equal'       => 'يجب أن يكون حقل :attribute تاريخًا بعد أو يساوي :date.',
    'alpha'                => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash'           => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num'            => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'any_of'               => 'حقل :attribute غير صالح.',
    'array'                => 'يجب أن يكون حقل :attribute مصفوفة.',
    'ascii'                => 'يجب أن يحتوي حقل :attribute على أحرف ورموز أبجدية رقمية أحادية البايت فقط.',
    'before'               => 'يجب أن يكون حقل :attribute تاريخًا قبل :date.',
    'before_or_equal'      => 'يجب أن يكون حقل :attribute تاريخًا قبل أو يساوي :date.',
    'between'              => [
        'array'   => 'يجب أن يحتوي حقل :attribute على ما بين :min و :max عنصرًا.',
        'file'    => 'يجب أن يكون حقل :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute بين :min و :max.',
        'string'  => 'يجب أن يكون حقل :attribute بين :min و :max حرفًا.',
    ],
    'boolean'              => 'يجب أن يكون حقل :attribute صحيحًا أو خاطئًا.',
    'can'                  => 'يحتوي حقل :attribute على قيمة غير مصرح بها.',
    'confirmed'            => 'تأكيد حقل :attribute غير مطابق.',
    'contains'             => 'حقل :attribute يفتقد إلى قيمة مطلوبة.',
    'current_password'     => 'كلمة المرور غير صحيحة.',
    'date'                 => 'يجب أن يكون حقل :attribute تاريخًا صالحًا.',
    'date_equals'          => 'يجب أن يكون حقل :attribute تاريخًا يساوي :date.',
    'date_format'          => 'يجب أن يطابق حقل :attribute التنسيق :format.',
    'decimal'              => 'يجب أن يحتوي حقل :attribute على :decimal منزلة عشرية.',
    'declined'             => 'يجب رفض حقل :attribute.',
    'declined_if'          => 'يجب رفض حقل :attribute عندما يكون :other هو :value.',
    'different'            => 'يجب أن يكون حقل :attribute و :other مختلفين.',
    'digits'               => 'يجب أن يكون حقل :attribute :digits رقمًا.',
    'digits_between'       => 'يجب أن يكون حقل :attribute بين :min و :max رقمًا.',
    'dimensions'           => 'يحتوي حقل :attribute على أبعاد صورة غير صالحة.',
    'distinct'             => 'يحتوي حقل :attribute على قيمة مكررة.',
    'doesnt_contain'       => 'يجب ألا يحتوي حقل :attribute على أي مما يلي: :values.',
    'doesnt_end_with'      => 'يجب ألا ينتهي حقل :attribute بأي مما يلي: :values.',
    'doesnt_start_with'    => 'يجب ألا يبدأ حقل :attribute بأي مما يلي: :values.',
    'email'                => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالحًا.',
    'encoding'             => 'يجب أن يكون حقل :attribute مشفرًا بـ :encoding.',
    'ends_with'            => 'يجب أن ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'enum'                 => ':attribute المحدد غير صالح.',
    'exists'               => ':attribute المحدد غير صالح.',
    'extensions'           => 'يجب أن يحتوي حقل :attribute على أحد الامتدادات التالية: :values.',
    'file'                 => 'يجب أن يكون حقل :attribute ملفًا.',
    'filled'               => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt'                   => [
        'array'   => 'يجب أن يحتوي حقل :attribute على أكثر من :value عنصرًا.',
        'file'    => 'يجب أن يكون حقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من :value.',
        'string'  => 'يجب أن يكون حقل :attribute أكبر من :value حرفًا.',
    ],
    'gte'                  => [
        'array'   => 'يجب أن يحتوي حقل :attribute على :value عنصرًا أو أكثر.',
        'file'    => 'يجب أن يكون حقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من أو يساوي :value.',
        'string'  => 'يجب أن يكون حقل :attribute أكبر من أو يساوي :value حرفًا.',
    ],
    'hex_color'            => 'يجب أن يكون حقل :attribute لونًا سداسيًا عشريًا صالحًا.',
    'image'                => 'يجب أن يكون حقل :attribute صورة.',
    'in'                   => ':attribute المحدد غير صالح.',
    'in_array'             => 'يجب أن يوجد حقل :attribute في :other.',
    'in_array_keys'        => 'يجب أن يحتوي حقل :attribute على واحد على الأقل من المفاتيح التالية: :values.',
    'integer'              => 'يجب أن يكون حقل :attribute عددًا صحيحًا.',
    'ip'                   => 'يجب أن يكون حقل :attribute عنوان IP صالحًا.',
    'ipv4'                 => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحًا.',
    'ipv6'                 => 'يجب أن يكون حقل :attribute عنوان IPv6 صالحًا.',
    'json'                 => 'يجب أن يكون حقل :attribute سلسلة JSON صالحة.',
    'list'                 => 'يجب أن يكون حقل :attribute قائمة.',
    'lowercase'            => 'يجب أن يكون حقل :attribute بأحرف صغيرة.',
    'lt'                   => [
        'array'   => 'يجب أن يحتوي حقل :attribute على أقل من :value عنصرًا.',
        'file'    => 'يجب أن يكون حقل :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أقل من :value.',
        'string'  => 'يجب أن يكون حقل :attribute أقل من :value حرفًا.',
    ],
    'lte'                  => [
        'array'   => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عنصرًا.',
        'file'    => 'يجب أن يكون حقل :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أقل من أو يساوي :value.',
        'string'  => 'يجب أن يكون حقل :attribute أقل من أو يساوي :value حرفًا.',
    ],
    'mac_address'          => 'يجب أن يكون حقل :attribute عنوان MAC صالحًا.',
    'max'                  => [
        'array'   => 'يجب ألا يحتوي حقل :attribute على أكثر من :max عنصرًا.',
        'file'    => 'يجب ألا يكون حقل :attribute أكبر من :max كيلوبايت.',
        'numeric' => 'يجب ألا يكون حقل :attribute أكبر من :max.',
        'string'  => 'يجب ألا يكون حقل :attribute أكبر من :max حرفًا.',
    ],
    'max_digits'           => 'يجب ألا يحتوي حقل :attribute على أكثر من :max رقمًا.',
    'mimes'                => 'يجب أن يكون حقل :attribute ملفًا من النوع: :values.',
    'mimetypes'            => 'يجب أن يكون حقل :attribute ملفًا من النوع: :values.',
    'min'                  => [
        'array'   => 'يجب أن يحتوي حقل :attribute على :min عنصرًا على الأقل.',
        'file'    => 'يجب أن يكون حقل :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute على الأقل :min.',
        'string'  => 'يجب أن يكون حقل :attribute على الأقل :min حرفًا.',
    ],
    'min_digits'           => 'يجب أن يحتوي حقل :attribute على :min رقمًا على الأقل.',
    'missing'              => 'يجب أن يكون حقل :attribute مفقودًا.',
    'missing_if'           => 'يجب أن يكون حقل :attribute مفقودًا عندما يكون :other هو :value.',
    'missing_unless'       => 'يجب أن يكون حقل :attribute مفقودًا ما لم يكن :other هو :value.',
    'missing_with'         => 'يجب أن يكون حقل :attribute مفقودًا عندما يكون :values موجودًا.',
    'missing_with_all'     => 'يجب أن يكون حقل :attribute مفقودًا عندما تكون :values موجودة.',
    'multiple_of'          => 'يجب أن يكون حقل :attribute من مضاعفات :value.',
    'not_in'               => ':attribute المحدد غير صالح.',
    'not_regex'            => 'تنسيق حقل :attribute غير صالح.',
    'numeric'              => 'يجب أن يكون حقل :attribute رقمًا.',
    'password'             => [
        'letters'       => 'يجب أن يحتوي حقل :attribute على حرف واحد على الأقل.',
        'mixed'         => 'يجب أن يحتوي حقل :attribute على حرف كبير واحد وحرف صغير واحد على الأقل.',
        'numbers'       => 'يجب أن يحتوي حقل :attribute على رقم واحد على الأقل.',
        'symbols'       => 'يجب أن يحتوي حقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'ظهر :attribute المحدد في تسريب بيانات. يرجى اختيار :attribute آخر.',
    ],
    'present'              => 'يجب أن يكون حقل :attribute موجودًا.',
    'present_if'           => 'يجب أن يكون حقل :attribute موجودًا عندما يكون :other هو :value.',
    'present_unless'       => 'يجب أن يكون حقل :attribute موجودًا ما لم يكن :other هو :value.',
    'present_with'         => 'يجب أن يكون حقل :attribute موجودًا عندما يكون :values موجودًا.',
    'present_with_all'     => 'يجب أن يكون حقل :attribute موجودًا عندما تكون :values موجودة.',
    'prohibited'           => 'حقل :attribute ممنوع.',
    'prohibited_if'        => 'حقل :attribute ممنوع عندما يكون :other هو :value.',
    'prohibited_if_accepted' => 'حقل :attribute ممنوع عند قبول :other.',
    'prohibited_if_declined' => 'حقل :attribute ممنوع عند رفض :other.',
    'prohibited_unless'    => 'حقل :attribute ممنوع ما لم يكن :other في :values.',
    'prohibits'            => 'حقل :attribute يمنع :other من الظهور.',
    'regex'                => 'تنسيق حقل :attribute غير صالح.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_array_keys'  => 'يجب أن يحتوي حقل :attribute على إدخالات لـ: :values.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',
    'required_if_declined' => 'حقل :attribute مطلوب عند رفض :other.',
    'required_unless'      => 'حقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with'        => 'حقل :attribute مطلوب عندما يكون :values موجودًا.',
    'required_with_all'    => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'حقل :attribute مطلوب عندما لا يكون :values موجودًا.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :values موجودًا.',
    'same'                 => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size'                 => [
        'array'   => 'يجب أن يحتوي حقل :attribute على :size عنصرًا.',
        'file'    => 'يجب أن يكون حقل :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute :size.',
        'string'  => 'يجب أن يكون حقل :attribute :size حرفًا.',
    ],
    'starts_with'          => 'يجب أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'string'               => 'يجب أن يكون حقل :attribute سلسلة نصية.',
    'timezone'             => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
    'unique'               => ':attribute مأخوذ بالفعل.',
    'uploaded'             => 'فشل تحميل :attribute.',
    'uppercase'            => 'يجب أن يكون حقل :attribute بأحرف كبيرة.',
    'url'                  => 'يجب أن يكون حقل :attribute عنوان URL صالحًا.',
    'ulid'                 => 'يجب أن يكون حقل :attribute ULID صالحًا.',
    'uuid'                 => 'يجب أن يكون حقل :attribute UUID صالحًا.',

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
