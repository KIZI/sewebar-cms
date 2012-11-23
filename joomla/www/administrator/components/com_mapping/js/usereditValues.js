// JavaScript Document
var $jq = jQuery.noConflict();
var legendData;
var valuesMapData;
var sessionData;
var viewMode;

Array.prototype.in_array = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++) {
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
}


function noNullSession(){
  if(!sessionData.finalArr){
    sessionData.finalArr=new Array();
  }
  if (!viewMode){
    viewMode=new Array();
  }
}
  
function loadValuesLegendData(){
  $jq.getJSON('index.php?option=com_mapping&task=usereditValues&action=jsonLegend&format=raw&no_html=1',function(data){
    legendData=data.legendArr;   
    valuesMapData=data.valuesMapArr; 
    loadValuesData(); 
  });
}  
             
function loadValuesData(){                      
  $jq.getJSON('index.php?option=com_mapping&task=usereditValues&action=json&format=raw&no_html=1', function(data) {  
    sessionData=data;   
    noNullSession();                          
    $jq("#contentDiv").html('');            
    /*vytvorime odstavce pro jednotlive pary klicu*/   
    for (var keyName in data.finalArr){  
      assignText = getAssignHtml(keyName); 
      if (assignText!=""){
        var divClass='keyDiv';
      }else{
        var divClass='noDIv';
      }
      $jq("#contentDiv").append('<div id="'+keyName+'_div" class="'+divClass+'">'+assignText+'</div>');
      viewMode[keyName]='a';
    }
    $jq('.viewA').show();
  });
}

/**
 *  Funkce pro vygenerování obsahu jednoho klíče
 */ 
