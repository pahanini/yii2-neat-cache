<?php

namespace tests\components;

class TestMutex extends \yii\mutex\FileMutex
{
    private $_names = [];

    protected function acquireLock($name, $timeout = 0)
    {
        $result = parent::acquireLock($name, $timeout);
        if ($result) {
            $this->_names[] = $name;
        }
        return $result;
    }

    public function releaseAll()
    {
        foreach ($this->_names as $name) {
            $this->release($name);
        }
        $this->_names = [];
    }

    public function isAcquired($name)
    {
        return isset($this->_names[$name]);
    }
}