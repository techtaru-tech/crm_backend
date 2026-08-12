<?php

declare(strict_types=1);

return [

    // --- Navegación ----------------------------------------------------
    'nav_label'  => 'Almacenamiento',

    // --- Título de la página -------------------------------------------
    'page_title' => 'Configuración de almacenamiento',

    // --- Disco de almacenamiento ---------------------------------------
    'storage_disk' => 'Disco de almacenamiento',
    'disk_local'   => 'Local (sistema de archivos del servidor)',
    'disk_s3'      => 'Compatible con S3 (AWS S3, DigitalOcean Spaces, MinIO, etc.)',

    // --- Sección S3 ----------------------------------------------------
    's3_section_description' => 'Requerido cuando el disco de almacenamiento está configurado como Compatible con S3.',
    'endpoint_url'           => 'URL del endpoint',
    'endpoint_placeholder'   => 'https://nyc3.digitaloceanspaces.com',
    'endpoint_helper'        => 'Déjelo en blanco para AWS S3 estándar.',
    'bucket_name'            => 'Nombre del bucket',
    'region'                 => 'Región',
    'access_key_id'          => 'ID de clave de acceso',
    'secret_access_key'      => 'Clave de acceso secreta',
    'secret_helper'          => 'Déjelo en blanco para conservar el secreto almacenado.',

    // --- Acciones de cabecera ------------------------------------------
    'test_connection' => 'Probar conexión',
    'save_settings'   => 'Guardar configuración',

];
