window.kteryObjekt = 0;
window.pocetObjekt = 0;
window.ulozene = 0;
window.vybranyObjekt;
window.posledneVybranyObr; 
//positiveFrequnecy a negativeFrequency
/*
 Funkce, která se volá v případě, že uživatel klikne v rámci jedné influence na nějaký 
 obrázek. Nastaví barvu všech ostatních na bílou a přenese tento obrázek i do tabulky.
 Následně ještě nastaví doplňkový text k danému typu influence.
*/
function prebarvi(image) {
	window.posledneVybranyObr.style.borderColor = "white";	
	image.style.border = "3px solid black";
	window.vybranyObjekt.setAttribute("src",image.getAttribute("src")); 
	if(image.id == "Negative Frequency" || image.id == "Positive Frequency"){
		for(i = 0; i < document.getElementsByName("BKEF_formatBPosVal").length; i++){
			document.getElementsByName("BKEF_formatBPosVal")[i].style.display = "";
		}
  		document.getElementById("BKEF_firstButton").style.display = "none";
   		document.getElementById("BKEF_secondButton").style.display = "";
	}
	else{
		for(i = 0; i < document.getElementsByName("BKEF_formatBPosVal").length; i++){
			document.getElementsByName("BKEF_formatBPosVal")[i].style.display = "none";
		}
 		document.getElementById("BKEF_secondButton").style.display = "none";
   		document.getElementById("BKEF_firstButton").style.display = "";
	}
	metaAttributeA = window.vybranyObjekt.getAttribute("metaattributei");
	metaAttributeB = window.vybranyObjekt.getAttribute("metaattributeii");
	window.vybranyObjekt.alt = image.id;
	if(image.id == "Not Set"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "";
	}
	if(image.id == "Unknown"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "Between attributes <b>"+metaAttributeA+"</b>, <b>"+metaAttributeB+"</b> can be dependency, but details are unknown.";
	}
	if(image.id == "Some Influence"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "Between attributes <b>"+metaAttributeA+"</b>, <b>"+metaAttributeB+"</b> is dependency, but is unknown.";
	}
	if(image.id == "Positive Influence"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "If the values of attribute <b>"+metaAttributeA+"</b> increase, the values of attribute <b>"+metaAttributeB+"</b> also increase.";
	}
	if(image.id == "Positive Frequency"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "If the values of attribute <b>"+metaAttributeA+"</b> increase, the relative frequency of clients having certain value in attribute <b>"+metaAttributeB+"</b> also increase. <span style=\"color: red;font-weight:bold\">Set this value below.</span>";
	}
	if(image.id == "Positive Boolean"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "The truth of attribte <b>"+metaAttributeA+"</b> increase the relative frequency of truthfull values of attribute <b>"+metaAttributeB+"</b>.";
	}
	if(image.id == "None"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "Between attributes <b>"+metaAttributeA+"</b> and <b>"+metaAttributeB+"</b> is no dependency.";
	}
	if(image.id == "Negative Influence"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "If the values of attribute <b>"+metaAttributeA+"</b> increase, the values of attribute <b>"+metaAttributeB+"</b> decrease.";
	}
	if(image.id == "Negative Frequency"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "If the values of attribute <b>"+metaAttributeA+"</b> increase, the relative frequency of values having certain value in attribute <b>"+metaAttributeB+"</b> decrease.<span style=\"color: red;font-weight:bold\">Set this value below.</span>";
	}
	if(image.id == "Negative Boolean"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "The truth of attribute <b>"+metaAttributeA+"</b> decrease the relative frequency of thruthfull values of the attribute <b>"+metaAttributeB+"</b>.";
	}
	if(image.id == "Functional"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "Between attributes <b>"+metaAttributeA+"</b>, <b>"+metaAttributeB+"</b> is a strong functional dependency.";
	}
	if(image.id == "Do Not Care"){
		document.getElementById("BKEF_doplnkovytext").innerHTML = "Between attributes <b>"+metaAttributeA+"</b>, <b>"+metaAttributeB+"</b> is dependency, but it´s not interesting.";
	}
	window.posledneVybranyObr = image;
	             
}

