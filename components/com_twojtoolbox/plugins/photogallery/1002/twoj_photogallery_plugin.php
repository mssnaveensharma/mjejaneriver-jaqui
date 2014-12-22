<?php
/**
* @package 2JToolBox 2J Photo Gallery
* @Copyright (C) 2014 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.0 $
**/

JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/tables');

defined('_JEXEC') or die;

class TwoJToolBoxPhotoGallery extends TwoJToolBoxPlugin{
	protected $uniqueId = 0;
	
	protected $css_list=array( 'mg', 'popup', 'button'	);
	protected $js_list=array( 'mg.plugin', 'mg.load', 'mg', 'popup.mouse', 'popup.raf', 'popup', 'button');
	
	protected $_db = '';
	
	protected $galleryArray = array();
	protected $galleryId 	= array();
	protected $galleryName 	= array();
	
	protected $totalItem = 0;
	protected $items = 0;
	protected $countPagination = 0;
	protected $onlyImagesLoad = 0;
	
	protected $catCoutArray=array();
	protected $itemsIdArray=array();
	protected $twojLoadArray=array();
	
	protected $twojCategory = 0;
	
	protected $menu = 0;
	protected $menuColor = '';
	protected $menuStyle = '';
	
	protected $javaAcriptAddon 	= '';
	protected $javaScriptStyles = array();
	
	protected $galleryStyle = '';
	protected $lightboxStyle = '';
	
	
	
	public function includeLib(){	
		$this->menu = $this->getInt('menu', 0);
		if( $this->menu ){
			$this->css_list[] = 'menu';
			$this->menuStyle = $this->getString('menuStyle', 'default');
			$this->menuColor = $this->getString('menuColor', 'red');
			$this->css_list[] = 'menu/'.$this->menuStyle.'/2j.menu.'.$this->menuStyle.'.'.$this->menuColor;
			$this->css_list[] = '@@ROOT@@fa.new';
			$this->js_list[] = 'menu';
		}

		$this->lightboxStyle = $this->getString('lightboxStyle', 'dark');
		$this->css_list[] = 'popup.'.$this->lightboxStyle;
		if( $this->onlyImagesLoad = JRequest::getInt('only-images-load', 0) ){
			$this->css_list = array();
			$this->js_list = array();
		}
		
		parent::includeLib();
	}
	
	
	public function getElement(){ 
		$this->_db	= JFactory::getDBO();
		$app 		= JFactory::getApplication();
		
		$return_text = '';
		
		$generet_big_img_url 	= $this->getUrlResize('big_');
		$generet_thumb_img_url 	= $this->getUrlResize('thumb_');
		
		$this->uniqueId = JRequest::getInt('unique-id', 0);
		if( !$this->uniqueId ){
			$this->uniqueId = $this->getuniqueid();
		}
		
		$this->countPagination = $this->getInt('countPagination');
		
		$modeReturn = (bool) ( $this->getInt('orderby') >  1) ;
		$rows = array();
		
		if( $this->id == -1 ){
			$this->showPagination = 0;
			$rows = $this->loadDemo();
			$generet_big_img_url .= '&ems_root=1';
			$generet_thumb_img_url .= '&ems_root=1';
		} else {
			$this->twojCategory = JRequest::getInt('twoj-category', 0);
			if($this->twojCategory){
				$rows = $this->getElementItems( $this->twojCategory );
			} else {
	
	//getChildrenId   build array gallery from current (id)
	//getElementItems  get listing items   $id- single or array ; $mode-  1-array;  0 -single 
	//getGalleryItem  Came on array and exec getElementItems
	//getGalleryArray  get children gallery

				$this->galleryId[] = $this->id;

				if(!$modeReturn) $rows = $this->getElementItems( $this->id );

				if( $this->galleryArray = $this->getGalleryArray($this->id) ){
					if( $modeReturn ){
							$rows = $this->getElementItems( $this->galleryId, $modeReturn );
					}
					if( !$modeReturn ){
						$returnGalleryData = $this->getGalleryItem( $this->galleryArray, $modeReturn );
						if( is_array($returnGalleryData)) $rows = array_merge((array)$rows, (array)$returnGalleryData);
					}
				}elseif($modeReturn){
					$rows = $this->getElementItems( $this->galleryId, $modeReturn );
				}
			}
			$this->totalItem = count($rows);
			
			if( !$this->onlyImagesLoad ){
				
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					if( $row->catid ){
						if( isset($this->catCoutArray[$row->catid]) ) ++$this->catCoutArray[$row->catid];
							else $this->catCoutArray[$row->catid] = 1;
					}
				}
				
				$this->catCoutArray['0'] = count($rows);
			} else {
				$this->twojLoadArray = JRequest::getVar( 'twoj-load', array(), '', 'array' );
			}
			
			if( count($rows) && count($this->twojLoadArray) ){
				foreach ($rows as $key => $value){
					if( in_array($value->id, $this->twojLoadArray) ) unset($rows[$key]);
				}
			}
			
			if( $this->getInt( 'orderby' ) == 1 ) $rows = array_reverse ($rows);
			$rows = array_splice ( $rows, 0, $this->countPagination);
		}
		
