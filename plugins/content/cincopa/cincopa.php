<?php
/**
 * @version     $Id: cincopa.php $
 * @copyright   Copyright (C) 2010 Oren Shmulevich. All rights reserved.
 * @license     GNU/GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');



class plgContentCincopa extends JPlugin {





    ///// Content plugin API interface starts here



    /**

     * Constructor

     *

     * For php4 compatability we must not use the __constructor as a constructor for plugins

     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.

     * This causes problems with cross-referencing necessary for the observer design pattern.

     *

     * @param object $subject The object to observe

     * @param object $params  The object that holds the plugin parameters

     * @param int    $special Used internally

     * @since 1.5

     */

    function plgContentCincopa(& $subject, $params, $special = 0) {

        parent::__construct($subject, $params);

    }



    /**

     * Main prepare content method

     * Method is called by the view

     *

     * @param       object          The article object.  Note $article->text

     *                              is also available

     * @param       object          The article params

     * @param       int             The 'page' number

     */

    function onContentPrepare($context, &$article, &$params, $limitstart=0)

    {

		$article->text = preg_replace_callback('/\[(.*?)cincopa(.+?)\]/', 'cincopa_plugin_callback', $article->text);

//		$article->text = preg_replace_callback('/\{cincopa ([[:print:]]+?)\}/', 'cincopa_plugin_callback', $article->text);

    }

    

  

}



	function cincopa_plugin_callback($match)

	{

		$uni = uniqid('');



		$ret = '

<div id="_cp_widget_'.$uni.'"><img src="http://www.cincopa.com/media-platform/runtime/loading.gif" style="border:0;"/></div>

<script src="http://www.cincopa.com/media-platform/runtime/libasync.js" type="text/javascript"></script>

<script type="text/javascript">

cp_load_widget("'.urlencode(trim(html_entity_decode(strip_tags($match[0])))).'", "_cp_widget_'.$uni.'");

</script>

	';



		return $ret;

	}  