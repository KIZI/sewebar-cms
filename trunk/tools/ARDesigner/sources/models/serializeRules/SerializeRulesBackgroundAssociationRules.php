<?php

/**
 * Description of SerializeRules. It serializes just one Rule, if there are more rules
 * it serializes first one.
 *
 * @author balda
 */
class SerializeRulesBackgroundAssociationRules extends AncestorSerializeRules {

    private $id = 0;
    private $finalXMLDocument;
    private $antecedent = -1;
    private $consequent = -1;
    private $rule = array();
    private $rulePosition = -1;
    private $Dictionary;
    private $ARQuery;
    private $ONE_CATEGORY = "One Category";
    private $bbas = array();
    private $dbas = array();
    private $rules = array();

    /**
     * It creates instance of this class.
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * serializeRules, this one is main function and it gets JSON and returns
     * correct XML reprezantation of the data.
     */
    public function serializeRules($json) {
        // Create basic structure of Document.
        $this->createBasicStructure();
        // get Data from JSON
        $jsonData = json_decode($json);
        if ($jsonData->{'rules'} < 1) {
            return $this->finalXMLDocument->saveXML();
        }
        // Create BBAs and InterestMeasureSettings
        for($actualRule = 0; $actualRule < $jsonData->{'rules'}; $actualRule++){
            $rulePosition = 'rule'.$actualRule;
            $this->serializeRule($jsonData->{$rulePosition});
        }
        // Serialize XML

        $this->createXML();
        return $this->finalXMLDocument->saveXML();
    }

    /**
     * It creates final XML document from all BBAs, DBAs and AsociationRules
     */
    private function createXML(){
        $bbaLength = count($this->bbas);
        for($actualBba = 0; $actualBba < $bbaLength; $actualBba++){
            $this->ARQuery->appendChild($this->bbas[$actualBba]);
        }
        $dbaLength = count($this->dbas);
        for($actualDba = 0; $actualDba < $dbaLength; $actualDba++){
            $this->ARQuery->appendChild($this->dbas[$actualDba]);
        }
        $ruleLength = count($this->rules);
        for($actualRule = 0; $actualRule < $ruleLength; $actualRule++){
            $this->ARQuery->appendChild($this->rules[$actualRule]);
        }
    }

    /**
     * serializeRules, this one is main function and it gets array of elements
     * representing one rule nad creates appropriate XMLelements
     *
     * @param <Array> $ruleData Array of elements creating the rule.
     */
    private function serializeRule($ruleData){
        $IM = array();
        $IMValue = array();
        $this->rule = array();
        $this->rulePosition = -1;
        $ruleDataLength = count($ruleData);
        for ($ruleElement = 0; $ruleElement < $ruleDataLength; $ruleElement++) {
            $actualRuleElement = $ruleData[$ruleElement];
            if ($actualRuleElement->type == "attr") {
                $text = $actualRuleElement->name;
                $name = $actualRuleElement->name;

                $fieldRef = $actualRuleElement->{'name'};
                $fields = $actualRuleElement->{'fields'};
                $fieldName = $fields[0]->{'name'};
                $type = $actualRuleElement->{'category'};

                $category = null;
                $minLength = null;
                $maxLength = null;
                if ($type == $this->ONE_CATEGORY) {
                    $category = $fields[0]->value;
                } else {
                    $fieldLength = count($fields);
                    if($fieldLength < 1){
                        $minLength = "";
                    }
                    else{
                        $minLength = $fields[0]->{'value'};
                    }
                    if($fieldLength < 2){
                        $maxLength = "";
                    }
                    else{
                        $maxLength = $fields[1]->{'value'};
                    }
                }
                $this->createBBASetting($text, $fieldRef, $category);
            }
            if ($actualRuleElement->type == "oper") {
                $name = $actualRuleElement->name;
                $fields = $actualRuleElement->{'fields'};
                if(count($fields) > 0){
                    $value = $fields[0]->{'value'};
                }
                else{
                    $value = 0;
                }
                if($value == ""){
                    $value = 0;
                }
                $IM[] = $name;
                $IMValue[] = $value;
                $this->rule[$this->getNewRulePosition()] = "oper";
            }
            if ( $actualRuleElement->{'type'} == "rbrac" || $actualRuleElement->{'type'} == "lbrac" || $actualRuleElement->{'type'} == "and" || $actualRuleElement->{'type'} == "or" || $actualRuleElement->{'type'} == "neg"){
                $this->rule[$this->getNewRulePosition()] = $actualRuleElement->{'name'};
            }
        }

        // Create DBAs
        $this->createDBAs();

        $this->createInterestMeasureSetting($IM, $this->antecedent, $this->consequent, $IMValue);
    }

