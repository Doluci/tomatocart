<?php
/*
  $Id: payfast.php $
  Eloras Web Solutions for Tomatocart
  http://www.tomatocart.co.za

  Copyright (c) 2011 Eloras Web Solutions;

  This program is commercial software; you cannot redistribute it and/or modify
  it without the express and written permission of Eloras Web Solutions;.
*/

/**
 * The administration side of the payfast payment module
 */

  class osC_Payment_payfast extends osC_Payment_Admin {

/**
 * The administrative title of the payment module
 *
 * @var string
 * @access private
 */
  var $_title;
  
/**
 * The code of the payment module
 *
 * @var string
 * @access private
 */

  var $_code = 'payfast';
  
/**
 * The developers name
 *
 * @var string
 * @access private
 */

  var $_author_name = 'eloraswebsolutions';
  
/**
 * The developers address
 *
 * @var string
 * @access private
 */  
  
  var $_author_www = 'http://www.eloraswebsolutions.co.za';
  
/**
 * The status of the module
 *
 * @var boolean
 * @access private
 */

  var $_status = false;
  
/**
 * Constructor
 */

  function osC_Payment_payfast() {
    global $osC_Language;
    
    $this->_title = $osC_Language->get('payment_payfast_title');
    $this->_description = $osC_Language->get('payment_payfast_description');
    $this->_method_title = $osC_Language->get('payment_payfast_method_title');
    $this->_status = (defined('MODULE_PAYMENT_PAYFAST_STATUS') && (MODULE_PAYMENT_PAYFAST_STATUS == '1') ? true : false);
    $this->_sort_order = (defined('MODULE_PAYMENT_PAYFAST_SORT_ORDER') ? MODULE_PAYMENT_PAYFAST_SORT_ORDER : null);
  }
  
/**
 * Checks to see if the module has been installed
 *
 * @access public
 * @return boolean
 */

  function isInstalled() {
    return (bool)defined('MODULE_PAYMENT_PAYFAST_STATUS');
  }
  
/**
 * Installs the module
 *
 * @access public
 * @see osC_Payment_Admin::install()
 */

  function install() {
    global $osC_Database, $osC_Language;
    
    parent::install();
    
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ("*Enable Payfast Module", "MODULE_PAYMENT_PAYFAST_STATUS", "-1", "Do you want to accept credit cart payments?", "6", "0", "osc_cfg_set_boolean_value(array(1, -1))", now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ("Sort order of display.", "MODULE_PAYMENT_PAYFAST_SORT_ORDER", "0", "Sort order of display. Lowest is displayed first.", "6", "0", now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ("Payment Zone", "MODULE_PAYMENT_PAYFAST_ZONE", "0", "If a zone is selected, only enable this payment method for that zone.", "6", "2", "osc_cfg_use_get_zone_class_title", "osc_cfg_set_zone_classes_pull_down_menu", now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ("*Set Order Status", "MODULE_PAYMENT_PAYFAST_ORDER_STATUS_ID", ' . ORDERS_STATUS_PAID . ', "Set the status of orders made with this payment module to this value", "6", "0", "osc_cfg_set_order_statuses_pull_down_menu", "osc_cfg_use_get_order_status_title", now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ("Payfast Merchant ID", "MODULE_PAYMENT_PAYFAST_MERCHANT_ID", "", "Your customer ID as supplied by Payfast", "6", "0",  now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ("Test mode", "MODULE_PAYMENT_PAYFAST_TEST_MODE", "True", "Run module in test mode", "6", "0", "osc_cfg_set_boolean_value(array(\'True\', \'False\'))", now())');
    $osC_Database->simpleQuery('insert into ' . TABLE_CONFIGURATION . ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ("PAYFAST Merchant Key", "MODULE_PAYMENT_PAYFAST_MERCHANT_KEY", "", "Your Secret Key which should match exactly the secret key you have set in the PAYFAST Merchant Tool", "6", "0", now())');
  }

/**
 * Return the configuration parameter keys in an array
 *
 * @access public
 * @return array
 */

  function getKeys() {
    if (!isset($this->_keys)) {
      $this->_keys = array('MODULE_PAYMENT_PAYFAST_STATUS', 
                           'MODULE_PAYMENT_PAYFAST_ZONE', 
                           'MODULE_PAYMENT_PAYFAST_ORDER_STATUS_ID', 
                           'MODULE_PAYMENT_PAYFAST_SORT_ORDER', 
                           'MODULE_PAYMENT_PAYFAST_MERCHANT_ID', 
                           'MODULE_PAYMENT_PAYFAST_MERCHANT_KEY',
                           'MODULE_PAYMENT_PAYFAST_TEST_MODE');
    }
  
    return $this->_keys;
 } 
}
?>
