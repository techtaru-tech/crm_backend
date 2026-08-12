<?php

declare(strict_types=1);

return [
    'unable_to_open_zip_for_writing'    => 'Unable to open :path for writing.',
    'backup_not_found'                  => 'Backup not found.',
    'unable_to_open_archive'            => 'Unable to open backup archive.',
    'backup_file_not_found'             => 'Backup file not found: :path',
    'zip_open_error_code'               => 'ZipArchive::open returned error code :code',
    'missing_database_sql'              => 'Archive is missing database.sql',
    'database_sql_empty'                => 'database.sql is empty (0 bytes)',
    'cannot_open_sql_stream'            => 'Could not open a stream over database.sql inside the archive',
    'no_recognizable_sql_statements'    => 'database.sql contains no recognizable SQL statements',
    'only_mysql_supported'              => 'Only MySQL/MariaDB backups are supported.',
    'unable_to_write_dump'              => 'Unable to write :path',
];
