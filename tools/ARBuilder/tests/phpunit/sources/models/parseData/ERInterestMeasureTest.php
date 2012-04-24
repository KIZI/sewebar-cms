<?php

require_once "Bootstrap.php";
require_once "PHPUnit/Framework.php";

class ERInterestMeasureTest extends PHPUnit_Framework_TestCase {

    private $IM;

    protected function setUp()
    {
        $this->IM = new ERInterestMeasure('Support', 0.91);
    }

    public function testToArray() {
        $array = array('name' => 'Support',
               'type' => 'im',
               'category' => '',
               'fields' => array('name' => 'prahovaHodnota', 
               			   	 'value' => 0.91));
        $this->assertEquals($array, $this->IM->toArray());
        $this->assertNotEquals(array(), $this->IM->toArray());
    }

}
