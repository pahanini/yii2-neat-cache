<?php

namespace pahanini\neatcache;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\mutex\Mutex;
use \yii\caching\Dependency;

/**
 * Class MutexDependency
 * @package pahanini\neatcache
 */
class MutexDependency extends Dependency
{
    /**
     * @var Mutex|string the mutex object or the ID of the mutex application component.
     * @see enableSchemaCache
     */
    public $mutexID = 'mutex';

    /**
     * @var string:array
     */
    public $tag;

    /**
     * @return \yii\mutex\Mutex
     * @throws \yii\base\InvalidConfigException
     */
    protected function getMutex()
    {
        return Instance::ensure($this->mutexID, Mutex::className());
    }

    /**
     * @param \yii\caching\Cache $cache
     * @return int
     */
    public function generateDependencyData($cache)
    {
        return 1;
    }

    /**
     * @param \yii\caching\Cache $cache
     * @return bool
     * @throws InvalidConfigException
     */
    public function getHasChanged($cache)
    {
        if (!$this->tag) {
            throw new InvalidConfigException("Invalid tag attribute of mutex dependency");
        }
        if (!$cache instanceof Cache) {
            throw new InvalidParamException("Unexpected ". get_class($cache) . " param type");
        }
        return $this->getMutex()->acquire($cache->buildKey($this->tag));
    }
}