<?php

return [
    // The document store config file will be dependent on what document store you use
    // Below is an example config for Elasticsearch
    // TODO: Move Elasticsearch into separate repo with its own document store config

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Default scroll size
    |--------------------------------------------------------------------------
    |
    | When using scroll what is the default number of records to be fetched
    | at a time
    |
    */

    'size' => 10000,

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch indices Configuration
    |--------------------------------------------------------------------------
    |
    | List of all indices to be created
    |
    */

    'indices' => [
        'example',
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Settings Configuration
    |--------------------------------------------------------------------------
    |
    | Settings used to create indices
    | TODO: Make setting indices specific
    |
    */

    'settings' => [
        'max_result_window' => 100000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Mapping Configuration
    |--------------------------------------------------------------------------
    |
    | These mappings tell Elasticsearch how to store each field of data.
    | TODO: Make mappings indices specific
    |
    */

    'mappings' => [],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Aggregation Fields
    |--------------------------------------------------------------------------
    |
    | This sets the aggregations to be used for filtering the data coming
    | out of ES.
    |
    */
    'aggs' => [
        'example' => [
            'terms' => [
                'size' => 100,
                'field' => 'example.keyword',
                'order' => ['_term' => 'asc'],
                'exclude' => '',
            ]
        ]
    ]
];
