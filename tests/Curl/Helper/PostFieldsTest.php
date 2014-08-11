<?php
namespace Aka\Test\Helper;
use Aka\Curl\Helper\PostFields;

/**
 * Description of PostFieldsTest
 *
 * @author Alex
 */
class PostFieldsTest extends \PHPUnit_Framework_TestCase {

    public function testContainerSize() {
        $postFileds = new PostFields();
        $postFileds->setParam('p1', 1);
        $postFileds->setParam('p2', 2);
        $this->assertEquals($postFileds->size(), 2);
    }
    
    public function testGetFieldsString(){
        $postFileds = new PostFields();
        $postFileds->setParam('p1', 'param 1');
        $postFileds->setParam('p2', 'param 2');
        $this->assertEquals($postFileds->getQuery(), 'p1=param+1&p2=param+2');
    }

}
