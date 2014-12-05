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

jimport('joomla.form.formfield');

class JFormFieldjvxml extends JFormField {
	
	protected $type = 'jvxml';

	protected function getInput(){
		$view =$this->element['view'];

		switch ($view){

		case 'intro':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>".JText::_('PLG_THUMBGALLERY')." Version: ".JText::_('PLG_THUMBGALLERY_VERSION')."</b><br />";
            $html.= JText::_('PLG_THUMBGALLERY_XML_TEXT_VISIT')." <a href='http://www.joomlahill.com' target='_blank'>joomlahill.com</a>";
            $html.="</div>";
		break;

		case 'gd':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>".JText::_('PLG_THUMBGALLERY_XML_TEXT_GD_LIB_LABEL')."</b> - ".JText::_('PLG_THUMBGALLERY_XML_TEXT_GD_LIB_DESC');
						if(function_exists("gd_info")){
            	$html.="<br /><br /><span style=\"color:rgb(0,102,0)\">".JText::_('PLG_THUMBGALLERY_XML_TEXT_GD_SUPPORTED_TRUE')."</span><br /><br />";
							$gd = gd_info();
							$be_gdarray=array(
										"gd" => "<span style='color:red'>unknown</span>",
										"jpg" => "<span style='color:red'>not enabled</span>",
										"png" => "<span style='color:red'>not enabled</span>",
										"gifr" => "<span style='color:red'>not enabled</span>",
										"gifw" => "<span style='color:red'>not enabled</span>");
							foreach ($gd as $k => $v) {
								if(stristr($k,"gd")!=FALSE){$be_gdarray["gd"]=$v;}
								if((stristr($k,"jpg")!=FALSE||stristr($k,"jpeg")!=FALSE)&&$v==1&&function_exists("imagecreatefromjpeg")){$be_gdarray["jpg"]="enabled";}
								if(stristr($k,"png")!=FALSE&&$v==1&&function_exists("imagecreatefrompng")){$be_gdarray["png"]="enabled";}
								if(stristr($k,"gif read")!=FALSE&&$v==1){$be_gdarray["gifr"]="enabled";}
								if(stristr($k,"gif create")!=FALSE&&$v==1&&function_exists("imagecreatefromgif")){$be_gdarray["gifw"]="enabled";}
							}
            	$html.=JText::_('PLG_THUMBGALLERY_XML_TEXT_GD_VERSION')." ".$be_gdarray["gd"]."<br />";
            	$html.=JText::_('PLG_THUMBGALLERY_XML_TEXT_JPG_SUPPORT')."  ".$be_gdarray["jpg"]."<br />";
            	$html.=JText::_('PLG_THUMBGALLERY_XML_TEXT_PNG_SUPPORT')." ".$be_gdarray["png"]."<br />";
            	$html.=JText::_('PLG_THUMBGALLERY_XML_TEXT_GIF_READ_SUPPORT')." ".$be_gdarray["gifr"]."<br />";
            	$html.=JText::_('PLG_THUMBGALLERY_XML_TEXT_GIF_CREATE_SUPPORT')."<br />";				
						}else{
            	$html.="<br /><span style='color:red'>".JText::_('PLG_THUMBGALLERY_XML_TEXT_GD_SUPPORTED_FALSE')."</span><br />";
						}
            $html.="</div>";
		break;

		}
		return $html;
	}
}