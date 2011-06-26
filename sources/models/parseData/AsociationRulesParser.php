<?php                                                      
/**
 * This class represents one DBA
 *
 * @author Jakub Balhar
 * @version 1.0
 */
class DBA{
    /**
     * It creates instance of this class based on params
     *
     * @param <DomNode> $dbaNode Node representing one DBA
     * @param <Array> $elements array containing elements of DBA
     */
    function  __construct($dbaNode, $elements) {
        $this->elements = $elements;
        $this->refs = array();
        $this->connective = "";
        $this->id = Utils::getAttribute($dbaNode, "id");
        $utils = new Utils();

        $dbas = $dbaNode->childNodes;
        $connective = $utils->getAttribute($dbaNode, "connective");
        if($connective == "Negation"){
            $this->connective = "NEG";
        }
        else if($connective == "Conjunction"){
            $this->connective = "AND";
        }
        else if($connective == "Disjunction"){
            $this->connective = "OR";
        }
        foreach ($dbas as $dba) {
            if($dba->nodeName == "BARef"){
                $this->refs[] = $dba->nodeValue;
            }
        }
    }

    /**
     * It is simple getter returning id
     *
     * @return <integer> id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * It creates JSON from this Object
     *
     * @return <String> JSON representing this Object
     */
    public function toJSON(){
        // Pro vsechny refs
        $elements = array();
        $elements[] = Utils::getBoolean("(", "lbrac");
        for($actualEl = 0; $actualEl < count($this->refs);$actualEl++){
            if($this->connective == "NEG"){
                $elements[] = Utils::getBoolean($this->connective, strtolower($this->connective));
            }
            // Proved toJSON a pole ktere vrati spoj.
            // Je jeste potreba nejprve podle id DBA ziskat
            $allElements = $this->elements[$this->refs[$actualEl]]->toJSON();
            for($el = 0; $el < count($allElements); $el++){
                $elements[] = $allElements[$el];
            }
            // dopln pole o boolean operator
            if($actualEl+1 < count($this->refs) && $this->connective != "NEG"){
                $elements[] = Utils::getBoolean($this->connective, strtolower($this->connective));
            }
        }
        $elements[] = Utils::getBoolean(")", "rbrac");
        return $elements;
    }
}

/**
 * This class represents one BBA
 *
 * @author Jakub Balhar
 * @version 1.0
 */
class BBA{
    /**
     * It creates instance of this class based on params
     *
     * @param <DomNode> $bbaNode Node representing one BBA
     */
    public function  __construct($bbaNode) {
        $this->fieldRef = "";
        $this->catRef = array();

        $bbaChildren = $bbaNode->childNodes;
        foreach ($bbaChildren as $bbaChild) {
            if($bbaChild->nodeName == "FieldRef"){
                $this->fieldRef = $bbaChild->nodeValue;
            }
            if($bbaChild->nodeName == "CatRef"){
                $this->catRef[] = $bbaChild->nodeValue;
            }
        }
        $utils = new Utils();
        $this->id = $utils->getAttribute($bbaNode,"id");
    }

    /**
     * It is simple getter returning id
     *
     * @return <integer> id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * It creates JSON from this Object
     *
     * @return <String> JSON representing this Object
     */
    public function toJSON(){
        $element = array();
        $element['name'] = $this->fieldRef;
        $element['type'] = "attr";
        $element['category'] = "One category";

        $fields = array();
        for($actualField = 0; $actualField < sizeof($this->catRef); $actualField++){
            $field = array();
            $field['name'] = "category";
            $field['value'] = $this->catRef[$actualField];

            $fields[] = $field;
        }

        $element['fields'] = $fields;
        return array($element);
    }
}

/**
 * This class represents one rule
 *
 * @author Jakub Balhar
 * @version 1.0
 */
class AsociationRule{
    /**
     * It creates AsociationRule based on the parameters
     *
     * @param <DomNode> $asociationRuleNode Node representing one rule
     * @param <DomDocument> $domER DomDocument representing XML containing rules
     */
    function  __construct($asociationRuleNode, $domER) {
        $this->antecedent = Utils::getAttribute($asociationRuleNode, "antecedent");
        $this->consequent = Utils::getAttribute($asociationRuleNode, "consequent");
        $this->interestMeasures = array();
        $elements = $asociationRuleNode->childNodes;
        foreach ($elements as $element){
            if($element->nodeName == "IMValue"){
              $this->interestMeasures[] = Utils::getIm($element, "name");
            }
        }    
        
        $this->elements = array();
        $elements = $domER->getElementsByTagName('BBA');
        foreach ($elements as $element){
            $bba = new BBA($element, $this->elements);
            $this->elements[$bba->getId()] = $bba;
        }
        $elements = $domER->getElementsByTagName('DBA');
        foreach ($elements as $element){
            $dba = new DBA($element, $this->elements);
            $this->elements[$dba->getId()] = $dba;
        }
    }

    /**
     * It gets all elements and creates JSON from them
     * 
     * @return <String> JSON representing Rules
     */
    public function toJSON(){
        $arrayOfElements = array();
        $antJson = $this->elements[$this->antecedent]->toJSON();
        foreach ($antJson as $element){
            $arrayOfElements[] = $element;
        }
        
        foreach($this->interestMeasures as $interestMeasure) {
          $im = array();
          $im['name'] = $interestMeasure['name'];
          $im['type'] = 'oper';
          $im['category'] = '';
          $im['fields'] = array();
          $im['fields'] = array(array('name' => 'prahovaHodnota', 'value' => $interestMeasure['value']));
          $arrayOfElements[] = $im;
        }
        
        $consJson = $this->elements[$this->consequent]->toJSON();
        foreach ($consJson as $element){
            $arrayOfElements[] = $element;
        }
        return $arrayOfElements;
    }
}

?>
