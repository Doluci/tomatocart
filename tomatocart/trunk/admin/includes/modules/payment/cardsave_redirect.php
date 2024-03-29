<?php
/*
  $Id: cardsave_redirect.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The administration side of the Amazon payment module
 */

  class osC_Payment_Cardsave_redirect extends osC_Payment_Admin {

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

    var $_code = 'cardsave_redirect';

/**
 * The developers name
 *
 * @var string
 * @access private
 */

    var $_author_name = 'TomatoCart';

/**
 * The developers address
 *
 * @var string
 * @access private
 */

    var $_author_www = 'http://www.tomatocart.com';

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

    function osC_Payment_Cardsave_redirect() {
      global $osC_Language;

      $this->_title = $osC_Language->get('payment_cardsave_redirect_title');
      $this->_description = $osC_Language->get('payment_cardsave_redirect_description');
      $this->_method_title = $osC_Language->get('payment_cardsave_redirect_method_title');
      $this->_status = (defined('MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS') && (MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_CARDSAVE_REDIRECT_SORT_ORDER') ? MODULE_PAYMENT_CARDSAVE_REDIRECT_SORT_ORDER : null);
    }

/**
 * Checks to see if the module has been installed
 *
 * @access public
 * @return boolean
 */

    function isInstalled() {
      return (bool)defined('MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS');
    }

/**
 * Installs the module
 *
 * @access public
 * @see osC_Payment_Admin::install()
 */

    function install() {
      global $osC_Database;

      parent::install();
      
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Text Title', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_TEXT_PUBLIC_TITLE', 'Credit Card', 'Title to use on payment form.', '6', '0', now())"); 
		  $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Enable Cardsave Redirect Module', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS', '-1', 'Do you want to accept payments through the Cardsave gateway?', '6', '1', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
		  $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '0', 'osc_cfg_use_get_zone_class_title', 'osc_cfg_set_zone_classes_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant ID', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_ID', 'MerchantID', 'Merchant ID to use.', '6', '2', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant Password', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_PASSWORD', 'MerchantPassword', 'The gateway account password.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Pre-Shared Key', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_PRESHARED_KEY', 'yourPSK', 'The Pre-Shared Key from the Cardsave MMS for this gateway account.', '6', '0', now())");
		  $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_CURRENCY', 'GBP', 'The currency to use for credit card transactions.', '6', '3', 'osc_cfg_set_boolean_value(array(\'GBP\',\'EUR\', \'USD\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CARDSAVE_REDIRECT_ORDER_STATUS_ID', '" . DEFAULT_ORDERS_STATUS_ID . "', 'Set the status of orders made with this payment module to this value', '6', '0', 'osc_cfg_set_order_statuses_pull_down_menu', 'osc_cfg_use_get_order_status_title', now())");
  }

/**
 * Return the configuration parameter keys in an array
 *
 * @access public
 * @return array
 */

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_CARDSAVE_REDIRECT_TEXT_PUBLIC_TITLE',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS',
        										 'MODULE_PAYMENT_CARDSAVE_REDIRECT_ZONE',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_ID',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_PASSWORD',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_PRESHARED_KEY',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_CURRENCY',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_SORT_ORDER',
                             'MODULE_PAYMENT_CARDSAVE_REDIRECT_ORDER_STATUS_ID');
      }

      return $this->_keys;
    }
  }
?>

