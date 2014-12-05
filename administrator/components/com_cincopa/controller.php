<?php
/**
 * @version		$Id: controller.php $
 * @package		Joomla
 * @copyright	Copyright (C) 2010 Oren Shmulevich. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Media Manager Component Controller
 *
 * @package		Joomla
 * @version 1.5
 */
class CincopaController extends JControllerLegacy
{
	function plugin_ver() { return 'jm2.44'; }
	function cincopa_url() { return 'http://www.cincopa.com'; }
	function selfURL()
	{
		$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";

//		$protocol = strleft ( strtolower ( $_SERVER ["SERVER_PROTOCOL"] ), "/" ) . $s;

		$protocol =  strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
		$protocol =  substr($protocol, 0, strpos($protocol, "/"));
		$protocol .= $s;
		
		$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
		$ret = $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];

		return $ret;
	}


	/**
	 * Display the view
	 */
	function display($cachable = false, $urlparams = array())
	{
		if (!strpos($_SERVER['REQUEST_URI'], '&wrt='))
		{
			header('Location: '.$this->cincopa_url().'/media-platform/start.aspx?ver='.$this->plugin_ver().'&rdt='.urlencode($this->selfURL().'&'));
			exit;
		}

?>
Please Wait...

<script type="text/javascript">

	function insertPageTag(tag)
	{
		window.parent.jInsertEditorText(tag, 'jform_articletext');
		window.parent.SqueezeBox.close();
		return false;
	}

	function cincopa_stub()
	{
		var i = location.href.indexOf("&wrt=");

		if (i > -1)
		{
			insertPageTag(unescape(location.href.substring(i+5)))
		}

	}

	window.onload = cincopa_stub;

</script>


<?php
	}
}
