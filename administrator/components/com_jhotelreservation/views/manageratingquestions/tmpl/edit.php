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

defined('_JEXEC') or die('Restricted access');
$questionId = JRequest::getVar('review_question_id');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="review_question_id" value="<?php echo $questionId[0];?>">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_RATING_QUESTION',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_RATING_QUESTION',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "review_question_desc"
						id			= "review_question_desc"
						value		= '<?php if(isset($this->item->review_question_desc)) echo $this->item->review_question_desc;?>'
						size		= 32
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
		</TABLE>
	</fieldset>
	<script language="javascript" type="text/javascript">
		<?php
		if( JHotelUtil::getCurrentJoomlaVersion() < 1.6 )
		{
		?>
		function submitbutton(pressbutton) 
		<?php
		}
		else
		{
		?>
		Joomla.submitbutton = function(pressbutton) 
		<?php
		}
		?>
		{
			var form = document.adminForm;
			if (pressbutton == 'saveratingquestion') 
			{
				if( !validateField( form.review_question_desc, 'string', false, '<?php echo JText::_('LNG_PLEASE_INSERT_RATING_QUESTION',true)?>' ) )
					return false;
				submitform( pressbutton );
				return;
				
			} else {
				submitform( pressbutton );
			}
		}
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="managehotelratings.saveratingquestion" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

