<?php 		 
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

define("ROOT", JPATH_ROOT.DS.'xml');
define("EDITABLEFILES", 'htm,html,css,js,txt,log,xml,xsd,xsl,sch');

jimport( 'joomla.application.component.controller' );

/**
 * Controller for XSTL Editor.
 *
 * @package		com_xsltmagic
 */
class XsltMagicControllerXslts extends JController
{
    	/**
    	 * Constructor
    	 */
    function __construct( $config = array() ){
        parent::__construct( $config );
        // Register Extra tasks
        $this->registerTask( 'add',		'addNew' );
        $this->registerTask( 'apply',	'save' );
        $this->registerTask( 'importFile',	'importFile' );
     }

    /*! @function display
        @abstract display list of folders and files, basic view of XSLT Editor            
    */
    function display(){
        $option="com_xsltmagic";
       
        $document =& JFactory::getDocument();
        $viewName = JRequest::getVar('controller', 'xslts');
        $viewType = $document->getType();
   
        $view =& $this->getView($viewName, $viewType);
        $model = &$this->getModel('xslts');
        $user=& JFactory::getUser();

        if (isset($_GET['jump'])){
            for ($i=0;$i<count($_GET['jump']) ;$i++ ) {
                $url.='/'.$_GET['jump'][$i];
                $jump.='&amp;jump[]='.$_GET['jump'][$i];	
            }
        }

        if(is_dir($this->getRoot().$url)){  
            $rows = $model->getListFileTree($this->getRoot().$url, $jump);
        }else{
            if(!isset($_GET['no'])){
                $this->setMessage( JText::sprintf( 'File or folder -- '.  $this->getRoot().$url .' -- doesnt exist', $n )); 
                $this->setRedirect("index.php?option=$option&controller=xslts&no=1".$table->jump);   
            }
        }
		
   
        jimport('joomla.html.pagination');
        $pageNav = new JPagination( $total, $limitstart, $limit );

        $view->setLayout('default');

        $view->assignRef('rows', $rows);
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('lists', $lists);

        $view->display();
    }


