<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

include dirname(__FILE__).'/../../../../components/com_content/helpers/route.php';

class DocumentsModel extends JModel
{
	/**
	 *  Funkce pro načtení dat 1 článku z databáze
	 */
	function getArticle($articleId,$text='all',$skipPlugins=false)
	{
		$db = & JFactory::getDBO();

		$db->setQuery( "SELECT * FROM #__content WHERE id='$articleId' LIMIT 1;");

		$rows = $db->loadObjectList();
		if (count($rows)==1){
			$article=$rows[0]->introtext.$rows[0]->fulltext;
		}else {
			return false;
		}

		//připravíme text
		if ($text=='introtext'){
			$rows[0]->text=$rows[0]->introtext;
		}elseif ($text=='fulltext'){
			$rows[0]->text=$rows[0]->fulltext;
		}else {
			$rows[0]->text=$rows[0]->introtext.$rows[0]->fulltext;
		}

		if (!$skipPlugins){
			$dispatcher =& JDispatcher::getInstance();
			JPluginHelper::importPlugin("content");                  //naimportujeme všechny pluginy pro zpracování obsahu
			$rows[0]->parameters = new JParameter($rows[0]->attribs);//vytvoříme objekt s parametry článku
			$results = $dispatcher->trigger('onPrepareContent', array (& $rows[0], & $rows[0]->parameters, 0)); //načtení pluginů
		}

		//doplnime uri na clanek
		$rows[0]->uri = JRoute::_(JURI::root() . ContentHelperRoute::getArticleRoute($rows[0]->id), true, -1);

		return $rows[0];
	}

	/**
	 *  Funkce pro načtení sekcí z databáze.
	 *  Vrací pole hodnot ve tvari $arr[id]=title
	 */
	function getSections()
	{
		$db = & JFactory::getDBO();
		$db->setQuery( "SELECT title,id FROM #__sections order by title;" );
		$rows = $db->loadObjectList();
		$result=array();
		foreach ( $rows as $row ) {
			$result[$row->id]=$row->title;
		}
		return $result;
	}

	/**
	 *  Funkce pro načtení kategorií z databáze. Parametrem je ID sekce, pro kterou chceme vypsat kategorie. Pokud chceme kategorie pro všechny sekce, je parametrem -1;
	 *  Vrací pole hodnot ve tvari $arr[id]=title
	 */
	function getCategories($section)
	{
		$db = & JFactory::getDBO();

		//pokud je nastavena sekce, tak ji budeme filtrovat...
		if ($section!=-1) {
			$whereClause = "where section='$section'";
		} else {
			$whereClause = '';
		}
		$db->setQuery( "SELECT title,id FROM #__categories $whereClause order by title;" );
		$rows = $db->loadObjectList();
		$result=array();
		foreach ( $rows as $row ) {
			$result[$row->id]=$row->title;
		}
		return $result;
	}

	/**
	 *  Funkce vracející seznam článků jako výstupní listObject databázového dotazu
	 */
	function getArticles($section,$categorie,$filter,$order,$order_dir,$limitstart,$limit,$editor=false)
	{
		$db = & JFactory::getDBO();

		if(!empty($order))
		{
			$order = "order by $order";
		}

		//nastavení where částí SQL dotazu
		$whereClause="";
		if ($section!=-1){
			$whereClause.=" AND ct.sectionid='".$section."'";
		}
		if ($categorie!=-1){
			$whereClause.=" AND ct.catid='".$categorie."'";
		}
		if ($filter!=''){
			$whereClause.=" AND ct.title LIKE '%".$filter."%'";
		}
		//
		$user =& JFactory::getUser();
		if ($editor){
			/*ošetření přístupových práv pro editaci*/
			if (!$user->authorize('com_content', 'edit', 'content', 'all')){
				/*uživatel nemůže upravovat vše*/       //echo 'edit';
				if ($user->authorize('com_content', 'edit', 'content', 'own')){//echo 'sem';
					$whereClause.=" AND ct.created_by='".$user->get('id')."'";
				}else {  //echo 'no';
					return null;
				}
			}
			/**/
			$whereClause.=" AND ct.checked_out='0'"; //kontrola, jestli daný článek neupravuje někdo jiný...
		}else{
			/*ošetření přístupových práv pro čtení*/
			$whereClause.=" AND ct.access<='".$user->get('aid')."'";
			/**/
		}

		$db->setQuery("SELECT ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,cat.title as categorie,sec.title as section FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE true $whereClause $order $order_dir",$limitstart,$limit);

		$rows = $db->loadObjectList();
		return $rows;
	}

