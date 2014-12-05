<?php
$percent 		= 0;
$explication	= '';
$payment_id		= 0;

foreach( $this->itemsPayments as $val )
{
	if( $this->itemModelVariables->hotel_id != $val->hotel_id )
		continue;
	if( $val->payment_id == PENALTY_PAYMENT_ID )
	{
		$payment_id		= $val->payment_id;
		$percent		= $val->payment_percent;
		$explication 	= $val->payment_name;
		break;
	}
}
// foreach( $this->itemModelVariables->payments as $val )
foreach( $this->itemModelVariables->itemPayments as $val )
{

	if( $val->payment_id == PENALTY_PAYMENT_ID )
	{
		$payment_id		= $val->payment_id;
		$percent		= $val->payment_percent;
		$explication 	= $val->payment_explication;
		break;
	}
}


?>
<form autocomplete='off' action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		
		<legend><?php echo JText::_('LNG_APPLY_PENALTIES',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0 width=100%>
			<TR>
				<TD width=10% nowrap><?php echo JText::_('LNG_PENALTY',true)?> :</TD>
				<TD nowrap width=90% align=left>
					<input 
						type		= "input"
						name		= "payment_percent"
						id			= "payment_percent"
						value		= '<?php echo $percent?>'
						size		= 10
						maxlength	= 10
						style		= 'text-align:right'
						
					/> %
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap><?php echo JText::_('LNG_EXPLICATION',true)?> :</TD>
				<TD nowrap width=90% align=left>
					<input 
						type		= "input"
						name		= "payment_explication"
						id			= "payment_explication"
						value		= '<?php echo $explication?>'
						size		= 80
						maxlength	= 255
						style		= 'text-align:left'
						
					/>
				</TD>
			</TR>
			<?php
			if( $this->itemModelVariables->is_enable_payment == true )
			{
			?>
			<TR>
				<TD colspan=2>
					<?php echo $this->itemModelVariables->displayPaymentProcessors(true, array(PROCESSOR_PAYFLOW,PROCESSOR_AUTHORIZE, PROCESSOR_PAYPAL_EXPRESS, PROCESSOR_MPESA, PROCESSOR_BANK_ORDER)); ?>
				</TD>
			</TR>

			<?php
			}
			else
			{
			?>
			<input type ='hidden' name= 'tip_payment_penalties' id= 'tip_payment_penalties' value='cash'>
			<?php
			}
			?>
		</table>
		</center>
	</fieldset>
	<input type="hidden" name="option"				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" 				value="" />
	<input type="hidden" name="payment_id" 			value="<?php echo $payment_id?>" />
	<input type="hidden" name="is_enable_payment" 	value="<?php echo $this->itemModelVariables->is_enable_payment?>" />
	<input type="hidden" name="is_penalty" 			value="1" />
	<input type="hidden" name="view" 				value="" />
	<input type="hidden" name="hotel_id" 			value="<?php echo $this->itemModelVariables->hotel_id ?>" />
	<input type="hidden" name="email" 				value="<?php echo $this->itemModelVariables->email ?>" />
	<input type="hidden" name="total" 				value="<?php echo $this->itemModelVariables->total ?>" />
	<input type="hidden" name="total_payed" 		value="<?php echo $this->itemModelVariables->total_payed ?>" />
	<input type="hidden" name="confirmation_id" 	value="<?php echo $this->itemModelVariables->confirmation_id ?>" />
	<input type="hidden" name="controller" 			value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
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
			if (pressbutton == 'save') 
			{
				if( !validateField( form.elements['payment_percent'], 'numeric', true, "<?php echo JText::_('LNG_PLEASE_INSERT_PENALTY_PERCENT',true)?>" ) )
					return false;
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>
</form>
