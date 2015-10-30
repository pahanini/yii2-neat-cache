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
     * @var
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
     * @return bool
     * @throws InvalidConfigException
     */
    public function getHasChanged()
    {
        if (!$this->tag || !is_string($this->tag)) {
            throw new InvalidConfigException("Invalid tag attribute of mutex dependency");
        }
        return $this->getMutex()->acquire($this->tag);
    }
}