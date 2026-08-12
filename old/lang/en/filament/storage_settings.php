<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| StorageSettingsPage strings — accessed via __('filament/storage_settings.X')
|--------------------------------------------------------------------------
|
| Tenant-side Filament Page: file storage driver (local vs. S3) and S3
| credentials for the current workspace.
|
*/

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Storage',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Storage Settings',

    // --- Storage disk --------------------------------------------------
    'storage_disk' => 'Storage Disk',
    'disk_local'   => 'Local (server filesystem)',
    'disk_s3'      => 'S3-Compatible (AWS S3, DigitalOcean Spaces, MinIO, etc.)',

    // --- S3 section ----------------------------------------------------
    's3_section_description' => 'Required when storage disk is set to S3-Compatible.',
    'endpoint_url'           => 'Endpoint URL',
    'endpoint_placeholder'   => 'https://nyc3.digitaloceanspaces.com',
    'endpoint_helper'        => 'Leave blank for standard AWS S3.',
    'bucket_name'            => 'Bucket Name',
    'region'                 => 'Region',
    'access_key_id'          => 'Access Key ID',
    'secret_access_key'      => 'Secret Access Key',
    'secret_helper'          => 'Leave blank to keep the stored secret.',

    // --- Header actions ------------------------------------------------
    'test_connection' => 'Test Connection',
    'save_settings'   => 'Save Settings',

];