    /*! @function remove
        @abstract remove selected file or folder, RECURSIVE function 
        @param source filename - parameter for recursion
    */
    function remove($source='') {
        $option="com_xsltmagic";

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        if ($source==''){
            $ids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
            $source=$ids[0];
            $source=$this->getRoot() . DS . $this->getURL() . $source;
        }
 
        if (is_dir($source)) {
            $objects = scandir($source);

            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($source."/".$object) == "dir"){
                        // echo $source."/".$object;
                        $this->remove($source."/".$object); // recursive call if folder
                    }else{
                        unlink($source."/".$object); // delete file 
                    }
                }
                reset($objects);
                rmdir($source); // delete folder
            }   
        }else{
            unlink ($source); // delete just single file
        }
        
        $this->setMessage( JText::sprintf( 'File or folder -- '.  $this->getURL() . $source .' -- has been deleted ', $n )); 
        $this->setRedirect("index.php?option=$option&controller=xslts".$this->getJump());   
    }

    /*! @function edit
        @abstract function switch to detailed view (xslt) whether source is editable file + initizializing data for edit
            whether source is folder redirecting to folder edit function
    */
    function edit(){
        $option="com_xsltmagic";
      
        if (isset($_GET['id'][0])){
            $source=$_GET['id'][0];
        }else{
            $ids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
            $source=$ids[0];
        }

        $currentFile = $this->getRoot() . '/' . $this->getURL() . $source;
        $currentEncoded = urlEncode($currentFile); 
        $subArr=explode('%2F', $currentEncoded);
        $filename=$subArr[count($subArr)-1];
        $subArrr=explode('.', $filename);
        $extension=$subArrr[count($subArrr)-1];

        //Check whether file type is editable
        if (!in_array($extension, $this->trsplit(',', $this->getEditableFiles())) and (!is_dir( $this->getRoot() . '/' . $this->getURL() . $source))){
            $this->setMessage( JText::sprintf( 'File type '.$extension.' is not allowed to be edited', $n )); 
            $this->setRedirect("index.php?option=$option&controller=xslts".$this->getJump());
        }

        // checking directory 
        if (is_dir( $this->getRoot() . '/' . $this->getURL() . $source)){
            $this->setRedirect( "index.php?option=$option&controller=xslts&task=addNew&folder={$source}".$table->jump );
        }

        $strContent = file_get_contents($this->getRoot() . '/' . $this->getURL() . $source);
		
        $document =& JFactory::getDocument();
        $viewName = 'xslt';
        $viewType = $document->getType();
        $view =& $this->getView($viewName, $viewType);

        // Get/Create the model
        if ($model = &$this->getModel('xslts')) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        $view->setLayout('default');
        $model->_source ->name = $filename;
        $model->_source ->jump = $this->getJump();
        $model->_source ->url = $this->getURL(); 
        $model->_source ->style = $strContent;

        $view->display();
    }

    /*! @function trsplit
        @abstract Split a string into fragments, remove whitespace and return fragments as array 
        @param string
        @param string
        @return string
     */

    function trsplit($strPattern, $strString){
        $arrFragments = array_map('trim', preg_split('/'.$strPattern.'/ui', $strString));

        if (count($arrFragments) < 2 && !strlen($arrFragments[0])){
            return array();
        }
        
        return $arrFragments;
    }

    /*! @function save
        @abstract Save file after editing, change name, save content 
    */
    function save(){
        $option="com_xsltmagic";

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $table->prevName = JRequest::getVar( 'prevName', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->newName = JRequest::getVar( 'name', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->jump = JRequest::getVar( 'jump', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->url = JRequest::getVar( 'url', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->style = JRequest::getVar( 'code', '','post', 'string', JREQUEST_ALLOWRAW );

        file_put_contents($this->getRoot(). '/'. $table->url.$table->prevName, $table->style);
        $message="File was updated";
  
        if($table->prevName !=$table->newName){
            rename ($this->getRoot(). '/'. $table->url.$table->prevName, $this->getRoot(). '/'. $table->url.$table->newName);
            $message.=' and renamed from '.$table->prevName.' to '.$table->newName;
        }

        $this->setRedirect( "index.php?option=$option&controller=xslts".$table->jump);
  
        switch (JRequest::getCmd( 'task' )){
            case 'apply':
            $this->setRedirect( "index.php?option=$option&controller=xslts&task=edit&id[]={$table->newName}".$table->jump );
            break;
        }

        $this->setMessage($message, $n );
    }

    /*! @function cancel
        @abstract "back" function from detailed view, return to previous page
    */
    function cancel(){
        $option="com_xsltmagic";

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $table->jump = JRequest::getVar( 'jump', '','post', 'string', JREQUEST_ALLOWRAW );
        $this->setRedirect( "index.php?option=$option&controller=xslts".$table->jump);
    }

    /*! @function import
        @abstract switching to "upload file" view, after click on Import File button
    */
    function import(){
        $option="com_xsltmagic";

        JRequest::checkToken() or jexit( 'Invalid Token' );

        $document =& JFactory::getDocument();
        $viewName = 'xsltuploadfile';
        $viewType = $document->getType();
        $view =& $this->getView($viewName, $viewType);
   
        // Get/Create the model
        if ($model = &$this->getModel('xslts')) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
            $view->setLayout('default');
        }
      
        $model->_source ->name = $source;
        $model->_source ->jump = $this->getJump();
        $model->_source ->url = $this->getURL();
        $view->display();
    }

    /*! @function importFile
        @abstract uploading and moving file function, after click on upload button in xsltuploadfile view
    */
    function importFile(){
        $option="com_xsltmagic";
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $table->newName = JRequest::getVar( 'uploadedfile', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->jump = JRequest::getVar( 'jump', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->url = JRequest::getVar( 'url', '','post', 'string', JREQUEST_ALLOWRAW );

        // Where the file is going to be placed 
        $target_path = $this->getRoot().DS.$table->url;

        /* Add the original filename to our target path.  
        Result is "uploads/filename.extension" */
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

        $params = &JComponentHelper::getParams( 'com_xsltmagic' );
        $size = $params->get( 'maxSize' )*1024;

        if ($_FILES["uploadedfile"]["size"] < $size){
            if ($_FILES["uploadedfile"]["error"] > 0){
                echo "Return Code: " . $_FILES["uploadedfile"]["error"] . "<br />";
            }else{
                if (file_exists($target_path)){
                    $this->setMessage( JText::sprintf( "File already exist", $n)); 
                    $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);
                    continue;                    
                }else{
                    move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_path);
                    echo "Stored in: " . $target_path;
                }
            }
        }else{
            $this->setMessage( JText::sprintf( "Invalid File", $n)); 
            $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);
            continue;       
        }
 
        $this->setMessage( JText::sprintf( "The file ".  basename( $_FILES['uploadedfile']['name'])." has been uploaded", $n)); 
        $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);   
    }


    /*! @function addNew
        @abstract after clicking on edit button with selected directory, or new folder, initializing data for editing folder + switch to folder view
    */
    function addNew() {
        if (isset($_GET['folder'])){
            $source=$_GET['folder'];
        }

        $document =& JFactory::getDocument();
        $viewName = 'xsltfolder';
        $viewType = $document->getType();
        $view =& $this->getView($viewName, $viewType);
   
        // Get/Create the model
        if ($model = &$this->getModel('xslts')) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
            $view->setLayout('default');
        }
      
        $model->_source ->name = $source;
        $model->_source ->jump = $this->getJump();
        $model->_source ->url = $this->getURL();
        $view->display();
    }

    /*! @function saveF
        @abstract saving or renaming folder function
    */
    function saveF() {
        $option="com_xsltmagic";
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
  
        $table->prevName = JRequest::getVar( 'prevName', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->newName = JRequest::getVar( 'name', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->jump = JRequest::getVar( 'jump', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->url = JRequest::getVar( 'url', '','post', 'string', JREQUEST_ALLOWRAW );
        $table->style = JRequest::getVar( 'code', '','post', 'string', JREQUEST_ALLOWRAW );
   
        if (is_dir( $this->getRoot() . '/' . $table->url . $table->newName)){
            $this->setMessage( JText::sprintf( 'Directory with "'.$table->newName.'" is already exist', $n )); 
            $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);   
        }else{
            if($table->prevName==''){
                mkdir($this->getRoot() . '/' . $table->url . $table->newName, 0777);

                $this->setMessage( JText::sprintf( 'Directory with "'.$table->newName.'" was created succesful', $n )); 
                $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);   
            }else{
                rename($this->getRoot() . '/' . $table->url . $table->prevName,$this->getRoot() . '/' . $table->url . $table->newName);
             
                $this->setMessage( JText::sprintf( 'Directory with "'.$table->prevName.'" was renamed succesful to "'.$table->newName.'"', $n)); 
                $this->setRedirect("index.php?option=$option&controller=xslts".$table->jump);   
            } 
        }       
    }

    /*! @function getRoot
        @abstract  
        @return string root path
    */
    function getRoot(){
        $params = &JComponentHelper::getParams( 'com_xsltmagic' );
        $root_path = JPATH_ROOT.DS.$params->get( 'root' );
        return $root_path;        
    }

    /*! @function getURL
        @abstract parsing function, returns path to selected folder in basic view
        @return string URL
    */
    function getURL(){
        $link=explode('&jump[]=', $_SERVER['HTTP_REFERER']);
        $url='';

        for ($i=1;$i<count($link) ;$i++ ) {
            $url.=$link[$i].'/';
        }
        return $url;
    }

    /*! @function getJump
        @abstract parsing function from URL, useful for page redirecting after making some command
        @return string jump
    */
    function getJump(){
        $link=explode('&jump[]=', $_SERVER['HTTP_REFERER']);
        $jump='';

        for ($i=1;$i<count($link) ;$i++ ) {
            $jump.='&jump[]='.$link[$i];
        }

        return $jump;
    }

    /*! @function getEditableFiles
        @abstract get list of editable files from config
        @return Editable Files 
    */  
    function getEditableFiles(){
        $params = &JComponentHelper::getParams( 'com_xsltmagic' );
        $edit_files = JPATH_ROOT.DS.$params->get( 'edFiles' );
        return $edit_files;   
    }
}
  