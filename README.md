# The Filesystem Package [![Build Status](https://travis-ci.org/joomla-framework/filesystem.png?branch=master)](https://travis-ci.org/joomla-framework/filesystem)

## Changes From 1.x

### Patcher

In 1.x, the second parameter of the `add` and `addFile` methods was optional.  In 2.0, this parameter is required.  This parameter requires the
root path of the source which you are patching.

## Installation via Composer

Add `"joomla/filesystem": "2.0.*@dev"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/filesystem": "2.0.*@dev"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/filesystem "2.0.*@dev"
```
