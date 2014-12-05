<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

/**
 *
 * @author George
 *
 */
class JHotelReservationModelCubilis extends JModel{
	
	
	public function getHotelRoomList(){
		$xmlContent = '<?xml version="1.0" encoding="utf-8"?>
						<OTA_HotelRoomListRS Version="2.0" xmlns="http://www.opentravel.org/OTA/2003/05">
						    <Success/>
						    <HotelRoomLists>
						        <HotelRoomList HotelCode="11">
						            <RoomStays>
						                <RoomStay>
						                    <RoomTypes>
						                        <RoomType IsRoom="true" RoomID="28">
						                            <RoomDescription Name="Luxe kamer" />
						                        </RoomType>
						                    </RoomTypes>
						                    <RatePlans>
						                        <RatePlan RatePlanID="3" RatePlanName="Best Available Rate" />
						                     </RatePlans>
						                </RoomStay>
						                <RoomStay>
						                    <RoomTypes>
						                        <RoomType IsRoom="true" RoomID="29">
						                            <RoomDescription Name="Standard " />
						                        </RoomType>
						                    </RoomTypes>
						                    <RatePlans>
						                        <RatePlan RatePlanID="2" RatePlanName="Best Available Rate" />
						                    </RatePlans>
						                </RoomStay>
						            </RoomStays>
						        </HotelRoomList>
						    </HotelRoomLists>
						</OTA_HotelRoomListRS>';
		return $xmlContent;
	}
	
	public function getNewReservations($hotelId){
		$reservationsTable = $this->getTable("Confirmations");
		return $reservationsTable->getCubilisReservations($hotelId, CUBILIS_MAX_RESERVATIONS);
	}
	
	public function setReservationCubilisStatus($reservations){
		$reservationsTable = $this->getTable("Confirmations");
		$reservationsTable->setReservationCubilisStatus($reservations);
	}
}