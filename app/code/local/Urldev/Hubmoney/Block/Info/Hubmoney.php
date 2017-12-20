<?php
class Urldev_Hubmoney_Block_Info_Hubmoney extends Mage_Payment_Block_Info
{
  protected function _prepareSpecificInformation($transport = null)
  {
    if (null !== $this->_paymentSpecificInformation) 
    {
      return $this->_paymentSpecificInformation;
    }
     
    $data = array();
    if ($this->getInfo()->getCardNumber()) 
    {
      $data[Mage::helper('payment')->__('Card Number')] = $this->getInfo()->getCardNumber();
    }
     
    if ($this->getInfo()->getCardCvv()) 
    {
      $data[Mage::helper('payment')->__('Card Cvv')] = $this->getInfo()->getCardCvv();
    }
	
	if ($this->getInfo()->getCardMonth()) 
    {
      $data[Mage::helper('payment')->__('Card Month')] = $this->getInfo()->getCardMonth();
    }
	
	if ($this->getInfo()->getCardYear()) 
    {
      $data[Mage::helper('payment')->__('Card Year')] = $this->getInfo()->getCardYear();
    }
	
	if ($this->getInfo()->getCardName()) 
    {
      $data[Mage::helper('payment')->__('Card Name')] = $this->getInfo()->getCardName();
    }
 
    $transport = parent::_prepareSpecificInformation($transport);
     
    return $transport->setData(array_merge($data, $transport->getData()));
  }
}