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

if( 
	JRequest::getString( 'task') !='edit' 
	&& 
	JRequest::getString( 'task') !='add' 
) 
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
				<TABLE class="adminlist" >
					<thead>
						<th width='1%'>#</th>
						<th width='1%'  align=center>&nbsp;</th>
						<th width='20%' align=center><B><?php echo JText::_('LNG_NAME',true)?></B></th>
						<th width='30%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true)?></B></th>
						<th width="1%"  align=center><B><?php echo JText::_('LNG_TYPE',true)?></B></th>
					</thead>
					<tbody>

					<?php
					$nrcrt = 1;
					
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$feature = $this->items[$i]; 
					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $feature->feature_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.feature_id.value = '<?php echo $feature->feature_id?>'
										" 
							/>
						</TD>
						<TD align=left>
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=manageroomfeatures&view=manageroomfeatures&task=edit&feature_id[]='. $feature->feature_id )?>'
								title		= 	"<?php JText::_('LNG_CLICK_TO_EDIT',true)?>"
							>
								<B><?php echo $feature->feature_name?></B>
							</a>	
							
						</TD>
						<TD align=left><?php echo $feature->feature_description?></TD>
						<TD align=center width=10%><?php echo $feature->is_multiple_selection? JText::_('LNG_MULTIPLE_SELECT',true): JText::_('LNG_SINGLE_SELECT',true) ?></TD>
					</TR>
					<?php
					}
					?>
					</tbody>
				</TABLE>
		<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="feature_id" value="" />
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
					alert('<?php JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true)?>');
					return false;
				}
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
		</script>
	</div>
