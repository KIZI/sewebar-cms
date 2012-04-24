<?php

/**
 * FeatureList parser
 *
 * @author Radek Skrabal <radek@skrabal.me>
 * @version 1.0
 */
class FeatureListParser {

    private $FL;
    private $lang;
    private $XPath;
    private $data;

    private static $PATTERN_MAX = 99999;
    private static $IM_MIN = -9999999;
    private static $IM_MAX = 9999999;
    private static $IM_INCLUSIVE_MIN = true;
    private static $IM_INCLUSIVE_MAX = true;
    private static $CONSTRAINT_REMAINING = 'remaining';

    function __construct($FL, $lang) {
        $this->FL = $FL;
        $this->lang = $lang;
        $this->XPath = new DOMXPath($this->FL);
        $this->data = array();
    }

    public function parseData() {
        $this->data = array_merge_recursive($this->data,
        $this->parseUserInterface(),
        $this->parseRulePattern(),
        $this->parseIMs(),
        $this->parseBBAs(),
        $this->parseDBAs());

        return $this->data;
    }

    protected function parseUserInterface() {
        $array['miningMode'] = ($this->XPath->evaluate('UserInterface/AllowMultipleRules')->item(0)->nodeValue === 'false');

        return $array;
    }

    protected function parseRulePattern() {
        $array['rulePattern'] = array();
        foreach ($this->XPath->evaluate('RulePattern')->item(0)->childNodes as $n) {
            $array['rulePattern'][$n->nodeName]['minNumberOfBBAs'] = intval($n->getAttribute('minNumberOfBBAs'));
            $array['rulePattern'][$n->nodeName]['maxNumberOfBBAs'] = $n->hasAttribute('maxNumberOfBBAs') ? intval($n->getAttribute('maxNumberOfBBAs')) : self::$PATTERN_MAX;
        }

        return $array;
    }

    protected function parseIMs() {
        $array['interestMeasures'] = array_merge_recursive($this->parseIMTreshold(), $this->parseIMTypes(), $this->parseIMCombinations());

        return $array;
    }

    protected function parseIMTreshold() {
        $array['treshold'] = $this->XPath->evaluate('BuildingBlocks/InterestMeasures/@threshold')->item(0)->value;

        return $array;
    }

    protected function parseIMTypes() {
        $array['types'] = array();
        foreach ($this->XPath->evaluate('BuildingBlocks/InterestMeasures/Types/Type') as $t) {
            $name = $this->XPath->evaluate('Name', $t)->item(0)->nodeValue;
            $localizedName = '';
            if ($LN = $this->XPath->evaluate('LocalizedName[@lang="'.$this->lang.'"]', $t)->item(0)) {
                $localizedName = $LN->nodeValue;
            }
            $explanation = '';
            if ($EX = $this->XPath->evaluate('Explanation[@lang="'.$this->lang.'"]', $t)->item(0)) {
                $explanation = $EX->nodeValue;
            }
            $IM = new FLInterestMeasure($name, $localizedName, $explanation);

            $f = $this->XPath->evaluate('Field', $t)->item(0);
            $name = $this->XPath->evaluate('Name', $f)->item(0)->nodeValue;
            $localizedName = $this->XPath->evaluate('LocalizedName[@lang="'.$this->lang.'"]', $f)->item(0)->nodeValue;
            $dataType = $this->XPath->evaluate('Validation/Datatype', $f)->item(0)->nodeValue;
            
            if ($dataType === 'listOfFieldValues') { // enumeration
                $vals = array();
                foreach($this->XPath->evaluate('Validation/FieldValue', $f) as $fv) {
                    array_push($vals, $fv->nodeValue);
                }
                sort($vals);
                
                $IM->setEnumerationField($name, $localizedName, $vals, $dataType);
            } else { // interval
                if ($mv = $this->XPath->evaluate('Validation/MinValue', $f)->item(0)) {
                    $minValue = intval($mv->nodeValue);
                    $minValueInclusive = ($mv->getAttribute('inclusive') === 'yes');
                } else {
                    $minValue = self::$IM_MIN;
                    $minValueInclusive = self::$IM_INCLUSIVE_MIN;
                }
    
                if ($mv = $this->XPath->evaluate('Validation/MaxValue', $f)->item(0)) {
                    $maxValue = intval($mv->nodeValue);
                    $maxValueInclusive = ($mv->getAttribute('inclusive') === 'yes');
                } else {
                    $maxValue = self::$IM_MAX;
                    $maxValueInclusive = self::$IM_INCLUSIVE_MAX;
                }
    
                $IM->setIntervalField($name, $localizedName, $minValue, $minValueInclusive, $maxValue, $maxValueInclusive, $dataType);
            }
            
            $array['types'] = array_merge_recursive($array['types'], $IM->toArray());
        }

        return $array;
    }

