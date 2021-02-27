# Germania KG · TwigServiceProvider

**Pimple Service Provider for Twig templating engine**

[![Packagist](https://img.shields.io/packagist/v/germania-kg/twigserviceprovider.svg?style=flat)](https://packagist.org/packages/germania-kg/twigserviceprovider)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/twigserviceprovider.svg)](https://packagist.org/packages/germania-kg/twigserviceprovider)
[![Build Status](https://img.shields.io/travis/GermaniaKG/TwigServiceProvider.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/TwigServiceProvider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/TwigServiceProvider/build-status/master)


## Installation with Composer

```bash
$ composer require germania-kg/twigserviceprovider
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/twigserviceprovider": "^1.0|^2.0"
}
```

**The v2 release requires PHP 7.3+ and Twig from v2 and up.**

## Setup

Have your **Pimple** dependency container at hand and register the **TwigServiceProvider:** 

```php
<?php
use Germania\TwigServiceProvider\TwigServiceProvider;
use Pimple\Container;

$pimple = new Container;
$pimple->register( new TwigServiceProvider );
```

## Usage

Once you've registered the *TwigServiceProvider*, you can grab and use your **Twig_Environment** like this:

```php
<?php
$twig_environment = $pimple['Twig'];
echo $twig_environment->render('template', [ 'foo' => 'bar']);
```

From v2 of this package, with Twig v2 and up, this is also possible:

```php
$twig_environment = $pimple[ \Twig\Environment::class ];
```

…There are more services, see [Services section](#services)

## Configuration

### The default options

```php
<?php
$options = array(
	// For Twig's Filesystem Loader (string or array)
	'templates' => '/path/to/templates',

	// The most important Twig Environment options
	'debug' => false,
	'cache' => '/path/to/cache',
	'auto_reload' => true,
	'autoescape'  => false,
	'strict_variables' => false	
);
```

You can refine these options by either passing those you like to override to the constructor or extending the `Twig.Config` service at runtime:


### Override on instantiation

```php
<?php
use Germania\TwigServiceProvider\TwigServiceProvider;

$custom_options = [
	'templates' => [ 
		'/another/path/to/templates',
		__DIR__ . '/vendor/foo/bar/templates'
	],
    'strict_variables' => true
];

$pimple->register( new TwigServiceProvider( $custom_options ));
```

### Override at runtime
```php
<?php
$pimple->register( new TwigServiceProvider );

$pimple->extend('Twig.Config', function( $defaults, $pimple) {
	return array_merge( $defaults, [
		'templates'  => $pimple['custom.templates'],
		'strict_variables' => getenv('STRICT_ONLY')
	]);
});
```

## Other Services


### Twig.Options

Per default, the ***Twig_Environment*** instance is built with the “most important” options defined configuration—see [Configuration section.](#configuration). You may add other options, like so:

```php
// @return array
$pimple->extend('Twig.Options', function($options, $pimple) {
	return array_merge($options, [
		'charset' => 'iso-8859-1',
		'optimizations' => 0
	]);
});
```


### Twig.CachePath

```php
// @return string
$pimple->extend('Twig.CachePath', function($old, $pimple) {
	return __DIR__ . '/var/cache';
});
```


### Twig.TemplatePaths


```php
// @return array
$pimple->extend('Twig.TemplatePaths', function($paths, $pimple) {
	return array_merge($paths, [
		'another/one',
		'vendor/name/package/templates'
	]);
});
```



### Twig.Loaders

This service per default contains only the ***Twig_Loader_Filesystem*** instance;
To add one or more others, add them to the `$loaders` array:

```php
// @return array
$pimple->extend('Twig.Loaders', function($loaders, $pimple) {
	return array_merge($loaders, [
			new Twig_Loader_Array( [ ... ] )
	]);
});
```

All loaders in `$loaders` will automatically be chained via Twig's ***Twig_Loader_Chain.***


See [Twig developer docs](https://twig.symfony.com/doc/2.x/api.html#environment-options) for full description.

## Other services

All these services return an (empty) array you may extend with custom data. They all will be added into the *Twig_Environment*.

```php
// @return array
$pimple->extend( 'Twig.Globals', ... );
$pimple->extend( 'Twig.Filters', ... );
$pimple->extend( 'Twig.Tests', ... );
$pimple->extend( 'Twig.Functions', ... );
$pimple->extend( 'Twig.Extensions', ... );
```




## Development

```bash
$ git clone https://github.com/GermaniaKG/TwigServiceProvider.git
$ cd TwigServiceProvider
$ composer install
```

## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bi

```