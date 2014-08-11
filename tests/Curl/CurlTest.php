<?php

namespace Aka\Test;
use Aka\Curl\Curl;

/**
 * Description of CurlTest
 *
 * @author Alex
 */
class CurlTest extends \PHPUnit_Framework_TestCase{
   /**
    * @dataProvider providerTestSetUrlMethodThoughExeption
    * @expectedException InvalidArgumentException
    */
    public function testSetUrlMethodThoughExeption($url){
      new Curl($url);
    }
    
    /**
    * @expectedException Aka\Curl\Exeption\CurlExeption
    */
    public function testSetUrlExeptionWhenCurkError(){
      $curl = new Curl('foo://www.google.com');
      $curl->get();
    }
    
   
    /*============== DATA PROVIDERS ================ */
    public function providerTestSetUrlMethodThoughExeption(){
        $testStrings = array('foo', 'b ar.com','%my-site.com');
        
        return array_chunk($testStrings, 1);
    }
   
}
