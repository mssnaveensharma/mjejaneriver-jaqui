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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!checkUserAccess(JFactory::getUser()->id,"manage_excursions") && !checkUserAccess(JFactory::getUser()->id,"availability_section")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}




class JHotelReservationViewExcursionRatePrices extends JViewLegacy{

	protected $item;
	protected $state;
	protected $rate;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//$this->form	= $this->get('Form');
		$this->items	= $this->get('Items');
		$this->state = $this->get('State');
		$this->rate = $this->get('Rate');
	
		$this->onlyAvailability = JRequest::getVar("onlyAvailability", false);
		
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
		 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title(JText::_('LNG_EXCURSION',true)." : ". JText::_('LNG_EDIT_CUSTOM_RATES',true), 'menu.png');
		JToolBarHelper::apply('excursionrateprices.apply');
		JToolBarHelper::save('excursionrateprices.save');
		JToolBarHelper::cancel('excursionrateprices.cancel');
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_EXCURSION_EDIT');
	}
}

