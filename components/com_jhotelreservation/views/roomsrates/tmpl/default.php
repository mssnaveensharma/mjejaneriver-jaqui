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
//$max_package_number = 0;
?>


<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationinfo.php'; ?> 
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'reservationsteps.php'; ?> 

<div class="booking-info">
<?php
		//if( $this->_models['variables']->getReservedItems() < $this->_models['variables']->rooms )
		{
			echo JText::_('LNG_BOOKED_ROOM',true) ."&nbsp;&nbsp;";
			echo "<strong>";
			echo $this->_models['variables']->getReservedItems();
			echo "&nbsp;".JText::_('LNG_OUT_OF',true) ."&nbsp;";
			echo $this->_models['variables']->rooms;
			echo "&nbsp;&nbsp;".JText::_('LNG_ROOMS',true);
			echo "</strong>";
		}
?>
</div>
<?php  require_once JPATH_COMPONENT_SITE.DS.'include'.DS.'roomratesf.php'; ?> 
<div CLASS='DIV_BUTTONS'>
	<table width='100%' border= 0 align=center>
		<tr>
			<td align=left>
				<?php
				if( $this->_models['variables']->tip_oper >2 )
				{
				?>
					<span class="button button-green">
						<button value="checkRates" name="checkRates" type="button" onclick="formBack()">
							<?php echo JText::_('LNG_BACK',true)?>
						</button>
					</span>
				<?php
				}
				?>
			</td>
			<td align=right>
				<div 
					<?php echo $this->_models['variables']->getReservedItems() !=$this->_models['variables']->rooms? " style='display:none' " :""; ?>
				>
					
					<span class="button button-green">
										<button 
											type		= 'button'
											onclick 	= "return checkContinue(false);"							
										><?php echo JText::_('LNG_CONTINUE',true)?></button>	
					</span>
				</div>
			</td>
		</tr>
	</table>
</div>