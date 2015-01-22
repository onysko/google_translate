<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 21.01.2015
 * Time: 16:39
 */

namespace samsonphp\google\translate;


class Request
{
    /**
     * Create request to Google Translate API
     * @param $url string Google API url
     * @return mixed Google response
     */
    public function get($url)
    {
        // Get result of get request in array format using curl
        $curlHandler = curl_init($url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

        return $response;
    }
}
