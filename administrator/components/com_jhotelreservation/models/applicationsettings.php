<?php
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die( 'Restricted access' );


class JHotelReservationModelApplicationSettings extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('applicationsettings_id',  0, '', 'array');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
	}
	function setId($applicationsettings_id)
	{
		// Set id and wipe data
		$this->_applicationsettings_id		= $applicationsettings_id;
		$this->_data						= null;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = ' SELECT * FROM #__hotelreservation_applicationsettings';
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
		}
		$config =JFactory::getConfig();
		$this->_data->sendmail_from = $config->get( 'config.mailfrom' );
		$this->_data->sendmail_name = $config->get( 'config.fromname' );

		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			
			$this->_data->applicationsettings_id 	= null;
			$this->_data->is_enable_reservation		= null;			
			$this->_data->currency_id				= null;			
			$this->_data->special_notes				= null;			
			$this->_data->terms_and_conditions		= null;			
			$this->_data->is_enable_payment			= null;		
			$this->_data->is_enable_https			= null;					
			$this->_data->is_site_online			= null;			
			$this->_data->css_style					= null;
			$this->_data->css_module_style			= null;
			$this->_data->company_name				= null;
			$this->_data->company_email				= null;
			$this->_data->policy					= null;
			$this->_data->currencies				= array();
			$this->_data->is_enable_extra_options	= null;

			$config =JFactory::getConfig();
			$this->_data->sendmail_from = $config->get( 'config.mailfrom' );
			$this->_data->sendmail_name = $config->get( 'config.fromname' );
			
			$this->data->is_enable_offers			= null;
			$this->data->is_email_notify_canceled_pending			= null;
			$this->data->invoice_email = null;
			$this->data->send_invoice_to_email  = null;
			$this->data->enable_hotel_tabs  = null;
			$this->data->enable_hotel_description  = null;
			$this->data->enable_hotel_facilitites  = null;
			$this->data->enable_hotel_information  = null;
		}
		$config =JFactory::getConfig();
		$this->_data->sendmail_from = $config->get( 'config.mailfrom' );
		$this->_data->sendmail_name = $config->get( 'config.fromname' );

		
		if( $this->_data) 
		{
			$this->_data->card_types				= array();
			$this->_data->currencies				= array();
		
			$query = ' SELECT currency_id, description FROM #__hotelreservation_currencies';
			//$this->_db->setQuery( $query );
			$this->_data->currencies = $this->_getList( $query );
			
			$this->_data->dateFormats = array();
			$query = ' SELECT * FROM #__hotelreservation_date_formats';
			//$this->_db->setQuery( $query );
			$this->_data->dateFormats = $this->_getList( $query );
				
			
			//var_dump($this->_data);
		}
		//JPATH_COMPONENT_SITE. DS.'assets'.DS
		$this->_data->css_styles 			= glob(JPATH_COMPONENT_SITE. DS.'assets'.DS.'css'.DS.'*.css');
		$this->_data->css_module_styles 	= glob(JPATH_ROOT.DS.'modules'.DS.'mod_jhotelreservation'. DS.'assets'.DS.'*.css');
		
		$this->_data->languages 			= glob(JPATH_COMPONENT_ADMINISTRATOR. DS.'language'.DS.'*', GLOB_ONLYDIR);
		
		return $this->_data;
	}
	
	
	function store( $data )
	{	
		$row = $this->getTable();

		if( count($data['card_type_ids'] > 0 ) )
			$data['card_type_ids'] = implode(',', $data['card_type_ids'] );
		else
			$data['card_type_ids'] =  '';
		
		// Bind the form fields to the table
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	function getLanguages(){
		
		$path = JLanguage::getLanguagePath(JPATH_ROOT);
		$dirs = JFolder::folders( $path );

		foreach ($dirs as $dir)
		{
			if(strlen($dir) != 5) continue;
			$xmlFiles = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
			$xmlFile = reset($xmlFiles);
			if(empty($xmlFile)) continue;
			$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$xmlFile);
			$oneLanguage = new stdClass();
			$oneLanguage->language 	=	 $dir;
			$oneLanguage->name = $data['name'];
			$languageFiles = JFolder::files( JPATH_COMPONENT.DS.'language'.DS. $dir, '^(.*)\.'.getBookingExtName().'\.ini$'  );
			$imageName = JFolder::files( JPATH_COMPONENT.DS.'language'.DS. $dir, '^(.*)\.'.getBookingExtName().'\.png$'  );
			$languageFile = reset($languageFiles);
			if(!empty($languageFile)){
				$linkEdit = 'index.php?option='.getBookingExtName().'&amp;task=language.editLanguage&amp;lngCode='.$oneLanguage->language;
				$oneLanguage->edit = ' <a class="modal" title="'.JText::_('LNG_EDIT_LANGUAGE_FILE',true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><img id="image'.$oneLanguage->language.'" class="icon16" src="'.PATH_ASSETS_IMG.'edit.png" alt="'.JText::_('LNG_EDIT_LANGUAGE_FILE',true).'"/></a>';
			}else{
				$linkEdit = 'index.php?option='.getBookingExtName().'&amp;task=language.editLanguage&amp;lngCode='.$oneLanguage->language;
				$oneLanguage->edit = ' <a class="modal" title="'.JText::_('LNG_ADD_LANGUAGE_FILE',true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><img id="image'.$oneLanguage->language.'" class="icon16" src="'.PATH_ASSETS_IMG.'edit.png" alt="'.JText::_('LNG_EDIT_LANGUAGE_FILE',true).'"/></a>';
			}
			$languages[] = $oneLanguage;
		}
		return $languages;
	}
}
?>