	/**
	 *  Funkce vracející počet článků odpovídajících vybranému filtru
	 */
	function getArticlesCount($section,$categorie,$filter,$editor=false)
	{
		$db = & JFactory::getDBO();

		//nastavení where částí SQL dotazu
		$whereClause="";
		if ($section!=-1){
			$whereClause.=" AND sectionid='".$section."'";
		}
		if ($categorie!=-1){
			$whereClause.=" AND catid='".$categorie."'";
		}
		if ($filter!=''){
			$whereClause.=" AND title LIKE '%".$filter."%'";
		}
		//
		$user =& JFactory::getUser();
		if ($editor){
			/*ošetření přístupových práv pro editaci*/
			if (!$user->authorize('com_content', 'edit', 'content', 'all')){
				/*uživatel nemůže upravovat vše*/
				if ($user->authorize('com_content', 'edit', 'content', 'own')){
					$whereClause.=" AND created_by='".$user->get('id')."'";
				}else {
					return null;
				}
			}
			/**/
			$whereClause.=" AND checked_out='0'"; //kontrola, jestli daný článek neupravuje někdo jiný...
		}else{
			/*ošetření přístupových práv pro čtení*/
			$whereClause.=" AND access<='".$user->get('aid')."'";
			/**/
		}

		$db->setQuery( "SELECT count(id) as pocet FROM #__content WHERE true $whereClause");
		$rows = $db->loadObjectList();
		return $rows[0]->pocet;
	}

	/**
	 *  Funkce pro obalení vraceného kusu článku informační částí a také gInclude komentáři
	 */
	function finalizeGetArticleContent($text,$articleId,$partId=-1,$sourceLine=true)
	{
		$db = & JFactory::getDBO();
		$db->setQuery( "SELECT `title`,date_format(created, '%d.%m.%y %h:%i') as cdate FROM #__content WHERE id='$articleId' LIMIT 1;");
		$rows = $db->loadObjectList();
		if (count($rows)==1){
			$article=$rows[0];
		}else {
			return $text;
		}

		/*odstraneni komentaru z vystupniho textu*/
		$commentStart=strpos($text,"<!--");
		$commentEnd=-3;
		$text2='';
		while (!($commentStart===false)){
			if ((($commentEnd<$commentStart))&&(!($commentEnd===false))){
				$text2.=substr($text,$commentEnd+3,$commentStart-$commentEnd-3);
			}                      //tod
			$commentEnd=strpos($text,"-->",$commentStart);
			$commentStart=strpos($text,'<!--',$commentEnd);
		}
		if (($commentEnd+3<strlen($text))&&(!($commentEnd===false))){
			$text2.=substr($text,$commentEnd+3);
		}

		$text=$text2;
		/**/

		$sourceTextParam=$this->getPluginParameter('sourceText',1);

		$returnText ='<!-- gLink{"article":"'.$articleId.'","part":"'.$partId.'","title":"'.htmlspecialchars($article->title, ENT_QUOTES,'utf-8').'"}-->';
		if (($sourceLine)&&($sourceTextParam==1)){
			$returnText.='<div style="font-size:10px;padding:0px;" class="gincludeTitleDiv">Source article: '.$article->title.', created: '.$article->cdate.'</div>';
		}
		$returnText.=trim($text);
		if (($sourceLine)&&($sourceTextParam==2)){
			$returnText.='<div style="font-size:10px;padding:0px;" class="gincludeTitleDiv">Source article: '.$article->title.', created: '.$article->cdate.'</div>';
		}
		$returnText.='<!-- gLink{}-->';

		return $returnText;
	}

