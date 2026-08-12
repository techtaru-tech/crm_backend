<?php

declare(strict_types=1);

return [

    'nav_label'  => 'कस्टम डोमेन',

    'page_title' => 'कस्टम डोमेन',

    'section_description' => 'अपने कस्टम डोमेन को इस प्लेटफ़ॉर्म पर इंगित करें। एक CNAME रिकॉर्ड जोड़ें जो इंगित करे: :host',

    'custom_domain'             => 'कस्टम डोमेन',
    'custom_domain_placeholder' => 'leads.yourcompany.com',
    'custom_domain_helper'      => 'अपना डोमेन https:// के बिना दर्ज करें। फिर एक CNAME रिकॉर्ड जोड़ें: leads.yourcompany.com → :host',

    'reverify_dns' => 'DNS पुनः सत्यापित करें',
    'save_domain'  => 'डोमेन सहेजें',

    'current_domain_label'      => 'वर्तमान डोमेन:',
    'status_verified'           => 'सत्यापित',
    'status_pending'            => 'सत्यापन लंबित',
    'dns_record_lede'           => 'अपने डोमेन में निम्नलिखित DNS रिकॉर्ड जोड़ें:',
    'col_type'                  => 'प्रकार',
    'col_name'                  => 'नाम',
    'col_value'                 => 'मान',
    'dns_txt_hint_html'         => 'या एक TXT रिकॉर्ड जोड़ें: <code class="ds-code">_leadhub-verify.:domain</code> → <code class="ds-code">:token</code>',
    'dns_propagation_hint'      => 'DNS प्रसार में 24 घंटे तक लग सकते हैं। सत्यापन स्वचालित रूप से पृष्ठभूमि में चलता है।',
    'dns_record_type_cname'     => 'CNAME',

];
