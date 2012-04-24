<?php

/**
 * Data parser
 *
 * @author Radek Skrabal <radek@skrabal.me>
 * @version 1.0
 */
class DataParser {

    private $DDPath;
    private $DD;
    private $FLPath;
    private $FL;
    private $FGC;
    private $FGCPath;
    private $ERPath;
    private $ER;
    private $ETreePath;
    private $ETree;
    private $lang;
    private $data;

    function __construct($DDPath, $FLPath, $FGCPath, $ERPath, $ETreePath, $lang) {
        $this->DDPath = $DDPath;
        $this->FLPath = $FLPath;
        $this->FGCPath = $FGCPath;
        $this->ERPath = $ERPath;
        $this->ETreePath = $ETreePath;
        $this->FAPath = $FAPath;
        $this->lang = $lang;
        $this->data = array();
    }

    public function loadData() {
        $this->DD = new DOMDocument('1.0', 'UTF-8');
        if (file_exists($this->DDPath)) {
            @$this->DD->load($this->DDPath, LIBXML_NOBLANKS); // throws notice due to the PI declaration
        } else {
            @$this->DD->loadXML($this->DDPath, LIBXML_NOBLANKS); // throws notice due to the PI declaration
        }

        $this->FL = new DOMDocument('1.0', 'UTF-8');
        if (file_exists($this->FLPath)) {
            $this->FL->load($this->FLPath, LIBXML_NOBLANKS);
        } else {
            $this->FL->loadXML($this->FLPath, LIBXML_NOBLANKS);
        }

        $this->FGC = new DOMDocument('1.0', 'UTF-8');
        if ($this->FGCPath !== null) {
            if (file_exists($this->FGCPath)) {
                $this->FGC->load($this->FGCPath, LIBXML_NOBLANKS);
            } else {
                $this->FGC->loadXML($this->FGCPath, LIBXML_NOBLANKS);
            }

        }

        $this->ER = new DOMDocument('1.0', 'UTF-8');
        if ($this->ERPath !== null) {
            if (file_exists($this->ERPath)) {
                $this->ER->load($this->ERPath, LIBXML_NOBLANKS);
            } else {
                $this->ER->loadXML($this->ERPath, LIBXML_NOBLANKS);
            }
        }
        
        $this->ETree = new DOMDocument('1.0', 'UTF-8');
        if ($this->ETreePath !== null) {
            if (file_exists($this->ETreePath)) {
                $this->ETree->load($this->ETreePath, LIBXML_NOBLANKS);
            } else {
                $this->ETree->loadXML($this->ETreePath, LIBXML_NOBLANKS);
            }
        }
    }

    public function parseData() {
        $DDParser = new DataDescriptionParser($this->DD);
        $this->data = array_merge_recursive($this->data, $DDParser->parseData());

        $FLParser = new FeatureListParser($this->FL, $this->lang);
        $this->data = array_merge_recursive($this->data, $FLParser->parseData());
        
        $FGCParser = new FieldGroupConfigParser($this->FGC, $this->data['attributes'], $this->data['BBA']['coefficients'], 
                             $this->lang);
        $this->data['fieldGroups'] = $FGCParser->parseConfig();
        
        $ERParser = new ExistingRulesParser($this->ER, $this->data['attributes'], $this->data['interestMeasures']);
        $this->data = array_merge_recursive($this->data, $ERParser->parseData());
        
        $ETreeParser = new ETreeParser($this->ETree, $this->FA);
        $this->data = array_merge_recursive($this->data, $ETreeParser->parseData());

        return $this->toJSON($this->data);
    }
    
    public function getER() {
        return $this->toJSON($this->data['existingRules']);
    }
    
    public function getRecommendedAttributes() {
        return $this->toJSON($this->data['recommendedAttributes']);
    }

    protected function toJSON($array) {
        if (function_exists('json_encode')) {
            $json = json_encode($array);
        } else {
            $JSON = new Services_JSON();
            $json = $JSON->encode($array);
        }

        return $json;
    }
}

?>
