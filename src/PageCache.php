<?php

namespace pahanini\neatcache;

use Yii;

class PageCache extends \yii\filters\PageCache
{
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
            $this->dependency = [
                'class' => '\yii\caching\ChainedDependency',
                'dependOnAll' => false,
                'dependencies' => [
                    $oldDependency,
                    Yii::createObject([
                        'class' => '\pahanini\neatcache\MutexDependency',
                        'tag' => $tag,
                    ]),
                ]
            ];
        }

        return parent::beforeAction($action);
    }
}