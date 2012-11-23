// JavaScript Document
var $jq = jQuery.noConflict();
var sessionData;
var legendData;
               
function noNullSession(){
  if(!sessionData.ignoreArr){
    sessionData.ignoreArr=new Array();
  }
  if(!sessionData.userArr){
    sessionData.userArr=new Array();
  }
  if (!sessionData.usedKeys2Arr){
    sessionData.usedKey2Arr=new Array();
  }
  if (!sessionData.keysArr){
    sessionData.keysArr=new Array();
  }
  if (!sessionData.assignArr){
    sessionData.assignArr=new Array();
  }
}
  
function loadLegendData(){         
  $jq.getJSON('index.php?option=com_mapping&task=useredit&action=jsonLegend&format=raw&no_html=1',function(data){
    legendData=data.legendArr;
    defaultData=data.defaultDataArr;                                          
    loadData();
  });
}  
             
function loadData(){                      
  $jq.getJSON('index.php?option=com_mapping&task=useredit&action=json&format=raw&no_html=1', function(data) {  
    sessionData=data;                 
    noNullSession();                          
    $jq("#contentDiv").html('');
    /*vytvorime odstavce pro jednotlive klice A*/
    if (data.keysArr.length>0){
      for (var i = 0; i < data.keysArr.length; i++){  
        var keyName=data.keysArr[i];             
        assignText = getAssignHtml(keyName);   
        if (sessionData.userArr[keyName]){
          //priradime oznaceni ke spojenim vytvorenym/potvrzenym uzivatelem
          classString='userMerge';
        }else if(sessionData.assignArr[keyName]){
          classString='autoMerge';
        }else if(jQuery.inArray(keyName,sessionData.ignoreArr)>-1){
          classString='ignoreMerge';
        }else{
          classString='nonMerge';
        }                                              
        $jq("#contentDiv").append('<div id="'+keyName+'_div" class="'+classString+'"><div class="nameDiv">'+legendData[keyName]+'</div><div id="'+keyName+'_assignDiv">'+assignText+'</div></div>');  
      }
    }else{
      window.alert(NO_ITEMS_FOUND);
    } 
  });
}
      
function reloadData(url){   
  saveScroll();
  $jq.getJSON(url, function(data) {  
    sessionData=data;
    noNullSession();
    //projdeme jednotlive klice a aktualizujeme jejich zobrazeni
    for (var i = 0; i < sessionData.keysArr.length; i++) {  
      var keyName=sessionData.keysArr[i];
      assignText = getAssignHtml(keyName); 
      if (sessionData.userArr[keyName]){
        //priradime oznaceni ke spojenim vytvorenym/potvrzenym uzivatelem
        classString='userMerge';
      }else if(sessionData.assignArr[keyName]){
        classString='autoMerge';
      }else if(jQuery.inArray(keyName,sessionData.ignoreArr)>-1){
          classString='ignoreMerge';
      }else{
        classString='nonMerge';
      }
      //zmenime class
      remClass=$jq("#"+keyName+"_div").attr('class');
      $jq("#"+keyName+"_div").removeClass(remClass);
      $jq("#"+keyName+"_div").addClass(classString);
      //zmenime info o propojeni
      $jq("#"+keyName+"_assignDiv").html(assignText);
    } 
    refreshScroll();
  });
}   

function getAssignHtml(keyName){     
  if (sessionData.assignArr[keyName]){         
    assignName=sessionData.assignArr[keyName]['name']; 
    var itemValue='-';
    if (sessionData.assignArr[keyName]['value']){
      itemValue=Math.round(sessionData.assignArr[keyName]['value']['ratio']*1000)/1000;
    }
    assignText='<span class="assignName">'+legendData[assignName]+'</span>'+'<span class="ratio"  title="'+getRatioTitle(sessionData.assignArr[keyName]['value'])+'">('+itemValue+')</span>';
    if (!sessionData.userArr[keyName]){          
      assignText+='<a href="javascript:addMerge(\''+keyName+'\',\''+sessionData.assignArr[keyName]['name']+'\');">'+CONFIRM_MAPPING+'...</a> | ';
      assignText+='<a href="javascript:addIgnore(\''+keyName+'\');">'+IGNORE_THIS_KEY+'...</a> | ';
    }else{
      sessionData.usedKey2Arr.push(sessionData.assignArr[keyName]['name']);
      assignText+='<a href="javascript:removeMerge(\''+keyName+'\',\''+sessionData.assignArr[keyName]['name']+'\');">'+CANCEL_MAPPING+'...</a> | ';
    }                        
    assignText+='<a href="javascript:showItems(\''+keyName+'\');">'+SELECT_OTHER_ITEM+'...</a> | ';   
    assignText+='<a href="javascript:showMultimergeItems(\''+keyName+'\');">'+SELECTED_OTHER_ITEM_INCLUDE_USED+'...</a>';
    
  }else{
    if (jQuery.inArray(keyName,sessionData.ignoreArr)>-1){
      assignText='<a href="javascript:removeIgnore(\''+keyName+'\');">'+CANCEL_IGNORE_KEY+'...</a>';
    }else{
      assignText='<a href="javascript:showItems(\''+keyName+'\');">'+SELECT_ITEM+'...</a> | ';
      assignText+='<a href="javascript:showMultimergeItems(\''+keyName+'\');">'+SELECTED_ITEM_INCLUDE_USED+'...</a>';
    }
  }
  return assignText;
}

/**
 *  Funkce zobrazující položky, které jdou přiřadit ke klíči
 */             
