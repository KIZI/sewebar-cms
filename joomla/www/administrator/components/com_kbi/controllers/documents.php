<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

/**
 * Controller for sources administration.
 *
 * @package		com_kbi
 */
class KbiControllerDocuments extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct($config);
		// Register Extra tasks
		$this->registerTask('apply', 'upload');
	}

	function display()
	{
		global $option;
		$document =& JFactory::getDocument();
		$view =& $this->getView('documents', $document->getType());
		$user =& JFactory::getUser();

		$documents = array();

		try
		{
			if ($model = &$this->getModel('sources'))
			{
				$id = JRequest::getVar('id', NULL, 'method', 'array');

				// remote documents
				if($id !== NULL)
				{
					$sourceConfig = $model->getSource($id[0]);

					$source = KBIntegrator::create(get_object_vars($sourceConfig));
					$documents = $source->getDocuments();
				}
				else // local documents
				{
					$_SESSION['ginclude']['article']='-1';
					$_SESSION['ginclude']['part']='-1';

					require_once (JPATH_COMPONENT.DS.'models'.DS.'documents.php');

					$view = $this->getView('documentsLocal', $document->getType());
					$view->setModel(new DocumentsModel(), true);
				}
			}
		}
		catch(Exception $ex)
		{
			$this->setRedirect("index.php?option={$option}");
			$this->setMessage( JText::_( 'ERROR LISTING DOCUMENTS' ) . ": " . $ex->getMessage());
		}

		$view->assignRef('source', $sourceConfig);
		$view->assignRef('rows', $documents);

		$view->setLayout('default');
		$view->display();
	}

	function cancel()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$id = JRequest::getVar('id', array(0), 'method', 'array');

		$this->setRedirect("index.php?option={$option}&controller=documents&id[]={$id[0]}");
	}

	function synchronize()
	{
		$document =& JFactory::getDocument();
		$view =& $this->getView('synchronize', $document->getType());
		$user =& JFactory::getUser();

		if ($model = &$this->getModel('sources'))
		{
			$id = JRequest::getVar('id', array(0), 'method', 'array');
			$sourceConfig = $model->getSource($id[0]);

			$source = KBIntegrator::create(get_object_vars($sourceConfig));

			$view->assignRef('source', $sourceConfig);
		}

		require_once (JPATH_COMPONENT.DS.'models'.DS.'documents.php');

		$view->setModel(new DocumentsModel(), true);
		$view->display();
	}

	function uploadlocal()
	{
		global $option;

		$application = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		if ($model = &$this->getModel('sources')) {
			$id = JRequest::getVar('id', array(0), 'method', 'array');
			$cid = JRequest::getVar('cid', array(0), 'method', 'array');
			$success = 0;

			require_once (JPATH_COMPONENT.DS.'models'.DS.'documents.php');
			$documents_model = new DocumentsModel();
			$sourceConfig = $model->getSource($id[0]);
			$source = KBIntegrator::create(get_object_vars($sourceConfig));

			foreach ($cid as $document_id) {
				try	{
					$document = $documents_model->getArticle($document_id, 'all', true);
					if($document) {
						KBIDebug::log($document);
						$source->addDocument($document->id, $document, FALSE);
						$success ++;

						$application->enqueueMessage(JText::_( 'Document uploaded' ) . "({$document->title})");
					}
				} catch(Exception $ex) {
					$application->enqueueMessage(JText::_('ERROR ADDING FILE') . " - " . $ex->getMessage(), 'error');
				}
			}

			$application->enqueueMessage(JText::_( 'Documents uploaded' ) . "($success)");
		} else {
			$application->enqueueMessage(JText::_('SOURCE NOT FOUND'), 'error');
		}

		$this->setRedirect("index.php?option={$option}&controller=documents&id[]={$id[0]}");
	}

	function add()
	{
		$document =& JFactory::getDocument();
		$view =& $this->getView('documents', $document->getType());
		$user =& JFactory::getUser();

		try
		{
			if ($model = &$this->getModel('sources'))
			{
				$id = JRequest::getVar('id', array(0), 'method', 'array');
				$sourceConfig = $model->getSource($id[0]);

				$source = KBIntegrator::create(get_object_vars($sourceConfig));
				$documents = $source->getDocuments();

				$view->assignRef('source', $sourceConfig);
				$view->assignRef('rows', $documents);
			}
		}
		catch(Exception $ex)
		{
			$this->setError( JText::_( 'ERROR ADDING FILE' ) . "<br />" . $ex->getMessage());
		}

		$view->setLayout('upload');
		$view->display();
	}

	/**
	 * Uploads XML/PMML document to temporary location and uploads to ISynchronable
	 *
	 * @see http://docs.joomla.org/Creating_a_file_uploader_in_your_component
	 */
	function upload()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		global $option;
		$document =& JFactory::getDocument();
		$view =& $this->getView('documents', $document->getType());
		$user =& JFactory::getUser();
		$id = JRequest::getVar('id', array(0), 'method', 'array');
		$fieldName = 'document';

		$this->setRedirect("index.php?option={$option}&controller=documents&id[]={$id[0]}");

		if(empty($_FILES) || !isset($_FILES[$fieldName]))
		{
			$this->setError( JText::_( 'ERROR NO FILE' ) ); return;
		}

		//any errors the server registered on uploading
		$fileError = $_FILES[$fieldName]['error'];
		if ($fileError > 0)
		{
			switch ($fileError)
			{
				case 1: $this->setError( JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' ) ); return;
				case 2: $this->setError( JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' ) ); return;
				case 3: $this->setError( JText::_( 'ERROR PARTIAL UPLOAD' ) ); return;
				case 4: $this->setError( JText::_( 'ERROR NO FILE' ) ); return;
			}
		}

		//check the file extension is ok
		$file_name = $_FILES[$fieldName]['name'];
		$file_name_info = pathinfo($file_name);

		if ($file_name_info['extension'] != 'xml')
		{
			$this->setError( JText::_( 'INVALID EXTENSION' ) );
			return;
		}

		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $_FILES[$fieldName]['tmp_name'];

		//lose any special characters in the filename
		$file_name = ereg_replace("[^A-Za-z0-9.]", "-", $file_name);

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$uploadPath = JPATH_SITE.DS.'images'.DS.'pmml'.DS.$file_name;

		if(!JFile::upload($fileTemp, $uploadPath))
		{
			$this->setError( JText::_( 'ERROR MOVING FILE' ) );
			return;
		}
		else
		{
			try
			{
				if ($model = &$this->getModel('sources'))
				{
					$sourceConfig = $model->getSource($id[0]);

					$source = KBIntegrator::create(get_object_vars($sourceConfig));
					$source->addDocument($file_name, $uploadPath);
					//unlink($uploadPath);
				}

				$this->setMessage( JText::_( 'Document uploaded' ) );
			}
			catch(Exception $ex)
			{
				$this->setError( JText::_( 'ERROR ADDING FILE' ) . "<br />" . $ex->getMessage());
			}
		}

	}

	function view()
	{
		$document =& JFactory::getDocument();
		$view =& $this->getView('documents', $document->getType());
		$user =& JFactory::getUser();

		try
		{
			if ($model = &$this->getModel('sources'))
			{
				$id = JRequest::getVar('id', array(0), 'method', 'array');
				$cid = JRequest::getVar('cid', array(0), 'method', 'array');
				$sourceConfig = $model->getSource($id[0]);

				$source = KBIntegrator::create(get_object_vars($sourceConfig));
				$doc = $source->getDocument($cid[0]);

				$view->assignRef('source', $sourceConfig);
				$view->assignRef('document', $doc);
			}
		}
		catch(Exception $ex)
		{
			$this->setError( JText::_( 'ERROR ADDING FILE' ) . "<br />" . $ex->getMessage());
		}

		$view->setLayout('view');
		$view->display();
	}

	function delete()
	{
		global $option;
		$document =& JFactory::getDocument();
		$view =& $this->getView('documents', $document->getType());
		$user =& JFactory::getUser();

		try
		{
			if ($model = &$this->getModel('sources'))
			{
				$id = JRequest::getVar('id', array(0), 'method', 'array');
				$cids = JRequest::getVar('cid', array(0), 'method', 'array');
				$sourceConfig = $model->getSource($id[0]);
				$messages = array();

				$source = KBIntegrator::create(get_object_vars($sourceConfig));

				foreach($cids as $cid)
				{
					$source->deleteDocument($cid);
					$messages[] = JText::_("Document {$cid} deleted.");
				}

				$this->setMessage(implode(', ', $messages));
				$this->setRedirect("index.php?option={$option}&controller=documents&id[]={$id[0]}");
			}
		}
		catch(Exception $ex)
		{
			$this->setRedirect("index.php?option={$option}");
			$this->setMessage( JText::_( 'ERROR LISTING DOCUMENTS' ) . ": " . $ex->getMessage());
		}
	}
}