	/**
	 *  Funkce vracející část článku
	 */
	function getArticleContent($articleId,$partId=-1)
	{
		/*načtení článku*/
		$article=$this->prepareArticleDB($articleId);
		if ($article===false){
			return 'content not found...';
		}else {
			$article=$article->text;
		}
		/**/
		if ($partId==-1){
			/*chceme vratit cely obsah*/
			return $article;
		}else {
			$pos=strpos($article, '<!-- gInclude{', 0);
			while (!($pos===false)){
				/*nalezli jsme gInclude instrukci - tak ji zpracujeme*/
				$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
				$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
				$jsonString=substr($instrukce,13, strlen($instrukce)-16); //a z ni pote JSON instrukci
				/* odstraneni zalomeni radku */
				$jsonString=str_replace(chr(13),"",trim($jsonString));
				$jsonString=str_replace(chr(10),"",$jsonString);
				$jsonString=str_replace(chr(9),"",$jsonString);
				/**/
				$json=json_decode($jsonString); //nacteni instrukce do objektu
				if ($json->id==$partId){
					$textPos2=strpos($article,'<!-- gInclude{',$pos2);
					if ($textPos2===false){
						//vracime cely zbytek textu
						$text=substr($article,$pos2+3);
					}else {
						/*pokud je level=0, najdeme konec jinak...*/
						if ($json->level==0){
							$hledej=true;
							while ($hledej==true){
								$textPos2end=strpos($article,'-->',$textPos2); //nalezneme konec komentáře s instrukcí
								$instrukce2=substr($article,$textPos2,($textPos2end+3)-$textPos2); //ziskame samotnou instrukci
								$jsonString2=substr($instrukce2,13, strlen($instrukce2)-16); //a z ni pote JSON instrukci
								/* odstraneni zalomeni radku */
								$jsonString2=str_replace(chr(13),"",trim($jsonString2));
								$jsonString2=str_replace(chr(10),"",$jsonString2);
								$jsonString=str_replace(chr(9),"",$jsonString2);
								/**/
								$json2=json_decode($jsonString2); //nacteni instrukce do objektu
								if (($json2->id==$json->id)||($json2->level=="0")){
									//jde o správný konec
									$hledej=false;
								}else {
									//najdeme zacatek dalsiho gInclude komentare
									$textPos2=strpos($article, '<!-- gInclude{', $textPos2end);
									if ($textPos2===false){
										//dozkopírujeme zbytek textu
										$textPos2=strlen($article)+3;
										$hledej=false;
									}
								}
							}
						}
						/**/
						$text=substr($article,$pos2+3,$textPos2-($pos2+3));
					}
					$pos=false;
					return $text;
				}else {
					$pos=strpos($article, '<!-- gInclude{', $pos2);
				}
			}

		}

	}

