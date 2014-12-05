<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="hotel_content">


	<form action="index.php" method="post" name="adminForm" id="adminForm">
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
			
				if($this->item->hotel_state==1 && !isSuperUser(JFactory::getUser()->id)){
					echo "<div class='message'>".JText::_("LNG_HOTEL_LIVE_MODE_MESSAGE")."</div>";
				}
				
				echo JHtml::_('tabs.start', 'tab_hotel_id', $options);
				
				echo JHtml::_('tabs.panel', JText::_('LNG_GENERAL_INFORMATION'), 'panel_1_id');
				require_once 'general.php';
				
				echo JHtml::_('tabs.panel', JText::_('LNG_CHARACTERISTICS'), 'panel_2_id');
				require_once 'characteristics.php';
				
				echo JHtml::_('tabs.panel', JText::_('LNG_IMPORTANT_INFORMATION'), 'panel_3_id');
				require_once 'informations.php';
					
				echo JHtml::_('tabs.panel', JText::_('LNG_PICTURES'), 'panel_4_id');
				require_once 'pictures.php';
				
				if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
					echo JHtml::_('tabs.panel', JText::_('LNG_EXTRA_INFO'), 'panel_5_id');
					require_once 'extrainfo.php';
				}
				
				if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){
					echo JHtml::_('tabs.panel', JText::_('LNG_CHANNEL_MANAGER'), 'panel_6_id');
					require_once 'channelmanagers.php';
				}
				
				echo JHtml::_('tabs.panel', JText::_('LNG_CONTACT_INFORMATION'), 'panel_7_id');
				require_once 'contact.php';
						
				echo JHtml::_('tabs.end');
				
			?>
			
		<div class="clr"></div>
		
		<input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" /> 
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="cid" id="cid" value="<?php echo $this->item->hotel_id ?>" /> 
		<input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo $this->item->hotel_id ?>" /> 
		<input type="hidden" name="controller" value="hotels" />
		<?php echo JHTML::_( 'form.token' ); ?> 
	</form>
