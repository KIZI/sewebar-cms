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
    private $thresholdType;
    private $compareType;
    private $explanation;
    private $fields;

    public function __construct ($name, $localizedName, $thresholdType, $compareType, $explanation) {
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->thresholdType = $thresholdType;
        $this->compareType = $compareType;
        $this->explanation = $explanation;
        $this->fields = array();
    }

    public function addIntervalField ($name, $defaultValue, $localizedName, $minValue, $minValueInclusive, $maxValue, $maxValueInclusive, $dataType) {
        $arr = array(
        	'name' => $name,
            'defaultValue' => $defaultValue,
            'localizedName' => $localizedName,
            'minValue' => $minValue,
            'minValueInclusive' => $minValueInclusive,
            'maxValue' => $maxValue,
            'maxValueInclusive' => $maxValueInclusive,
            'dataType' => $dataType);
        array_push($this->fields, $arr);
    }

    public function addEnumerationField ($name, $defaultValue, $localizedName, $values, $dataType) {
        $arr = array(
        	'name' => $name,
            'defaultValue' => $defaultValue,
            'localizedName' => $localizedName,
            'values' => $values,
            'dataType' => $dataType);
        array_push($this->fields, $arr);
    }

    public function toArray () {
        $array = array(
        $this->name => array(
            'localizedName' => $this->localizedName,
            'thresholdType' => $this->thresholdType,
            'compareType' => $this->compareType,
			'explanation' => $this->explanation,
			'fields' => $this->fields));

        return $array;
    }

}