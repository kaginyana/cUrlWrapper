<?php

namespace Aka\Curl\Exeption;

/**
 * Custom exeption to manage cURL errors
 *
 * @author Alexander Kaginyan
 */
class CurlExeption extends \Exception{
    /**
     * @param resource $handler cURL handler
     */
    public function __construct($handler) {
        $message = curl_error($handler);
        $code = curl_errno($handler);
        parent::__construct($message, $code);
    }
}
