
			    	
    	<?php
    	/*------------------------------------------------------------------------
    	# JHotelReservation
    	# author CMSJunkie
    	# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
    	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
    	# Websites: http://www.cmsjunkie.com
    	# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
    	-------------------------------------------------------------------------*/
    	defined('_JEXEC') or die('Restricted access');

    	function quickiconButton( $link, $image, $text,$description=null )
    			{
    				$lang = JFactory::getLanguage();
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

	    	<?php if (checkUserAccess(JFactory::getUser()->id,"application_settings"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=applicationsettings", 'settings.png', JText::_('LNG_APPLICATION_SETTINGS',true), JText::_('LNG_APPLICATION_SETTINGS_DESC',true) );?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"manage_hotels")) echo quickiconButton( "index.php?option=".getBookingExtName()."&view=hotels", 'hotels.png', JText::_('LNG_MANAGE_HOTELS',true), JText::_('LNG_MANAGE_HOTELS_DESC',true) );?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"manage_rooms"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=rooms", 'rooms.png', JText::_('LNG_MANAGE_ROOMS',true), JText::_('LNG_MANAGE_ROOMS_DESC',true) );?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"availability_section")) echo quickiconButton( "index.php?option=".getBookingExtName()."&view=availability", 'availability.png', JText::_('LNG_MANAGE_AVAILABILITY',true), JText::_('LNG_MANAGE_AVAILABILITY',true) );?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"currency_settings"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&controller=managecurrencies&view=managecurrencies", 'currencies.png', JText::_('LNG_CURRENCY_SETTINGS',true), JText::_('LNG_CURRENCY_SETTINGS_DESC',true) );?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"manage_taxes"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=managetaxes", 'taxes.png', JText::_('LNG_MANAGE_TAXES',true), JText::_('LNG_MANAGE_TAXES_DESC',true));?>
	    	<?php if (checkUserAccess(JFactory::getUser()->id,"manage_email_templates"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&controller=manageemails&view=manageemails", 'emailtemplated.png', JText::_('LNG_MANAGE_EMAIL_TEMPLATES',true), JText::_('LNG_MANAGE_EMAIL_TEMPLATES_DESC',true) );?>
			    	
			    	
			    	
			    	
			    
					
			<?php if (checkUserAccess(JFactory::getUser()->id,"add_reservations"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=reservation&sourceId=0&layout=edit", 'addreservation.png', JText::_('LNG_ADD_RESERVATIONS') );?>
			<?php if (checkUserAccess(JFactory::getUser()->id,"manage_reservations"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=reservations", 'reservations.png', JText::_('LNG_MANAGE_RESERVATIONS',true), JText::_('LNG_MANAGE_RESERVATIONS_DESC',true));?>
			<?php if (checkUserAccess(JFactory::getUser()->id,"updates_hotelreservation"))  echo quickiconButton( "index.php?option=".getBookingExtName()."&view=updates", 'downloads.png', JText::_('LNG_UPDATE',true), JText::_('LNG_UPDATE_DESC',true) );?>
			<?php echo quickiconButton( "index.php?option=".getBookingExtName()."&about&view=about", 'about.png', JText::_('LNG_ABOUT',true), JText::_('LNG_ABOUT_DESC',true) );?>
	  				
		<?php echo "</ul></div>"?>
	  				
		<div id="chartPanel"> 
			<div id="chartdiv">
				<div style='text-align:center;padding-top:150px;width:100%'><img src='<?php echo JURI::base()."/components/".getBookingExtName();?>/assets/img/loader.gif'>
				</div>
			</div>
			<div id="buttonDiv">
				<button class="chartButton" id="daysLag" value="7" onClick="generateChart(this.value);">7D</button>
				<button class="chartButton" id="daysLag" value="30" onClick="generateChart(this.value);">1M</button>
				<button class="chartButton" id="daysLag" value="90" onClick="generateChart(this.value);">3M</button>
				<button class="chartButton" id="daysLag" value="180" onClick="generateChart(this.value);">6M</button>
				<button class="chartButton" id="daysLag" value="365" onClick="generateChart(this.value);">1Y</button>
				<button class="chartButton" id="daysLag" value="730" onClick="generateChart(this.value);">2Y</button>
				<button class="chartButton" id="daysLag" value="1095" onClick="generateChart(this.value);">3Y</button>
			</div>
		</div>
		<?php return; ?>
	  				
			   	 