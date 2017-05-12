<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Harvester Configuration
    |--------------------------------------------------------------------------
    |
    | These settings can be used to set extended data types used within the
    | database schema for AssetType, DateType, LocationType, TermType and
    | TextType. These settings allow the havester to be more flexable.
    |
    */
    'since' => 1, // number of days
    'filetypes' => [
        'images' => ['jpg', 'jpeg', 'png', 'tif', 'gif', 'jp2', 'bmp'],
        'audio' => ['ogg', 'mp3', 'wav'],
        'video' => ['ogg', 'mp4', 'webm'],
        'documents' => [
            'svg', 'txt', 'ppt',
            'pptx', 'doc','docx',
            'pdf','xls', 'xlsx',
            'html', 'htm','zip',
            'rar', 'xps','psd', 'ai'
        ]
    ],
];
