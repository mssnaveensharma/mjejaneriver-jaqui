<?php defined('_JEXEC') or die('Restricted access'); ?>

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

function quickiconButton( $link, $image, $text,$description=null )
		{
			$lang =JFactory::getLanguage();
			?>
			
			<li class="option-button">
				<a href="<?php echo $link; ?>" class="box box-inset">
					<?php echo JHTML::_('image','administrator/components/'.getBookingExtName().'/assets/img/'.$image, $text); ?>	
					<h3>
						<?php echo $text; ?>
					</h3>
					<p> <?php //echo $description; ?> &nbsp;</p>
				</a>
			</li>
			<?php
	} 
?>

<?php echo "<div class='user-options-container'><ul>"?>
	<?php if (checkUserAccess(JFactory::getUser()->id,"reservations_reports"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=reports", 'reservationsreports_48_48_icon.gif', JText::_('LNG_RESERVATIONS_CALENDAR_SIMPLE',true),JText::_('LNG_RESERVATIONS_CALENDAR_SIMPLE',true),JText::_('LNG_RESERVATIONS_REPORTS_DESC',true) );?>
	<?php if (checkUserAccess(JFactory::getUser()->id,"reservations_reports") && PROFESSIONAL_VERSION==1)  echo quickiconButton( "index.php?option=".getBookingExtName()."&task=reservationsreports.incomeReport", 'managecurrencies_48_48_icon.gif', JText::_('LNG_RESERVATIONS_INCOME_REPORT',true),JText::_('LNG_RESERVATIONS_INCOME_REPORT',true),JText::_('LNG_RESERVATIONS_REPORTS_DESC',true) );?>
	<?php if (checkUserAccess(JFactory::getUser()->id,"reservations_reports") && PROFESSIONAL_VERSION==1)  echo quickiconButton( "index.php?option=".getBookingExtName()."&task=reservationsreports.countryReservationReport", 'manageroomfeatures_48_48_icon.gif', JText::_('LNG_RESERVATIONS_BY_COUNTRY_REPORT',true),JText::_('LNG_RESERVATIONS_BY_COUNTRY_REPORT',true),JText::_('LNG_RESERVATIONS_REPORTS_DESC',true) );?>
<?php echo "</ul></div>"?>


