<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Column Types
    |--------------------------------------------------------------------------
    |
    | Available Column Types :
    |
    |	'bigInteger', 'binary', 'boolean',
    |   'char', 'date', 'dateTime', 'dateTimeTz',
    |   'decimal', 'double', 'float', 'geometry',
    |   'geometryCollection', 'integer', 'ipAddress',
    |   'json', 'jsonb', 'lineString', 'longText',
    |   'macAddress', 'mediumInteger', 'mediumText',
    |   'multiLineString', 'multiPoint', 'multiPolygon',
    |   'point', 'polygon', 'smallInteger', 'string',
    |   'text', 'time', 'timeTz', 'timestamp', 'timestampTz',
    |   'tinyInteger', 'unsignedBigInteger', 'unsignedInteger',
    |   'unsignedMediumInteger','unsignedSmallInteger',
    |   'unsignedTinyInteger', 'uuid', 'year',
    */

    'fieldTypes' => [
        'text', 'string', 'json'
    ],

    /*
    |--------------------------------------------------------------------------
    | Html Element Types
    |--------------------------------------------------------------------------
    |
    | Available Element Types :
    |
    |   'text', 'select', 'number', 'textarea',
    |   'integer', 'date', 'time', 'dateTime',
    |
    */

    'elementTypes' => [
        'text', 'select'
    ],

    /*
    |--------------------------------------------------------------------------
    | Select Sources
    |--------------------------------------------------------------------------
    |
    */

    'selectSources' => [
        'database',
        \Eav\Attribute\Source\Boolean::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table prefix for information_schema table
    |--------------------------------------------------------------------------
    |
    */

    'information_schema_prefix' => '',
];
