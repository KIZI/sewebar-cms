<?php
/**
 * It is descendant of AncestorGetData. This specific implementation gets data
 * from Data Description and Feature List.
 */
class GetDataARBuilderQuery extends AncestorGetData {

    private $domDD;
    private $domFL;
    private $domER;
    private $finalJSON;
    private $lang;

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
        $this->domFL->loadXML($domFL1);



        if ($domER1 != null) {
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
        $this->solveData();
        $this->solveInterestMeasures();
        $this->solveNumberBBA();
        $this->solveDepthNesting();
        $this->solveDisjunction();
        $this->solveMoreRules();
        $this->solvePosCoef();
        if ($this->domER != null) {
            $this->solveExistingRules();
        } else {
            $this->finalJSON .= "\"rules\" : \"0\",";
        }
        $this->finalJSON .= "\"lang\" : \"" . $this->lang . "\",";
        $this->finalJSON = substr($this->finalJSON, 0, strlen($this->finalJSON) - 1);
        $this->finalJSON .= "}";
        return $this->finalJSON;
    }

    /**
     * It gets attribute from node based on the name of attribute.
     *
     * @param <DomNode> $node Node from which it should get the attribute.
     * @param <String>  $name name of attribute
     * @return <String> Content of the attribute
     */
    private function getAttribute($node, $name) {
        $nodeattributes = $node->attributes;
        foreach ($nodeattributes as $attribute) {
            if ($attribute->name == $name) {
                return $attribute->value;
            }
        }
        return "";
    }

