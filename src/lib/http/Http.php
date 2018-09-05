<?php
/**
 * Copyright (c) 2018-present, Renderforest, LLC.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory.
 */

require_once(dirname(__FILE__) . '/../Singleton.php');
require_once(dirname(__FILE__) . '/../auth/Auth.php');
require_once(dirname(__FILE__) . '/../../../vendor/autoload.php');

class Http
{
    use Singleton;

    private $signKey;
    private $clientId;
    private $HOST;
    private $DEFAULT_OPTIONS;
    private $_options;

    /**
     * Http constructor.
     */
    private function __construct()
    {
        $this->signKey = NULL;
        $this->clientId = NULL;
        $this->HOST = 'https://api.renderforest.com';

        /**
         * Get SDK version from composer.json file and set it in `User-Agent`.
         */
        $ComposerJson = json_decode(file_get_contents(dirname(__FILE__) . '/../../../composer.json'), true);
        $sdkVersion = $ComposerJson['version'];
        $this->DEFAULT_OPTIONS = array(
            'headers' => array(
                'Accept' => 'application/json',
                'User-Agent' => "renderforest/sdk-php/$sdkVersion"
            )
        );
    }

    /**
     * @param $signKey {string}
     * @param $clientId {number}
     * Set config.
     */
    public function setConfig($signKey, $clientId)
    {
        $this->signKey = $signKey;
        $this->clientId = $clientId;
    }

    /**
     * @param $options {Object}
     * Append query params.
     * Format object parameters into GET request query string.
     */
    public function appendQueryParams($options)
    {
        if (isset($options['method']) && $options['method'] === 'GET' && isset($options['qs']) && sizeof(get_object_vars($options['qs']))) {
            $queryString = $options['qs'];
            $queryParams = parse_url($queryString);

            $this->_options['endpoint'] .= $queryParams;
        }
    }

    /**
     * @param {Object} options
     * Append URI.
     */
    public function appendURI($options)
    {
        $endpoint = $options['endpoint'];
        $this->_options['uri'] = $this->HOST . $endpoint;
    }

    /**
     * @param {Object} options
     * Prepare request.
     */
    public function prepareRequest($options)
    {
        $this->appendQueryParams($options);
        $this->appendURI($options);
    }

    /**
     * @param $options {Object}
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * Request.
     */
    public function request($options)
    {
        $client = new \GuzzleHttp\Client();
        $requestMethod = isset($options['method']) ? $options['method'] : 'GET';
        $requestURI = isset($options['uri']) ? $options['uri'] : '';

        try {
            $response = $client->request($requestMethod, $requestURI, $options);
            $responseContent = (array)json_decode($response->getBody()->getContents());

            return $responseContent['data'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo $e;
        }
    }

    /**
     * @param $options {Object}
     * @return \GuzzleHttp\Promise\PromiseInterface
     * Unauthorized request.
     */

    /**
     * @param $options
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unauthorizedRequest($options)
    {
        $_options = array_replace_recursive($this->DEFAULT_OPTIONS, (array)$options);
        $this->prepareRequest($_options);

        return $this->request($_options);
    }

    /**
     * @param $options {Object}
     * @return null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authorizedRequest($options)
    {
        $Auth = Auth::getInstance();
        $this->_options = array_replace_recursive($this->DEFAULT_OPTIONS, (array)$options);

        $this->prepareRequest($this->_options);
        $authorizedRequestOptions = $Auth->setAuthorization($this->_options, $this->signKey, $this->clientId);

        return $this->request($authorizedRequestOptions);
    }
}
