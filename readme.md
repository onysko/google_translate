# SamsonPHP Google Translate API module

[![Latest Stable Version](https://poser.pugx.org/samsonphp/google_translate/v/stable.svg)](https://packagist.org/packages/samsonphp/google_translate) 
[![Build Status](https://scrutinizer-ci.com/g/samsonphp/google_translate/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/google_translate/badges/build.png?b=master)
[![Code Coverage](https://scrutinizer-ci.com/g/samsonphp/google_translate/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/google_translate/?branch=master)
[![Total Downloads](https://poser.pugx.org/samsonphp/google_translate/downloads.svg)](https://packagist.org/packages/samsonphp/google_translate)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonphp/google_translate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/google_translate/?branch=master)
[![Stories in Ready](https://badge.waffle.io/samsonphp/google_translate.png?label=ready&title=Ready)](https://waffle.io/samsonphp/google_translate)

##Configuration

Before using translate module methods, you must create configuration and enter your Google API Key for using Google Translate API

All you need is create configuration class which is working thanks to [SamsonPHP module/service configuration](https://github.com/samsonphp/config):

```php
class Google_TranslateConfig extends \samson\core\Config
{
    public $apiKey = 'Your_Google_API_Key';
}
```

## Creating translate request

After creating configuration you can make request to Google Translate API. To create simple request you must define source language of your text and target language which you want to get. To identify languages you can use ```source($source)``` and ```target($target)``` methods.

For example you want to translate 'Hello World' to french:

```php
/** @var \samson\google\Translate $trans Get SamsonPHP GoogleTranslate module */
$trans = & m('google_translate');

// Source text
$helloWorld = 'Hello World';

// Translated text
$bonjourLeMonde = $trans->source('en')->target('fr')->trans($helloWorld);
```

## Fixing translation errors

If you have some problems with API Key or you have make some errors in defining source or target locales, you will get error from Google Translate API.
You can check status of your request using method ```lastRequestStatus()```:

```php
/** @var \samson\google\Translate $trans Get SamsonPHP GoogleTranslate module */
$trans = & m('google_translate');

// Source text
$helloWorld = 'Hello World';

// Translated text
$bonjourLeMonde = $trans->source('gb')->target('fr')->trans($helloWorld);
// Will return 'Invalid value'

// Is false, because gb locale is not found in Google language codes.
echo $trans->lastRequestStatus();
```

## Translate array of information using just one request

If you need to translate a lot of strings, the best way is define array of your strings as ```trans($data)``` parameter.
Simple example:

```php
/** @var \samson\google\Translate $trans Get SamsonPHP GoogleTranslate module */
$trans = & m('google_translate');

// Source strings
$myStrings = array('white dog', 'cat', 'rabbit', 'squirrel');

// Translate it
$myTranslatedStrings = $trans->source('en')->target('fr')->trans($myStrings);

// Look at the response
print_r($myTranslatedStrings);
```

If your Google API Key s active, you will get this data:

```
Array
(
    [white dog] => chien blanc
    [cat] => chat
    [rabbit] => lapin
    [squirrel] => écureuil
)
```

This module is working using [Google Translate API](https://cloud.google.com/translate/)
