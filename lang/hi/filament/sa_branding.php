<?php

declare(strict_types=1);

/*
|-----------------------------------------------------------------
| SuperAdmin BrandingPage - Filament admin strings
|-----------------------------------------------------------------
| Accessed via __('filament/sa_branding.<key>').
*/

return [
    'navigation_label'              => 'ब्रांडिंग',
    'title'                         => 'ब्रांडिंग',

    'section_identity'              => 'ब्रांड पहचान',
    'section_identity_description'  => 'वह नाम और विज़ुअल्स जो आपके प्लेटफ़ॉर्म का सार्वजनिक लैंडिंग पृष्ठ, लॉगिन स्क्रीन और टेनेंट पैनलों में प्रतिनिधित्व करते हैं।',
    'app_name_label'                => 'ब्रांड का नाम',
    'app_name_helper'               => 'ब्राउज़र टैब, लॉगिन पृष्ठ और पैनल हेडर में दिखाया जाने वाला उत्पाद का नाम। "LeadHub" से अपनी ब्रांड में बदलें।',
    'logo_url_label'                => 'लोगो URL',
    'logo_url_helper'               => 'आपके ब्रांड लोगो (PNG या SVG) का सार्वजनिक URL। डिफ़ॉल्ट चिह्न का उपयोग करने के लिए खाली छोड़ें।',
    'logo_file_label'               => 'या लोगो फ़ाइल अपलोड करें',
    'logo_file_helper'              => 'PNG / SVG / JPG 2 MB तक। अपलोड करने पर ऊपर के URL को अपलोड की गई फ़ाइल से बदल दिया जाता है।',
    'favicon_url_label'             => 'फ़ेविकॉन URL',
    'favicon_url_helper'            => 'ब्राउज़र टैब के लिए 32x32 (या बड़े) ICO / PNG का सार्वजनिक URL।',
    'favicon_file_label'            => 'या फ़ेविकॉन फ़ाइल अपलोड करें',
    'favicon_file_helper'           => 'चौकोर PNG / ICO / SVG (32x32 या बड़ा)। अपलोड करने पर ऊपर के URL को बदल दिया जाता है।',
    'footer_text_label'             => 'फ़ुटर टेक्स्ट',
    'footer_text_helper'            => 'सार्वजनिक फ़ुटर में दिखाई जाने वाली छोटी टैगलाइन या कॉपीराइट पंक्ति। Markdown पार्स नहीं किया जाता।',

    'section_colors'                => 'ब्रांड के रंग',
    'section_colors_description'    => 'सार्वजनिक साइट और एडमिन पैनलों में बटन, लिंक, और एक्सेंट पर लागू रंग पैलेट।',
    'primary_color_label'           => 'प्राथमिक रंग',
    'accent_color_label'            => 'एक्सेंट रंग',

    'section_login'                 => 'लॉगिन स्क्रीन',
    'section_login_description'     => '/admin और /super-admin लॉगिन स्क्रीनों के बैकग्राउंड को कस्टमाइज़ करें।',
    'login_bg_color_label'          => 'लॉगिन बैकग्राउंड रंग',
    'login_bg_image_label'          => 'लॉगिन बैकग्राउंड छवि URL',

    'action_save'                   => 'ब्रांडिंग सहेजें',
    'saved_title'                   => 'ब्रांडिंग सहेजी गई',
    'saved_body'                    => 'आपके परिवर्तन सार्वजनिक साइट और एडमिन पैनलों पर लाइव हैं।',
    'save_failed_title'             => 'ब्रांडिंग सहेजी नहीं जा सकी',
    'save_failed_body'              => 'सहेजना विफल हुआ और वापस ले लिया गया। विस्तृत त्रुटि के लिए storage/logs/laravel.log देखें।',
];
