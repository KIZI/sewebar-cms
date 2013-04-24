// JavaScript Document

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
    $('previewDiv').set('html','<div class=\"loadingDiv\">'+lang['LOADING']+'...</div>');
    $('reloadPreviewA').style.visibility='hidden';
  
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
                          alert('chciAjax');
    var a = new Request( {
      url:ajaxUrl,
    	method: 'get',
    	/*update: $('previewDiv'),*/
      data: {'delimitier':delimitier,
             'enclosure':enclosure,
             'escape':escape,
             'encoding':encoding},
      onRequest:function(){
        alert('request');
      },       
      onComplete:function(response){  alert('complete'); 
        $('reloadPreviewA').style.visibility='visible';
        data=JSON.parse(response);    
        $('previewDiv').set('html',data.html);
        $('rowsCount').set('html',data.rows_count);
        $('colsCount').set('html',data.cols_count);
        if (data.cols_count>150){
          $('colsCountWarning').style.display='block';
        }else{
          $('colsCountWarning').style.display='none';
        }
      }
    });
    
    
    /*
    var req = new Request.HTML({
      method: 'get',
      url: ajaxUrl,
      data: {  },
      onRequest: function() { alert('Request made. Please wait...'); },
      update: $('previewDiv'),
      onComplete: function(response) { 
                    alert('Request completed successfully.'); $('message-here').setStyle('background','#fffea1');
                  }
    }).send();
        */
    
    
  }
