<?php

return [
    'id' => 'unit',
    'basePath' => dirname(__DIR__),
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mutex' => [
            'class' => 'tests\components\TestMutex',
            'mutexPath' => '@tests/runtime'
        ]
    ]
];
