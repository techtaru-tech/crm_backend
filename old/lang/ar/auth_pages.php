<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Auth pages (resources/views/auth/*.blade.php, invitation/*.blade.php)
|------------------------------------------------------------
| Accessed via __('auth_pages.<key>').
*/

return [

    // Password setup
    'set_your_password'        => 'تعيين كلمة المرور',
    'fix_the_following'        => 'يُرجى تصحيح ما يلي:',
    'email_label'              => 'البريد الإلكتروني',
    'new_password_label'       => 'كلمة المرور الجديدة',
    'confirm_password_label'   => 'تأكيد كلمة المرور',
    'set_password_submit'      => 'تعيين كلمة المرور وتسجيل الدخول',

    // Invitation accept
    'create_your_account'      => 'إنشاء حسابك',
    'your_name_label'          => 'اسمك',
    'password_label'           => 'كلمة المرور',
    'accept_invitation_submit' => 'قبول الدعوة وإنشاء الحساب',

    // ─── Browser titles ──────────────────────────────────────────────
    'password_setup_title'     => 'تعيين كلمة المرور — :app',
    'invitation_accept_title'  => 'قبول الدعوة — :app',

    // ─── Password setup lead + footer ────────────────────────────────
    'password_setup_lead'         => 'تمت دعوتك إلى :app. عيّن كلمة مرور لـ :email لإكمال تسجيل الدخول.',
    'password_min_placeholder'    => '٨ أحرف على الأقل',
    'password_strength_hint'      => 'استخدم ٨ أحرف على الأقل، تتضمن حرفًا ورقمًا.',
    'password_setup_link_expired' => 'إذا انتهت صلاحية هذا الرابط، ارجع إلى :signin واطلب دعوة جديدة.',
    'signin_link'                 => 'تسجيل الدخول',

    // ─── Invitation pill + headings ──────────────────────────────────
    'invitation_pill'             => 'دعوة فريق',
    'invitation_youre_invited'    => 'تمت دعوتك للانضمام إلى :workspace',
    'invitation_role_on_app'      => 'بصفة :role على :app',
    'name_placeholder'            => 'محمد أحمد',
    'invitation_expires_at'       => 'تنتهي صلاحية هذه الدعوة :when.',
];
