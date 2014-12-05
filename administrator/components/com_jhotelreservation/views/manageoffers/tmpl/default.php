<?php defined('_JEXEC') or die('Restricted access'); 

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

if( JRequest::getString( 'task') !='edit' && JRequest::getString( 'task') !='add' )
{
?>
<form action="index.php" method="post" name="adminForm">
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'LNG_MANAGE_OFFERS' ,true); ?></legend>
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									var obView	= document.createElement("input");
									obView.type = "hidden";
									obView.name	= "view";
									obView.value= "manageoffers";
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
				<div id="boxes">
					<TABLE class="table table-striped adminlist"  name='table_offers' id='table_offers'>
						<thead>
					
							<th width='1%'>&nbsp;</th>
							<th width='2%'>&nbsp;</th>
							<?php 
								if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
							?>
							<th width='20%' align=center><B><?php echo JText::_( 'LNG_VOUCHER_CODE' ,true); ?></B></th>
							<?php 
								}
							?>
							<th width='15%' align=center><B><?php echo JText::_( 'LNG_NAME' ,true); ?></B></th>
							<th width='2%' align=center><B><?php echo JText::_( 'LNG_MIN_NIGHTS' ,true); ?></B></th>
							<th width='2%' align=center><B><?php echo JText::_( 'LNG_MAX_NIGHTS' ,true); ?></B></th>
							<th width='10%' align=center><B><?php echo JText::_( 'LNG_DATA_FRONT_DISPLAYED' ,true); ?></B></th>
							<th width='10%' align=center><B><?php echo JText::_( 'LNG_PERIOD' ,true); ?></B></th>
							<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
							?>
								<th width='1%' align=center>
									<?php echo JText::_( 'LNG_TOP'); ?>
								</th>
								<th width='1%' align=center>
									<?php echo JText::_( 'LNG_FEATURED'); ?>
								</th>
							<?php 
								}
							?>
							<th width='1%' align=center><B><?php echo JText::_( 'LNG_ENABLED' ,true); ?></B></th>
							<th width='1%' align=center><B>&nbsp;</B></th>
							<th width='1%' align=center><B>&nbsp;</B></th>
							<th width='1%' align=center><B>&nbsp;</B></th>
						</thead>
						<tbody>
						<?php
						$nrcrt = 1;
						//if(0)
						for($i = 0; $i <  count( $this->items ); $i++)
						{
							$offer = $this->items[$i]; 

						?>
						<TR
							onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
							onmouseout	=	"this.style.cursor='default'"
						>
							<TD align=center><?php echo $nrcrt++?></TD>
							<TD align=center>
								 <input type="radio" name="boxchecked"  id="boxchecked" value="<?php echo $offer->offer_id?>" 
									onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	=	"this.style.cursor='default'"
									onclick="
												adminForm.offer_id.value = '<?php echo $offer->offer_id?>'
											" 
								/>
							</TD>
							<?php 
								if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
							?>
							<TD align=center>
								<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageoffers.edit&offer_id[]='. $offer->offer_id.'&hotel_id='. $offer->hotel_id )?>'
									title		= 	"<?php echo JText::_( 'LNG_CLICK_TO_EDIT' ,true); ?>"
								>
									<B><?php echo $offer->vouchers?></B>
								</a>
							</TD>
							<?php } ?>
							<TD align=left>
								<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageoffers.edit&offer_id[]='. $offer->offer_id.'&hotel_id='. $offer->hotel_id )?>'
									title		= 	"<?php echo JText::_( 'LNG_CLICK_TO_EDIT' ,true); ?>"
								>
									<B><?php echo $offer->offer_name?></B>
								</a>
							</TD>
							<TD align=center>
								<B><?php echo $offer->offer_min_nights !=0 ? $offer->offer_min_nights : "&nbsp;"?></B>
							</TD>
							<TD align=center>
								<B><?php echo $offer->offer_max_nights !=0 ? $offer->offer_max_nights : "&nbsp;"?></B>
							</TD>
							<TD align=center>
								<?php echo $offer->offer_datasf=='0000-00-00' ? "&nbsp;" : JHotelUtil::getDateGeneralFormat($offer->offer_datasf)?>
								<?php echo JText::_( 'LNG_TO' ,true); ?>		
								<?php echo $offer->offer_dataef=='0000-00-00' ? "&nbsp;" : JHotelUtil::getDateGeneralFormat($offer->offer_dataef)?>
							</TD>
							<TD align=center>
								<?php echo $offer->offer_datas=='0000-00-00' ? "&nbsp;" : JHotelUtil::getDateGeneralFormat($offer->offer_datas)?>
								<?php echo JText::_( 'LNG_TO' ,true); ?>	
								<?php echo $offer->offer_datae=='0000-00-00' ? "&nbsp;" : JHotelUtil::getDateGeneralFormat($offer->offer_datae)?>
							</TD>
								<?php 
									if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
								?>
									<td align=center><img border=1
										src="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($offer->top==false? "unchecked.gif" : "checked.gif")?>"
										onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageoffers.changeTopState&offer_id='.$offer->offer_id.'&hotel_id='. $offer->hotel_id  )?> '" />
									</td>
									<td align=center><img border=1
										src="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($offer->featured==false? "unchecked.gif" : "checked.gif")?>"
										onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageoffers.changeFeaturedState&offer_id='.$offer->offer_id.'&hotel_id='. $offer->hotel_id  )?> '" />
									</td>
								<?php 
									}
								?>
							<TD align=center>
								<img border= 1 
									src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($offer->is_available==false? "unchecked.gif" : "checked.gif")?>" 
									onclick	=	"	
													document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=manageoffers.state&offer_id[]='. $offer->offer_id.'&hotel_id='. $offer->hotel_id )?> '
												"
								/>
							</TD>
							<TD>
								<div>
									<a href='#dialog_<?php echo $offer->offer_id?>' name='modal'>
										<img src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/info_icon.gif"?>" >
									</a>
								</div>
								<div 
									id		='dialog_<?php echo $offer->offer_id?>'
									class	='window'
								>
									<div class='info'>
										<SPAN class='title_ID'>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Close it' class='close'/>
										</span>
										<?php echo $offer->content_info?>
									</div>
								</div>
							</TD>
							<TD>
								<?php
								if( strlen($offer->warning_info)  > 0 )
								{
								?>
								<div>
									<a href='#warning_<?php echo $offer->offer_id?>' name='modal'>
										<img width='16px'  height='16px' src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/warning_offers.gif"?>" >
									</a>
								</div>
								<div 
									id		='warning_<?php echo $offer->offer_id?>'
									class	='window'
								>
									<div class='info'>
										<SPAN class='title_ID'>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Close it' class='close'/>
										</span>
										<?php echo $offer->warning_info?>
									</div>
								</div>
								<?php
								}
								?>
							</TD>
							<TD width='1%' align=center nowrap>
								<B>
									<span 
										name="span_up_<?php echo $offer->offer_id?>"
										id	="span_up_<?php echo $offer->offer_id?>"
										class= "span_up"
										onclick='
														jQuery.ajax({
															url		: "<?php echo JURI::base()?>?index.php&task=manageoffers.offer_order&tip_order=up&hotel_id=<?php echo $offer->hotel_id?>&offer_id=<?php echo $offer->offer_id?>",
															context	: document.body,
															success	: function( responce ){
																					var xml = responce;
																					jQuery(xml).find("answer").each(function()
																					{
																						if( jQuery(this).attr("error") == "0" )
																						{
																							var row = jQuery("#span_up_<?php echo $offer->offer_id?>").parents("tr:first"); 
																							row.insertBefore(row.prev());
																						}
																					});
																			}
														});
												'
									>
										<?php echo JText::_('LNG_STR_UP',true)?>
									</span>
									&nbsp;
									<span 
										name="span_down_<?php echo $offer->offer_id?>"
										id	="span_down_<?php echo $offer->offer_id?>"
										class="span_down"
										onclick='
													// var row = jQuery(this).parents("tr:first"); 
																					// row.insertAfter(row.next());
																	
														jQuery.ajax({
															url		: "<?php echo JURI::base()?>?index.php&controller=manageoffers&option=<?php echo getBookingExtName()?>&task=offer_order&tip_order=down&hotel_id=<?php echo $offer->hotel_id?>&offer_id=<?php echo $offer->offer_id?>",
															context	: document.body,
															success	: function(responce){
																					var xml = responce;
																					jQuery(xml).find("answer").each(function()
																					{
																						if( jQuery(this).attr("error") == "0" )
																						{
																							var row = jQuery("#span_down_<?php echo $offer->offer_id?>").parents("tr:first"); 
																							row.insertAfter(row.next());
																						}
																					});
																					
																				}
														});
												'
									>
										<?php echo JText::_('LNG_STR_DOWN',true)?>
									</span>
								</B>
							</TD>
						</TR>
						<?php
						}
						?>
						</tbody>
					</TABLE>
				</div>
				<?php
				}
				?>
				<div id="mask"></div>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="offer_id" value="" />
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
					alert('<?php echo JText::_( 'LNG_YOU_MUST_SELECT_ONE_RECORD' ,true); ?>');
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
<script>
	jQuery(document).ready(function()
	{
		jQuery('a[name=modal]').click(function(e) {
			//Cancel the link behavior
			e.preventDefault();
			//Get the A tag
			var id = jQuery(this).attr('href');
		
			//Get the screen height and width
			var maskHeight = jQuery(document).height();
			var maskWidth = jQuery(window).width();
				
			//Set heigth and width to mask to fill up the whole screen
			jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
			
			//transition effect		
			jQuery('#mask').fadeIn(1000);	
			jQuery('#mask').fadeTo("slow",0.8);	
		
			//Get the window height and width
			var winH = jQuery(window).height();
			var winW = jQuery(window).width();
			//Set the popup window to center
			// jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
			// jQuery(id).css('left', winW/2-jQuery(id).width()/2);
			jQuery(id).css('top',  f_scrollTop() + 20);
			jQuery(id).css('left', winW/2-jQuery(id).width()/2);
				
			//transition effect
			jQuery(id).fadeIn(2000); 
		
		});
		
		//if close button is clicked
		jQuery('.window .close').click(function (e) {
			//Cancel the link behavior
			e.preventDefault();
			
			jQuery('#mask').hide();
			jQuery('.window').hide();
		});		
		
		//if mask is clicked
		jQuery('#mask').click(function () {
			jQuery(this).hide();
			jQuery('.window').hide();
		});	
		
		function f_clientWidth() {
			return f_filterResults (
				window.innerWidth ? window.innerWidth : 0,
				document.documentElement ? document.documentElement.clientWidth : 0,
				document.body ? document.body.clientWidth : 0
			);
		}
		function f_clientHeight() {
			return f_filterResults (
				window.innerHeight ? window.innerHeight : 0,
				document.documentElement ? document.documentElement.clientHeight : 0,
				document.body ? document.body.clientHeight : 0
			);
		}
		function f_scrollLeft() {
			return f_filterResults (
				window.pageXOffset ? window.pageXOffset : 0,
				document.documentElement ? document.documentElement.scrollLeft : 0,
				document.body ? document.body.scrollLeft : 0
			);
		}


		function f_scrollTop() {
			return f_filterResults (
				window.pageYOffset ? window.pageYOffset : 0,
				document.documentElement ? document.documentElement.scrollTop : 0,
				document.body ? document.body.scrollTop : 0
			);
		}	
		function f_filterResults(n_win, n_docel, n_body) {
			var n_result = n_win ? n_win : 0;
			if (n_docel && (!n_result || (n_result > n_docel)))
				n_result = n_docel;
			return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
		}

		
	});
