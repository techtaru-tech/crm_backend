<?php

declare(strict_types=1);

return [

    'nav_label'                   => 'कूपन',

    'code_helper'                 => 'ग्राहक चेकआउट पर यह टाइप करते हैं। केस-असंवेदनशील — अपरकेस में संग्रहीत।',
    'description_helper'          => 'आंतरिक नोट — यह कोड किस लिए है, कौन सा अभियान, अपेक्षित मोचन संख्या।',

    'discount_type_helper'        => 'प्रतिशत एक % घटाता है। निश्चित किसी विशिष्ट मुद्रा में राशि घटाता है। ट्रायल विस्तार टेनेंट के ट्रायल में दिन जोड़ता है — कोई पैसे की छूट नहीं।',
    'discount_value_suffix_days'  => 'दिन',
    'discount_value_helper_percent' => '0–100। 100 का अर्थ है "छूट विंडो के लिए मुफ़्त"।',
    'discount_value_helper_fixed' => 'पूर्ण-मुद्रा राशि, जैसे $20 के लिए 20।',
    'discount_value_helper_trial' => 'मोचन पर टेनेंट के trial_ends_at में जोड़े गए दिन।',
    'currency_helper'             => 'किसी भी मुद्रा पर लागू करने के लिए खाली छोड़ें।',

    'max_total_uses'              => 'अधिकतम कुल उपयोग',
    'max_total_uses_placeholder'  => 'असीमित',
    'max_total_uses_helper'       => 'सभी टेनेंट्स में कुल मोचन। असीमित के लिए खाली छोड़ें।',
    'max_per_tenant'              => 'प्रति टेनेंट अधिकतम',
    'max_per_tenant_helper'       => 'एक टेनेंट कितनी बार इस कोड को रिडीम कर सकता है। 1 = केवल पहली बार।',
    'applies_to_plans_placeholder'=> 'सभी योजनाएँ',
    'applies_to_plans_helper'     => 'हर योजना पर लागू करने के लिए खाली छोड़ें।',

    'starts_at_placeholder'       => 'अभी',
    'starts_at_helper'            => 'खाली = तत्काल सक्रिय।',
    'ends_at_placeholder'         => 'कभी समाप्त नहीं होगा',
    'ends_at_helper'              => 'खाली = कोई समाप्ति नहीं।',
    'is_active_helper'            => 'निष्क्रिय कोड कभी सत्यापित नहीं होते, यहाँ तक कि तिथि विंडो के भीतर भी।',

    'column_type'                 => 'प्रकार',
    'column_value'                => 'मान',
    'column_uses'                 => 'उपयोग',
    'column_status'               => 'स्थिति',
    'column_ends_at_placeholder'  => 'कभी नहीं',
    'trial_days_suffix'           => 'ट्रायल दिन',

    'filter_label_discount_type'  => 'प्रकार',
    'filter_active'               => 'सक्रिय',
    'filter_active_yes'           => 'हाँ',
    'filter_active_no'            => 'नहीं',

    'code'                        => 'कोड',
    'description'                 => 'विवरण',
    'discount_type'               => 'छूट प्रकार',
    'discount_value'              => 'छूट मान',
    'currency'                    => 'मुद्रा',
    'applies_to_plans'            => 'योजनाओं पर लागू',
    'starts_at'                   => 'प्रारंभ',
    'ends_at'                     => 'समाप्ति',
    'is_active'                   => 'सक्रिय',
    'created_at'                  => 'बनाया गया',

    'model_label'                 => 'कूपन',
    'plural_model_label'          => 'कूपन',

    'status_active'               => 'सक्रिय',
    'status_scheduled'            => 'निर्धारित',
    'status_expired'              => 'समाप्त',
    'status_exhausted'            => 'समाप्त',
    'status_inactive'             => 'निष्क्रिय',

];
