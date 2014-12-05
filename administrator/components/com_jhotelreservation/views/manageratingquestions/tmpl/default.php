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
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<TABLE class="adminlist" >
			<thead>
				<th width='1%'>#</th>
				<th width='1%'>&nbsp;</th>
				<th width='80%' align=center ><B><?php echo JText::_('LNG_RATING_QUESTION',true); ?></B></th>
				<th width='1%' align=center ><B><?php echo JText::_('LNG_ORDER',true); ?></B></th>
			</thead>
			<tbody>
			<?php
			$nrcrt = 1;
			//if(0)
			for($i = 0; $i <  count( $this->items ); $i++)
			{
				$reviewQuestion = $this->items[$i]; 

			?>
			<TR class="row<?php echo $i%2 ?>"
				onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
				onmouseout	=	"this.style.cursor='default'"
			>
				<TD align=center><?php echo $nrcrt++?></TD>
				<TD align=center>
					 <input type="checkbox" name="review_question_id[]"  id="boxchecked" 
						value="<?php echo $reviewQuestion->review_question_id ?>" 
						onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
					/>
					
				</TD>
				<TD align=left style="padding-left:50px;">	
					<a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=manageratingquestions&task=managehotelratings.editratingquestion&review_question_id[]='. $reviewQuestion->review_question_id )?>' title		= 	"<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>">			
						<B><?php echo $reviewQuestion->review_question_desc ?></B>
					</a>
				</TD>
				<TD width='1%' valign=top align=center nowrap>
				<B>
				<span
				name="span_up_<?php echo $reviewQuestion->review_question_id?>"
				id	="span_up_<?php echo $reviewQuestion->review_question_id?>"
				class= "span_up"
				onclick='
							jQuery.ajax({
								url		: "<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&view=manageratingquestions&task=managehotelratings.changequestionorder&tip_order=up&review_question_id=<?php echo $reviewQuestion->review_question_id?>",
								context	: document.body,
								success	: function( responce ){
											if(responce.search("error")<0){
													var row = jQuery("#span_up_<?php echo $reviewQuestion->review_question_id?>").parents("tr:first"); 
													row.insertBefore(row.prev());
												}
										}
							});
															'
				>
				<?php echo JText::_('LNG_UP',true)?>
												</span>
												&nbsp;
												<span 
													name="span_down_<?php echo $reviewQuestion->review_question_id?>"
													id	="span_down_<?php echo $reviewQuestion->review_question_id?>"
													class="span_down"
													onclick='
																// var row = jQuery(this).parents("tr:first"); 
																								// row.insertAfter(row.next());
																				
																	jQuery.ajax({
																		url		: "<?php echo JURI::base()?>?index.php&option=<?php echo getBookingExtName()?>&view=manageratingquestions&task=managehotelratings.changequestionorder&tip_order=down&review_question_id=<?php echo $reviewQuestion->review_question_id?>",
																		context	: document.body,
																		success	: function(responce){
																						if(responce.search("error")<0){
																							var row = jQuery("#span_down_<?php echo $reviewQuestion->review_question_id?>").parents("tr:first"); 
																							row.insertAfter(row.next());
																						}
																				  }
																	});
															'
												>
													<?php echo JText::_('LNG_DOWN',true)?>
												</span>
											</B>
										</TD>
			<?php
			}
			?>
			</tbody>
		</TABLE>
	</div>
	<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="task" value="managehotelratings" />
	<input type="hidden" name="controller" value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<?php echo JHTML::_( 'form.token' ); ?> 
	
</form>