    /**
     * It decides whether DomNode has attribute.
     *
     * @param <DomNode> $node Node by which I watn to decide whether it has attribute.
     * @param <String> $name Name of wanted attribute
     * @return <Boolean> True if it has attributes otherwise false.
     */
    private function hasAttributes($node, $name) {
        if ($this->getAttribute($node, $name) != "") {
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

        $antecedent = $this->domFL->getElementsByTagName("Antecedent")->item(0);
        if ($this->hasAttributes($antecedent, "minNumberOfBBAs")) {
            $antecedentMin = $this->getAttribute($antecedent, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($antecedent, "maxNumberOfBBA")) {
            $antecedentMax = $this->getAttribute($antecedent, "maxNumberOfBBA");
        }

        $consequent = $this->domFL->getElementsByTagName("Consequent")->item(0);
        if ($this->hasAttributes($consequent, "minNumberOfBBAs")) {
            $consequentMin = $this->getAttribute($consequent, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($consequent, "maxNumberOfBBA")) {
            $consequentMax = $this->getAttribute($consequent, "maxNumberOfBBA");
        }

        $IM = $this->domFL->getElementsByTagName("InterestMeasureConstraint")->item(0);
        if ($this->hasAttributes($IM, "minNumberOfInterestMeasures")) {
            $IMMin = $this->getAttribute($IM, "minNumberOfInterestMeasures");
        }
        if ($this->hasAttributes($IM, "maxNumberOfInterestMeasures")) {
            $IMMax = $this->getAttribute($IM, "maxNumberOfInterestMeasures");
        }

        $general = $this->domFL->getElementsByTagName("GeneralConstraint")->item(0);
        if ($this->hasAttributes($general, "minNumberOfBBAs")) {
            $generalMin = $this->getAttribute($general, "minNumberOfBBAs");
        }
        if ($this->hasAttributes($general, "maxNumberOfBBA")) {
            $generalMax = $this->getAttribute($general, "maxNumberOfBBA");
        }


        $this->finalJSON .= "\"minNumberBBA\": \"" . $generalMin . "\",";
        $this->finalJSON .= "\"maxNumberBBA\": \"" . $generalMax . "\",";
        $this->finalJSON .= "\"antMinNumberBBA\": \"" . $antecedentMin . "\",";
        $this->finalJSON .= "\"antMaxNumberBBA\": \"" . $antecedentMax . "\",";
        $this->finalJSON .= "\"consMinNumberBBA\": \"" . $consequentMin . "\",";
        $this->finalJSON .= "\"consMaxNumberBBA\": \"" . $consequentMax . "\",";
        $this->finalJSON .= "\"IMMinNumberBBA\": \"" . $IMMin . "\",";
        $this->finalJSON .= "\"IMMaxNumberBBA\": \"" . $IMMax . "\",";
    }

    /**
     * It gets from DataDictionary data which are known as attributes in ARBuilder
     * and add them to the finalJSON
     */
    private function solveData() {
        $field = $this->domDD->getElementsByTagName("Field");
        $json1 = "{\"attributes\": [ ";
        $json2 = "";
        foreach ($field as $elField) {
            $name = $elField->getAttribute("name");
            $json1 .= "\"" . $name . "\",";
            $fieldChildren = $elField->childNodes;
            $json2 .= "\"" . $name . "\": [ ";
            foreach ($fieldChildren as $elFieldChildren) {
                if ($elFieldChildren->nodeName == "Category") {
                    $json2 .= "\"" . $elFieldChildren->nodeValue . "\",";
                }
            }
            $json2 = substr($json2, 0, strlen($json2) - 1);
            $json2 .= "],";
        }
        $json1 = substr($json1, 0, strlen($json1) - 1);
        $json1 .= "],";
        $this->finalJSON .= $json1 . $json2;
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
        $json2 = "\"" . str_replace(" ", "", $name) . "F\" : [ ";
        $json3 = "\"" . str_replace(" ", "", $name) . "FLang\" : [ ";
        $json4 = "\"" . str_replace(" ", "", $name) . "MinValue\" : [ ";
        $json5 = "\"" . str_replace(" ", "", $name) . "MaxValue\" : [ ";
        $json6 = "\"" . str_replace(" ", "", $name) . "Datatype\" : [ ";
        foreach ($fields as $field) {
            if ($field->nodeName == "Name" && !$field->hasAttributes()) {
                $json2 .= "\"" . $field->nodeValue . "\",";
            }
            if ($field->nodeName == "LocalizedName" && ($field->getAttribute("lang") == $this->lang)) {
                $json3 .= "\"" . $field->nodeValue . "\",";
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
                $json4 .= "\"" . $minValue . "\",";
                $json5 .= "\"" . $maxValue . "\",";
                $json6 .= "\"" . $datatype . "\",";
            }
        }
        $json3 = substr($json3, 0, strlen($json3) - 1);
        $json3 .= "],";
        $json4 = substr($json4, 0, strlen($json4) - 1);
        $json4 .= "],";
        $json5 = substr($json5, 0, strlen($json5) - 1);
        $json5 .= "],";
        $json6 = substr($json6, 0, strlen($json6) - 1);
        $json6 .= "],";
        $json2 = substr($json2, 0, strlen($json2) - 1);
        $json2 .= "],";
        return $json2 . $json3 . $json4 . $json5 . $json6;
    }

    /**
     * It get all Type from InterestMeasures and so adds to finalJSON part containing
     * operators and operatorsLang and field Information about the Interest Measure
     * and solve supported Interest Measures
     */
    private function solveInterestMeasures() {
        // /InterestMeasures/Types/Type /Name -> jm�no /Field/Name -> jm�no pol��ka
        $xPath = new DOMXPath($this->domFL);
        $anXPathExpr = "//InterestMeasures/Types/Type";
        $types = $xPath->query($anXPathExpr);
        $json1 = "\"operators\" : [ ";
        $json3 = "\"operatorsLang\" : [ ";
        $json2 = "";
        foreach ($types as $typeS) {
            $typeChild = $typeS->childNodes;
            foreach ($typeChild as $type) {
                if ($type->nodeName == "Name" && !$type->hasAttributes()) {
                    $name = $type->nodeValue;
                    $json1 .= "\"" . $type->nodeValue . "\",";
                    $json7 = "\"" . str_replace(" ", "", $name) . "Expl\" : [ ";
                }
                if ($type->nodeName == "LocalizedName" && ($type->getAttribute("lang") == $this->lang)) {
                    $json3 .= "\"" . $type->nodeValue . "\",";
                }
                if ($type->nodeName == "Field") {
                    $json2 .= $this->solveIMFields($type, $name);
                }
                if ($type->nodeName == "Explanation" && ($type->getAttribute("lang") == $this->lang)) {
                    $json7 .= "\"" . $type->nodeValue . "\",";
                }
            }
        }
        $json1 = substr($json1, 0, strlen($json1) - 1);
        $json1 .= "],";
        $json3 = substr($json3, 0, strlen($json3) - 1);
        $json3 .= "],";
        $json7 = substr($json7, 0, strlen($json7) - 1);
        $json7 .= "],";
        $this->finalJSON .= $json1 . $json3 . $json2 . $json7;
        $this->solveSupportedIM();
    }

    /**
     * It solves which combinations of Interest Measures are supported and adds
     * them to final JSON
     */
    private function solveSupportedIM() {
        $supportedIMCombinations = $this->domFL->getElementsByTagName("SupportedIMCombination");
        $amount = 1;
        foreach ($supportedIMCombinations as $supportedIMCombination) {
            $supportedIM = "\"supIMCom" . $amount . "\": [ ";
            $supportedChildren = $supportedIMCombination->childNodes;
            foreach ($supportedChildren as $supChild) {
                if ($supChild->nodeName == "InterestMeasure") {
                    $supportedIM .= "\"" . $supChild->nodeValue . "\",";
                }
            }
            $supportedIM = substr($supportedIM, 0, strlen($supportedIM) - 1);
            $supportedIM .= "],";
            $amount++;
            $this->finalJSON .= $supportedIM;
        }
        $amount--;
        $this->finalJSON .= "\"supIMCombinations\":\"" . $amount . "\",";
    }

    /**
     * It solves which connectives are allowed on which depth of nesting and adds
     * it to depthNesting of JSON.
     */
    private function solveDepthNesting() {
        $LEVEL_REMAINING = "remaining";
        $maxLevel = $this->domFL->getElementsByTagName("MaxLevels")->item(0);
        $this->finalJSON .= "\"depthNesting\": \"" . $maxLevel->nodeValue . "\",";
        $nestingConstraint = $this->domFL->getElementsByTagName("NestingConstraint");
        $level = 0;
        foreach ($nestingConstraint as $constraint) {
            if ($constraint->getAttribute("level") != $LEVEL_REMAINING) {
                $level = $constraint->getAttribute("level");
            }
            $constraintChild = $constraint->childNodes;
            foreach ($constraintChild as $connective) {
                $connectiveChild = $connective->childNodes;
                foreach ($connectiveChild as $connegdis) {
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
                for ($i = $level + 1; $i <= $maxLevel->nodeValue; $i++) {
                    $this->finalJSON .= "\"depth" . $i . "\":[\"" . $dis . "\",\"" . $conj . "\",\"" . $neg . "\"],";
                }
            } else {
                $this->finalJSON .= "\"depth" . $level . "\":[\"" . $dis . "\",\"" . $conj . "\",\"" . $neg . "\"],";
            }
        }
    }

    /**
     * It is deprecated and ads the value of disjunction: false to final JSON.
     */
    private function solveDisjunction() {
        $this->finalJSON .= "\"disjunction\": \"false\",";
    }

    /**
     * It solves whether it is possible to create more than one rule.
     */
    private function solveMoreRules() {
        $moreRules = $this->domFL->getElementsByTagName("AllowMultipleRules")->item(0);
        $this->finalJSON .= "\"moreRules\": \"" . $moreRules->nodeValue . "\",";
    }

    /**
     * It solves which fields are possible to attributes.
     */
    private function solvePosCoef() {
        $xPath = new DOMXPath($this->domFL);
        $anXPathExpr = "//BasicBooleanAttribute/Coefficient/Type";
        $types = $xPath->query($anXPathExpr);
        $json1 = "\"posCoef\" : [ ";
        $json2 = "";
        foreach ($types as $typeS) {
            $typeChild = $typeS->childNodes;
            foreach ($typeChild as $type) {
                if ($type->nodeName == "Name" && !$type->hasAttributes()) {
                    $name = $type->nodeValue;
                    $json1 .= "\"" . $type->nodeValue . "\",";
                    $json3 .= "\"" . str_replace(" ", "", $name) . "Expl\" : [ ";
                }
                if ($type->nodeName == "Explanation" && ($type->getAttribute("lang") == $this->lang)) {
                    $json3 .= "\"" . $type->nodeValue . "\",";
                }
            }
            $json3 = substr($json3, 0, strlen($json3) - 1);
            $json3 .= "],";
            $json2 .= $this->solveAttrFields($typeS, $name);
        }
        $json1 = substr($json1, 0, strlen($json1) - 1);
        $json1 .= "],";
        $this->finalJSON .= $json1 . $json2 . $json3;
    }

    /**
     * It solves attribute fields.
     *
     * @param <DomNode> $type Node from which I get info
     * @param <String> $name Name of AttributeField
     * @return <String> final JSON containing attribute fields from data.
     */
    private function solveAttrFields($type, $name) {
        $fields = $type->childNodes;
        $json2 = "\"" . str_replace(" ", "", $name) . "F\" : [ ";
        $json3 = "\"" . str_replace(" ", "", $name) . "FLang\" : [ ";
        $json4 = "\"" . str_replace(" ", "", $name) . "MinValue\" : [ ";
        $json5 = "\"" . str_replace(" ", "", $name) . "MaxValue\" : [ ";
        $json6 = "\"" . str_replace(" ", "", $name) . "Datatype\" : [ ";
        foreach ($fields as $field1) {
            if ($field1->nodeName == "Field") {
                $fieldsSub = $field1->childNodes;
                foreach ($fieldsSub as $field) {
                    if ($field->nodeName == "Name" && !$field->hasAttributes()) {
                        $json2 .= "\"" . $field->nodeValue . "\",";
                    }
                    if ($field->nodeName == "LocalizedName" && ($field->getAttribute("lang") == $this->lang)) {
                        $json3 .= "\"" . $field->nodeValue . "\",";
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
                        $json4 .= "\"" . $minValue . "\",";
                        $json5 .= "\"" . $maxValue . "\",";
                        $json6 .= "\"" . $datatype . "\",";
                    }
                }
            }
        }
        $json3 = substr($json3, 0, strlen($json3) - 1);
        $json3 .= "],";
        $json4 = substr($json4, 0, strlen($json4) - 1);
        $json4 .= "],";
        $json5 = substr($json5, 0, strlen($json5) - 1);
        $json5 .= "],";
        $json6 = substr($json6, 0, strlen($json6) - 1);
        $json6 .= "],";
        $json2 = substr($json2, 0, strlen($json2) - 1);
        $json2 .= "],";
        return $json2 . $json3 . $json4 . $json5 . $json6;
    }

    private function solveExistingRules() {
        //echo "Solving Existing rules";
    }

}

?>