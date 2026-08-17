<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    // Public media (member photos, news images, etc.)
    'media_disk' => env('MEDIA_DISK', 'public'),

    // Fan club protected content (behind paywall)
    'fanclub_disk' => env('FANCLUB_DISK', 'public'),

    // Compliance documents (signed contracts, IDs, guardian consent) - private by default
    'documents_disk' => env('DOCUMENTS_DISK', 'local'),

    'media_fallback_disk' => env('MEDIA_FALLBACK_DISK'),
    'media_mirror' => env('MEDIA_MIRROR', false),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Cloudflare R2 - Public Media (CDN-enabled)
        'r2-public' => [
            'driver'                  => 's3',
            'key'                     => env('R2_PUBLIC_ACCESS_KEY_ID'),
            'secret'                  => env('R2_PUBLIC_SECRET_ACCESS_KEY'),
            'region'                  => 'auto',
            'bucket'                  => env('R2_PUBLIC_BUCKET', 'klp48-media-public'),
            'url'                     => env('R2_PUBLIC_URL'), // Custom domain like https://media.klp48.com
            'endpoint'                => env('R2_PUBLIC_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw'                   => false,
            'report'                  => false,
        ],

        // Cloudflare R2 - Fan Club Protected Content
        'r2-fanclub' => [
            'driver'                  => 's3',
            'key'                     => env('R2_FANCLUB_ACCESS_KEY_ID'),
            'secret'                  => env('R2_FANCLUB_SECRET_ACCESS_KEY'),
            'region'                  => 'auto',
            'bucket'                  => env('R2_FANCLUB_BUCKET', 'klp48-media-fanclub'),
            'url'                     => null, // No public URL - access controlled via signed URLs
            'endpoint'                => env('R2_FANCLUB_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw'                   => false,
            'report'                  => false,
        ],

        // Cloudflare R2 - Compliance Documents (private, no CDN)
        'r2-documents' => [
            'driver'                  => 's3',
            'key'                     => env('R2_DOCUMENTS_ACCESS_KEY_ID'),
            'secret'                  => env('R2_DOCUMENTS_SECRET_ACCESS_KEY'),
            'region'                  => 'auto',
            'bucket'                  => env('R2_DOCUMENTS_BUCKET', 'klp48-compliance-documents'),
            'url'                     => null, // No public URL - access controlled via signed URLs
            'endpoint'                => env('R2_DOCUMENTS_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw'                   => false,
            'report'                  => false,
        ],

        // Legacy R2 config (for backward compatibility)
        'r2' => [
            'driver'                  => 's3',
            'key'                     => env('R2_ACCESS_KEY_ID', env('R2_PUBLIC_ACCESS_KEY_ID')),
            'secret'                  => env('R2_SECRET_ACCESS_KEY', env('R2_PUBLIC_SECRET_ACCESS_KEY')),
            'region'                  => 'auto',
            'bucket'                  => env('R2_BUCKET', env('R2_PUBLIC_BUCKET')),
            'url'                     => env('R2_PUBLIC_URL'),
            'endpoint'                => env('R2_ENDPOINT', env('R2_PUBLIC_ENDPOINT')),
            'use_path_style_endpoint' => true,
            'throw'                   => false,
            'report'                  => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
