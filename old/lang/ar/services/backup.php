<?php

declare(strict_types=1);

return [
    'unable_to_open_zip_for_writing'    => 'تعذّر فتح :path للكتابة.',
    'backup_not_found'                  => 'لم يتم العثور على النسخة الاحتياطية.',
    'unable_to_open_archive'            => 'تعذّر فتح أرشيف النسخ الاحتياطي.',
    'backup_file_not_found'             => 'لم يتم العثور على ملف النسخة الاحتياطية: :path',
    'zip_open_error_code'               => 'أعاد ZipArchive::open رمز الخطأ :code',
    'missing_database_sql'              => 'الأرشيف يفتقد ملف database.sql',
    'database_sql_empty'                => 'ملف database.sql فارغ (0 بايت)',
    'cannot_open_sql_stream'            => 'تعذّر فتح دفق على ملف database.sql داخل الأرشيف',
    'no_recognizable_sql_statements'    => 'لا يحتوي database.sql على عبارات SQL معروفة',
    'only_mysql_supported'              => 'تُدعم النسخ الاحتياطية لـ MySQL/MariaDB فقط.',
    'unable_to_write_dump'              => 'تعذّرت الكتابة في :path',
];
