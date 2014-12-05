<?php
/**
 * @version		$Id: cincopa.php  $
 * @copyright	Copyright (C) 2010 Oren Shmulevich. All rights reserved.
 * @license		GNU/GPLv2
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Editor button
 */
class plgButtonCincopa extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param 	object $subject The object to observe
     * @param 	array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgButtonCincopa(& $subject, $config) {
        parent::__construct($subject, $config);
    }

    /**
     * Display the button
     *
     * @return array A two element array of(imageName, textToInsert)
     */
    function onDisplay($name) {
    
        $link = 'index.php?option=com_cincopa&amp;view=insert&amp;tmpl=component&amp;e_name='.$name;
//        JHTML::_('behavior.modal');
        $button = new JObject();
        $button->set('modal', true);
        $button->set('link', $link);
        $button->set('text', JText::_('Cincopa Media'));
        $button->set('name', 'picture');
        $button->set('options', "{handler: 'iframe', size: {x: 1050, y: 700}}");
        return $button;
    }
}
