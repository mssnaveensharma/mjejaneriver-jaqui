<?php 

class ExtraOptionsService{
	
	
	public static function getHotelExtraOptions($hotelId, $startDate, $endDate, $extraOptionIds, $roomId, $offerId, $onlySelected = true){
		$db = JFactory::getDBO();
		$filter="";
		if(isset($roomId) && $roomId > 0){
			$filter= " and FIND_IN_SET( ".$roomId.", room_ids  ) ";
		}
	
		if(isset($offerId) && $offerId > 0){
			$filter= "and FIND_IN_SET( ".$offerId.", offer_ids  ) ";
		}
		//dmp($extraOptionIds);
		$extraFilter = "";
		if(isset($extraOptionIds) && count($extraOptionIds)>0){
			$extraFilter = " eo.id in (";
			foreach( $extraOptionIds as $id )
			{
				$extraFilter .= $id[3].',';
			}
			$extraFilter = substr($extraFilter,0,-1);
			$extraFilter .= ")";
		}
		//dmp($extraFilter);
		//exit;
		
		$whereFilter = " status = 1 ";
		if(!empty($extraFilter)){
			if($onlySelected ){
				$whereFilter = " $extraFilter ";
			}else{
				$whereFilter = "(status = 1 or $extraFilter) ";
			}
		}
		$languageTag = JRequest::getVar( '_lang');
		
		//dmp($whereFilter);
		
		$query = "select eo.*,hlt.content as description
					from #__hotelreservation_extra_options eo
					left join
					(select * from 
					 #__hotelreservation_language_translations 
					 where type = ".EXTRA_OPTIONS_TRANSLATION."
					 and language_tag = '$languageTag'
					) as hlt on hlt.object_id = eo.id
					WHERE
					$whereFilter
					$filter 
					and
					IF(
					eo.start_date <> '0000-00-00'
					AND
					eo.end_date <> '0000-00-00',
					('".$startDate."' BETWEEN eo.start_date  AND eo.end_date) and  ('".$endDate."' BETWEEN eo.start_date  AND eo.end_date),
					If(
						eo.start_date = '0000-00-00'
						AND
						eo.end_date <> '0000-00-00',
						'".$endDate."' < eo.end_date,
						if(
							eo.start_date <> '0000-00-00'
							AND
							eo.end_date = '0000-00-00',
							'".$startDate."' > eo.start_date ,
							1
							)
						)
					)
					and hotel_id = $hotelId
					order by ordering";
		//echo($query);
		$db->setQuery( $query );
		$extraOptions = $db->loadObjectList();
		$result = array();
		foreach($extraOptions as &$extraOption){
			$found = false;
			foreach( $extraOptionIds as $id )
			{
				if($extraOption->id == $id[3]){
					$extraOption->checked = true;
					$found = true;
					$extraOption->persons = $id[5];
					$extraOption->days = $id[6];
					$extraOption->current = $id[2];
					array_unshift($result, clone $extraOption);
				}
			}
			if(!$found){
				array_push($result,$extraOption);
			}
		}
		
		//dmp($extraOptions);
		return $result;
	}
}


?>