    protected function parseIMCombinations() {
        $array['combinations'] = array();
        foreach ($this->XPath->evaluate('BuildingBlocks/InterestMeasures/SupportedInterestMeasureCombinations/SupportedIMCombination') as $c) {
            $combination = array();
            foreach ($c->childNodes as $n) {
                array_push($combination, $n->nodeValue);
            }
            array_push($array['combinations'], $combination);
        }

        return $array;
    }

    protected function parseBBAs() {
        $array['BBA'] = array_merge_recursive($this->parseCoefficient(), $this->parseCoefficients());

        return $array;
    }

    protected function parseCoefficient() {
        $array['coefficient'] = $this->XPath->evaluate('BuildingBlocks/BasicBooleanAttribute/@coefficient')->item(0)->value;

        return $array;
    }

    protected function parseCoefficients() {
        $array['coefficients'] = array();
        foreach ($this->XPath->evaluate('BuildingBlocks/BasicBooleanAttribute/Coefficient/Type') as $t) {
            $name = $this->XPath->evaluate('Name', $t)->item(0)->nodeValue;
            $localizedName = '';
            if ($LN = $this->XPath->evaluate('LocalizedName[@lang="'.$this->lang.'"]', $t)->item(0)) {
                $localizedName = $LN->nodeValue;
            }
            $explanation = '';
            if ($EX = $this->XPath->evaluate('Explanation[@lang="'.$this->lang.'"]', $t)->item(0)) {
                $explanation = $EX->nodeValue;
            }
            $C = new Coefficient($name, $localizedName, $explanation);

            foreach ($this->XPath->evaluate('Field', $t) as $f) {
                $name = $this->XPath->evaluate('Name', $f)->item(0)->nodeValue;
                $localizedName = $this->XPath->evaluate('LocalizedName[@lang="'.$this->lang.'"]', $f)->item(0)->nodeValue;
                if ($mv = $this->XPath->evaluate('Validation/MinValue', $f)->item(0)) {
                    $minValue = intval($mv->nodeValue);
                } else {
                    $minValue = self::$IM_MIN;
                }

                if ($mv = $this->XPath->evaluate('Validation/MaxValue', $f)->item(0)) {
                    $maxValue = intval($mv->nodeValue);
                } else {
                    $maxValue = self::$IM_MAX;
                }

                $dataType = $this->XPath->evaluate('Validation/Datatype', $f)->item(0)->nodeValue;

                $previous = '';
                if ($PR = $this->XPath->evaluate('Validation/Previous', $f)->item(0)) {
                    $previous = $PR->nodeValue;
                }

                $C->addField($name, $localizedName, $minValue, self::$IM_INCLUSIVE_MIN, $maxValue, self::$IM_INCLUSIVE_MAX, $dataType, $previous);
            }

            $array['coefficients'] = array_merge_recursive($array['coefficients'], $C->toArray());
        }

        return $array;
    }

    protected function parseDBAs() {
        $array['DBA'] = array();
        $array['DBA']['maxLevels'] = intval($this->XPath->evaluate('BuildingBlocks/DerivedBooleanAttribute/NestingConstraints/MaxLevels')->item(0)->nodeValue);
        $array['DBA'] = array_merge_recursive($array['DBA'], $this->parseNestingConstraints($array['DBA']['maxLevels']));

        return $array;
    }

    protected function parseNestingConstraints($maxLevels) {
        $array['constraints'] = array();
        foreach ($this->XPath->evaluate('BuildingBlocks/DerivedBooleanAttribute/NestingConstraints/NestingConstraint') as $k => $nc) {
            $NC = new NestingConstraint();
            $level = $nc->getAttribute('level');
            foreach ($this->XPath->evaluate('Connectives/child::node()', $nc) as $c) {
                $allowed = ($c->getAttribute('allowed') === 'yes');
                $NC->addConnective($c->nodeName, $allowed);
            }

            if ($level === self::$CONSTRAINT_REMAINING) {
                for ($i = ($k + 1); $i <= $maxLevels; $i++) {
                    $NC->setLevel($i);
                    $array['constraints'] = array_merge_recursive($array['constraints'], $NC->toArray());
                }
            } else {
                $NC->setLevel($level);
                $array['constraints'] = array_merge_recursive($array['constraints'], $NC->toArray());
            }
        }

        return $array;
    }

}

?>