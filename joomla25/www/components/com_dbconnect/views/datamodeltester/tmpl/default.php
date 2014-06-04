<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.reloadBRBase();parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>Test classification model</h1>';

  if (isset($this->testFile)&&(file_exists($this->testFile))){

    echo '
          <script type="text/javascript">
            /*<![CDATA[*/
            window.addEvent("domready", function(){
                 modelTester = new ModelTester({
                  rulesExportUrl:"'.$this->rulesExportUrl.'",
                  testUrl:"'.$this->testUrl.'",
                  '.($this->ruleRemoveUrl?'removeRuleUrl:"'.$this->ruleRemoveUrl.'",':'').'
                  infoElementId:"loadingInfoDiv",
                  contentElementId:"contentDiv",
                });


                modelTester.runTest();
            });

            /* ]]>*/
          </script>';



    //TODO vykreslení výsledků
    echo '<div id="loadingInfoDiv">testing...</div>';
    echo '<div id="contentDiv">';

    echo '</div>';
  }else{
    echo '<div class="bigButtonsDiv">';
    echo '<h2>Testing dataset</h2>';
    echo '<a href="index.php?option=com_dbconnect&amp;controller=data&amp;task=modelTesterExportConnectionCSV&amp;tmpl=component&amp;kbi='.$this->kbi.'&amp;lmtask='.$this->lmtask.'&amp;rules='.$this->rules.'&amp;testData=db">Use training dataset</a>';
    //TODO potencionální poslední soubor
    echo '<a href="index.php?option=com_dbconnect&amp;controller=data&amp;task=modelTesterUploadCsv&amp;tmpl=component&amp;kbi='.$this->kbi.'&amp;lmtask='.$this->lmtask.'&amp;rules='.$this->rules.'&amp;testData=upload">Upload CSV with testing dataset</a>';
    echo '</div>';
  }

  //--vypsani jednotlivych pravidel
  echo '</div>';
  echo '</div>';  
?>