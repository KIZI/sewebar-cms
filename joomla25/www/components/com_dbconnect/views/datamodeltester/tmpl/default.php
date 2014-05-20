<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>Test classification model</h1>';

  if (isset($this->testFile)&&(file_exists($this->testFile))){
    echo '<script type="text/javascript">
            /*<![CDATA[*/

            function ModelTester(args){
              this.element=(args.element?args.element:"");
              this.rulesExportUrl=(args.rulesExportUrl?args.rulesExportUrl:"");
              this.testUrl=(args.testUrl?args.testUrl:"");

              this.runTest=function(){
                if (this.rulesExportUrl!=""){
                  //prepare XML
                  this.prepareRulesXmlFile();
                }else if(this.testUrl!=""){
                  //run test
                  this.testFiles();
                }else{
                  this.showResults();
                }
              }

              this.showResults=function(){
                alert ("show results");
              }

              this.showStatus=function(status){
                alert ("status is:"+status);
              }

              this.testFiles=function(){
                this.showStatus("testing...");
                new Request.JSON({
                  url:this.testUrl,
                  onComplete: function(){
                    this.testUrl="";
                    this.runTest();
                  }.bind(this)
                }).send();
              }

              this.prepareRulesXmlFile=function(){
                this.showStatus("preparing rules...");
                new Request.HTML({
                  url: this.rulesExportUrl,
                  onSuccess: function(responseText){
                    this.rulesExportUrl="";
                    this.runTest();

                  }.bind(this),
                  onFailure: function(){
                      alert("failure");
                  }

                }).send();
              }



            }

            window.addEvent("domready", function(){
                 modelTester = new ModelTester({
                  rulesExportUrl:"'.$this->rulesExportUrl.'",
                  testUrl:"'.$this->testUrl.'",
                });


                modelTester.runTest();
            });

            /* ]]>*/
          </script>';



    //TODO vykreslení výsledků
    echo '<div id="contentDiv">';
    echo '<h2>Testing...</h2>';
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