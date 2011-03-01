<?php
/**
 * @version		$Id: jucene.php 
 * @package		Joomla
 * @subpackage	Jucene
 * @copyright	Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class JuceneModelJucene extends JModel
{
	var $_data = null;
	var $_indexInfo = null;
	
	
	function getIndexInfo(){
		require_once JPATH_SITE .DS. 'administrator' . DS . 'components' . DS . 'com_jucene' . DS . 'helpers'.DS.'jucene.php';
		
		$index = JuceneHelper::getIndex();
		$info['infodocs'] = $index->count();
		$dir_info = $this->getDirectorySize(JuceneHelper::getIndexPath());
		$info['folderSize'] = $dir_info['size'];
		
		return $info;
	}

	function getData(){
		
		if(empty($this->_data)){
			
			$arr = array();
			$arr['max_execution_time'] = ini_get('max_execution_time');
			$arr['memory_limit'] = ini_get('memory_limit');
			$arr['PCRE'] = @! preg_match ( '/\pL/u', 'a' );
			
			
			$this->_data = $arr;
			
		}
		return $this->_data;
	}

/**
	 *
	 * @param $path
	 */
	function getDirectorySize($path) {
		$totalsize = 0;
		$totalcount = 0;
		$dircount = 0;
		if ($handle = opendir ( $path )) {
			while ( false !== ($file = readdir ( $handle )) ) {
				$nextpath = $path . '/' . $file;
				if ($file != '.' && $file != '..' && ! is_link ( $nextpath )) {
					if (is_dir ( $nextpath )) {
						$dircount ++;
						$result = $this->getDirectorySize ( $nextpath );
						$totalsize += $result ['size'];
						$totalcount += $result ['count'];
						$dircount += $result ['dircount'];
					} elseif (is_file ( $nextpath )) {
						$totalsize += filesize ( $nextpath );
						$totalcount ++;
					}
				}
			}
		}
		closedir ( $handle );
		$total ['size'] = $this->sizeFormat ( $totalsize );
		$total ['count'] = $totalcount;
		$total ['dircount'] = $dircount;
		return $total;
	}
	
	function sizeFormat($size) {
		/*if($size<1024)
		 {
			return $size." bytes";
			}
			else if($size<(1024*1024))
			{*/
		$size = round ( $size / 1024, 1 );
		return $size . " KB";
		/*}
		 else if($size<(1024*1024*1024))
		 {
			$size=round($size/(1024*1024),1);
			return $size." MB";
			}
			else
			{
			$size=round($size/(1024*1024*1024),1);
			return $size." GB";
			}*/
	
	}


}