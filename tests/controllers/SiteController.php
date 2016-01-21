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
        if (NeatCacheTest::$mutexState === true) {
            // @todo create better way to wait
            sleep(1);
        }
        return "body" . NeatCacheTest::$body;
    }
}
