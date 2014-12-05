<?php defined('_JEXEC') or die('Restricted access'); 
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task != 'hotels.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true,true);?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotels');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left fltlft">
				<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL',true); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC',true); ?>" />
				<?php if(!JHotelUtil::isJoomla3()) {?>
					<button class="btn" type="submit">Search</button>
					<button onclick="document.id('filter_search').value='';this.form.submit();" type="button">Clear</button>
				<?php } ?>
			</div>
			<?php if(JHotelUtil::isJoomla3()) {?>
				<div class="btn-group pull-left hidden-phone">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT',true); ?>"><i class="icon-search"></i></button>
					<button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR',true); ?>"><i class="icon-remove"></i></button>
				</div>
				
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC',true); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php } ?>
			
			<div class="filter-select pull-right fltrt btn-group">
				<select name="filter_accommodationtypeId" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_TYPE',true);?></option>
					<?php echo JHtml::_('select.options', $this->accomodationTypes, 'value', 'text', $this->state->get('filter.accommodationtypeId'));?>
				</select>
			
				<select name="filter_status_id" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_STATUS',true);?></option>
					<?php echo JHtml::_('select.options', $this->statuses, 'value', 'text', $this->state->get('filter.status_id'));?>
				</select>
			</div>
		</div>
	</div>
	
	<div class="clr clearfix"> </div>
			
	<table class="table table-striped adminlist"  id="itemList">
		<thead>
			 <tr>
				<th width='1%'>&nbsp;</th>
				<th width="1%" class="hidden-phone">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width='20%' align=center>
					<B><?php echo JHtml::_('grid.sort', 'LNG_NAME', 'h.hotel_name', $listDirn, $listOrder); ?></B>
				</th>
				<?php 
				if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
				?>
					<th width='1%' align=center>
						<B><?php echo JHtml::_('grid.sort', 'LNG_FEATURED', 'h.featured', $listDirn, $listOrder); ?></B>
					</th>
				<?php 
					}
				?>
				<th width='8%' align=center>
				<B><?php echo JHtml::_('grid.sort', 'LNG_COUNTRY', 'hc.country_name', $listDirn, $listOrder); ?></B>
				</th>
				<th width='8%' align=center>
					<B><?php echo JHtml::_('grid.sort', 'LNG_CITY', 'h.hotel_city', $listDirn, $listOrder); ?></B>
				</th>
				<th width='8%' align=center>
					<B><?php echo JHtml::_('grid.sort', 'LNG_PHONE', 'h.email', $listDirn, $listOrder); ?></B>
				</th>
				<th width='8%' align=center>
					<B><?php echo JHtml::_('grid.sort', 'LNG_EMAIL', 'h.email', $listDirn, $listOrder); ?></B>
				</th>
				<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>
				<th width='1%' align=center>
					<B>
					   <?php echo JHtml::_('grid.sort', 'LNG_AVAILABLE', 'h.is_available', $listDirn, $listOrder); ?>
				    </B>
				</th>
				<?php } ?>
				<th width='8%' align=center> </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
				<?php
				$nrcrt = 1;
				$i=-1;
				if(count($this->items))
				foreach($this->items as $hotel)
				{
					$i++;
					?>
					<TR class="row<?php echo $i%2 ?>"
						onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
						onmouseout="this.style.cursor='default'">
						<TD align=center><?php echo $nrcrt++?></TD>
						<TD align=center>
							<?php echo JHtml::_('grid.id', $i, $hotel->hotel_id); ?>
						</TD>
						<TD align=left><a
							href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=hotel.edit&cid[]='. $hotel->hotel_id )?>'
							title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"> <B><?php echo stripslashes($hotel->hotel_name)?>
							</B>
						</a>
						</TD>
						<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>
						<td align=center><img border=1
							src="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($hotel->featured==false? "unchecked.gif" : "checked.gif")?>"
							onclick="	
													document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=hotels.changeFeaturedState&hotel_id='.$hotel->hotel_id  )?> '
												" />
						</td>
						<?php 
							}
						?>
						<TD align=center>
							<?php echo $hotel->country_name?>
						</TD>
						<TD align=center>
							<?php echo $hotel->hotel_city?>
						</TD>
						<TD align=center>
							<?php echo $hotel->hotel_phone?>
						</TD>
						<TD align=center>
							<?php echo $hotel->email?>
						</TD>
						<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>
						<td align=center><img border=1
							src="<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/".($hotel->is_available==false? "unchecked.gif" : "checked.gif")?>"
							onclick="	
													document.location.href = '<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=hotels.state&hotel_id='. $hotel->hotel_id )?> '
												" />
						</td>
						<?php 
							}
						?>
						<TD align="center" nowrap="nowrap">
						<?php 
							if (checkUserAccess(JFactory::getUser()->id,"manage_featured_hotels")){
						?>
							<?php if (checkUserAccess(JFactory::getUser()->id,"availability_section")){ ?>
								<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=availability&hotel_id='. $hotel->hotel_id )?>'
									title="<?php echo JText::_('LNG_AVAILABILITY')?>"
								> 
									<b><?php echo JText::_('LNG_AVAILABILITY')?></b>
								</a>
								 |
							<?php } ?>
							<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=rooms&hotel_id='. $hotel->hotel_id )?>'
								title="<?php echo JText::_('LNG_ROOMS',true)?>"
							> 
								<b><?php echo JText::_('LNG_ROOMS',true)?></b>
							</a>
							<?php if(PROFESSIONAL_VERSION==1){?>
							    |
								<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=offers&hotel_id='. $hotel->hotel_id )?>'
									title="<?php echo JText::_('LNG_OFFERS',true)?>"
								> 
									<b><?php echo JText::_('LNG_OFFERS',true)?></b>
								</a>
								|						
								<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=extraoptions&hotel_id='. $hotel->hotel_id )?>'
									title="<?php echo JText::_('LNG_EXTRAS',true)?>"
								> 
									<b><?php echo JText::_('LNG_EXTRAS',true)?></b>
								</a>
							<?php }?>
							|
							<a	href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&view=reservations&filter_hotel_id='. $hotel->hotel_id )?>'
								title="<?php echo JText::_('LNG_RESERVATIONS',true); ?>"
							> 
								<b><?php echo JText::_('LNG_RESERVATIONS',true)?></b>
							</a>
						<?php } ?>
						</td>
						
	
					</tr>
					<?php
					}
					?>
				</tbody>
		
			</table>
	<input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" /> 
	<input type="hidden" name="task" value="viewHotels" /> 
	<input type="hidden" name="hotel_id" value="" /> 
	<input type="hidden" name="controller"	value="<?php echo JRequest::getCmd('controller', 'J-HotelReservation')?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?> 
</form>