		$thumb_width_set = $this->getInt('thumb_width', 240);
		$thumb_height_set = $this->getInt('thumb_height', 120);
		
		$reSize = new TwoJToolBoxReSize( $thumb_width_set, $thumb_height_set);
		
		$thumbsStyle 		= $this->getInt('thumbsStyle');
		$thumbsHover 		= $this->getInt('thumbsHover');
		$thumbsHoverClick 	= $this->getInt('thumbsHoverClick');
		if($thumbsHover==2) $thumbsHoverClick = 2;
		
		$thumbsHoverText	= $this->getInt('thumbsHoverText');
		
		if ( count( $rows ) ){
			$image_listing = ''; 
			foreach ($rows as $row){
				
				//if( !$this->onlyImagesLoad ) 
				$this->itemsIdArray[] = $row->id;
				
				$bgColor='mega-white';
				
				if( $thumbsStyle==1 || $thumbsStyle==2 	|| $thumbsHover==1 ){
					$params = new JRegistry;
					$params->loadString($row->params);
					if( $params->get('bgColor') ) $bgColor = 'mega-'.$params->get('bgColor');
				}
				
				$row->title = str_replace('&', '&amp;', $row->title);
				
				$big_img_url = JURI::root().($this->id == -1 ? '' : 'media/com_twojtoolbox/' ).$row->img;
				
				$thumb_img_url = $generet_thumb_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
				
				if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($thumb_img_url ) ) $thumb_img_url = $url_link;
					else  $thumb_img_url = str_replace( '&', '&amp;', $thumb_img_url );
				
				$twoButton = (bool) ( ($thumbsHover==2 || $thumbsHoverClick==2) &&  isset($row->link_blank) &&  ( $row->link_blank==1 ||  $row->link_blank==2 ) && $row->link);
				$linkArray = $this->getLink( $row, $big_img_url, $twoButton );
				
				//if( $linkArray['type']=='iframe' )
				$linkArray['options'] .= ($linkArray['options']?', ':''). "thumbnail: '".$thumb_img_url."'";
				
				$thumb_width = $thumb_width_set;
				$thumb_height = $thumb_height_set;
				if( $this->getInt('thumb_type_resizing', 1)==1 && $row->width && $row->height){
					$reSize->setSize($row->width, $row->height);
					$thumb_width = $reSize->tnWidth;
					$thumb_height = $reSize->tnHeight;
				}
				
				$lightboxDesc = $this->getInt('lightboxDesc');
				
				$image_listing .= "\t\t"
					.'<div class="'
							.(!$thumbsHover?'mega-entry-cursor ':'')
							.'mega-entry category-all category-'.$row->catid.'" '
						.'id="mega-entry-'.$row->id.'" '
						.'data-id="'.$row->id.'" '
						.($thumbsHover!=2 && $linkArray['type']=='link' ?'data-openlink="1" '.($linkArray['blank'] == 1 ?'data-blank="1" ':''):'')
						
						.'data-link="'.$linkArray['link'].'" '
						 .($linkArray['type']	?'data-type="'.$linkArray['type'].'" '		:'')
						 .($linkArray['options']?'data-options="'.$linkArray['options'].'" ':'')
						.'data-src="'.$thumb_img_url.'" '
						.'data-width="'.$thumb_width.'" '
						.'data-height="'.$thumb_height .'" '
						.( $lightboxDesc==1 || $lightboxDesc==2 ? 'data-title="'.JText::_(str_replace(array("\n", "\r"), ' ', htmlspecialchars($row->title)), 1).'" ':'')
						.( $lightboxDesc==2 || $lightboxDesc==3 ? 'data-caption="'.JText::_(str_replace(array("\n", "\r"), ' ', htmlspecialchars($row->desc)), 1).'" ':'')
					.'>';
				if($thumbsStyle==1 &&  !$params->get('descPanel')){
					$thumbsStyleFix = $this->getString('thumbsStyleFix');
					$image_listing .= ' <div class="'.$thumbsStyleFix.'">';
						if( $this->getInt('thumbsDesc')==1 || $this->getInt('thumbsDesc')==2 ) $image_listing .= $row->title;
						if( $this->getInt('thumbsDesc')==2 || $this->getInt('thumbsDesc')==3 ) $image_listing .= $row->desc;
					$image_listing .= '</div>';
				}
				if($thumbsStyle==2 &&  !$params->get('descPanel') ){
					$captionClass = '';
					$captionClass .= ' '.$bgColor ;
					if( $params->get('squareLayout') ) 		$captionClass .= ' mega-'.$params->get('squareLayout');
					if( $params->get('landscapeLayout') ) 	$captionClass .= ' mega-'.$params->get('landscapeLayout');
					if( $params->get('portraitLayout') ) 	$captionClass .= ' mega-'.$params->get('portraitLayout');
					if( !$params->get('bgTransp') ) 		$captionClass .= ' mega-transparent';

					$image_listing .= '<div class="mega-covercaption '.$captionClass.' ">';
						if( $this->getInt('thumbsDesc')==1 || $this->getInt('thumbsDesc')==2 ) $image_listing .= '<div class="mega-title">'.$row->title.'</div>';
					 	//$image_listing .= '<div class="mega-date">'.(isset($this->galleryName[$row->catid]['title']) ? $this->galleryName[$row->catid]['title']:'').'</div>'; 
						if( $this->getInt('thumbsDesc')==2 || $this->getInt('thumbsDesc')==3 ) $image_listing .= $row->desc;
					$image_listing .= '</div>';
				}
				if($thumbsHover){
					$this->getHover($row);
					if($thumbsHover==1){
						$image_listing .= '<div class="mega-hover '.(!$thumbsHoverText?' notitle':'').($row->link_blank && ($row->link_blank==1 || $row->link_blank==2) && $row->link?'':' alone').'">';
						if($thumbsHoverText){
							$image_listing .= '<div class="mega-hovertitle">';
								if($thumbsHoverText==1 || $thumbsHoverText==3) $image_listing .= $row->title;
								if($thumbsHoverText==2 || $thumbsHoverText==3) $image_listing .= $row->desc;
							$image_listing .= '</div>';
						}
					}
					if($thumbsHover==2){
						$image_listing .= '<div class="mega-coverbuttons">';
					}
					if( ($thumbsHover==2 || $thumbsHoverClick==2) &&  isset($row->link_blank) &&  ( $row->link_blank==1 ||  $row->link_blank==2 ) && $row->link){
						$image_listing .= 
							'<a href="'.$row->link.'"  title="" '.($row->link_blank==1?' target="_blank"':'').'>'
								.'<div class="mega-'.($thumbsHover==1 ? 'hoverlink' : 'link '.($bgColor!='mega-white'?$bgColor:'mega-black')).'"></div>'
							.'</a>';
					}
					if( ($thumbsHover==2 || $thumbsHoverClick==2) ){
						$image_listing .= 
										'<a class="lightbox" rel="galleryGroup_'.$this->uniqueId.'" '
											.($linkArray['type']	?'data-type="'.$linkArray['type'].'" '		:'')
											.($linkArray['options']?'data-options="'.$linkArray['options'].'" ':'')
											.( $lightboxDesc==1 || $lightboxDesc==2 ? 'data-title="'.JText::_(str_replace(array("\n", "\r"), ' ', htmlspecialchars($row->title)), 1).'" ':'')
											.( $lightboxDesc==2 || $lightboxDesc==3 ? 'data-caption="'.JText::_(str_replace(array("\n", "\r"), ' ', htmlspecialchars($row->desc)), 1).'" ':'')
											.' href="'.$linkArray['link'].'" '
											.' title=""'
										.'>';
							$image_listing .= ($thumbsHover==1 ? '<div class="mega-hoverview"></div>':'<div class="mega-view '.($bgColor!='mega-white'?$bgColor:'mega-black').'"></div>');
						$image_listing .= '</a>';
					}
					$image_listing .= '</div>';
				}
				$image_listing .= '</div>'."\n";
			}
			
			
			if( !$this->onlyImagesLoad )  $return_text .= '<div id="twoj_photo_gallery_root'.$this->uniqueId.'" '.$this->getRootStyle().' class="twoj_photo_gallery_root">';
			if( !$this->onlyImagesLoad )  $return_text .= $this->getCategoryMenu();
			$this->getOptions();
			if( !$this->onlyImagesLoad ) $return_text .= '<div id="twoj_photo_gallery'.$this->uniqueId.'" '.$this->getGalleryStyle().' class="twoj_photo_gallery_class">';
				if( !$this->onlyImagesLoad ) $return_text .=  '<div id="twoj_photo_gallery_wrap'.$this->uniqueId.'" class="twoj_photo_gallery_wrap_class ">';
 					if( $this->onlyImagesLoad ) $return_text .=  '<div>';
						$return_text .= $image_listing;
						if( $this->onlyImagesLoad ) $return_text .=  '<div class="images-array">['.implode(',', $this->itemsIdArray).']</div>';
					if( $this->onlyImagesLoad ) $return_text .=  '</div>';
					
