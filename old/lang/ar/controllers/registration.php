<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| RegistrationController — public registration validator/messages
|--------------------------------------------------------------------------
|
| Translation strings used by RegistrationController custom-validator
| closures and validator message bag overrides. Accessed via
| __('controllers/registration.<key>').
|
*/

return [
    'reserved_workspace_url' => 'سينتج عن «:value» رابط مساحة عمل محجوز. يُرجى اختيار اسم مختلف.',
    'must_accept_terms'      => 'يجب قبول الشروط للمتابعة.',
    'account_exists'         => 'يوجد حساب بهذا البريد الإلكتروني بالفعل. جرّب تسجيل الدخول بدلًا من ذلك.',
];