	/**
	 *  Funkce vracející seznam částí článku
	 *  $article - id článku
	 *  $parent - ID nadřazené sekce
	 */
	function getParts($articleId,$parent=-1)
	{
		/*načtení článku*/
		$article=$this->prepareArticleDB($articleId);
		if ($article===false){
			exit('content not found...');
		}else {
			$article=$article->text;
		}
		/**/

		$pos=strpos($article, '<!-- gInclude{', 0);

		$vybranaOblast = false;

		while (!($pos===false)){
			/*nalezli jsme gInclude instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,13, strlen($instrukce)-16); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);

			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu

			if (!$json){
				exit();
			}
			if (($json->id!='')&&($json->level!='')){
				//nejde o konec predchozi sekce => pokracujeme ve zpracovani
				if ($json->level==0){
					//jde o vychozi sekci
					$mainResult[$json->id]=$json->title;
					if ($parent==$json->id){ $vybranaOblast=true; }else { $vybranaOblast=false; } //overime, jestli budeme sledovat podsekce
				}elseif($json->level==1){
					if($vybranaOblast==true) {
						//jde o prvek z oblasti, jejíž prvky chceme vypsat
						$partResult[$json->id]=$json->title;
					}
				}
			}
			$pos=strpos($article, '<!-- gInclude{', $pos+13);
		}
		/*vyhodnoceni vysledku vyhledavani v textu*/
		if (count($mainResult)>0){
			$result['main']=$mainResult;
			$result['part']=$partResult;

			return $result;
		}else {
			return false;
		}
	}

	/**
	 *  Funkce pro načtení článku z databáze
	 */
	function getArticleDB($id)
	{
		$db = & JFactory::getDBO();
		$db->setQuery( "SELECT ct.created_by,ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,cat.title as categorie,sec.title as section FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE ct.id='".$id."' LIMIT 1;");
		return $db->loadObjectList();
	}

	/**
	 * Funkce pro načtení seznamu všech gLink odkazů na články
	 */
	function getArticleLinks($articleId)
	{
		$article='';
		/*načtení obsahu článku*/
		$article=$this->prepareArticleDB($articleId);
		if ($article===false){
			return 'content not found...';
		}else {
			$article=$article->text;
		}
		/**/
		$returnArr=array();
		$pos=strpos($article, '<!-- gLink{', 0);
		while (!($pos===false)){
			/*nalezli jsme gInclude instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,10, strlen($instrukce)-13); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);
			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu

			if ($json->article!=''){
				$returnArr[$json->article]=$json->title;
			}

			$pos=strpos($article, '<!-- gLink{', $pos2);
		}
		return $returnArr;
	}

	/**
	 *  Funkce pro složení částí článků, které se mají event. nahradit
	 */
	function getPartsView($articleId)
	{
		/*načtení obsahu článku*/
		$article=$this->prepareArticleDB($articleId);
		if ($article===false){
			return 'content not found...';
		}else {
			$article=$article->text;
		}
		/**/
		$returnString='';
		$searchArr=array();
		$articlesArr=array();
		/*načteme idčka všech článků a jejich částí, které jsou vloženy do článku*/
		$pos=strpos($article, '<!-- gLink{', 0);
		while (!($pos===false)){
			/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,10, strlen($instrukce)-13); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);
			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu

			if ($json->article!=''){
				$searchArr[JRequest::getInt('art'.$json->article)][$json->part]='-2'; //search pole použijeme pro ukládání jednotlivých částí
				$articlesArr[$json->article]=JRequest::getInt('art'.$json->article);  //articles pole použijeme pro co nejefektivnější projití částí
			}

			$pos=strpos($article, '<!-- gLink{', $pos2);
		}
		/*projdeme jednotlivé články a načteme z nich obsahy do pole $searchArr*/
		foreach ($articlesArr as $oldId=>$article2Id) {
			/*načtení článku*/
			$article2=$this->prepareArticleDB($article2Id);
			if (!($article2===false)){
				$article2=$article2->text;
			}
			/**/
			if (!($article2===false)){
				/*projdeme celý článek a najdeme části, které nás zajímají*/
				$posx=strpos($article2, '<!-- gInclude{', 0);
				while (!($posx===false)){
					/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
					$posx2=strpos($article2,'-->',$posx); //nalezneme konec komentáře s instrukcí
					$instrukcex=substr($article2,$posx,($posx2+3)-$posx); //ziskame samotnou instrukci
					$jsonString2=substr($instrukcex,13, strlen($instrukcex)-16); //a z ni pote JSON instrukci
					/* odstraneni zalomeni radku */
					$jsonString2=str_replace(chr(13),"",trim($jsonString2));
					$jsonString2=str_replace(chr(10),"",$jsonString2);
					$jsonString=str_replace(chr(9),"",$jsonString2);
					/**/
					$json2=json_decode($jsonString2); //nacteni instrukce do objektu
					if ($json2->id!=''){
						/*ověříme, jestli nás daná část zajímá a pokud ano, tak ji uložíme do pole*/
						if ($searchArr[$article2Id][$json2->id]==-2){
							if ($json2->level=="0"){
								$searchArr[$article2Id][$json2->id]=$this->getArticleContent($article2Id,$json2->id);
							}else {
								$text2Pos2=strpos($article2,'<!-- gInclude{',$posx2);
								$text2='';
								if ($text2Pos2===false){
									//vracime cely zbytek textu
									$text2=substr($article2,$posx2+3);
								}else {
									$text2=substr($article2,$posx2+3,$text2Pos2-($posx2+3));
								}
								$searchArr[$article2Id][$json2->id]=$text2;
							}
						}
						/**/
					}
					$posx=strpos($article2, '<!-- gInclude{', $posx2);
				}
				/**/
				$returnString.='<input type="hidden" name="art'.$oldId.'" value="'.$article2Id.'" />';
			}
		}
		/*zobrazíme článek s původními i novými obsahy*/
		$pos=strpos($article, '<!-- gLink{', 0);
		$posI=0;
		while (!($pos===false)){
			if ($posI<$pos){
				$returnString.=substr($article,$posI,$pos-$posI);
			}

			/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,10, strlen($instrukce)-13); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);
			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu
			if ($json->article!=''){
				$textPos2=strpos($article,'<!-- gLink{',$pos2);
				$text2='';
				if ($textPos2===false){
					//vracime cely zbytek textu
					$text2=substr($article,$pos2+3);
				}else {
					$text2=substr($article,$pos2+3,$textPos2-($pos2+3));
				}
				if ($text2!=''){
					$returnString.='<div class="oldDiv">';
					$returnString.=$text2;
					$returnString.='</div>';

					$aId=$articlesArr[$json->article];

					if (($searchArr[$aId][$json->part]!=-2)&&(isset($searchArr[$aId][$json->part]))){
						/*v poli je ulozen aktualizovany obsah*/
						$returnString.='<div class="newDiv">';
						$returnString.=$searchArr[$aId][$json->part];
						$returnString.='</div>';
					}else {
						$returnString.='<div class="newDiv">NOT FOUND :-(</div>';
					}

					$returnString.='<div class="radioDiv">
                              <input type="radio" name="art'.$json->article.'part'.$json->part.'" value="old">&nbsp;'.JText::_('ORIGINAL_TEXT').'&nbsp;&nbsp;
                              <input type="radio" name="art'.$json->article.'part'.$json->part.'" value="new">&nbsp;'.JText::_('NEW_TEXT').'&nbsp;&nbsp;
                            </div>';
				}
				$posI=$pos2+strlen($text2)+3;
			}else {
				$posI=$pos2+3;
			}

			$pos=strpos($article, '<!-- gLink{', $pos2);

		}

