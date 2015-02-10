<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 20.01.2015
 * Time: 11:49
 */

namespace samsonphp\google\translate;

use samson\core\CompressableService;
use samsonphp\event\Event;

/**
 * Translation using Google Translate API
 * Class Translate
 * @package samsonphp\google\translate
 */
class Translate extends CompressableService
{
    /** @var string Module identifier */
    protected $id = 'google_translate';

    /** @var string Url string for translation request */
    protected $get;

    /** @var bool Last request status */
    protected $status = false;

    /** @var Request Object for creating request on Google Translate API */
    public $request;

    /** @var string Google API Key */
    public $apiKey;

    /** @var string Source language for translations */
    public $source;

    /** @var string Target language for translations */
    public $target;

    /**
     * Create response with error message
     * @param $response
     * @return mixed
     */
    protected function error($response)
    {
        // Failed status
        $this->status = false;

        // Create error message
        return $response['error']['message'];
    }

    /**
     * Create response as simple string
     * @param $response array Parsed Google API response
     * @return mixed Translated string
     */
    protected function asString($response)
    {
        return $response[0]['translatedText'];
    }

    /**
     * Create response as associative array
     * @param $strings array Source - strings for translations
     * @param $response array Target - parsed Google API response
     * @return array Associative array with translations
     */
    protected function asArray($strings, $response)
    {
        /** @var array $return Associative array with translations */
        $return = array();

        // Build translations array
        for ($i = 0; $i < sizeof($response); $i++) {
            $return[$strings[$i]] = $response[$i]['translatedText'];
        }

        return $return;
    }

    /**
     * @param $strings array Strings for translation
     * @param $response array Google API response
     * @return string|array Translations
     */
    protected function createResponse($strings, $response)
    {
        // Get only necessary data from response array
        $response = $response['data']['translations'];

        // Success status
        $this->status = true;

        // Count translated strings
        $length = sizeof($response);

        /** @var array|string $return Associative array with translations or translated string */
        $return = $length > 1 ? $this->asArray($strings, $response) : $this->asString($response);

        return $return;
    }

    /**
     * @param $strings array Strings for translating
     * @param $json mixed JSON data for parsing
     * @return string|array Translated data or translation error message
     */
    protected function translateData($strings, $json)
    {
        // Decode response from JSON to array
        $response = json_decode($json, true);

        /** @var array|string $return */
        $return = 'Check your API Settings';

        if ($response != null) {
            // Detect errors
            $return = isset($response['error']) ? $this->error($response) : $this->createResponse($strings, $response);
        }

        return $return;
    }

    /**
     * Module initialization
     * @param array $params
     * @return bool
     */
    public function init(array $params = array())
    {
        // Create default or users Request object
        $this->request = (!isset($this->request) || !class_exists($this->request)) ? new Request() : new $this->request;

        // If configuration for API Key is not set
        if (!isset($this->apiKey)) {
            // Signal error
            Event::fire('error', array($this, 'Cannot initialize Translate module - Google API Key does not exists'));
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
     * @param $data string|array Data for translation
     * @return string|array Translated data
     */
    public function trans($data)
    {
        // Get only array values
        $strings = !is_array($data) ? array($data) : array_values($data);

        // Build url for translation
        $url = $this->get.'&source='.$this->source.'&target='.$this->target;

        // Add each of strings as url get parameter
        foreach ($strings as $string) {
            $url .= '&q='.rawurlencode($string);
        }

        // Set default last request status
        $this->status = false;

        // Return translated array or error message
        return $this->translateData($strings, $this->request->get($url));
    }

    /**
     * Get bool status of last translation request
     * @return bool Last request status
     */
    public function lastRequestStatus()
    {
        return $this->status;
    }
}
