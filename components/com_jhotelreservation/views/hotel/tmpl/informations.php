<?php
/**
 * @copyright	Copyright (C) 2009-2012 CMSJunkie - All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');
$free = JText::_('LNG_FREE',true);
?>

<div class="hotel-box hotel-item informations">
	<h3><?php echo (isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARK_IMPORTANT_INFORMATION',true) : JText::_('LNG_HOTEL_IMPORTANT_INFORMATION',true)).' '.$this->hotel->hotel_name?></h3>
	<div class="left">
		<h4>
			<?php echo JText::_('LNG_CHECK_IN',true); ?>
		</h4>
		<p>
			<?php echo JText::_('LNG_AFTER',true); ?>&nbsp;<?php echo $hotel->informations->check_in  ?>&nbsp;<?php echo JText::_('LNG_HOURS',true); ?>
		</p>
		<h4>
			<?php echo JText::_('LNG_CHECK_OUT',true); ?>
		</h4>
		<p>
			<?php echo JText::_('LNG_BEFORE',true); ?>&nbsp;<?php echo $hotel->informations->check_out ?>&nbsp;<?php echo JText::_('LNG_HOURS',true); ?>
		</p>
		<h4>
			<?php echo JText::_('LNG_PARKING',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->parking==0?JText::_('LNG_NO_PARKING',true):JText::_('LNG_PARKING_AVAILABLE',true); ?>
			
			<?php
				$price =  $hotel->informations->price_parking>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($hotel->informations->price_parking,2):$free;
				echo ($hotel->informations->parking!=0 )?JText::_('LNG_FOR',true).' '.$price :'';
				if(isset($hotel->informations->parking_period)){
					echo  ' '.$hotel->informations->parking_period;
				}
			?>
		</p>
		<h4>
			<?php echo JText::_('LNG_PETS',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->pets==0?JText::_('LNG_NO_PETS',true):JText::_('LNG_PETS_ALLOWED',true); ?>
			 
			<?php 
				$price =  $hotel->informations->price_pets>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($hotel->informations->price_pets,2):$free;
				echo ( $hotel->informations->pets!=0)?JText::_('LNG_FOR',true).' '.$price.' '.JText::_('LNG_PER_NIGHT',true):'';
				echo  ' '.$hotel->informations->pet_info;
			?>
		</p>
		<h4>
			<?php echo JText::_('LNG_CITY_TAX',true); ?>
		</h4>
		<p>
			<?php if($hotel->informations->city_tax_percent == 1){
				echo$hotel->informations->city_tax.'%';
			}else{
				 echo $this->hotel->currency_symbol.' '.number_format($hotel->informations->city_tax,2).' p.p.p.n';
			}
			 ?>
		</p>
		<h4>
			<?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_NUMBER_OF_ROOMS_PARK',true) : JText::_('LNG_NUMBER_OF_ROOMS',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->number_of_rooms ?>
		</p>	
	</div>
	<div class="right">
		
		<h4>
			<?php echo JText::_('LNG_CANCELATION_CONDITIONS',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->cancellation_conditions ?>
		</p>
		
		<h4>
			<?php echo JText::_('LNG_INTERNET_WIFI',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->wifi==0?JText::_('LNG_NO_WIFI',true):JText::_('LNG_WIFI_AVAILABLE',true); ?>
			
			<?php
				$price =  $hotel->informations->price_wifi>0? $this->hotel->currency_symbol.' '.JHotelUtil::fmt($hotel->informations->price_wifi,2):$free;
				echo ($hotel->informations->wifi!=0 )?JText::_('LNG_FOR',true).' '.$price:'';
				if(isset($hotel->informations->wifi_period)){
					echo  ' '.$hotel->informations->wifi_period;
				}
			?>
		</p>
		<h4>
			<?php echo JText::_('LNG_SUITABLE_FOR_DISABLED',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->suitable_disabled==0?(isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_NO_SUITABLE_DISABLED_PARK',true): JText::_('LNG_NO_SUITABLE_DISABLED',true)):(isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_SUITABLE_DISABLED_AVAILABLE_PARK',true) : JText::_('LNG_SUITABLE_DISABLED_AVAILABLE',true)); ?>
		</p>
		<h4>
			<?php echo JText::_('LNG_PUBLIC_TRANSPORTATION',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->public_transport==0?JText::_('LNG_NO_PUBLIC_TRANPORTATION',true):JText::_('LNG_PUBLIC_TRANSPORTATION_AVAILABLE',true); ?>
		</p>
		<h4>	
			<?php echo isset($this->hotel->types) & $this->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_PARK_PAYMENT_OPTIONS',true) : JText::_('LNG_HOTEL_PAYMENT_OPTIONS',true); ?>
		</h4>
		<p>
			<?php 
				$paymentOptions ='';	
				
				if(isset($hotel->paymentOptions)){
					foreach( $hotel->paymentOptions as $po){
						$paymentOptions = $paymentOptions.$po->name.", ";
					} 

					$paymentOptions = substr($paymentOptions, 0, -2);
					echo $paymentOptions;
				}
			?>
		</p>
		<h4>
			<?php echo JText::_('LNG_CHILDREN_AGE_CATEGORY',true); ?>
		</h4>
		<p>
			<?php echo $hotel->informations->children_category ?>
		</p>
	</div>
	<div class="clear"></div>
</div>
