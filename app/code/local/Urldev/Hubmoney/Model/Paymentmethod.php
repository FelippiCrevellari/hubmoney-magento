<?php
class Urldev_Hubmoney_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
  protected $_code  = 'hubmoney';
  protected $_formBlockType = 'hubmoney/form_hubmoney';
  protected $_infoBlockType = 'hubmoney/info_hubmoney';
 
  public function assignData($data)
  {
    $info = $this->getInfoInstance();
     
    if ($data->getCardNumber())
    {
      $info->setCardNumber($data->getCardNumber());
    }
     
    if ($data->getCardCvv())
    {
      $info->setCardCvv($data->getCardCvv());
    }
	
	if ($data->getCardMonth())
    {
      $info->setCardMonth($data->getCardMonth());
    }
	
	if ($data->getCardYear())
    {
      $info->setCardYear($data->getCardYear());
    }
	
	if ($data->getCardName())
    {
      $info->setCardName($data->getCardName());
    }
	
	if ($data->getCardParc())
    {
      $info->setCardParc($data->getCardParc());
    }
 
    return $this;
  }
 
  public function validate()
  {
    parent::validate();
    $info = $this->getInfoInstance();
     
	 $errorMsg = '0';
	 
    if (!$info->getCardNumber())
    {
      $errorCode = 'invalid_data';
      $errorMsg = $this->_getHelper()->__("Numero do cartão é Obrigatório.\n");
    }
     
    if (!$info->getCardCvv())
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('CVV é Obrigatório.');
    }
	
	 if (!$info->getCardMonth())
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('Mês de validade é Obrigatório.');
    }
	
	 if (!$info->getCardYear())
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('Ano de validade é Obrigatório.');
    }
	
	 if (!$info->getCardName())
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('Nome no Cartão é Obrigatório.');
    }
	
	if (!$info->getCardParc())
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('Parcelamento é Obrigatório.');
    }
 
    if ($errorMsg != '0') 
    {
      Mage::throwException($errorMsg);
    }
  
	
    return $this;
	
  }

  
 
 /* public function getOrderPlaceRedirectUrl()
  {
    return Mage::getUrl('hubmoney/payment/', array('_secure' => false));
  }
  */
  


}