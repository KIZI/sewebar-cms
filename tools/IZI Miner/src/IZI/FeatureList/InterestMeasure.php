<?php

namespace IZI\FeatureList;

class InterestMeasure
{
    private $name;
    private $localizedName;
    private $thresholdType;
    private $compareType;
    private $explanation;
    private $fields;
    private $default;

    public function __construct ($name, $localizedName, $thresholdType, $compareType, $explanation, $default)
    {
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->thresholdType = $thresholdType;
        $this->compareType = $compareType;
        $this->explanation = $explanation;
        $this->fields = array();
        $this->default = $default;
    }

    public function addIntervalField ($name, $defaultValue, $localizedName, $minValue, $minValueInclusive, $maxValue, $maxValueInclusive, $dataType)
    {
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

    public function addEnumerationField ($name, $defaultValue, $localizedName, $values, $dataType)
    {
        $arr = array(
            'name' => $name,
            'defaultValue' => $defaultValue,
            'localizedName' => $localizedName,
            'values' => $values,
            'dataType' => $dataType);
        array_push($this->fields, $arr);
    }

    public function toArray ()
    {
        $array = array(
        $this->name => array(
            'default' => $this->default,
            'localizedName' => $this->localizedName,
            'thresholdType' => $this->thresholdType,
            'compareType' => $this->compareType,
            'explanation' => $this->explanation,
            'fields' => $this->fields));

        return $array;
    }

}