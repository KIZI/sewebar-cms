<?php

require_once dirname(__FILE__).'/../../www/plugins/kbi/KBIntegrator.php';
require_once dirname(__FILE__).'/../data/integrators.php';

/**
 * Test class for KBIntegrator.
 * Generated by PHPUnit on 2011-02-14 at 09:39:04.
 */
class KBIntegratorTest extends PHPUnit_Framework_TestCase
{
	/**
     * @var KBIntegrator
     */
    protected $object;

   	private $_testData = array();
	private $_testObjects = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
		$this->_testData = IntegratorsData::getData();

		foreach($this->_testData as $obj) {
			$this->_testObjects[] = KBIntegrator::create($obj['source']);
		}
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		unset($this->_testData);
		unset($this->_testObjects);
    }

    public function testCreate()
    {
    	foreach($this->_testObjects as $obj) {
			$this->assertInstanceOf('KBIntegrator', $obj);
		}
    }

    public function testSetUrl()
    {
    	foreach($this->_testData as $obj) {
    		$this->object = KBIntegrator::create($obj['source']);

    		$this->object->setUrl($obj['source']['url']);
			$this->assertEquals($obj['source']['url'], $this->object->getUrl());
		}
    }

    public function testSetMethod()
    {
       foreach($this->_testData as $obj) {
    		$this->object = KBIntegrator::create($obj['source']);

    		$this->object->setMethod($obj['source']['method']);
			$this->assertEquals($obj['source']['method'], $this->object->getMethod());
		}
    }

    public function testSetPort()
    {
     	foreach($this->_testData as $obj) {
    		$this->object = KBIntegrator::create($obj['source']);

    		$this->object->setPort($obj['source']['port']);
			$this->assertEquals($obj['source']['port'], $this->object->getPort());
		}
    }

    /**
     * @todo Implement test__contruct().
     */
    public function test__contruct()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testQuery().
     */
    public function testQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRequestGet().
     */
    public function testRequestGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRequestCurl().
     */
    public function testRequestCurl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRequestCurlPost().
     */
    public function testRequestCurlPost()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRequestPost().
     */
    public function testRequestPost()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>