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
if (!checkUserAccess(JFactory::getUser()->id,"manage_offers")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('script','administrator/components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
JHTML::_('script','administrator/components/'.getBookingExtName().'/assets/js/jquery.upload.js');
JHTML::_('script','administrator/components/'.getBookingExtName().'/assets/js/manageextraoptions.js');

jimport('joomla.html.pane');

class JHotelReservationViewManageOffers extends JViewLegacy
{
	function display($tpl = null){
		
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		if (  JRequest::getString( 'task') =='save' && JRequest::getString( 'is_save_ok') == 0 )
		{
			if( JRequest::getString('offer_id')==0 )
			JRequest::setVar( 'task', 'add');
			else
			JRequest::setVar( 'task', 'edit');
		}

		if(
		JRequest::getString( 'task') !='edit'
		&&
		JRequest::getString( 'task') !='add'
		)
		{
			JToolBarHelper::title(   'J-Hotel Reservation : '.JText::_( 'LNG_MANAGE_OFFERS' ,true), 'generic.png' );
			// JRequest::setVar( 'hidemainmenu', 1 );
			$hotel_id =  $this->get('HotelId');

			JHotelReservationHelper::addSubmenu('offers');
			
			if( $hotel_id > 0 )
			{
				JToolBarHelper::addNew('manageoffers.edit');
				JToolBarHelper::editList('manageoffers.edit');
				JToolBarHelper::deleteList( '', 'manageoffers.delete', JText::_('LNG_DELETE',true));
			}
			JToolBarHelper::custom( 'back', JHotelUtil::getDashBoardIcon(), 'home', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
				
			$this->hotel_id =  $hotel_id;
				
			$items		= $this->get('Datas');
			// dmp($items);
			$this->items =  $items;
				
			$hotels		= $this->get('Hotels');
			$hotels = checkHotels(JFactory::getUser()->id,$hotels);
			$this->hotels =  $hotels;
		}
		else
		{
			$item = $this->get('Data');
			$this->item =  $item;
			$this->extraOptions =  $this->get('ExtraOptions');
				
			$hotel_id =  $this->get('HotelId');
			$this->hotel_id =  $hotel_id;

			if( JRequest::getString( 'is_error_save') == '1' )
			{
				//exit;
			}
			JToolBarHelper::title(    'J-Hotel Reservation : '.JText::_($item->offer_id > 0? "LNG_EDIT" : "LNG_ADD_NEW",true).' '.JText::_('LNG_OFFER' ,true), 'generic.png' );
			JRequest::setVar( 'hidemainmenu', 1 );
			
			if($this->item->state==0 || isSuperUser(JFactory::getUser()->id)){
				JToolBarHelper::apply('manageoffers.apply');
				JToolBarHelper::save('manageoffers.save'); 
				JToolBarHelper::custom('manageoffers.saveAsNew', 'save.png', 'save.png', 'Duplicate',false, false );
			}
			JToolBarHelper::cancel('manageoffers.cancel');
			
			$this->includeFunctions();
		
		}
		parent::display($tpl);
	}

	function includeFunctions(){
		$doc =JFactory::getDocument();
		$doc->addStyleSheet('components/'.getBookingExtName().'/assets/js/validation/css/validationEngine.jquery.css' );
		$tag = JHotelUtil::getJoomlaLanguage();
		$doc->addScript('components/'.getBookingExtName().'/assets/js/validation/js/languages/jquery.validationEngine-'.$tag.'.js');
		$doc->addScript('components/'.getBookingExtName().'/assets/js/validation/js/jquery.validationEngine.js');
		$doc->addScript('components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
	
	}
	
	function displayThemes($themes, $selectedThemes){
		ob_start();
		?>

			<select id="themes" multiple="multiple" name="themes[]">
				<option value="">
			
				<?php echo JText::_('LNG_SELECT_THEME',true)?></option>
				
				<?php
				if( isset($themes) && is_array($themes))
				foreach( $themes as $theme )
				{
					$selected = false;
					foreach( $selectedThemes as $selectedTheme ){
						if($theme->id == $selectedTheme->themeId)
						$selected =true;
					}
					?>
					<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $theme->id?>'><?php echo $theme->name ?></option>
					<?php
					}
					?>
			</select>

			<?php
			$buff = ob_get_contents();
			ob_end_clean();
			return $buff;
	}
}