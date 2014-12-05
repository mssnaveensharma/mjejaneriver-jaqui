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
defined('_JEXEC') or die('Restricted access');

class modJHotelSearchFilterHelper
{

	function getItems(&$params)
	{

		$db = JFactory::getDBO();
		$table 	= explode('#', $params->get('type'));
		//var_dump($table);
		$table = $table[0];
		$query = 'SELECT *  FROM #__hotelreservation_hotel_'.$table;

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function getFilter(){
		
		$query="
				select hotelId1, facilities, types, accommodationTypes, enviroments, regions, themes, room_available, offer_available from (
						select hotelId1, facilities, types, accommodationTypes, enviroments, regions, room_available, max(ho.is_available) as offer_available, GROUP_CONCAT(hotr.themeId) as themes from (
								select hotelId1, facilities, types, accommodationTypes, enviroments, regions, max(hr.front_display) as room_available from (
										select hotelId1, facilities, types, accommodationTypes, enviroments,  GROUP_CONCAT(rl.regionId) as regions from (
												select hotelId1, facilities, types, accommodationTypes, GROUP_CONCAT(er.environmentId) as enviroments from (
														Select hotelId1, facilities, types, GROUP_CONCAT(atr.accommodationTypeId) as accommodationTypes from(
																Select hotelId1, facilities,GROUP_CONCAT(tr.typeId) as types  from (
																		SELECT h.hotel_id as hotelId1, GROUP_CONCAT(fr.facilityId) as facilities FROM
																		#__hotelreservation_hotels as h
																		left join #__hotelreservation_hotel_facility_relation as fr on h.hotel_id=fr.hotelId $whereClause $activeHotelsFilter and h.is_available = 1  group by hotelId1
																		) as hh1
																left join #__hotelreservation_hotel_type_relation as tr on hotelId1=tr.hotelId $facilityFilter group by hotelId1
																) as hh2
				left join #__hotelreservation_hotel_accommodation_type_relation as atr on hotelId1=atr.hotelId $typesFilter  group by hotelId1
				) as hh3
				left join #__hotelreservation_hotel_environment_relation as er on hotelId1=er.hotelId $accommodationTypeFilter  group by hotelId1
				) as hh4
				left  join #__hotelreservation_hotel_region_relation as rl on hotelId1=rl.hotelId $enviromentFilter  group by hotelId1
														)as hh5
														left join #__hotelreservation_rooms as hr on hotelId1=hr.hotel_id $regionFilter  group by hotelId1
														) as hh6 left join #__hotelreservation_offers ho on hotelId1=ho.hotel_id
														left join #__hotelreservation_offers_themes_relation hotr on hotr.offerId=ho.offer_id
														group by hotelId1
														)as hh7
				) as hh8
				";
		
	}
}
?>
