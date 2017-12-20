<?php
class Urldev_Hubmoney_Helper_Data extends Mage_Core_Helper_Abstract
{
  function getPaymentGatewayUrl() 
  {
    return Mage::getUrl('hubmoney/payment/gateway', array('_secure' => false));
  }
  
}