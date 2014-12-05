<div class="hotel-facilities hotel-item">
	
	<h2><?php echo JText::_('LNG_HOTEL_FACILITIES')?> <?php echo $this->hotel->hotel_name; ?></h2>
		<ul class="blue">
			<?php 
			foreach($this->hotel->facilities as $facility)	{
			?>
				<li><?php echo $facility->name?></li>			
			<?php } ?>
		</ul>
</div>