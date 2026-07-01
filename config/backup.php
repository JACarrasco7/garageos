<?php

return [

    'backup' => [
        'destination' => [
            'filename_prefix' => 'garageos-backup-',
            'disks' => ['local'],
        ],
        'source' => [
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