    /**
     * Create Basic Structure
     * <ARBuilder>
     *     <Dictionary></Dictionary>
     *     <AssociationRules>
     *     </AssociationRules>
     * </ARBuilder>
     */
   private function createBasicStructure() {
        $this->finalXMLDocument = new DomDocument("1.0", "UTF-8");

        $ARBuilder = $this->finalXMLDocument->createElement("ar:ARBuilder");
        $ARBuilder->setAttribute("xmlns:ar", "http://keg.vse.cz/ns/arbuilder0_1");
        $ARBuilder->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $ARBuilder->setAttribute("xsi:schemaLocation", "http://keg.vse.cz/ns/arbuilder0_1 http://sewebar.vse.cz/schemas/ARBuilder0_1.xsd");
        $ARBuilder->setAttribute("xmlns:guha", "http://keg.vse.cz/ns/GUHA0.1rev1");
        $root = $this->finalXMLDocument->appendChild($ARBuilder);

        $this->createDictionary($root);

        $ARQuery = $this->finalXMLDocument->createElement("AssociationRules");
        $this->ARQuery = $root->appendChild($ARQuery);
    }

    /**
     * Create Dictionary
     * It means get dictionary from elsewhere and just inject it here.
     */
    private function createDictionary($root) {
        $Dictionary = $this->finalXMLDocument->createElement("DataDescription");
        $this->Dictionary = $root->appendChild($Dictionary);
        // Get data from Session
        $domDD1 = $_SESSION["ARBuilder_domDataDescr"];
        // load XML
        $domDD = new DomDocument();
        $domDD->load($domDD1);
        // get <Dictionary>
        $fields = $domDD->getElementsByTagName("Dictionary");

        foreach($fields as $field ){
            $this->Dictionary->appendChild($this->finalXMLDocument->importNode($field, true));
        }
        //return $this->finalXMLDocument->createElement("Dictionary");
    }

    /**
     * It gets position of new rule. At the moment it just increments rulePosition
     * and return the number.
     *
     * @return <int> position of new rule
     */
    private function getNewRulePosition(){
        return ++$this->rulePosition;
    }

    /**
     * Create BBA
     * <BBA id="1">
     *       <Text>duration(Nad 2 roky vcetne)</Text>
     *       <FieldRef>duration</FieldRef>
     *       <CatRef>Nad 2 roky vcetne</CatRef>
     * </BBA>
     *
     * @param <String> $text Text of BBA
     * @param <String> $fieldRef FieldRef of BBA
     * @param <String> $category CatRef of BBA
     * @return <int> id id of this BBA
     */
    private function createBBASetting($text, $fieldRef, $category) {
        $BBASetting = $this->finalXMLDocument->createElement("BBA");
        $id = $this->getNewID();
        $position = $this->getNewRulePosition();
        $this->rule[$position] = $id;
        $BBASetting->setAttribute("id", $id);

        $Text = $this->finalXMLDocument->createElement("Text");
        $Text->appendChild($this->finalXMLDocument->createTextNode($text));

        $FieldRef = $this->finalXMLDocument->createElement("FieldRef");
        $FieldRef->appendChild($this->finalXMLDocument->createTextNode($fieldRef));

        $Name = $this->finalXMLDocument->createElement("CatRef");
        $Name->appendChild($this->finalXMLDocument->createTextNode($category));

        $BBASetting->appendChild($Text);
        $BBASetting->appendChild($FieldRef);
        $BBASetting->appendChild($Name);
        
        $this->bbas[] = $BBASetting;
    }

    /**
     * At the moment it just increments id and return it back.
     *
     * @return <int> id
     */
    private function getNewID() {
        return ++$this->id;
    }

