<?php

/**
 * FLInterestMeasure value object
 *
 * @author Radek Skrabal <radek@skrabal.me>
 * @version 1.0
 */
class FLInterestMeasure {

    private $name;
    private $localizedName;
    private $explanation;
    private $field;

    public function __construct ($name, $localizedName, $explanation) {
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->explanation = $explanation;
        $this->field = array();
    }

    public function setIntervalField ($name, $localizedName, $minValue, $minValueInclusive, $maxValue, $maxValueInclusive, $dataType) {
        $this->field = array('name' => $name,
                     'localizedName' => $localizedName,
                     'minValue' => $minValue,
                     'minValueInclusive' => $minValueInclusive,
                     'maxValue' => $maxValue,
                     'maxValueInclusive' => $maxValueInclusive,
                     'dataType' => $dataType);
    }
    
    public function setEnumerationField ($name, $localizedName, $values, $dataType) {
        $this->field = array('name' => $name,
                     'localizedName' => $localizedName,
                     'values' => $values,
                     'dataType' => $dataType);
    }

    public function toArray () {
        $array = array($this->name => array('localizedName' => $this->localizedName,
    		   'explanation' => $this->explanation,
               'field' => $this->field));

        return $array;
    }

}