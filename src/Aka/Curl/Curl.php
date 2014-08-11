<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Aka\Curl;

use Aka\Curl\HttpMethods,
    Aka\Curl\Exeption\InvalidArgumentException,
    Aka\Curl\Exeption\ErrorException,
    Aka\Curl\Exeption\CurlExeption,
    Aka\Curl\Helper\PostFields;

/**
 * Description of Curl
 *
 * @author Alexander Kaginyan
 */
class Curl {

    /**
     * Library  version
     * 
     * @var string
     */
    const VERSION = '0.2';

    /**
     * The cURL handler
     *
     * @var resource|null
     */
    private $handler = null;

    /**
     * Stores execution results from server
     *
     * @var string
     */
    private $result = null;

    /**
     * cURL session URL 
     *
     * @var string
     */
    private $url = null;

    /**
     * cURL session User Agent 
     * 
     * @var string 
     */
    private $userAgent = null;

    /**
     * HTTP headers to send
     * 
     * @var array 
     */
    private $headers = array();

    /*
     * Stores information regarding the last transfer 
     * 
     * @var array 
     */
    private $requestInfo = array();

    /**
     * @var string Server response headers
     */
    private $responsetHeaders = null;

    /**
     * @var string Response body
     */
    private $responseBody = null;

    /**
     *
     * @var int Last HTTP code
     */
    private $httpCode = 0;

    /**
     * Initialize the cURL and sets the URL
     * 
     * @param string $url
     * @throws ErrorException
     */
    function __construct($url) {
        if (!extension_loaded('curl')) {
            throw new ErrorException('PHP cURL extension must be loaded to use this library.');
        }

        $this->initialize();
        $this->setUrl($url);
    }

    /**
     * Closes cURL session and frees resource
     */
    public function __destruct() {
        $this->close();
    }

    private function initialize() {
        $this->handler = curl_init();
        $this->setOption(CURLOPT_RETURNTRANSFER, 1)
                ->setOption(CURLINFO_HEADER_OUT, 1)
                ->setOption(CURLOPT_VERBOSE, 1)
                ->setOption(CURLOPT_HEADER, 1);
        // Sets default User Agent
        $this->setUserAgent();
    }

    /**
     * Executes GET request using $data as 
     * @return string
     */
    public function get() {
        return $this->exec(HttpMethods::HTTP_GET, null);
    }

    public function post(PostFields $data = null) {
        return $this->exec(HttpMethods::HTTP_POST, $data);
    }

    /**
     * Executes PUT request
     * 
     * @param PostFields $data
     * @return mixed
     */
    public function put(PostFields $data = null) {
        return $this->exec(HttpMethods::HTTP_PUT, $data);
    }

    public function delete(PostFields $data = null) {
        return $this->exec(HttpMethods::HTTP_DELETE, $data);
    }

    /**
     * Sets the user agent
     * 
     * @param string $userAgent
     */
    public function setUserAgent($userAgent = 'default') {
        if ($userAgent === 'default') {
            $userAgent = 'PHP cUrl ' . curl_version()['version'];
        }
        $this->userAgent = $userAgent;
        $this->setOption(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * Gets the user agent string
     * 
     * @return string $userAgent
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Sets cURL session option
     * 
     * @param int $option
     * @param mixed $value
     * @return Curl
     */
    public function setOption($option, $value) {
        if (filter_var($option, FILTER_VALIDATE_INT)) {
            curl_setopt($this->handler, $option, $value);
        } else {
            $msg = sprintf('Option value expected to be int. %s provided.', gettype($option));
            throw new InvalidArgumentException($msg);
        }
        return $this;
    }

    /**
     * Starts the cUrl session
     * 
     * @throws CurlExeption
     * @return mixed
     */
    public function exec($method, $data) {
        // Stting header
        if (!empty($this->headers)) {
            $this->setOption(CURLOPT_HTTPHEADER, $this->buildHeaders());
        }
        // Adding payloads
        if (!is_null($data)) {
            $this->setOption(CURLOPT_POST, $data->size());
            $this->setOption(CURLOPT_POSTFIELDS, $data->getQuery());
        }
        switch ($method) {
            case HttpMethods::HTTP_POST:
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'POST');
                break;
            case HttpMethods::HTTP_PUT:
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case HttpMethods::HTTP_DELETE:
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case HttpMethods::HTTP_GET:
            default :
                break;
        }

        $result = curl_exec($this->handler);

        // Any cURL errors?
        if ($result === false) {
            // Throw an exeption
            throw new CurlExeption($this->handler);
        }

        //Storing the last successful execution result
        $this->result = $result;
        $headerSize = curl_getinfo($this->handler, CURLINFO_HEADER_SIZE);
        $this->responsetHeaders = substr($this->result, 0, $headerSize);
        $this->requestInfo = curl_getinfo($this->handler);
        $this->httpCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
        $this->responseBody = substr($this->result, $headerSize);
        $this->responseBody = $this->responseBody ? $this->responseBody : '';
        
        return curl_exec($this->handler);
    }

    /**
     * Close the cURL session
     * 
     * return void
     */
    public function close() {
        if (is_resource($this->handler)) {
            curl_close($this->handler);
        }

        $this->curl = null;
    }

    /**
     * Gets the cURL handler
     * 
     * @return resource
     */
    public function getHandler() {
        return $this->handler;
    }

    /**
     * Gets the URL
     * 
     * @return string Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Sets the URL
     * 
     * @param string $url
     * @throws InvalidArgumentException
     */
    public function setUrl($url) {
        $url = trim($url);
        if (filter_var($url, FILTER_VALIDATE_URL) || filter_var($url, FILTER_VALIDATE_IP)) {
            $this->url = $url;
            $this->setOption(CURLOPT_URL, $url);
        } else {
            throw new InvalidArgumentException("Not a valid URL or IP address");
        }
    }

    /**
     * Returns an associative array with information
     * regarding last execute transfer 
     * 
     * @return array
     */
    public function getRequestInfo() {
        return $this->requestInfo;
    }

    /**
     * @return string Reques Headers
     */
    public function getRequestHeaders() {
        return $this->responsetHeaders;
    }

    /**
     * Gets the execution result recieved from server
     * 
     * @return string 
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Gets the response bode
     * @return string
     */
    public function getResponseBody() {
        return $this->responseBody;
    }

    /**
     * HTTP code recieved from server
     * 
     * @return int
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * Adds a request header
     * 
     * @param string $key
     * @param string $value
     * @throws InvalidArgumentException
     * @return Curl
     */
    public function addHeader($key, $value) {
        if (!is_string($key) && !is_string($value)) {
            $msg = sprintf('Options value expected to be string. %s, %s provided.', gettype($key), gettype($value));
            throw new InvalidArgumentException($msg);
        }

        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 
     * @param type $key
     * @return boolean
     */
    public function removeHeader($key) {
        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$kry]);
            return true;
        }

        return true;
    }

    /**
     * Clears the headers
     */
    public function clearHeaders() {
        $this->headers = array();
    }

    /**
     * Builde cURL headers array 
     */
    private function buildHeaders() {
        $cUrlHeaders = array();

        foreach ($this->headers as $headerKey => $value) {
            $cUrlHeaders[] = $headerKey . ': ' . $value;
        }

        return $cUrlHeaders;
    }

}

/**
 * Emulating Enums in PHP
 * 
 * It should be better way  to use SplEnum 
 * but it does not available in standard PHP bundle 
 */
abstract class HttpMethods {

    const HTTP_GET = 0;
    const HTTP_POST = 1;
    const HTTP_PUT = 2;
    const HTTP_DELETE = 3;

}
