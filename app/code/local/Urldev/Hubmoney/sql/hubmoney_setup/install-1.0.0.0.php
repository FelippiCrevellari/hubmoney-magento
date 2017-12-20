<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` 
ADD `card_number` VARCHAR( 255 ) NOT NULL,
ADD `card_cvv` VARCHAR( 255 ) NOT NULL;
ADD `card_month` VARCHAR( 255 ) NOT NULL;
ADD `card_year` VARCHAR( 255 ) NOT NULL;
ADD `card_parc` VARCHAR( 255 ) NOT NULL;
  
ALTER TABLE `{$installer->getTable('sales/order_payment')}` 
ADD `card_number` VARCHAR( 255 ) NOT NULL,
ADD `card_cvv` VARCHAR( 255 ) NOT NULL;
ADD `card_month` VARCHAR( 255 ) NOT NULL;
ADD `card_year` VARCHAR( 255 ) NOT NULL;
ADD `card_name` VARCHAR( 255 ) NOT NULL;
ADD `card_parc` VARCHAR( 255 ) NOT NULL;

");
$installer->endSetup();