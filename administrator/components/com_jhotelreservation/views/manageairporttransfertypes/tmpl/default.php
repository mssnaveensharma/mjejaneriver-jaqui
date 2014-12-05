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
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									form.elements["view"].value = "manageairporttransfertypes";
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
				<TABLE class="adminlist" >
					<thead>
						<th width='1%'>#</th>
						<th width='1%'  align=center>&nbsp;</th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true); ?></B></th>
						<th width='30%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_PRICE',true); ?></B></th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_VAT',true); ?></B></th>
						<th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$airport_transfer_type = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $airport_transfer_type->airport_transfer_type_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.airport_transfer_type_id.value = '<?php echo $airport_transfer_type->airport_transfer_type_id?>'
										" 
							/>
							
						</TD>
						<TD align=left>
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=manageairporttransfertypes&view=manageairporttransfertypes&task=edit&hotel_id='. $airport_transfer_type->hotel_id.'&airport_transfer_type_id[]='. $airport_transfer_type->airport_transfer_type_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $airport_transfer_type->airport_transfer_type_name?></B>
							</a>	
							
						</TD>
						<TD align=left><?php echo $airport_transfer_type->airport_transfer_type_description?></TD>
						<TD align=center><?php echo $airport_transfer_type->airport_transfer_type_price?></TD>
						<TD align=center><?php echo $airport_transfer_type->airport_transfer_type_vat !=0? ($airport_transfer_type->airport_transfer_type_vat.' %') : $airport_transfer_type->airport_transfer_type_vat?></TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($airport_transfer_type->is_available==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"	
												document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=manageairporttransfertypes&view=manageairporttransfertypes&task=state&hotel_id='. $airport_transfer_type->hotel_id.'&airport_transfer_type_id[]='. $airport_transfer_type->airport_transfer_type_id )?> '
											"
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
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="airport_transfer_type_id" value="" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
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
			if( pressbutton =='back' )
			{
				form.elements['task'].value 		= 'menu_airport_transfer';
				form.elements['view'].value 		= 'jhotelreservation';
				form.elements['controller'].value 	= 'jhotelreservation';
				pressbutton = 'menu_airport_transfer';
				submitform( pressbutton )
			}
			else if (pressbutton == 'edit' || pressbutton == 'Delete') 
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
		<legend><?php echo JText::_('LNG_AIRPORT_TRANSFER_TYPE_DETAILS',true); ?></legend>
		<center>
		<div style='text-align:left'>
			<strong>
				<?php echo JText::_('LNG_HOTEL',true)?> : 
				<?php 
					echo $this->hotel->hotel_name;
					echo (strlen($this->hotel->country_name)>0? ", ".$this->hotel->country_name : "");
					echo (strlen($this->hotel->hotel_city)>0? ", ".$this->hotel->hotel_city : "");
				?>
			</strong>
			<hr>
		</div>
		<TABLE class="admintable" align=center border=0 width=100%>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?>:</TD>
				<TD nowrap width=90% align=left>
					<input 
						type		= "text"
						name		= "airport_transfer_type_name"
						id			= "airport_transfer_type_name"
						value		= '<?php echo $this->item->airport_transfer_type_name?>'
						size		= 70
						maxlength	= 128
						
					/>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_PRICE',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "airport_transfer_type_price"
						id			= "airport_transfer_type_price"
						value		= '<?php echo $this->item->airport_transfer_type_price?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
					/>
					
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_VAT',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "airport_transfer_type_vat"
						id			= "airport_transfer_type_vat"
						value		= '<?php echo $this->item->airport_transfer_type_vat?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
					/>
					
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DESCRIPTION',true); ?> :</TD>
				<TD nowrap colspan=1 ALIGN=LEFT>
					<?php 
					$editor =JFactory::getEditor();
					echo  $editor->display('airport_transfer_type_description',  $this->item->airport_transfer_type_description, '850', '250', '70', '15', false); ?>
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
			if( !validateField( form.airport_transfer_type_name, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_TYPE_NAME',true); ?>" ) )
				return false;
			if( !validateField( form.airport_transfer_type_price, 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_TYPE_PRICE',true); ?>" ) )
				return false;
			if( !validateField( form.airport_transfer_type_vat, 'numeric', true, "<?php echo JText::_('LNG_PLEASE_INSERT_AIRPORT_TRANSFER_TYPE_VAT',true); ?>" ) )
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
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />	
	<input type="hidden" name="airport_transfer_type_id" value="<?php echo $this->item->airport_transfer_type_id ?>" />
	<input type="hidden" name="controller" value="manageairporttransfertypes>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

