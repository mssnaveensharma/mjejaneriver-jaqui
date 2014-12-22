<?php 
/**
* @package     	2J Photo Gallery
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class TwoJToolBoxPhotoGalleryCallBack extends TwoJToolBoxPluginCallBack{
	public $pluginType = 'photogallery';
	
	public $sendData = array();
	public $galleryName = array();
	public $curPath = array();
	
	public function createTreeElement($elemt){
		$retHTML = '';
		$galleryName = $elemt['id'];
		if( isset($this->galleryName[$elemt['id']]) ){
			$this->galleryName[$elemt['id']]['use'] = 1;
			$galleryName = $this->galleryName[$elemt['id']]['title'];
		} else {
			return ;
		}
		$retHTML .= '<li class="dd-item" data-id="'.$elemt['id'].'"><div class="dd-handle">'.$galleryName.'</div>';
		if( isset($elemt['children']) && count($elemt['children']) ) $retHTML .= $this->createTree( $elemt['children'] );
		$retHTML .= '</li>';
		return $retHTML;
	}
	
	public function createTree($elemtArray, $rootCat=0){
		$retHTML = '';
		$retHTML .= '<ol class="dd-list">';

		for($i=0;$i<count($elemtArray); $i++){
			$retHTML .= $this->createTreeElement( $elemtArray[$i] );
		}
		if($rootCat && count($this->galleryName) ){
			while (list($key, $value) = each($this->galleryName)){
				if(!isset($value['use']) || $value['use'] == 0 ){
					$retHTML .= $this->createTreeElement( $value );
				}
			}
		}
		$retHTML .= '</ol>';
		return $retHTML;
	}
	
	public function readData(){
		$retHTML = '';
		$row = JTable::getInstance('Data', 'TwojToolboxTable');
		$row->loadPluginType($this->pluginType);
		$jsonInput = $row->json;
		if(!$jsonInput) $jsonInput = '{}';
		$jsonArray = json_decode($jsonInput, true);
		if( $jsonArray === NULL ) $jsonArray = array();
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select( '`id`, `title`' );
		$query->from('#__twojtoolbox');
		$query->where('	type = '. $db->quote($this->pluginType) );
		$db->setQuery( (string) $query );
		$this->galleryName = $db->loadAssocList('id');
		
		$retHTML .= '<div class="dd" id="twojCategoryContentRunTime">';
		$retHTML .= $this->createTree( $jsonArray, 1 );
		$retHTML .= '</div>';
		echo $retHTML;
	}
	
	public function writeData(){
		$jsonInput = '{}';
		if( isset($this->sendData['jsonSerialize']) ){
			$jsonInput  = $this->sendData['jsonSerialize'];
			if(!$jsonInput) $jsonInput = '{}';
			$jsonObj = json_decode($jsonInput);
			if( $jsonObj === NULL ) $jsonObj = new stdClass();
			
			$row = JTable::getInstance('Data', 'TwojToolboxTable'); 
			$row->loadPluginType($this->pluginType);
			
			$row->json = json_encode($jsonObj);
			$row->check();
			echo $row->store();
		} else echo '0';
	}
	
	public function readPath(){
		$pluginId = JRequest::getVar('pluginId', 0, 'POST', 'int');
		$retHTML = '';
		$row = JTable::getInstance('Data', 'TwojToolboxTable');
		$row->loadPluginType($this->pluginType);
		$jsonInput = $row->json;
		if(!$jsonInput) $jsonInput = '{}';
		$jsonArray = json_decode($jsonInput, true);
		if( $jsonArray === NULL ) $jsonArray = array();
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select( '`id`, `title`' );
		$query->from('#__twojtoolbox');
		$query->where('	type = '. $db->quote($this->pluginType) );
		$db->setQuery( (string) $query );
		$this->galleryName = $db->loadAssocList('id');
		
		$this->createPatch( $jsonArray, $pluginId );
		
		if( !count($this->curPath) && isset( $this->galleryName[$pluginId]) && isset($this->galleryName[$pluginId]['title']) ) 
			$this->curPath[] = $this->galleryName[$pluginId]['title']; 
		
		$this->curPath = array_reverse($this->curPath);
		for($i=0;$i<count($this->curPath);$i++){
			echo '<span class="twojCategoryName">'.$this->curPath[$i].'</span>'.($i!=(count($this->curPath)-1)?' > ':'');
		}
	}
	
	public function createPatch($elementArray, $elementNeed){
		$retHTML = '';
	
		for($i=0;$i<count($elementArray);$i++){
			if(isset($this->galleryName[$elementArray[$i]['id']]['title']) ){
				$retHTML = $this->galleryName[$elementArray[$i]['id']]['title'];
			} else {
				
			}
			if( $elementArray[$i]['id'] == $elementNeed){
				$this->curPath[] = $this->galleryName[$elementArray[$i]['id']]['title'];
				return 1;
			}
			if( isset($elementArray[$i]['children']) && count($elementArray[$i]['children']) ){
				if( $this->createPatch($elementArray[$i]['children'], $elementNeed)){
					$this->curPath[] = $retHTML;
					return 1;
				}
			}
		}
		return 0;
	}
	
	public function outContent(  ){ 
		$this->sendData = JRequest::getVar('sendData', array(), 'POST', 'array');
		$type 			= JRequest::getVar('type', '', 'POST', 'string');
		if( $type && method_exists('TwoJToolBoxPhotoGalleryCallBack', $type) ) $this->$type();
	}
}
?>