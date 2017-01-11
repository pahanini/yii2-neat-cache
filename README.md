#Yii2 Neat cache

[![Build Status](https://travis-ci.org/pahanini/yii2-neat-cache.svg?branch=master)](https://travis-ci.org/pahanini/yii2-neat-cache)
[![Latest Stable Version](https://poser.pugx.org/pahanini/yii2-neat-cache/v/stable)](https://packagist.org/packages/pahanini/yii2-neat-cache) 
[![Total Downloads](https://poser.pugx.org/pahanini/yii2-neat-cache/downloads)](https://packagist.org/packages/pahanini/yii2-neat-cache) 
[![Latest Unstable Version](https://poser.pugx.org/pahanini/yii2-neat-cache/v/unstable)](https://packagist.org/packages/pahanini/yii2-neat-cache) 
[![License](https://poser.pugx.org/pahanini/yii2-neat-cache/license)](https://packagist.org/packages/pahanini/yii2-neat-cache)

## About

Improved Yii2 [PageCache](http://www.yiiframework.com/doc-2.0/yii-filters-pagecache.html)
filter to prevent dog-pile effect in yii2 applications. Please see 
[http://www.sobstel.org/blog/preventing-dogpile-effect/](http://www.sobstel.org/blog/preventing-dogpile-effect/)
for more information about dog-pile effect.

## Install

- Add `"pahanini/yii2-neat-cache": "*"` to required section of your composer.json  


## Usage

There are two main components MutexDependency and PageCache. Both require mutex component of your application.

``` php

'components' => [
	'mutex' => [
		'class' => 'tests\components\MysqlMutex',
	]
]
```

### MutexDependency 

For example you need prevent simultaneous calls of heavy function. Even if the function result is cached
at the moment cache expired there is a chance that two apache workers will call this function twice or 
even worse. 

First step to prevent this behavior is to prepare chained dependency with dependOnAll property set to false. 
Use first sub dependency to manage data expiration. Second dependency is MutexDependency.

```php
$dependency = Yii::createObject([
	'class' => '\yii\caching\ChainedDependency',
	'dependOnAll' => false,
	'dependencies' => [
		Yii::createObject([
			'class' => '\yii\caching\ExpressionDependency',
			'expression' => 'Helper::isTimeToUpdate()',
		]),
		Yii::createObject([
			'class' => '\pahanini\neatcache\MutexDependency',
			'tag' => 'HeavyFunction',
		]),
	]
]);

```

If first dependency has changed for the first time then second one tries to acquire mutex lock and in case 
of success is considered to be changed and make cache invalid (both dependencies were changed).

Second step is to use created dependency with never expired duration value to set cache data
 
```php
	if (!$data = Yii::$app->cache->get('heavyDataId')) {
		Yii::$app->cache->set('heavyDataId', heavyFunctionCall(), 0, $dependency);		
	}
```

### PageCache filter


Replace native yii2 PageCache filter neat one and make cache never expired. Everlasting cache 
allows neat PageCache filter to use old data from expired cache to prevent dog pile effect. To 
make page expired you should use any of cache dependencies. 

``` php

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

```

Neat PageCache automatically creates chained dependency based on specified one to prevent dog pile effect
during page caching.
 
### 

## Testing

Copy tests config `main-local.php.sample` to  `main-local.php` and run

``` bash
$ phpunit
```

## Security

If you discover any security related issues, please email pahanini@gmail.com instead of using the issue tracker.

## License

The BSD License.