				if( !$this->onlyImagesLoad ) $return_text .=  '</div>';
			if( !$this->onlyImagesLoad ) $return_text .=  '</div>';
			if( !$this->onlyImagesLoad ) $return_text .= '<div class="twojtoolbox_clear"></div>';
			
			/* Load more button +*/
			if( $this->totalItem > count($rows) ){
				if( !$this->onlyImagesLoad ) $return_text .= '<div class="progress-button-'.$this->getString('buttonAlign').'">';
					if( !$this->onlyImagesLoad ) $return_text .= 
						'<button ' 
							.'id="load_more_'.$this->uniqueId.'" '
							.'class="progress-button '.$this->getString('buttonColor').'" '
							.'data-finished="'.JText::_($this->getString('buttonTextLoadmore'), 1).'" '
							.'data-loading="'.JText::_($this->getString('buttonTextLoading'), 1).'">'
								.$this->getString('buttonTextLoadmore')
						.'</button>';
				if( !$this->onlyImagesLoad ) $return_text .= '</div>';
			}
			/* Load more button -*/
			
			if( !$this->onlyImagesLoad ) $return_text .= '</div>';
			
			$this->javascript_code .= 'var urlPatchTwojPhotoGallery = "'.JURI::base().'", galleryNowClick'.$this->uniqueId.' = 0, galleryButtonPos'.$this->uniqueId.' = 0;'."\n";
			$this->javascript_code .= 'emsajax(function(){'."\n";
				$this->javascript_code .= 'emsajax("head").append("<style '.($this->id==-1?'id=\'dynamic_css\'':'').' type=\'text/css\'>'.$this->compileJavaScriptStyles().'</style>");';
				$this->javascript_code .= ' window["galleryAPI'.$this->uniqueId.'"] = emsajax("#twoj_photo_gallery_wrap'.$this->uniqueId.'").megafoliopro({ '.implode(' ,', $this->gen_option).' }); '."\n";  //.megaremix()
				$this->javascript_code .= ' window["galleryAPI'.$this->uniqueId.'"] = emsajax("#twoj_photo_gallery_wrap'.$this->uniqueId.'").megaremix(); '."\n";  //.megaremix()
				