</script>
<?php
}
else
{

?>
<script>
	function update_offer_room_info(room_id)
	{
		// offer_room_info_discounts
		var strHtml = '';
		jQuery('input[name=\'btn_offer_room_discount_'+room_id+'[]\']').each(function()
		{
		
			if( this.checked )
			{
				var str =this.value;
				var v = str.split( "|");
				// jQuery("select#offer_type_discount_id option[value='"+v[1]+"']").html();
				var strTitle = "";
				strHtml	+=	'<TR>';
				if( jQuery("select#offer_type_discount_id option[value='"+v[1]+"']").html()==null )
				{
					//alert(jQuery('#type_price_'+room_id).val() );
					switch( jQuery('#type_price_'+room_id).val() )
					{
						case 'price':
							strTitle = "<?php echo JText::_('LNG_SAME_EVERY_DAY',true)?>";
							break;
						case 'week_day':
							strTitle = "<?php echo JText::_('LNG_DAY_BY_DAY',true)?>";
							break;
						case 'midweek_weekend_day':
							strTitle = "<?php echo JText::_('LNG_MIDDWEEK_WEEKEND',true)?>";
							break;
					}
				}
				else
					strTitle = jQuery("select#offer_type_discount_id option[value='"+v[1]+"']").html() ;
				strHtml +=	'<TD>' + strTitle + '&nbsp;</TD>';
				switch( v[2] )
				{
					case 'nr_per':
					case 'nr_day':
						strHtml	+=	'<TD colspan=7>';
						strHtml+= " >= " + jQuery('input[name=\'nr_'+room_id+"_"+v[1]+'\']').val();
						strHtml+= " : " + jQuery('input[name=\'val_'+room_id+"_"+v[1]+'\']').val();
						if( jQuery('input[name=\'type_'+room_id+"_"+v[1]+'\']').attr('checked') )
						{
							strHtml	+=	' %';
						}
						strHtml	+=	'</TD>';
						break;
					case 'week_day':
						var nr = 1;
						jQuery('input[name=\'week_vals_'+room_id+"_"+v[1]+'[]\']').each(function()
						{
							strHtml	+=	'<TD '+(nr<7? "style='border-right:solid 2px black'" : "")+'>';
							strHtml+= " " + this.value;
							var nr_sec = 1;
							jQuery('input[name=\'week_type_'+room_id+"_"+v[1]+'\']').each(function()
							{
								if( nr_sec == nr )
								{
									if(  this.checked ) 
									{
										strHtml	+=	' %';
									}
								}
								nr_sec ++;
							});
							
							strHtml	+=	'</TD>';
							nr ++;
						});
						break;
					case 'price':
						strHtml	+=	'<TD colspan=7>';
						strHtml+= " : " + jQuery('input[name=\'val_'+room_id+"_"+v[1]+'\']').val();
						if( jQuery('input[name=\'type_'+room_id+"_"+v[1]+'\']').attr('checked') )
						{
							strHtml	+=	' %';
						}
						strHtml	+=	'</TD>';
						break;
					case 'midweek_weekend_day':
						strHtml	+=	'<TD style=\'border-right:solid 2px black\' colspan=3>';
						strHtml+= "  " + jQuery('input[name=\'midweek_val_'+room_id+"_"+v[1]+'\']').val();
						if( jQuery('input[name=\'midweek_type_'+room_id+"_"+v[1]+'\']').attr('checked') )
						{
							strHtml	+=	' %';
						}
						strHtml	+=	'</TD>';
						strHtml	+=	'<TD colspan=4>';
						strHtml+= " : " + jQuery('input[name=\'weekend_val_'+room_id+"_"+v[1]+'\']').val();
						if( jQuery('input[name=\'weekend_type_'+room_id+"_"+v[1]+'\']').attr('checked') )
						{
							strHtml	+=	' %';
						}
						strHtml	+=	'</TD>';
						break;
				}
				strHtml	+=	'</TR>';
			}
		});
		jQuery("#offer_room_info_discounts").html("<I><TABLE cellpadding=0 cellspacing=0 align=center>"+strHtml +"</TABLE></I>"); 
	}
	
	jQuery(document).ready(function()
	{
		updateStatus();
		
		/* jQuery('#period_offer_calendar').DatePicker(
											{
												flat: 			true,
												date: 			[  ],
												current: 		new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, 1, 0,0,0),
												format: 		'Y-m-d',
												calendars: 		2,
												mode: 			'range',
												position:		'right',
												starts: 		0,
												onRender: function(date) {
																			var d =  new Date(<?php echo date('Y')?>, <?php echo date('m')-1?>, <?php echo date('d')?>, 0,0,0);
																			return {
																				//disabled: (date.valueOf() < d.valueOf()),
																				className: date.valueOf() == d.valueOf() ? 'datepickerSpecial' : false
																			}
																		},
												onChange: function(formated, dates){
																					if( formated.length > 0 )
																						jQuery("#offer_datas").val( formated[0] );
																					if( formated.length > 1 )
																						jQuery("#offer_datae").val( formated[1] );
																				}

											}
										);
		*/
		
		
		
		jQuery(".room_ids").change(function () {
			var str = "";
			//need clean all
			jQuery("select[name='room_details_id']").children().remove();
			
			<?php
			foreach( $this->item->itemRooms as $valueRoom )
			{
				?>
				jQuery("#div_info_room_price_<?php echo $valueRoom->room_id?>").css("display", "none"); 			
				<?php
			}
			?>

			//~need clean all
			jQuery("select[name='room_details_id']").append(new Option('', 0));

			jQuery("select[name='room_ids[]'] option:selected").each( function () 
			{
				if( jQuery(this).val()  > 0 )
				{					
					jQuery("select[name='room_details_id']").append(new Option(jQuery(this).text(), jQuery(this).val()));
				}
			});
			jQuery("#div_offer_discounts").css("display", "none"); 
			jQuery("#div_offer_packages").css("display", "none"); 
			jQuery("#div_offer_arrival_options").css("display", "none"); 
			
        });
		
		jQuery(".room_details_id").change(function () {
			var is_show 		= false;
			var option 			= this.options[this.selectedIndex];
			var id_room_show 	= 0;
			if( jQuery(option).val()  > 0 )
			{
				is_show 		= true;
				id_room_show 	= jQuery(option).val();
			}
			if( is_show )
			{
				jQuery("#div_offer_packages").show(1000);
				jQuery("#div_offer_discounts").show(1000);
				jQuery("#div_offer_arrival_options").show(1000);
				update_offer_room_info(this.options[this.selectedIndex].value);
			}
			else
			{
				jQuery("#div_offer_packages").hide(1000);
				jQuery("#div_offer_discounts").hide(1000);
				jQuery("#div_offer_arrival_options").hide(1000);
				update_offer_room_info(-1);
			}

			//alert(id_room_show);
			//jQuery("#offer_type_discount_id").change();
			<?php
			foreach( $this->item->itemRooms as $valueRoom )
			{
				?>
				if( id_room_show == <?php echo $valueRoom->room_id?> )
				{
					jQuery("#div_info_room_price_<?php echo $valueRoom->room_id?>").show( 1000 ); 
					jQuery("#div_info_offer_price_<?php echo $valueRoom->room_id?>").show( 1000 ); 
					jQuery("#div_offer_room_packages_<?php echo $valueRoom->room_id?>").show( 1000 ); 
					jQuery("#div_offer_room_arrival_options_<?php echo $valueRoom->room_id?>").show( 1000 ); 
				}
				else
				{
					jQuery("#div_info_room_price_<?php echo $valueRoom->room_id?>").hide( 1000 ); 
					jQuery("#div_info_offer_price_<?php echo $valueRoom->room_id?>").hide( 1000 ); 
					jQuery("#div_offer_room_packages_<?php echo $valueRoom->room_id?>").hide( 1000 );
					jQuery("#div_offer_room_arrival_options_<?php echo $valueRoom->room_id?>").hide( 1000 ); 
				}	
				<?php
			}
			?>
		});
		
		
		jQuery(function()
		{
			jQuery('#uploadedfile').change(function() {
				var fisRe 	= /^.+\.(jpg|bmp|gif|png)$/i;
				var path = jQuery('#uploadedfile').val();
				if (path.search(fisRe) == -1)
				{   
					alert(' JPG, BMP, GIF, PNG only!');
					return false;
				}
				jQuery(this).upload('<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/upload.php?t=<?php echo strtotime('now')?>&_root_app=<?php echo urlencode(JPATH_ROOT)?>&_target=<?php echo urlencode(PATH_PICTURES.PATH_OFFER_PICTURES.($this->item->offer_id+0).'/')?>', function(responce) 
																									{
																										//alert(responce);
																										if( responce =='' )
																										{
																											alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
																											jQuery(this).val('');
																										}
																										else
																										{
																											var xml = responce;
																											// alert(responce);
																											jQuery(xml).find("picture").each(function()
																											{
																												if(jQuery(this).attr("error") == 0 )
																												{
																													addPicture(
																																"<?php echo PATH_OFFER_PICTURES.($this->item->offer_id+0).'/'?>" + jQuery(this).attr("path"),
																																jQuery(this).attr("name")
																													);
																												}
																												else if( jQuery(this).attr("error") == 1 )
																													alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
																												else if( jQuery(this).attr("error") == 2 )
																													alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
																												else if( jQuery(this).attr("error") == 3 )
																													alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
																												else if( jQuery(this).attr("error") == 4 )
																													alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
																											});
																											
																											jQuery(this).val('');
																										}
																									}, 'html'
				);
			});
			
		});
		
		
		jQuery(function()
		{
			jQuery('#btn_removefile').click(function() {
			//function delPicture( obj, path, pos )
			//{
				pos 	= jQuery('#crt_pos').val();
				path 	= jQuery('#crt_path').val();
				jQuery( this ).upload('<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/remove.php?_root_app=<?php echo urlencode(JPATH_COMPONENT_ADMINISTRATOR)?>&_filename='+ path + '&_pos='+pos, function(responce) 
																									{
																										// alert(responce);
																										if( responce =='' )
																										{
																											alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
																											jQuery(this).val('');
																										}
																										else
																										{
																											var xml = responce;
																											//alert(responce);
																											jQuery(xml).find("picture").each(function()
																											{
																												if(jQuery(this).attr("error") == 0 )
																												{
																													removePicture( jQuery(this).attr("pos") );
																												}
																												else if( jQuery(this).attr("error") == 2 )
																													alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
																												else if( jQuery(this).attr("error") == 3 )
																													alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
																											});
																											
																											jQuery('#crt_pos').val('');
																											jQuery('#crt_path').val('');
																										}
																									}, 'html'
				);
			
			});
			
			
		});
		
		/*jQuery('#period_offer_calendar').DatePickerShow();		
		var crtVal = new Array();
		crtVal[0] = jQuery('#offer_datas').val();
		crtVal[1] = jQuery('#offer_datae').val();
		jQuery('#period_offer_calendar').DatePickerSetDate(crtVal);*/
		
		
		//if is error try to display all fields incomplete
		jQuery("select#room_details_id option[value='"+jQuery('#selected_OFFER_ROOM').val()+"']").attr("selected", "selected");
		jQuery("select#offer_type_discount_id option[value='"+jQuery('#selected_OFFER_TYPE_DISCOUNT').val()+"']").attr("selected", "selected");
		if( jQuery('#selected_OFFER_ROOM').val() > 0 )
		{
			jQuery("#div_offer_packages").show(1000);
			jQuery("#div_offer_arrival_options").show(1000);
			jQuery("#div_offer_discounts").show(1000);
			jQuery("#div_info_room_price_" 				+ jQuery('#selected_OFFER_ROOM').val() ).show( 1000 ); 
			jQuery("#div_offer_room_packages_" 			+ jQuery('#selected_OFFER_ROOM').val() ).show( 1000 ); 
			jQuery("#div_offer_room_arrival_options_" 	+ jQuery('#selected_OFFER_ROOM').val() ).show( 1000 ); 
		
			if( jQuery('#selected_OFFER_TYPE_DISCOUNT').val() > 0 )
			{
				jQuery("#div_offer_room_discount_" 	+ jQuery('#selected_OFFER_ROOM').val()  + "_" + jQuery('#selected_OFFER_TYPE_DISCOUNT').val() ).show( 1000 ); 
			}
		}
		//if is error try to display all fields incomplete
	});

	function addPicture(path, name)
	{
		var tb = document.getElementById('table_offer_pictures');
		if( tb==null )
		{
			alert('1Undefined table, contact administrator !');
		}
		
		var td1_new			= document.createElement('td');  
		td1_new.style.textAlign='left';
		var textarea_new 	= document.createElement('textarea');
		textarea_new.setAttribute("name","offer_picture_info[]");
		textarea_new.setAttribute("id","offer_picture_info");
		textarea_new.setAttribute("cols","50");
		textarea_new.setAttribute("rows","2");
		td1_new.appendChild(textarea_new);
		
		var td2_new			= document.createElement('td');  
		td2_new.style.textAlign='center';
		var img_new		 	= document.createElement('img');
		img_new.setAttribute('src', "<?php echo JURI::root().PATH_PICTURES?>" + path );
		img_new.setAttribute('class', 'img_picture_offer');
		td2_new.appendChild(img_new);
		var span_new		= document.createElement('span');
		span_new.innerHTML 	= "<BR>"+name;
		td2_new.appendChild(span_new);
		
		var input_new_1 		= document.createElement('input');
		input_new_1.setAttribute('type',		'hidden');
		input_new_1.setAttribute('name',		'offer_picture_enable[]');
		input_new_1.setAttribute('id',			'offer_picture_enable[]');
		input_new_1.setAttribute('value',		'1');
		td2_new.appendChild(input_new_1);
		
		var input_new_2		= document.createElement('input');
		input_new_2.setAttribute('type',		'hidden');
		input_new_2.setAttribute('name',		'offer_picture_path[]');
		input_new_2.setAttribute('id',			'offer_picture_path[]');
		input_new_2.setAttribute('value',		path);
		td2_new.appendChild(input_new_2);
		
		var td3_new			= document.createElement('td');  
		td3_new.style.textAlign='center';
		
		var img_del		 	= document.createElement('img');
		img_del.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>");
		img_del.setAttribute('class', 'btn_picture_delete');
		img_del.setAttribute('id', 	tb.rows.length);
		img_del.setAttribute('name', 'del_img_' + tb.rows.length);
		img_del.onmouseover  	= function(){ this.style.cursor='hand';this.style.cursor='pointer' };
		img_del.onmouseout 		= function(){ this.style.cursor='default' };
		img_del.onclick  		= function(){ 
											if( !confirm('<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE',true)?>' )) 
												return; 
											
											var row 		= jQuery(this).parents('tr:first');
											var row_idx 	= row.prevAll().length;
														
											jQuery('#crt_pos').val(row_idx);
											jQuery('#crt_path').val( path );
											jQuery('#btn_removefile').click();
									};
			
		td3_new.appendChild(img_del);
		
		var td4_new			= document.createElement('td');  
		td4_new.style.textAlign='center';
		var img_enable	 	= document.createElement('img');
		img_enable.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/checked.gif"?>");
		img_enable.setAttribute('class', 'btn_picture_status');
		img_enable.setAttribute('id', 	tb.rows.length);
		img_enable.setAttribute('name', 'enable_img_' + tb.rows.length);
		
		img_enable.onclick  		= function(){ 
													var form 		= document.adminForm;
													var v_status  	= null; 
													if( form.elements['offer_picture_enable[]'].length == null )
													{
														v_status  = form.elements['offer_picture_enable[]'];
													}
													else
													{
														pos = this.id;
														var tb = document.getElementById('table_offer_pictures');
														if( pos >= tb.rows.length )
															pos = tb.rows.length-1;
														v_status  = form.elements['offer_picture_enable[]'][ pos ];
													}
													
													if(v_status.value=='1')
													{
														jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/unchecked.gif"?>');
														v_status.value ='0';
													}
													else
													{
														jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/checked.gif"?>');
														v_status.value ='1';
													}
									};
		td4_new.appendChild(img_enable);
		
		
		var td5_new			= document.createElement('td');  
		td5_new.style.textAlign='center';
				
		td5_new.innerHTML	= 	'<span class=\'span_up\' onclick=\'var row = jQuery(this).parents("tr:first");  row.insertBefore(row.prev());\'><?php echo JText::_('LNG_STR_UP',true)?></span>'+
								'&nbsp;' +
								'<span class=\'span_down\' onclick=\'var row = jQuery(this).parents("tr:first"); row.insertAfter(row.next());\'><?php echo JText::_('LNG_STR_DOWN',true)?></span>';
		
		var tr_new = tb.insertRow(tb.rows.length);
		
		tr_new.appendChild(td1_new);
		tr_new.appendChild(td2_new);
		tr_new.appendChild(td3_new);
		tr_new.appendChild(td4_new);
		tr_new.appendChild(td5_new);
	}
	
	
	function removePicture(pos)
	{
		var tb = document.getElementById('table_offer_pictures');
		//alert(tb);
		if( tb==null )
		{
			alert('Undefined table, contact administrator !');
		}
		
		if( pos >= tb.rows.length )
			pos = tb.rows.length-1;
		tb.deleteRow( pos );
	
	}

	function disableAllControls(){
		jQuery("#offer-form :input").attr("disabled", true);
		jQuery("#offer-form :select").attr("disabled", true);
		jQuery("#offer-form :a").attr("href", "#");
	}
</script>
<form  action="index.php" method="post" name="adminForm" id="offer-form">
	
	<?php 
		   $options = array(
											    'onActive' => 'function(title, description){
											        description.setStyle("display", "block");
											        title.addClass("open").removeClass("closed");
											    }',
											    'onBackground' => 'function(title, description){
											        description.setStyle("display", "none");
											        title.addClass("closed").removeClass("open");
											    }',
											    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
											    'useCookie' => true, // this must not be a string. Don't use quotes.
					);
					
		  			if($this->item->state==1 && !isSuperUser(JFactory::getUser()->id)){
		  				echo "<div class='message'>".JText::_("LNG_OFEFR_LIVE_MODE_MESSAGE")."</div>";
		  			}
					echo JHtml::_('tabs.start', 'tab_room_edit', $options);
					echo JHtml::_('tabs.panel', JText::_('LNG_OFFER_DETAILS',true), 'tab1' );
						include(dirname(__FILE__).DS.'offerdetails.php');
					echo JHtml::_('tabs.panel', JText::_('LNG_ROOMS',true), 'tab2' );
						include(dirname(__FILE__).DS.'rooms.php');					
					echo JHtml::_('tabs.panel', JText::_('LNG_ROOM_DETAILS',true), 'tab3');
						include(dirname(__FILE__).DS.'roomdetails.php');
					echo JHtml::_('tabs.panel', JText::_('LNG_EXTRA_OPTIONS',true), 'tab4');
						include(dirname(__FILE__).DS.'extraoptions.php');
					echo JHtml::_('tabs.panel', JText::_('LNG_PICTURES'), 'tab5');
						include(dirname(__FILE__).DS.'pictures.php');
					echo JHtml::_('tabs.end');
	?>


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
				if (pressbutton == 'save' || pressbutton == 'apply') 
				{

					if( !validateField( form.offer_name, 'string', false, "<?php echo JText::_( 'LNG_PLEASE_INSERT_OFFER_NAME' ,true); ?>" ) )
					{
						$$('dt.tabs')[0].fireEvent('click');
						return false;
					}
					if( tinyMCE.get('offer_description').getContent() =='' )
					{
						alert("<?php echo JText::_( 'LNG_PLEASE_INSERT_OFFER_DESCRIPTION' ,true); ?>" );
						$$('dt.tabs')[0].fireEvent('click');
						return false;
					}

					var offerDaySelected = false;
					jQuery(".offer-day").each(function(){
						if(jQuery(this).attr('checked'))
							offerDaySelected = true;
					});

					if(!offerDaySelected){
						alert("<?php echo JText::_( 'LNG_PLEASE_SELECT_ONE_DAY' ,true); ?>" );
						$$('dt.tabs')[0].fireEvent('click');
						return false;
					}
					
					if(jQuery("#room_ids :selected").length == 0){
						alert("<?php echo JText::_( 'LNG_PLEASE_SELECT_ROOM_TYPES' ,true); ?>" );
						$$('dt.tabs')[1].fireEvent('click');
						return false;
					}

					/*
					if(!String.prototype.startsWith){
					    String.prototype.startsWith = function (str) {
					        return !this.indexOf(str);
					    }
					}

					
					var offerPriceSelected = false;				
					jQuery(".type_price_sel").each(function(){
						
						var selectedPriceType = jQuery(this).attr('name');
						var selectedId = parseInt(selectedPriceType.substr(-2));
						//alert(jQuery("#room_ids option[value='"+selectedId+"']").attr('selected'));
						if(!jQuery("#room_ids option[value='"+selectedId+"']").attr('selected')){
							return true;
						}
						
						if(selectedPriceType.startsWith("link_price_day_by_day")){
							jQuery(".offer-price"+selectedId).each(function(){
								//alert(jQuery(this).attr('name'));
								offerPriceSelected = true;
								if(isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val())==0){
									offerPriceSelected = false;
									//alert(parseInt(jQuery(this).val()));
								}
							});
						}
						
						if(selectedPriceType.startsWith("link_price_every_day")){
							jQuery(".offer-price-days"+selectedId).each(function(){
								offerPriceSelected = true;
								//alert(jQuery(this).attr('name'));
								if(isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val())==0){
									offerPriceSelected = false;
									//alert(parseInt(jQuery(this).val()));
								}
							});
						}
						if(selectedPriceType.startsWith("link_price_midweek_weekend")){
							jQuery(".offer-price-week"+selectedId).each(function(){
								offerPriceSelected = true;
								//alert(jQuery(this).attr('name'));
								if(isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val())==0){
									offerPriceSelected = false;
									//alert(parseInt(jQuery(this).val()));
								}
							});
						}
					});


					if(!offerPriceSelected){
						alert("<?php echo JText::_( 'LNG_INVALID_PRICE_SETUP' ,true); ?>" );
						$$('dt.tabs')[2].fireEvent('click');
						return false;
					} */
				
					submitform( pressbutton );
					return;
				} else {
					submitform( pressbutton );
				}
			}

		function clearSelectedItem(elementId){
			jQuery("#"+elementId+" option:selected").removeAttr("selected");
		}

		function updateStatus(){
			<?php
				foreach( $this->item->itemRooms as $valueRoom )
				{
			?>
			if(jQuery("input[name='price_type_<?php echo $valueRoom->room_id?>']:checked").val()==1){
				jQuery("#single-supplement-container-<?php echo $valueRoom->room_id?>").show();
				jQuery("#single-discount-container-<?php echo $valueRoom->room_id?>").hide();
			}else{
				jQuery("#single-supplement-container-<?php echo $valueRoom->room_id?>").hide();
				jQuery("#single-discount-container-<?php echo $valueRoom->room_id?>").show();
			}

			<?php } ?>
		}
	</script>
	
	<input type="hidden" name="option" value="<?php echo getBookingExtName() ?>" />
	<input type="hidden" name="task" value="" />
	
	<input type="hidden" name="is_error" value="1" />
	<input type="hidden" name="selected_TAB" id='selected_TAB' value="0" />
	<input type="hidden" name="selected_OFFER_ROOM" id='selected_OFFER_ROOM' value="0" />
	<input type="hidden" name="selected_OFFER_TYPE_DISCOUNT" id='selected_OFFER_TYPE_DISCOUNT' value="0" />
	<input type="hidden" name="offer_id" id="offer_id" value="<?php echo $this->item->offer_id ?>" />
	<input type="hidden" name="hotel_id" value="<?php echo $this->hotel_id ?>" />
	<input type="hidden" name="controller" value="manageoffers" />
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

