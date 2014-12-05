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

class modJHotelReservationHelper
{
	static function getTitle( $params )
	{
		return '';
	}

	static function getCSS_Style()
	{
		$db = JFactory::getDBO();
		$query = ' SELECT css_module_style FROM #__hotelreservation_applicationsettings LIMIT 1';
		$db->setQuery($query);
		$list = $db->loadObjectList();
		if( count($list) > 0 )
		{
			return $list[0]->css_module_style;
		}
		else
		return 'style.css';

	}

	static function getHotelItems()
	{
		
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		
		$db = JFactory::getDBO();
		$query = "  SELECT
				h.*, hh2.content as hotelDescription,
				min(hp.hotel_picture_id),
				hotel_picture_path
				FROM #__hotelreservation_hotels h 
				left join #__hotelreservation_hotel_pictures hp on h.hotel_id=hp.hotel_id
				inner join (
	                select h.hotel_id as hotelId
	                	from #__hotelreservation_hotels h
	                    left join #__hotelreservation_offers hof on hof.hotel_Id = h.hotel_id
						left join #__hotelreservation_offers_vouchers hov on hof.offer_id = hov.offerId  
	                    where hof.is_available =1 and (hov.voucher is null or hov.voucher='' or hof.public=1)
	                    group by hotelId
	             ) as hh1 on h.hotel_id=hh1.hotelId  
			    left join(
					    select * from #__hotelreservation_language_translations t
					    where type = ".HOTEL_TRANSLATION." and language_tag = '$language_tag'
				)as hh2 on hh2.object_id = h.hotel_id 	  
				WHERE h.is_available=1 and LENGTH(h.hotel_latitude) > 3
				group by h.hotel_id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		
		return $list;

	}

}
?>
