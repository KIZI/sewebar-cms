<?php
/**
 * It is descendant of AncestorGetData. This specific implementation gets data
 * from Data Description and Feature List.
 */
class GetDataARBuilderQuery extends AncestorGetData {

    private $domDD;
    private $domFL;
    private $domER;
    private $lang;
    private $jsonObject;

    /**
     * This creates instance of descendant.
     *
     * @param <String> $dataDescription Location of DataDescription
     * @param <String> $featureList     Location of FeatureList
     * @param <String> $existingRules   Location of Existig Rules
     * @param <String> $lang            Language which should be used
     */
    function __construct($domDD1, $domFL1, $domER1, $lang) {

        $this->lang = $lang;

        $this->domDD = new DomDocument();
        $this->domFL = new DomDocument();

        $this->domDD->load($domDD1);
        $this->domFL->load($domFL1);

        $this->jsonObject = array();

        if ($domER1 != null) {
            $this->domER = new DomDocument();
            $this->domER->load($domER1);
        } else {
            $this->domER = null;
        }
        
        session_start();
        $_SESSION["ARBuilder_domDataDescr"] = $domDD1;
        $_SESSION["ARBuilder_domFeatureList"] = $domFL1;
        $_SESSION["ARBuilder_domExistingRules"] = $domER1;
    }

    /**
     * It is specific implementation of ancestor method getData()
     *
     * @return <String> JSON represantation of given data.
     */
    public function getData() {
        $attributes = $this->solveData();
        $this->jsonObject['attributes'] = $attributes;

        $interestMeasure = $this->solveInterestMeasures();
        $this->jsonObject['interestMeasures'] = $interestMeasure;
        
        $this->solveNumberBBA();
        $this->solveDepthNesting();
        $this->solveMoreRules();
        
        $posCoef = $this->solvePosCoef();
        $this->jsonObject['possibleCoef'] = $posCoef;

        if ($this->domER != null) {
            $this->solveExistingRules();
        } else {
            $this->jsonObject['rules'] = 0;
        }
        $this->jsonObject['lang'] = $this->lang;

        $json = new Services_JSON();
        return $json->encode($this->jsonObject);
    }

    /**
     * It decides whether DomNode has attribute.
     *
     * @param <DomNode> $node Node by which I watn to decide whether it has attribute.
     * @param <String> $name Name of wanted attribute
     * @return <Boolean> True if it has attributes otherwise false.
     */
    private function hasAttributes($node, $name) {
        $utils = new Utils();
        if ($utils->getAttribute($node, $name) != "") {
            return true;
        }
        return false;
    }