function zpracujLoc(loc){
	loc = loc.split("?");
	location1 = loc[0];
	if(loc.length > 1){
		loc2 = loc[1].split("&");
		location1 = location1 + "?";
		for(i = 0; i < loc2.length; i++){
			loc3 = loc2[i].split("=");
			if(loc3[0] == "task"){
				location1 = location1+loc3[0]+"=getTableMatrix&";
			}
			else{
				location1 = location1+loc2[i]+"&";
			}
		}
	}
	location1 = location1.substring(0,location1.length-1);
	return location1;
}

function zpracujaddInf(loc){
	loc = loc.split("?");
	location1 = loc[0];
	if(loc.length > 1){
		loc2 = loc[1].split("&");
		location1 = location1 + "?";
		for(i = 0; i < loc2.length; i++){
			loc3 = loc2[i].split("=");
			if(loc3[0] == "additionalInfo"){
				location1 = location1+"";
			}
			else{
				location1 = location1+loc2[i]+"&";
			}
		}
	}
	location1 = location1.substring(0,location1.length-1);
	return location1;
}

function zjistiData(){	
	deti = document.getElementById("checkBoxes").childNodes;
	inputy1 = "zaskrtnuto=";
	for(j = 0; j < deti.length; j++){
		if(deti[j].nodeName == "INPUT"){
			if(deti[j].checked){
				inputy1 = inputy1 + deti[j].value + "ŧ";
			}
		}
	}
	inputy1 = inputy1.substring(0, inputy1.length-1);
	//inputy1 = inputy1 + ";";
	//alert(inputy1);
	return inputy1;
}
window.pomocny2;
var urlBase = "components/com_bkef/influences/"
function ajaxTest(image){
	if(document.getElementById("BKEF_mainObject") == null){
		window.pomocny2 = image;
		//alert(window.location.href);
		//testD = zjistiData();
		var url = zpracujLoc(window.location.href);
		var testD = "attribute="+image.getAttribute("metaAttributeI")+"&attributeii="+image.getAttribute("metaAttributeII");
		//alert(url);
		//var url = urlBase+"BKEF_infl_main.php";
		try
	    {
	     //var test = "otaznik=gghhh;cislo_clanku;";
	     var myAjax = new Ajax(url, {data: testD, method: 'post', update: $('BKEF_editInfl'), onComplete: function() {
				spust(window.pomocny2);
				toBeInserted = document.getElementById("content");
				topLeft = findPos(toBeInserted);
				node = document.getElementById("BKEF_mainObject");
				otec = node.parentNode;
				otec.removeChild(node);
				document.getElementById("content").appendChild(node);
				document.getElementById("BKEF_mainObject").style.position="absolute";
				document.getElementById("BKEF_mainObject").style.top=topLeft[1]+"px";
				document.getElementById("BKEF_mainObject").style.left=topLeft[0]+"px";
				document.getElementById("BKEF_mainObject").style.display="";
				vyresIS_BK();
				vyresFormats();
			}}).request();
	    }
	    catch(err)
	    {
	     $('myForm').innerHTML = 
	       "This demo may not work if you don't have the proper permissions setup with your browser.<br />" +
	       "This page must be 'trusted' by the browser to work.<br />" +
	       "If you are using Firefox, try using Internet Explorer.<br />" + 
	       err;
	    }
    }
}

function ajaxSubmit(){
	try
    {
     $('formularInfluences').send()
    }
    catch(err)
    {
     $('BKEF_editInfl').innerHTML = 
       "This demo may not work if you don't have the proper permissions setup with your browser.<br />" +
       "This page must be 'trusted' by the browser to work.<br />" +
       "If you are using Firefox, try using Internet Explorer.<br />" + 
       err;
    }
}

function solveLocation(){
	document.getElementById("lokace").innerHTML = "<b>Adresa: "+zpracujaddInf(window.location.href)+vyresInputy1()+"</b>";
}

function vyresInputy1(){
	hodnotyPom = "";
	detiS = document.getElementById("checkBoxes").childNodes;
	for(i = 0; i < detiS.length; i++){
		if(detiS[i].nodeName == "INPUT" && detiS[i].checked){
			hodnotyPom = hodnotyPom + detiS[i].getAttribute("value") + "ŧ";
		}
	}
	if(hodnotyPom != ""){
		hodnotyPom = "&additionalInfo="+hodnotyPom;
	}
	return hodnotyPom;
}

