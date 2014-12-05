<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

/**
 * 
* This file builds the replies to the 4b platform petitions and actions that
* needs to be done when the payment succees or the payment fails.
*
* That's how it works: We redirect a user to the 4b platform with just an
* Order ID. Then the 4b platform begins a petition to the 'details' URL to
* know the price of the Oder. And then it makes another petition depending on
* the payment status (OK or KO).

Login at https://tpv.4b.es/config, enter the username and the password, click "Configuraci�n"
in the menu at the left, and enter the following URLs, replacing YOURDOMAINNAME

URL que devuelve el desglose de la compra: YOURDOMAINNAME/commerce_4b/checkout_details
URL que graba el resultado en la BD del comercio (TRANSACCIONES AUTORIZADAS): YOURDOMAINNAME/commerce_4b/payment_response
URL que graba el resultado en la BD del comercio (TRANSACCIONES DENEGADAS): YOURDOMAINNAME/commerce_4b/payment_response
URL de continuaci�n posterior a la p�gina de recibo: YOURDOMAINNAME/commerce_4b/return
URL de recibo (TRANSACCI�N AUTORIZADA): YOURDOMAINNAME/commerce_4b/payment_response
URL de recibo (TRANSACCI�N DENEGADA): YOURDOMAINNAME/commerce_4b/payment_respons
*/


class JHotelReservationControllerProcessorFourB extends JController
{
	function __construct()
	{
		parent::__construct();
	
	}	
	function display(){
		parent::display();
	}
	
	function rdet(){
		$model = $this->getModel('processorfourb');
		$model->processFourBDetails();
	}

	function payOK() {
		JHotelReservationModelVariables::writeMessage(" call function payOK() ");
		
		$model = $this->getModel('processorfourb');
		$data = array();
		$post = JRequest::get( 'GET' );
		
		$data['order_id']        =  $post['pszPurchorderNum'];
		$confirmationId  		 = $post['pszPurchorderNum'];
		$data['status']          =  $post['result'];
		$data['store']           =  $post['store'];
		$data['pszTxnDate']      =  $post['pszTxnDate'];
		$data['tipotrans']       =  $post['tipotrans'];
		$data['pszApprovalCode'] =  $post['pszApprovalCode'];
		$data['pszTxnID']        =  $post['pszTxnID'];
		$data['MAC']             =  $post['MAC'];
	
		list($errors, $message) = $model->validatePetition($data);
		if ($data['status'] != 0 || $errors) {
			JHotelReservationModelVariables::writeMessage("error in validation -".$message.' with status='.$data['status']);
		}
		else {
			// If no errors, validate payment	.
			JHotelReservationModelVariables::writeMessage( 'Order '.$confirmationId.' => Payment OK. ');
			JHotelReservationModelVariables::writeMessage( 'Approval code is '. $data['pszApprovalCode']);
			$model->updatePayment($confirmationId);
		}
	}
	
	function payKO() {
		JHotelReservationModelVariables::writeMessage(" call function payKO() ");
		
		$post = JRequest::get( 'GET' );
		$data = array();
		$model = $this->getModel('processorfourb');
		
		$data['order_id']        = $post['pszPurchorderNum'];
		$confirmationId  		 = $post['pszPurchorderNum'];
		$data['status']          =  $post['result'];
		$data['store']           =  $post['store'];
		$data['pszTxnDate']      =  $post['pszTxnDate'];
		$data['tipotrans']       =  $post['tipotrans'];
		$data['coderror']        = $post['coderror'];
		$data['deserror']        = $post['deserror'];
		$data['MAC']             = $post['MAC'];
	
		list($errors, $message) = $model->validatePetition($data);
		if (($data['status'] != 1 && $data['status'] != 2) || $errors) {
			JHotelReservationModelVariables::writeMessage("error in validation -".$message.' with status='.$data['status']);
		}
		else {
			// If no errors, log it could not be paid.
			$model->cancelPayment($confirmationId);
		}
	}
	
	//called when the client is redirect back to the website 
	function retur(){
		JHotelReservationModelVariables::writeMessage(" call function return() ");
		$post = JRequest::get( 'GET' );
		$model = $this->getModel('processorfourb');
		
		$data['order_id']        = $post['pszPurchorderNum'];
		$confirmationId  		 = $post['pszPurchorderNum'];
		$data['status']          =  $post['result'];
		$data['store']           =  $post['store'];
		$data['pszTxnDate']      =  $post['pszTxnDate'];
		$data['tipotrans']       =  $post['tipotrans'];

		
		list($errors, $message) = $model->validatePetition($data);
		if($errors)
			JHotelReservationModelVariables::writeMessage("error in validation -".$message);
		//payment successfull redirect confirmation page
		else if (($data['status'] == 0)) {
			$this->showPaymentResult($confirmationId,'true');
		}
		//payment not successfull redirect to payment page
		else if (($data['status'] == 1 || $data['status'] == 2)){
			$data['coderror']        = $post['coderror'];
			$data['deserror']        = $post['deserror'];
			JError::raiseWarning('error',JText::_($data['coderror']." ".$data['deserror'],true));
			$this->showPaymentResult($confirmationId,'false');
		}
	}
	
	
	private function showPaymentResult($confirmationId,$success){
		JHotelReservationModelVariables::writeMessage(" call function showPaymentResult() - confirmationId: ".$confirmationId);
		$tipOper = 5; 
		$view = "confirmation";
		if($success=='false'){
			$tipOper = 4;
			$view = "guestinformation";
		}
		JRequest::setVar( 'tip_oper',  $tipOper);
		try
		{
			$modelVariables = new JHotelReservationModelVariables();
			if(!$modelVariables->load($confirmationId, null, null))
				throw new Exception(JText::_('LNG_CANNOT_LOAD_MODEL',true) );
			$view	= $this->getView($view);
			$view->setModel( $modelVariables, true );
			$view->display();
		}
		catch( Exception $e )
		{
			JHotelReservationModelVariables::writeMessage("Error accured processing showPaymentResult()- 4B Spain function.");
			JHotelReservationModelVariables::writeMessage("Error: ".$e);
			JRequest::setVar( 'tip_oper', 0 );
			$this->display();
			return;
		}
	}
	
	
	
	
		
}