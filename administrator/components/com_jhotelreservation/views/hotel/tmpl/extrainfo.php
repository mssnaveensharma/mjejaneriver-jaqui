<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="page-characteristics">
	<br style="font-size: 1px;" />
	<fieldset class="adminform">
		<legend>
			
		<?php echo JText::_( 'HOTEL_COMMISION' ,true); ?></legend>
		<table class="admintable" cellspacing="1">
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_HOTEL_NUMBER',true); ?>:</TD>
				<TD nowrap align=left>
					<input type="text" name="hotel_number"
					id="hotel_number" 
					value='<?php echo isset($this->item->hotel_number) ? $this->item->hotel_number:''?>' 
					size=20
					maxlength=255 
					 /> 
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_COMMISSION',true); ?>:</TD>
				<TD nowrap align=left>
					<input type="text" 
					name="commission"
					id="commission" 
					value='<?php echo $this->item->commission?>' 
					size=10
					maxlength=255 
					class="validate[required,custom[integer]] text-input"
					 /> (%)
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_RESERVATION_COSTS',true); ?>:</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "reservation_cost_val"
						id			= "reservation_cost_val"
						value		= '<?php echo $this->item->reservation_cost_val!=0? $this->item->reservation_cost_val :''?>'
						size		= 10
						maxlength	= 128
						class="validate[required,custom[number]] text-input"
					/>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_RESERVATION_CHARGE_PERCENT',true); ?>:</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "reservation_cost_proc"
						id			= "reservation_cost_proc"
						value		= '<?php echo $this->item->reservation_cost_proc!=0? $this->item->reservation_cost_proc : ''?>'
						size		= 10
						maxlength	= 128
						class="validate[required,custom[number]] text-input"
						
					/> (%)
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_RECOMMENDED',true); ?>:</TD>
				<TD nowrap align=left>
					<?php echo $this->elements->recommended; ?>
				</TD>
			</TR>
		</table>
	</fieldset>
</div>

