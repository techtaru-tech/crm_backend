<?php

declare(strict_types=1);

return [
    'title'                              => 'ईमेल ब्रांडिंग',
    'navigation_label'                   => 'ईमेल ब्रांडिंग',

    'header_section_description'         => 'हर जावक ईमेल के शीर्ष पर रंगीन बैंड। टेनेंट-विशिष्ट प्राथमिक-रंग ओवरराइड तब भी जीतते हैं जब कोई टेनेंट ब्रांडिंग में अपना खुद का सेट करता है — ये मान स्क्रिप्ट-व्यापी डिफ़ॉल्ट हैं।',
    'header_style_label'                 => 'शैली',
    'header_style_solid'                 => 'ठोस रंग',
    'header_style_gradient'              => 'रैखिक ग्रेडिएंट',
    'header_color_primary_gradient'      => 'ग्रेडिएंट प्रारंभ रंग',
    'header_color_primary_solid'         => 'पृष्ठभूमि रंग',
    'header_color_secondary_label'       => 'ग्रेडिएंट समाप्ति रंग',
    'header_gradient_angle_label'        => 'ग्रेडिएंट कोण (डिग्री)',
    'header_gradient_angle_helper'       => '0 = नीचे से ऊपर · 90 = बाएँ से दाएँ · 135 = विकर्ण (डिफ़ॉल्ट) · 180 = ऊपर से नीचे।',

    'footer_section_description'         => 'हर ईमेल के नीचे छोटा बैंड। डिज़ाइन से सादा रंग — यहाँ ग्रेडिएंट ऊपर के CTA ब्लॉक से प्रतिस्पर्धा करता है।',
    'footer_color_label'                 => 'पृष्ठभूमि रंग',
    'footer_text_color_label'            => 'पाठ रंग',
    'footer_text_color_helper'           => 'पठनीयता के लिए ऊपर की पृष्ठभूमि के साथ कंट्रास्ट होना चाहिए।',

    'save_failed_title'                  => 'ईमेल ब्रांडिंग सहेजी नहीं जा सकी',
    'save_failed_body'                   => 'विवरण सर्वर लॉग में हैं। सबसे आम कारण: सेटिंग्स माइग्रेशन अभी तक नहीं चलाया गया — सर्वर पर `php artisan migrate --force` चलाएँ।',
    'saved_title'                        => 'ईमेल ब्रांडिंग सहेजी गई',
    'saved_body'                         => 'नए रंग अब से भेजे गए हर ईमेल पर लागू होते हैं।',
    'action_save'                        => 'सहेजें',

    'preview_title'                      => 'पूर्वावलोकन',
    'preview_subtitle'                   => 'जावक ईमेल लेआउट का प्रतिबिंब — जैसे ही आप पिकर बदलते हैं अपडेट होता है।',
    'preview_sample_greeting'            => 'नमस्ते जेन,',
    'preview_sample_body'                => 'नमूना ईमेल मुख्य पाठ। वास्तविक संदेश इसे अपनी सामग्री से बदल देते हैं।',
    'preview_footer_reason'              => 'आपको यह ईमेल इसलिए प्राप्त हुआ क्योंकि आप :app के उपयोगकर्ता हैं।',
];