</div>
<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{	
			
			if (pressbutton == 'hotel.save' || pressbutton == 'hotel.apply') 
			{
				jQuery('#adminForm').validationEngine('attach');				
				jQuery('#adminForm').validationEngine('validate');

				if(jQuery('#hotel_name').validationEngine('validate')){
					jQuery('#hotel_name').focus();
					$$('dt.tabs')[0].fireEvent('click');
					return false; 
				}
				
				if(jQuery('#email').validationEngine('validate')){
					$$('dt.tabs')[0].fireEvent('click');
					jQuery('#email').focus();
					return false; 
				}
				
				if(jQuery('#country').validationEngine('validate')){
					$$('dt.tabs')[0].fireEvent('click');
					jQuery('#country_id').focus();
					return false; 
				}

				if(jQuery('#currency_id').validationEngine('validate')){
					$$('dt.tabs')[0].fireEvent('click');
					jQuery('#currency_id').focus();
					return false; 
					
				}
				if(jQuery('#locality').validationEngine('validate')){
					$$('dt.tabs')[0].fireEvent('click');
					jQuery('#hotel_city').focus();
					return false; 
				}
				if(jQuery('#route').validationEngine('validate')){
					$$('dt.tabs')[0].fireEvent('click');
					jQuery('#hotel_address').focus();
					return false; 
					
				}
								
				if(jQuery('#city_tax').validationEngine('validate')){
					$$('dt.tabs')[2].fireEvent('click');
					jQuery('#city_tax').focus();
					return false; 
					
				}
				
				if(jQuery('#number_of_rooms').validationEngine('validate')){
					$$('dt.tabs')[2].fireEvent('click');
					jQuery('#number_of_rooms').focus();
					return false; 
					
				}
				if(jQuery('#children_category').validationEngine('validate')){
					$$('dt.tabs')[2].fireEvent('click');
					jQuery('#children_category').focus();
					return false; 
				}

				<?php if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){ ?>
					$$('dt.tabs')[4].fireEvent('click');
					
					if(jQuery('#commission').validationEngine('validate')){
						$$('dt.tabs')[4].fireEvent('click');
						jQuery('#commission').focus();					
						return false; 
					}
				<?php }?>
				$$('dt.tabs')[0].fireEvent('click');
				jQuery('#hotel_name').focus();
				submitform( pressbutton );
				return;
			} else {
				submitform( pressbutton );
			}
		}
		
		var deleteImagePath = "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>";
		
		jQuery("select#facilities").selectList({ 
					 sort: true,
					 classPrefix: 'facilities',
					 onAdd: function (select, value, text) {
						    if(value=='new'){
							    return true;
						    }
					 },
					 onRemove: function (select, value, text) {
						 jQuery('select#facilities option[value='+value+']').removeAttr('selected');	
					 }

		 		});
		
		jQuery("select#accommodationtypes").selectList({ 
			 sort: true,
			 classPrefix: 'accommodationtypes',
			 onAdd: function (select, value, text) {
				    if(value=='new'){
					    return true;
				    }
			 },
			 onRemove: function (select, value, text) {
				 jQuery('select#accommodationtypes option[value='+value+']').removeAttr('selected');	
			 }

		});

		jQuery("select#environments").selectList({ 
			 sort: true,
			 classPrefix: 'environments',
			 onAdd: function (select, value, text) {
				    if(value=='new'){
					    return true;
				    }
			 },
			 onRemove: function (select, value, text) {
				 jQuery('select#environments option[value='+value+']').removeAttr('selected');	
			 }

		});
		
		jQuery("select#regions").selectList({ 
			 sort: true,
			 classPrefix: 'regions',
			 onAdd: function (select, value, text) {
				    if(value=='new'){
					    return true;
				    }
			 },
			 onRemove: function (select, value, text) {
				 jQuery('select#regions option[value='+value+']').removeAttr('selected');	
			 }

		});
		
		jQuery('#dates_hotel_calendar').DatePicker(
				{
					flat: 		true,
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
												crtVal = (jQuery("#ignored_dates").val( )).split(',');
												jQuery('#dates_hotel_calendar').DatePickerClear();
												jQuery('#dates_hotel_calendar').DatePickerSetDate(crtVal);
											},
					onHide: function()
											{
												
												return true;
											},

					onChange: function(formated, dates){
														jQuery("#ignored_dates").val( formated.join(',') );
													}

				}
			);
			
	jQuery(function()
	{
		jQuery('#uploadedfile').change(function() {
			jQuery("#adminForm").validationEngine('detach');
			var fisRe 	= /^.+\.(jpg|bmp|gif|png)$/i;
			var path = jQuery('#uploadedfile').val();
			if (path.search(fisRe) == -1)
			{   
				alert(' JPG, BMP, GIF, PNG only!');
				return false;
			}  
			jQuery(this).upload('<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/upload.php?t=<?php echo strtotime('now')?>&_root_app=<?php echo urlencode(JPATH_ROOT)?>&_target=<?php echo urlencode(PATH_HOTEL_PICTURES.($this->item->hotel_id+0).'/')?>', 
																								function(responce){
																									//alert(responce);
																									if( responce =='' )
																									{
																										alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
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
																												addPicture(
																															"<?php echo "/hotels/".($this->item->hotel_id+0).'/'?>" + jQuery(this).attr("path"),
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

	var crtVal = new Array();
	crtVal = (jQuery("#ignored_dates").val( )).split(',');
	jQuery('#dates_hotel_calendar').DatePickerClear();
	jQuery('#dates_hotel_calendar').DatePickerSetDate(crtVal);
	jQuery('#dates_hotel_calendar').DatePickerShow();
	
	function clickBtnIgnoreDays()
	{
		jQuery('#dates_hotel_calendar').DatePickerHide();
		jQuery('#hotel_availability_dates').append( jQuery('#div_calendar') );
		jQuery('#dates_hotel_calendar').DatePickerShow();
		this.className = 'span_ignored_days_sel';
	}
	
	function addPicture(path, name)
	{
		var tb = document.getElementById('table_hotel_pictures');
		if( tb==null )
		{
			alert('Undefined table, contact administrator !');
		}
		
		var td1_new			= document.createElement('td');  
		td1_new.style.textAlign='left';
		var textarea_new 	= document.createElement('textarea');
		textarea_new.setAttribute("name","hotel_picture_info[]");
		textarea_new.setAttribute("id","hotel_picture_info");
		textarea_new.setAttribute("cols","50");
		textarea_new.setAttribute("rows","2");
		td1_new.appendChild(textarea_new);
		
		var td2_new			= document.createElement('td');  
		td2_new.style.textAlign='center';
		var img_new		 	= document.createElement('img');
		img_new.setAttribute('src', "<?php echo JURI::root().PATH_PICTURES ?>" + path );
		img_new.setAttribute('class', 'img_picture_hotel');
		td2_new.appendChild(img_new);
		var span_new		= document.createElement('span');
		span_new.innerHTML 	= "<BR>"+name;
		td2_new.appendChild(span_new);
		
		var input_new_1 		= document.createElement('input');
		input_new_1.setAttribute('type',		'hidden');
		input_new_1.setAttribute('name',		'hotel_picture_enable[]');
		input_new_1.setAttribute('id',			'hotel_picture_enable[]');
		input_new_1.setAttribute('value',		'1');
		td2_new.appendChild(input_new_1);
		
		var input_new_2		= document.createElement('input');
		input_new_2.setAttribute('type',		'hidden');
		input_new_2.setAttribute('name',		'hotel_picture_path[]');
		input_new_2.setAttribute('id',			'hotel_picture_path[]');
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
											if( !confirm("<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE',true)?>" )) 
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
													if( form.elements['hotel_picture_enable[]'].length == null )
													{
														v_status  = form.elements['hotel_picture_enable[]'];
													}
													else
													{
														pos = this.id;
														var tb = document.getElementById('table_hotel_pictures');
														if( pos >= tb.rows.length )
															pos = tb.rows.length-1;
														v_status  = form.elements['hotel_picture_enable[]'][ pos ];
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
				
		td5_new.innerHTML	= 	"<span class=\'span_up\' onclick=\'var row = jQuery(this).parents(\"tr:first\");  row.insertBefore(row.prev());\'><?php echo JText::_('LNG_STR_UP',true)?></span>"+
								'&nbsp;' +
								"<span class=\'span_down\' onclick=\'var row = jQuery(this).parents(\"tr:first\"); row.insertAfter(row.next());\'><?php echo JText::_('LNG_STR_DOWN',true)?></span>";
		
		var tr_new = tb.insertRow(tb.rows.length);
		
		tr_new.appendChild(td1_new);
		tr_new.appendChild(td2_new);
		tr_new.appendChild(td3_new);
		tr_new.appendChild(td4_new);
		tr_new.appendChild(td5_new);
	}
	
	
	function removePicture(pos)
	{
		var tb = document.getElementById('table_hotel_pictures');
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
		jQuery("#hotel_content :input").attr("readonly", "readonly");
		jQuery("#hotel_content :select").attr("readonly", "readonly");
		jQuery("#hotel_content :a").attr("href", "#");
	}

	<?php 
	if($this->item->hotel_state==1 && !isSuperUser(JFactory::getUser()->id)){
		echo "disableAllControls()";
	}
	?>
	</script>

</form>



