<?php

namespace pahanini\neatcache;

use Yii;

class PageCache extends \yii\filters\PageCache
{
    /**
     * Mutex dependency
     *
     * @var array
     */
    public $mutexDependency = [
        'class' => '\pahanini\neatcache\MutexDependency',
    ];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        if (!$this->enabled) {
            return true;
        }

        if (is_array($this->dependency)) {
            $this->dependency = Yii::createObject($this->dependency);
        }

        if ($this->dependency) {
            $oldDependency = $this->dependency;
            $tag = [$this->varyByRoute ? $action->getUniqueId() : __CLASS__];
            if (is_array($this->variations)) {
                foreach ($this->variations as $factor) {
                    $tag[] = $factor;
                }
            }

            $this->mutexDependency['tag'] = $tag;

            $this->dependency = [
                'class' => '\yii\caching\ChainedDependency',
                'dependOnAll' => false,
                'dependencies' => [
                    $oldDependency,
                    Yii::createObject($this->mutexDependency),
                ]
            ];
        }

        return parent::beforeAction($action);
    }
}