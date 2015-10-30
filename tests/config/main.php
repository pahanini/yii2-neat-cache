<?php

return [
    'id' => 'unit',
    'basePath' => dirname(__DIR__),
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex',
            'mutexPath' => '@tests/runtime'
        ]
    ]
];
