<?php

declare(strict_types=1);

return [

    'workspace'                       => 'कार्यक्षेत्र',
    'workspaces'                      => 'कार्यक्षेत्र',

    'reserved_slug_error'             => '":value" आरक्षित है। एक अलग कार्यक्षेत्र स्लग चुनें।',
    'workspace_url_helper'            => 'कार्यक्षेत्र URL: :url — सार्वजनिक लैंडिंग पृष्ठों के लिए भी उपयोग किया जाता है। नाम से ऑटो-फ़िल।',
    'max_seats_helper'                => 'टीम सदस्यों पर हार्ड कैप।',
    'subscription_status_helper'      => 'निलंबन "निलंबित करें" बटन के माध्यम से सेट किया जाता है — यहाँ नहीं। ट्रायल समाप्त और समाप्त सामान्यतया जीवनचक्र क्रॉन द्वारा स्वचालित रूप से सेट किए जाते हैं।',
    'trial_ends_at_label'             => 'ट्रायल समाप्ति',
    'trial_ends_at_helper'            => 'जब मुफ़्त ट्रायल समाप्त होता है। योजना चुनने पर योजना के trial_days से ऑटो-फ़िल।',
    'subscription_ends_at_label'      => 'सब्सक्रिप्शन समाप्ति',
    'subscription_ends_at_helper'     => 'सक्रिय सब्सक्रिप्शन के लिए अगली बिलिंग तिथि, या रद्द/समाप्त के लिए जिस तिथि को पहुँच समाप्त होती है।',

    'status_trial'                    => 'ट्रायल',
    'status_trial_expired'            => 'ट्रायल समाप्त',
    'status_active_paid'              => 'सक्रिय (भुगतान)',
    'status_cancelled'                => 'रद्द',
    'status_expired'                  => 'समाप्त',
    'status_active'                   => 'सक्रिय',
    'status_suspended'                => 'निलंबित',
    'status_unknown'                  => 'अज्ञात',

    'tenant_admin_description'        => 'व्यवस्थापक उपयोगकर्ता जो इस कार्यक्षेत्र का स्वामी होगा और इसका प्रबंधन करेगा।',
    'admin_name'                      => 'व्यवस्थापक का नाम',
    'admin_email'                     => 'व्यवस्थापक ईमेल',
    'admin_password_mode'             => 'पासवर्ड सेटअप',
    'admin_password_mode_email_link'  => 'व्यवस्थापक को सेटअप लिंक ईमेल करें (अनुशंसित)',
    'admin_password_mode_generate'    => 'ऑटो-जनरेट + यहाँ दिखाएँ',
    'admin_password_mode_manual'      => 'अभी पासवर्ड सेट करें',
    'admin_password'                  => 'पासवर्ड',
    'admin_password_helper'           => 'कम से कम 10 वर्ण। व्यवस्थापक को सुरक्षित रूप से संप्रेषित करें।',

    'owner'                           => 'स्वामी',
    'owner_email'                     => 'स्वामी ईमेल',
    'status_column'                   => 'स्थिति',
    'seats'                           => 'सीट',
    'trial_ends'                      => 'ट्रायल समाप्त',
    'sub_ends'                        => 'सब्स समाप्त',

    'filter_suspension'               => 'निलंबन',

    'suspend'                         => 'निलंबित करें',
    'suspend_modal_heading'           => 'कार्यक्षेत्र निलंबित करें',
    'suspend_modal_description'       => '":name" को निलंबित करें? सभी सदस्य अगले अनुरोध पर पहुँच खो देंगे और सब्सक्रिप्शन-आवश्यक पृष्ठ देखेंगे। यह प्रतिवर्ती है।',
    'suspend_reason_label'            => 'कारण (वैकल्पिक, आंतरिक — ऑडिट लॉग के लिए)',
    'suspend_demo_guard'              => 'डेमो: टेनेंट निलंबित नहीं कर सकते (एक वास्तविक अधिसूचना ईमेल भेजेगा)।',
    'suspend_notification_title'      => 'कार्यक्षेत्र ":name" निलंबित',
    'suspend_notification_body_base'  => 'सदस्यों को उनके अगले अनुरोध पर पुनर्निर्देशित किया जाएगा।',
    'suspend_notification_body_owner_notified'  => ' :email पर स्वामी को सूचित किया गया।',
    'suspend_notification_body_owner_failed'    => ' स्वामी ईमेल भेजने में विफल (लॉग देखें)।',
    'suspend_notification_body_no_owner'        => ' टेनेंट का कोई स्वामी नहीं — कोई अधिसूचना नहीं भेजी गई।',

    'reactivate'                      => 'पुनः सक्रिय करें',
    'reactivate_modal_heading'        => 'कार्यक्षेत्र पुनः सक्रिय करें',
    'reactivate_modal_description'    => '":name" को पुनः सक्रिय करें? सदस्य उनके अगले अनुरोध पर तत्काल पहुँच पुनः प्राप्त करते हैं।',
    'reactivate_demo_guard'           => 'डेमो: टेनेंट पुनः सक्रिय नहीं कर सकते।',
    'reactivate_notification_title'   => 'कार्यक्षेत्र ":name" पुनः सक्रिय',

    'impersonate'                     => 'छद्म-प्रवेश',
    'impersonate_modal_heading'       => 'टेनेंट व्यवस्थापक के रूप में छद्म-प्रवेश',
    'impersonate_modal_description'   => 'आप ":tenant_name" कार्यक्षेत्र में ":owner_name" (:owner_email) के रूप में साइन इन होंगे। सभी क्रियाएँ इस उपयोगकर्ता के रूप में की जाएँगी। आप बैनर के माध्यम से कभी भी सुपर एडमिन पर वापस जा सकते हैं।',
    'impersonate_demo_guard'          => 'डेमो: छद्म-प्रवेश अक्षम है।',

    'workspace_created'               => 'कार्यक्षेत्र बनाया गया',
    'workspace_created_password_body' => ":email के लिए पासवर्ड: \n\n  :password\n\nइसे अभी कॉपी करें — यह फिर से नहीं दिखाया जाएगा।",
    'workspace_created_manual_body'   => 'आपके द्वारा चुना गया व्यवस्थापक पासवर्ड सक्रिय है। इसे :email के साथ सुरक्षित रूप से साझा करें।',
    'workspace_created_email_body'    => 'एक सेटअप लिंक :email को ईमेल किया गया है',
    'workspace_created_email_failed_title' => 'कार्यक्षेत्र बनाया गया लेकिन ईमेल विफल',
    'workspace_created_email_failed_body'  => 'कार्यक्षेत्र बनाया गया था, लेकिन सेटअप ईमेल :email को नहीं भेजा जा सका। उपयोगकर्ता पहुँच प्राप्त करने के लिए लॉगिन पृष्ठ पर \'पासवर्ड भूल गए\' लिंक का उपयोग कर सकता है।',
    'workspace_created_existing_user' => 'मौजूदा उपयोगकर्ता :email को व्यवस्थापक के रूप में असाइन किया गया है। कोई सेटअप ईमेल नहीं भेजा गया।',

    'name'                            => 'नाम',
    'slug'                            => 'स्लग',
    'max_seats'                       => 'अधिकतम सीट',
    'plan'                            => 'योजना',
    'subscription_status'             => 'सब्सक्रिप्शन स्थिति',
    'created_at'                      => 'बनाया गया',

    'model_label'                     => 'टेनेंट',
    'plural_model_label'              => 'टेनेंट',

];
