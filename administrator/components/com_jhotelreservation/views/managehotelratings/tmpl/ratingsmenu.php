<?php
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

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
<?php echo "<div class='user-options-container'><ul>";
echo quickiconButton( "index.php?option=".getBookingExtName()."&controller=managehotelratings&view=managehotelratings&task=managehotelratings", 'manage_reviews_48_48_icon.gif', JText::_('LNG_MANAGE_HOTEL_REVIEWS',true) );
echo quickiconButton( "index.php?option=".getBookingExtName()."&controller=managehotelratings&view=manageratingquestions&task=manageratingquestions", 'manage_review_questions_48_48_icon.gif', JText::_('LNG_MANAGE_RATING_QUESTIONS',true) );
?>
<?php echo "</ul></div>"?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			//alert(pressbutton);
			if (pressbutton == 'back') 
			{
				<?php
				if(JRequest::getVar('task') =='')
				{
				?>
				form.option.value = '';
				<?php
				}
				?>
				form.submit();
				//submitform( pressbutton );
				return;

			} else {
				submitform( pressbutton );
			}
		}
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="managehotelratings" />
</form>	
<?php return; ?>

