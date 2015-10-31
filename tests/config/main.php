<?php

return [
    'id' => 'unit',
    'basePath' => dirname(__DIR__),
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
        ],
        'mutex' => [
            'class' => 'tests\components\TestMutex',
            'mutexPath' => '@tests/runtime'
        ]
    ]
];
