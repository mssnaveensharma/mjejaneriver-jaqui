<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function getBookingExtName()
{
	$componentname = JRequest::getVar('option');
	//$componentname = explode("_",$componentname);
	return $componentname;
}

function dmp( $text )
{
	echo "<pre>";
	var_dump($text);
	echo "</pre>";
}


class JHotelUtil{

	var $applicationSettings ;
	
	private function __construct()
	{
	
	}
	
	public static function getInstance()
	{
		static $instance;
		if ($instance === null) {
			$instance = new JHotelUtil();
		}
		return $instance;
	}
	
	public static function getApplicationSettings(){
		$instance = JHotelUtil::getInstance();
		if(!isset($instance->applicationSettings)){
			$db		=JFactory::getDBO();
			$query	= "	SELECT * FROM #__hotelreservation_applicationsettings fas
						inner join  #__hotelreservation_date_formats df on fas.date_format_id=df.id
						";
			$db->setQuery( $query );
			$instance->applicationSettings =  $db->loadObject();
		}
		return $instance->applicationSettings;
	}
	
	public static function getDefaultCurrency(){
		$instance = JHotelUtil::getInstance();
		if(!isset($instance->applicationSettings)){
			$instance->applicationSettings = self::getApplicationSettings();
		}
		return $instance->applicationSettings;
	}
	
	public static function loadAdminLanguage(){
		$user 	=JFactory::getUser();
		$db 	= JFactory::getDBO();
		
		//languages
		$language 		= JFactory::getLanguage();
		/*if( JRequest::getVar( '_lang') !='' ){
			$lng_tmp = JRequest::getVar( '_lang');
			$query = ' UPDATE #__hotelreservation_users set _lang = "'.$lng_tmp.'" WHERE user_id = '.$user->id;
			$db->setQuery($query);
			$db->query();
		
		}
		$query = ' SELECT _lang FROM #__hotelreservation_users WHERE user_id='.$user->id;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		if( count($list) > 0 )
		{
			if( isset($language->_lang) )
				$language->_lang = $list[0]->_lang;
			else
				$language->setLanguage( $list[0]->_lang );
		}
		else
		{
			$query = ' INSERT INTO #__hotelreservation_users(user_id,_lang) VALUES( '.$user->id.', "'.$language->getTag().'")';
			$db->setQuery($query);
			$db->query();
		}*/
		
		$language_tag 	= isset($language->_lang) ? $language->_lang : $language->getTag();
		JRequest::setVar('_lang',$language_tag);
		
		// dmp($user);
		
		$x = $language->load(
								'com_installer' ,
					dirname(JPATH_ADMINISTRATOR.DS.'language') ,
					$language_tag,
					true
		);
		
		$x = $language->load(
				'com_jhotelreservation' ,
				dirname(JPATH_COMPONENT_ADMINISTRATOR. DS.'language') ,
				$language_tag,
				true
		);
	}
	
	public static function loadSiteLanguage(){
		$language 		= JFactory::getLanguage();
		
		// dmp($language->_lang);
	
		$language_tag 	= JRequest::getVar( '_lang' );
		if($language_tag==""){
			$language_tag = isset($language->_lang) ? $language->_lang : $language->getTag();
			JRequest::setVar('_lang',$language_tag);
		}
		$x = $language->load(
				'com_jhotelreservation' ,
				dirname(JPATH_COMPONENT_ADMINISTRATOR.DS.'language') ,
				$language_tag,
				true
		);
		$x = $language->load(   'com_users' ,
				dirname( JPATH_SITE.DS.'language') ,
				$language_tag,
				true
		);	
		
		JRequest::setVar( 'language_tag',$language_tag);
		
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
	}
	
	public static function loadClasses(){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		
		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment'.DS.'processors';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
		
		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
		
		//load services
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'services';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
	}
	public static function getJoomlaLanguage(){
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$tagArray = explode("-",$language_tag);
		return $tagArray[0];
	}
	
	public static function getStringIDConfirmation($confirmationId)
	{
		return str_pad($confirmationId, LENGTH_ID_CONFIRMATION, "0", STR_PAD_LEFT);
	}
	
