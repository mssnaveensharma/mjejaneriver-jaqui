<div class='picture-container'>

<div id="gallery" class="content">
<div id="controls" class="controls"></div>
<div class="slideshow-container">
<div id="loading" class="loader"></div>
<div id="slideshow" class="slideshow"></div>
</div>
<div id="caption" class="caption-container"></div>
</div>
<div id="thumbs" class="navigation">
<ul class="thumbs noscript">
<?php
foreach( $this->hotel->pictures as $picture ){
	?>
						<li>
						<a class="thumb" name="leaf" href="<?php echo JURI::root().PATH_PICTURES.$picture->hotel_picture_path?>" alt="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>" title="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>">
							<img 
									class="img_picture"
									style="height: 50px"
									src='<?php echo JURI::root().PATH_PICTURES.$picture->hotel_picture_path?>' alt="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>"  title="<?php echo isset($picture->hotel_picture_info)?$picture->hotel_picture_info:'' ?>"/>
						</a>
						<div class="caption">
								<div class="download">
									<a href="">Download Original</a>
								</div>
								<div class="image-title">Title #0</div>
								<div class="image-desc">Description</div>
							</div>
					<?php
					}
					?>
						
					</ul>
				</div>
				<div style="clear: both;"></div>
			</div>
			
			
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// We only want these styles applied when javascript is enabled
				jQuery('div.navigation').css({'margin' : ' 0 10px', 'float' : 'left'});
				jQuery('div.content').css('display', 'block');

				// Initially set opacity on thumbs and add
				// additional styling for hover effect on thumbs
				var onMouseOutOpacity = 0.67;
				jQuery('#thumbs ul.thumbs li').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				
				// Initialize Advanced Galleriffic Gallery
				var gallery = jQuery('#thumbs').galleriffic({
					delay:                     2500,
					numThumbs:                 18,
					preloadAhead:              10,
					enableTopPager:            false,
					enableBottomPager:         true,
					maxPagesToShow:            7,
					imageContainerSel:         '#slideshow',
					controlsContainerSel:      '#controls',
					captionContainerSel:       '#caption',
					loadingContainerSel:       '#loading',
					renderSSControls:          true,
					renderNavControls:         true,
					playLinkText:              '<?php echo JText::_('LNG_PLAY_SLIDESHOW',true);?>',
					pauseLinkText:             '<?php echo JText::_('LNG_PAUSE_SLIDESHOW',true);?>',
					prevLinkText:              '&lsaquo; <?php echo JText::_('LNG_PREVIOUS_PHOTO',true);?>',
					nextLinkText:              '<?php echo JText::_('LNG_NEXT_PHOTO',true);?> &rsaquo;',
					nextPageLinkText:          'Next &rsaquo;',
					prevPageLinkText:          '&lsaquo; Prev',
					enableHistory:             false,
					autoStart:                 false,
					syncTransitions:           true,
					defaultTransitionDuration: 900,
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of jQuery('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						this.fadeTo('fast', 1.0);
					}
				});
			});
		</script>
	