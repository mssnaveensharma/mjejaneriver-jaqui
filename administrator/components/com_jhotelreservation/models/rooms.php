<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelRooms extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array	An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'room_id', 'r.id',
				'title', 'a.title',
				'menutype', 'a.menutype',
			);
		}

		parent::__construct($config);
	}

	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 0, 'int');
		$this->setState('filter.type', $type);
		
		$hotel_id = JRequest::getVar('hotel_id', null);
		if ($hotel_id) {
			if ($hotel_id != $app->getUserState($this->context.'.filter.hotel_id')) {
				$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
				JRequest::setVar('limitstart', 0);
			}
		}
		else {
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
	
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		$app->setUserState('com_jhotelreservation.rooms.filter.hotel_id', $hotel_id);
		$this->setState('filter.hotel_id', $hotel_id);
		//dmp($hotel_id);
		// List state information.
		parent::populateState('r.room_id', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.hotel_id');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();
		
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		if (empty($items))
		{
			return array();
		}
		

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'r.*'));
		$query->from($db->quoteName('#__hotelreservation_rooms').' AS r');

		// Join over currency
		$query->select('hrr.id as rate_id');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_rooms_rates').' AS hrr ON hrr.room_id=r.room_id');
		
		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('r.is_available = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(r.is_available IN (0, 1))');
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('r.hotel_id = '.$db->quote($hotelId));
		}
		
		$query->group('r.room_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'r.room_order')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	function &getHotels()
	{
		// Load the data
		if (empty( $this->_hotels )) 
		{
			$query = ' SELECT 
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						ORDER BY hotel_name, country_name ';
			//$this->_db->setQuery( $query );
			$this->_hotels = $this->_getList( $query );
		}
		return $this->_hotels;
	}
	
	
	function changeRoomOrder()
	{
		$tip_order 	= '';
		$room_id 	= 0;
		if( isset( $_GET['tip_order'] ) )
		$tip_order = $_GET['tip_order'];
		if( isset( $_GET['room_id'] ) )
			$room_id = $_GET['room_id'];
		if( isset( $_GET['hotel_id'] ) )
			$hotel_id = $_GET['hotel_id'];
		
		$ret	= true;
		$up	 	= false;
		$down	= false;
		$e		= '';
		$p		= -1;
				$id_alter = 0;
		if( $tip_order == '' || $room_id == 0  || $room_id =='' )
		{
			$ret = false;
			$e = 'Invalid params';
		}
		if( $ret == true )
				{
		$db = JFactory::getDBO();
		
		$db->setQuery( "
		(
				SELECT
				*,
				'up'	AS type_order
												FROM #__hotelreservation_rooms 
												WHERE 
													room_order <= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
													AND
													room_id <> $room_id
				AND
				hotel_id = $hotel_id
												ORDER BY room_order DESC
												LIMIT 1
											)
											UNION ALL
											(
												SELECT 
													*,
													'crt'	AS type_order
												FROM #__hotelreservation_rooms 
												WHERE 
													room_order = ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
													AND 
													hotel_id = $hotel_id
												ORDER BY room_order
												LIMIT 1
											)
											UNION ALL
											(
												SELECT 
													*,
													'down'	AS type_order
												FROM #__hotelreservation_rooms 
												WHERE 
													room_order >= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id) 
													AND
													room_id <> $room_id
				AND
													hotel_id = $hotel_id
												ORDER BY room_order
				LIMIT 1
									)
									
									" );
					// dmp($db);
		// exit;
		$rows 			= $db->loadObjectList();
		$row_up			= null;
		$row_crt		= null;
		$row_down		= null;
		foreach( $rows as $value )
		{
			switch( $value->type_order )
			{
				case 'up':
					$row_up = $value;
					break;
				case 'crt':
					$row_crt = $value;
					break;
				case 'down':
				$row_down = $value;
					break;
			}
		}
		$db->setQuery( " START TRANSACTION ");
		if (!$db->query() )
		{
			$ret = false;
			$e = ' sql STATEMENT error !';
		}
		switch( $tip_order )
		{
			case 'up':
			{
				if( $row_up != null && $row_crt != null )
				{
				$id_alter = $row_up->room_id;
				$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_up->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_crt->room_id );
					if (!$db->query() )
					{
					$ret = false;
									$e = 'UPDATE sql STATEMENT error !';
								} 
					$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_crt->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_up->room_id );
								if (!$db->query() ) 
								{
								$ret = false;
								$e = 'UPDATE sql STATEMENT error !';
								} 
								
					}
								else
								$ret = false;

							break;
			 }
			case 'down':
			{
				if( $row_down != null && $row_crt != null )
				{
					$id_alter = $row_down->room_id;
					$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_down->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_crt->room_id );
						if (!$db->query() )
						{
						$ret = false;
						$e = 'UPDATE sql STATEMENT error !';
					} 

					$db->setQuery( " UPDATE  #__hotelreservation_rooms  SET room_order = ".$row_crt->room_order." WHERE hotel_id = $hotel_id AND room_id=".$row_down->room_id );
				if (!$db->query() ) 
				{
					$ret = false;
					$e = 'UPDATE sql STATEMENT error !';
				} 
				
				}
				else
					$ret = false;
				break;
			}
	
		}
			if( $ret == true )
			{
				$db->setQuery( " COMMIT ");
				if (!$db->query() )
				{
								$ret = false;
								$e = ' sql STATEMENT error !';
							}
							//check results
							$db->setQuery( "
							(
								SELECT
								*,
								'up'	AS type_order
								FROM #__hotelreservation_rooms
										WHERE 
								room_order <= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id )
											AND
											room_id <> $room_id
											AND 
											hotel_id = $hotel_id
										LIMIT 1
									)
									UNION ALL
									(
								SELECT
											*,
											'crt'	AS type_order
										FROM #__hotelreservation_rooms 
										WHERE 
								room_order = ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id  )
											AND  
											hotel_id = $hotel_id 
										LIMIT 1
									)
									UNION ALL
									(
										SELECT 
											*,
											'down'	AS type_order
										FROM #__hotelreservation_rooms 
										WHERE 
								room_order >= ( SELECT room_order FROM #__hotelreservation_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id  )
											AND
											room_id <> $room_id
											AND 
											hotel_id = $hotel_id
										LIMIT 1
									)
									
									" );
					// dmp($db);
								$rows 			= $db->loadObjectList();
					$row_up			= null;
					$row_crt		= null;
					$row_down		= null;
					foreach( $rows as $value )
					{
						switch( $value->type_order )
						{
							case 'up':
								$up = true;
								break;
							case 'crt':
								break;
							case 'down':
								$down = true;
								break;
						}
					}
								//check results
				}
				else{
					$db->setQuery( " ROLLBACK ");
					if (!$db->query() )
					{
						$ret = false;
						$e = ' sql STATEMENT error !';
					}
				}
		
			}
			ob_clean();
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo '<room_order>';
			echo '<answer up="'.($up? "1" : "0").'" down="'.($down? "1" : "0").'" error="'.($ret ? "0" : "1").'" info="'.$e.'" p="'.$id_alter.'" />';
			echo '</room_order>';
			echo '</xml>';
			exit;
		}
}
