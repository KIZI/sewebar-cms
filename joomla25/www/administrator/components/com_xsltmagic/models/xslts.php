<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

define("ROOT", JPATH_ROOT.DS.'xml');

/**
 * JModel for XSLT Editor.
 *
 * @package com_xsltmagic
 */
class XsltmagicModelXslts extends JModel {

    var $_sources = null;
    var $_source = null;


    /*! @function getListFileTree
        @abstract function generating file/folder tree of current folder 
        @param path string - path to folder, what we wnat display
        @param jump - variable useful to generate directory link    
        @return Array - file/folder tree
     */
    public function getListFileTree($path='',  $jump=''){
        $option="com_xsltmagic";

        $files = array();
        $folders = array();
        $size=0;

        foreach (scandir($path) as $v){
            if (!is_dir($path . '/' . $v)){
            $files[] = $path.'/'.$v;
            continue;
            }

            if (substr($v, 0, 1) != '.'){    // not need '.' '..'
                $folders[] = $path . '/' . $v;
            }
        }

        natcasesort($folders);
        natcasesort($files);

        if (isset($jump)){
            $link=explode('&', $jump);
            $url='';

            for ($i=1;$i<count($link)-1 ;$i++ ) {
                $url.='&'.$link[$i];
            }   

            $row[$size]->link = JRoute::_("index.php?option={$option}&controller=xslts".$url);
            $row[$size]->checked_out = false;
            $row[$size]->name = '...';
            $size++;
        }

        // Folders
        for ($f=0; $f<count($folders); $f++){
            $currentFolder = str_replace($path.'/', '', $folders[$f]);

            $link=$jump.'&amp;jump[]='.basename($currentFolder);
            $row[$size]->link = JRoute::_("index.php?option={$option}&controller=xslts".$link);
            $row[$size]->checked_out = false;
            $row[$size]->name = basename($currentFolder);
            $row[$size]->type = 'Folder';
            $row[$size]->modified = date("d.m.Y H:i:s", filemtime($path.DS.$currentFolder));
            $size++; 
        }

        // Process files
        for ($h=0; $h<count($files); $h++){
            $currentFile = str_replace($path.'/', '', $files[$h]);
            $currentEncoded = urlEncode($currentFile); 

            $subArr=explode('%2F', $currentEncoded);
            $filename=$subArr[count($subArr)-1];
            $subArrr=explode('.', $filename);
            $extension=$subArrr[count($subArrr)-1];
            
            $row[$size]->link = JRoute::_("index.php?option={$option}&id[]={$filename}&task=edit&controller=xslts".$jump);
            $row[$size]->checked_out = false;
            $row[$size]->name = $filename;
            $row[$size]->type = $extension;
            $row[$size]->modified = date("d.m.Y H:i:s", filemtime($path.DS.$currentFile));
            $row[$size]->fileSize = round(filesize ($path.DS.$currentFile ) /1024)  .' kb';
            $size++;
        }

        return $row;
    }

    /*! @function createFileList
        @abstract RECURSIVE function that create list of FILES, useful for XSLT Magic menu 
        @param strFolder string - for recursive function parameter, path to folder
        @param level int- folder level
        @return source string - file to select
     */
    public function createFileList($strFolder='', $level=-1, $source=''){
        $arrPages = scandir($this->getRoot() . '/' . $strFolder);
		
        ++$level;
        $strFolders = '';
        $strFiles = '';

        // Recursively list all 
        foreach ($arrPages as $strFile){
            if (substr($strFile, 0, 1) == '.'){
            continue;
            }

           if (is_dir($this->getRoot() . '/' . $strFolder . '/' . $strFile)){
               $strFolders .= $this->createFileList($strFolder . '/' . $strFile, $level);
           }
   
           $actfile=$strFolder . '/' . $strFile;
   
           if ($actfile==$source){
               $selected=' selected="selected"';
           }else{
               $selected='';
           }
   
           if (!is_dir($this->getRoot() . '/' . $strFolder . '/' . $strFile)) {
               $strFiles .= sprintf('<option value="%s" '.$selected.'>%s%s -- ('.DS.$strFolder.')</option>', $strFolder . '/' . $strFile, str_repeat("&nbsp;", (2 * $level)), $this->specialchars($strFile));
           }
        }

        return $strFiles . $strFolders;
    }

    /*! @function specialchars
        @abstract replacing unsupported characters
        @param strString string
        @return string - replaced chars
     */
    function specialchars($strString){
        $arrFind = array('"', "'", '<', '>');
        $arrReplace = array('&#34;', '&#39;', '&lt;', '&gt;');

        return str_replace($arrFind, $arrReplace, $strString);
    }

    /*! @function getStyle
        @param id int - unique identifier
        @return string - source
     */
    public function getStyle($id){
        return $this->_source;
    }

    /*! @function getRoot
        @abstract  
        @return string root path
    */
    function getRoot(){
        $params = &JComponentHelper::getParams( 'com_xsltmagic' );
        $root_path = JPATH_ROOT.DS.$params->get( 'root' );
        return $root_path;    
        // return ROOT;
    }
}

?>