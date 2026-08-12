<?php

declare(strict_types=1);

return [
    'unable_to_open_zip_for_writing'    => 'No se pudo abrir :path para escritura.',
    'backup_not_found'                  => 'Copia de seguridad no encontrada.',
    'unable_to_open_archive'            => 'No se pudo abrir el archivo de copia de seguridad.',
    'backup_file_not_found'             => 'Archivo de copia de seguridad no encontrado: :path',
    'zip_open_error_code'               => 'ZipArchive::open devolvió el código de error :code',
    'missing_database_sql'              => 'Al archivo le falta database.sql',
    'database_sql_empty'                => 'database.sql está vacío (0 bytes)',
    'cannot_open_sql_stream'            => 'No se pudo abrir un flujo sobre database.sql dentro del archivo',
    'no_recognizable_sql_statements'    => 'database.sql no contiene instrucciones SQL reconocibles',
    'only_mysql_supported'              => 'Solo se admiten copias de seguridad de MySQL/MariaDB.',
    'unable_to_write_dump'              => 'No se pudo escribir :path',
];
