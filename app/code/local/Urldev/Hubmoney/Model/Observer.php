<?php
class Urldev_Hubmoney_Model_Observer extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	
protected $_code  = 'hubmoney';
protected $_formBlockType = 'hubmoney/form_hubmoney';
protected $_infoBlockType = 'hubmoney/info_hubmoney';
    
  
		
public function autorizePay(Varien_Event_Observer $observer)
  {

	$postData = $observer->getEvent()->getData();

    $scopeId = Mage::getModel('core/website')->load($postData['website'])->getId();
    $user  = Mage::app()->getWebsite($scopeId)->getConfig('payment/hubmoney/user_name');
	$password  = Mage::app()->getWebsite($scopeId)->getConfig('payment/hubmoney/user_password');
    $wokingop  = Mage::app()->getWebsite($scopeId)->getConfig('payment/hubmoney/user_wokingop');
	$gatwayuuid  = Mage::app()->getWebsite($scopeId)->getConfig('payment/hubmoney/user_gatwayuuid');
	$capture = Mage::app()->getWebsite($scopeId)->getConfig('payment/hubmoney/capture_autorize');
    $currentScope = 'websites';
 
	$_order = $observer->getEvent()->getOrder();
	
	$customer_id = $_order->getCustomerId();
	$customer = Mage::getModel('customer/customer')->load($customer_id);


	$payment = $_order->getPayment();

	$uniqueid = $orderId;
	$urlautorize ='https://homl.gateway.hubmoney.com/api/acquire/card/authorize'; 
	$cardnumber = $payment->getCardNumber(); 
	$cvv = $payment->getCardCvv();
	$cardmonth = $payment->getCardMonth();  
	$cardyear = $payment->getCardYear();  
	$cardname = $payment->getCardName();  
	$cardparc = $payment->getCardParc();
		
	$valor = $_order->getGrandTotal();
	$semjuros = Mage::app()->getWebsite()->getConfig('payment/hubmoney/sem_juros');

		if ($cardparc == '2'){
			if ($semjuros >= '2'){$valor = $valor;}
			else{ $valor = $valor * 1.0249;}
			}
		if ($cardparc == '3'){
			if ($semjuros >= '3'){$valor = $valor;}
			else{ $valor = $valor * 1.0478;}
			}
		if ($cardparc == '4'){
			if ($semjuros >= '4'){$valor = $valor;}
			else{ $valor = $valor * 1.0717;}
			}
		if ($cardparc == '5'){
			if ($semjuros >= '5'){$valor = $valor;}
			else{ $valor = $valor * 1.0899;}
			}
		if ($cardparc == '6'){
			if ($semjuros >= '6'){$valor = $valor;}
			else{ $valor = $valor * 1.1049;}
			}
		if ($cardparc == '7'){
			if ($semjuros >= '7'){$valor = $valor;}
			else{ $valor = $valor * 1.1183;}
			}
		if ($cardparc == '8'){
			if ($semjuros >= '8'){$valor = $valor;}
			else{ $valor = $valor * 1.1249;}
			}
		if ($cardparc == '9'){
			if ($semjuros >= '9'){$valor = $valor;}
			else{ $valor = $valor * 1.1321;}
			}
		if ($cardparc == '10'){
			if ($semjuros >= '10'){$valor = $valor;}
			else{ $valor = $valor * 1.1459;}
			}

	$ch2 = curl_init();
	curl_setopt($ch2, CURLOPT_URL, "https://homl.gateway.hubmoney.com/api/acquire/auth");
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch2, CURLOPT_POSTFIELDS, "{  \n   \"requesterId\": \"$user\",  \n   \"requesterPassword\":\"$password\",  \n   \"tokenExpirationPeriod\": 30  \n }");
	curl_setopt($ch2, CURLOPT_POST, 1);
	$headers2 = array();
	$headers2[] = "Content-Type: application/json";
	$headers2[] = "Accept: application/json";
	$headers2[] = "Unique-Trx-Id: $uniqueid";
	$headers2[] = "Working-Operation-Uuid: $wokingop";
	$headers2[] = "Gateway-Uuid: $gatwayuuid";
	curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
	$result2 = curl_exec($ch2);
	if (curl_errno($ch2)) {
		echo 'Error:' . curl_error($ch2);
	}
	curl_close ($ch2);

	$obj = json_decode($result2);
	$token = $obj->token;
		

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "$urlautorize");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{  \n \"amount\": $valor,  \n \"installments\": $cardparc,  \n \"transactionType\": 1,  \n \"merchantOrderId\":\"$uniqueid\",  \n \"capture\": $capture,  \n \"card\": {  \n \"pan\": \"$cardnumber\",  \n \"securityCode\": \"$cvv\",  \n \"expirationMonth\": $cardmonth,  \n \"expirationYear\": $cardyear,  \n \"name\": \"$cardname\"  \n }  \n }");
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Accept: application/json";
	$headers[] = "Requester-Id: $user";
	$headers[] = "Requester-Token: $token";
	$headers[] = "Unique-Trx-Id: $uniqueid";
	$headers[] = "Gateway-Uuid: $gatwayuuid";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	$obj = json_decode($result);
	
	$cardDisplayNumber = $obj->cardDisplayNumber;
	if ($cardDisplayNumber == ''){
		
		$_order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();

	}else {
		
		 $payment->setCardNumber($cardDisplayNumber);
	}
	
	
	// Enviar Holder 
	
	
	$ch3 = curl_init();

	curl_setopt($ch3, CURLOPT_URL, "https://homl.gateway.hubmoney.com/api/acquire/auth");
	curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch3, CURLOPT_POSTFIELDS, "{  \n   \"requesterId\": \"$user\",  \n   \"requesterPassword\":\"$password\",  \n   \"tokenExpirationPeriod\": 30  \n }");
	curl_setopt($ch3, CURLOPT_POST, 1);
	$headers3 = array();
	$headers3[] = "Content-Type: application/json";
	$headers3[] = "Accept: application/json";
	$headers3[] = "Unique-Trx-Id: $uniqueid";
	$headers3[] = "Working-Operation-Uuid: $wokingop";
	curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers3);
	$result3 = curl_exec($ch3);
	if (curl_errno($ch3)) {
		echo 'Error:' . curl_error($ch3);
	}
	curl_close ($ch3);
	$obj3 = json_decode($result3);
	$token2 = $obj3->token;
		
	$urlholder ='https://homl.gateway.hubmoney.com/api/acquire/cardManager/add';
	
	$holdername = $customer->getName();
	$holdervat =  $customer->getTaxvat();
	$holderemail =    $customer->getEmail();
	$holderbtd =  $customer->getDob();
