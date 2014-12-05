<?php // no direct access
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

defined('_JEXEC') or die('Restricted access');
JHTML::_('script', 							'components/com_jhotelreservation/assets/js/search.js');
$userData =  $_SESSION['userData'];
//$max_package_number = 0;
?>

<div class="reservation-info-container">
	<div class="reservation-info-container-outer">
		<div class="reservation-info-container-inner">
			<div class="choose-room">
				<?php
				//$this->_models['variables']->getReservedItems() < $this->_models['variables']->rooms
						if(  $this->userData->rooms > 1)
						{
							echo (isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_CHOOSE_YOUR_PARK',true) : JText::_('LNG_CHOOSE_YOUR_ROOM',true)) ."&nbsp;(";
							echo count($this->userData->reservedItems) +1 ;
							echo "&nbsp;".JText::_('LNG_OF',true) ." ";
							echo $this->userData->rooms.") ";
							echo JText::_('LNG_ADULTS').":".$this->userData->roomGuests[count($this->userData->reservedItems)];
							if($this->appSettings->show_children){
								echo " ".JText::_('LNG_CHILDREN').":".$this->userData->roomGuestsChildren[count($this->userData->reservedItems)];
							}
						}else{
						    //echo JText::_('LNG_AVAILABLE_ROOMS',true);
						}
				?>
			</div>
			<div>
			
				<?php echo ucfirst(isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_AVAILABLE_PARKS',true) : JText::_('LNG_AVAILABLE_ROOMS',true)." ".JText::_('LNG_FROM',true)) ?>
					
				<?php
					$data_1 = $this->userData->year_start.'-'.$this->userData->month_start.'-'.$this->userData->day_start;
				?>
				<strong>
				<?php 	
					echo JHotelUtil::getDateGeneralFormat($data_1);
				?>
				</strong>
				<?php 
					//echo date( 'l, F d, Y', strtotime( $this->userData->year_start.'-'.$this->userData->month_start.'-'.$this->userData->day_start ) )
					echo JText::_('LNG_TO',true);
				?>
				<strong>
				<?php 
					$data_2 = $this->userData->year_end.'-'.$this->userData->month_end.'-'.$this->userData->day_end;
					echo JHotelUtil::getDateGeneralFormat($data_2);
				?>
				</strong>
				<?php  //echo ", ".JText::_('LNG_FOR',true)." ".(isset($this->userData->roomGuests)?$this->userData->roomGuests[$this->_models['variables']->getReservedItems()]:$this->userData->guest_adult).' '.strtolower(JText::_('LNG_ADULT_S',true)) ?>
						
			</div>
		</div>
	</div>
</div>

		<?php
			if( JRequest::getVar( 'infoCheckAvalability') != '' )	
			{
		?>
			<div class="alert_message" style="color: #FF0000; margin-top: 15px;">
					<?php echo JRequest::getVar('infoCheckAvalability') ?>
			</div>	
			<?php
			}else{
				require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'roomratesf.php'; 
			}			
			?>
