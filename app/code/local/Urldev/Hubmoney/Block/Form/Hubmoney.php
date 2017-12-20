<?php
class Urldev_Hubmoney_Block_Form_Hubmoney extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('hubmoney/form/hubmoney.phtml');
  }
}