/*	
	$holdername = 'Felippi Crevellari';
	$holdervat = '15236256860';
	$holderemail = 'silmara@urlstore.com.br';
	$holderbtd = '1993-10-20';
*/

	
	$ch4 = curl_init();
	curl_setopt($ch4, CURLOPT_URL, "$urlholder");
	curl_setopt($ch4, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch4, CURLOPT_POSTFIELDS, "{  \n   \"cardTypeUUID\": \"1b22cda8-cfac-463e-85f4-2969c0634c1e\",  \n   \"card\": {  \n   \n     \"pan\": \"$cardnumber\",  \n     \"securityCode\": \"$cvv\",  \n     \"expirationMonth\": $cardmonth,  \n     \"expirationYear\": $cardyear,  \n     \"name\": \"$cardname\"  \n   },  \n   \"cardHolder\": {  \n     \"entity\": {  \n       \"name\": \"$holdername\",  \n       \"vatNumber\": \"$holdervat\",  \n       \"email\": \"$holderemail\" ,  \n       \"birthDate\": \"$holderbtd\"  \n     }  \n }  \n }");
	curl_setopt($ch4, CURLOPT_POST, 1);
	$headers4 = array();
	$headers4[] = "Content-Type: application/json";
	$headers4[] = "Accept: application/json";
	$headers4[] = "Requester-Id: $user";
	$headers4[] = "Requester-Token: $token2";
	$headers4[] = "Unique-Trx-Id: $uniqueid";
	curl_setopt($ch4, CURLOPT_HTTPHEADER, $headers4);

	$result4 = curl_exec($ch4);
	if (curl_errno($ch4)) {
		echo 'Error:' . curl_error($ch4);
	}
	curl_close ($ch4);


		
		return $this;
  }
  
  
  
}
  ?>