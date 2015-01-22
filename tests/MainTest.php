<?php
namespace tests;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samson\google\Translate */
    public $instance;

    /** @var \samson\google\Request */
    public $request;

    /**
     *
     */
    public function setUp()
    {
        \samson\core\Error::$OUTPUT = false;

        // Create S3 mock
        $this->request = $this->getMockBuilder('\samson\google\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = \samson\core\Service::getInstance('\samson\google\Translate');
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
    "translatedText": "Привіт"
   }
  ]
 }
}
        ';
        $this->request
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $translated = $this->instance->source('ru')->target('uk')->trans('Привет');

        // Perform test
        $this->assertEquals('Привіт', $translated);
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
        $this->assertEquals('Translation has failed : Invalid Value', $translated);
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
        $this->assertEquals('Translation has failed : Unknown error', $translated);
    }

    public function testRequestClass()
    {
        $request = new \samson\google\Request();
        $response = $request->get('url');
    }

}
