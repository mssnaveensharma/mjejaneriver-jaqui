<?php
?>

<form autocomplete='off' action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_RESERVATION_DETAILS',true); ?></legend>
		<?php
			echo $this->item->email_confirmation;
		?>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="confirmation_id" value="<?php echo JRequest::getString( 'task' ) =='info'? $this->item->confirmation_id : $this->itemModelVariables->confirmation_id ?>" />
	<input type="hidden" name="controller" value="managereservations>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>