function vyresFormats(){
	formatsBSS = document.getElementsByName("BKEF_formatBPosVal");
	for(i = 0; i < document.getElementsByName("BKEF_formatBPosVal").length; i++){
		if(document.getElementsByName("BKEF_formatBPosVal")[i].nodeName == "SELECT"){
			pozice = document.getElementsByName("BKEF_formatBPosVal")[i].options.length - 1;
			udaje = document.getElementsByName("BKEF_formatBPosVal")[i].options[pozice].innerHTML;
			//udaje = formatsBSS[i].options[formatBSS.options.length-1].innerHTML;
			udajePole = udaje.split("Ł");
			for(j = 0; j < (pozice + 1); j++){
				for(k = 0; k < udajePole.length; k++){
					if(document.getElementsByName("BKEF_formatBPosVal")[i].options[j].innerHTML == trim(udajePole[k])){
						document.getElementsByName("BKEF_formatBPosVal")[i].options[j].selected = true;
						document.getElementsByName("BKEF_formatBPosVal")[i].options[0].selected = false;
					}
				}
			}
			document.getElementsByName("BKEF_formatBPosVal")[i].removeChild(document.getElementsByName("BKEF_formatBPosVal")[i].options[pozice]);
		}
	} 
	//formats = document.getElementById("BKEF_formatInfo").innerHTML.split;
}

function vyresIS_BK(){
	bgKnow = document.getElementById("BKEF_bgKnow");
	for(i = 1; i < bgKnow.options.length; i++){
		if(bgKnow.options[i].getAttribute("type") == bgKnow.options[0].getAttribute("vychozi")){
			bgKnow.selectedIndex = i;
			//bgKnow.options[0].innerHTML = "";
		}
	}
	inflScope = document.getElementById("BKEF_inflScope");
	for(i = 1; i < inflScope.options.length; i++){
		if(inflScope.options[i].innerHTML == inflScope.options[0].getAttribute("vychozi")){
			inflScope.selectedIndex = i;
			//inflScope.options[0].innerHTML = "";
		}
	}
}
 
/*
 Prevod mezi jmény obrázku pro lidské oči a jejich reprezentaci pro účely
 samotného skriptu.
*/
function prevedJmeno(jmeno) {
    if (jmeno.search("Some-influence") != "-1") {
        return "Some Influence";
    }
    if (jmeno.search("Positive-growth") != "-1") {
        return "Positive Influence";
    }
    if (jmeno.search("Negative-growth") != "-1") {
        return "Negative Influence";
    }
    if (jmeno.search("Positive-bool-growth") != "-1") {
        return "Positive Frequency";
    }
    if (jmeno.search("Negative-bool-growth") != "-1") {
        return "Negative Frequency";
    }
    if (jmeno.search("Double-bool-growth") != "-1") {
        return "Positive Boolean";
    }
    if (jmeno.search("Negative boolean") != "-1") {
        return "Negative Boolean";
    }
    if (jmeno.search("Functional") != "-1") {
        return "Functional";
    }
    if (jmeno.search("None") != "-1") {
        return "None";
    }
    if (jmeno.search("Uninteresting") != "-1") {
        return "Do Not Care";
    }
    if (jmeno.search("Unknown") != "-1") {
        return "Unknown";
    }
    if (jmeno.search("Not Set") != "-1") {
        return "Not Set";
    }
    return jmeno;
}
     
/*
 Prevod mezi jmény obrázku pro lidské oči a jejich reprezentaci pro účely
 samotného skriptu. Jenom v opačném směru než prevedJmeno
*/
function prevedJmenoInv(jmeno) {
    if (jmeno.search("someInfluence") != "-1") {
        return "Some-influence";
    }
    if (jmeno.search("positiveInfluence") != "-1") {
        return "Positive-growth";
    }
    if (jmeno.search("negativeInfluence") != "-1") {
        return "Negative-growth";
    }
    if (jmeno.search("positiveFrequency") != "-1") {
        return "Positive-bool-growth";
    }
    if (jmeno.search("negativeFrequency") != "-1") {
        return "Negative-bool-growth";
    }
    if (jmeno.search("positiveBoolean") != "-1") {
        return "Double-bool-growth";
    }
    if (jmeno.search("negativeBoolean") != "-1") {
        return "Negative boolean";
    }
    if (jmeno.search("functional") != "-1") {
        return "Functional";
    }
    if (jmeno.search("none") != "-1") {
        return "None";
    }
    if (jmeno.search("doNotCare") != "-1") {
        return "Uninteresting";
    }
    if (jmeno.search("unknown") != "-1") {
        return "Unknown";
    }
    if (jmeno.search("notSet") != "-1") {
        return "Not Set";
    }
    return jmeno;
}
     
