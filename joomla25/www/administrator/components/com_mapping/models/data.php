<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

/**
 * @package		Joomla
 * @copyright	Copyright (C) 2010 Stanislav Vojíř. All rights reserved.
 * @license		GNU/GPL
 * 
 *  Model obsluhující přístupy k datům. Součást komponenty pro vkládání obsahu gInclude. 
 */
class DataModel extends JModel
{
  /**
   * Funkce pro načtení jednoho článku z databáze, které vrací na výstup
   * 
   * @return object (id,title,text,introtext,fulltext)      
   */     
  function loadArticle($articleId,$unsetTexts=false){    
    $db = & JFactory::getDBO();
    $db->setQuery( "SELECT id,`title`,`introtext`,`fulltext` FROM #__content WHERE id='$articleId' LIMIT 1;");
    $rows = $db->loadObjectList();   
    if (count($rows)==1){  
      $rows[0]->text=$rows[0]->introtext.$rows[0]->fulltext;
      /*TODO pracovni nacitani dat z externiho souboru*/
        if (strpos(' x'.$rows[0]->text,"xFILE:")){
          //máme načíst externí soubor
          $filename=substr($rows[0]->text,5);
          $rows[0]->text=file_get_contents($filename);
        }
      /**/
      if ($unsetTexts){
        unset($rows[0]->introtext);
        unset($rows[0]->fulltext);
      }
      return $rows[0];   
    }else {
      return '';
    } 
  }
  
  /**
   *  Funkce pro načtení XML dat jednoho článku
   */     
  function loadArticleXML($articleId){
    $article=$this->loadArticle($articleId);
    return simplexml_load_string($article->text);
  } 
  
  /**
   * Funkce pro ulozeni dat jednoho článku
   */     
  function saveArticle($id,$data){  
    $db = & JFactory::getDBO();    
    $db->setQuery( "UPDATE #__content SET `introtext`='".$db->getEscaped($data)."',`fulltext`='' WHERE id='$id' LIMIT 1;");
    $db->query();
  } 
  
  /**
   *  Funkce pro uložení simpleXml do jednoho článku v DB
   */     
  function saveArticleXML($id,$xml){
    $this->saveArticle($id,$xml->asXML());
  } 


  /**
   *  Funkce pro načtení sekcí z databáze. 
   *  Vrací pole hodnot ve tvari $arr[id]=title 
   */ 
  function getSections(){
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
  function getCategories($section){
    $db = & JFactory::getDBO();
    if ($section!=-1){$whereClause="AND section='".$section."'";} //pokud je nastavena sekce, tak ji budeme filtrovat...
    $db->setQuery( "SELECT title,id FROM #__categories WHERE section in (select id from #__sections) $whereClause order by title;" );
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
  function getArticles($section,$categorie,$filter,$order,$order_dir,$limitstart,$limit,$editor=false,$metakey=''){
    $db = & JFactory::getDBO();
  
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
          /*uživatel nemůže upravovat vše*/       echo 'edit';
          if ($user->authorize('com_content', 'edit', 'content', 'own')){echo 'sem';
            $whereClause.=" AND ct.created_by='".$user->get('id')."'";
          }else {  echo 'no';
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
    if ($metakey!=''){
      $whereClause.=' AND `metakey`="'.$metakey.'"';
    }        
    //$db->setQuery("SELECT ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,cat.title as categorie,sec.title as section FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE true $whereClause order by $order $order_dir",$limitstart,$limit);
    $db->setQuery("SELECT ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,cat.title as categorie,sec.title as section FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE state>-2 $whereClause order by $order $order_dir",$limitstart,$limit);
     
    $rows = $db->loadObjectList();     
    return $rows;
  }
  
  /**
   *  Funkce vracející počet článků odpovídajících vybranému filtru
   */       
  function getArticlesCount($section,$categorie,$filter,$editor=false,$metakey=''){
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
    if ($metakey!=''){
      $whereClause.=' AND `metakey`="'.$metakey.'"';
    }          
    $db->setQuery( "SELECT count(id) as pocet FROM #__content WHERE 1 $whereClause");
    $rows = $db->loadObjectList();     
    return $rows[0]->pocet;
  }    
  
  /**
   *  Funkce pro načtení článku z databáze
   */             
  function getArticleDB($id){
    $db = & JFactory::getDBO();
    $db->setQuery( "SELECT ct.created_by,ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,cat.title as categorie,sec.title as section FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE ct.id='".$id."' LIMIT 1;");
    return $db->loadObjectList();
  }        
  
  
  /**
   *  Funkce pro založení nového článku   
   */       
   function newArticle($articleName,$articleContent='',$state='1',$section=-1,$category=-1){
     //TODO
     $db = & JFactory::getDBO();
     if ($section!=-1){
       $sectionid=$section;
     }else{
       $sectionid=0;
     }
     if ($category!=-1){
       $categoryid=$category;
     }else{
       $categoryid=0;
     }
     
     $user =& JFactory::getUser();
     $db->setQuery("INSERT INTO #__content (title,introtext,state,sectionid,catid,created,created_by,metakey,version)VALUES ('".$db->getEscaped($articleName)."','".$db->getEscaped(stripslashes($articleContent))."', '".$state."', '".$sectionid."', '".$categoryid."',NOW(),'".$user->get('id')."','BKEF','1');");
     if($db->query()){
       return $db->insertid();
     }else{
       return false;
     }  
   }
   
    
   function isArticleWritable($id){
     $article=$this->getArticleDB($id);
     $article=@$article[0];
     
     //TODO vyreseni prav
     return true;
   } 
    
}  
?>