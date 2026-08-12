<?php

declare(strict_types=1);

return [
    'title'                            => 'अपडेट',
    'navigation_label'                 => 'अपडेट',

    'apply_section_description'        => 'इस इंस्टॉलेशन को इन-प्लेस अपग्रेड करने के लिए एक .zip रिलीज़ पैकेज अपलोड करें। पहले एक सुरक्षा बैकअप लिया जाता है जब तक आप स्पष्ट रूप से इसे छोड़ नहीं देते।',
    'package_label'                    => 'अपडेट पैकेज (.zip)',
    'package_helper'                   => 'समर्थित: कोई भी zip जिसका शीर्ष-स्तर लेआउट LeadHub वितरण से मेल खाता है।',
    'skip_backup_label'                => 'अपडेट से पहले बैकअप छोड़ें',
    'skip_backup_helper'               => 'अनुशंसित नहीं। बंद रखें जब तक आपने अभी मैन्युअल रूप से बैकअप नहीं लिया है।',

    'check_complete_title'             => 'अपडेट जाँच पूर्ण।',
    'check_complete_default_body'      => 'ऊपर वर्तमान/नवीनतम संस्करण बैनर देखें।',
    'check_failed_title'               => 'अपडेट जाँच विफल।',

    'apply_summary_files_written'      => ':count फ़ाइलें लिखी गईं',
    'apply_summary_version'            => ' · अब v:version',
    'apply_summary_backup'             => ' · बैकअप: :backup',
    'apply_success_title'              => 'अपडेट सफलतापूर्वक लागू किया गया।',
    'apply_failed_title'               => 'अपडेट विफल।',

    'action_check'                     => 'अपडेट के लिए जाँचें',
    'action_apply'                     => 'अपलोड किया गया पैकेज लागू करें',
    'action_apply_confirmation'        => 'यह एप्लिकेशन फ़ाइलों को अपलोड किए गए zip की सामग्री से अधिलेखित कर देगा, लंबित माइग्रेशन चलाएगा, और हर कैश साफ़ कर देगा। पहले अपडेट-पूर्व बैकअप लिया जाता है। जारी रखें?',

    'update_history'                   => 'अपडेट इतिहास',

    'installed_version'                => 'इंस्टॉल किया गया संस्करण',
    'update_available'                 => 'अपडेट उपलब्ध',
    'view_changelog'                   => 'चेंजलॉग देखें',
    'on_latest_version'                => 'आप नवीनतम संस्करण पर हैं।',
    'history_empty'                    => 'अभी तक कोई अपडेट लागू नहीं।',
    'col_package'                      => 'पैकेज',
    'col_backup'                       => 'बैकअप',
    'col_result'                       => 'परिणाम',
    'last_checked'                     => 'अंतिम जाँच :time',
    'col_when'                         => 'कब',
    'col_from_to'                      => 'से → तक',

    'badge_failed'                     => 'विफल',
    'badge_files_written'              => ':count फ़ाइलें',
];