function getAssignHtml(keyName,generateDiv){            
  var arr = sessionData.finalArr[keyName];  
  var nameB=arr.name; 
  if (!nameB){
    return '';
  }else{                                  
    //vytvorime zakladni DIV
    var returnString =  '<div class="nameDiv">'+legendData[keyName]+' &lt;=&gt; '+legendData[nameB]+'<a href="javascript:showA(\''+keyName+'\');">&laquo;</a><a href="javascript:showB(\''+keyName+'\');">&raquo;</a></div>'+
                        '<div class="valuesDiv"><div id="'+keyName+'_viewA" class="viewA"><table>';  
    //projdeme jednotlive hodnoty
    var usedValuesB=new Array();
    var transponedMappedValues=Array();//zároveň vytvoříme i transponované pole s přiřazením hodnot z A k hodnotám z B
    var transponedAutoMappedValues=Array();
    if (arr.valuesA){
      if (arr.valuesA.length>0){     
        for(i=0;i<arr["valuesA"].length;i++){               
          valueA=arr["valuesA"][i];
          returnString+='<tr>'+
                          '<td class="nameTd">'+
                            valuesMapData[valueA]+
                          '</td>'+
                          '<td>';
                            /*uživatelsky namapované hodnoty*/
                            if (arr.mappedValues){
                              if (arr.mappedValues[valueA]){
                                //máme nějaké přiřazené hodnoty...
                                pocetNamapovanychHodnot=arr.mappedValues[valueA].length;
                                for(x=0;x<pocetNamapovanychHodnot;x++){
                                  valueB=arr.mappedValues[valueA][x];
                                  usedValuesB.push(valueB);
                                  if (!transponedMappedValues[valueB]){
                                    transponedMappedValues[valueB]=new Array();
                                  }
                                  transponedMappedValues[valueB].push(valueA);
                                  returnString+='<div class="userMappedValueDiv"><strong>'+valuesMapData[valueB]+'</strong>';
                                  returnString+='<div class="linksDiv"><a href="javascript:unconfirmMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+UNCONFIRM+'...</a> | ';
                                  returnString+='<a href="javascript:removeMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CANCEL_MAPPING+'...</a></div>';
                                  returnString+='</div>';
                                }
                              }
                            }
                            /*automaticky namapované hodnoty*/
                            if (arr.autoMappedValues){
                              if (arr.autoMappedValues[valueA]){
                                //máme nějaké přiřazené hodnoty...
                                pocetNamapovanychHodnot=arr.autoMappedValues[valueA].length;
                                for(x=0;x<pocetNamapovanychHodnot;x++){
                                  valueB=arr.autoMappedValues[valueA][x];
                                  usedValuesB.push(valueB);
                                  if (!transponedAutoMappedValues[valueB]){
                                    transponedAutoMappedValues[valueB]=new Array();
                                  }                                                          
                                  transponedAutoMappedValues[valueB].push(valueA);
                                  returnString+='<div class="autoMappedValueDiv"><strong>'+valuesMapData[valueB]+'</strong>';
                                  returnString+='<div class="linksDiv"><a href="javascript:addMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CONFIRM_MAPPING+'...</a> | ';
                                  returnString+='<a href="javascript:removeMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CANCEL_MAPPING+'...</a></div>';
                                  returnString+='</div>';
                                }
                              }
                            }                     
          returnString+=    '<div id="'+keyName+'_'+valueA+'_AmorevaluesDiv">'+
                              '<a href="javascript:showMoreValuesA(\''+keyName+'\',\''+valueA+'\',0);"><strong>+</strong> '+ADD_UNUSED_ITEM+'...</a> | '+
                              '<a href="javascript:showMoreValuesA(\''+keyName+'\',\''+valueA+'\',1);"><strong>+</strong> '+ADD_ITEM+'...</a>'+
                            '</div>';                  
          returnString+=  '</td>'+
                        '</tr>';
        }                        
      }
    }           
    if (arr.valuesB){
      if (arr.valuesB.length>0){
        for(i=0;i<arr["valuesB"].length;i++){
          valueB=arr["valuesB"][i];
          if(!usedValuesB.in_array(valueB)){
            returnString+='<tr>'+
                            '<td width="50%">'+
                            '</td>'+
                            '<td>'+
                              '<div class="noMappedValueDiv">'+valuesMapData[valueB]+"</div>"+
                            '<td>'+
                          '</tr>';
          }
            
        }
      }
    }                      
    //uzavreni zakladniho DIVu
    returnString+=                             '</table></div>'+
                                               '<div id="'+keyName+'_viewB" class="viewB"><table>';   
    //div a tabulka pro opacne zobrazeni
    var usedValuesA=new Array();
    if (arr.valuesB)
      if (arr.valuesB.length>0){                
        for(i=0;i<arr["valuesB"].length;i++){
          valueB=arr["valuesB"][i];
          returnString+='<tr><td>';
          //uzivatelsky namapovane hodnoty
          if (transponedMappedValues[valueB]){
            pocetNamapovanychHodnot=transponedMappedValues[valueB].length;
          }else{
            pocetNamapovanychHodnot=0;
          }
          if (pocetNamapovanychHodnot>0){
            for(x=0;x<pocetNamapovanychHodnot;x++){
              valueA=transponedMappedValues[valueB][x];
              usedValuesA.push(valueA);
              returnString+='<div class="userMappedValueDiv"><strong>'+valuesMapData[valueA]+'</strong>';
              returnString+='<div class="linksDiv"><a href="javascript:unconfirmMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+UNCONFIRM+'...</a> | ';
              returnString+='<a href="javascript:removeMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CANCEL_MAPPING+'...</a></div>';
              returnString+='</div>';
            }
          }
          //autonamapovane hodnoty
          if (transponedAutoMappedValues[valueB]){
            pocetNamapovanychHodnot=transponedAutoMappedValues[valueB].length;
          }else{
            pocetNamapovanychHodnot=0;
          }
          if (pocetNamapovanychHodnot>0){
            for(x=0;x<pocetNamapovanychHodnot;x++){
              valueA=transponedAutoMappedValues[valueB][x];
              usedValuesA.push(valueA);
              returnString+='<div class="autoMappedValueDiv"><strong>'+valuesMapData[valueA]+'</strong>';
              returnString+='<div class="linksDiv"><a href="javascript:addMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CONFIRM_MAPPING+'...</a> | ';
              returnString+='<a href="javascript:removeMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\')">'+CANCEL_MAPPING+'...</a></div>';
              returnString+='</div>';
            }
          }
          //
          returnString+=    '<div id="'+keyName+'_'+valueB+'_BmorevaluesDiv">'+
                              '<a href="javascript:showMoreValuesB(\''+keyName+'\',\''+valueB+'\',0);"><strong>+</strong> '+ADD_UNUSED_ITEM+'...</a> | '+
                              '<a href="javascript:showMoreValuesB(\''+keyName+'\',\''+valueB+'\',1);"><strong>+</strong> '+ADD_ITEM+'...</a>'+
                            '</div>';
          //nazev B polozky
          returnString+='</td><td class="nameTd">'+valuesMapData[valueB]+'</td></tr>';
        }
      }           
    if (arr.valuesA)
      if (arr.valuesA.length>0){
        for(i=0;i<arr['valuesA'].length;i++){
          valueA=arr["valuesA"][i];
          if(!usedValuesA.in_array(valueA)){
            returnString+='<tr>'+
                            '<td width="50%">'+
                              '<div class="noMappedValueDiv">'+valuesMapData[valueA]+"</div>"+
                            '</td>'+
                            '<td>&nbsp;</td>'+
                          '</tr>';
          }
        }
      }  
    //--div pro opacne zobrazeni                                           
    returnString+=                             '</table></div></div>';
    return returnString;                  
  } 
}
      
