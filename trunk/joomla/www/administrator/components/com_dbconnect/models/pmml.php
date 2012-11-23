<?php

  class dbconnectModelPmml extends JModel{
    
    var $uniDb;
    /**
     *  Funkce pro vygenerování PMML na základě mapování a dat úlohy
     */         
    public function generatePMML($task,$connection,$bkefModel,$fmlModel){
      $user =& JFactory::getUser();
      $xml=simplexml_load_string('<'.'?xml version="1.0" encoding="utf-8"?'.'>
                                  <PMML version="4.0" xmlns="http://www.dmg.org/PMML-4_0" 
                                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  
                                        xmlns:pmml="http://www.dmg.org/PMML-4_0"
                                        xsi:schemaLocation="http://www.dmg.org/PMML-4_0 http://sewebar.vse.cz/schemas/PMML4.0+GUHA0.1.xsd" >
                                    <Header copyright="Copyright (c) KIZI UEP">
                                      <Extension name="author" value="'.$user->get('name').'" />
                                      <Extension name="format" value="4ftMiner.Task" />
                                      <Extension name="dataset" value="'.$connection->table.'" />
                                      <Application name="SEWEBAR" version="1.0" />
                                      <Annotation>DM Task export from SEWEBAR DB CONNECT</Annotation>
                                      <Timestamp>'.date('c').'</Timestamp>
                                    </Header>
                                    <MiningBuildTask><Extension name="DatabaseDictionary"></Extension></MiningBuildTask>
                                    <DataDictionary></DataDictionary>
                                    <TransformationDictionary></TransformationDictionary>
                                  </PMML>');
       /*vytvorime MiningBuildTask*/
       $extTable=$xml->MiningBuildTask->Extension->addChild('Table');
       $extTable->addAttribute('name',(string)$connection->table);
       //připravíme uzel pro vypsání jednotlivých sloupců
       $extTableColumns=$extTable->addChild('Columns');
       //zadáme primární klíč
       $extTablePrimaryKey=$extTable->addChild('PrimaryKey');
       $extTablePrimaryKeyColumn=$extTablePrimaryKey->addChild('Column');
       $extTablePrimaryKeyColumn->addAttribute('name',(string)$connection->primary_key);
       $extTablePrimaryKeyColumn->addAttribute('primaryKeyPosition',0);
       /*vytvorime DataDictionary*/
       $columns=simplexml_load_string($task->columns);
       $dataDictionary=$xml->DataDictionary;
       if (count($columns->column)>0){
         //máme nějaké sloupce
         foreach ($columns->column as $column) { 
         	 if ($column['use']){
             $dataField=$dataDictionary->addChild('DataField');
             $dataField->addAttribute('name',(string)$column['name']);
             $dataField->addAttribute('dataType',(string)$column['type']);
             //TODO není generován optype
             $dataField->addAttribute('optype','categorical');
             //vypíšeme sloupec také do předchozí extension
             $extColumn=$extTableColumns->addChild('Column');
             $extColumn->addAttribute('name',(string)$column['name']);
             $extColumn->addAttribute('dataType',(string)$column['type']);
           }
         }             
       }
       $pmmlFieldNamesArr=$fmlModel->getPmmlFieldNamesArr();
       $fieldMappingsArr=$fmlModel->getFieldMappingsArr();
       $derivedFieldNamesArr=array();
       /*Vytvorime TransformationDictionary*/
       $transformationDictionary=$xml->TransformationDictionary;
       if (count($columns->column)>0){
         foreach ($columns as $column) {   
           $columnName=(string)$column['name'];
           $pmmlFieldName=$columnName;
           if ((!$column['use'])||(!isset($pmmlFieldNamesArr[$columnName]))){continue;}  
           
         	 //kontrola, zda daný sloupec existuje v FML a zda má zadaný preprocessing
           if ($fmlModel->hasPreprocessingForPmmlField($pmmlFieldName)){  
             $bkefInfoArr=$fmlModel->getPmmlPreprocessing($pmmlFieldName); 
             if (!$bkefInfoArr){continue;}           
             $bkefPreprocessing=$bkefModel->getPreprocessingHint($bkefInfoArr['metaattributeName'],$bkefInfoArr['formatName'],$bkefInfoArr['preprocessingHint']);
             if (!$bkefPreprocessing){continue;}          
             $bkefFormat=$bkefModel->getFormat($bkefIntoArr['metaattributeName'],$bkefInfoArr['formatName']);
              
             //máme všechny potřebné údaje - můžeme se pokusit sloupec předzpracovat
             $derivedField=$transformationDictionary->addChild('DerivedField');                              
             if (!in_array($bkefInfoArr['metaattributeName'],$derivedFieldNamesArr)){
               $derivedName=$bkefInfoArr['metaattributeName'];
             }else{
               $derivedName=$pmmlFieldName.' ('.$bkefIntoArr['metaattributeName'].')';               
             }
             $derivedField->addAttribute('name',$derivedName);
             //TODO datatype,optype              
             //výběr aktuálního způsobu preprocessingu
             if (isset($bkefPreprocessing->EachValueOneBin)){
               $mapValues=$derivedField->addChild('MapValues');
               $mapValues->addAttribute('outputColumn',$derivedName);
               $fieldColumn=$mapValues->addChild('FieldColumnPair');
               $fieldColumn->addAttribute('column',$pmmlFieldName);
               $fieldColumn->addAttribute('field',$derivedName);
               $inlineTable=$mapValues->addChild('InlineTable');
               $this->discretize_eachValueOneBin($inlineTable,$connection,$pmmlFieldName);
             
             }elseif(isset($bkefPreprocessing->IntervalEnumeration)){
               $discretize=$derivedField->addChild('Discretize');
               $discretize->addAttribute('field',$pmmlFieldName);
               $this->discretize_intervalEnumeration($discretize,$bkefPreprocessing->IntervalEnumeration);
             
             }elseif(isset($bkefPreprocessing->EquidistantInterval)){
               $discretize=$derivedField->addChild('Discretize');
               $discretize->addAttribute('field',$pmmlFieldName);
               $this->discretize_equidistant($discretize,$bkefPreprocessing->EquidistantInterval);
             
             }elseif(isset($bkefPreprocessing->NominalEnumeration)){
               $mapValues=$derivedField->addChild('MapValues');
               $mapValues->addAttribute('outputColumn',$derivedName);
               $fieldColumn=$mapValues->addChild('FieldColumnPair');
               $fieldColumn->addAttribute('column',$pmmlFieldName);
               $fieldColumn->addAttribute('field',$derivedName);
               $inlineTable=$mapValues->addChild('InlineTable');
               
               
               //TODO
               $this->discretize_nominalEnumeration($inlineTable,$fmlModel,$bkefPreprocessing->NominalEnumeration,$connection,$pmmlFieldName);
             }
             //--výběr aktuálního způsobu preprocessingu            
           }
         }
       }
       
       //TODO
        //exit($xml->asXML());
       /*vrátíme vytvořené PMML zadání*/
       return $xml->asXML();                    
    }
    
    /**
     *  Funkce pro pripraveni DerivedField do TransformationDictionary pro IntervalEnumeration
     */         
    private function discretize_intervalEnumeration(&$discretize,$intervalEnumeration){
      if (count($intervalEnumeration->IntervalBin)>0){
        foreach ($intervalEnumeration->IntervalBin as $intervalBin) {
          if (count($intervalBin->Interval)==0){continue;}
          
          if (isset($intervalBin->Name)){$intervalName=(string)$intervalBin->Name.' ';}else{$intervalName='';}
          foreach ($intervalBin->Interval as $interval) {
          	$discretizeBin=$discretize->addChild('DiscretizeBin');
            $leftMargin=(string)$interval["leftMargin"];
            $rightMargin=(string)$interval["rightMargin"];
            $closure=(string)$interval['closure'];                                                 //TODO hranice intervalů!
            $discretizeBin->addAttribute('binValue',$intervalName.'['.$leftMargin.';'.$rightMargin.']');
            $pmmlInterval=$discretizeBin->addChild('Interval');
            $pmmlInterval->addAttribute('closure',$closure);
            $pmmlInterval->addAttribute('leftMargin',$leftMargin);
            $pmmlInterval->addAttribute('rightMargin',$rightMargin);            
          }
        }
      }
    } 
    
    /**
     *  Funkce pro pripraveni DerivedField do TransformationDictionary pro Equidistant
     */
    private function discretize_equidistant(&$discretize,$equidistant){
      $start=@floatval($equidistant->Start);
      $end=@floatval($equidistant->End);
      $step=@floatval($equidistant->Step);
      if (($start<$end)&&($step>0)){
        //máme k dispozici nějaká čísla - vygenerujeme intervaly
        
        while ($start<$end){ 
        	$closure='closed';
          $endX=$start+$step;
          if ($endX>=$end){
            //jde o posledni interval
            $endX=$end;
            $closure.='Closed';
            $intervalName=JText::_('INTERVAL_LEFT_CLOSED').$start.';'.$endX.JText::_('INTERVAL_RIGHT_CLOSED');
          }else{
            $closure.='Open';
            $intervalName=JText::_('INTERVAL_LEFT_CLOSED').$start.';'.$endX.JText::_('INTERVAL_RIGHT_OPEN');
          }
          
          $discretizeBin=$discretize->addChild('DiscretizeBin');
          $discretizeBin->addAttribute('binValue',$intervalName);
          $pmmlInterval=$discretizeBin->addChild("Interval");
          $pmmlInterval->addAttribute('closure',$closure);
          $pmmlInterval->addAttribute('leftMargin',$start);
          $pmmlInterval->addAttribute('rightMargin',$endX);
          
          $start+=$step;
        }
      }
    }
    
    
    /**
     *  Funkce pro připravení DerivedField do TransformationDictionary pro EachValueOneBin
     */         
    private function discretize_eachValueOneBin(&$inlineTable,$connection,$columnName){   
      //pokusíme se připojit k externí databázi...
      if (!$this->prepareUniDb($connection)){return;}         
      //načteme všechny hodnoty z daného sloupce a vypíšeme je
      $values=$this->uniDb->getColumnValues($connection->table,$columnName);      
      if (count($values)>0){
        foreach ($values as $value) {
        	$row=$inlineTable->addChild('row');
          $row->addChild('column',$value[0]);
          $row->addChild('field',$value[0]);
        }
      }
    }
    
    /**
     *  Funkce pro připravení DerivedField do TransformationDictionary pro NominalEnumeration
     */         
    private function discretize_nominalEnumeration(&$inlineTable,$fmlModel,$nominalEnumeration,$connection,$columnName){
      //připravíme si pole s roztříděním jednotlivých hodnot do skupin podle BKEFu
      $valuesGroupsArr=array();
      if (@count($nominalEnumeration->NominalBin)>0){
        foreach ($nominalEnumeration->NominalBin as $nominalBin){
        	$nominalBinName=@(string)$nominalBin->Name;
          if (!$nominalBinName){$nominalBinName='group';}
          if (@count($nominalBin->Value)>0){
            foreach ($nominalBin->Value as $value) {
            	$valuesGroupsArr[((string)$value)]=$nominalBinName;
            }
          }
        }
      }
      //připravíme si mapovací pole
      $valuesMapArr=$fmlModel->getValuesMappingsArr($columnName);
      //TODO!!!
      //pokusíme se připojit k externí databázi a udělat preprocessing konkrétních hodnot
      if (!$this->prepareUniDb($connection)){return;}         
      //načteme všechny hodnoty z daného sloupce a vypíšeme je
      $values=$this->uniDb->getColumnValues($connection->table,$columnName);      
      if (count($values)>0){         
        foreach ($values as $valueItem) {
          $value=$valueItem[0];
          if (is_numeric($value)){
            $mappedValue=$value;
          }else{                          
            $mappedValue=@$valuesMapArr[$value];  
            if (!$mappedValue){continue;}        
          }                         
          $valueGroup=@$valuesGroupsArr[$mappedValue];
          if (!$valueGroup){continue;}   
        	$row=$inlineTable->addChild('row'); 
          $row->addChild('column',(string)$value);
          $row->addChild('field',(string)$valueGroup);
        }
        //exit(var_dump($inlineTable));
      }
      
      
      //exit(var_dump($valuesGroupsArr));
      //exit(var_dump($nominalEnumeration));
      //TODO preprocessing
    } 
    
    private function prepareUniDb($connection){    
      if (!$this->uniDb){                        
        //zatím není připravený objekt pro práci s uniDb
        require_once (JPATH_COMPONENT.DS.'models'.DS.'unidb.php');
        $this->uniDb=new dbconnectModelUnidb(); 
        if ($this->uniDb->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name)!=""){
          $this->uniDb=null;
          return false;
        }
      }
      return true; 
    }
    
  }
  
?>