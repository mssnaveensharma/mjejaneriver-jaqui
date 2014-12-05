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
$appSetings = JHotelUtil::getApplicationSettings();

if( 
	JRequest::getString( 'task') !='edit' 
	&& 
	JRequest::getString( 'task') !='add' 
) 
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_MANAGE_PACKAGES',true); ?></legend>
			<center>
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "managepackages";
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
				<TABLE class="adminlist" >
					<thead>
						<TD width='1%'>#</TD>
						<TD width='1%'  align=center>&nbsp;</TD>
						<TD width='20%' align=center><B><?php echo JText::_('LNG_NAME',true)?></B></TD>
						<TD width='20%' align=center><B><?php echo JText::_('LNG_PERIOD',true)?></B></TD>
						<TD width='30%' align=center ><B><?php echo JText::_('LNG_DESCRIPTION',true)?></B></TD>
						<!--
						<TD width='10%' align=center><B><?php echo JText::_('LNG_CAPACITY',true)?>Capacity</B></TD>
						-->
						<TD width='10%' align=center><B><?php echo JText::_('LNG_PRICE',true)?></B></TD>
						<TD width='1%' align=center><B>&nbsp;</B></TD>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$package = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD valign=top align=center><?php echo $nrcrt++?></TD>
						<TD valign=top align=center>
							 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $package->package_id?>" 
								onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
								onmouseout	=	"this.style.cursor='default'"
								onclick="
											adminForm.package_id.value = '<?php echo $package->package_id?>'
										" 
							/>
							
						</TD>
						<TD  valign=top align=left>
							<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&task=edit&package_id[]='. $package->package_id.'&hotel_id='.$this->hotel_id )?>'
								title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true)?>"
							>
								<B><?php echo $package->package_name?></B>
							</a>	
							
						</TD>
						<TD valign=top align=center>
						
							<?php echo $package->package_datas!='0000-00-00' ? JHotelUtil::getDateGeneralFormat($package->package_datas) : str_repeat('&nbsp;',10)?>
							<?php echo $package->package_datas!='0000-00-00' ||  $package->package_datae!='0000-00-00' ? '&nbsp;&nbsp;'. JText::_('LNG_TO',true) .'&nbsp;&nbsp;'  : ' '?>
							<?php echo $package->package_datae!='0000-00-00' ? JHotelUtil::getDateGeneralFormat($package->package_datae) : str_repeat('&nbsp;',10)?>
						</TD>
						<TD  valign=top align=left><?php echo $package->package_description?></TD>
						<!--
						<TD align=center><?php echo $package->package_number?></TD>
						-->
						<TD  valign=top align=center>
							<?php
							/*
							if( $package->is_price_day ==false )
							{
							?>
							<?php echo $package->package_price?> (<?php echo  JText::_('LNG_PACKAGE_PRICE',true) ?>)
							<?php
							}
							else
							{
								?>
								<ul style='margin-top:0px'>
								<?php
								ksort($package->package_prices);
								$daysWeek = array( "", "MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN" );
								foreach( $package->package_prices as $keyPrice => $days )
								{
									echo "<li style='text-align:left'>".$keyPrice."<ul>";
									foreach( $days as $valDay )
									{
										echo "<li style='text-align:left'>".JText::_($daysWeek[$valDay],true)."</li>";
									}
									echo "</ul></li>";
								}
								?>
								</ul>
								<?php
							}*/
							if( $package->is_price_day ==false )
							{
							?>
							<?php echo $package->package_price?> (<?php echo  JText::_('LNG_PACKAGE_PRICE',true) ?>)
							<?php
							}
							else
							{
							?>
							<table cellpadding=0 cellspacing=0 class="price-room-view">
								<tr>
									<?php
									switch( $package->package_type_price )
									{
										case 0:
											?>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_MON',true)?> <br/> <?php echo $package->package_price_1?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_TUE',true)?> <br/> <?php echo $package->package_price_2?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_WED',true)?> <br/> <?php echo $package->package_price_3?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_THU',true)?> <br/> <?php echo $package->package_price_4?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_FRI',true)?> <br/> <?php echo $package->package_price_5?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_SAT',true)?> <br/> <?php echo $package->package_price_6?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_SUN',true)?> <br/> <?php echo $package->package_price_7?></TD>
											<TD align=center align=center>(<?php echo JText::_('LNG_DAY_BY_DAY',true)?>)</TD>
											<?php
											break;
										case 1:
											?>
											<TD align=center style='border-right:solid 1px black'><?php echo $package->package_price?></TD>
											<TD align=center align=center>(<?php echo JText::_('LNG_SAME_EVERY_DAY',true)?>)</TD>
											<?php
											break;
										case 2:
											?>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_STR_MIDWEEK',true)?> : <?php echo $package->package_price_midweek?></TD>
											<TD align=center style='border-right:solid 1px black'><?php echo JText::_('LNG_STR_WEEKEND',true)?> : <?php echo $package->package_price_weekend?></TD>
											<TD align=center align=center>(<?php echo JText::_('LNG_MIDDWEEK_WEEKEND',true)?>)</TD>
											
											<?php
											break;
									}
									?>
								</tr>
							</table>
							<?php
							}
							?>
						</TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($package->is_available==false? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"	
												document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&controller=managepackages&view=managepackages&task=state&package_id[]='. $package->package_id.'&hotel_id='.$this->hotel_id )?> '
											"
							/>
							
						</TD>
						
					</TR>
					<?php
					}
					?>
					<tbody>
				</TABLE>
				<?php
				}
				?>
			</center>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName() ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="package_id" value="" />
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
					alert('<?php echo JText::_('LNG_YOU_MUST_SELECT_ONE_RECORD',true)?>');
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
<form autocomplete='off' action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LNG_PACKAGE_DETAILS',true); ?></legend>
		<center>
		<div id='div_calendar' class='div_calendar'>
			<p>
				<div class="dates_package_calendar" id="dates_package_calendar"></div>
			</p>
		</div>

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
				<TD width=10% class="key" nowrap><?php echo JText::_('LNG_NAME',true)?> :</TD>
				<TD nowrap width=1% align=left>
					<input 
						type		= "text"
						name		= "package_name"
						id			= "package_name"
						value		= '<?php echo $this->item->package_name?>'
						size		= 32
						maxlength	= 128
						
					/>
				</TD>
				<TD>&nbsp;</TD>
			</TR>
			<TR>
				<TD width=10% nowrap class="key"><?php echo JText::_('LNG_PRICE',true); ?> :</TD>
				<td colspan=2 valign=top align=left>
					<input type='radio' name='is_price_day' id='is_price_day' <?php echo $this->item->is_price_day==1? "checked" : ""?> value='1'
						onclick = "
									jQuery('#type_of_price').show(0);
									if( jQuery('#package_type_price').val() == '0' )
									{
										jQuery('#div_price_day_by_day').show(0); 
										jQuery('#div_price_every_day').hide(0); 
										jQuery('#div_price_midweek_weekend').hide(0); 
									}
									else if( jQuery('#package_type_price').val() == '1' )
									{
										jQuery('#div_price_every_day').show(0); 
										jQuery('#div_price_day_by_day').hide(0); 
										jQuery('#div_price_midweek_weekend').hide(0); 
									}
									else if( jQuery('#package_type_price').val() == '2' )
									{
										jQuery('#div_price_midweek_weekend').show(0); 
										jQuery('#div_price_day_by_day').hide(0); 
										jQuery('#div_price_every_day').hide(0); 
									}
						"
					>
					<i><?php echo JText::_('LNG_FOR_ONE_NIGHT',true); ?></i>
					<input type='radio' name='is_price_day' id='is_price_day' <?php echo $this->item->is_price_day==0? "checked" : ""?> value='0'
						onclick ="
									jQuery('#type_of_price').hide();
									jQuery('#div_price_day_by_day').hide(0); 
									jQuery('#div_price_midweek_weekend').hide(0); 
									jQuery('#div_price_every_day').show(10); 
						"
					>
					<i><?php echo JText::_('LNG_PACKAGE_PRICE',true); ?></i>
					
					<BR>
					<div id='type_of_price' name='type_of_price' <?php echo $this->item->is_price_day==0? "style='display:none'" : ""?> >
						<BR>
						&nbsp;&nbsp;<?php echo JText::_('LNG_SWITCH_TO',true) ?> :
						<input type='hidden' name='package_type_price' id='package_type_price' value='<?php echo $this->item->package_type_price?>'>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href='javascript:'
							class='<?php echo $this->item->package_type_price == 0 ? "type_price_sel" : "type_price"?>'
							onclick = '
									if( jQuery("#package_type_price").val() == "0" )
										return;
									jQuery("#package_type_price").val(0);
									
									jQuery("#div_price_day_by_day").show(100);
									jQuery("#link_price_day_by_day").attr("class","type_price_sel");

									jQuery("#div_price_every_day").hide(0); 
									jQuery("#link_price_every_day").attr("class","type_price");
									
									jQuery("#div_price_midweek_weekend").hide(0);
									jQuery("#link_price_midweek_weekend").attr("class","type_price");
								' 
							name = 'link_price_day_by_day'
							id	 = 'link_price_day_by_day'
						>
							<?php echo JText::_('LNG_DAY_BY_DAY',true)?>
						<a>
						&nbsp;
						<a 
							class='<?php echo $this->item->package_type_price == 1 ? "type_price_sel" : "type_price"?>'
							href='javascript:' 
							onclick = '
										if( jQuery("#package_type_price").val() == "1" )
											return;
										
										jQuery("#package_type_price").val(1);
										
										jQuery("#div_price_day_by_day").hide(0);
										jQuery("#link_price_day_by_day").attr("class","type_price");
										
										jQuery("#div_price_every_day").show(100); 
										jQuery("#link_price_every_day").attr("class","type_price_sel");
										
										jQuery("#div_price_midweek_weekend").hide(0);
										jQuery("#link_price_midweek_weekend").attr("class","type_price");
									' 
							name = 'link_price_every_day'
							id	 = 'link_price_every_day'
						>
							<?php echo JText::_('LNG_SAME_EVERY_DAY',true)?>
						<a>
						&nbsp;
						<a 
							class='<?php echo $this->item->package_type_price == 2 ? "type_price_sel" : "type_price"?>'
							href='javascript:' 
							onclick = '
										if( jQuery("#package_type_price").val() == "2" )
											return;
										
										jQuery("#package_type_price").val(2);
										
										jQuery("#div_price_day_by_day").hide(0);
										jQuery("#link_price_day_by_day").attr("class","type_price");
										
										jQuery("#div_price_every_day").hide(0); 
										jQuery("#link_price_every_day").attr("class","type_price");
										
										jQuery("#div_price_midweek_weekend").show(100);
										jQuery("#link_price_midweek_weekend").attr("class","type_price_sel");
									' 
							name = 'link_price_midweek_weekend'
							id	 = 'link_price_midweek_weekend'
						>
							<?php echo JText::_('LNG_MIDDWEEK_WEEKEND',true)?>
						<a>
					</div>
					<div id='div_price_day_by_day' name='div_price_day_by_day' style='<?php echo $this->item->package_type_price == 0? "display:block" : "display:none"?>'>
						<TABLE class='admintable' align=left border=0 width=100%>
							<TR>
								<td colspan=2 valign=top align=left>
									<TABLE cellpadding=0 cellspacing=0 align=left class="admintable" align=center border=0
										id='table_package_price_days' name='table_package_price_days' 
									>
										<TR>
											<?php
											for($day=1;$day<=7;$day++)
											{
											?>
											<TD nowrap="nowrap" align=center>
												<i>
												<?php		
												switch( $day )
												{
													case 1:
														echo JText::_('LNG_MON',true);
														break;
													case 2:
														echo JText::_('LNG_TUE',true);
														break;
													case 3:
														echo JText::_('LNG_WED',true);
														break;
													case 4:
														echo JText::_('LNG_THU',true);
														break;
													case 5:
														echo JText::_('LNG_FRI',true);
														break;
													case 6:
														echo JText::_('LNG_SAT',true);
														break;
													case 7:
														echo JText::_('LNG_SUN',true);
														break;
												}
												?>
												</i>
											</TD>
												<?php
											}
											?>
											<TD rowspan=2% width=40%>
												&nbsp;
											</TD>
										</TR>
										<TR>
											<?php
											for($day=1;$day<=7;$day++)
											{
												switch( $day )
												{
													case 1:
														$p = $this->item->package_price_1;
														break;
													case 2:
														$p = $this->item->package_price_2;
														break;
													case 3:
														$p = $this->item->package_price_3;
														break;
													case 4:
														$p = $this->item->package_price_4;
														break;
													case 5:
														$p = $this->item->package_price_5;
														break;
													case 6:
														$p = $this->item->package_price_6;
														break;
													case 7:
														$p = $this->item->package_price_7;
														break;
												}
											?>
											<TD nowrap nowrap align=left width=1% align=left valign=center nowrap>
												<input 
													type		= "text"
													name		= "package_price_<?php echo $day?>"
													id			= "package_price_<?php echo $day?>"
													value		= '<?php echo $p?>'
													size		= 10
													maxlength	= 10
													
													style		= 'text-align:right'
												/>
											</td>
											<?php
											}
											?>
										</TR>
									</TABLE>
								</td>
							</tr>
						</table>
					</div>
					<div id='div_price_every_day' name='div_price_every_day'  style='<?php echo $this->item->package_type_price == 1? "display:block" : "display:none"?>' >
						<TABLE class='admintable' align=left border=0 width=100%>
							<TR>
								<td colspan=2 valign=top align=left>
									<TABLE cellpadding=0 cellspacing=0 align=left class="admintable" align=center border=0
										id='table_package_price_days' name='table_package_price_days' 
									>
										<TR>
											<TD nowrap nowrap align=left width=100% align=left valign=center nowrap>
												<input 
													type		= "text"
													name		= "package_price"
													id			= "package_price"
													value		= '<?php echo $this->item->package_price==0? ""  : $this->item->package_price?>'
													size		= 10
													maxlength	= 10
													
													style		= 'text-align:right'
												/>
											</td>
										</TR>
									</TABLE>
								</td>
							</tr>
						</table>
					</div>
					<div id='div_price_midweek_weekend' name='div_price_midweek_weekend' style='<?php echo $this->item->package_type_price == 2? "display:block" : "display:none"?>' >
						<TABLE class='admintable' align=left border=0 width=100%>
							<TR>
								<td colspan=2 valign=top align=left>
									<TABLE cellpadding=0 cellspacing=0 align=left class="admintable" align=center border=0
										id='table_package_price_days' name='table_package_price_days' 
									>
										<TR>
											<TD nowrap nowrap align=center width=5% valign=center nowrap>
												<?php echo JText::_('LNG_STR_MIDWEEK',true)?>
											</td>
											<TD nowrap nowrap align=center width=5% valign=center nowrap>
												<?php echo JText::_('LNG_STR_WEEKEND',true)?>
											</td>
											<td width=90% rowspan=2>
												&nbsp;
											</TD>
										</TR>
										<TR>
											<TD nowrap nowrap align=left align=left valign=center nowrap>
												<input 
													type		= "text"
													name		= "package_price_midweek"
													id			= "package_price_midweek"
													value		= '<?php echo $this->item->package_price_midweek==0? ""  : $this->item->package_price_midweek?>'
													size		= 10
													maxlength	= 10
													
													style		= 'text-align:right'
												/>
											</td>
											<TD nowrap nowrap align=left align=left valign=center nowrap>
												<input 
													type		= "text"
													name		= "package_price_weekend"
													id			= "package_price_weekend"
													value		= '<?php echo $this->item->package_price_weekend==0? ""  : $this->item->package_price_weekend?>'
													size		= 10
													maxlength	= 10
													
													style		= 'text-align:right'
												/>
											</td>
										</TR>
									</TABLE>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<TR>
				<TD width=10% nowrap  class="key"><?php echo JText::_('LNG_PERIOD',true)?> :</TD>
				<TD nowrap colspan=1 ALIGN=LEFT>
					<?php echo JHTML::_('calendar', $this->item->package_datas==$appSetings->defaultDateValue?'': $this->item->package_datas, 'package_datas', 'package_datas', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					<?php echo JHTML::_('calendar', $this->item->package_datae==$appSetings->defaultDateValue?'': $this->item->package_datae, 'package_datae', 'package_datae', $appSetings->calendarFormat, array('class'=>'inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
				</TD>
				<td valign=middle nowrap>
						<input 
							type='hidden' 
							name='package_datai' 
							id='package_datai'
							value='<?php echo $this->item->package_datai?>'
						>
						<span
							class='span_ignored_days'
							name='btn_ignored_days'
							id='btn_ignored_days[]'
							onclick="
										clickBtnIgnoreDays();
									"
						>
							<?php echo JText::_('LNG_IGNORED_DAYS',true); ?>
						</span>
						<div 
							style	='font-size:1px;width:1px;height:1px' 
							id		='div_interval_packages_dates' 
							name	='div_interval_packages_dates' 
							class	='div_interval_packages_dates'
						>
						</div>
				</TD>
			</TR>
			<TR>
				<TD width=10% nowrap  class="key"><?php echo JText::_('LNG_DESCRIPTION',true)?> :</TD>
				<TD nowrap colspan=2 ALIGN=LEFT>
					<textarea id='package_description' name='package_description' rows=10 cols=70><?php echo $this->item->package_description?></textarea>
				</TD>
			</TR>
		</TABLE>
	</fieldset>
	<script language="javascript" type="text/javascript">
		var arrayDaySelected = [-1,0,0,0,0,0,0,0];
		<?php
		foreach( $this->item->package_prices as $keyPrice => $value )
		{
			foreach($value as $day)
			{
			?>
				arrayDaySelected[ <?php echo $day?> ] = 1;
			<?php
			}
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
			if (pressbutton == 'save') 
			{
				if( !validateField( form.elements['package_name'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_PACKAGE_NAME',true)?>" ) )
					return false;
				// if( !validateField( form.elements['package_price'], 'numeric', false, "<?php echo JText::_('LNG_PLEASE_INSERT_PACKAGE_PRICE',true)?>" ) )
					// return false;
				// if( !validateField( form.elements['package_description'], 'string', false, "<?php echo JText::_('LNG_PLEASE_INSERT_DESCRIPTION_PACKAGE',true)?>" ) )
					// return false;
			
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
	jQuery(document).ready(function()
	{
		tinyMCE.init({
			// General options
			mode : "exact",
			elements : "package_description" ,
			theme : "advanced",
			skin : "o2k7",
			skin_variant : "silver",
			plugins : "lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

			// Theme options
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
	});
	
	jQuery('#dates_package_calendar').DatePicker(
											{
												//flat: 		true,
												date: 			[  ],
												current: 		new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, 1, 0,0,0),
												format: 		'Y-m-d',
												calendars: 		2,
												mode: 			'multiple',
												position:		'right',
												className: 		'custom-picker',
												starts: 		0,
												onRender: function(date) {
																			var d =  new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, <?php echo date('d')?>, 0,0,0);
																			return {
																				disabled: (date.valueOf() < d.valueOf()),
																				className: date.valueOf() == d.valueOf() ? 'datepickerSpecial' : false
																			}
																		},
												onBeforeShow: function(){
													
																			var crtVal = new Array();
																			crtVal = (jQuery("#package_datai").val( )).split(',');
																			jQuery('#dates_package_calendar').DatePickerClear();
																			jQuery('#dates_package_calendar').DatePickerSetDate(crtVal);
																		},
												onHide: function()
																		{
																			
																			jQuery('span[name=btn_ignored_days]').each(function()
																			{
																				this.className = 'span_ignored_days';
																			});
																			return true;
																		},

												onChange: function(formated, dates){
																					//package_number_datai
																					jQuery("#package_datai").val( formated.join(',') );
																				}

											}
										);
	function clickBtnIgnoreDays()
	{
		jQuery('#div_interval_packages_dates').append( jQuery('#div_calendar') );
		jQuery('#dates_package_calendar').DatePickerShow();
		this.className = 'span_ignored_days_sel';
	}
	
	function delPackagePriceDay(pos)
	{
		var form 		= document.adminForm;
		var tb = document.getElementById('table_package_price_days');

		if( tb==null )
		{
			alert('Undefined table, contact administrator !');
		}
		if( pos >= tb.rows.length )
			pos = tb.rows.length-1;
		
		if( pos < tb.rows.length-1 )
		{
			alert("<?php echo JText::_('LNG_ONLY_REVERSE_DELETE',true)?>',true);
			return;
		}		
		for( i = 1; i <= 7; i++ )
		{
			if( form.elements['day_'+ pos+'_'+ i+'[]'].checked )
			{
				//onCheckDay( pos, i, false );
				//onCheckDay( 0, i, true );
				alert("<?php echo JText::_('LNG_UNCHECK_FIRST',true)?>',true);
				return;
			}
		}
		tb.deleteRow( pos );
	}
	
	function onCheckDay( nPos, nDay, bStatus )
	{
		var form 		= document.adminForm;
		var tb = document.getElementById('table_package_price_days');
		if( nPos >= tb.rows.length )
			nPos = tb.rows.length-1;
		
		arrayDaySelected[nDay] = bStatus ? 1 : 0;
		var v_status  	= null;
		
		var tb = document.getElementById('table_package_price_days');
		
		var r 		= tb.rows.length;
		for( i = 0; i < r; i++ )
		{
			if( nPos == i )
				continue;
			form.elements['day_'+  i +'_' + nDay+'[]'].disabled = bStatus;
		}
	}
	
	function addPackagePriceDay()
	{
	
		var tb = document.getElementById('table_package_price_days');
		if( tb==null )
		{
			alert('Undefined table, contact administrator !');
		}
		
		count_tr = tb.rows.length;
		
		if( count_tr == 7 )
		{
			alert("<?php echo JText::_('LNG_YOU_HAVE_SEVEN_ROWS_DAYS',true)?>',true);
			return ;
		}
		
		var td1_new			= document.createElement('td');  
		td1_new.style.textAlign='left';

		var input_o_new 	= document.createElement('input');
		input_o_new.setAttribute('type',		'text');
		input_o_new.setAttribute('name',		'package_price_day[]');
		input_o_new.setAttribute('id',			'package_price_day_'+count_tr);
		input_o_new.setAttribute('size',		'10');
		input_o_new.setAttribute('maxlength',	'10');
		//input_o_new.autocomplete				= 'off';
		
		td1_new.appendChild(input_o_new);
				
		var td2_new			= document.createElement('td');  
		td2_new.style.textAlign='left';
		td2_new.setAttribute("nowrap","nowrap");
		<?php
		for($day=1;$day<=7;$day++)
		{
			?>
			var chkbox 		= document.createElement('input');   
			chkbox.type 	= "checkbox";
			if( arrayDaySelected[<?php echo $day?>] ==1 )
				chkbox.disabled = true;
			chkbox.id 		= 'day_'+count_tr+'_<?php echo $day ?>[]';
			chkbox.name 	= 'day_'+count_tr+'_<?php echo $day ?>[]';  
			chkbox.value	= '1';
			chkbox.setAttribute('onclick', 'onCheckDay('+ count_tr + ',' + "<?php echo $day?>" + ', this.checked' +')');
			<?php
			$str_day_name = '';
			switch( $day )
			{
				case 1:
					$str_day_name = JText::_('LNG_MON',true);
					break;
				case 2:
					$str_day_name = JText::_('LNG_TUE',true);
					break;
				case 3:
					$str_day_name = JText::_('LNG_WED',true);
					break;
				case 4:
					$str_day_name = JText::_('LNG_THU',true);
					break;
				case 5:
					$str_day_name = JText::_('LNG_FRI',true);
					break;
				case 6:
					$str_day_name = JText::_('LNG_SAT',true);
					break;
				case 7:
					$str_day_name = JText::_('LNG_SUN',true);
					break;
			}
			$str_day_name .='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
			?>
			td2_new.appendChild(chkbox);
			td2_new.innerHTML += "<?php echo $str_day_name?>";
			<?php
		}
		?>
		
		var td3_new			= document.createElement('td');
		td3_new.style.textAlign='left';
		td3_new.setAttribute("nowrap","nowrap");
		
		var img_del		 	= document.createElement('img');
		img_del.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>");
		img_del.setAttribute('alt', 'Delete price day');
		img_del.setAttribute('id', 	count_tr);
		img_del.setAttribute('name', 'del_price_day_' + count_tr);
		img_del.setAttribute('class', 'btn_picture_delete');
		img_del.onclick  		= function(){ delPackagePriceDay(this.id); };
		img_del.onmouseover  	= function(){ this.style.cursor='hand';this.style.cursor='pointer' };
		img_del.onmouseout 		= function(){ this.style.cursor='default' };
		td3_new.appendChild(img_del);

		var tr_new = tb.insertRow(tb.rows.length);

		tr_new.appendChild(td1_new);
		tr_new.appendChild(td2_new);
		tr_new.appendChild(td3_new);
				
	}

	</script>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="is_error" value="1" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />
	<input type="hidden" name="package_id" value="<?php echo $this->item->package_id ?>" />
	<input type="hidden" name="controller" value="managepackages" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>
<?php
}
?>

