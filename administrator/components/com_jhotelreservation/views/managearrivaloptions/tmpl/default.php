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
									form.elements["view"].value = "managearrivaloptions";
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
						<th width='1%' align=center><B><?php echo JText::_('LNG_AVAILABLE',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$arrival_option = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $arrival_option->arrival_option_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.arrival_option_id.value = '<?php echo $arrival_option->arrival_option_id?>'
										" 
							/>
							
						</TD>
						<TD align=left>
							
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&task=edit&hotel_id='.$arrival_option->hotel_id.'&arrival_option_id[]='. $arrival_option->arrival_option_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"
							>
								<B><?php echo $arrival_option->arrival_option_name?></B>
							</a>	
							
						</TD>
						<TD align=left><?php echo $arrival_option->arrival_option_description?></TD>
						<TD align=center><?php echo $arrival_option->arrival_option_price?></TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($arrival_option->is_available==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"	
												document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managearrivaloptions&view=managearrivaloptions&task=state&hotel_id='.$arrival_option->hotel_id.'&arrival_option_id[]='. $arrival_option->arrival_option_id )?>'
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
	<input type="hidden" name="arrival_option_id" value="" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
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
	<script language="javascript" type="text/javascript">
			
		jQuery(document).ready(function()
		{
		
			tinyMCE.init({
				// General options
				mode : "exact",
				elements : "arrival_option_description",
				theme : "advanced",
				skin : "o2k7",
				skin_variant : "silver",
				plugins : "lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
			
				// Theme options
				//theme_advanced_buttons1 : "mylistbox",
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

				// Example content CSS (should be your site CSS)
				content_css : "css/content.css",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/template_list.js",
				external_link_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/link_list.js",
				external_image_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/image_list.js",
				media_external_list_url : "<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/assets/tiny_mce/sample/lists/media_list.js",

				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
				
			});
		})
	</script>
<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_ARRIVAL_OPTION_DETAILS',true); ?></legend>
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
						name		= "arrival_option_name"
						id			= "arrival_option_name"
						value		= '<?php echo $this->item->arrival_option_name?>'
						size		= 32
						maxlength	= 128
						
					/>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_PRICE',true); ?> :</TD>
				<TD nowrap align=left>
					<input 
						type		= "text"
						name		= "arrival_option_price"
						id			= "arrival_option_price"
						value		= '<?php echo $this->item->arrival_option_price?>'
						size		= 10
						maxlength	= 10
						
						style		= 'text-align:right'
					/>
					
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_DESCRIPTION',true); ?> :</TD>
				<TD nowrap colspan=1 ALIGN=LEFT>
					<textarea id='arrival_option_description' name='arrival_option_description' rows=10 cols=70><?php echo $this->item->arrival_option_description?></textarea>
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
			if( !validateField( form.arrival_option_name, 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_ARRIVAL_OPTION_NAME',true); ?>" ) )
				return false;
			if( !validateField( form.arrival_option_price, 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_ARRIVAL_OPTION_PRICE',true); ?>" ) )
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
	<input type="hidden" name="arrival_option_id" value="<?php echo $this->item->arrival_option_id ?>" />
	<input type="hidden" name="controller" value="managearrivaloptions>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

