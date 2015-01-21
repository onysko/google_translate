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
    }
}
