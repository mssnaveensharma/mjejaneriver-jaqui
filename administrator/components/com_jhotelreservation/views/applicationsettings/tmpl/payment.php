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

defined( '_JEXEC' ) or die( 'Restricted access' );
$editor =JFactory::getEditor();
?>

		</fieldset>
				<fieldset class='adminform'>
			<legend><?php echo JText::_( 'LNG_PRICE_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<TR>
					<TD width=10% nowrap  class="key" ><?php echo JText::_('LNG_SHOW_PRICE_PER_PERSON',true); ?>:</TD>
					<TD nowrap>
						<?php echo $this->elements->show_price_per_person; ?>
					</TD>
				</TR>
				<TR>
					<TD width=10% nowrap  class="key" ><?php echo JText::_('LNG_CHARGE_ONLY_RESERVATION_COST',true); ?>:</TD>
					<TD nowrap>
						<?php echo $this->elements->charge_only_reservation_cost; ?>
					</TD>
				</TR>
			</TABLE>
		</fieldset>
		
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'LNG_PAYMENT_METHODS_PAYFLOW_PRO_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<TR>
					<TD width=10% nowrap  class="key" ><?php echo JText::_('LNG_ENABLE_PAYMENT',true); ?>:</TD>
					<TD nowrap>
						<input 
							type		= "radio"
							name		= "is_enable_payment"
							id			= "is_enable_payment"
							value		= '1'
							<?php echo $this->item->is_enable_payment==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_payment"
							id			= "is_enable_payment"
							value		= '0'
							<?php echo $this->item->is_enable_payment==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
					<TD nowrap>
						<?php echo JText::_('LNG_BY_SELECT_YES_YOU_CLIENTS_ARE_REQUIRED_TO_HAVE_A_CREDIT_CARD_IN_ORDER_TO_MAKE_RESERVATIONS',true)?>
					</TD>
				</TR>
				<!-- 
				<TR>
					<TD width=10% nowrap  class="key" ><?php echo JText::_('LNG_ENABLE_HTTPS',true); ?></TD>
					<TD nowrap colspan=2>
						<input 
							type		= "radio"
							name		= "is_enable_https"
							id			= "is_enable_https"
							value		= '1'
							<?php echo $this->item->is_enable_https==true? " checked " :""?>
							accesskey	= "Y"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

							
						/>
					<?php echo JText::_('LNG_YES',true); ?>
						&nbsp;
						<input 
							type		= "radio"
							name		= "is_enable_https"
							id			= "is_enable_https"
							value		= '0'
							<?php echo $this->item->is_enable_https==false? " checked " :""?>
							accesskey	= "N"
							onmouseover	="this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	="this.style.cursor='default'"

						/>
						<?php echo JText::_('LNG_NO',true); ?>
					</TD>
				</TR> -->
				
				
			</TABLE>
		</fieldset>	
			<fieldset class='adminform'>
			<legend><?php echo JText::_( 'LNG_INVOICE_SETUP' ,true); ?></legend>
			<TABLE class='admintable'  width=100%>
				<tr>
					<td width=10% nowrap  class="key" ><?php echo JText::_('LNG_SEND_INVOICE_ONLY_TO_EMAIL',true); ?>:</td>
					<td nowrap>
						<?php echo $this->elements->send_invoice_to_email; ?>
							<input 
							type		= "input"
							name		= "invoice_email"
							id			= "invoice_email"
							value		= "<?php echo $this->item->invoice_email; ?>"
						/>
					</td>
				</tr>
			</TABLE>
		</fieldset>
	
		
	
