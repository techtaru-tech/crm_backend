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
    'set_your_password'        => 'अपना पासवर्ड सेट करें',
    'fix_the_following'        => 'निम्नलिखित को ठीक करें:',
    'email_label'              => 'ईमेल',
    'new_password_label'       => 'नया पासवर्ड',
    'confirm_password_label'   => 'पासवर्ड की पुष्टि करें',
    'set_password_submit'      => 'पासवर्ड सेट करें और साइन इन करें',

    // Invitation accept
    'create_your_account'      => 'अपना खाता बनाएँ',
    'your_name_label'          => 'आपका नाम',
    'password_label'           => 'पासवर्ड',
    'accept_invitation_submit' => 'आमंत्रण स्वीकार करें और खाता बनाएँ',

    // Browser titles
    'password_setup_title'     => 'अपना पासवर्ड सेट करें — :app',
    'invitation_accept_title'  => 'आमंत्रण स्वीकार करें — :app',

    // Password setup lead + footer
    'password_setup_lead'         => "आपको :app में आमंत्रित किया गया है। साइन-इन पूरा करने के लिए :email हेतु पासवर्ड सेट करें।",
    'password_min_placeholder'    => 'कम से कम 8 वर्ण',
    'password_strength_hint'      => 'कम से कम 8 वर्णों का उपयोग करें, जिसमें एक अक्षर और एक संख्या शामिल हो।',
    'password_setup_link_expired' => 'यदि यह लिंक समाप्त हो गया है, तो :signin पर वापस जाएँ और नया आमंत्रण अनुरोध करें।',
    'signin_link'                 => 'साइन इन करें',

    // Invitation pill + headings
    'invitation_pill'             => 'टीम आमंत्रण',
    'invitation_youre_invited'    => "आपको :workspace में शामिल होने के लिए आमंत्रित किया गया है",
    'invitation_role_on_app'      => ':app पर :role के रूप में',
    'name_placeholder'            => 'जेन डो',
    'invitation_expires_at'       => 'यह आमंत्रण :when समाप्त होगा।',
];