function findPos(obj) {
   var curleft = curtop = 0;
   if (obj.offsetParent) {
     do {
		 curleft += obj.offsetLeft;
		 curtop += obj.offsetTop;
	  } while (obj = obj.offsetParent);
	return [curleft,curtop];
	}
}
     
function jsouZadane(){
	jeOznaceno = true; 	
	influenceType = window.posledneVybranyObr.getAttribute("alt");	
	if (influenceType == "Positive Frequency" || influenceType == "Negative Frequency°") {
		for(m = 0; m < document.getElementsByName("BKEF_formatBPosVal").length; m++){
			for(n = 0; n < document.getElementsByName("formatsB").length; n++){
				if(document.getElementsByName("formatsB")[n].getAttribute("format") == document.getElementsByName("BKEF_formatBPosVal")[m].getAttribute("format") && document.getElementsByName("formatsB")[n].checked){
					if(document.getElementsByName("BKEF_formatBPosVal")[m].getAttribute("typ") == "interval"){
						if(document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format"))[0].value == ""){
							alert("Nezadal jste doplňující údaje týkající se formátu na pravé straně závislosti. \nAtribut A je: "+window.vybranyObjekt.getAttribute("metaattributei")+"\nAtribut B je: "+window.vybranyObjekt.getAttribute("metaattributeii"));
							return false;
						}
						if(document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"1")[0].value == ""){
							alert("Nezadal jste doplňující údaje týkající se formátu na pravé straně závislosti. \nAtribut A je: "+window.vybranyObjekt.getAttribute("metaattributei")+"\nAtribut B je: "+window.vybranyObjekt.getAttribute("metaattributeii"));
							return false;
						}
					}
					else{
						if(document.getElementsByName("BKEF_formatBPosVal")[m].selectedIndex == 0){
							jeOznaceno = false;
							alert("Nezadal jste doplňující údaje týkající se formátu na pravé straně závislosti. \nAtribut A je: "+window.vybranyObjekt.getAttribute("metaattributei")+"\nAtribut B je: "+window.vybranyObjekt.getAttribute("metaattributeii"));
						}
					}
				}
			}	
		}
    }
	//alert(jeOznaceno);
	return jeOznaceno;
}     
     
/*
 Funkce, která se spouští při odesílání formuláře. Vezme všechny relevantní údaje
 které známe o jednotlivých buňkách tabulky matrix a nacpe je do inputů typu hidden
 které následně umí zpracovat PHP do výsledného XML dokumentu. 
*/
function save() {
	if(!jsouZadane()){
     	return false;
    }
    zpracujJednuInfluenci();
     
    //alert("Tady");
    // místo submitu přijde na řadu AJAX 
    ajaxSubmit();
    node = document.getElementById("BKEF_mainObject");
    rodic = document.getElementById("BKEF_mainObject").parentNode;
    rodic.removeChild(node);
    //document.getElementById("formularInfluences").submit();
}
     
        
/*
 Vytvoří jeden prvek input typu hidden s údaji, které mu jsou zadány. 
 Používá se při odeslání formuláře funkcí zpracujJednuInfluenci
*/
function vytvorHidden(value, jmeno) {
	var influence = document.createElement("input");
	//aalert("Jmeno: "+jmeno+inflId+" "+value);
    influence.setAttribute("name", jmeno);
    influence.setAttribute("type", "hidden");
    influence.setAttribute("value", value);
    document.getElementById("formularInfluences").appendChild(influence);
}

