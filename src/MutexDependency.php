<?php

namespace pahanini\neatcache;

use yii\base\InvalidConfigException;
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
     * Returns a value indicating whether the dependency has changed.
     * @deprecated Use [[isChanged()]] instead.
     */
    public function getHasChanged($cache)
    {
        return $this->isChanged($cache);
    }

    /**
     * @param \yii\caching\Cache $cache
     * @return bool
     * @throws InvalidConfigException
     */
    public function isChanged($cache)
    {
        if (!$this->tag) {
            throw new InvalidConfigException("Invalid tag attribute of mutex dependency");
        }
        // We do not use $cache->buildKey method because travis-ci fails in this case with strange error
        // yii\base\UnknownMethodException: Calling unknown method: yii\caching\MemCache::buildKey()
        return $this->getMutex()->acquire(md5(json_encode($this->tag)));
    }
}