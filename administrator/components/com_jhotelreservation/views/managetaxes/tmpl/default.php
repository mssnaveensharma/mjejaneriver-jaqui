<?php defined('_JEXEC') or die('Restricted access'); ?>
 
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

if( JRequest::getString( 'task') !='edit' && JRequest::getString( 'task') !='add' )
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_MANAGE_TAXES',true); ?></legend>
			<center>
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "managetaxes";
									form.appendChild(obView);
									// form.view.value="managerooms";
									form.submit();
									'
					>
						<option value=0 <?php echo $this->hotel_id ==0? 'selected' : ''?>><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php
						foreach($this->hotels as $hotel )
						{
						?>
						<option value='<?php echo $hotel->hotel_id?>' 
							<?php echo $this->hotel_id ==$hotel->hotel_id||(count($this->hotels)==1)? 'selected' : ''?>
						>
							<?php 
								echo stripslashes($hotel->hotel_name);
								echo (strlen($hotel->country_name)>0? ", ".$hotel->country_name : "");
								echo stripslashes(strlen($hotel->hotel_city)>0? ", ".$hotel->hotel_city : "");
							?>
						</option>
						<?php
						}
						?>
					</select>
					<hr>
				</div>
				<?php
				if( $this->hotel_id > 0  )
				{
				?>
				<TABLE class="table table-striped adminlist">
					<thead>
						<th width='1%'>#</th>
						<th width='1%'  align=center>&nbsp;</th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_TYPE',true); ?></B></th>
						<th width='30%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_VALUE',true); ?></B></th>
						<th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$tax = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $tax->tax_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.tax_id.value = '<?php echo $tax->tax_id?>'
										" 
							/>
						</TD>
						<TD align=left>
							
							<a href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&view=managetaxes&task=managetaxes.edit&tax_id[]='. $tax->tax_id.'&hotel_id='.$this->hotel_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $tax->tax_name?></B>
							</a>	
						</TD>
						<TD align=left>
							<a href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&view=managetaxes&task=managetaxes.edit&tax_id[]='. $tax->tax_id.'&hotel_id='.$this->hotel_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $tax->tax_type=='Fixed'? JText::_('LNG_AMOUNT',true) : JText::_('LNG_PERCENT',true);?></B>
							</a>	
							
						</TD>
						<TD align=left><?php echo $tax->tax_description?></TD>
						<TD align=center><?php echo $tax->tax_value.($tax->tax_type=='Percent'? " %" : "")?></TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/com_jhotelreservation/assets/img/".($tax->is_available==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&task=managetaxes.state&tax_id[]='. $tax->tax_id.'&hotel_id='.$this->hotel_id )?> '"
							/>
						</TD>
						
					</TR>
					<?php
					}
					?>
					</tbody>
				</TABLE>
				<?php
				}
				?>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tax_id" value="" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
		<?php
		if(JHotelUtil::isJoomla3()){
			JHtml::_('behavior.framework');
		}
		else{
			JHTML::_('behavior.mootools');
		}
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
			if (pressbutton == 'edit' || pressbutton == 'Delete') 
			{
				var isSel = false;
				if( form.elements['boxchecked'].length == null )
				{
					if(form.elements['boxchecked'].checked)
					{
						isSel = true;
					}
				}
				else
				{
					for( i = 0; i < form.boxchecked.length; i ++ )
					{
						if(form.elements['boxchecked'][i].checked)
						{
							isSel = true;
							break;
						}
					}
				}
				
				if( isSel == false )
				{
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true); ?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
		jQuery(document).ready(function()
				{
					var hotelId=jQuery('#hotel_id').val();
					var refreshScreen=jQuery('#refreshScreen').val();
					var nrHotels = jQuery('#hotel_id option').length;
					if(hotelId>0 && refreshScreen=="" && parseInt(nrHotels)==2){
						jQuery('#refreshScreen').val("true");
						jQuery("#hotel_id").trigger('change');	
					}
				});	
	</script>
</form>
<?php
}
else
{

?>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_TAX_DETAILS',true); ?></legend>
		<center>
		<div style='text-align:left'>
			<strong>
				<?php echo JText::_('LNG_HOTEL',true)?> : 
				<?php 
					echo stripslashes($this->hotel->hotel_name);
					echo (strlen($this->hotel->country_name)>0? ", ".$this->hotel->country_name : "");
					echo stripslashes(strlen($this->hotel->hotel_city)>0? ", ".$this->hotel->hotel_city : "");
				?>
			</strong>
			<hr>
		</div>
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_TYPE',true); ?> :</TD>
				<TD nowrap colspan=2 align=left>
					<select
						id 		= "tax_type"
						name	= "tax_type"
					>
						<option <?php echo $this->item->tax_type=='Fixed'? "selected" : ""?> value='Fixed'><?php echo JText::_('LNG_AMOUNT',true); ?></option>
						<option <?php echo $this->item->tax_type=='Percent'? "selected" : ""?> value='Percent'><?php echo JText::_('LNG_PERCENT',true); ?></option>
					</select>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?>:</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "tax_name"
						id			= "tax_name"
						value		= '<?php echo $this->item->tax_name?>'
						size		= 50
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_VALUE',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "tax_value"
						id			= "tax_value"
						value		= '<?php echo $this->item->tax_value?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
					/>
					
				</TD>
				<TD align=left><?php echo JText::_( 'LNG_TAX_PRICE_PERCENT' ,true); ?></TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DESCRIPTION',true); ?> :</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<textarea id='tax_description' name='tax_description' rows=10 cols=70><?php echo $this->item->tax_description?></textarea>
				</TD>
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
			if (pressbutton == 'save') 
			{
				if( !validateField( form.tax_name, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_TAX_NAME',true); ?>" ) )
					return false;
				if( !validateField( form.tax_value, 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_TAX_PRICE__PERCENT',true); ?>" ) )
					return false;
				if( !validateField( form.tax_description, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_DESCRIPTION_TAX',true); ?>" ) )
					return false;
			
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tax_id" value="<?php echo $this->item->tax_id ?>" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />
	<input type="hidden" name="controller" value="managetaxes>" />
	<input type="hidden" name="view" value="managetaxes>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