function zpracujAnotaci(){
	vytvorHidden(document.getElementsByName("annotationsAuthors").length,"pocetAnotaci");
	for(i = 0; i < document.getElementsByName("annotationsAuthors").length; i++){
		vytvorHidden(document.getElementsByName("annotationsAuthors")[i].value,"Tautor"+i+"Anotace");
		vytvorHidden(document.getElementsByName("annotationsTexts")[i].value,"Ttext"+i+"Anotace");
	}
}

/*
 Zpracuje jednu konkrétní buňku tabulky při odesílání do formy, kterou umí PHP převést
*/
function zpracujJednuInfluenci() {
 //pro kazdou vec jeden input hidden
 	influenceType = prevedJmenoInv(window.posledneVybranyObr.getAttribute("src").replace("components/com_bkef/influences/img/","").replace(".png",""));
    if(influenceType != "Not Set") {
          
        influenceArity = "2";
         
        knowledgeValidity = "";
        if(document.getElementById("BKEF_bgKnow").selectedIndex != 0) {
        	knowledgeValidity = document.getElementById("BKEF_bgKnow").options[document.getElementById("BKEF_bgKnow").selectedIndex].getAttribute("type");
        }
        
        zpracujAnotaci();
        //annotation = document.getElementById("anotace°"+idPom).value;
        //if(annotation == "Zde můžete zadat doplňující informace k této závislosti."){
        //	annotation = "";
        //}
        //alert(anotace);
                                     
        influenceScope = "";
        if(document.getElementById("BKEF_inflScope").selectedIndex != 0) {
            influenceScope = document.getElementById("BKEF_inflScope").options[document.getElementById("BKEF_inflScope").selectedIndex].innerHTML;
        }
         
        metaNameA = "";
        metaNameA = window.vybranyObjekt.getAttribute("metaattributei");
         
        metaNameB = "";
        metaNameB = window.vybranyObjekt.getAttribute("metaattributeii");
         
        formatNameA = "";
         //projdou se chekboxy a zjisti se checked a nasledne value
        for(i = 0; i!= document.getElementsByName("formatsA").length; i++) {
            if(document.getElementsByName("formatsA")[i].checked) {
                formatNameA += document.getElementsByName("formatsA")[i].getAttribute("format")+"Ł";
            }
        }
         
        formatNameB = "";
        for(i = 0; i!= document.getElementsByName("formatsB").length; i++) {
            if(document.getElementsByName("formatsB")[i].checked) {
                formatNameB += document.getElementsByName("formatsB")[i].getAttribute("format")+"Ł";
            }
        }
         
        valuesB = "";
        if(influenceType == "Positive-bool-growth" || influenceType == "Negative-bool-growth"){ 
        	for(m = 0; m < document.getElementsByName("BKEF_formatBPosVal").length; m++){
				for(n = 0; n < document.getElementsByName("formatsB").length; n++){
					if(document.getElementsByName("formatsB")[n].getAttribute("format") == document.getElementsByName("BKEF_formatBPosVal")[m].getAttribute("format") && document.getElementsByName("formatsB")[n].checked){
						if(document.getElementsByName("BKEF_formatBPosVal")[m].getAttribute("typ") == "interval"){
							zavorkaL = document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"0")[0].options[document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"0")[0].selectedIndex].innerHTML;
							zavorkaP = document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"2")[0].options[document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"2")[0].selectedIndex].innerHTML;
							valuesB += zavorkaL+document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format"))[0].value+";"+document.getElementsByName(document.getElementsByName("formatsB")[n].getAttribute("format")+"1")[0].value+zavorkaP; 
							valuesB += "Ł";        				
        				}
						else{
							for(z = 1; z < document.getElementsByName("BKEF_formatBPosVal")[m].options.length; z++){
								if(document.getElementsByName("BKEF_formatBPosVal")[m].options[z].selected){
									valuesB = valuesB + document.getElementsByName("BKEF_formatBPosVal")[m].options[z].value + "°";
								}
							}
							valuesB += "Ł";
						}
					}
				}	
			}
        }
        influenceId = 1;
        valuesB = valuesB.replace("&lt;","<").replace("&gt;",">");
		vyresInputy();        
        
        vytvorHidden(influenceType,"infltype");
        vytvorHidden(influenceId,"inflid");
        vytvorHidden(influenceArity,"inflarity");
        vytvorHidden(knowledgeValidity,"knowval");
        vytvorHidden(influenceScope,"inflscope");
        vytvorHidden(metaNameA,"metanaa");
        vytvorHidden(metaNameB,"metanab");
        vytvorHidden(formatNameA,"formnaa");
        vytvorHidden(formatNameB,"formnab");
        vytvorHidden(valuesB,"valuesb");
        
        //vytvorHidden(inflId,annotation,"annotation");
	   //alert(valuesA+" "+valuesB+" "+formatNameA+" "+formatNameB);	             
    }
    else{
    	metaNameA = window.vybranyObjekt.getAttribute("metaattributei");
        metaNameB = window.vybranyObjekt.getAttribute("metaattributeii");
    	vyresInputy();
    	vytvorHidden(influenceType,"infltype");
    	vytvorHidden(metaNameA,"metanaa");
        vytvorHidden(metaNameB,"metanab");
        //Poslat informaci o tom že NotSET
    	return -1;
    }
	return "";
}

