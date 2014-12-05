<fieldset class="adminform">
	<legend><?php echo JText::_( 'LNG_WIZARD_OFFER_PACKAGES' ,true); ?></legend>
	<TABLE class="admintable">
		<TR>
			<TD width=10% nowrap class="key"><?php echo JText::_('LNG_EXTRA_OPTIONS',true); ?> :</TD>
			<TD nowrap align=left id="offers-holder">
				<div>
					<select id='extra_options_ids' name='extra_options_ids[]' multiple="multiple" >
						<option value=""><?php echo JText::_('LNG_SELECT_EXTRA_OPTION'); ?></option>
						<?php
							if(isset($this->extraOptions) && count($this->extraOptions)>0){
								foreach( $this->extraOptions as $extraOption )
								{
								?>
								<option value='<?php echo $extraOption->id?>' <?php echo strpos($extraOption->offer_ids, $this->item->offer_id) !== false? " selected" : ""?>>
									<?php echo $extraOption->name?>
								</option>
								<?php
								}
							}
						?>
					</select>
					<a href="javascript:checkAllExtraOptions()" class="select-all"><?php echo JText::_('LNG_CHECK_ALL',true)?></a>
					<a href="javascript:uncheckAllExtraOptions()" class="deselect-all"><?php echo JText::_('LNG_UNCHECK_ALL',true)?></a>
				</div>
			</TD>
		</TR>
	</TABLE>
</fieldset>

<script>
var offerSelectList = null;
jQuery(document).ready(function(){
	offerSelectList = jQuery("select#extra_options_ids").selectList({ 
		sort: true,
		classPrefix: 'offer_ids',
		instance: true
	});
});

function checkAllExtraOptions(){
	//uncheckAllExtraOptions();
	jQuery(".offer_ids-select option:not(:disabled)").each(function(){ 
		jQuery(this).attr("selected","selected"); 
		if(jQuery(this).val()!=""){
			offerSelectList.add(jQuery(this));
		}
		
	});
}  

function uncheckAllExtraOptions(){
	jQuery("#extra_options_ids option").each(function(){ 
		jQuery(this).removeAttr("selected"); 
	});
	
	offerSelectList.remove();
}  



</script>