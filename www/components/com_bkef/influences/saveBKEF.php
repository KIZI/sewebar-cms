<?php
	//Ze souboru na cestě cestaKSouboru načtu původní XML dokument, který se upravoval.
	// V případě, že by měl tento skript místo cest k souboru dostat string představující
	// onen xml soubor. Pak je potřeba zakomentovat cestaKSouboru, soubor a první while cyklus.
	/*$cestaKSouboru = "bkef.xml";
	$soubor = fopen($cestaKSouboru,"r");
	$obsahSouboru = ""; */
	$obsahSouboru1 = "";
	//Tento cyklus je potřeba zakomentovat pokud by se původní xml nenačítalo ze souboru. a následně
	//je potřeba do proměnné obsahSouboru dosadti string reprezentující ten soubor.
	/*while(!feof($soubor)){
		$obsahSouboruPom = fgets($soubor, 9192);
		$obsahSouboru .= $obsahSouboruPom;
	}    */
	$dom = new DomDocument();
    $dom->loadXML($obsahSouboru);
	$influenceType = $_POST["infltype"];
	$metaNameA = $_POST["metanaa"];
	$metaNameB = $_POST["metanab"];
	if($influenceType != "Not Set"){
		$influenceArity = $_POST["inflarity"];
		$knowledgeValidity = $_POST["knowval"];
		$influenceScope = $_POST["inflscope"];
		$formatNameA = $_POST["formnaa"];
		$formatNameB = $_POST["formnab"];
		$valuesB = $_POST["valuesb"];
		$anotacePocet = $_POST["pocetAnotaci"];
		
		$influenceId = 0;
		$proslo = false;
		$domnodelist = $dom->getElementsByTagName("Influence");
		foreach($domnodelist as $domnodepom){
			$childNodes = $domnodepom->childNodes;
			$pomocna = 0;
			foreach($childNodes as $domnodepom1){
				if($domnodepom1->nodeName == "MetaAttribute"){
					$atributy = $domnodepom1->attributes;
					$role1 = $atributy->getNamedItem("role");
					$role = $role1->value;
					$name1 = $atributy->getNamedItem("name");
					$name = $name1->value;
					if($role == "A"){
                        if($name == $metaNameA){
                            $pomocna++;
                        }
					}
					if($role == "B"){
						if($name == $metaNameB){
                            $pomocna++;
                        }
					}
				}
			}
			$parent = $domnodepom->parentNode;
			$atributy2 = $domnodepom->attributes;
			$id2 = $atributy2->getNamedItem("id");
			if($influenceId < $id2){
				$influenceId = $id2;
			}
			if($pomocna == 2){
				$parent->removeChild($domnodepom);
				$atributy2 = $domnodepom->attributes;
				$id2 = $atributy2->getNamedItem("id");
				$influenceId = $id2->value;
			}
			$proslo = true;
		}
		$influenceId = 20;
		$influenceN = $dom->createElement("Influence");
		$influenceN->setAttribute("type",$influenceType);
		$influenceN->setAttribute("id",$influenceId);
		$influenceN->setAttribute("arity",$influenceArity);
		$parent->appendChild($influenceN);
	    if($proslo){
			if($anotacePocet != 0){
				$annotationsN = $dom->createElement("Annotations");
				$influenceN->appendChild($annotationsN);
				for($k = 0; $k < $anotacePocet; $k++){
					if($_POST["Ttext".$k."Anotace".$i] != ""){
						$annotationN = $dom->createElement("Annotation");
						$annotationsN->appendChild($annotationN);
						$annotationAutor = $_POST["Tautor".$k."Anotace".$i];
						$annotationText = $_POST["Ttext".$k."Anotace".$i];
						$annotationN->appendChild($dom->createElement("Author",$annotationAutor));
						$annotationN->appendChild($dom->createElement("Text",$annotationText));
					}
				}
			}
			if($knowledgeValidity != ""){
				//$knowledgeValidity = str_replace("Prokázáno","Proven",$knowledgeValidity);
				//$knowledgeValidity = str_replace("Zavrhnuto","Rejected",$knowledgeValidity);
				//$knowledgeValidity = str_replace("Neznámo","Unknown",$knowledgeValidity);
				$knowledgeValidityN = $dom->createElement("KnowledgeValidity",$knowledgeValidity);
				$influenceN->appendChild($knowledgeValidityN);
			}
			if($influenceScope != ""){
				$influenceScopeN = $dom->createElement("InfluenceScope",$influenceScope);
				$influenceN->appendChild($influenceScopeN);
			}
			$metaAttributeAN = $dom->createElement("MetaAttribute");
			$metaAttributeAN->setAttribute("role","A");
			$metaAttributeAN->setAttribute("name",$metaNameA);
			$influenceN->appendChild($metaAttributeAN);

			$formatNameA = split("Ł", $formatNameA);
			if(count($formatNameA) > 0){
				if(count($formatNameA)-1 > 0){
				$restrictedToN = $dom->createElement("RestrictedTo");
				$metaAttributeAN->appendChild($restrictedToN);
				$values1 = split("Ł", $valuesA);
				for($m = 0; $m < count($formatNameA)-1;$m++){
					$formatN = $dom->createElement("Format");
					$formatN->setAttribute("name",$formatNameA[$m]);
					$restrictedToN->appendChild($formatN);
					if($values1[$m] != ""){
						$values = split("°", $values1[$m]);
						$pocetPrvku = count($values);
						if($pocetPrvku == 1){
							$intervalsN = $dom->createElement("Intervals");
							$formatN->appendChild($intervalsN);
							$delkaRetezce = strlen($values[0]);
							$retezec = $values[0];
							$hodnota1 = false;
							$hodnota1Pom = "";
							$hodnota2 = false;
							$hodnota2Pom = "";
							$levaZavorka = "";
							$pravaZavorka = "";
							for($k = 0; $k < $delkaRetezce; $k++){
								if($retezec[$k] == "<" || $retezec[$k] == "("){
									if($retezec[$k] == "<"){
										$levaZavorka = "closed";
									}
									else{
										$levaZavorka = "open";
									}
									$hodnota1 = true;
									continue;
								}
								if($retezec[$k] == ")" || $retezec[$k] == ">"){
									if($retezec[$k] == ">"){
										$pravaZavorka = "closed";
									}
									else{
										$pravaZavorka = "open";
									}
									$intervalN = $dom->createElement("Interval");
									$intervalsN->appendChild($intervalN);
									$leftBoundN = $dom->createElement("LeftBound");
									$leftBoundN->setAttribute("type",$levaZavorka);
									$leftBoundN->setAttribute("value",$hodnota1Pom);
									$intervalN->appendChild($leftBoundN);
									$rightBoundN = $dom->createElement("RightBound");
									$rightBoundN->setAttribute("type",$pravaZavorka);
									$rightBoundN->setAttribute("value",$hodnota2Pom);
									$intervalN->appendChild($rightBoundN);
									$hodnota1 = false;
									$hodnota2 = false;
									$hodnota1Pom = "";
									$hodnota2Pom = "";
									continue;
								}
								if($retezec[$k] == ";"){
									$hodnota1 = false;
									$hodnota2 = true;
									continue;
								}
								if($hodnota1){
									$hodnota1Pom .= $retezec[$k];
								}
								if($hodnota2){
									$hodnota2Pom .= $retezec[$k];
								}
							}
						}
						if($pocetPrvku > 1){
							for($j = 0; $j < $pocetPrvku-1; $j++){
								$valueN = $dom->createElement("Value",$values[$j]);
								$valueN->setAttribute("format",$formatNameA[$m]);
								$formatN->appendChild($valueN);
							}
						}
					}
				}
				}
			}
			$metaAttributeBN = $dom->createElement("MetaAttribute");
			$metaAttributeBN->setAttribute("role","B");
			$metaAttributeBN->setAttribute("name",$metaNameB);
			$influenceN->appendChild($metaAttributeBN);
			$formatNameB = split("Ł", $formatNameB);
			if(count($formatNameB) > 0){
				$restrictedToN = $dom->createElement("RestrictedTo");
				$metaAttributeBN->appendChild($restrictedToN);
				$values1 = split("Ł", $valuesB);
				for($m = 0; $m < count($formatNameB)-1;$m++){
					$formatN = $dom->createElement("Format");
					$formatN->setAttribute("name",$formatNameB[$m]);
					$restrictedToN->appendChild($formatN);
					if($values1[$m] != ""){
						$values = split("°", $values1[$m]);
						$pocetPrvku = count($values);
						if($pocetPrvku == 1){
							$intervalsN = $dom->createElement("Intervals");
							$formatN->appendChild($intervalsN);
							$delkaRetezce = strlen($values[0]);
							$retezec = $values[0];
							$hodnota1 = false;
							$hodnota1Pom = "";
							$hodnota2 = false;
							$hodnota2Pom = "";
							$levaZavorka = "";
							$pravaZavorka = "";
							for($k = 0; $k < $delkaRetezce; $k++){
								if($retezec[$k] == "<" || $retezec[$k] == "(" || $retezec[$k] == "&lt;"){
									if($retezec[$k] == "<" || $retezec[$k] == "&lt;"){
										$levaZavorka = "closed";
									}
									else{
										$levaZavorka = "open";
									}
									$hodnota1 = true;
									continue;
								}
								if($retezec[$k] == ")" || $retezec[$k] == ">" || $retezec[$k] == "&gt;"){
									if($retezec[$k] == ">" || $retezec[$k] == "&gt;"){
										$pravaZavorka = "closed";
									}
									else{
										$pravaZavorka = "open";
									}
									$intervalN = $dom->createElement("Interval");
									$intervalsN->appendChild($intervalN);
									$leftBoundN = $dom->createElement("LeftBound");
									$leftBoundN->setAttribute("type",$levaZavorka);
									$leftBoundN->setAttribute("value",$hodnota1Pom);
									$intervalN->appendChild($leftBoundN);
									$rightBoundN = $dom->createElement("RightBound");
									$rightBoundN->setAttribute("type",$pravaZavorka);
									$rightBoundN->setAttribute("value",$hodnota2Pom);
									$intervalN->appendChild($rightBoundN);
									$hodnota1 = false;
									$hodnota2 = false;
									$hodnota1Pom = "";
									$hodnota2Pom = "";
									continue;
								}
								if($retezec[$k] == ";"){
									$hodnota1 = false;
									$hodnota2 = true;
									continue;
								}
								if($hodnota1){
									$hodnota1Pom .= $retezec[$k];
								}
								if($hodnota2){
									$hodnota2Pom .= $retezec[$k];
								}
								
							}
							
						}
						if($pocetPrvku > 1){
							for($j = 0; $j < $pocetPrvku-1; $j++){
								$valueN = $dom->createElement("Value",$values[$j]);
								$valueN->setAttribute("format",$formatNameB[$m]);
								$formatN->appendChild($valueN);
							}
						}
					}
				}
			}
		}
	}
	else{
		$domnodelist = $dom->getElementsByTagName("Influence");
		foreach($domnodelist as $domnodepom){
			$childNodes = $domnodepom->childNodes;
			$pomocna = 0;
			foreach($childNodes as $domnodepom1){
				if($domnodepom1->nodeName == "MetaAttribute"){
					$atributy = $domnodepom1->attributes;
					$role = $atributy->getNamedItem("role");
					$name = $atributy->getNamedItem("name");
					if($role == "A"){
                        if($name == $metaNameA){
                            $pomocna++;
                        }
					}
					if($role == "B"){
						if($name == $metaNameB){
                            $pomocna++;
                        }
					}
				}
			}
			if($pomocna == 2){
				$parent = $domnodepom->parentNode;
				$parent->removeChild($domnodepom);
				break;
			}
		}
	}
	$obsahSouboru = $dom->saveXML();
	//die();
	// Tento kus řeší uzavření souboru, ze kterého jsem načítal
/*	fclose($soubor);
*/	//Tohle řeší uložení do toho samého souboru.
	/*$soubor = fopen($cestaKSouboru,"w");
	fwrite($soubor, $obsahSouboru);
	fclose($soubor);
	  */
	//Vloží index.php
	/*include "index.php";
  */
?>
