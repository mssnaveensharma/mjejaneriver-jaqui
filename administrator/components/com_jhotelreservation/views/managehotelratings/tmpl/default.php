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

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<div id="boxes">
				<div style='text-align:left'>
					<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
					<select name='hotel_id' id='hotel_id' style='width:300px'
						onchange ='
									var form 	= document.adminForm; 
									form.elements["view"].value = "managehotelratings";
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
				<div>
					<?php echo JText::_('LNG_HOTEL_RATING_INFO',true); ?>: <strong><?php echo number_format($this->hotelInfo->hotel_rating_score,2); ?> </strong>
				</div>
				
				<TABLE class="table table-striped adminlist" >
					<thead>
						<th width='1%'>#</th>
						<th width='10%' align=center><B><?php echo JText::_('LNG_CLIENT_NAME',true); ?></B></th>
						<th width='20%' align=center ><B><?php echo JText::_('LNG_REVIEW_SHORT_DESC',true); ?></B></th>
						<th width='30%' align=center><B><?php echo JText::_('LNG_REVIEW_REMARKS',true); ?></B></th>
						<th width='30%' align=center><B><?php echo JText::_('LNG_VIEW_REVIEW',true); ?></B></th>
						<th width='10%' align=center><B><?php echo JText::_('LNG_PUBLISHED',true); ?></B></th>
					</thead>
					<tbody>
					<?php
					$nrcrt = 1;
					//if(0)
					for($i = 0; $i <  count( $this->items ); $i++)
					{
						$hotelReview = $this->items[$i]; 

					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout	=	"this.style.cursor='default'"
					>
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=left>
							
							<B><?php echo $hotelReview->clientsName?></B>
								
							
						</TD>
						<TD align=left><?php echo $hotelReview->review_short_description?></TD>
						<TD align=center><?php echo $hotelReview->review_remarks?></TD>
						<TD align=center>
								<div>
									<a href='#dialog_<?php echo $hotelReview->review_id?>' id="<?php echo $hotelReview->review_id?>" name='modal'>
										<?php echo JText::_('LNG_VIEW',true); ?> 
										<img border= 1 src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/addreservations_16_16_icon.gif"?>"/>
									</a>
								</div>
								<div id='dialog_<?php echo $hotelReview->review_id?>' class='window' width="100%">
									<div class='info'>
										<table width="100%">
											<tr>
												<td style="background-color:#FFFFFF">
													<SPAN class='title_ID'>
										 				 <?php echo JText::_('LNG_HOTEL_REVIEW',true); ?>
													</SPAN>
												</td>
												<td align="right"  style="background-color:#FFFFFF">
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Close' class="btn_normal" />
												</td>
											</tr>
											
										</table>

										
										<div id="reviewContent_<?php echo $hotelReview->review_id?>">
										<br><br><br><?php echo JText::_('LNG_LOADING',true); ?>
										<br><br><br>
										</div>
									</div>
								</div>
							
							
						</TD>
						<TD align=center>
							<img border= 1 
								src ="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($hotelReview->published==0? "unchecked.gif" : "checked.gif")?>" 
								onclick	=	"	
												document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=managehotelratings&task=managehotelratings.changeState&hotel_id='.$this->hotel_id.'&review_id='. $hotelReview->review_id )?>'
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
	<div id="mask"></div>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="managehotelratings" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
		<input type="hidden" name="view" value="managehotelratings" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	<script language="javascript" type="text/javascript">
	jQuery(document).ready(function() {	
		jQuery('.window').hide();
		//select all the a tag with name equal to modal
		jQuery('a[name=modal]').click(function(e) {
			//Cancel the link behavior
			e.preventDefault();
			//Get the A tag
			var id = jQuery(this).attr('href');	
			var reviewID = jQuery(this).attr('id');

			var fieldName = 'reviewContent_'+reviewID;
			var siteRoot = '<?php echo JURI::root();?>';
			var compName = '<?php echo getBookingExtName();?>';
			var url = siteRoot+'index.php?option='+compName+'&task=hotelratings.printRating&view=hotelratings&review_id='+reviewID;
			getData(fieldName,url);
		
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
		jQuery('.window .btn_normal').click(function (e) {
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

	
		getData = function(fieldName,url,inputParams) {
			jQuery.ajax({
			    type: "POST",  
			    url: url,
			    data: inputParams,
			    dataType: "html",
			    cache: false,
			    success: function(data) {
					try {
						jQuery('div[id=' + fieldName + ']').html(data);
					} catch(err) {
						alert(err);
					};
			    }
			})
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