				$this->javascript_code .= ' window["galleryCategory'.$this->uniqueId.'"] = "0"; '."\n";  //.megaremix()
				$this->javascript_code .= ' window["galleryCategoryCount'.$this->uniqueId.'"] = '.json_encode($this->catCoutArray).'; '."\n";  //.megaremix()
				$this->javascript_code .= ' window["galleryItems'.$this->uniqueId.'"] = ['.implode(',', $this->itemsIdArray).']; '."\n";  //.megaremix()
				
				$this->javascript_code .= $this->javaAcriptAddon;
				$this->javascript_code .= ' init2JPhotoGallery( "'.$this->uniqueId.'", "'.$this->id.'", '.$thumbsHoverClick.', {'.$this->getLightBoxOptions().'} );'."\n";
			$this->javascript_code .= ' });'."\n";

			
			if( $this->render_content == 0) {
				//$document = JFactory::getDocument();
				//$document->addScriptDeclaration($this->javascript_code);
				$return_text .= '<script language="JavaScript" type="text/javascript">'."\n".'<!--//<![CDATA['."\n".$this->javascript_code."\n".'//]]>-->'."\n".'</script>';
			}
		}
		if($return_text) return  $return_text; else return null;
	}
	protected function getGalleryStyle(){
		$padding = 0;
		if( $border = $this->getStyleFromJSON('border', 2) ){
			$padding += (int) $border['width'];
		}
		if( $shadow = $this->getStyleFromJSON('shadow') ){
			$padding += (int) $shadow['width'];
		}
		if($padding){
			$this->galleryStyle .= 'padding:'.(int)($padding*2) .'px;' ;
		}
		
		if( $this->getJSONValue('menuPending') =='menuPendingSet' && $this->menu ){
			$padding = $padding + $this->getJSONValue( 'menuPending', 'menuPendingSet', 'paddingBottom');
			$this->galleryStyle .= 'padding-top:'.(int)$padding.'px;';
		}
		
		if(count($this->galleryStyle)) return 'style="'.$this->galleryStyle.'" ';
	}
	protected function getRootStyle(){
		$rootStyles = array();
		if( $galleryWidth = $this->getString('galleryWidth') ){
			$rootStyles[] = 'width: '.(int) $galleryWidth.(strpos($galleryWidth, '%')!==false  ? '%' : 'px').';';
		}
		if( $galleryMaxWidth = $this->getString('galleryMaxWidth') ){
			$rootStyles[] = 'max-width: '.(int) $galleryMaxWidth.(strpos($galleryMaxWidth, '%')!==false  ? '%' : 'px').';';
		}
		if( $pad_top = $this->getInt( 'galleryPadding_top') )  		$rootStyles[] = 'padding-top: '.	$pad_top.'px;';
		if( $pad_bot = $this->getInt( 'galleryPadding_bottom') )  	$rootStyles[] = 'padding-bottom: '.	$pad_bot.'px;';
		if( $pad_left = $this->getInt( 'galleryPadding_left') )  	$rootStyles[] = 'padding-left: '.	$pad_left.'px;';
		if( $pad_right = $this->getInt( 'galleryPadding_right') )  	$rootStyles[] = 'padding-right: '.	$pad_right.'px;';
		
		if( $galleryAlign = $this->getString('galleryAlign') ){
			if( $galleryAlign == 'centre' ) $rootStyles[] = 'margin: 0 auto;';
				else $rootStyles[] = 'float:'.$galleryAlign.';';
		}
		
		$borderStyles = '';
		if( $galleryBorder = $this->getStyleFromJSON('galleryBorder', 2) ){
			$rootStyles[] = 'border: '.$galleryBorder['width'].'px '.$galleryBorder['style'].' '.$galleryBorder['color'].';';
		}

		if( $galleryShadow = $this->getStyleFromJSON('galleryShadow') ){
			$rootStyles[] = 'box-shadow: 1px 1px '.$galleryShadow['width'].'px rgba( '.$galleryShadow['color_rgb'][0].', '.$galleryShadow['color_rgb'][1].', '.$galleryShadow['color_rgb'][2].', '.$galleryShadow['opacity'].');';
		}
		
		if( $galleryBgColor = $this->getColor('galleryBgColor') ) 	$rootStyles[] = 'background-color: '.$galleryBgColor.'; ';
		
		if(count($rootStyles)) return 'style="'.implode('', $rootStyles).'" ';
	}
	
	protected function compileJavaScriptStyles(){
		$borderStyles = '';
		if( $border = $this->getStyleFromJSON('border', 2) ){
			$borderStyles .= 'border: '.$border['width'].'px '.$border['style'].' '.$border['color'].';';
		}
		if( $radius = $this->getInt('radius') ){
			$borderStyles .= 'border-radius: '.$radius.'px; -moz-border-radius: '.$radius.'px; -webkit-border-radius:'.$radius.'px;';
		}
		if( $shadow = $this->getStyleFromJSON('shadow') ){
			$borderStyles .= 'box-shadow: 1px 1px '.$shadow['width'].'px rgba( '.$shadow['color_rgb'][0].', '.$shadow['color_rgb'][1].', '.$shadow['color_rgb'][2].', '.$shadow['opacity'].');';
		}
		if($borderStyles) $this->javaScriptStyles[] = '#twoj_photo_gallery_wrap'.$this->uniqueId.' .mega-entry .mega-entry-innerwrap{'.$borderStyles.'}';
		
		$borderStylesHover = '';
		if( $hovershadow = $this->getStyleFromJSON('hovershadow') ){
			$borderStylesHover .= 'box-shadow: 1px 1px '.$hovershadow['width'].'px rgba( '.$hovershadow['color_rgb'][0].', '.$hovershadow['color_rgb'][1].', '.$hovershadow['color_rgb'][2].', '.$hovershadow['opacity'].');';
		}
		if( $hoverborder = $this->getStyleFromJSON('hoverborder', 2) ){
			$borderStylesHover .= 'border: '.$hoverborder['width'].'px '.$hoverborder['style'].' '.$hoverborder['color'].';';
		}
		if($borderStylesHover) $this->javaScriptStyles[] = '#twoj_photo_gallery_wrap'.$this->uniqueId.' .mega-entry .mega-entry-innerwrap:hover{'.$borderStylesHover.'}';
		
		return implode(' ', $this->javaScriptStyles);
	}
	
	function getMenuItem( $items, $rootItem = 0 ){
		$returnElemtnHTML = '';
		
		if($rootItem){	 
			$returnElemtnHTML .= '<ul class="twoj-menu-'.$this->menuStyle.'-'.$this->menuColor.' zetta-menu " id="twoj_photo_gallery_menu'.$this->uniqueId.'">';
			if( $this->getInt('menuRoot') ) $returnElemtnHTML .= '<li class="zm-active" ><a data-categoryid="0" data-category="category-all" href="#">'.$this->getString('menuRootLabel').'</a></li>';
		} else {
			$returnElemtnHTML .= '<ul zm-size="200" >';
		}
		
		if( is_array($items) ){
			for($i=0;$i<count($items);$i++){
				$item = $items[$i];
				if( is_array($item) ){
					$chEx = (bool) ( isset($item['ch']) && is_array($item['ch']) && count($item['ch']) );
					$returnElemtnHTML .= '<li ><a data-categoryid="'.$item['id'].'" data-category="category-'.$item['id'].'" href="#">'
						.(isset($this->galleryName[$item['id']]['title']) ? $this->galleryName[$item['id']]['title']:'Category '.$item['id'])
						.($chEx?'<i class="zm-caret fa fa-angle-'.($rootItem?'down':'right').'"></i>':'')
					.'</a>';
					if( $chEx ) $returnElemtnHTML .= $this->getMenuItem( $item['ch'] );
					$returnElemtnHTML .='</li>';
				} else {
					$returnElemtnHTML .='<li ><a data-categoryid="'.$item.'" data-category="category-'.$item.'" href="#">'
					.(isset($this->galleryName[$item]['title']) ? $this->galleryName[$item]['title']:'Category '.$item)
					.'</a></li>';
				}
			}
		}
		$returnElemtnHTML .='</ul>';
		return $returnElemtnHTML;
	}
	
	function getCategoryMenu(){
		$returnHTML = '';
		
		if( !$this->menu ) return ;
		
		$menuStyle = '';
		
		if( $menuMaxWidth = $this->getString('menuMaxWidth') ){
			$menuStyle .= 'max-width: '.(int) $menuMaxWidth.(strpos($menuMaxWidth, '%')!==false  ? '%' : 'px').';';
		}
		
		if( $menuAlign = $this->getString('menuAlign') ){
			$menuStyle .= 'text-align: '.$menuAlign.';';
		}
		
		if( $this->getJSONValue('menuPending') =='menuPendingSet'){
			$menuStyle .= 'padding-left:'.	(int) $this->getJSONValue( 'menuPending', 'menuPendingSet', 'paddingLeft').'px;';
			$menuStyle .= 'padding-right:'.	(int) $this->getJSONValue( 'menuPending', 'menuPendingSet', 'paddingRight').'px;';
		}
		
		$returnHTML .= '<div '.($menuStyle?'style="'.$menuStyle.'"':'').'>';
		
		$menuType = $this->getInt('menuType');
		if( $menuType ){
			$returnHTML .= $this->getMenuItem($this->galleryArray, 1);
		} else {
			$returnHTML .= $this->getMenuItem($this->galleryId, 1);
		}
		$returnHTML .= '</div>';
		$returnHTML .= '<div class="clear"></div>';
		
		/* simple switch stack switch-margin stack-margin */
		$menuOptions = array();
		if( !$this->getInt('menuFullWidth') ){
			$menuOptions[] = 'fullWidth : false';
		}
		$menuOptions[] = "responsive: 'switch'";
		
		if( $menuEvent = $this->getString('menuEvent') ){
			$menuOptions[] = "showOn: '".$menuEvent."'";
		}
		
 		$this->javaAcriptAddon .= 'emsajax("#twoj_photo_gallery_menu'.$this->uniqueId.'").zettaMenu({'.implode(',', $menuOptions).'});';
		
		return $returnHTML;
	}	
	
	public function getLink( $row, $big_img_url, $twoButton ){
		$rA = array();
	
		$rA['link'] 	= $big_img_url; 
		$rA['options']	= '';
		$rA['blank'] = '';
		$rA['type']	= 'image';
		
		if($row->link_blank && $row->link && !$twoButton ){
			switch($row->link_blank){
				case 3: 
						$rA['link'] = $row->link; 	
						$rA['options'].='width: '.$this->getInt('lightboxIframeWidth').', height: '.$this->getInt('lightboxIframeHeight');
						$rA['type']	= 'iframe';
					break;
				
				case 4: 
						$rA['link'] = 'http://www.youtube.com/embed/'.$row->link.'?autoplay=1&autohide=1&border=0&egm=0&showinfo=0'; 	
						$rA['options'].='width: '.$this->getInt('lightboxIframeWidth').', height: '.$this->getInt('lightboxIframeHeight');
						$rA['type']	= 'iframe';
					break;
				case 5: 
						$rA['link'] = 'http://player.vimeo.com/video/'.$row->link.'?autoplay=1'; 		
						$rA['options'].='width: '.$this->getInt('lightboxIframeWidth').', height: '.$this->getInt('lightboxIframeHeight');
						$rA['type']	= 'iframe';						
					break;
				case 1:
						$rA['blank'] = 1;
				case 2:
						$rA['type']	= 'link';
						$rA['link'] = $row->link; 
					break;						
			
			}
		}
		return $rA;
	}
	
	function getLightBoxOptions( ){
		$lightboxOptions=array();
		$lightboxOptions[] = 'fullViewPort:"'.$this->getString('lightboxFullViewPort').'"';
		$lightboxOptions[] = 'skin:"'.$this->lightboxStyle.'"';
		if( $this->getInt('lightboxSocialButton') ){
			$lightboxOptions[] = 'social: { buttons:{facebook: true,  twitter: true, googleplus: true } }';
		}
		
		$lightboxOptionsControls = array();
		$lightboxOptionsControls[] =  'arrows:'.	$this->getInt('lightboxArrows');
		$lightboxOptionsControls[] =  'slideshow:'.	$this->getInt('lightboxSlideshow');
		$lightboxOptionsControls[] =  'toolbar:'.	$this->getInt('lightboxToolbar');
		$lightboxOptionsControls[] =  'fullscreen:'.$this->getInt('lightboxFullscreen');
		$lightboxOptionsControls[] =  'thumbnail:'.	$this->getInt('lightboxThumbnail');
		$lightboxOptionsControls[] =  'keyboard:'.	$this->getInt('lightboxKeyboard');
		$lightboxOptionsControls[] =  'mousewheel:'.$this->getInt('lightboxMousewheel');
		$lightboxOptionsControls[] =  'swipe:'.		$this->getInt('lightboxSwipe');
		if( count($lightboxOptionsControls) ){
			$lightboxOptions[] = 'controls:{'.implode(' ,', $lightboxOptionsControls).'}';
		}
			
		$lightboxOptions[] = 'path:"'.$this->getString('lightboxThumb').'"';
		
		$lightboxOptions[] = 'text: {'
								.'close:"'.				JText::_( $this->getString('lightboxTextClose'), 1).'",'
								.'enterFullscreen:"'.	JText::_( $this->getString('lightboxTextenF'), 1).'",'
								.'exitFullscreen:"'.	JText::_( $this->getString('lightboxTextexF'), 1).'",'
								.'slideShow:"'.			JText::_( $this->getString('lightboxTextSlideShow'), 1).'",'
								.'next:"'.				JText::_( $this->getString('lightboxTextNext'), 1).'",'
								.'previous:"'.			JText::_( $this->getString('lightboxTextPrevious'), 1).'",'
							.'}';
		return implode(' ,', $lightboxOptions);
	}
	
	function getHover( $row ){
	}
	
	function getOptions(){
		if( !$this->getInt('thumbLayout', 0) ){
			$thumbLayout = $this->getInt('thumbLayoutOne', 0);
		} else {
			$thumbLayout = $this->params->get('thumbLayoutArray');
			$thumbLayout = implode(",", $thumbLayout);
		}
		$this->addGenOption("layoutarray: [ ".$thumbLayout."]");
		
		$this->addGenOption(' filterChangeAnimation: "'.$this->getString('categoryAnimation').'"');
		
		$this->insertInt( 'filterChangeSpeed');
		$this->insertInt( 'filterChangeRotate');
		
		$this->addGenOption(' filterChangeScale: '.$this->getString('filterChangeScale').'');
		
		/* $this->insertInt( 'delay'); */
		
		if( $this->getJSONValue('pending') =='pendingOn'){
			$this->addGenOption(' paddingHorizontal: '.	(int) $this->getJSONValue( 'pending', 'pendingOn', 'paddingHorizontal'));
			$this->addGenOption(' paddingVertical: '.	(int) $this->getJSONValue( 'pending', 'pendingOn', 'paddingVertical'));
		}
	}
	
	
	
	
	
	
	public function getChildrenId($elementArray, $elementNeed, $getChild = 0){
		$returnArray = array();
		
		for($i=0;$i<count($elementArray);$i++){
			if( !isset($elementArray[$i]['id']) ) continue;
			if( !$getChild ){
				if( $elementArray[$i]['id'] == $elementNeed ){
					if( isset($elementArray[$i]['children']) ){  
						return $this->getChildrenId($elementArray[$i]['children'], $elementNeed, 1);
					}
				} elseif( isset($elementArray[$i]['children']) ){
					$tempArray = array();
					$tempArray = $this->getChildrenId($elementArray[$i]['children'], $elementNeed);
					if( count($tempArray) ) return $tempArray;
				}
			} else {
				$this->galleryId[] = $elementArray[$i]['id'];
				
				if( isset($elementArray[$i]['children']) )
					$returnArray[] = array( 'id'=> $elementArray[$i]['id'], 'ch' => $this->getChildrenId($elementArray[$i]['children'], $elementNeed, 1) );
				else 
					$returnArray[] = $elementArray[$i]['id'];
			}
		}
		return $returnArray;
	}
	
	public function getGalleryArray( $id ){
		$row = JTable::getInstance('Data', 'TwojToolboxTable');
		$row->loadPluginType($this->type);
		$jsonInput = $row->json;
		
		if(!$jsonInput) $jsonInput = '[{"id":'. $id.'}]';
		$jsonArray = json_decode($jsonInput, true);
		if( $jsonArray === NULL ) return false;
		
		$query = $this->_db->getQuery(true);
		$query->select( '`id`, `title`' );
		$query->from('#__twojtoolbox');
		$query->where('	type = '. $this->_db->quote($this->type) );
		$this->_db->setQuery( (string) $query );
		//echo (string) $query;
		$this->galleryName = $this->_db->loadAssocList('id');
		
		if(!$galleryArray = $this->getChildrenId( $jsonArray, $id ) ) return false;
	
		return $galleryArray;
	}
	
	public function getGalleryItem( $curArray, $mode=0 ){		
		$returnItems = array();
		for($i=0;$i<count($curArray);$i++){
			if(is_array($curArray[$i])){
				$returnItems = array_merge((array) $returnItems, (array) $this->getElementItems( $curArray[$i]['id'], $mode ) );
				$returnItems = array_merge((array)$returnItems , (array) $this->getGalleryItem( $curArray[$i]['ch'], $mode ));
			} else {
				$returnItems = array_merge((array)$returnItems , (array) $this->getElementItems( $curArray[$i], $mode ));
			}
		}
		return $returnItems;
	}
	
	public function getElementItems( $id, $mode=0 ){
		if($id){
			$query= $this->_db->getQuery(true);
			$query->select('a.*');
			$query->from('#__twojtoolbox_elements AS a');
			if($mode){
				$query->where('a.catid IN ('.implode( ',' , $id ).') AND a.state = 1');
			} else {
				$query->where('a.catid = '.(int) $id.' AND a.state = 1');
			}
			switch($this->getInt( 'orderby' )){
				case 6: $query->order('a.id DESC'); 		break;
				case 5: $query->order('a.id ASC');			break;
				case 4: $query->order('RAND()');			break;
				case 3: $query->order('a.title DESC');		break;
				case 2: $query->order('a.title ASC');		break;
				case 1: //$query->order('a.ordering DESC');	break;
				case 0: 
				default:$query->order('a.ordering ASC');
			}
			$this->_db->setQuery( (string) $query );
			//echo "<br />".(string) $query ."<br />";
			return $this->_db->loadObjectList();
		} else return '';
	}
	
	static public function html2rgb($color){
		$color = trim($color);
		if ( strpos($color, '#') !==false  ) $color = str_replace('#', '', $color);
		if(!preg_match('/[0-9a-fA-F]{3,6}/', $color)) return array( '0', '0', '0');
		if( strlen($color) == 6 ){
			list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		} elseif (strlen($color) == 3){
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		} else return  array( '0', '0', '0');
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}
	
	function getPend( $name ){
		$html_ret = ' ';
		$html_ret .=  $this->getInt( $name.'_top', 10)	.'px ';
		$html_ret .=  $this->getInt( $name.'_right', 10 ).'px ';
		$html_ret .=  $this->getInt( $name.'_bottom', 10).'px ';			
		$html_ret .=  $this->getInt( $name.'_left', 10 ).'px';
		return str_replace( ' 0px', ' 0', $html_ret );
	}
	
	function getStyleFromJSON( $name, $add_check = 0 ){
		$json_temp = $this->getString($name);

		if($json_temp){
			$json_temp = json_decode($json_temp, 1 );
			
			if( $json_temp==null || !isset($json_temp['enabled']) || !$json_temp['enabled'] ) return false;
			
			$json_temp['enabled'] = 1;
			
			if( !isset($json_temp['width'])  ) $json_temp['width'] = 1;
			$json_temp['width'] = (int) $json_temp['width'];
			
			if( !isset($json_temp['opacity'])  ) $json_temp['opacity'] = 1;
			$json_temp['opacity'] = ( (int) $json_temp['opacity'] / 100 ); 
			
			if( !isset($json_temp['color']) )  return false; 
			
			$json_temp['color_rgb'] = TwoJToolBoxPhotoGallery::html2rgb($json_temp['color']); 
			
			if($add_check){
				if($add_check==2  && (!isset($json_temp['style']) || $json_temp['style']=='none;' ) ) return false;
			}
			return $json_temp;
		} return false;
	}
}

