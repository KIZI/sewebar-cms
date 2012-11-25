<?php
jimport( 'joomla.application.component.view' );
                       
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziUploadCSV_2 extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UPLOAD_CSV_FILE') );
                                                                
    JHtml::script('','',true);
    
    $document->addScriptDeclaration("
        
        window.addEvent('domready', function() {
            uploadcsv2preview();
          }
        );
        
        var paramsArr=new Array();
        var activeInput='';
        var timer;
        
        function setTimer(item){
          activeInput=item.name;
          timer=setInterval('checkParamsChange(\"\");',1000);
        }
        
        function clearTimer(item){
          if (item.name==activeInput){
            clearInterval(timer);
          }
          checkParamsChange(item.name)
        }
        
        function checkParamsChange(itemName){
          if (itemName==''){
            item=$(activeInput);
          }else{
            item=$(itemName);
          }
          if ((item.value.length>0)&&(paramsArr[item.name]!=item.value)){
            paramsArr[item.name]=item.value;
            uploadcsv2preview();
          }
        }
        
        function delimitierChange(){
          delimitier=$('delimitier').value;
          if (delimitier==''){        
            $('delimitier_text').style.display='block';
          }else{
            $('delimitier_text').style.display='none';
          }
        }
        
        function uploadcsv2preview(){
          delimitier=$('delimitier').value;  
          if (delimitier==''){
            delimitier=$('delimitier_text').value;
          }
          enclosure=$('enclosure').value;  
          if (enclosure.length==0){
            //enclosure='\"';
            //$('enclosure').value='\"';
          }
          encoding=$('encoding').value;
          escape=$('escape').value;
          if (escape.length==0){
            //$('escape').value='\\\\';
            //escape='\\\\';
          }
          
          var a = new Ajax( '".JRoute::_('index.php?option=com_dbconnect&controller=izi&format=raw&task=uploadCSV_getData&file='.$this->fileData->id)."', {
          	method: 'get',
          	/*update: $('previewDiv'),*/
            data: {'delimitier':delimitier,
                   'enclosure':enclosure,
                   'escape':escape,
                   'encoding':encoding},
            onComplete:function(response){   
              data=JSON.parse(response);    
              $('previewDiv').setHTML(data.html);
              $('rowsCount').setHTML(data.rows_count);
              $('colsCount').setHTML(data.cols_count);
              if (data.cols_count>150){
                $('colsCountWarning').style.display='block';
              }else{
                $('colsCountWarning').style.display='none';
              }
            }
          }).request();
          
          
          /*
          var req = new Request.HTML({
            method: 'get',
            url: '".JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=uploadCSV_getData&file='.$this->fileData->id)."',
            data: {  },
            onRequest: function() { alert('Request made. Please wait...'); },
            update: $('previewDiv'),
            onComplete: function(response) { 
                          alert('Request completed successfully.'); $('message-here').setStyle('background','#fffea1');
                        }
          }).send();
              */
          
          
        }
    
      ");    
		
    parent::display();		
  }
}
?>
