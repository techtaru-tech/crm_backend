<?php

declare(strict_types=1);

return [
    'unable_to_open_zip_for_writing'    => 'लिखने के लिए :path नहीं खोला जा सका।',
    'backup_not_found'                  => 'बैकअप नहीं मिला।',
    'unable_to_open_archive'            => 'बैकअप संग्रह नहीं खोला जा सका।',
    'backup_file_not_found'             => 'बैकअप फ़ाइल नहीं मिली: :path',
    'zip_open_error_code'               => 'ZipArchive::open ने त्रुटि कोड :code लौटाया',
    'missing_database_sql'              => 'संग्रह में database.sql गायब है',
    'database_sql_empty'                => 'database.sql खाली है (0 बाइट्स)',
    'cannot_open_sql_stream'            => 'संग्रह के अंदर database.sql पर स्ट्रीम नहीं खोली जा सकी',
    'no_recognizable_sql_statements'    => 'database.sql में कोई पहचानने योग्य SQL कथन नहीं हैं',
    'only_mysql_supported'              => 'केवल MySQL/MariaDB बैकअप समर्थित हैं।',
    'unable_to_write_dump'              => ':path नहीं लिखा जा सका',
];
