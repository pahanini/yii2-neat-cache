<?php

namespace tests;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class NeatCacheTest extends \PHPUnit_Framework_TestCase
{
    public static $tag = 0;
    public static $body = 0;
    public static $timeout = 0;

    /**
     * @return \yii\web\Application
     */
    public function getApp()
    {
        $config = ArrayHelper::merge(
            require(__DIR__ . '/config/main.php'),
            require(__DIR__ . '/config/main-local.php')
        );
        return new \yii\web\Application($config);
    }

    public function testMain()
    {
        // First start
        ob_start();
        $app = $this->getApp();
        $app->cache->flush();
        $app->run();
        $result = ob_get_clean();
        $this->assertEquals('body0', $result, "First run, put data into cache");

        // Make sure cache works
        ob_start();
        self::$body = 1;
        $app = $this->getApp();
        $app->run();
        $result = ob_get_clean();
        $app->mutex->releaseAll();
        $this->assertEquals('body0', $result, "Second run, get data from cache");

        // Make sure dependency works
        ob_start();
        self::$tag = 1;
        $app = $this->getApp();
        $app->run();
        $app->mutex->releaseAll();
        $result = ob_get_clean();
        $this->assertEquals('body1', $result);

        $pid = pcntl_fork();
        $this->assertNotEquals(-1, $pid, "Can not fork process");

        self::$tag = 2;
        self::$body = 2;

        if ($pid) {

            // Parent starts after child and should get old data from cache, because child
            // is regenerating data right now
            usleep(50);
            ob_start();
            $app = $this->getApp();
            $app->run();
            $result = ob_get_clean();
            $this->assertEquals('body1', $result, "Both processes have started cache rebuild ;-(");

            // Wait for child ends and now parent should get new data
            pcntl_wait ($status);
            ob_start();
            $app = $this->getApp();
            $app->run();
            $result = ob_get_clean();
            $this->assertEquals('body2', $result);


        } else {

            // Child starts immediately after forking and generates new data with 100 ms delay
            self::$timeout = 100;
            $app->run();
        }
    }

}
