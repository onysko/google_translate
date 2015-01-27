<?php
namespace samsonphp\google\translate;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonphp\google\translate\Translate */
    public $instance;

    /** @var \samsonphp\google\translate\Request */
    public $request;

    /**
     *
     */
    public function setUp()
    {
        \samson\core\Error::$OUTPUT = false;

        // Create S3 mock
        $this->request = $this->getMockBuilder('\samsonphp\google\translate\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = \samson\core\Service::getInstance('\samsonphp\google\translate\Translate');
    }

    /** Test if we can pass parameters by reference to change them in event callback handler */
    public function testTranslation()
    {
        $this->instance->init();

        $this->instance->request = & $this->request;

        $response = '
            {
             "data": {
              "translations": [
               {
                "translatedText": "chien"
               }
              ]
             }
            }
        ';
        $this->request
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $translated = $this->instance->source('en')->target('fr')->trans('dog');

        // Perform test
        $this->assertEquals('chien', $translated);
        $this->assertEquals(true, $this->instance->lastRequestStatus());
    }

    public function testTranslateArray()
    {
        $this->instance->init();

        $this->instance->request = & $this->request;

        $response = '
            {
             "data": {
              "translations": [
               {
                "translatedText": "chien"
               },
               {
                "translatedText": "chat"
               }
              ]
             }
            }
        ';
        $this->request
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $translated = $this->instance->source('en')->target('fr')->trans(array('dog', 'cat'));

        // Perform test
        $this->assertEquals(array('dog' => 'chien', 'cat' => 'chat'), $translated);
        $this->assertEquals(true, $this->instance->lastRequestStatus());
    }

    /**
     *
     */
    public function testInValidTranslation()
    {
        $this->instance->init();

        $this->instance->request = & $this->request;

        $response = '
        {
 "error": {
  "errors": [
   {
    "domain": "global",
    "reason": "invalid",
    "message": "Invalid Value"
   }
  ],
  "code": 400,
  "message": "Invalid Value"
 }
}
        ';
        $this->request
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $translated = $this->instance->source('ua')->target('ru')->trans('Привет');

        // Perform test
        $this->assertEquals('Invalid Value', $translated);
        $this->assertEquals(false, $this->instance->lastRequestStatus());
    }

    /**
     *
     */
    public function testUnknownTranslation()
    {
        $this->instance->apiKey = 'google_api_key';

        $this->instance->init();

        $this->instance->request = & $this->request;

        $this->request
            ->expects($this->once())
            ->method('get')
            ->willReturn('Привет');

        $translated = $this->instance->source('uk')->target('ru')->trans('Привет');

        // Perform test
        $this->assertEquals('Check your API Settings', $translated);
    }

    public function testRequestClass()
    {
        $request = new \samsonphp\google\translate\Request();
        $response = $request->get('url');
    }

}
