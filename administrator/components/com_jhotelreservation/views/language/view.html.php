<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The HTML  View.
 */
class JHotelReservationViewLanguage extends JViewLegacy
{
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){

		$function = $this->getLayout();
		if(method_exists($this,$function)) $tpl = $this->$function();
		$this->setLayout('default');
		parent::display($tpl);
	}
	
	function language(){
		$app =JFactory::getApplication();
		$code = JRequest::getString('code');
		if(empty($code)){
			$app->enqueueMessage(JText::_('Code not specified',true));
			return;
		}
	
		$file = new stdClass();
		$file->name = $code;
		
		$path = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code.DS.$code.getBookingExtName().'.ini';
		$file->path = $path;
	
		jimport('joomla.filesystem.file');
		$showLatest = true;
		$loadLatest = false;
	
		if(JFile::exists($path)){
			$file->content = JFile::read($path);
			if(empty($file->content)){
				$app->enqueueMessage('File not found : '.$path);
			}
		}else{
			$loadLatest = true;
			$file->content = JFile::read(JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.'en-GB'.DS.'en-GB.'.getBookingExtName().'.ini');
		}
		$this->assignRef('file',$file);
		
		$tpl = "language";
		return $tpl;
	}

	protected function addToolbar()
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'LNG_NEW_EXTRA_OPTION' : 'LNG_EDIT_EXTRA_OPTION',true), 'menu.png');
		
		JToolbarHelper::apply('extraoption.apply');
			
		JToolbarHelper::save('extraoption.save');
		
		JToolbarHelper::cancel('extraoption.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_JHotelReservation_COMPANY_TYPE_EDIT');
	}
}