	public static function secretizeCreditCard($creditCardNumber){
		$ex = $creditCardNumber;
	
		if( strlen($ex) <= 4 )
		{
			for( $i =0; $i < strlen($ex); $i++ )
			{
				$cc = $cc."".str_repeat("X", strlen($ex[$i]));
				if( $i < count($ex)-1 )
				$cc = $cc. "-";
			}
			$creditCardNumber = $cc;
		}
		else
		$creditCardNumber = str_repeat("*", strlen($creditCardNumber)-4).substr($creditCardNumber,-4);
	
		return $creditCardNumber;
	}
	public static function showUnavailable(){
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option='.getBookingExtName().'&task=hotelsettings.showUnavailable'),"");
	}	
	public static function getDashBoardIcon(){
		if(self::isJoomla3())
			return "home";
		else 
			return "back.png";
	}
	
	public static function getEmailDefaultIcon(){
		if(self::isJoomla3())
			return "generic.png";
		else
			return "default.png";
	}
	
	public static function getExportIcon(){
		if(self::isJoomla3())
			return "download";
		else
			return "upload.png";
	}
	
	
	public static function getCoordinates($zipCode){
		try{
			if(empty($zipCode)){
				return null;
			}
			$url ="http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($zipCode);
			$data = file_get_contents($url);
			$search_data = json_decode($data);
			if(empty($search_data->results[0]->geometry->location->lat)){
				return null;
			}
			$lat =  $search_data->results[0]->geometry->location->lat;
			$lng =  $search_data->results[0]->geometry->location->lng;
		
		
			$location =  array();
			$location["latitude"] = $lat;
			$location["longitude"] = $lng;
		
			return $location;
		}
		catch(Exception $e){
			
		}
		
		return null;
	}
	
	//included functions
	

	public static function my_round($x,$decimals=2)
	{
		if ($x < 0)
			$semn=-1;
		else
			$semn = 1;
	
		$fuzzy = 0.0000001;
		return round(abs($x) + $fuzzy, $decimals)*$semn;
	}
	
	public static function checkIndexKey( $table_name, $arr_fields, $key_autoincrement, $current_id )
	{
		$conditions = '';
	
		foreach( $arr_fields as $key => $value )
		{
			if( $conditions != '' )
				$conditions .= ' AND ';
			$conditions .= " $key = '$value' ";
		}
		$db		=JFactory::getDBO();
		$query	= "
		SELECT *
		FROM $table_name
		WHERE
		1
		".( strlen($conditions)>0? " AND $conditions " : "") ."
		".($current_id !=''? " AND $key_autoincrement <> $current_id" : "")."
		";
	
		$db->setQuery( $query );
		if (!$db->query() )
			{
			JError::raiseWarning( 500, JText::_('LNG_UNKNOWN_ERROR',true) );
			return true;
		}
		return  $db->getNumRows() > 0;
	}
	
	public static function fmt($val, $decimals=2 )
	{
		// romanian format
		if( !isset($val)|| !is_numeric($val) )
			$val = 0;
		return number_format($val, $decimals);//, ',', '.');
	}
	
	public static function makePathFile($path){
		$path_tmp = str_replace( '\\', DIRECTORY_SEPARATOR, $path );
		$path_tmp = str_replace( '/', DIRECTORY_SEPARATOR, $path_tmp);
		return $path_tmp;
	}

	public static function getCurrentJoomlaVersion()
	{
		$version = new JVersion;
		$version = new JVersion;
		return $version->RELEASE + 0.00;
	}
	
	public static function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
		$ret = array();
	
		foreach ( (array) $data as $k => $v ) {
			if ( $urlencode)
				$k = urlencode($k);
			if ( is_int($k) && $prefix != null )
				$k = $prefix.$k;
			if ( !empty($key) )
				$k = $key . '%5B' . $k . '%5D';
			if ( $v === NULL )
				continue;
			elseif ( $v === FALSE )
				$v = '0';
		
			if ( is_array($v) || is_object($v) )
				array_push($ret,JHotelUtil::_http_build_query($v, '', $sep, $k, $urlencode));
			elseif ( $urlencode )
				array_push($ret, $k.'='.urlencode($v));
			else
				array_push($ret, $k.'='.$v);
		}
		if ( NULL === $sep )
			$sep = "|";

