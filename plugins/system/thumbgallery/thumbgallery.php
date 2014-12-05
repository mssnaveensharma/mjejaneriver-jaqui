<?php
/*------------------------------------------------------------------------
# plg_thumbgallery - Thumb Gallery Plugin
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# copyright Copyright (C) 2010 joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemThumbGallery extends JPlugin {
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config); 
		$this->loadLanguage();
	}

	public function onAfterRender()
	{
		$app     = JFactory::getApplication();
		$doc	 = JFactory::getDocument();
		$doctype = $doc->getType();
		
		if (!($app->getName() == 'site' || $doctype !== 'html')) {
			return true;
		}
		
		$body  = JResponse::getBody();

		if (preg_match_all("#{tg}(.*?){/tg}#s", $body, $matches, PREG_PATTERN_ORDER) > 0) {
			
			$csscount = -1;	
			$im_quality		= $this->params->get('im_quality', 95);
			$im_proportions = $this->params->get('im_proportions', 'bestfit');
			$im_type        = $this->params->get('im_type','');
			$th_sort		= $this->params->get('th_sort', 0);
			$layout			= $this->params->get('layout', 0);;
			$bgcolor        = $this->params->get('bgcolor', '#FFFFFFF');
			$th_quality		= $this->params->get('th_quality', 80);
			$th_proportions = $this->params->get('th_proportions', 'bestfit');
			$th_type        = $this->params->get('th_type','');
			$pg_show        = $this->params->get('pg_show', 1);
			
			foreach ($matches[0] as $match) {
				$csscount++;
				$tg_code = preg_replace("/{.+?}/", "", $match);
				$tg_raw = explode ("|", $tg_code);
				$folder = $tg_raw[0];
				if(substr($folder,-1,1)!="/"&&$folder!=""){$folder=$folder."/";}
				if(substr($folder,0,1)=="/"&&$folder!=""){$folder=substr($folder,1,strlen($folder)-1);}

				unset ($tg_overrides);
				$tg_overrides=array();
				if(count($tg_raw)>=2){
					for($i=1;$i<count($tg_raw);$i++){
						$overr_temp=explode("=",$tg_raw[$i]);
						if(count($overr_temp)>=2){
							$tg_overrides[strtolower(trim($overr_temp[0]))]=trim($overr_temp[1]);
						}
					}
				}
				
				ob_start();

				unset($images);
				$noimage = 0;
				if(substr($folder, -1)!="/"){$folder=$folder."/";}
				if(substr($folder,0,1)!="/"){$folder="/".$folder;}

				if ($dh = @opendir(JPATH_SITE.$folder)) {
					while (($f = readdir($dh)) !== false) {
						if((substr(strtolower($f),-4) == '.jpg') || (substr(strtolower($f),-4) == '.gif') || (substr(strtolower($f),-4) == '.png')) {
							$noimage++;
							$images[] = array('filename' => $f, 'flastmod' => filemtime(JPATH_SITE.$folder.$f)); 
						}
					}
					closedir($dh); ?>
				<?php 
				} else { ?>
					<br /><?php echo JText::_('PLG_THUMBGALLERY');?>:<br /><?php echo JText::_('PLG_THUMBGALLERY_MSG_FOLDER_NOT_FOUND')." ".JPATH_SITE.$folder;?>
				<?php
                }

				if(!$noimage) { ?>
					<br /><?php echo JText::_('PLG_THUMBGALLERY'); ?>:<br /><?php echo JText::_('PLG_THUMBGALLERY_MSG_NO_IMAGES')." ".JPATH_SITE.$folder; ?>
                <?php } else {
					$im_space 		= (array_key_exists("im_space",$tg_overrides)&&$tg_overrides['im_space']!="")?($tg_overrides['im_space']):($this->params->get('im_space', 10));
					$im_width		= (array_key_exists("im_width",$tg_overrides)&&$tg_overrides['im_width']!="")?($tg_overrides['im_width']):($this->params->get('im_width', 416));
					$im_height		= (array_key_exists("im_height",$tg_overrides)&&$tg_overrides['im_height']!="")?($tg_overrides['im_height']):($this->params->get('im_height', 300));
					$th_width       = (array_key_exists("th_width",$tg_overrides)&&(int)$tg_overrides['th_width']!=0)?($tg_overrides['th_width']):($this->params->get('th_width', 83));
					$th_height      = (array_key_exists("th_height",$tg_overrides)&&(int)$tg_overrides['th_height']!=0)?($tg_overrides['th_height']):($this->params->get('th_height', 60));
					$th_cols	    = (array_key_exists("th_cols",$tg_overrides)&&$tg_overrides['th_cols']!="")?($tg_overrides['th_cols']):($this->params->get('th_cols', 3));
					$th_rows	    = (array_key_exists("th_rows",$tg_overrides)&&$tg_overrides['th_rows']!="")?($tg_overrides['th_rows']):($this->params->get('th_rows', 4));
					$th_space 		= (array_key_exists("th_space",$tg_overrides)&&$tg_overrides['th_space']!="")?($tg_overrides['th_space']):($this->params->get('th_space', 5));
										
					$images = plgSystemThumbGallery::sortImages($images,$th_sort);

					$identifier="i_".$csscount;

					$thumbdir=JPATH_SITE.$folder.'tg_thumbs/';
					if(!is_dir($thumbdir)){plgSystemThumbGallery::createFolder($thumbdir,'thumbnail');}

					$imgdir=JPATH_SITE.$folder.'tg_images/';
					if(!is_dir($imgdir)){plgSystemThumbGallery::createFolder($imgdir,'image');} 					
					
					$thumbswidth=$th_cols*($th_space+$th_width)-$th_space;
					if($layout==0){
						$thumbsmrg='margin-top:'.$im_space.'px';
						$gallerywidth=($im_width>$thumbswidth?$im_width:$thumbswidth);
					}else{
						$thumbsmrg='margin-left:'.$im_space.'px';
						$gallerywidth=intval($im_width+$im_space+$thumbswidth);
					}
					$mainImg=plgSystemThumbGallery::getImage($folder,'tg_images',$images[0]['filename'],$im_width, $im_height, $im_proportions, $im_type, $bgcolor);
					?>

					<div id="tg_holder_<?php echo $identifier; ?>" class="tg_holder <?php echo ($layout==0?'ver':'hor'); ?>" data-width="<?php echo $gallerywidth; ?>"><a id="g_<?php echo $identifier; ?>"></a>
						<div id="tg_main_<?php echo $identifier; ?>" class="tg_main" data-width="<?php echo $im_width; ?>">
							<img alt="<?php echo $images[0]['filename']; ?>" src="<?php echo JURI::base(true).$folder.'tg_images'.'/'.$mainImg[0]; ?>" width="" />
						</div>
						<div id="tg_thumbs_<?php echo $identifier; ?>" class="tg_thumbs" data-width="<?php echo $thumbswidth; ?>">
					<?php
					$slide=0;
					for($a=0;$a<$noimage;$a++) {
						if($images[$a]['filename'] != '') {
							$thethumb = plgSystemThumbGallery::getImage($folder,'tg_thumbs',$images[$a]['filename'],$th_width, $th_height, $th_proportions, $th_type, $bgcolor);
							$theimage = plgSystemThumbGallery::getImage($folder,'tg_images',$images[$a]['filename'],$im_width, $im_height, $im_proportions, $im_type, $bgcolor);
							
							$new_slide = is_int($a/($th_cols*$th_rows));
							$new_row = is_int($a/$th_cols);
								
							if($new_slide){
								$slide++; ?>
								<div class="thumbslide ts_<?php echo $identifier; ?><?php echo ($slide==1?' first':''); ?><?php echo ($slide==(intval($noimage/$th_rows/$th_cols)+1)?' last':''); ?>" id="sl_<?php echo $identifier.'_'.$slide; ?>"<?php echo ($slide==1?' style="display:table;opacity:100;"':''); ?>>
							<?php 
							}
							$thumbrow=intval(($a-$th_cols*$th_rows*($slide-1))/$th_cols);
							if($new_row) { ?>
									<div class="th_row row_<?php echo $thumbrow; ?>">
							<?php
							}
							?>
									<img src="<?php echo JURI::base(true).$folder.'tg_thumbs/'.$thethumb[0]; ?>" alt="<?php echo $thethumb[0]; ?>" class="<?php echo $identifier."_".$a; ?><?php echo($new_row?' firstinrow': ''); ?>" data-fullimage="<?php echo JURI::base(false).$folder.'tg_images/'.$theimage[0]; ?>" onmouseover="tg_showImage(this)" width="<?php echo $thethumb[2]; ?>" />
							<?php
							if( ($a+1)%$th_cols==0 || $a==($noimage-1) ){ ?>
									</div>
							<?php
							}
							if( ($a+1)==($th_cols*$th_rows*$slide) || $a==($noimage-1) ){ ?>
								</div>
							<?php }							
						}
					}
					$pagination   = '';
					$pagheight    = 0;
					if ($pg_show && $slide>1) {	
						$pagination = plgSystemThumbGallery::paginate($identifier,$slide);	
						$pagheight=34;
					}										
					$css="<style type='text/css'>\n";
					$css.="#tg_holder_".$identifier." {background-color:".$bgcolor.";}\n";
					$css.="#tg_thumbs_".$identifier." {".$thumbsmrg.";}\n";
					$css.="#tg_thumbs_".$identifier." img {margin-left:".$th_space."px;margin-bottom:".$th_space."px;}\n";
					if ($pagination) {	
						$pg_image = $this->params->get('pg_image', -1);
						if ($pg_image != -1) {
							$css.="#tg_holder_".$identifier." .slidepage li,.slidepage span{background-image:url(".JURI::root()."plugins/system/thumbgallery/images/".$pg_image.");}\n";
							$css.="#tg_holder_".$identifier." .slidepage span.firstpage,.slidepage span.prevpage,.slidepage span.nextpage,.slidepage span.lastpage{text-indent:-10000px;font-size:0;}\n";
						}
					}
					$css.="</style>\n";
					echo $pagination;
					?>
					</div>
					<?php
					?>					
					</div>
					<?php echo 			
					$head = "";
					$head .= "
<link rel=\"stylesheet\" href=\"".JURI::root()."plugins/system/thumbgallery/scripts/style.css\" type=\"text/css\" />";	
					$head .= $css;
					$head .= "
<script type=\"text/javascript\" src=\"".JURI::root()."plugins/system/thumbgallery/scripts/thumbslide.js\"></script>";
	
					$body = str_replace("</head>",$head."\n</head>",$body);
				}
				
				$body = plgSystemThumbGallery::replaceCall("{tg}".$tg_code."{/tg}",ob_get_clean(), $body);				
				JResponse::setBody($body);

			}
		}
	}
	
	public function paginate($id,$slides) {
		
		$pg_1 = $this->params->get('pg_1', 1);
		$pg_2 = $this->params->get('pg_2', 1);
		$pg_3 = $this->params->get('pg_3', 1);
		$pg_image = $this->params->get('pg_image', -1);
		$visible = $this->params->get('pg_visible', 3);
		if($slides<$visible) $visible=$slides; 
		$li_width = 25;
		
		ob_start();
		?>
        <div class="slidepage<?php echo ($pg_image==-1?' noimage':''); ?>" id="sp_<?php echo $id; ?>">
          <?php if ($pg_1) : ?>
          <span class="firstpage" onClick="tg_thumbJump(this,1,0,<?php echo $visible; ?>)">&lt;&lt;</span>
          <?php endif; ?>
          <?php if ($pg_2) : ?>
          <span class="prevpage" onClick="tg_thumbJump(this,0,-1,<?php echo $visible; ?>)">&lt;</span>
          <?php endif; ?>
          <div class="pages" data-pages="<?php echo $slides; ?>,<?php echo $visible; ?>" >
            <ul id="ul_<?php echo $id; ?>">
              <?php 
			  for ($i=0;$i<$slides;$i++) :
				$class = 'pagenumber'; 
				if ($i==0) : $class.=' activepagenumber first edge-l';
				elseif ($i==($slides-1)) : $class.=' last'; 
				endif; 
				if ($i+1==$visible) : $class.=' edge-r'; endif;
				?>
                <li class="<?php echo $class; ?>" id="pn_<?php echo $id; ?>_<?php echo $i+1; ?>" onClick="tg_thumbJump(this,<?php echo $i+1; ?>,0,<?php echo $visible; ?>)"><?php echo $i+1; ?></li>
              <?php endfor; ?>
            </ul>
          </div>
          <?php if ($pg_2) : ?>
          <span class="nextpage" onClick="tg_thumbJump(this,0,+1,<?php echo $visible; ?>)">&gt;</span>
          <?php endif; ?>
          <?php if ($pg_1) : ?>
          <span class="lastpage" onClick="tg_thumbJump(this,<?php echo $slides; ?>,0,<?php echo $visible; ?>)">&gt;&gt;</span>
          <?php endif; ?>
          <?php if ($pg_3) : ?>
          <span class="totalpages">(<?php echo $slides; ?>)</span>
          <?php endif; ?>
        </div>
    	<?php	
		return ob_get_clean();	
	}
	
	public function getImage($folder, $sub_folder, $img, $width, $height, $proportion, $img_type, $bgcolor) 
	{
				
		$img_name   = pathinfo($img, PATHINFO_FILENAME);
		$img_ext    = pathinfo($img, PATHINFO_EXTENSION);
		$img_path   = JPATH_BASE . $folder . $img;
		$bgcolor    = hexdec($bgcolor);
		$size 	    = @getimagesize($img_path);
		
		$errors = array();
		
		if(!$size) 
		{	
			echo 'There was a problem loading image ' . $img_name . '.' . $img_ext . ' in plg_thumbgallery'; exit;
		
		} else {
														
			if ( $img_type ) {
				$img_ext = $img_type;
			}
	
			$origw = $size[0];
			$origh = $size[1];
			$prop  = $origw/$origh;
			$new_h = $width/$prop;
			if( ($origw<$width && $origh<$height)) {
				$width = $origw;
				$height = $origh;
			}
			if ($new_h > $height && $proportion == 'bestfit') {
				$width = $height*$prop; 			
			}			
			
			$prefix = substr($proportion,0,1) . "_".$width."_".$height."_".$bgcolor."_";
	
			$new_file = $prefix . str_replace(array( JPATH_ROOT, ':', '/', '\\', '?', '&', '%20', ' '),  '_' ,$img_name . '.' . $img_ext);		
			
			$new_path = JPATH_BASE . $folder . $sub_folder . '/' . $new_file;
			
			$attribs = array();
			
			if(file_exists($new_path))	{
				$size = @getimagesize($new_path);
				if($size) {
					$attribs['width']  = $size[0];
					$attribs['height'] = $size[1];
				}
			} else {
		
				plgSystemThumbGallery::calculateSize($origw, $origh, $width, $height, $proportion, $newwidth, $newheight, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
	
				switch(strtolower($size['mime'])) {
					case 'image/png':
						$imagecreatefrom = "imagecreatefrompng";
						break;
					case 'image/gif':
						$imagecreatefrom = "imagecreatefromgif";
						break;
					case 'image/jpeg':
						$imagecreatefrom = "imagecreatefromjpeg";
						break;
					default:
						$errors[] = "Unsupported image type $img_name.$img_ext ".$size['mime'];
				}
	
				
				if ( !function_exists ( $imagecreatefrom ) ) {
					$errors[] = "Failed to process $img_name.$img_ext in plg_thumbgallery. Function $imagecreatefrom doesn't exist.";
				}
				
				$src_img = $imagecreatefrom($img_path);
				
				if (!$src_img) {
					$errors[] = "There was a problem to process image $img_name.$img_ext ".$size['mime'] . ' in plg_thumbgallery';
				}
				
				$dst_img = ImageCreateTrueColor($width, $height);
				
				imagefill( $dst_img, 0,0, $bgcolor);
				if ( $proportion == 'transparent' ) {
					imagecolortransparent($dst_img, $bgcolor);
				}
				
				imagecopyresampled($dst_img,$src_img, $dst_x, $dst_y, $src_x, $src_y, $newwidth, $newheight, $src_w, $src_h);		
				
				switch(strtolower($img_ext)) {
					case 'png':
						$imagefunction = "imagepng";
						break;
					case 'gif':
						$imagefunction = "imagegif";
						break;
					default:
						$imagefunction = "imagejpeg";
				}
				
				if($imagefunction=='imagejpeg') {
					$result = @$imagefunction($dst_img, $new_path, 80 );
				} else {
					$result = @$imagefunction($dst_img, $new_path);
				}
	
				imagedestroy($src_img);
				if(!$result) {				
					$errors[] = 'Could not create image:<br />' . $new_path . ' in plg_thumbgallery.<br /> Check if the folder exists and if you have write permissions:<br /> ' . dirname(__FILE__) . '/thumbs/' . $sub_folder;
				} else {
					imagedestroy($dst_img);
				}
			}
		}
		
		if (count($errors)) {
			echo implode("\n", $errors);
			return false;
		}
				
		$thenewimage=array($new_file,$img_ext,$width,$height);
		
		return $thenewimage;
    }
	
	public function calculateSize($origw, $origh, &$width, &$height, &$proportion, &$newwidth, &$newheight, &$dst_x, &$dst_y, &$src_x, &$src_y, &$src_w, &$src_h) {
		
		if(!$width ) {
			$width = $origw;
		}

		if(!$height ) {
			$height = $origh;
		}

		if ( $height > $origh ) {
			$newheight = $origh;
			$height = $origh;
		} else {
			$newheight = $height;
		}
		
		if ( $width > $origw ) {
			$newwidth = $origw;
			$width = $origw;
		} else {
			$newwidth = $width;
		}
		
		$dst_x = $dst_y = $src_x = $src_y = 0;

		switch($proportion) {
			case 'fill':
			case 'transparent':
				$xscale=$origw/$width;
				$yscale=$origh/$height;

				if ($yscale<$xscale){
					$newheight =  round($origh/$origw*$width);
					$dst_y = round(($height - $newheight)/2);
				} else {
					$newwidth = round($origw/$origh*$height);
					$dst_x = round(($width - $newwidth)/2);

				}

				$src_w = $origw;
				$src_h = $origh;
				break;

			case 'crop':

				$ratio_orig = $origw/$origh;
				$ratio = $width/$height;
				if ( $ratio > $ratio_orig) {
					$newheight = round($width/$ratio_orig);
					$newwidth = $width;
				} else {
					$newwidth = round($height*$ratio_orig);
					$newheight = $height;
				}
					
				$src_x = ($newwidth-$width)/2;
				$src_y = ($newheight-$height)/2;
				$src_w = $origw;
				$src_h = $origh;				
				break;
				
 			case 'only_cut':
				// }
				$src_x = round(($origw-$newwidth)/2);
				$src_y = round(($origh-$newheight)/2);
				$src_w = $newwidth;
				$src_h = $newheight;
				
				break; 
				
			case 'bestfit':
				$xscale=$origw/$width;
				$yscale=$origh/$height;

				if ($yscale<$xscale){
					$newheight = $height = round($width / ($origw / $origh));
				}
				else {
					$newwidth = $width = round($height * ($origw / $origh));
				}
				$src_w = $origw;
				$src_h = $origh;	
				
				break;
			}
	}
	
    public function replaceCall( $myneedle, $myreplacement, $myhaystack) {

		$myneedle = preg_quote($myneedle, '#');
		if(preg_match("#<p>(\s|<br />)*".$myneedle."(\s|<br />)*</p>#s", $myhaystack)>=1){
			$myhaystack = preg_replace( "#<p>(\s|<br />)*".$myneedle."(\s|<br />)*</p>#s", $myreplacement , $myhaystack ,1);
		}
		else{
			$myhaystack = preg_replace( "#".$myneedle."#s", $myreplacement , $myhaystack ,1);
		}
		return $myhaystack;
	}
	
    public function sortImages( $myarray, $myorder) {

		unset($theage);
		unset($thename);
		switch ($myorder) {
			case 1:
				foreach ($myarray as $key => $val) {$thename[$key]=substr(strtolower($val['filename']),0,-4);}
				array_multisort($thename, SORT_DESC, $myarray);
				break;
			case 2:
				foreach ($myarray as $key => $val) {$theage[$key]=$val['flastmod'];}
				array_multisort($theage, SORT_ASC, $myarray);
				break;
			case 3:
				foreach ($myarray as $key => $val) {$theage[$key]=$val['flastmod'];}
				array_multisort($theage, SORT_DESC, $myarray);
				break;
			case 4:
				shuffle($myarray);
				break;
			default:
				foreach ($myarray as $key => $val) {$thename[$key]=substr(strtolower($val['filename']),0,-4);}
				array_multisort($thename, SORT_ASC, $myarray);
				break;
		}
		return $myarray;
	}

    public function createFolder($fld, $func) {

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if(!JFolder::create($fld)) {
			echo "Failed creating ".$func." directory ".$fld;
			return;
		}
		$dontpassbyref="<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
		if(!JFile::write($fld."index.html", $dontpassbyref)) {
			echo "Failed creating index.html in  ".$func." directory ".$fld;
			return;
		}
	}
}