    /**
     * Create DBASetting
     * <DBA id="7" connective="Conjunction">
     *      <BARef>1</BARef>
     *      <BARef>6</BARef>
     * </DBA>
     *
     * @param <String> $type type of connective
     * @param <Array> $elements elements BARef
     * @return <int> id of DBA
     */
    private function createDBASetting($type, $elements) {
        if($type == "(" || $type == ")" || $type == null){
            return;
        }

        $types = array();
        $types["NEG"] = "Negation";
        $types["AND"] = "Conjunction";
        $types["OR"] = "Disjunction";
        $types["Literal"] = "Literal";

        $DBASetting = $this->finalXMLDocument->createElement("DBA");
        $DBASetting->setAttribute("connective", $types[$type]);
        $id = $this->getNewID();
        $DBASetting->setAttribute("id", $id);

        if ($elements != null) {
            for ($i = 0; $i < count($elements); $i++) {
                $BASettingRef = $this->finalXMLDocument->createElement("BARef");
                $BASettingRef->appendChild($this->finalXMLDocument->createTextNode($elements[$i]));
                $DBASetting->appendChild($BASettingRef);
            }
        }

        $this->dbas[] = $DBASetting;

        return $id;
    }

    /**
     * Solve DBAs from the rule.
     */
    private function createDBAs() {
        $workingRule = $this->rule;
        // change negations into DBA and create corresponding DBAs
        for ($i = 0; $i < count($workingRule); $i++) {
            if ($workingRule[$i] == "NEG") {

                // create XML element DBA and insert it into tree
                $connective = $workingRule[$i];
                unset($elements);
                $elements = array();
                $elements[0] = $workingRule[$i + 1];
                $dbaID = $this->createDBASetting($connective, $elements);

                // In the rule replace part with negation by new DBA(Derived Boolean Atrtibute)
                $from = $i;
                $to = $i + 1;
                $workingRule = $this->replacePartWithDBA($workingRule, $dbaID, $from, $to);
            }
        }
        // while bracket exists
        while ($this->existsBracket($workingRule)) {
            // find deepest brackets
            $rbrac = $this->getRbrac($workingRule);
            $lbrac = $this->getLbrac($workingRule, $rbrac);
            // change content of brackets into DBAs
            $rulePartPos = 0;
            unset($rulePart);
            $rulePart = array();

            for ($j = $lbrac + 1; $j < $rbrac; $j++) {
                $rulePart[$rulePartPos] = $workingRule[$j];
                $rulePartPos++;
            }

            $dbaPos = "";
            if(count($rulePart) > 0){
                $dpaPos = $this->solvePlainDBA($rulePart);
            }
            $workingRule = $this->replacePartWithDBA($workingRule, $dpaPos, $lbrac, $rbrac);
        }
        $ruleAntecedent = array();
        $ruleAntPos = 0;
        $ruleConsequent = array();
        $ruleConPos = 0;
        $isConsequent = false;
        for ($k = 0; $k < count($workingRule); $k++) {
            if ($workingRule[$k] == "oper") {
                $isConsequent = true;
                continue;
            }
            if (!$isConsequent) {
                $ruleAntecedent[$ruleAntPos] = $workingRule[$k];
                $ruleAntPos++;
            } else {
                $ruleConsequent[$ruleConPos] = $workingRule[$k];
                $ruleConPos++;
            }
        }
        // change antecedent for one DBA
        $this->antecedent = $this->solvePlainDBA($ruleAntecedent);
        // change consequent for one DBA
        if(count($ruleConsequent) > 0){
            $this->consequent = $this->solvePlainDBA($ruleConsequent);
        }
        else{
            $this->consequent = "";
        }

    }

    /**
     * Whether there are any more brackets.
     *
     * @param <Array> $rule Rule
     * @return <Boolean>
     */
    private function existsBracket($rule) {
        if ($this->getRbrac($rule) != -1) {
            return true;
        }
        return false;
    }

