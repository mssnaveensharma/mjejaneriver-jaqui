<?php 
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.html.tabs' );
?>


<div id="application-settings">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col100">
		<?php 	
			$options = array(
		    'onActive' => 'function(title, description){
		        description.setStyle("display", "block");
		        title.addClass("open").removeClass("closed");
		    }',
		    'onBackground' => 'function(title, description){
		        description.setStyle("display", "none");
		        title.addClass("closed").removeClass("open");
		    }',
		    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		    'useCookie' => true, // this must not be a string. Don't use quotes.
		);
		
		echo JHtml::_('tabs.start', 'tab_group_id', $options);
		
		echo JHtml::_('tabs.panel', JText::_('LNG_GENERAL_SETTINGS',true), 'panel_1_id');
		require_once 'general.php';
		
		echo JHtml::_('tabs.panel', JText::_('LNG_PAYMENT_SETUP',true), 'panel_2_id');
		require_once 'payment.php';
		
		echo JHtml::_('tabs.panel', JText::_('LNG_FRONTEND_SETTINGS',true), 'panel_3_id');
		require_once 'frontend.php';

		//echo JHtml::_('tabs.panel', JText::_('LNG_HOTEL_LANGUAGES',true), 'panel_4_id');
		//require_once 'languages.php';
				
		echo JHtml::_('tabs.end');
		?>
		
	</div>
	<input type="hidden" name="sendmail_from" value="<?php echo $this->item->sendmail_from?>" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="applicationsettings_id" value="<?php echo $this->item->applicationsettings_id?>" />
	<input type="hidden" name="controller" value="applicationsettings" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
</div>