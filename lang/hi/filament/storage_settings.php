<?php

declare(strict_types=1);

return [

    'nav_label'  => 'भंडारण',

    'page_title' => 'भंडारण सेटिंग्स',

    'storage_disk' => 'भंडारण डिस्क',
    'disk_local'   => 'स्थानीय (सर्वर फ़ाइलसिस्टम)',
    'disk_s3'      => 'S3-संगत (AWS S3, DigitalOcean Spaces, MinIO, आदि)',

    's3_section_description' => 'जब भंडारण डिस्क S3-संगत पर सेट हो तो आवश्यक।',
    'endpoint_url'           => 'एंडपॉइंट URL',
    'endpoint_placeholder'   => 'https://nyc3.digitaloceanspaces.com',
    'endpoint_helper'        => 'मानक AWS S3 के लिए खाली छोड़ दें।',
    'bucket_name'            => 'बकेट नाम',
    'region'                 => 'क्षेत्र',
    'access_key_id'          => 'एक्सेस की ID',
    'secret_access_key'      => 'सीक्रेट एक्सेस की',
    'secret_helper'          => 'संग्रहीत सीक्रेट को रखने के लिए खाली छोड़ दें।',

    'test_connection' => 'कनेक्शन परीक्षण करें',
    'save_settings'   => 'सेटिंग्स सहेजें',

];
