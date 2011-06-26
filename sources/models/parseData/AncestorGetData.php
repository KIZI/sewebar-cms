<?php                                                                          
/**
 * This class is only ancestor of specific implementation of getting Data classes
 * Every class which wants to be treated as get Data must implement this data.
 *
 * @author Jakub Balhar
 * @version 1.0
 */
abstract class AncestorGetData {
    /**
     * This creates instance of descendant.
     *
     * @param <String> $dataDescription Location of DataDescription
     * @param <String> $featureList     Location of FeatureList
     * @param <String> $existingRules   Location of Existig Rules
     * @param <String> $lang            Language which should be used
     */
    abstract public function  __construct($dataDescription, $featureList, $existingRules, $lang);

    /**
     *  This is supposed to get back the Data in format of JSON. JSON format used
     *  is documented elsewhere.
     */
    public abstract function getData();
}
?>