		return implode($sep, $ret);
	}
	
	public static function convertToFormat($date){
		if(!isset($date) || $date=='' || strcmp($date,"0000-00-00")==0)
			return $date;
	
			$appSettings = self::getApplicationSettings();
			$date = date($appSettings->dateFormat, strtotime($date));
			return $date;
	}

	public static function convertToMysqlFormat($date){
	if(!isset($date) || $date=='')
		return $date;
		$date = date("Y-m-d", strtotime($date));
		return $date;
	}
	
	public static function getDateGeneralFormat($data){
	
		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;

		$data =strtotime($data);
		$appSettings= self::getApplicationSettings();
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();

		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');

		switch ($appSettings->dateFormat){
			case "Y-m-d":
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$dateS =  strftime("%Y %B %#d", $data);
				else
					$dateS =  strftime("%Y %B %e", $data);
				break;
			case "d-m-Y":
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$dateS =  strftime("%A, %#d %B, %Y", $data);
				else
					$dateS =  strftime("%A, %e %B, %Y", $data);
				break;		
			case "m/d/Y":
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$dateS =  strftime("%A, %B %#d, %Y", $data);
				else
					$dateS =  strftime("%A, %B %e, %Y", $data);
				break;		
		}
	
		return $dateS;
	}
	

	
	
	public static function getDateGeneralFormatDay($data){

		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;

		$data =strtotime($data);

		$appSettings= self::getApplicationSettings();
		setlocale(LC_TIME, $appSettings->date_language/*.'.UTF-8'*/);
		if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
		$dateS =  strftime("%A %#d", $data);
				else
		$dateS =  strftime("%A %e", $data);

		return $dateS;
	}
	
	
	public static function getDateGeneralFormatWithTime($data){
		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;
	
		$data =strtotime($data);
		$dateS = date( 'j M Y  G:i:s', $data );
	
		return $dateS;
	}
	
	public static function truncate($text, $length, $suffix = '&hellip;', $isHTML = true){
		$i = 0;
		$tags = array();
		if($isHTML){
		preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		foreach($m as $o){
			if($o[0][1] - $i >= $length)
				break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				if($t[0] != '/')
					$tags[] = $t;
					elseif(end($tags) == substr($t, 1))
					array_pop($tags);
					$i += $o[1][1] - $o[0][1];
			}
		}
	
		$output = substr($text, 0, $length = min(strlen($text),  $length + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
	
		// Get everything until last space
		$one = substr($output, 0, strrpos($output, " "));
		// Get the rest
		$two = substr($output, strrpos($output, " "), (strlen($output) - strrpos($output, " ")));
			// Extract all tags from the last bit
			preg_match_all('/<(.*?)>/s', $two, $tags);
			// Add suffix if needed
			if (strlen($text) > $length) {
			$one .= $suffix;
		}
			// Re-attach tags
			$output = $one . implode($tags[0]);
	
		return $output;
	}
	
	public static function getAvailabilityCalendar($hotelId, $month, $year, $rooms, $nrDays=2, $identifier, $loading = false){
	
		/* draw table */
		$calendar = '<table cellpadding="0" cellspacing="0" class="availability-calendar">';

		$appSettings= self::getApplicationSettings();
		setlocale(LC_TIME, $appSettings->date_language/*.'.UTF-8'*/);

		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
	
		/* table headings */
		for($i=1;$i<8;$i++)
			$headings [] = strftime("%a ", mktime(0,0,0,3,28,2009)+$i * (3600*24));
		
		$calendar.= '<tr><td colspan="7" align="center"><table align="center" class="room-calendar-header"><tr>';
		$calendar.= '<td><a href="javascript:void(0)" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month-1,1,$year)).',\''.$identifier.'\')"><div class="arrow-left"></div></a></td><td>'. strftime("%B", mktime(0,0,0,$month,1,$year)).'</td><td><a href="javascript:void(0)" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month+1,1,$year)).',\''.$identifier.'\')"><div class="arrow-right"></div></a></td>';
		$calendar.= '<td><a href="javascript:void(0)" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month,1,$year-1)).',\''.$identifier.'\')"><div class="arrow-left"></div></a></td><td>'.$year.'</td><td><a href="javascript:void(0)" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month,1,$year+1)).',\''.$identifier.'\')"><div class="arrow-right"></div></a></td>';
					$calendar.= '</tr></table></td></tr>';
		
					$calendar.=  '<tr><td colspan="7">';
					$calendar.=  '<div id="loader-'.$identifier.'" class="room-loader" style="display:'.($loading?'block':'none').'"></div>';
		
		$calendar.= '<table id="room-calendar-'.$identifier.'" style="display:'.($loading?'none':'block').'">';
		
					$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
		
					/* days and weeks vars now ... */
					$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
					$days_in_this_week = 1;
					$day_counter = 0;
					$dates_array = array();
		
						/* row for week one */
		$calendar.= '<tr class="calendar-row">';
	
		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++){
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		}
	
		$appSetings = self::getApplicationSettings();
		$dateFormat = $appSetings->dateFormat;
		/* keep going with days.... */
		if(!$loading){
			for($list_day = 1; $list_day <= $days_in_month; $list_day++){
				$calendar.= '<td class="calendar-day">';
				/* add in the day number */
				
				$startDate = date($dateFormat,mktime(0,0,0,$month,$list_day,$year));
				$endDate = date($dateFormat,mktime(0,0,0,$month,$list_day+$nrDays,$year));
		
				$currentMonthDay = date('j');
				if(($list_day<($currentMonthDay) && $month==date('n') && $year==date('Y')) || ($month<date('n')&& $year==date('Y'))|| $year<date('Y'))
					$rooms[$list_day-1]["isAvailable"] = false;
				
				if($rooms[$list_day-1]["isAvailable"]){
					$priceClass=$rooms[$list_day-1]["price"]>1000?"small":"";
					$calendar.='<div class="day-cell" onclick="selectCalendarDate('.$hotelId.',\''.$startDate.'\',\''.$endDate.'\');">
					<div class="date">
							<div class="date '.($rooms[$list_day-1]["isAvailable"]==false?'not-available':'').'">'.sprintf('%02s',$list_day).'/'.sprintf('%02s',$month).'</div>
							<div class="price '.$priceClass.'">'.$rooms[$list_day-1]["price"].'</div>
					</div>
					</div>';
				}else{
					$calendar.='<div class="day-cell not-available" >
					<div class="date">
					<div class="date not-available">'.sprintf('%02s',$list_day).'/'.sprintf('%02s',$month).'</div>
					<div class="price">'.$rooms[$list_day-1]["price"].'</div>
					</div>
					</div>';
				}
		
				$calendar.= '</td>';
				if($running_day == 6){
					$calendar.= '</tr>';
					if(($day_counter+1) != $days_in_month){
						$calendar.= '<tr class="calendar-row">';
					}
					$running_day = -1;
					$days_in_this_week = 0;
				}
				$days_in_this_week++; $running_day++; $day_counter++;
			}	
		}
					/* finish the rest of the days in the week */
		if($days_in_this_week < 8){
			for($x = 1; $x <= (8 - $days_in_this_week); $x++){
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			}
		}
	
		/* final row */
		$calendar.= '</tr>';
		$calendar.= '</table></td></tr>';
		$calendar.= '<tr><td>'.JText::_('LNG_ROOM_CALENDAR_INFO',true).'</td></tr>';
		
		$calendar.= '<tr>';
		$calendar.= '<td colspan="10">';
		$calendar.= '<div class="legend"><div class="available"></div>'.JText::_('LNG_AVAILABLE',true).'</div>';
		$calendar.= '</td>';
		$calendar.= '</tr>';
		$calendar.= '<tr>';
		$calendar.= '<td colspan="10">';
		$calendar.= '<div class="legend"><div class="not-available"></div>'.JText::_('LNG_NOT_AVAILABLE',true).'</div>';
		$calendar.= '</td>';
		$calendar.= '</tr>';
		/* end the table */
		$calendar.= '</table>';
	
		/* all done, return result */
		return $calendar;
	}
	
	public static function getHotelLink($hotel){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		
		$conf = JFactory::getConfig();
		$index ="";
		
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
		
		
		$hotelName = stripslashes(strtolower($hotel->hotel_name));
		$hotelName = str_replace(" ", "-", $hotelName);
				
		$url = JURI::base().$index."hotel-".$hotelName;
		
		return $url;
	}
	
	public static function getRoomLink($hotel){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		
		$conf = JFactory::getConfig();
		$index ="";
		
		$hotelName = stripslashes(strtolower($hotel->hotel_name));
		$hotelName = str_replace(" ", "-", $hotelName);
	
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
		$url = JURI::base().$index."hotel-".$hotelName."?rm_id=".$hotel->room_id;
		
		return $url;
	}
	
	public static function getExcursionLink($hotel){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		
		$conf = JFactory::getConfig();
		$index ="";
		
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
		
		$hotelName = stripslashes(strtolower($hotel->hotel_name));
		$hotelName = str_replace(" ", "-", $hotelName);
	
		$url = JURI::base().$index."hotel-".$hotelName."?rm_id=".$hotel->id;
		
		//dmp($url);
		return $url;
	}
	
	public static function getOfferLink2($offer, $mediaReferer=null, $voucher=nul){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		$containsIndex = strpos($path, "index.php");
		$path = substr($path, 0, strpos($path,"/"));
	
		$offerName = stripslashes(strtolower($offer->offer_name));
		$offerName = str_replace(" ", "-", $offerName);
		if($containsIndex!==false){
			$url = "index.php/"."hotelarrangement-".$offerName;
		}
		else
			$url = ""."hotelarrangement-".$offerName;
		//$url = $current."/".$path."/hotelarrangement-".$offerName;
		//$url = $current."/hotelarrangement-".$offerName;
	
		if(!empty($mediaReferer) || !empty($voucher)){
			$url = $url."?";
			$isMediaSet = false;
			if(!empty($mediaReferer)){
				$url.="mediaReferer=".$mediaReferer;
				$isMediaSet = true;
			}
	
			if(!empty($voucher)){
				if($isMediaSet){
					$url .= "&";
				}
				$url.="voucher=".$voucher;
			}
		}
		return $url;
	}
	
	public static function getOfferLink($offer, $mediaReferer=null, $voucher=null){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		$containsIndex = strpos($path, "index.php");
		$path = substr($path, 0, strpos($path,"/"));
	
		$city = stripslashes(strtolower($offer->hotel_city));
		$city = str_replace(" ", "-", $city);
	
		if($containsIndex!==false){
			$url = "index.php/"."hotelarrangement-".$city."-".$offer->offer_id;
		}
		else
			$url = ""."hotelarrangement-".$city."-".$offer->offer_id;
		//$url = $current."/hotelarrangement-".$city."-".$offer->offer_id;
		if(!empty($mediaReferer) || !empty($voucher)){
			$url = $url."?";
			$isMediaSet = false;
			if(!empty($mediaReferer)){
				$url.="mediaReferer=".$mediaReferer;
				$isMediaSet = true;
			}
	
			if(!empty($voucher)){
				if($isMediaSet){
					$url .= "&";
				}
				$url.="voucher=".$voucher;
			}
		}
	
	
		return $url;
	}
	
	public static function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) {
	
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
	
		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
	
	public static function getNumberOfDays($startData, $endDate){
	
		$nrDays = ceil((strtotime($endDate) - strtotime($startData)) / (60 * 60 * 24));
	
		return $nrDays;
	}
	
	public static function isJoomla3(){
		$version = new JVersion();
		$versionA =  explode(".", $version->getShortVersion());
		if($versionA[0] =="3"){
			return true;
		}
		return false;
	}
	
	public static function includeFile($type, $file, $path){
		$version = new JVersion();
		$versionA =  explode(".", $version->getShortVersion());
		if($versionA[0] =="3"){
			JHTML::_($type, $path.$file);
		}else{
			JHTML::_($type, $file, $path);
		}
	}
	
	
}

?>
