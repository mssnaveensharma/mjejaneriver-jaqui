<?php
/**
 * @package Blueimp lightbox for Joomla! 3.x
 * @version $Id: plbueimp.php
 * @author Nter webdesign
 * @copyright (C) 2014- Nter webdesign
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/


defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


class plgContentBlueimp extends JPlugin {

  	public function onContentAfterDisplay($context, &$article, &$params) {
		return $this->blueimp($article);
	}

	private function blueimp($article) {
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/plugins/content/blueimp/css/blueimp-gallery.min.css');
		$document->addScript(JURI::root(true).'/plugins/content/blueimp/js/blueimp-gallery.min.js');

		if ($this->params->get('show-title') === '1') {
			$title = "<h3 class=\"title\"></h3>";
		}

		if ($this->params->get('show-autoplay') === '1') {
			$playpause = "<a class=\"play-pause\"></a>";
		}

		return "
		<div id=\"blueimp-gallery\" class=\"blueimp-gallery ".$this->params->get('show-controls')."\">
			<div class=\"slides\"></div>
			".$title."
			<a class=\"prev\">‹</a>
			<a class=\"next\">›</a>
			<a class=\"close\">×</a>
			".$playpause."
			<ol class=\"indicator\"></ol>
		</div>
		<script>
		document.getElementById('".$this->params->get('divid')."').onclick = function (event) {
		    event = event || window.event;
		    var target = event.target || event.srcElement,
		        link = target.src ? target.parentNode : target,
		        options = {index: link, event: event},
		        links = this.getElementsByTagName('a');
		    blueimp.Gallery(links, options);
		};
		</script>
		";
	}

}

?>