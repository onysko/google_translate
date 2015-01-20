<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 20.01.2015
 * Time: 11:49
 */

namespace samson\google;

use samson\core\CompressableService;
use samsonphp\event\Event;

/**
 * Translation using Google Translate API
 * Class Translate
 * @package samson\google
 */
class Translate extends CompressableService
{
    /** @var string Module identifier */
    protected $id = 'google_translate';

    /** @var string Url string for translation request */
    protected $get;

    /** @var string Google API Key */
    public $apiKey;

    /** @var string Source language for translations */
    public $source;

    /** @var string Target language for translations */
    public $target;

    /**
     * Module initialization
     */
    public function init(array $params = array())
    {
        // If configuration for API Key is not set
        if (!isset($this->apiKey)) {
            // Signal error
            Event::fire(
                'error',
                array(
                    $this,
                    'Cannot initialize GoogleTranslate module - Google API Key does not exists'
                )
            );
        } else {
            // Create default get url
            $this->get = 'https://www.googleapis.com/language/translate/v2?key='.$this->apiKey;
        }

        // Call parent initialization
        return parent::init($params);
    }

    /**
     * Set source language
     * @param $source string Source language for translations
     * @return $this
     */
    public function source($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Set target language
     * @param $target string Target language for translations
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param $text string Text for translation
     * @return string Translated text
     */
    public function trans($text)
    {
        // Encode source text in url format
        $text = rawurlencode($text);

        // Build url for translation
        $this->get .= '&q='.$text.'&source='.$this->source.'&target='.$this->target;

        // Get result of get request in array format using curl
        $curlHandler = curl_init($this->get);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandler);
        curl_close($curlHandler);
        $response = json_decode($response, true);

        // If we have some response
        if ($response != null) {
            // Detect errors
            if (isset($response['error'])) {
                // Create error message
                $return = 'Translation has failed : '.$response['error']['message'];
            } else {
                trace($response);
                // Get translated text from the response array
                $return = $response['data']['translations'][0]['translatedText'];
            }
        } else {
            // Empty response error
            $return = 'Translation has failed : Unknown error';
        }

        // Return translated text or error message
        return $return;
    }
}
