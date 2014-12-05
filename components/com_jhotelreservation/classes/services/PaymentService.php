<?php 
/**
 * Payment Service
 * 
 * @author George
 *
 */
JTable::addIncludePath('administrator/components/com_jhotelreservation/tables');

class PaymentService{
	
	/**
	 * Create all active payment processors that are displyed on front based on database details
	 * 
	 * @param boolean $onlyFrontEnd
	 */
	public static function getPaymentProcessors($onlyFrontEnd = true){
		$paymentProcessors = array();
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_payment_processors where status=1 and displayfront =1 ";
		$db->setQuery($query);
		$paymentProcessorsDetails =  $db->loadObjectList();
		
		foreach($paymentProcessorsDetails as $paymentProcessorsDetail){
			$query = "SELECT * FROM #__hotelreservation_payment_processor_fields where processor_id=$paymentProcessorsDetail->id order by id asc";
			$db->setQuery($query);
			$fields =  $db->loadObjectList();
			foreach($fields as $field){
				$paymentProcessorsDetail->fields[$field->column_name]= $field->column_value;
			}

			$processorFactory = new ProcessorFactory();
			$processor = $processorFactory->getProcessor($paymentProcessorsDetail->type);
			$processor->initialize($paymentProcessorsDetail);
			$paymentProcessors[] = $processor;
		}
		return $paymentProcessors;
	}
	
	/**
	 * Retreive processor details from database
	 * 
	 * @param string $type
	 * @return unknown
	 */
	
	public static function getPaymentProcessorDetails($type){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_payment_processors where type='$type'";
		$db->setQuery($query);
		$processor = $db->loadObject();
	
		if(isset($processor)){
			$query = " SELECT * FROM #__hotelreservation_payment_processor_fields where processor_id=$processor->id order by id asc";
			$db->setQuery( $query );
			$fields = $db->loadObjectList();
			foreach($fields as $field){
				$processor->fields[$field->column_name]= $field->column_value;
			}
			
		}
		return $processor;
	}
	
	public static function getConfirmationPaymentDetails($confirmationId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_confirmations_payments where confirmation_id='$confirmationId'";
		$db->setQuery($query);
		$paymentDetails = $db->loadObject();
		return $paymentDetails;
	}
	
	/**
	 * Create payment processor
	 * 
	 * @param string $type
	 */
	public static function createPaymentProcessor($type){
		$processorFactory = new ProcessorFactory();
		$processor = $processorFactory->getProcessor($type);
		
		$initData = self::getPaymentProcessorDetails($type);
		$processor->initialize($initData);
		
		return $processor;
	}
	
	/**
	 * Add a payment into the databse
	 * @param object $paymentDetails
	 */
	public static function addPayment($paymentDetails){
		$confirmationsPayments = JTable::getInstance('ConfirmationsPayments','Table', array());
		//dmp($confirmationsPayments);
		//dmp($paymentDetails);
		if (!$confirmationsPayments->bind($paymentDetails)){
			JError::raiseWarning('error',$confirmationsPayments->getError());
			return false;
		}
		
		if (!$confirmationsPayments->check()){
			JError::raiseWarning('error',$confirmationsPayments->getError());
			return false;
		}
		
		if (!$confirmationsPayments->store()){
			JError::raiseWarning('error',$confirmationsPayments->getError());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add a payment into the databse
	 * @param object $paymentDetails
	 */
	public static function updatePayment($paymentDetails){
		$confirmationsPayments = JTable::getInstance('ConfirmationsPayments','Table', array());
		$result = $confirmationsPayments->updatePaymentStatus($paymentDetails->confirmation_id, $paymentDetails->amount, $paymentDetails->transaction_id,
				 $paymentDetails->payment_method, $paymentDetails->response_code, $paymentDetails->response_message,$paymentDetails->transactionTime, $paymentDetails->payment_status);
		return $result;
	}
	
	//general update of payment status
	public static function updateReservationPayment($paymentDetails){
		$confirmationsPayments = JTable::getInstance('ConfirmationsPayments','Table', array());
		$result = $confirmationsPayments->updateReservationPaymentStatus($paymentDetails->confirmation_id, $paymentDetails->transaction_id,
				$paymentDetails->payment_method, $paymentDetails->response_code, $paymentDetails->response_message,$paymentDetails->transactionTime, $paymentDetails->payment_status);
		return $result;
	}
	
	
}


?>