</form>
<?php
}
else
{
?>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_ROOM_FEATURE_DETAILS',true); ?></legend>
		<center>
		<TABLE id='table_feature_options' name='table_feature_options' align=left border=0 width=100% >
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_FEATURE_ROOM_NAME',true)?> :</TD>
				<TD nowrap width=1%  align=left>
					<input 
						type		= "text"
						name		= "feature_name"
						id			= "feature_name"
						value		= '<?php echo $this->item->feature_name?>'
						size		= 32
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10%  class="key" nowrap><?php echo JText::_('LNG_TYPE',true)?> :</TD>
				<TD nowrap  align=left >
					<input 
						type		= "radio"
						name		= "is_multiple_selection"
						id			= "is_multiple_selection"
						value		= '1'
						<?php echo $this->item->is_multiple_selection==true? " checked " :""?>
						accesskey	= "M"
						onclick 	= 	'
										/*
										if(document.adminForm.number_of_options.length == 1)
										{
											//option1 = new Option("2",1);
											//document.adminForm.number_of_options[1] = option1;
											//document.adminForm.number_of_options.selectedIndex = 1;
										}
										else
										{
											//alert(document.adminForm.number_of_options.selectedIndex);
											document.adminForm.number_of_options.selectedIndex = 1;
										}
										
										if( adminForm.elements["option_name[]"].type == "text" )
										{
											addFeatureRoomOption();
										}
										else
										{
											
										}
										*/
										
									'
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
						
					/>
					<?php echo JText::_('LNG_MULTIPLE_SELECT',true)?>
					&nbsp;
					<input 
						type		= "radio"
						name		= "is_multiple_selection"
						id			= "is_multiple_selection"
						value		= '0'
						<?php echo $this->item->is_multiple_selection==false? " checked " :""?>
						accesskey	= "S"
						onclick 	= 	'
											/*
											if( document.adminForm.number_of_options.length > 1 )
											{
												document.adminForm.number_of_options.selectedIndex = 0;
											}
											else
											{
												
											}
											*/
										'
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"

					/>
					<?php echo JText::_('LNG_SINGLE_SELECT',true)?>
				</TD>
				<TD nowrap>
					&nbsp;
				</TD>
			</TR>
			<TR style='display:none'>
				<TD width=10%  class="key" nowrap><?php echo JText::_('LNG_NUMBER_OF_OPTIONS',true)?> :</TD>
				<TD nowrap colspan=2 align=left>
					<select 
						style		= 'width:50px;text-align:center' 
						id			= 'number_of_options'
						name		= 'number_of_options'
						onchange 	= 	'
											/*
											if( this.options[ this.selectedIndex ].value > 1 )
											{
												adminForm.is_multiple_selection[0].checked = true;
											}
											else
											{
												adminForm.is_multiple_selection[1].checked = true;
											}
											*/
										'
					>
						<?php
						for( $i=0;$i < count($this->item->option_ids); $i++ )
						{
						?>
						<option <?php echo $this->item->number_of_options==($i+1)? " SELECTED " : ""?> value = "<?php echo ($i+1)?>">
							<?php echo $i+1?>
						</option>
						<?php
						}
						?>
					</select>
					&nbsp;&nbsp;
					<?php echo JText::_('LNG_NUMBER_OF_OPTION_FOR_ONE_FEATURE',true)?>
				</TD>
			</TR>
			<?php
			$i = 0;
			if( count($this->item->option_ids ) > 0 )
			{
				foreach( $this->item->option_ids as $key => $value )
				{
				?>
				<TR>
					<TD  class="key" width=10% nowrap><?php echo JText::_('LNG_OPTION_NAME',true)?> :</TD>
					<TD nowrap nowrap align=left width=30% align=left valign=center>
						<input type='hidden' name='option_id[]' id='option_id[]' value='<?php echo $value->option_id?>' >
						<input 
							type		= "text"
							name		= "option_name[]"
							id			= "option_name[]"
							value		= '<?php echo $value->option_name?>'
							size		= 32
							maxlength	= 128
							autocomplete= OFF
						/>
						&nbsp;&nbsp;<?php echo JText::_('LNG_PRICE',true)?>:&nbsp;&nbsp;
						<input 
							type		= "text"
							name		= "option_price[]"
							id			= "option_price[]"
							value		= '<?php echo $value->option_price?>'
							size		= 10
							maxlength	= 20
							autocomplete= OFF
							style		= 'text-align:right'
						/>
						<?php
						if( $i>0)
						{
						?>
						<img
							valign		=middle
							width		=12px 
							height  	=12px
							title		='Delete option'
							src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>"
							onclick 	="
											//if( adminForm.is_multiple_selection[1].checked == true )
											//	return false;
												
											delFeatureRoomOption(<?php echo $i+3?>);
										" 
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
						>
						<?php
						}
						?>
					</TD>
					<?php
					if( $i==0)
					{
					?>
					<TD nowrap nowrap align=left valign=top rowspan="<?php echo count($this->item->option_ids)?>" >
						<img 
							width		=16px 
							height 		=16px
							title		='Add option'
							src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/add_options.gif"?>"
							onclick 	="
											//if( adminForm.is_multiple_selection[1].checked == true )
											//	return false;
												
											addFeatureRoomOption();
										" 
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
						>
					</TD>
					<?php
					}
					?>
				</TR>
				<?php
				$i++;
				}
			}
			?>

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
				if( !validateField( form.feature_name, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_FEATURE_ROOM_NAME',true)?>" ) )
					return false;
				
				if( form.elements["option_name[]"].type =="text")
				{
					
					//alert('nu e obiect');
					if( !validateField( form.elements["option_name[]"], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_OPTION_NAME',true)?>" ) )
						return false;
					if( !validateField( form.elements["option_price[]"], 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_OPTION_PRICE',true)?>" ) )
						return false;
						
					
				}
				else
				{
					for( i = 0; i <= form.number_of_options.selectedIndex; i++ )
					{
						if( !validateField( form.elements["option_name[]"][i], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_OPTION_NAME',true)?>"  ) )
							return false;
						if( !validateField( form.elements["option_price[]"][i], 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_OPTION_PRICE',true)?>" ) )
							return false;
						
						
					}
					
				}
				
				//return false;
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
		
		function delFeatureRoomOption(pos)
		{
			var tb = document.getElementById('table_feature_options');
			//alert(tb);
			if( tb==null )
			{
				alert('Undefined table, contact administrator !');
			}
			
			if( pos >= tb.rows.length )
				pos = tb.rows.length-1;
			tb.deleteRow( pos );
			document.adminForm.number_of_options.remove(document.adminForm.number_of_options.length - 1);
			document.adminForm.number_of_options.selectedIndex 	= document.adminForm.number_of_options.length-1;
				
		}
		
		function addFeatureRoomOption()
		{
			var tb = document.getElementById('table_feature_options');
			//alert(tb);
			if( tb==null )
			{
				alert('Undefined table, contact administrator !');
			}
			
			var td1_new			= document.createElement('td');  
			td1_new.innerHTML	= 'Option Name :'
			
			var td2_new			= document.createElement('td');  
			td2_new.style.textAlign='left';

			var input_o_new 	= document.createElement('input');
			input_o_new.setAttribute('type',		'text');
			input_o_new.setAttribute('name',		'option_name[]');
			input_o_new.setAttribute('id',			'option_name[]');
			input_o_new.setAttribute('size',		'32');
			input_o_new.setAttribute('maxlength',	'128');
			//input_o_new.autocomplete				= 'off';
			
			
			var span_new 		= document.createElement('span');
			span_new.innerHTML 	= "&nbsp;&nbsp;&nbsp;Price :&nbsp;&nbsp;&nbsp;";
			
			var input_p_new 	= document.createElement('input');
			input_p_new.setAttribute('type',		'text');
			input_p_new.setAttribute('name',		'option_price[]');
			input_p_new.setAttribute('id',			'option_price[]');
			input_p_new.setAttribute('size',		'10');
			input_p_new.setAttribute('maxlength',	'20');
			
			var img_del		 	= document.createElement('img');
			img_del.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>");
			img_del.setAttribute('alt', 'Delete option');
			img_del.setAttribute('height', '12px');
			img_del.setAttribute('width', '12px');
			img_del.setAttribute('onclick', 'delFeatureRoomOption('+ (tb.rows.length) +')');
			img_del.setAttribute('onmouseover', "this.style.cursor='hand';this.style.cursor='pointer'");
			img_del.setAttribute('onmouseout', "this.style.cursor='default'");
			/*
			<img
							valign=middle
							width		=12px 
							height  	=12px
							title		='Delete option'
							src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>"
							onclick 	="
											//if( adminForm.is_multiple_selection[1].checked == true )
											//	return false;
												
											delFeatureRoomOption(<?php echo $i+3?>);
										" 
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
						>
			*/
			//input_p_new.autocomplete				= 'off';
			td2_new.appendChild(input_o_new);
			td2_new.appendChild(span_new);
			td2_new.appendChild(input_p_new);
			td2_new.innerHTML = td2_new.innerHTML + "&nbsp;";
			td2_new.appendChild(img_del);

			//var tr_new			= document.createElement('tr'); 
			var tr_new = tb.insertRow(tb.rows.length);

			tr_new.appendChild(td1_new);
			tr_new.appendChild(td2_new);
			
		
			//tb.appendChild(tr_new);
			
			//var fo = document.getElementById('feature_option');
			//alert(tr_new );
			var lenOptions = tb.rows.length - 3;
			
			if( document.adminForm.number_of_options.length < lenOptions ) // -3 first rows
			{
				var c 	= lenOptions;
				var v 	= lenOptions;
				option1 = new Option(c, v );
				document.adminForm.number_of_options[document.adminForm.number_of_options.length] 						= option1;
				document.adminForm.number_of_options.selectedIndex 														= document.adminForm.number_of_options.length-1;
				document.adminForm.number_of_options.options[document.adminForm.number_of_options.length-1].selected  	= true;
				document.adminForm.number_of_options.value															  	= v;
				
				//document.adminForm.is_multiple_selection[0].checked 					= true;
			}
			
			//alert(document.adminForm.number_of_options.value);
		}
		
	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="feature_id" value="<?php echo $this->item->feature_id ?>" />
	<input type="hidden" name="controller" value="manageroomfeatures" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

