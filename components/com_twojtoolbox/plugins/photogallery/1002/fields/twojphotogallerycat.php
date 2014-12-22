<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');
JFormHelper::loadFieldClass('TwoJTextArea');


JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class JFormFieldTwoJPhotoGalleryCat extends JFormFieldTwoJTextArea{
	protected $type = 'TwoJPhotoGalleryCat';

	protected function getLabel(){
		if( !$this->form->getValue('id') ) return '';
		
		$type_plugin = $this->form->getValue('type');
		if(!$type_plugin) return false;
		$plugin_info  = TwojToolboxHelper::plugin_info( $type_plugin, 1);
	
		$twoj_add_js_field = JRequest::getVar('twoj_add_js_field', array(), '', 'array');
		$twoj_add_js_field[] = 'ui.mouse';
		$twoj_add_js_field[] = 'ui.sortable';
		$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.sort';
		$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.field';
		JRequest::setVar('twoj_add_js_field', $twoj_add_js_field );
		
		$twoj_add_css_field = JRequest::getVar('twoj_add_css_field', array(), '', 'array');
		$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.admin.sort';
		JRequest::setVar('twoj_add_css_field', $twoj_add_css_field );
		
		return parent::getLabel();
	}
	
	protected function getInput(){
		$id = $this->form->getValue('id');
		
		if( !$id ) return "<br /><strong>Please save instance, and you'll be able to edit category after that</strong>";
		
		$JSONString = $this->value;
		
	
		$ret_html = ' <div id="twojCategoryNameDiv"><div class="twojtoolbox_loading_small"></div> </div><button id="twojCategoryEdit">'.JText::_('Categories Manager').'</button>';
		
		$ret_html .= '<div id="twojCategoryEditDialog" title="'.JText::_('Categories Manager').'"><p>'
			.JText::_('Categories manager implemented based on drag and drop technology.').'<br />'
			.JText::_('You can drag and drop category items to build categories tree with multi level structure.')
			.'</p><div id="twojCategoryMainDiv"><div id="twojCategoryContent"><div class="twojtoolbox_loading_small"></div></div></div>'
			.'</div>';
	
		$ret_html .= '<div style="display: none;" >'.parent::getInput().'</div>';
		return $ret_html;
	}
	
	protected function setXmlVal($title, $value){
		if( TJTB_JVERSION_FULL=='3.2') $this->$title = $value; 
			else  $this->element[$title] = $value;
	}
	
	
}