    /**
     * It adds to the final JSON section which contents are limits of antecedent
     * elements, consequent elements, interest measure elements and elements in
     * the rule as such.
     */
    private function solveNumberBBA() {
        $antecedentMin = 0;
        $antecedentMax = 99999;
        $consequentMin = 0;
        $consequentMax = 99999;
        $IMMin = 0;
        $IMMax = 99999;
        $generalMin = 0;
        $generalMax = 99999;

        $utils = new Utils();
        $antecedent = $this->domFL->getElementsByTagName("Antecedent")->item(0);
        if ($this->hasAttributes($antecedent, "minNumberOfBBAs")) {
            $antecedentMin = $utils->getAttribute($antecedent, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($antecedent, "maxNumberOfBBA")) {
            $antecedentMax = $utils->getAttribute($antecedent, "maxNumberOfBBA");
        }

        $consequent = $this->domFL->getElementsByTagName("Consequent")->item(0);
        if ($this->hasAttributes($consequent, "minNumberOfBBAs")) {
            $consequentMin = $utils->getAttribute($consequent, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($consequent, "maxNumberOfBBA")) {
            $consequentMax = $utils->getAttribute($consequent, "maxNumberOfBBA");
        }

        $IM = $this->domFL->getElementsByTagName("InterestMeasureConstraint")->item(0);
        if ($this->hasAttributes($IM, "minNumberOfInterestMeasures")) {
            $IMMin = $utils->getAttribute($IM, "minNumberOfInterestMeasures");
        }
        if ($this->hasAttributes($IM, "maxNumberOfInterestMeasures")) {
            $IMMax = $utils->getAttribute($IM, "maxNumberOfInterestMeasures");
        }

        $general = $this->domFL->getElementsByTagName("GeneralConstraint")->item(0);
        if ($this->hasAttributes($general, "minNumberOfBBAs")) {
            $generalMin = $utils->getAttribute($general, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($general, "maxNumberOfBBA")) {
            $generalMax = $utils->getAttribute($general, "maxNumberOfBBA");
        }


        $this->jsonObject['minNumberBBA'] = $generalMin;
        $this->jsonObject['maxNumberBBA'] = $generalMax;
        $this->jsonObject['antMinNumberBBA'] = $antecedentMin;
        $this->jsonObject['antMaxNumberBBA'] = $antecedentMax;
        $this->jsonObject['consMinNumberBBA'] = $consequentMin;
        $this->jsonObject['consMaxNumberBBA'] = $consequentMax;
        $this->jsonObject['IMMinNumberBBA'] = $IMMin;
        $this->jsonObject['IMMaxNumberBBA'] = $IMMax;
    }

    /**
     * It gets from DataDictionary data which are known as attributes in ARBuilder
     * and add them to the finalJSON
     */
    private function solveData() {
        $field = $this->domDD->getElementsByTagName("Field");
        $attributeArray = array();
        foreach ($field as $elField) {
            $attribute = array();
            $attribute['name'] = $elField->getAttribute("name");
            $fieldChildren = $elField->childNodes;
            $choices = array();
            foreach ($fieldChildren as $elFieldChildren) {
                if ($elFieldChildren->nodeName == "Category") {
                    $choices[] = $elFieldChildren->nodeValue;
                }
            }
            $attribute['choices'] = $choices;
            $attributeArray[] = $attribute;
        }
        return $attributeArray;
    }

    /**
     * It adds additional information about one Interest Measure.
     *
     * @param <DomNode> $fieldNode Field of feature List
     * @param <String> $name Name of Interest Measure
     * @return <String> part of final JSON
     */
    private function solveIMFields($fieldNode, $name) {
        $fields = $fieldNode->childNodes;
        $names = array();
        $localizedNames = array();
        $minValues = array();
        $maxValues = array();
        $datatypes = array();
        $explanations = array();
        
        foreach ($fields as $field) {
            if ($field->nodeName == "Name" && !$field->hasAttributes()) {
                $names[] = $field->nodeValue;
            }
            if ($field->nodeName == "LocalizedName" && ($field->getAttribute("lang") == $this->lang)) {
                $localizedNames[] = $field->nodeValue;
            }
            //$json2 .= "FieldName ".$field->nodeName."<br><br>";
            if ($field->nodeName == "Validation") {
                $fieldValidations = $field->childNodes;
                $minValue = -9999999;
                $maxValue = 99999999;
                $datatype = "integer";
                foreach ($fieldValidations as $validation) {
                    if ($validation->nodeName == "MinValue") {
                        if ($validation->nodeValue != "" && $validation->nodeValue != null) {
                            $minValue = $validation->nodeValue;
                        }
                    }
                    if ($validation->nodeName == "MaxValue") {
                        if ($validation->nodeValue != "" && $validation->nodeValue != null) {
                            $maxValue = $validation->nodeValue;
                        }
                    }
                    if ($validation->nodeName == "Datatype") {
                        if ($validation->nodeValue != "" && $validation->nodeValue != null) {
                            $datatype = $validation->nodeValue;
                        }
                    }
                }
                $minValues[] = $minValue;
                $maxValues[] = $maxValue;
                $datatypes[] = $datatype;
                $explanations[] = "";
            }
        }
        $fieldsArray = array('fieldNames'=>$names,
                'fieldNamesLocalized'=>$localizedNames,
                'fieldMinValues'=>$minValues,
                'fieldMaxValues'=>$maxValues,
                'fieldDatatypes'=>$datatypes,
                'fieldExplanations'=>$explanations);
        return $fieldsArray;
    }

    /**
     * It get all Type from InterestMeasures and so adds to finalJSON part containing
     * operators and operatorsLang and field Information about the Interest Measure
     * and solve supported Interest Measures
     */
    private function solveInterestMeasures() {
        // /InterestMeasures/Types/Type /Name -> jméno /Field/Name -> jméno políèka
        $xPath = new DOMXPath($this->domFL);
        $anXPathExpr = "//InterestMeasures/Types/Type";
        $types = $xPath->query($anXPathExpr);
        $interestMeasures = array();
        foreach ($types as $typeS) {
            $interestMeasure = array();
            $typeChild = $typeS->childNodes;
            foreach ($typeChild as $type) {
                if ($type->nodeName == "Name" && !$type->hasAttributes()) {
                    $name = $type->nodeValue;
                    $interestMeasure['name'] = $name;
                }
                if ($type->nodeName == "LocalizedName" && ($type->getAttribute("lang") == $this->lang)) {
                    $interestMeasure['localizedName'] = $type->nodeValue;
                }
                if ($type->nodeName == "Field") {
                    $fields = array();
                    $fields = $this->solveIMFields($type, $name);
                    $interestMeasure['fields'] = $fields;
                }
                if ($type->nodeName == "Explanation" && ($type->getAttribute("lang") == $this->lang)) {
                    $interestMeasure['explanation'] = $type->nodeValue;
                }
            }
            $interestMeasures[] = $interestMeasure;
        }
        $this->solveSupportedIM();
        return $interestMeasures;
    }

    /**
     * It solves which combinations of Interest Measures are supported and adds
     * them to final JSON
     */
    private function solveSupportedIM() {
        $supportedIMCombinations = $this->domFL->getElementsByTagName("SupportedIMCombination");
        $amount = 0;
        foreach ($supportedIMCombinations as $supportedIMCombination) {
            $amount++;
            $supportedIM = array();
            $supportedChildren = $supportedIMCombination->childNodes;
            foreach ($supportedChildren as $supChild) {
                if ($supChild->nodeName == "InterestMeasure") {
                    $supportedIM[] = $supChild->nodeValue;
                }
            }
            $this->jsonObject['supIMCom'. $amount] = $supportedIM;
        }
        $this->jsonObject['supIMCombinations'] = $amount;
    }

    /**
     * It solves which connectives are allowed on which depth of nesting and adds
     * it to depthNesting of JSON.
     */
    private function solveDepthNesting() {
        $LEVEL_REMAINING = "remaining";
        $maxLevel = $this->domFL->getElementsByTagName("MaxLevels")->item(0);
        $this->jsonObject['depthNesting'] = $maxLevel->nodeValue;
        $nestingConstraint = $this->domFL->getElementsByTagName("NestingConstraint");
        $level = 0;
        foreach ($nestingConstraint as $constraint) {
            if ($constraint->getAttribute("level") != $LEVEL_REMAINING) {
                $level = $constraint->getAttribute("level");
            }
            $constraintChild = $constraint->childNodes;
            foreach ($constraintChild as $connective) {
                $connectiveChild = $connective->childNodes;
                if($connectiveChild == null){
                    continue;
                }
                $lengthList = $connectiveChild->length;
                for($connectivePos = 0; $connectivePos < $lengthList; $connectivePos++) {
                    $connegdis = $connectiveChild->item($connectivePos);
                    if ($connegdis->nodeName == "Conjunction") {
                        if ($connegdis->getAttribute("allowed") == "yes") {
                            $conj = "true";
                        } else {
                            $conj = "false";
                        }
                    }
                    if ($connegdis->nodeName == "Disjunction") {
                        if ($connegdis->getAttribute("allowed") == "yes") {
                            $dis = "true";
                        } else {
                            $dis = "false";
                        }
                    }
                    if ($connegdis->nodeName == "Negation") {
                        if ($connegdis->getAttribute("allowed") == "yes") {
                            $neg = "true";
                        } else {
                            $neg = "false";
                        }
                    }
                }
            }
            if ($constraint->getAttribute("level") == $LEVEL_REMAINING) {
                $conns = array($dis,$conj,$neg);
                for ($i = $level + 1; $i <= $maxLevel->nodeValue; $i++) {
                    $this->jsonObject['depth'. $i] = $conns;
                }
            } else {
                $conns = array($dis,$conj,$neg);
                $this->jsonObject['depth'. $level] = $conns;
            }
        }
    }

    /**
     * It solves whether it is possible to create more than one rule.
     */
    private function solveMoreRules() {
        $moreRules = $this->domFL->getElementsByTagName("AllowMultipleRules")->item(0);
        $this->jsonObject["moreRules"] = $moreRules->nodeValue;
    }

    /**
     * It solves which fields are possible to attributes.
     */
    private function solvePosCoef() {
        $xPath = new DOMXPath($this->domFL);
        $anXPathExpr = "//BasicBooleanAttribute/Coefficient/Type";
        $types = $xPath->query($anXPathExpr);
        $posCoefs = array();
        foreach ($types as $typeS) {
            $poesCoef = array();
            $typeChild = $typeS->childNodes;
            foreach ($typeChild as $type) {
                if ($type->nodeName == "Name" && !$type->hasAttributes()) {
                    $name = $type->nodeValue;
                    $poesCoef['name'] = $name;
                }
                if ($type->nodeName == "LocalizedName" && ($type->getAttribute("lang") == $this->lang)) {
                    $poesCoef['localizedName'] = $type->nodeValue;
                }
                if ($type->nodeName == "Explanation" && ($type->getAttribute("lang") == $this->lang)) {
                    $poesCoef['explanation'] = $type->nodeValue;
                }
                if ($type->nodeName == "Field") {
                    $poesCoef['fields'] = $this->solveIMFields($type, $name);
                }
            }
            $posCoefs[] = $poesCoef;
        }
        return $posCoefs;
    }

    /**
     * It gets existing rules from XML and based on the data creates JSON.
     */
    private function solveExistingRules() {
        // Dostanu soubor ve formátu ARBuilder.
        // Tento formát se následnì dìlí do tøí formátu: ARQuery, TaskSetting a AssociationRules
        // Z toho ARQuery a TaskSetting se zpracovávají stejnì. Liší se pouze spodek.
        // Každopádnì to pøevádíme do Elementù. ze kterých se vyrábí JSON
        $asocRuleType = $this->domER->getElementsByTagName('AssociationRules');
        if($asocRuleType->length > 0){
            $this->solveAssociationRules();
        }
        $taskSettingType = $this->domER->getElementsByTagName('TaskSetting');
        if($taskSettingType->length > 0){
            $this->solveTaskSetting('TaskSetting');
        }
        $ARQuery = $this->domER->getElementsByTagName('ARQuery');
        if($ARQuery->length > 0){
            $this->solveARQuery('ARQuery');
        }
    }

    /**
     * It solves the rules if the data format is AssociationRules
     */
    private function solveAssociationRules(){
        $ruleAr = array();
        $rules = $this->domER->getElementsByTagName('AssociationRule');
        foreach ($rules as $rule){
            $ruleAr[] = new AsociationRule($rule, $this->domER);
        }
        for($actualRule = 0; $actualRule < count($ruleAr); $actualRule++){
            $this->jsonObject["rule".$actualRule] = $ruleAr[$actualRule]->toJSON();
        }
        $this->jsonObject["rules"] = count($ruleAr);
    }

    /**
     * It solves the rules if the data format is ARQuery
     *
     * @param <String> $whichOne It is name of Element I get from the XML and then
     * proces further.
     */
    private function solveARQuery($whichOne) {
        $rule = $this->domER->getElementsByTagName($whichOne)->item(0);
        $ruleAR = new ARQueryRule($rule, $this->domER);
        $this->jsonObject["rule0"] = $ruleAR->toJSON();
        $this->jsonObject["rules"] = 1;
    }

    /**
     * It solves the rules if the data format is TaskSettings
     *
     * @param <String> $whichOne It is name of Element I get from the XML and then
     * proces further.
     */
    private function solveTaskSetting($whichOne) {
        $rule = $this->domER->getElementsByTagName($whichOne)->item(0);
        $ruleAR = new TaskSettingRule($rule, $this->domER);
        $this->jsonObject["rule0"] = $ruleAR->toJSON();
        $this->jsonObject["rules"] = 1;
    }

}
?>