<?php
}
?>

<div id="showThemesNewFrm" style="display:none;">
  		<div id="popup_container">
    <!--Content area starts-->

    		<div class="head">
      		    <div class="head_inner">
               <h2> <?php echo JText::_('LNG_MANAGE_THEMES'); ?></h2>
               <a href="#" class="cancel_btn" onclick="closePopup(); return false;"><span class="cancel_icon">&nbsp;</span><?php echo JText::_('LNG_CANCEL',true); ?></a></div>
            </div>
            <div class="content">
                    <div class="descriptions" >

                       <div id="content_section_tab_data1">
                       	<span id="frm_error_msg_theme" class="text_error" style="display: none;"></span> 
						<div class="row" id="theme-container">
						</div>
						 
					 	<div class="option-row">
							<a href="javascript:" onclick="addNewTheme(0,'')"><?php echo JText::_('LNG_ADD_NEW_THEME',true); ?></a>
						</div>
						<div class="proceed_row">
                           <!--button sec starts-->
                              <button name="btnSave" id="btnSave" onclick="saveThemes(this.form);" type="submit" class="submit">    
                                     <span><span>Save</span></span>
                              </button>
                              <input value="Cancel" class="cancel" name="btnCancel" id="btnCancel" onclick="closePopup(); return false;" type="button">
                          </div>
                          <!--button sec ends-->
                        </div>
                        <div class="buttom_sec" id="frmThemesFormSubmitWait" style="display: none;"> <span class="error_msg" style="background-image: none; color: rgb(0, 0, 0) ! important;">Please wait...</span> </div>
            </div>
          </div>
          </div>
     </div>        