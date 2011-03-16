<?php

require_once dirname(__FILE__).'/../../www/plugins/kbi/KBIntegrator.php';
require_once dirname(__FILE__).'/../data/queries.php';

/**
 * Test class for KBIQuery.
 */
class KBIQueryTest extends PHPUnit_Framework_TestCase
{
	/**
     * @var KBIQuery
     */
    protected $object;

	private $_testObjects = array();

	protected function setUp()
    {
		$this->object = new KBIQuery();
		//$this->_testObjects = json_decode(file_get_contents(dirname(__FILE__).'/../data/kbiqueries.json'), true);
		
		$this->_testObjects = QueriesData::getData();
    }

    protected function tearDown()
    {
		unset($this->object);
    }

	public function testSetQuery()
	{
		foreach($this->_testObjects as $obj) {
			$this->object->setQuery($obj['query']['query']);
			$this->assertEquals($obj['query']['query'], $this->object->getQuery());
		}
	}

	public function testSetParameters()
	{
		foreach($this->_testObjects as $obj) {
			$this->object->setParameters($obj['query']['parameters']);
			$this->assertEquals($obj['query']['parameters'], $this->object->getParameters());
		}
	}

	public function testSetXslt()
	{
		foreach($this->_testObjects as $obj) {
			$this->object->setXslt($obj['query']['xslt']);
			$this->assertEquals($obj['query']['xslt'], $this->object->getXslt());
		}
	}

	public function testSetDelimiter()
	{
		foreach($this->_testObjects as $obj) {
			if(isset($obj['query']['delimiter'])) {
				$this->object->setDelimiter($obj['query']['delimiter']);
				$this->assertEquals($obj['query']['delimiter'], $this->object->getDelimiter());
			}
		}
	}

	public function testProccessQuery()
	{
		foreach($this->_testObjects as $obj) {
			$this->object->setQuery($obj['query']['query']);
			$this->object->setParameters($obj['query']['parameters']);
			$this->object->setXslt($obj['query']['xslt']);
			
			if(isset($obj['query']['delimiter']))
				$this->object->setDelimiter($obj['query']['delimiter']);

			$this->assertEquals($obj['result'], $this->object->proccessQuery());
		}
	}
}