function showItems(key){
  var htmlArr = new Array();
  for(var i=0;i<sessionData.keys2Arr.length;i++){
    //projdeme všechny přiřaditelné položky pole
    keyName=sessionData.keys2Arr[i];
    if (jQuery.inArray(keyName,sessionData.usedKey2Arr)<0){
      if (sessionData.assignArr[key]){
        if (sessionData.assignArr[key]['name']==keyName){
          classString="selectedItem";
        }else{
          classString="";
        }
      }
      similarity='-';
      similarityTitle='';
      if (defaultData[key]){
        if (defaultData[key][keyName]){
          similarity=Math.round(defaultData[key][keyName]['ratio']*1000)/1000;
          similarityTitle=' title="'+getRatioTitle(defaultData[key][keyName])+'"';
        }
      }
      htmlArr.push('<a href="javascript:addMerge(\''+key+'\',\''+keyName+'\')" class="'+classString+'">'+legendData[keyName]+'</a> <span class="ratio"'+similarityTitle+'>('+similarity+')</span>');

    }else{
      //jde o pouzitou polozku
      if (sessionData.assignArr[key]){
        if (sessionData.assignArr[key]['name']==keyName){
          //jde o prirazeny typ...
          htmlArr.push('<strong>'+legendData[keyName]+'</strong> <span class="ratio">('+Math.round(sessionData.assignArr[key]['value']['ratio']*1000)/1000+')</span>');
        }
      }
    }
  }
  //mame pripravene odkazy => zobrazime je
  $jq("#"+key+"_assignDiv").html("<div class=\"itemsDiv\">"+htmlArr.join('<br />')+"<br /><br /><a href=\"javascript:hideItems('"+key+"');\">"+CANCEL+"...</a></div>");
}

/**
 *  Funkce zobrazující položky, které jdou přiřadit ke klíči (včetně již použitých)
 */             
function showMultimergeItems(key){
  var htmlArr = new Array();
  for(var i=0;i<sessionData.keys2Arr.length;i++){
    //projdeme všechny přiřaditelné položky pole
    keyName=sessionData.keys2Arr[i];  
    //připravíme informace o podobnosti
    similarity='-';
    similarityTitle='';
    if (defaultData[key]){
      if (defaultData[key][keyName]){
        similarity=Math.round(defaultData[key][keyName]['ratio']*1000)/1000;
        similarityTitle=' title="'+getRatioTitle(defaultData[key][keyName])+'"';
      }
    }           
    //zjistime, jestli jde o vybranou položku  
    if ((jQuery.inArray(keyName,sessionData.usedKey2Arr)<0)||(!(key in sessionData.assignArr))){
      if (sessionData.assignArr[key]){
        if (sessionData.assignArr[key]['name']==keyName){
          classString="selectedItem";
        }else{
          classString="";
        }
      }
      htmlArr.push('<a href="javascript:addMultiMerge(\''+key+'\',\''+keyName+'\')" class="'+classString+'">'+legendData[keyName]+'</a> <span class="ratio"'+similarityTitle+'>('+similarity+')</span>');
    }else{
      //jde o pouzitou polozku
      if (sessionData.assignArr[key]['name']==keyName){
        //jde o prirazeny typ...
        htmlArr.push('<strong>'+legendData[keyName]+'</strong> <span class="ratio" title="'+getRatioTitle(sessionData.assignArr[key]['value'])+'">('+Math.round(sessionData.assignArr[key]['value']['ratio']*1000)/1000+')</span>');
      }else{
        htmlArr.push('<a href="javascript:addMultiMerge(\''+key+'\',\''+keyName+'\')">'+legendData[keyName]+'</a> <span class="ratio"'+similarityTitle+'>('+Math.round(defaultData[key][keyName]['ratio']*1000)/1000+')</span>');
      }
    }
  }
  //mame pripravene odkazy => zobrazime je
  $jq("#"+key+"_assignDiv").html("<div class=\"itemsDiv\">"+htmlArr.join('<br />')+"<br /><br /><a href=\"javascript:hideItems('"+key+"');\">"+CANCEL+"...</a></div>");
}

/**
 *  Funkce pro skrytí položek přiřaditelných ke klíči
 */             
function hideItems(key){
  $jq("#"+key+"_assignDiv").html(getAssignHtml(key));
}

/**
 *  Funkce pro skrytí položek přiřaditelných ke klíči
 */             
function forceHideItems(key){
  $jq("#"+key+"_assignDiv").html('');
}

function getRatioTitle(dataArr){     
 if(dataArr){                       
   return KEY_NAMES+": "+(Math.round(dataArr['ratioArr']['names']*1000)/1000)+"; "+VALUES+": "+(Math.round(dataArr['ratioArr']['values']*1000)/1000)+"; "+EXPIRIENCES+": "+(Math.round(dataArr['ratioArr']['expirience']*1000)/1000);
 }else{
   return '';
 }
}

/*****************************************************************************************************************************************/
/*uživatelské funkce*/
function addMerge(key,key2){  
  forceHideItems(key);
  reloadData("index.php?option=com_mapping&task=useredit&no_html=1&format=raw&action=addMerge&keyA="+key+"&keyB="+key2);
}
function addMultiMerge(key,key2){  
  forceHideItems(key);
  reloadData("index.php?option=com_mapping&task=useredit&no_html=1&format=raw&action=addMerge&keyA="+key+"&keyB="+key2);
}
function removeMerge(key,key2){
  forceHideItems(key);
  reloadData("index.php?option=com_mapping&task=useredit&no_html=1&format=raw&action=removeMerge&keyA="+key+"&keyB="+key2);
}
function addIgnore(key){
  forceHideItems(key);
  reloadData("index.php?option=com_mapping&task=useredit&no_html=1&format=raw&action=addIgnore&keyA="+key);
}
function removeIgnore(key){
  forceHideItems(key);
  reloadData("index.php?option=com_mapping&task=useredit&no_html=1&format=raw&action=removeIgnore&keyA="+key);
}
