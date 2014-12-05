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
		<TABLE class="adminlist" >
			<thead>
				<th width='1%'>#</th>
				<th width='1%'  align=center>&nbsp;</th>
				<Th width='20%' align=left><B><?php echo JText::_('LNG_NAME',true); ?></B></Th>
				<Th width='30%' align=left ><B><?php echo JText::_('LNG_SYMBOL',true); ?></B></Th>
			</thead>
			<tbody>
			<?php
			$nrcrt = 1;
			//if(0)
			for($i = 0; $i <  count( $this->items ); $i++)
			{
				$currency = $this->items[$i]; 

			?>
			<TR class="row<?php echo $i%2?>"
				onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
				onmouseout	=	"this.style.cursor='default'"
			>
				<TD align=center><?php echo $nrcrt++?></TD>
				<TD align=center>
					 <input type="radio" name="boxchecked"  id="boxchecked" 
						value="<?php echo $currency->currency_id?>" 
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
						<?php echo $currency->is_default_app ? (" disabled TITLE='".JText::_('LNG_CURRENCY_APPLICATION_DEFAULT',true)."'") : ""?>
						onclick="
									adminForm.currency_id.value = '<?php echo $currency->currency_id?>'
								" 
					/>
					
				</TD>
				<TD align=left>
					
					<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=manageCurrencies&view=manageCurrencies&task=edit&currency_id[]='. $currency->currency_id )?>'
						title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
					>
						<B><?php echo $currency->description?></B>
					</a>	
					
				</TD align=center>
				<td><?php echo $currency->currency_symbol?></td>
			</TR>
			<?php
			}
			?>
			</tbody>
		</TABLE>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="currency_id" value="" />
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
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true)?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	</script>
</form>
<?php
}
else
{

?>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_CURRENCY_DETAILS',true); ?></legend>
		<center>
		<TABLE class="admintable" align=center border=0>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_NAME',true); ?> :</TD>
				<TD nowrap width=1% align=left>
					<select 
						name = 'description'
						id	 = 'description'
						style= 'width:250px'
						onchange="setSymbol(this.value)";
					>
						<?php
						foreach( $this->item->countries as $country_currency)
						{
						?>
						<option 
							value='<?php echo $country_currency->country_currency_short?>'
							<?php echo $country_currency->country_currency_short == $this->item->description ? ' selected ' : ''?>
						>
							<?php echo $country_currency->country_name.' | '.$country_currency->country_currency_short.' | '.$country_currency->country_currency?>
						</option>
						<?php
						}
						?>
					</select>
					<!--
					<input 
						type		= "text"
						name		= "description"
						id			= "description"
						value		= '<?php echo $this->item->description?>'
						size		= 32
						maxlength	= 128
						
					/>
					-->
				</TD>
				<TD></TD>
			</TR>
			<tr>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_SYMBOL',true); ?> :</TD>
				<td>
					<input 
							type		= "text"
							name		= "currency_symbol"
							id			= "currency_symbol"
							value		= '<?php echo $this->item->currency_symbol?>'
							size		= 32
							maxlength	= 128
						/>
				</td>
			</tr>
			
			
		</TABLE>
	</fieldset>
	<script language="javascript" type="text/javascript">
	 var currencies = new Array();
	 <?php
	 foreach( $this->item->countries as $country_currency)
	 {
	 ?>
	 currencies["<?php echo $country_currency->country_currency_short ?>"]= "<?php echo $country_currency->country_currency_symbol?>";
	 <?php } ?>
	 
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
				if( !validateField( form.description, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_CURRENCY_NAME',true)?>" ) )
					return false;
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}

		function setSymbol(symbol){
			//console.debug(symbol);
			//console.debug(currencies[symbol]);
			jQuery("#currency_symbol").val(currencies[symbol]);
		}
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="currency_id" value="<?php echo $this->item->currency_id ?>" />
	<input type="hidden" name="controller" value="managecurrencies" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