		if (!($posI+1>=strlen($article))){//pokud jsme nezkopírovali všechno, dokopírujeme zbytek :-)
			$returnString.=substr($article, $posI, strlen($article)-$posI);
		}
		/**/
		return $returnString;
	}

	/**
	 *  Funkce pro aktualizaci textu
	 */
	function getActualText($article)
	{
		$returnString='';
		$db = & JFactory::getDBO();
		$searchArr=array();
		$articlesArr=array();
		/*načteme idčka všech článků a jejich částí, které jsou vloženy do článku*/
		$pos=strpos($article, '<!-- gLink{', 0);
		while (!($pos===false)){
			/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,10, strlen($instrukce)-13); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);
			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu

			if ($json->article!=''){
				$searchArr[JRequest::getInt('art'.$json->article)][$json->part]='-2'; //search pole použijeme pro ukládání jednotlivých částí
				$articlesArr[$json->article]=JRequest::getInt('art'.$json->article);  //articles pole použijeme pro co nejefektivnější projití částí
			}

			$pos=strpos($article, '<!-- gLink{', $pos2);
		}
		/*projdeme jednotlivé články a načteme z nich obsahy do pole $searchArr*/
		foreach ($articlesArr as $article2Id) {
			/*načtení článku*/
			$article2=$this->prepareArticleDB($article2Id);
			if (!($article2===false)){
				$article2=$article2->text;
			}
			/**/
			if (!($article2===false)){
				/*projdeme celý článek a najdeme části, které nás zajímají*/
				$posx=strpos($article2, '<!-- gInclude{', 0);
				while (!($posx===false)){
					/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
					$posx2=strpos($article2,'-->',$posx); //nalezneme konec komentáře s instrukcí
					$instrukcex=substr($article2,$posx,($posx2+3)-$posx); //ziskame samotnou instrukci
					$jsonString2=substr($instrukcex,13, strlen($instrukcex)-16); //a z ni pote JSON instrukci
					/* odstraneni zalomeni radku */
					$jsonString2=str_replace(chr(13),"",trim($jsonString2));
					$jsonString2=str_replace(chr(10),"",$jsonString2);
					$jsonString=str_replace(chr(9),"",$jsonString2);
					/**/
					$json2=json_decode($jsonString2); //nacteni instrukce do objektu
					if ($json2->id!=''){
						/*ověříme, jestli nás daná část zajímá a pokud ano, tak ji uložíme do pole*/
						if ($searchArr[$article2Id][$json2->id]==-2){
							if ($json2->level=="0"){
								$searchArr[$article2Id][$json2->id]=$this->getArticleContent($article2Id,$json2->id);
							}else {
								$text2Pos2=strpos($article2,'<!-- gInclude{',$posx2);
								$text2='';
								if ($text2Pos2===false){
									//vracime cely zbytek textu
									$text2=substr($article2,$posx2+3);
								}else {
									$text2=substr($article2,$posx2+3,$text2Pos2-($posx2+3));
								}
								$searchArr[$article2Id][$json2->id]=$text2;
							}
						}
						/**/
					}
					$posx=strpos($article2, '<!-- gInclude{', $posx2);
				}
				/**/
			}
		}
		/*zobrazíme článek s původními i novými obsahy*/
		$pos=strpos($article, '<!-- gLink{', 0);
		$posI=0;
		while (!($pos===false)){
			if ($posI<$pos){
				$returnString.=substr($article,$posI,$pos-$posI);
			}

			/*nalezli jsme gLink instrukci - tak ji zpracujeme*/
			$pos2=strpos($article,'-->',$pos); //nalezneme konec komentáře s instrukcí
			$instrukce=substr($article,$pos,($pos2+3)-$pos); //ziskame samotnou instrukci
			$jsonString=substr($instrukce,10, strlen($instrukce)-13); //a z ni pote JSON instrukci
			/* odstraneni zalomeni radku */
			$jsonString=str_replace(chr(13),"",trim($jsonString));
			$jsonString=str_replace(chr(10),"",$jsonString);
			$jsonString=str_replace(chr(9),"",$jsonString);
			/**/
			$json=json_decode($jsonString); //nacteni instrukce do objektu
			if ($json->article!=''){
				$textPos2=strpos($article,'<!-- gLink{',$pos2);
				$text2='';
				if ($textPos2===false){
					//vracime cely zbytek textu
					$text2=substr($article,$pos2+3);
				}else {
					$text2=substr($article,$pos2+3,$textPos2-($pos2+3));
				}
				/*if ($text2!=''){*/
				/*zkontrolujeme, jak se uživatel rozhodl a podle toho vložíme buď starý, nebo nový obsah*/
				$aId=$articlesArr[$json->article];
				if (JRequest::getCmd('art'.$json->article.'part'.$json->part,'old')=='new'){
					/*vložíme nový obsah...*/
					if (($searchArr[$aId][$json->part]!='')&&($searchArr[$aId][$json->part]!=-2)){
						$returnString.=$this->finalizeGetArticleContent($searchArr[$aId][$json->part],$aId,$json->part);
					}else {
						$returnString.=$this->finalizeGetArticleContent('<div style="color:red">INCLUDED CONTENT NOT FOUND...</div>',$aId,$json->part);
					}
				}else {
					/*zachováme původní*/
					$returnString.=$this->finalizeGetArticleContent($text2,$json->article,$json->part,false);
				}
				/*}*/
				$posI=$pos2+strlen($text2)+3;
			}else {
				$posI=$pos2+3;
			}

			$pos=strpos($article, '<!-- gLink{', $pos2);

		}

		if (!($posI+1>=strlen($article))){//pokud jsme nezkopírovali všechno, dokopírujeme zbytek :-)
			$returnString.=substr($article, $posI, strlen($article)-$posI);
		}
		/**/
		return $returnString;
	}

	/**
	 *  Funkce pro uložení aktualizovaného článku
	 */
	function saveParts()
	{
		$article='';
		/*načtení obsahu článku*/
		$db = & JFactory::getDBO();
		$articleId=JRequest::getInt('article',-1);
		$rows=$this->getArticleDB($articleId);
		/**/
		if (!($rows[0]===false)){
			/*ověření uživatelských práv*/
			$user =& JFactory::getUser();
			if (!$user->authorize('com_content', 'edit', 'content', 'all')){
				/*uživatel nemůže upravovat vše*/
				if (!(($user->authorize('com_content', 'edit', 'content', 'own'))&&($rows[0]->created_by==$user->get('id')))){
					exit('article edit error... Access not allowed!');
				}
			}
			/**/
			$art=$this->prepareArticleDB($articleId,'introtext',true);
			$introtext=$this->getActualText($art->text);
			$art=$this->prepareArticleDB($articleId,'fulltext',true);
			$fulltext =$this->getActualText($art->text);

			$introtext=$db->getEscaped($introtext);
			$fulltext =$db->getEscaped($fulltext);

			//uložení dat do databáze
			$user =& JFactory::getUser();
			$db->setQuery( "UPDATE #__content SET `introtext`='".$introtext."',`fulltext`='".$fulltext."', `modified_by`='".$user->get('id')."', `modified`=NOW() WHERE id='$articleId' LIMIT 1;");
			$rows = $db->query();

			//$article=$rows[0]->introtext.'<hr id="system-readmore" title="page-break"/>'.$rows[0]->fulltext;
		}else {
			exit('content not found...');
		}
	}
}
?>