<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>Exported DRL Rules</h1>';

  echo '<div class="font-style:italic;">In following code, variable blocks <strong>%RULEID%</strong> and <strong>%PROJECTID%</strong> should be replaced with IDs (while saving into drl rules base)</div>';
  echo '<div>';
  //vypsani jednotlivych pravidel
  $drlStr="import cz.vse.droolsserver.drools.DrlObj;\nimport cz.vse.droolsserver.drools.DrlResult;\n\n";

  if (count($this->drlXml->Rule)){
    foreach ($this->drlXml->Rule as $rule){
      $drlStr.="\n//".$rule->Text."\n";
      $drlStr.='rule "Rule_%RULEID%"'."\n";
      $drlStr.="when\n".trim($rule->Condition);
      $drlStr.="\nthen\n\t".trim($rule->Execute)."\nend\n\n";
    }
  }
  echo '<pre>'.htmlspecialchars($drlStr).'</pre>';
  //--vypsani jednotlivych pravidel
  echo '</div>';
  echo '</div>';  
?>