class TwoJToolBoxReSize extends JObject{
	public $width = 0;
	public $height = 0;
	
	public $maxWidth = 0;
	public $maxHeight = 0;
	
	public $xRatio = 0;
	public $yRatio = 0;
	public $xyRatio = 0;
	
	public $tnWidth = 0;
	public $tnHeight = 0;
	
	function __construct( $maxWidth = 120, $maxHeight =80 ){
		$this->maxWidth 	= $maxWidth;
		$this->maxHeight 	= $maxHeight;
	}
	
	
	public function setSize( $width = 400 , $height = 600 ){
		$this->width 		= $width;
		$this->height		= $height;
		$this->calcSize();
	}
	
	public function calcSize(){
		$this->xRatio 	= $this->maxWidth 	/ $this->width;
		$this->yRatio 	= $this->maxHeight 	/ $this->height;
		$this->xyRatio 	= $this->width 		/ $this->height;
		
		if( ($this->width <= $this->maxWidth) && ($this->height <= $this->maxHeight) ) {
			$this->tnWidth 	= $this->width;
			$this->tnHeight = $this->height;
		} else if( ($this->xRatio * $this->height) < $this->maxHeight ) {
			$this->tnHeight = ceil($this->xRatio * $this->height);
			$this->tnWidth = $this->maxWidth;
		} else {
			$this->tnWidth = ceil($this->yRatio * $this->width);
			$this->tnHeight = $this->maxHeight;
		}
	}
	
		
	
}
