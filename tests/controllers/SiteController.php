<?php

namespace app\controllers;

use yii\web\Controller;
use tests\NeatCacheTest;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'pageCache' => [
                'class' => '\pahanini\neatcache\PageCache',
                'only' => ['index'],
                'duration' => 0,
                'dependency' => [
                    'class' => 'yii\caching\ExpressionDependency',
                    'expression' => '\tests\NeatCacheTest::$tag',
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (NeatCacheTest::$timeout) {
            usleep(NeatCacheTest::$timeout);
        }
        return "body" . NeatCacheTest::$body;
    }
}