    /**
     * It gets part of rule that is constructed only from attributes, AND, OR
     * It returns one number representing the whole thing(id)
     *
     * @param <Array> $inputRulePart Array of rule elements
     * @return <int>  id of this DBA
     */
    private function solvePlainDBA($inputRulePart) {
        $rulePart = $inputRulePart;
        // Until rulePart has only rule
        while(count($rulePart) > 1){
            // Find from beginning part of inputRule which has same connective.
            $sameConectiveRule = array();
            $actualConnective = null;
            // get only the correct(with same connective) part of rule
            $beginReplacing = 0;
            $endReplacing = count($rulePart);
            $elementsToBeReplaced = array();
            $elementsToBeReplacedPos = 0;
            for($positionInRule = 0; $positionInRule < count($rulePart); $positionInRule++){
                // If boolean
                $ruleType = $rulePart[$positionInRule];
                if($ruleType == "AND" or $ruleType == "OR" or $ruleType == "NEG"){
                    // if first found boolean set it as actual
                    if($actualConnective == null) {
                        $actualConnective = $rulePart[$positionInRule];
                        continue;
                    }
                    // check whether it is same as actual
                    // if it is not
                    if($actualConnective != $rulePart[$positionInRule]){
                        $endReplacing = $positionInRule-1;
                        break;
                        // break.
                    }
                }
                // else
                else {
                    // Add actual element into elements to be replaced
                    $elementsToBeReplaced[$elementsToBeReplacedPos++] = $rulePart[$positionInRule];
                }
            }
            // Create correct DBA.
            $type = $actualConnective;
            $elements = $elementsToBeReplaced;
            $replaceWith = $this->createDBASetting($type, $elements);
            // Replace this part with one number.
            $from = $beginReplacing;
            $to = $endReplacing;
            $rulePart = $this->replacePartWithDBA($rulePart, $replaceWith, $from, $to);
        }
        return $rulePart[0];
    }

    /**
     * It takes rule and replace part of it with just one int representing either
     * BBA or DBA
     *
     * @param <Array> $rule rule
     * @param <int> $replaceWith with what it shoiuld be replaced(id of either DBA or BBA)
     * @param <int> $from start of replacing
     * @param <int> $to end of replacing
     * @return <Array> rule
     */
    private function replacePartWithDBA($rule, $replaceWith, $from, $to) {
        $rule[$from] = $replaceWith;
        for ($i = $from + 1; $i <= $to; $i++) {
            unset($rule[$i]);
        }

        $help = array_values($rule);
        $rule = $help;

        return $rule;
    }

    /**
     * It gets position of RightBracket in the rule or -1 if there is no )
     *
     * @param <Array> $where rule
     * @return int
     */
    private function getRbrac($where) {
        for ($i = 0; $i < count($where); $i++) {
            if ($where[$i] == ")") {
                return $i;
            }
        }
        return -1;
    }

    /**
     * It gets position of first left Bracket in the rule before position of right
     * bracket or -1 if there is no (
     *
     * @param <Array> $where rule
     * @return int
     */
    private function getLbrac($where, $rBracPos) {
        for ($i = $rBracPos; $i >= 0; $i--) {
            if ($where[$i] == "(") {
                return $i;
            }
        }
        return -1;
    }

    /**
     * It decides whether element is boolean.
     *
     * @param <String> $ruleType  type of Element
     * @return <Boolean> Whetre type is boolean
     */
    private function isBoolean($ruleType) {
        if ($ruleType == "AND" or $ruleType == "OR" or $ruleType == "NEG" or $ruleType == "(" or $ruleType == ")") {
            return true;
        }
        return false;
    }

    /**
     * Create InterestMeasureSetting
     * <AssociationRule antecedent="7" consequent="2">
     *      <IMValue name="Support">0.55282316777220514479</IMValue>
     *      <IMValue name="Average Difference" imSettRef="2">0.1414</IMValue>
     *      <IMValue name="Kulczynski" imSettRef="3">0.8138</IMValue>
     *      <FourFtTable a="3586" b="874" c="768" d="953"/>
     *  </AssociationRule>
     *
     * @param <Array> $name Names of interest measures involved in rule.
     * @param <int> $antecedent number of antecedent
     * @param <int> $consequent number of consequent
     */
    private function createInterestMeasureSetting($name, $antecedent, $consequent, $value) {
        $InterestMeasureTreshold = $this->finalXMLDocument->createElement("AssociationRule");
        $InterestMeasureTreshold->setAttribute("antecedent", $antecedent);
        $InterestMeasureTreshold->setAttribute("consequent", $consequent);
        $InterestMeasure = null;

        $namesCount = count($name);
        for($i = 0; $i < $namesCount; $i++){
            $InterestMeasure = $this->finalXMLDocument->createElement("IMValue");
            $InterestMeasure->setAttribute("name", $name[$i]);
            $InterestMeasure->appendChild($this->finalXMLDocument->createTextNode($value[$i]));
            $InterestMeasureTreshold->appendChild($InterestMeasure);
        }

        $this->rules[] = $InterestMeasureTreshold;
    }

    /**
     * It prepares data of one rule.
     *
     * @param <String> $elements JSON
     */
    function prepareData($elements) {
        $obj = json_decode($elements);
        $obj->{'rules'};
        $ruleData = $obj->{'rule' . $i}; // this is array
    }

}

?>