function reloadValuesData(url){   
  saveScroll();
  $jq.getJSON(url, function(data) {  
    sessionData=data;   
    noNullSession();                          
    /*vytvorime odstavce pro jednotlive pary klicu*/   
    for (var keyName in data.finalArr){
      assignText = getAssignHtml(keyName); 
      $jq("#"+keyName+"_div").html(assignText);
      /*projdeme zobrazení dle jednotlivých klíčů a zobrazíme je...*/                                                       
      if (viewMode[keyName]=='b'){
        $jq("#"+keyName+"_viewB").show();
      }else{
        $jq("#"+keyName+"_viewA").show();
      }
    }
    /*obnovíme pozici okna*/
    refreshScroll();
  });
}   
/*****************************************************************************************************************************************/
function showMoreValuesA(keyName,valueA,showUsed){
  var arr = sessionData.finalArr[keyName];
  var returnString='';
  /*event. projdeme pole s mapováním...*/  
  var usedItemsArr=new Array();
  if (!showUsed){  
    /*budeme pridavat jenom nepouzite polozky*/
    if (arr.mappedValues){
      for(var key in arr.mappedValues){
        pocetB=arr.mappedValues[key].length;
        if (pocetB>0){
          for(var i=0;i<pocetB;i++){
            usedItemsArr.push(arr.mappedValues[key][i]);
          }
        }
      } 
    }
    if (arr.autoMappedValues){          
      //if (arr.autoMappedValues.length>0){ 
        for(var key in arr.autoMappedValues){
          pocetB=arr.autoMappedValues[key].length; 
          if (pocetB>0){
            for(var i=0;i<pocetB;i++){
              usedItemsArr.push(arr.autoMappedValues[key][i]);
            }
          }
        }  /*
        pocetB=arr.autoMappedValues[valueA].length;
        if (pocetB>0){                             
          for(var i=0;i<pocetB;i++){
            usedItemsArr.push(arr.autoMappedValues[valueA][i]);
          }
        }*/
      //}
    }
    /**/
  }else{
    if (arr.mappedValues){
      if (arr.mappedValues[valueA]){
        pocetB=arr.mappedValues[valueA].length;
        if (pocetB>0){
          for(var i=0;i<pocetB;i++){
            usedItemsArr.push(arr.mappedValues[valueA][i]);
          }
        }
      }
    }
    if (arr.autoMappedValues){
      if (arr.autoMappedValues[valueA]){
        pocetB=arr.autoMappedValues[valueA].length;
        if (pocetB>0){                        
          for(var i=0;i<pocetB;i++){
            usedItemsArr.push(arr.autoMappedValues[valueA][i]);
          }
        }
      }
    }
  }
  /*vypsani polozek*/
  if (arr.valuesB){
      if (arr.valuesB.length>0){
        /*projdeme pole s moznymi hodnotami a pokud nejsou v pouzitych, tak je vypiseme*/
        for(var i=0;i<arr.valuesB.length;i++){
          valueB=arr.valuesB[i];
          if(!usedItemsArr.in_array(valueB)){       
            returnString+='<a href="javascript:addMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\');">'+valuesMapData[valueB]+'</a><br />';
          }
        }
        /**/
        returnString+='<br /><a href="javascript:hideMoreValuesA(\''+keyName+'\',\''+valueA+'\')">'+CANCEL+'</a>';
      }
    }
  /*pokud mame co zobrazit, tak to zobrazime...*/
  if (returnString!=''){
    $jq("#"+keyName+"_"+valueA+"_AmorevaluesDiv").html(returnString);
  }else{
    window.alert("Nenalezeny žádné vhodné položky...");
  }  
}
/******************/
function hideMoreValuesA(keyName,valueX){
  returnString='<a href="javascript:showMoreValuesA(\''+keyName+'\',\''+valueX+'\',0);"><strong>+</strong> '+ADD_UNUSED_ITEM+'...</a>'+
               '<a href="javascript:showMoreValuesA(\''+keyName+'\',\''+valueX+'\',1);"><strong>+</strong> '+ADD_ITEM+'...</a>';
  $jq("#"+keyName+"_"+valueX+"_AmorevaluesDiv").html(returnString);
}
function hideMoreValuesB(keyName,valueX){
  returnString='<a href="javascript:showMoreValuesB(\''+keyName+'\',\''+valueX+'\',0);"><strong>+</strong> '+ADD_UNUSED_ITEM+'...</a>'+
               '<a href="javascript:showMoreValuesB(\''+keyName+'\',\''+valueX+'\',1);"><strong>+</strong> '+ADD_ITEM+'...</a>';
  $jq("#"+keyName+"_"+valueX+"_BmorevaluesDiv").html(returnString);
}
/**
 *  Funkce pro zobrazení přiřaditelných položek
 */ 
