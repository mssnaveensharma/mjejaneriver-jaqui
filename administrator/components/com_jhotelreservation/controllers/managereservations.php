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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'variables.php'; 
require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'confirmations.php'; 



class JHotelReservationControllerManageReservations extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');  
	}

	function apply()
	{
		$post = JRequest::get( 'post' );
		if( isset( $post['is_penalty']) )
		{
			$this->addPenalty();
		}
		
	}
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	 
	function save()
	{
		$bRet = 0;
		$post 	= JRequest::get( 'post' );
		
		$model 	= $this->getModel('managereservations');
		$tsk 	= 'edit';
		if( isset( $post['is_assign_number_rooms']) && $post['is_assign_number_rooms'] == 1 )
			$tsk = 'assign_number_rooms';
		if( isset( $post['is_status']) && $post['is_status'] == 1 )
		{
			$ret = $this->changeStatus();
			return $ret;
			
		}
		else if( isset( $post['change_confirmation_payment_status']) && $post['change_confirmation_payment_status'] > 0 )
		{
			$ret =  $this->changeConfirmationPaymentStatus($post['change_confirmation_payment_status']);
			return $ret;
		}
	/* 	}else if(isset ($post['first_name'])){
			if($model->store($post)){
				$msg = JText::_('LNG_RESERVATION_SAVED',true);
				$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
			}else{
				JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_RESERVATION',true) ); 
				$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&confirmation_id[]='.$post['confirmation_id'] );
			}
		} */
		else 
		{
		
			if( $tsk == 'edit' ) 
			{
				// if( strtotime($post['datas']) < strtotime( date('Y-m-d') ) )
					// $bRet = -1;
				// if( strtotime($post['datae']) < strtotime($post['datas'] ) )
					// $bRet = -2;
				// if( count($post['room_ids']) == 0) 
					// $bRet = -3;
			}	
			if( $bRet == 0 )
			{
				$modelVariables 		= $this->getModel('Variables');
				$modelConfirmations 	= $this->getModel('Confirmations');
				
				//$modelConfirmations = $this->getModel('Confirmations');
				if( isset( $post['is_assign_number_rooms']) && $post['is_assign_number_rooms'] == 1 )
				{
					JRequest::setVar( 'tip_oper',5);
					$modelVariables->load( $post['confirmation_id'], $post['email'], $modelVariables->itemCurrency, array( 'itemRoomsNumbers'=>$post['itemRoomsNumbers']) );
					
					$tsk = 'assign_number_rooms';
					// dmp($modelVariables->itemRoomsNumbers);
					// exit;
					foreach( $modelVariables->itemRoomsNumbers as $key => $valueNumber )
					{
						if( $valueNumber == 0 )  
						{
							JError::raiseWarning( 500, JText::_('LNG_PLEASE_SELECT_ROOM_NUMBER',true) ); 
							$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&confirmation_id[]='.$post['confirmation_id'].'&task='.$tsk );
					
							return false;
						}
					}
					//$post['itemRoomsNumbers'] 	= $str;
				}
				else if( $tsk == 'edit' )
				{
					
					$start_date = JHotelUtil::convertToMysqlFormat($post["datas"]);
					$end_date = JHotelUtil::convertToMysqlFormat($post["datae"]);
					//dmp($start_date);
					//dmp($end_date);
					//dmp($post);
					JRequest::setVar( 'tip_oper',5);
					$modelVariables = new JHotelReservationModelVariables($post['hotel_id']);
					$modelVariables->load( 
											$post['confirmation_id'], 
											$post['email'], 
											$modelVariables->itemCurrency, 
											$post
					 				);	
					$modelVariables->store($post);
					//dmp($modelVariables);
					
				}
				//dmp($modelVariables);
				//exit;
				if( $modelVariables->checkAvalability(true) )
				{
					// $str 						= $modelVariables->getStringRoomsCapacity( $modelVariables->itemRoomsCapacity );
					// $post['itemRoomsCapacity'] 	= $str;
					// $modelVariables->store($post);
					
					$check_totals				= true;
					if( isset( $post['is_assign_number_rooms']) && $post['is_assign_number_rooms'] == 1 )
					{
						$check_totals			= false;
					}
					else
					{
						$modelVariables->status_reservation_id 	= RESERVED_ID;
					}
					//$modelVariables->itemRoomsDiscounts 	= $modelVariables->getRoomsDiscounts();
					
					/*
					//numbers
					if( isset( $post['is_assign_number_rooms']) && $post['is_assign_number_rooms'] == 1 )
					{
						$modelVariables->getRoomsAvailable($modelVariables->room_ids, true );
						foreach($modelVariables->itemRoomsNumbers as $key=> $nr )
						{
							foreach( $modelVariables->itemRoomsAvailable as $k => $v )
							{
								if($v->room_id == $key )
								{
									foreach( $modelVariables->itemRoomsAvailable[$k]->daily as $d => $vd  )
									{
										// dmp($modelVariables->itemRoomsNumbers);
										$modelVariables->itemRoomsAvailable[$k]->daily['numbers'][] = array(
																										'data'			=>	$vd['numbers'],
																										'id'			=>  -1,
																										'nr'			=>	$nr,
																										'price'			=> 	$vd['price_final'],
																										'discounts'		=>  $vd[ 'discounts' ]
																									);
									}
								}						
							}
						}
						echo 3;
					}
					dmp($modelVariables->itemRoomsAvailable);
					exit;*/
					
					if( $modelVariables->total > $modelVariables->total_payed )
						$modelVariables->confirmation_payment_status = PAYMENT_STATUS_NOTPAYED; 
					
					
					//check is allready payed on website
					/*if( !$modelVariables->comparePayedValues() && $check_totals == true ){
						$bRet = -5;
					
					}*/
						
					if( $modelConfirmations->store( $modelVariables, true, false ) )
					{
					
						$bRet = 1;
					} 
					//exit;
				}
				else
					$bRet = -4;
			}
			
			//exit;
			switch( $bRet )
			{
				case 1:
					$msg = JText::_('LNG_RESERVATION_SAVED',true);
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					break;
				case 0:
					JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_RESERVATION',true) ); 
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&confirmation_id[]='.$post['confirmation_id'] );
					break;
				case -1:
					JError::raiseWarning( 500, JText::_('LNG_DATA_START_DATA_CURRENT',true));
					//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task='.$tsk.'&confirmation_id[]='.$post['confirmation_id'] ); 
					break;
				case -2:
					JError::raiseWarning( 500,JText::_('LNG_DATA_STOP_LOWER_DATA_START',true));
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task='.$tsk.'&confirmation_id[]='.$post['confirmation_id'] ); 
					//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					break;
				case -3:
					JError::raiseWarning( 500, JText::_('LNG_PLEASE_SELECT_AT_LEAST_ONE_ROOM',true));
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task='.$tsk.'&confirmation_id[]='.$post['confirmation_id'] ); 
					//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					break;
				case -4:
					JError::raiseWarning( 500, JText::_('LNG_CURRENTLY_THERE_ARE_NO_ENOUGH_ROOMS_AVAILABLE',true));
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task='.$tsk.'&confirmation_id[]='.$post['confirmation_id'] ); 
					//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					break;
				case -5:
					JError::raiseWarning( 500, JText::_('LNG_WEBSITE_PAYED_FINAL_LOWER_PRICE',true));
					$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task='.$tsk.'&confirmation_id[]='.$post['confirmation_id'] ); 
					//$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
					break;

			}
		}
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
	}
	
	function delete()
	{
		$model = $this->getModel('managereservations');
		$modelReview = $this->getModel('managehotelratings');		
		if ($model->remove()) 
		{
			$msg = JText::_('LNG_RESERVATION_HAS_BEEN_DELETED',true);
		} 
		else 
		{
			$msg = '';
			JError::raiseWarning( 500, JText::_('LNG_ERROR_DELETE_RESERVATION',true));
		}
		$modelReview->deleteHotelRating();

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
	}
	
	function edit()
	{	
		
		$model 	= $this->getModel('managereservations');
		$modelVariables = $model->getData();
		foreach($modelVariables->items_reserved as $room_reserved){
			//dmp($room_reserved);
			$values = explode("|",$room_reserved);
			$nr_guests= 0;
			//dmp($values);
			//dmp($this->room_guests);
			if( isset($this->room_guests) ){
				$nr_guests= $modelVariables->room_guests[$values[2]-1];
			}
			$roomSelected =$modelVariables->getRoomsAvailable(array($room_reserved), true, false, $nr_guests);
			if(count($roomSelected)==0){
				$msg=JText::_('LNG_CANNOT_EDIT_RESERVATION_ROOM_NOT_AVAILABLE',true);
				$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
			}
			$this->itemRoomsSelected[$values[2]-1] = $roomSelected[0];
				
		}

		JRequest::setVar( 'view', 'managereservations' );
		parent::display(); 
	}
	
	function add()
	{
		$msg = JText::_( '' ,true);
		$this->setRedirect( 'index.php?option='.getBookingExtName()."&task=addreservations&view=addreservations", ''); 
	}
	
	function backInfo()
	{
		$msg = JText::_( '' ,true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
	}
	
	function sendEmail()
	{
		$msg 					= JText::_( '' ,true);
		$post 					= JRequest::get( 'post' );
		$modelVariables 		= $this->getModel('Variables');
		$modelConfirmations 	= $this->getModel('Confirmations');
		JRequest::setVar( 'tip_oper',4);
		$modelVariables->load($post['confirmation_id'],null,null);
		$modelVariables->sendEmail($modelVariables->status_reservation_id);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
	}
		
	function info()
	{
		JRequest::setVar( 'view', 'managereservations' );
		parent::display(); 
	}

	
	function addPenalty()
	{
		$db =JFactory::getDBO();
		$db->setQuery("START TRANSACTION");
		$db->query();
	
		$post = JRequest::get( 'post' );
		$model = $this->getModel('managereservations');
		$msg	= '';
		if( $model->penalty( $post, $msg) ) 
		{
			$modelVariables 		= $this->getModel('Variables');
			$modelConfirmations 	= $this->getModel('Confirmations');
			JRequest::setVar( 'tip_oper',5);
			$modelVariables->load( $post['confirmation_id'], $post['email'], $modelVariables->itemCurrency, array( 'payment_percent'=>$post['payment_percent'], 'payment_explication'=>$post['payment_explication']) );
			//$modelConfirmations = $this->getModel('Confirmations');
			
			// JRequest::setVar( 'tip_oper',4);
			// $modelVariables->store($post);
			
			if( $modelVariables->total > $modelVariables->total_payed )
				$modelVariables->confirmation_payment_status = PAYMENT_STATUS_NOTPAYED; 
			if( !$modelConfirmations->store( $modelVariables, false, false ) )
			{
				//$msg = JText::_('LNG_ERROR_APPLY_PENALTIES',true);
				JError::raiseWarning( 500, JText::_('LNG_ERROR_APPLY_PENALTIES',true) );
				
				$db->setQuery("ROLLBACK");
				$db->query();
	
			}
			else
			{
				if($post['payment_percent'] > 0 && is_numeric($post['payment_percent']))
					$msg = JText::_('LNG_PENALTY_APPLIED',true);
				else
					$msg = JText::_('LNG_PENALTY_WAS_DELETED',true);
				
				// $db->setQuery("ROLLBACK");				
				$db->setQuery("COMMIT");
				$db->query();
	
			}
			
		} 
		else 
		{

		}
		
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
	}
	
	function changeStatus()
	{
		$post = JRequest::get( 'post' );
		$model = $this->getModel('managereservations');
		
		$db =JFactory::getDBO();
		$db->setQuery("START TRANSACTION");
		$db->query();
	
		$bRet = false;
			
		if( $model->status( $post ) ) 
		{
	
			$msg = JText::_( '' ,true);
			$modelVariables 		= $this->getModel('Variables');
			JRequest::setVar( 'tip_oper',4);
			JRequest::setVar( 'confirmation_payment_status',PAYMENT_STATUS_PAYED);
			$modelVariables->load( $post['confirmation_id'], $post['email'], $modelVariables->itemCurrency);
			$modelConfirmations 	= $this->getModel('Confirmations');
			// exit;

			$answer_processor = '';
			if( !$modelConfirmations->store( $modelVariables, false, false, $answer_processor ) )
			{
				$db->setQuery("ROLLBACK");
				$db->query();
				// $msg = JText::_('LNG_ERROR_CHANGE_STATUS_RESERVATION',true);
				$msg =  '';
				$info_error = JText::_('LNG_ERROR_CHANGE_STATUS_RESERVATION',true) ;
				
				if( $answer_processor > 0 )
					$info_error .= prepareErrorMsgProcessor(JText::_("LNG_PAYFLOW_".$answer_processor,true));
				JError::raiseWarning( 500, $info_error );
			}
			else
			{
				if( $post['status_reservation_id'] == CANCELED_ID )
				{
					//$modelVariables->sendEmail($modelVariables->status_reservation_id);
				}
				$msg = JText::_('LNG_RESERVATION_STATUS_HAS_BEEN_UPDATED_SUCCESSFULLY',true);
				// $db->setQuery("ROLLBACK");
				$db->setQuery("COMMIT");
				$db->query();
				
				$bRet = true;
				
			}
		} 
		else 
		{
			//$msg = JText::_('LNG_ERROR_CHANGE_STATUS_RESERVATION',true);
			$msg = JText::_('LNG_ERROR_CHANGE_STATUS_RESERVATION',true);
			// JError::raiseWarning( 500, JText::_('LNG_ERROR_CHANGE_STATUS_RESERVATION',true));
			$db->setQuery("ROLLBACK");
			$db->query();
		}
		if( $post['status_reservation_id'] == CHECKEDOUT_ID){
			//send email for rating;
			$modelVariables->sendReviewEmail();
		}
		
		if( $post['status_reservation_id'] != CHECKEDOUT_ID || $bRet ==false )
		{
			//$model = $this->getModel('managereservations');
			//if(  $model->changePaymentConfirmation( $post ) )
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations', $msg );
		}
		else
		{
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&task=info&confirmation_id[]='.$post['confirmation_id'], $msg );
		}
	}
	
	
function changeConfirmationPaymentStatus($tip)
	{
		
		$post = JRequest::get( 'post' );
		
		$db =JFactory::getDBO();
		$db->setQuery("START TRANSACTION");
		$db->query();

		$model = $this->getModel('managereservations');

		if(  $model->changePaymentConfirmation( $post ) ) 
		{
			$msg = JText::_( '' ,true);
			
			$modelVariables 		= $this->getModel('Variables');
			JRequest::setVar( 'tip_oper',4);
			JRequest::setVar( 'confirmation_payment_status',PAYMENT_STATUS_PAYED);
			$modelVariables->load( $post['confirmation_id'], $post['email'], $modelVariables->itemCurrency);
			
			$modelConfirmations 	= $this->getModel('Confirmations');
			//$modelVariables->store($post);
			$answer_processor = '';
			if( !$modelConfirmations->store( $modelVariables , false, false, $answer_processor  ) )
			{
				$db->setQuery("ROLLBACK");
				$db->query();
				$model->delete_confirmation_payment( $post['confirmation_id']);
				$msg = JText::_('LNG_ERROR_APPLY_PAYMENT_RESERVATION',true);
				if( $answer_processor > 0 )
					$msg .= prepareErrorMsgProcessor(JText::_("LNG_PAYFLOW_".$answer_processor,true));
			}
			else
			{
				$db->setQuery("COMMIT");
				// $db->setQuery("ROLLBACK");
				$db->query();
				$msg = JText::_('LNG_PAYMENT_RESERVATION_SUCCESSFUL',true);
			}
		} 
		else 
		{
			$db->setQuery("ROLLBACK");
			$db->query();
			
			$model->delete_confirmation_payment( $post['confirmation_id'] );
			//$msg = JText::_('LNG_ERROR_APPLY_PAYMENT_RESERVATION',true);
			$msg = JText::_('LNG_ERROR_APPLY_PAYMENT_RESERVATION',true);
		}
		
	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=managereservations&view=managereservations&filter_status_reservation='.$post['filter_status_reservation'], $msg );
	}
}