function vyresInputy(){
	hodnotyPom = "";
	detiS = document.getElementById("checkBoxes").childNodes;
	for(i = 0; i < detiS.length; i++){
		if(detiS[i].nodeName == "INPUT" && detiS[i].checked){
			hodnotyPom = hodnotyPom + detiS[i].getAttribute("value") + "ŧ";
		}
	}
	vytvorHidden(hodnotyPom,"checkboxess");
}

function pridejAnotaci(radek1, id){
	radek = radek1.parentNode.parentNode;
	kamVlozit = radek.rowIndex;
	novyRadek1 = radek.parentNode.insertRow(kamVlozit);
	//vytvořím s obsahem Autor: a pak s inputem
	bunka11 = novyRadek1.insertCell(0);
	bunka11.innerHTML = "Autor: ";
	bunka12 = novyRadek1.insertCell(1);
	input1 = bunka12.appendChild(document.createElement("input"));
	input1.setAttribute("type","text");
	input1.setAttribute("name","annotationsAuthors");
	//alert(prihlasenyUzivatel);
	input1.setAttribute("value",prihlasenyUzivatel);
	bunka12.colSpan = 5;
	novyRadek2 = radek.parentNode.insertRow(kamVlozit+1);
	bunka21 = novyRadek2.insertCell(0);
	bunka21.innerHTML = "Text: ";
	bunka22 = novyRadek2.insertCell(1);
	bunka22.colSpan = 5;
	textarea2 = bunka22.appendChild(document.createElement("textarea"));
	textarea2.setAttribute("cols","50");
	textarea2.setAttribute("rows","5");
	textarea2.setAttribute("name","annotationsTexts");
}

function trim(stringToTrim) {
	if(stringToTrim == null){
		return "";
	}
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function ShowHideAll(name) {
	for(i = 0; i < document.getElementsByName(name).length; i++){
		folding = window.document.getElementsByName(name)[i].style;
		folding.display = (folding.display == '')? 'none': '';
		//folding.display = 'none';
	}
}

//JSon
function uvodniNastaveni(){
	/* for(k = 0; k < detiS.length; k++){
		//alert(detiS[k].nodeName);
		if(detiS[k].nodeName == "INPUT"){
			//alert(detiS[k].getAttribute("value"));
			//detiS[i].checked = false;
			ShowHideAll(detiS[k].getAttribute("value"));
		}
	} */
	inputy = document.getElementById("coZaskrtnout").innerHTML.split("ŧ");
	for(j = 0; j < inputy.length; j++){
		detiS = document.getElementById("checkBoxes").childNodes;
		for(k = 0; k < detiS.length; k++){
			if(detiS[k].nodeName == "INPUT" && detiS[k].getAttribute("value") == inputy[j]){
				detiS[k].checked = true;
				//alert(detiS[k].getAttribute("value"));
				ShowHideAll(detiS[k].getAttribute("value"));
			}
		}
	}
	solveLocation()
}

function spust(image){
		window.vybranyObjekt = image;
		basicImage = prevedJmeno(image.getAttribute("alt"));
		alert
		if(trim(basicImage) == ""){
			basicImage = "Not Set";
		}	
		window.posledneVybranyObr = document.getElementById(basicImage);
		prebarvi(document.getElementById(basicImage));		
}

