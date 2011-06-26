<?php       
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author balda
 */
class Utils{
    /**
     * It gets attribute from node based on the name of attribute.
     *
     * @param <DomNode> $node Node from which it should get the attribute.
     * @param <String>  $name name of attribute
     * @return <String> Content of the attribute
     */
    public static function getAttribute($node, $name) {
        if($node == null){
            return "";
        }

        $nodeattributes = $node->attributes;
        foreach ($nodeattributes as $attribute) {
            if ($attribute->name == $name) {
                return $attribute->value;
            }
        }
        return "";
    }

    public static function getBoolean($name, $type){
        $element = array();
        $element['name'] = $name;
        $element['type'] = $type;
        $element['category'] = "";
        $element['fields'] = array();
        return $element;
    }
    
    public static function getIm($node, $name) {
      $im = array('name' => $node->getAttribute($name), 'value' => $node->nodeValue);
      
      return $im;
    }
    
}

?>
