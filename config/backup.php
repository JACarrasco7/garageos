<?php

return [

    'backup' => [
        'destination' => [
            'filename_prefix' => 'garageos-backup-',
            'disks' => ['local'],
        ],
        'source' => [
            'files' => [
                'include' => [
                    'storage/app',
                ],
                'exclude' => [
                    'storage/app/public/backups',
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => true,
                'relative_path' => '',
            ],
            'databases' => ['mysql'],
        ],
    ],

    'monitor' => [
        'mail' => [
            'to' => 'admin@garageos.app',
            'from' => 'noreply@garageos.app',
        ],
    ],

];