function showMoreValuesB(keyName,valueB,showUsed){//TODO
  var arr = sessionData.finalArr[keyName];
  var returnString='';
  /*event. projdeme pole s mapováním...*/  
  var usedItemsArr=new Array();
  if (!showUsed){  
    /*budeme pridavat jenom nepouzite polozky*/
    if (arr.mappedValues){
      if (arr.mappedValues.length>0){
        for (var valueA in arr.mappedValues){
          usedItemsArr.push(valueA);
        }
      }
    }
    if (arr.autoMappedValues){
      if (arr.autoMappedValues.length>0){
        for (var valueA in arr.autoMappedValues){
          usedItemsArr.push(valueA);
        }
      }
    }
    /**/
  }
  /*vypsani polozek*/
  if (arr.valuesA){
      if (arr.valuesA.length>0){
        /*projdeme pole s moznymi hodnotammi a pokud*/
        for(var i=0;i<arr.valuesA.length;i++){
          valueA=arr.valuesA[i];
          if(!usedItemsArr.in_array(valueA)){
            returnString+='<a href="javascript:addMerge(\''+keyName+'\',\''+valueA+'\',\''+valueB+'\');">'+valuesMapData[valueA]+'</a><br />';
          }
        }
        /**/
        returnString+='<br /><a href="javascript:hideMoreValuesB(\''+keyName+'\',\''+valueB+'\')">'+CANCEL+'</a>';
      }
    }         
  /*pokud mame co zobrazit, tak to zobrazime...*/
  if (returnString!=''){
    $jq("#"+keyName+"_"+valueB+"_BmorevaluesDiv").html(returnString);
  }else{
    window.alert(NO_ITEMS_FOUND);
  }  
}

/**
 *  Funkce pro zobrazení pohledu A<<B u daného klíče
 */ 
function showA(keyName){ 
  $jq('#'+keyName+'_viewB').hide();
  $jq('#'+keyName+'_viewA').show();
  viewMode[keyName]='a';
}

/**
 *  Funkce pro zobrazení pohledu A>>B u daného klíče
 */ 
function showB(keyName){
  $jq('#'+keyName+'_viewA').hide();
  $jq('#'+keyName+'_viewB').show();
  viewMode[keyName]='b';
}

/*****************************************************************************************************************************************/
/*uživatelské funkce*/
function addMerge(keyName,key,key2){  
  reloadValuesData("index.php?option=com_mapping&task=usereditvalues&no_html=1&format=raw&action=join&fieldA="+keyName+"&keyA="+key+"&keyB="+key2);
}
function unconfirmMerge(keyName,key,key2){
  reloadValuesData("index.php?option=com_mapping&task=usereditvalues&no_html=1&format=raw&action=unconfirm&fieldA="+keyName+"&keyA="+key+"&keyB="+key2);
}
function removeMerge(keyName,key,key2){
  reloadValuesData("index.php?option=com_mapping&task=usereditvalues&no_html=1&format=raw&action=unjoin&fieldA="+keyName+"&keyA="+key+"&keyB="+key2);
}
