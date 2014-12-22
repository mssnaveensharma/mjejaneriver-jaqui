<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

/*getting alias*/
$menu = &JSite::getMenu();
$active   = $menu->getActive();
$alias = $active->alias;

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$span = "span9";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span9";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<?php // Use of Google Font ?>
	
	<?php // Template color ?>
	
	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
	<![endif]-->


	<!-- my css -->

	 <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/style.css">
	 <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/style-slick.css">
	 <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/slicknav.css">
	  
	  	   <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
	 

	  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery-migrate-1.1.1.js"></script>
	  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.easing.1.3.js"></script>
	 
	  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.tools.min.js"></script>

	  <!-- font-awesome font -->
	  <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/font/font-awesome.css" type="text/css" media="screen">
	  <!-- fontello font -->





	   <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.slicknav.js"></script>
	  
	  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/camera.js"></script>
	  <!--[if (gt IE 9)|!(IE)]><!-->
	  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.mobile.customized.min.js"></script>

	  <!--<![endif]-->

	  <!--[if lt IE 8]>
		 <div style=' clear: both; text-align:center; position: relative;'>
			<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
			  <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
			</a>
		</div>
	 <![endif]-->
	 <!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="css/ie.css">
	 <![endif]-->
<!--<?php if($itemid == 108){ ?>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

	<script type="text/javascript">
		jQuery(document).ready(function(){		
			jQuery("#click").click(function(){
				jQuery(".downloads").slideToggle();
			});
		});
	</script>
	<?php } ?>-->




<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery('.nav').slicknav();
});
</script>


</head>

<body class="site <?php echo $option
	. ' alias-' . $alias
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ($params->get('fluidContainer') ? ' fluid' : '');
?>">
<!--button back top-->
<div id="back-top"></div>    
<div class="main">
	<div class="div-content">  

		<div class="top_section">
			<div class="container_16">
				<div class="grid_16">
					
					<div class="clear"></div>
			</div>
		</div>
	</div>

<!--==============================header=================================-->
	<div class="slider_wrapper">
          <div class="" id="camera_wrap">
              	<jdoc:include type="modules" name="banner" style="xhtml" />
              </div>
          </div>
      
	<h1>
	<?php if($itemid == 107){ ?>
		<a class="brand pull-left" href="<?php echo $this->baseurl; ?>">
			<img src="<?php echo $this->baseurl; ?>/images/logo1.jpg" alt="Peak">
			<div class="text_logo"><img src="images/logo2.png" alt="Peak1"></div>
			<?php //echo $logo; ?>
						<?php if ($this->params->get('sitedescription')) : ?>
							<?php echo '<div class="site-description">' . htmlspecialchars($this->params->get('sitedescription')) . '</div>'; ?>
						<?php endif; ?>
		</a>
		<?php }
		?>
	</h1>

		<header>
				<div class="container_16">
					 <div class="grid_16">
					 	<?php if ($this->countModules('position-1')) : ?>
							<nav class="navigation" role="navigation">
								<jdoc:include type="modules" name="position-1" style="none" />
							</nav>
						<?php endif; ?>


					 </div>
				</div>
		  </header>

		  <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> container_16">
				<div class="grid_16">
					<div class="wrapper txt_cntr">
						<?php if ($this->countModules('position-8')) : ?>
							<!-- Begin Sidebar -->
							<div id="sidebar" class="span3">
								<div class="sidebar-nav">
									<jdoc:include type="modules" name="position-8" style="xhtml" />
								</div>
							</div>
							<!-- End Sidebar -->
						<?php endif; ?>
						<main id="content" role="main" class="<?php echo $span; ?>">
							<!-- Begin Content -->
							<jdoc:include type="modules" name="position-3" style="xhtml" />
							<jdoc:include type="message" />
							<jdoc:include type="component" />
							<jdoc:include type="modules" name="position-2" style="none" />
							<!-- End Content -->
						</main>
						<?php if ($this->countModules('position-7')) : ?>
							<div id="aside" class="span3">
								<!-- Begin Right Sidebar -->
								<jdoc:include type="modules" name="position-7" style="well" />
								<!-- End Right Sidebar -->
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
	</div>
	<!-- Footer -->
	<footer class="footer" role="contentinfo">
				<div class="footer_section_up">
					<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> container_16">
						<div class="grid_16">
							<jdoc:include type="modules" name="footer" style="none" />
						</div>
					</div>
				</div>

				<div class="footer_priv">
					<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> container_16">
						<div class="grid_16">
							<jdoc:include type="modules" name="debug" style="none" />
							<!-- <a href="#top" id="back-top">
								<?php //echo JText::_('TPL_PROTOSTAR_BACKTOTOP'); ?>
							</a> -->

						</div>
					</div>
				</div>
				<!--<script src="http://code.jquery.com/jquery-1.8.3.js" type="text/javascript"></script>-->
				<script>
				jQuery(document).ready(function() {
    
    				var $item = jQuery('.alias-gallery div.wrapper.item'), //Cache your DOM selector
        			visible = 12, //Set the number of items that will be visible
        			index = 0, //Starting index
        			endIndex = ( $item.length / visible ) - 1; //End index
    
   				jQuery('.next').click(function(){
        			if(index < endIndex ){
          			index++;
          			$item.animate({'left':'-=220px'});
        		}
    			});
    
    			jQuery('.prev').click(function(){
        			if(index > 0){
         			 index--;            
          			$item.animate({'left':'+=220px'});
       			 }
    			});
    
				});
				</script>
				<!--<script type="text/javascript">
					function hello(){
						var ArrDate = document.getElementById("ArrDate").value;
						var Nts = document.getElementById("Nts").value;
						var Rooms_0 = document.getElementById("Rooms_0").value;
						var Adults_0 = document.getElementById("Adults_0").value;
						var Kids_0 = document.getElementById("Kids_0").value;
						
						//var url = "https://apps.hti-systems.com/extraordinary/blackrhino/desktop.html?locale=en_US#SearchResult:ArrDate=Dec 19, 2014,Nts="+ Nts +",RatesStartDate=Dec 19, 2014,RatesNts=14,Rooms_0="+ Rooms_0 +",Adults_0="+ Adults_0 +",Kids_0="+ Kids_0 +"";
						//window.location.href = url;
					}
				</script>-->
				<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
					<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
					<script>
						jQuery(function() {
						jQuery( ".dateBoxPopup" ).datepicker({
						dateFormat: 'dd-mm-yy'
						});
					});
					</script>
		
	</footer>
</div>








</body>
</html>
