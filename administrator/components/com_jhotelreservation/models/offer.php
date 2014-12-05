<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
require_once "hoteltranslations.php";
jimport('joomla.application.component.modeladmin');

/**
 * Menu Item Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jhotelreservation
 * @version		1.6
 */
class JHotelReservationModelOffer extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_OFFER';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.offer';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Room', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void

	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = (int) JRequest::getInt('offer_id');
		if(!$pk)
			$pk = (int) JRequest::getInt('id');
		$this->setState('offer.offer_id', $pk);
	
		if (!($hotelId = $app->getUserState('com_jhotelreservation.edit.offer.hotel_id'))) {
			$hotelId = JRequest::getInt('hotel_id', '0');
			//dmp("a: ".$hotelId);
		}
		
		//dmp($hotelId);
		
		//$app->setUserState('com_jhotelreservation.edit.room.hotel_id',$hotelId); 
		$app->setUserState('com_jhotelreservation.edit.offer.offer_id',$pk);
	
		$this->setState('offer.hotel_id', $hotelId);
	} 

	/**
	 * Method to get a menu item.
	 *
	 * @param	integer	The id of the menu item to get.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('offer.offer_id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable('ManageOffers','Table');
		
		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		if (!empty($table->offer_id)) {
			$this->setState('offer.hotel_id', $table->hotel_id);	
		}
		
		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');
		
		
		$value->itemRooms = $this->getOfferRooms(); 
		$value->itemExcursions = $this->getOfferExcursions($this->getState('offer.offer_id'));
		
		
		
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes ORDER BY name';
		$value->themes = $this->_getList( $query );

		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes_relation where offerId='.$this->getState('offer.offer_id');
		$value->selectedThemes = $this->_getList( $query );
		
		$query = " SELECT * FROM #__hotelreservation_offers_vouchers where offerId = ".$this->getState('offer.offer_id')." ORDER BY voucher";
		$value->vouchers = $this->_getList( $query );
		
		$value->offer_datas		= JHotelUtil::convertToFormat($value->offer_datas);
		$value->offer_datae		= JHotelUtil::convertToFormat($value->offer_datae);
		$value->offer_datasf		= JHotelUtil::convertToFormat($value->offer_datasf);
		$value->offer_dataef		= JHotelUtil::convertToFormat($value->offer_dataef);
		
		return $value;
	}
	
	/**
	 * Method to get the menu item form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jhotelreservation.room', 'room', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.room.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */

	function store($data)
	{
		// dmp($data);
		// exit;
	
		$data['offer_datas']=JHotelUtil::convertToMysqlFormat($data['offer_datas']);
		$data['offer_datae']=JHotelUtil::convertToMysqlFormat($data['offer_datae']);
		$data['offer_datasf']=JHotelUtil::convertToMysqlFormat($data['offer_datasf']);
		$data['offer_dataef']=JHotelUtil::convertToMysqlFormat($data['offer_dataef']);
	
		try
		{
			//$this->_db->BeginTrans();
			$query = "START TRANSACTION;";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
	
			$row = $this->getTable('ManageOffers','Table');
			$data['offer_order'] =$row->getOfferOrder();
			//dmp($data);
			//exit;
			// Bind the form fields to the table
			if (!$row->bind($data))
			{
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}
			// Make sure the record is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}
				
			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError( $this->_db->getErrorMsg() );
				throw( new Exception($this->_db->getErrorMsg()) );
				return false;
			}
				
			if( $data['offer_id'] =='' || $data['offer_id'] ==0 || $data['offer_id'] ==null ){
				$data['offer_id'] = $this->_db->insertid();
			}
			$this->setState('offer.offer_id', $data['offer_id']);
			$this->_offer_id = $data['offer_id'];
	
			$this->storeVouchers($data);
			$this->storePictures($data);
			$this->storeRooms($data);
			$this->storeRate($data);
			$this->storeThemes($data);
			
				
			//store extra options
			$this->storeExtraOptions($this->_offer_id, $data["extra_options_ids"]);
			$this->storeOfferExcursions($this->getState('offer.offer_id'),$data["excursion_ids"]);

			
			$query = "COMMIT";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
		}
		catch( Exception $ex )
		{
			dmp($ex);
			// exit;
			//$this->_db->RollbackTrans();
			$query = "ROLLBACK";
			$this->_db->setQuery($query);
			$this->_db->queryBatch();
			return false;
		}
		return true;
	}
	
	function storeRate($data){

		//exit;
		//room discounts
		$discount_ids 	= array();
		
		foreach( $data['rooms'] as $valueRoom )
		{
		//dmp( $valueRoom);
			if( count( $valueRoom['offer_price'] ) > 0 )
			{
		
				$offer_price = $valueRoom['offer_price'];
				$offer_price['offer_id'] = $data['offer_id'];
				$offer_price['room_id'] = $valueRoom['room_id'];
				$offer_price['id'] =$offer_price['offer_room_rate_id'];
		
				dmp($offer_price);
				//exit;
				$row = $this->getTable('OfferRate','Table');
		
				//dmp($offer_price);
				//exit;
				if (!$row->bind($offer_price))
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				// Make sure the record is valid
				if (!$row->check())
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
		
				// Store the web link table to the database
				if (!$row->store())
				{
					dmp($this->_db->getErrorMsg());
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
		
				$discount_ids[] = $this->_db->insertid();
			}
				
		}
		
	}
	
	function storeThemes($data){
		//prepare themes
		//dmp($data['themes']);
		$query = " DELETE FROM #__hotelreservation_offers_themes_relation
												WHERE offerId = ".$data['offer_id'];
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		print_r($data['themes']);
		foreach( $data['themes'] as $theme ){
				
			$row = $this->getTable('ManageOffersThemesRelation','Table');
			$themeRelation->offerId= $data['offer_id'];
			$themeRelation->themeId= $theme;
			//dmp($facilityRelation);
			if (!$row->bind($themeRelation))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
		
			}
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
				
			// Store the web link table to the database
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
				
		}
	}
	
	function storeRooms($data){
		//room
		$offer_room_ids 	= array();
		foreach( $data['rooms'] as $value )
		{
			$row = $this->getTable('ManageOfferRooms','Table');
		
			// dmp($key);
			$offer_room										= new stdClass();
			$offer_room->offer_id 							= $data['offer_id'];
			$offer_room->room_id 							= $value['room_id'];
		
			if (!$row->bind($offer_room))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
		
			}
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		
			// Store the web link table to the database
			if (!$row->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		
			$offer_room_ids[] = $this->_db->insertid();
		}
		
		
		$query = " DELETE FROM #__hotelreservation_offers_rooms
						WHERE offer_id = '".$data['offer_id']."'
						".( count($offer_room_ids)> 0 ? " AND offer_room_id NOT IN (".implode(',', $offer_room_ids).")" : "");
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}
	function storeVouchers($data){
		//update vouchers
		
		if($data["processVouchers"]==1){
			$this->_db->setQuery (	" DELETE FROM #__hotelreservation_offers_vouchers
					WHERE offerId = $this->_offer_id");
			if (!$this->_db->query() )
			{
				// dmp($db);
				$ret = false;
				$e = 'INSERT / UPDATE sql STATEMENT error !';
			}
			//dmp($data['vouchers']);
			if(isset($data['vouchers']) && count($data['vouchers'])>0){
				foreach($data['vouchers'] as $key => $value )
				{
					$recordName			= trim($data['vouchers'][ $key ]);
					$this->_db->setQuery( "
							INSERT INTO #__hotelreservation_offers_vouchers
							(
							offerId,
							voucher
					)
							VALUES
							(
							'$this->_offer_id',
							'$recordName'
								
					)
							" );
							dmp($recordName);
							if (!$this->_db->query() )
							{
								// dmp($db);
									$ret = false;
									$e = 'INSERT / UPDATE sql STATEMENT error !';
								}
					
								}
								}
							}
							//end update vouchers
							//exit;
	}
	
	function storePictures($data){
		//prepare photos
		
		$path_old = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
		$files = glob( $path_old."*.*" );
		$path_new = JHotelUtil::makePathFile(JPATH_ROOT.DS.PATH_PICTURES.PATH_OFFER_PICTURES.($data['offer_id']+0)."/");
		
		
		$picture_ids 	= array();
		foreach( $data['pictures'] as $value )
		{
			$row = $this->getTable('ManageOfferPictures','Table');
		
			// dmp($key);
			$pic 						= new stdClass();
			$pic->offer_picture_id		= 0;
			$pic->offer_id 				= $data['offer_id'];
			$pic->offer_picture_info	= $value['offer_picture_info'];
			$pic->offer_picture_path	= $value['offer_picture_path'];
			$pic->offer_picture_enable	= $value['offer_picture_enable'];
			//dmp($pic);
			$file_tmp = JHotelUtil::makePathFile( $path_old.basename($pic->offer_picture_path) );
				
			if( !is_file($file_tmp) )
				continue;
		
			if( !is_dir($path_new) )
			{
				if( !@mkdir($path_new) )
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
				
			if( $path_old.basename($pic->offer_picture_path) != $path_new.basename($pic->offer_picture_path) )
			{
				if(@rename($path_old.basename($pic->offer_picture_path),$path_new.basename($pic->offer_picture_path)) )
				{
		
					$pic->offer_picture_path	 = PATH_OFFER_PICTURES.($data['room_id']+0).'/'.basename($pic->offer_picture_path);
					//@unlink($path_old.basename($pic->offer_picture_path));
				}
				else
				{
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
		
			if (!$row->bind($pic))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
		
			}
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		
			// Store the web link table to the database
			if (!$row->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		
			$picture_ids[] = $this->_db->insertid();
		
		
		}
		
		$files = glob( $path_new."*.*" );
		
		foreach( $files as $pic )
		{
			$is_find = false;
			foreach( $data['pictures'] as $value )
			{
				if( $pic == JHotelUtil::makePathFile(JPATH_ROOT.$value['offer_picture_path']) )
				{
					$is_find = true;
					break;
				}
			}
			//if( $is_find == false )
			//	@unlink( JHotelUtil::makePathFile(JPATH_COMPONENT.$value['offer_picture_path']) );
		}
		
		$query = " DELETE FROM #__hotelreservation_offers_pictures
						WHERE offer_id = '".$data['offer_id']."'
						".( count($picture_ids)> 0 ? " AND offer_picture_id NOT IN (".implode(',', $picture_ids).")" : "");
		
		// dmp($query);
		// exit;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		//~prepare photos
	}
	
	
	function storeExtraOptions($offerId,$extraOptionsArray){
		$extraOptions = $this->getExtraOptions();
		foreach($extraOptions as $extraOption){
			if(in_array($extraOption->id, $extraOptionsArray)){
				if(strpos($extraOption->offer_ids, $offerId )===false){
					$extraOption->offer_ids.= ",".$offerId;
				}
			}else{
				if(strpos($extraOption->offer_ids, $offerId)!==false){
					$extraOption->offer_ids = str_replace($offerId, "", $extraOption->offer_ids);
					$extraOption->offer_ids = str_replace(",,", "", $extraOption->offer_ids);
					$extraOption->offer_ids = trim($extraOption->offer_ids, ",");
				}
			}
				
				
			//dmp($extraOption);
			//exit;
			$row =JTable::getInstance('ExtraOption',"JTable");
			if (!$row->bind($extraOption)){
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
	
			// Store the web link table to the database
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		}
	}
	
	function storeOfferExcursions($offerId,$excursionsArray){
		
		$row =JTable::getInstance('OffersExcursions',"Table");
		$row->deleteOfferExcursions($offerId);
		
		foreach($excursionsArray as $excursion_id){
			$row =JTable::getInstance('OffersExcursions',"Table");
			
			$row->offer_id = $offerId;
			$row->excursion_id = $excursion_id;
		
			// Make sure the record is valid
			if (!$row->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
	
			// Store the web link table to the database
			if (!$row->store(true))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		}
	}
	
	
	
	function getOfferPictures(){

		$pictures  	= array();
		$table = $this->getTable('ManageOffers','Table');
		$files = $table->getOffersPictures($this->getState('offer.offer_id'));
		
		if( isset( $files) )
		{			
			foreach( $files as $value )
			{
				$pictures[]	= array( 
													'offer_picture_info' 		=> $value->offer_picture_info,
													'offer_picture_path' 		=> $value->offer_picture_path,
													'offer_picture_enable'		=> $value->offer_picture_enable,
												);
			}
		}
		return $pictures;
	}
	
	
	
	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function getTranslations(){

		$hoteltranslationsModel = new JHotelReservationModelHotelTranslations();
		$translations = $hoteltranslationsModel->getAllTranslations(OFFER_TRANSLATION, $this->getState('offer.offer_id'));
		return $translations;
	}
	
	function saveOfferDescriptions($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationModelHotelTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(OFFER_TRANSLATION,$data['offer_id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['offer_description_'.$_lng]) && strlen($data['offer_description_'.$_lng])>0){
					$offerDescription = 		JRequest::getVar( 'offer_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(OFFER_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	}
	
	function saveOfferContent($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationModelHotelTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(OFFER_CONTENT_TRANSLATION,$data['offer_id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['offer_content_'.$_lng]) && strlen($data['offer_content_'.$_lng])>0){
					$offerDescription = 		JRequest::getVar( 'offer_content_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(OFFER_CONTENT_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}
	function saveOfferShortDescriptions($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationModelHotelTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(OFFER_SHORT_TRANSLATION,$data['offer_id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['offer_short_description_'.$_lng]) && strlen($data['offer_short_description_'.$_lng])>0){
					$offerDescription = 		JRequest::getVar( 'offer_short_description_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(OFFER_SHORT_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}
	
	function saveOfferOtherInfo($data){
		try{
			$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
			$dirs = JFolder::folders( $path );
			sort($dirs);
			$modelHotelTranslations = new JHotelReservationModelHotelTranslations();
			$modelHotelTranslations->deleteTranslationsForObject(OFFER_INFO_TRANSLATION,$data['offer_id']);
			foreach( $dirs  as $_lng ){
				if(isset($data['offer_other_info_'.$_lng]) && strlen($data['offer_other_info_'.$_lng])>0){
					$offerDescription = JRequest::getVar( 'offer_other_info_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
					$modelHotelTranslations->saveTranslation(OFFER_INFO_TRANSLATION,$data['offer_id'],$_lng,$offerDescription);
				}
			}
		}
		catch(Exception $e){
			print_r($e);
			exit;
			JError::raiseWarning( 500,$e->getMessage());
		}
	
	}
	
	
	function displayThemes($themes, $selectedThemes){
		ob_start();
		?>
	
				<select id="themes" multiple="multiple" name="themes[]">
					<option value="">
				
					<?php echo JText::_('LNG_SELECT_THEME',true)?></option>
					
					<?php
					if( isset($themes) && is_array($themes))
					foreach( $themes as $theme )
					{
						$selected = false;
						foreach( $selectedThemes as $selectedTheme ){
							if($theme->id == $selectedTheme->themeId)
							$selected =true;
						}
						?>
						<option <?php echo $selected? 'selected="selected"' : ''?> 	value='<?php echo $theme->id?>'><?php echo $theme->name ?></option>
						<?php
						}
						?>
				</select>
	
				<?php
				$buff = ob_get_contents();
				ob_end_clean();
				return $buff;
		}
		
		function getOfferRooms(){

			$offerId = $this->getState('offer.offer_id');
			$hotelId = $this->getState('offer.hotel_id');

			$query = " 	SELECT
							r.room_id,
							r.room_name,
							r.max_adults,
							r.max_children,
							".
									(
											$offerId > 0 ?
											"IF( ISNULL(ho.room_id), 0, 1 )"
											:
											"0"
									)
									."						AS is_sel
						FROM #__hotelreservation_rooms r
						".($offerId > 0 ?
										"LEFT JOIN (
										select * from  #__hotelreservation_offers_rooms o_r  where  o_r.offer_id = $offerId
								) ho  on r.room_id = ho.room_id
										":"")."
						WHERE
							r.hotel_id = ".$hotelId."
							";
			
										
									//dmp($query);
			$itemRooms = $this->_getList( $query );
			if( isset( $itemRooms) )
			{
					
				foreach($itemRooms as $k => $r )
				{
			
					//dmp($r);
					$query = "SELECT *	FROM  #__hotelreservation_offers_rates d where d.offer_id=$offerId and d.room_id=$r->room_id";
					//dmp($query);
					$res = $this->_getList( $query );
					if(isset( $res) && count($res)>0 )
					{
						foreach( $res as $d )
						{
							//$d->week_types 	= explode(',', $d->week_types);
							// dmp($d);
							$itemRooms[$k]->discounts  = $d;
						}
					}else{
						$discounts = new stdClass();
						$discounts->id =0;
						$discounts->offer_room_price_id = null;
						$discounts->offer_id = $offerId;
						$discounts->room_id = $r->room_id;
						$discounts->price_1 = null;
						$discounts->price_2 = null;
						$discounts->price_3 = null;
						$discounts->price_4 = null;
						$discounts->price_5 = null;
						$discounts->price_6 = null;
						$discounts->price_7 = null;
						$discounts->single_balancing = null;
						$discounts->child_price = null;
						$discounts->price_type = 1;
						$discounts->extra_night_price = null;
						$discounts->extra_pers_price = null;
						$discounts->base_adults = null;
						$discounts->base_children = null;
						$itemRooms[$k]->discounts = $discounts;
					}
				}
			}
		return 	$itemRooms;
	}
	
	function getExtraOptions(){
		$hotelId = $this->getState('offer.hotel_id');
		$query = "select * from  #__hotelreservation_extra_options WHERE  hotel_id = ". $hotelId;
		$this->_db->setQuery( $query );
		$extraOptions = $this->_db->loadObjectList();
		return $extraOptions;
	}
	
	public function setHotelMinOfferPrice(){
		$hotelId = $this->_hotel_id;
	
		$query="select rr.base_adults, rr.price_type, least(rr.price_1, rr.price_2, rr.price_3, rr.price_4, rr.price_5, rr.price_6, rr.price_7) as min_rate,min(rrp.price) as min_rate_custom
				from #__hotelreservation_offers r
				inner join #__hotelreservation_offers_rates rr on r.offer_id = rr.offer_id
				left join #__hotelreservation_offers_rate_prices rrp on rrp.rate_id = rr.id
				where r.is_available = 1 and r.hotel_id= $hotelId
				group by r.hotel_id";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObject();
	
	
		$price = $result->min_rate;
		if(isset($result->min_rate_custom) && $price>$result->min_rate_custom){
		$price = $result->min_rate_custom;
		}
	
		if($result->price_type == 0){
		$price = $price / $result->base_adults;
		}
	
		$query="update #__hotelreservation_hotels set min_offer_price = $price where hotel_id = $hotelId ";
	
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
		dmp($query);
		dmp("error");
		}
		exit;
	}
	
	function getLastOrderNumber($hotelId){
		$query = "select from max(offer_order) as offer_order from  #__hotelreservation_offers WHERE  hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		$offer = $this->_db->loadObject();
		return $offer->offer_order;
	}
	
	function getLastOrder($offerId)
	{
		$offer_id = 0;
		if( isset($offerId) )
			$offer_id = $offerId;
		$increment = 0;
		if( $offer_id > 0 ){
			$query = 	" SELECT * FROM #__hotelreservation_offers  WHERE offer_id = ".$offer_id;
		} else {
			$query = 	" SELECT * FROM #__hotelreservation_offers  ORDER BY offer_order DESC LIMIT 1 ";
			$increment++;
		}
	
		$db 	= JFactory::getDBO();
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
	
		if(!isset($row ))
			return 1;
	
		return ($row->offer_order+$increment);
	}
	
	function getExcursions(){

		$hotelId = $this->getState('offer.hotel_id');
		$query = " 	
	 				SELECT r.id, r.name as excursion_name
					FROM #__hotelreservation_excursions r
					WHERE r.is_available = 1 AND r.hotel_id  = ".$hotelId."
				";
		$excursions = $this->_getList( $query );
		return $excursions;
	}
	
	function getOfferExcursions($offerId){
		$row =JTable::getInstance('OffersExcursions',"Table");
		$excursions = $row->getOfferExcursions($offerId);
		if(count($excursions))
			return $excursions->excursionIds;
		else 
			return null;
	}

}
