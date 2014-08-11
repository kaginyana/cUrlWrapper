CurlWrapper
===========

Another Wrapper class for PHP cURL extension

Install
-------

### via Composer 

`php composer.phar require alexander-kaginyan/cUrlWrapper '>0.1'`


Usage
-----

### Initialization

```php
use Bp\Curl\Curl;

try {
    $client = new Curl('http://example.com');
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}

$client->setUserAgent('Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36');
client->get() ;
var_dump($client->getHttpCode());
var_dump($client->getResponseBody() );
var_dump($client->getRequestInfo());
var_dump($client->getRequestHeaders());
         
$client->close();

```