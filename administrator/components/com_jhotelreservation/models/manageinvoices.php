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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JHotelReservationModelManageInvoices extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
		$this->setId(JRequest::getVar('invoiceId'), JRequest::getVar('hotel_id'));
	}

	function setId($invoiceId, $hotelId)
	{
		// Set id and wipe data
		$this->_invoiceId	= $invoiceId;
		$this->_hotelId = $hotelId;
	}

	function createMonthlyInvoices(){
		dmp("createMonthlyInvoices");
		$hotelTable = $this->getTable('hotels');
		$confirmationsTable = $this->getTable('confirmations');

		if(JRequest::getVar('date')!=''){
			$date = JRequest::getVar('date');
		}
		else{
			$date = date("d-m-Y");
		}

		
		$startInvoiceDate = date("Y-m-d", mktime(00, 00, 00, date('m',strtotime( $date)), 01));
		$endInvoiceDate  = date("Y-m-d", mktime(23, 59, 59, date('m',strtotime($date))+1, 00));

		$startDate = date("Y-m-d", mktime(00, 00, 00, date('m',strtotime( $date))-1, 01));
		$endDate  = date("Y-m-d", mktime(23, 59, 59, date('m',strtotime( $date)), 00));
		
//  		dmp($startInvoiceDate);
//  		dmp($endInvoiceDate);
//  		dmp($startDate);
//  		dmp($endDate);

 		//get all invoices for current month that was generated for previous month.
		$hotels = $hotelTable->getAllHotelsWithoutMonthlyInvoice($startInvoiceDate, $endInvoiceDate);
		//dmp($hotels);
		foreach($hotels as $hotel){
			try	{
				//save hotel invoice
				$invoice = new stdClass();
				$invoice->date = date("Y-m-d",strtotime( $date));
				$invoice->hotelId = $hotel->hotel_id;
				$invoiceDetails = array();

				$this->storeInvoice($invoice);
				$reservations = $confirmationsTable->getHotelMonthlyReservations($hotel->hotel_id, $startDate, $endDate);
				dmp("Create invoice for hotel ".$hotel->hotel_id);
				$commission = 0;
				$amount = 0;
// 				dmp($reservations);
				if(isset($reservations) && is_array($reservations)){
					foreach($reservations as $reservation){
						$invoiceDetail = new stdClass();
						$invoiceDetail->invoiceId = $invoice->id;
						$invoiceDetail->reservationId=$reservation->confirmation_id;
						$invoiceDetail->name= $reservation->first_name.' '.$reservation->last_name;
						$invoiceDetail->arrival= $reservation->start_date;
						$invoiceDetail->departure= $reservation->end_date;
						$invoiceDetail->voucher= isset($reservation->voucher)?$reservation->voucher:'';
						$invoiceDetail->status= 0;
						$reservationCost = 0;
						if(isset($hotel->reservation_cost_val)){
							$reservationCost = $hotel->reservation_cost_val;
						}
						if($reservation->offer_id!=0)
							$reservationCost = $reservation->offer_reservation_cost_val;
						$invoiceDetail->amount= $reservation->total - $reservationCost;
						$reservationCommision = $hotel->commission;
						if($reservation->offer_id!=0)
							$reservationCommision = $reservation->offer_commission;
						
						$invoiceDetail->initialAmount= $invoiceDetail->amount;
						$invoiceDetail->commission =$reservationCommision;
						
						$invoiceDetail->commissionAmount= $invoiceDetail->amount * ($reservationCommision/100);
						$invoiceDetail->commissionAmount = round($invoiceDetail->commissionAmount,2);
						
						$amount += $invoiceDetail->amount;
						$commission += $invoiceDetail->commissionAmount;
	
						$row = $this->getTable("manageinvoicedetails");
							
						if (!$row->bind($invoiceDetail))
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
						$invoiceDetail->id = $this->_db->insertid();
						$invoiceDetails[]= $invoiceDetail;
					}
	
					$invoice->commissionAmount = round($commission,2);
					$invoice->reservationAmount = round($amount,2);
					//do not apply vat for Germany and Belgium
					if($hotel->country_id==20 || $hotel->country_id==54){
						$invoice->amount = round($commission ,2);
					} else if($hotel->country_id==161){
						$invoice->amount = round(($commission + $commission * VAT_HOLLAND/100),2);
					}else{
						$invoice->amount = round(($commission + $commission * VAT/100),2);
					}
					$this->storeInvoice($invoice);
					$invoice->invoiceDetails = $invoiceDetails;
				
					$this->issueInvoice($invoice, 0);
				}
			}catch (Exception $ex){
				//TODO threat exception
				dmp($ex);
			}
		}
	}

	function storeInvoice($invoice){
		$invoiceTable = $this->getTable("manageinvoices");
		if (!$invoiceTable->bind($invoice))
		{
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError($this->_db->getErrorMsg());
		}
			
		// Make sure the record is valid
		if (!$invoiceTable->check())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError($this->_db->getErrorMsg());
		}

		// Store the web link table to the database
		if (!$invoiceTable->store())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError($this->_db->getErrorMsg());
		}

		if($this->_db->insertid())
			$invoice->id = $this->_db->insertid();
		//save invoice details

		return $invoice;
	}
	
	function issueInvoices(){
		$invoiceTable = $this->getTable("manageinvoices");
		$invoices = $invoiceTable->getOpenInvoices();
		
		foreach($invoices as $invoice){
			$invoice->approvalDate = date("Y-m-d");
			//dmp($invoice);
			$this->issueInvoice($invoice, 1);
		}
	}

	function sendInvoice($data){
		$invoiceTable = $this->getTable("manageinvoices");
		$invoice = $invoiceTable->getInvoice($data["invoiceId"]);
		$invoice->approvalDate = date("Y-m-d");
		$invoice->approvalName = $data["approvalName"];
		
		return $this->issueInvoice($invoice, 1);
	}
	
	function issueInvoice($invoice, $status){
		$invoice->status = $status;
		$hotelTable = $this->getTable('hotels');
		$hotel = $hotelTable->getHotel($invoice->hotelId);
		
		$email = $this->prepareInvoiceEmail($hotel, $invoice);
		if(!isset($email))
			return false;
		
		$invoice->content = $email->content;
		$this->storeInvoice($invoice);
		
		$result = true;
		if($invoice->reservationAmount >0){
			$result = $this->sendInvoiceEmail($hotel, $email, $status);
		}
		
		return $result;
	}
	
	function prepareInvoiceEmail($hotel, $invoice){
		
		$email = new stdClass();
		$template = "Invoice Email";
		if(isset($invoice->invoiceDetails))
			$template = "Bookings List";
		
		$templ = $this->getEmailTemplate($hotel->hotel_id, $template );
		if( $templ ==null ){
			dmp(("No template found for hotel: ".$hotel->hotel_id));
			return null;
		}
		
		$applicationSettings = $this->getAppSettings();
		
		$email->content = $this->prepareEmail($hotel, $invoice, $templ->email_content, $applicationSettings);
		$email->subject = $templ->email_subject;
		
		$email->company_email = $applicationSettings->company_email;
		$email->company_name = $applicationSettings->company_name;
		
		return $email;
	}
	
	function sendInvoiceEmail($hotel, $email, $status)
	{

		$mode		 = 1 ;//html
		$ret = true;
		if($status ==0){
			//JMail::sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null)
			$ret = EmailService::sendEmail(
					$email->company_email,
					$email->company_name,
					$email->company_email,
					$hotel->email,
					null,
					null,
					$email->subject,
					$email->content,
					$mode
			);
		}else{
			$appSettings = JHotelUtil::getApplicationSettings();
			$emailAddress='';
			if($appSettings->send_invoice_to_email)
				$emailAddress = $appSettings->invoice_email;
			else
				$emailAddress = $hotel->email;
			//JMail::sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null)
			$ret = EmailService::sendEmail(
					$email->company_email,
					$email->company_name,
					$email->company_email,
					$emailAddress,
					null,
					null,
					$email->subject,
					$email->content,
					$mode
			);
		}
		
		return $ret;
	}
	
	function prepareEmail($hotel, $invoice, $templEmail, $appSettings)
	{
		
		$invoiceHotelDetails = $hotel->hotel_name." <br> ".$hotel->hotel_address." <br> ".$hotel->hotel_city.", ".$hotel->hotel_county." <br> ".$hotel->country_name;
		
		$invoiceFields = $this->generateInvoiceFieldsHTML($invoice);

		$bookingsList = $this->generateBookingsListHTML($invoice);
		
		$templEmail = str_replace("[company_logo]", "<img src='".JURI::root().PATH_PICTURES.$appSettings->logo_path."' alt='logo'>",				$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_DATE),				JHotelUtil::convertToFormat($invoice->date),				$templEmail);
		$templEmail = str_replace(EMAIL_INVOICE_DATE, 								JHotelUtil::convertToFormat($invoice->date),				$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_NUMBER), 			$invoice->id,				$templEmail);
		$templEmail = str_replace(EMAIL_INVOICE_NUMBER, 							$invoice->id,				$templEmail);

		$templEmail = str_replace(htmlspecialchars(EMAIL_HOTEL_NUMBER), 			$hotel->hotel_number,		$templEmail);
		$templEmail = str_replace(EMAIL_HOTEL_NUMBER, 								$hotel->hotel_number,		$templEmail);
		
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_HOTEL_DETAILS),	$invoiceHotelDetails, 		$templEmail);
		$templEmail = str_replace(EMAIL_INVOICE_HOTEL_DETAILS,						$invoiceHotelDetails, 		$templEmail);

		$templEmail = str_replace(htmlspecialchars(EMAIL_BOOKINGS_LIST),			$bookingsList, 		$templEmail);
		$templEmail = str_replace(EMAIL_BOOKINGS_LIST,								$bookingsList, 		$templEmail);
		
		$templEmail = str_replace(htmlspecialchars(EMAIL_INVOICE_FIELDS),			$invoiceFields,				$templEmail);
		$templEmail = str_replace(EMAIL_INVOICE_FIELDS,								$invoiceFields, 			$templEmail);

		$templEmail = str_replace(htmlspecialchars(EMAIL_COMPANY_NAME),				$appSettings->company_name,	$templEmail);
		$templEmail = str_replace(EMAIL_COMPANY_NAME,								$appSettings->company_name,	$templEmail);
		
		return "<html><body>".$templEmail.'</body></html>';
	}
	
	function generateInvoiceFieldsHTML($invoice){
		$hotelTable = $this->getTable('hotels');
		$hotel = $hotelTable->getHotel($invoice->hotelId);
		dmp($invoice);
		$vat = VAT;
		if($hotel->country_id==20 || $hotel->country_id==54){
			$vat = 0;
		} else if($hotel->country_id==161){
			//different VAT for Holland
			$vat = VAT_HOLLAND;
		}
		
		
		$style = "\"border:1px solid  #333\"";
		$invoiceFields= "<table  cellspacing='0' cellpadding='5'>
									<tr>
										<td style=$style>
											".JText::_('LNG_DESCRIPTION',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_AMOUNT_EXCL_VAT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_VAT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_VAT_AMOUNT',true)."
										</td>
										<td style=$style>
											".JText::_('LNG_AMOUNT_INCL_VAT',true)."
										</td>
									</tr>
									<tr>
										<td style=$style>
											".JText::_('LNG_COMMISSION',true)."
										</td>
										<td style=$style>
											 &#8364; ".round($invoice->commissionAmount,2)."
										</td>
										<td style=$style>
											 ".$vat." %
										</td>
										<td style=$style>
											&#8364; ".round(($invoice->commissionAmount * $vat/100),2)."
										</td>
										<td style=$style>
											&#8364; ".round(($invoice->commissionAmount + $invoice->commissionAmount * $vat/100),2)."
										</td>
									</tr>
									</table
								";
		dmp($invoiceFields);
		return $invoiceFields;
	}
	
	function generateBookingsListHTML($invoice){
		//if invoice details are defined only invoice details are sent
		$bookingsList = '';
		$style = "\"border:1px solid  #333\"";
		//dmp($invoice->invoiceDetails);
		if(isset($invoice->invoiceDetails)){
			foreach($invoice->invoiceDetails as $detail){
				$bookingsList=$bookingsList."<TR>
											<TD style=$style>
												". $detail->reservationId."
											</TD>
											<TD style=$style>". $detail->name."</TD>
											<TD style=$style>". JHotelUtil::convertToFormat($detail->arrival)."</TD>
											<TD style=$style>". JHotelUtil::convertToFormat($detail->departure)."</TD>
											<TD style=$style nowrap='nowrap'> &#8364; ". $detail->amount." </TD>
											<TD style=$style nowrap='nowrap'> &#8364; ". $detail->commissionAmount."</TD>
										</TR>
										";
			}
				
			$bookingsList="<TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"6\" class=\"adminlist\" align=center border=0>
										<thead>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_RESERVATION_NUMBER',true)) ."</B></th>
											<th style=$style width='30%' align=center ><B>".ucfirst(JText::_('LNG_NAME',true)) ."</B></th>
											<th style=$style width='20%' align=center><B>". ucfirst(JText::_('LNG_ARRIVAL',true)) ."</B></th>
											<th style=$style width='20%' align=center><B>". ucfirst(JText::_('LNG_DEPARTURE',true)) ."</B></th>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_AMOUNT',true)) ."</B></th>
											<th style=$style width='10%' align=center><B>". ucfirst(JText::_('LNG_COMMISSION',true)) ."</B></th>
										</thead>
										<tbody>".$bookingsList."
											<tr>
												<td colspan=\"3\">&nbsp;</td>
												<td  nowrap='nowrap' align=\"right\"><strong>". JText::_('LNG_TOTAL',true).": </strong></td>
												<td  nowrap='nowrap' id=\"total-amount\" style=\"border-top:1px solid #333\"> &#8364; ". $invoice->reservationAmount ."</td>
												<td  nowrap='nowrap' id=\"total-commission\"style=\"border-top:1px solid #333\"> &#8364; ". $invoice->commissionAmount ."</td>
											</tr>
									</tbody>
								</TABLE>
					";
		}
		dmp($bookingsList);
		return $bookingsList;
	}
	
	function getEmailTemplate($hotelId, $template)
	{
		$query = ' SELECT * FROM #__hotelreservation_emails WHERE hotel_id="'.$hotelId.'" AND is_default  = 1 AND email_type = "'.$template.'"';
		$this->_db->setQuery( $query );
		$templ= $this->_db->loadObject();
		return $templ;
	}
	
	function getAppSettings()
	{
		// Load the data
	
		$query = ' SELECT * FROM #__hotelreservation_applicationsettings ';
		$this->_db->setQuery( $query );
		$appSettings = $this->_db->loadObject();
	
		return $appSettings;
	}
	/**
	 *
	 * Enter description here ...
	 */
	function &getDatas()
	{
		$invoiceTable = $this->getTable("manageinvoices");
		$this->_data = $invoiceTable->getHotelInvoices($this->_hotelId);
		//dmp($this->_data);
		return $this->_data;
	}


	function &getData()
	{
	
		$invoiceTable = $this->getTable("manageinvoices");
		$invoiceDetailsTable = $this->getTable("manageinvoicedetails");
		$this->_data = $invoiceTable->getInvoice($this->_invoiceId);
		$this->_data->details = $invoiceDetailsTable->getInvoiceDetails($this->_data->id);

		return $this->_data;
	}

	function &getHotelId()
	{
		return $this->_hotelId;
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

	function &getHotel()
	{
		$query = 	' SELECT
							h.*,
							c.country_name,
							hp.*
							FROM #__hotelreservation_hotels				h
							LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
							left join #__hotelreservation_paymentsettings hp USING (hotel_id) 
							WHERE hotel_id = '.$this->_hotelId;
		//dmp($query);
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}

	function store($data){
		$result = true;
		$ids = $data["detailIds"];
		$statuses= $data["detailStatus"];

		$amount = 0;
		$comission= 0;
		$hotelTable = $this->getTable('hotels');
		
		for($i=0;$i<count($ids);$i++){
			$status = $statuses[$i];
			$invoiceDetailsTable = $this->getTable("manageinvoicedetails");
			$invoiceDetail = $invoiceDetailsTable->getInvoiceDetail($ids[$i]);
			$invoiceDetail->status = $status;
			
			if($status == 1){
				$invoiceDetail->amount = $invoiceDetail->initialAmount;
				$invoiceDetail->commissionAmount = $invoiceDetail->amount *($invoiceDetail->commission/100);
			}else if($status == 2){
				$invoiceDetail->amount = 0;
				$invoiceDetail->commissionAmount =0;
			}else if($status ==3){
				$invoiceDetail->amount = $data["newamount-".$invoiceDetail->id];
				$invoiceDetail->commissionAmount = $invoiceDetail->amount *($invoiceDetail->commission/100);
			}
			
			$amount += $invoiceDetail->amount;
			$commission += $invoiceDetail->commissionAmount;
			
			//dmp($invoiceDetail);
			try {
				if (!$invoiceDetailsTable->bind($invoiceDetail))
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
					
				// Make sure the record is valid
				if (!$invoiceDetailsTable->check())
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
				
				// Store the web link table to the database
				if (!$invoiceDetailsTable->store())
				{
					throw( new Exception($this->_db->getErrorMsg()) );
					$this->setError($this->_db->getErrorMsg());
				}
			}catch( Exception $ex ){
				dmp($ex);
				//exit();
				return false;
			}
			//if(!$invoiceDetailsTable->updateInvoiceDetailStatus($ids[$i],$statuses[$i]))
			//$result = false;
		}

		//exit;

		$invoiceTable = $this->getTable("manageinvoices");
		$invoice = $invoiceTable->getInvoice($data["invoiceId"]);
		$invoice->approvalName = $data["approvalName"];
		$invoice->agreed = $data["agreed"];
		$invoice->commissionAmount = round($commission,2);
		$invoice->reservationAmount = round($amount,2);
		
		
		$hotel = $hotelTable->getHotel($invoice->hotelId);
		//no VAT for Germany an Belgium
		if($hotel->country_id==20 || $hotel->country_id==54){
			$invoice->amount = round($commission ,2);
		} else if($hotel->country_id==161){
			//different VAT for Holland
			$invoice->amount = round(($commission + $commission * VAT_HOLLAND/100),2);
		}else{
			$invoice->amount = round(($commission + $commission * VAT/100),2);
		}
		
	
		try {
			if (!$invoiceTable->bind($invoice))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
				
			// Make sure the record is valid
			if (!$invoiceTable->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		
			// Store the web link table to the database
			if (!$invoiceTable->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
				$this->setError($this->_db->getErrorMsg());
			}
		}catch( Exception $ex ){
			dmp($ex);
			//exit();
			return false;
		}
		//exit;
		return $result;
	}
}
?>