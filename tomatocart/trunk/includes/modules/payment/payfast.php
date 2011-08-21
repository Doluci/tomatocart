<?php
/*
  $Id: payfast.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  Tomatocart is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation. The Payfast Payment Module is the property of Eloras Web Solutions.
  This payment module may not be copied, modified or redistributed without the written permission of Eloras Web Solutions.
  We reserve the right to institute criminal, civil or other proceedings against any pers, group or organisation that violates this agreement.
  On purchase of the Payfast module, you are authorised to use it on one domain name only. Should you require multi licence, please request when purchasing.
*/

  class osC_Payment_payfast extends osC_Payment {
    var $_title,
        $_code = 'payfast',
        $_author_name = 'Eloras Web Solutions',
        $_status = false,
        $_sort_order;

    function osC_Payment_payfast() {
      global $osC_Database, $osC_Language, $osC_ShoppingCart;

      $this->_title = $osC_Language->get('payment_payfast_title');
      $this->_method_title = $osC_Language->get('payment_payfast_method_title');
      $this->_description = $osC_Language->get('payment_payfast_description');
      $this->_status = (defined('MODULE_PAYMENT_PAYFAST_STATUS') && (MODULE_PAYMENT_PAYFAST_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_PAYFAST_SORT_ORDER') ? MODULE_PAYMENT_PAYFAST_SORT_ORDER : null);
      
      if (MODULE_PAYMENT_PAYFAST_TEST_MODE == 'True') {
        $this->form_action_url = 'https://sandbox.payfast.co.za/eng/process';
      } else {
        $this->form_action_url = 'https://payfast.co.za/eng/process';
      }
      
      if ($this->_status === true) {
        $this->order_status = MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID > 0 ? (int)MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID : (int)ORDERS_STATUS_PAID;
        
        if ((int)MODULE_PAYMENT_PAYFAST_ZONE > 0) {
          $check_flag = false;

          $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
          $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_PAYFAST_ZONE);
          $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getBillingAddress('country_id'));
          $Qcheck->execute();

          while ($Qcheck->next()) {
            if ($Qcheck->valueInt('zone_id') < 1) {
              $check_flag = true;
              break;
            } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getBillingAddress('zone_id')) {
              $check_flag = true;
              break;
            }
          }

          if ($check_flag == false) {
            $this->_status = false;
          }
        }
      }
    }
    
    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->_code,
                   'module' => $this->_method_title);
    }
    
    function confirmation() {
      return false;
    }
    
    function process_button() {
      global $osC_ShoppingCart, $osC_Currencies, $osC_Customer;
      
      //convert currency to payfast currency number
      $currency = $osC_Currencies->getCode();
      switch ($currency) {
        case 'GBP':
          $this->TransactionCurrency = '826';
          break;
        case 'USD':
          $this->TransactionCurrency = "840";
          break;
        case "EUR":
          $this->TransactionCurrency = "978";
          break;
        case "AUD":
          $this->TransactionCurrency = "036";
          break;
      }
            
      $this->payfast_web_notes = osc_create_random_string(10, 'digits');
      
      $process_button_string = '';
      $params = array();
      $params['merchant_id'] = MODULE_PAYMENT_PAYFAST_MERCHANT_ID;
      $params['merchant_key'] = MODULE_PAYMENT_PAYFAST_MERCHANT_KEY;
      $params['amount'] = number_format($osC_ShoppingCart->getTotal() * $osC_Currencies->value($currency), $osC_Currencies->getDecimalPlaces($osC_Currencies->getID($currency)));
      
      $products = $osC_ShoppingCart->getProducts();
      $product_name = '';
      foreach($products as $product) {
        $product_name .= $product['name'] . ((count($products) > 1) ? "<br />" : '');
      }
            
      $params['item_name'] = $product_name;
      $params['TransactionCurrency'] = $this->TransactionCurrency;
      $params['TransactionAmount'] = number_format($osC_ShoppingCart->getTotal() * $osC_Currencies->value($currency), $osC_Currencies->getDecimalPlaces($osC_Currencies->getID($currency)));
      $params['amount'] = number_format($osC_ShoppingCart->getTotal() * $osC_Currencies->value($currency), $osC_Currencies->getDecimalPlaces($osC_Currencies->getID($currency)));
      $params['CustomerEmail'] = $osC_Customer->getEmailAddress();
      $params['redirectorsuccess'] = HTTP_SERVER . DIR_WS_HTTP_CATALOG . FILENAME_CHECKOUT . '?process&Note=' . $this->payfast_notes . '&';
      $params['redirectorfailed'] = osc_href_link(FILENAME_CHECKOUT, 'process&fail=true', 'SSL', null, null, true);
      $params['PayPageType'] = 4;
      $params['Notes'] = $this->payfast_notes;
      
      foreach($params as $key => $value) {
        $process_button_string .= osc_draw_hidden_field($key, $value);
      }
      
      return $process_button_string;
    }
    
    function process() {
      global $messageStack;
      
      if (isset($_GET['TransID']) && isset($_GET['Note'])) {
        $ordID = trim($_GET['Note']);
        $thesuccess = trim($_GET['Status']);
        $theauthcode = trim($_GET['TransID']);
        $md5check = trim($_GET['Crypt']);
        $theamount = trim($_GET['Amount']);
        
        $md5hash = md5($thesuccess . $theauthcode . $theamount . MODULE_PAYMENT_PAYFAST_SECRET_KEY);
        
        if ($ordID != '' && $thesuccess == 'Success' && $theauthcode != '' && $md5check == $md5hash) {
          // validated - continue
        } else {
          // problem with order, ecom system says this failed or doesnt recognize it
          // so could be a spoof attempt. Dont process the order.
          //osc_redirect(osc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
          $the_error = 'There is a problem processing your order.';
          
          if (MODULE_PAYMENT_PAYFAST_SECRET_KEY == '') { $the_error .= ': Secret Key Not set in the Payfast Module :'; } ;
          if ($md5check != $md5hash) { $the_error .= ': Secret Keys Do Not Match :'; } ;
          if ($ordID == '') { $the_error .= ': Order ID not set :'; } ;
          if ($thesuccess != 'Success') { $the_error .= ': Status Code incorrect :'; } ;
          if ($theauthcode == '') { $the_error .= ': No auth code specififed:'; } ;
          
          $messageStack->add_session('checkout', $the_error, 'error');
          
          osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout', 'SSL', true, false));
        } 
      }else if (isset($_GET['fail']) && ($_GET['fail'] == true)) {
         $messageStack->add_session('checkout', "Your card has been declined", 'error');
         osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout', 'SSL'));
      } else {
        $myVars = array (
            'CustomerID' => MODULE_PAYMENT_PAYFAST_CUSTOMER_ID ,
            'Notes' => $this->payfast_notes
        );
        // to payfast api to check transaction 
        $path = "/paypage/confirm.asp";
        
        // PORT
        $port = 443;
    
        // BUILD THE POST STRING
        foreach($myVars AS $key => $val){
                $poststring .= urlencode($key) . "=" . urlencode($val) . "&";
        }
    
        // STRIP OFF THE TRAILING AMPHERSAND
        $poststring = substr($poststring, 0, -1);
    
        if ( ( MODULE_PAYMENT_PAYFAST_TEST_MODE == 'True') ) {
          $host = "https://sandbox.payfast.co.za/eng/process";
        } else {
          $host = "https://sandbox.payfast.co.za/eng/process";
        }
        
        // try using fsick for ssl connection, if this doesnt work
        // use curl
        $fp = @fsockopen("ssl://$host", $port, $errno, $errstr, $timeout = 30) ;
        if ($fp){
          // SEND THE SERVER REQUEST
          fputs($fp, "POST $path HTTP/1.1\r\n");
          fputs($fp, "Host: $host\r\n");
          fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
          fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
          fputs($fp, "Connection: close\n\n");
          fputs($fp, $poststring . "\n\n");
  
          // LOOP THROUGH THE RESPONSE FROM THE SERVER
          while(!feof($fp)) {
            $response .= @fgets($fp, 4096);
          }
          // CLOSE FP
          fclose($fp);
        } else {
          // ssl not installed so try using curl
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "https://" . $host .$path);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
          curl_setopt($ch, CURLOPT_POST, TRUE);
    
          $response = curl_exec($ch);
        }
        
        if (strpos( $response, "SUCCESS")) {
          // indicates a success transaction, just need to validate amount
          // everything okay, carry on with processing order
        } else {
          // problem with order, ecom system says this failed or doesnt recognize it
          // so could be a spoof attempt. Dont process the order
          $error = 'problem with order, ecom system says this failed or doesnt recognize it';
          $messageStack->add_session('checkout', $error, 'error');
          
          osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout', 'SSL'));
        } 
      }
      
      $this->_order_id = osC_Order::insert();
      osC_Order::process($this->_order_id, $this->order_status);
    }
  }
?>
