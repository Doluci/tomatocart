<?php
/*
  $Id: amazon.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Payment_cardsave_redirect extends osC_Payment {
    
    var $_title,
        $_code = 'cardsave_redirect',
        $_status = false,
        $_sort_order,
        $_order_id;
          
    static $public_key_cache = array();    
  
    function osC_Payment_cardsave_redirect() {
      global $osC_Database, $osC_Language, $osC_ShoppingCart;
  
      $this->_title = $osC_Language->get('payment_cardsave_redirect_title');
      $this->_method_title = $osC_Language->get('payment_cardsave_redirect_method_title');
      $this->_status = (MODULE_PAYMENT_CARDSAVE_REDIRECT_STATUS == '1') ? true : false;
      $this->_sort_order = MODULE_PAYMENT_CARDSAVE_REDIRECT_SORT_ORDER;
  
			$this->form_action_url = 'https://mms.cardsaveonlinepayments.com/Pages/PublicPages/PaymentForm.aspx';     
        
      if ($this->_status === true) {
        if ((int)MODULE_PAYMENT_CARDSAVE_REDIRECT_ORDER_STATUS_ID > 0) {
          $this->order_status = MODULE_PAYMENT_CARDSAVE_REDIRECT_ORDER_STATUS_ID;
        }
  
        if ((int)MODULE_PAYMENT_CARDSAVE_REDIRECT_ZONE > 0) {
          $check_flag = false;
  
          $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
          $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_CARDSAVE_REDIRECT_ZONE);
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
  
    function selection() {
      return array('id' => $this->_code,
                  'module' => $this->_method_title);
    }
      
    function confirmation() {
      $this->_order_id = osC_Order::insert(ORDERS_STATUS_PREPARING);
    }
    
    
  
    function process_button() {
      global $osC_Customer, $osC_Currencies, $osC_ShoppingCart, $osC_Language, $osC_Database; 
      
      switch (MODULE_PAYMENT_CARDSAVE_REDIRECT_CURRENCY) {
        case 'GBP':
          $cs_currency = 826;
          break;
        case 'EUR':
          $cs_currency = 978;
          break;
				case 'USD':
          $cs_currency = 840;
          break;
      }

			//Get country ISO Code
			require_once('ext/cardsave_redirect/Common.php');
			require_once('ext/cardsave_redirect/ISOCountries.php');
							
			$CountryCode = 0;
			$Countryid = $osC_ShoppingCart->getBillingAddress('country_id');
			
      $Qcountry = $osC_Database->query('select countries_iso_code_3 from :table_countries where  countries_id = :country_id');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':country_id', $Countryid);
      $Qcountry->execute();	
			
      if($Qcountry->affectedRows() > 0) {
				$tep_country_code = $Qcountry->value('countries_iso_code_3');
      }
			
			for ($country_i = 0; $country_i < $iclISOCountryList->getCount() - 1; $country_i++) {
				if ($iclISOCountryList->getAt($country_i)->getCountryNameShort() == $tep_country_code) {
					$CountryCode = $iclISOCountryList->getAt($country_i)->getISOCode();
					break;
				}
			}
			
	
			//Get server date/time
			$gatewaydatetime = date('Y-m-d H:i:s O');
	
			$OrderAmount = $osC_ShoppingCart->getTotal() * 100;
	
			$OrderDescription = STORE_NAME . " " . date('Ymdhis');
	
			$CustomerName = $osC_ShoppingCart->getBillingAddress('firstname') . ' ' . $osC_ShoppingCart->getBillingAddress('lastname');
			$Address1 = $osC_ShoppingCart->getBillingAddress('street_address');
			$Address2 = $osC_ShoppingCart->getBillingAddress('suburb');
			$Address3 = "";
			$Address4 = "";
			$City = $osC_ShoppingCart->getBillingAddress('city');
			$State = $osC_ShoppingCart->getBillingAddress('state');
			$PostCode = $osC_ShoppingCart->getBillingAddress('postcode');
	
//			$CallbackURL = tep_href_link('includes/modules/payment/cardsave_redirect/callback.php', '', 'NONSSL', false);
			$CallbackURL = HTTP_SERVER. '/cardsave_callback.php';
//			$ServerResultURL = osc_href_link(FILENAME_CHECKOUT, 'callback', 'SSL', null, null, true);
			$ServerResultURL = HTTP_SERVER. '/cardsave_server_result.php';
	
			$CV2Mandatory = "TRUE";
			$Address1Mandatory = "TRUE";
			$CityMandatory = "TRUE";
			$PostCodeMandatory = "TRUE";
			$StateMandatory = "TRUE";
			$CountryMandatory = "TRUE";
	  
			// Calculate the digest to send
			$digest_string = "PreSharedKey=" . MODULE_PAYMENT_CARDSAVE_REDIRECT_PRESHARED_KEY;
			$digest_string = $digest_string . '&MerchantID=' . MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_ID;
			$digest_string = $digest_string . '&Password=' . MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_PASSWORD;
			$digest_string = $digest_string . '&Amount=' . $OrderAmount;
			$digest_string = $digest_string . '&CurrencyCode=' . $cs_currency;
			$digest_string = $digest_string . '&OrderID=' . $this->_order_id;
			$digest_string = $digest_string . '&TransactionType=' . "SALE";
			$digest_string = $digest_string . '&TransactionDateTime=' . $gatewaydatetime;
			$digest_string = $digest_string . '&CallbackURL=' . $CallbackURL;
			$digest_string = $digest_string . '&OrderDescription=' . $OrderDescription;
			$digest_string = $digest_string . '&CustomerName=' . $CustomerName;
			$digest_string = $digest_string . '&Address1=' . $Address1;
			$digest_string = $digest_string . '&Address2=' . $Address2;
			$digest_string = $digest_string . '&Address3=' . $Address3;
			$digest_string = $digest_string . '&Address4=' . $Address4;
			$digest_string = $digest_string . '&City=' . $City;
			$digest_string = $digest_string . '&State=' . $State;
			$digest_string = $digest_string . '&PostCode=' . $PostCode;
			$digest_string = $digest_string . '&CountryCode=' . $CountryCode;
			$digest_string = $digest_string . "&CV2Mandatory=" . $CV2Mandatory;
			$digest_string = $digest_string . "&Address1Mandatory=" . $Address1Mandatory;
			$digest_string = $digest_string . "&CityMandatory=" . $CityMandatory;
			$digest_string = $digest_string . "&PostCodeMandatory=" . $PostCodeMandatory;
			$digest_string = $digest_string . "&StateMandatory=" . $StateMandatory;
			$digest_string = $digest_string . "&CountryMandatory=" . $CountryMandatory;
			$digest_string = $digest_string . "&ResultDeliveryMethod=" . 'SERVER';
			$digest_string = $digest_string . "&ServerResultURL=" . $ServerResultURL;
			$digest_string = $digest_string . "&PaymentFormDisplaysResult=" . 'FALSE';
			$digest_string = $digest_string . "&ServerResultURLCookieVariables=" . '';
			$digest_string = $digest_string . "&ServerResultURLFormVariables=" . '';
			$digest_string = $digest_string . "&ServerResultURLQueryStringVariables=" . '';
	
			$digest = sha1($digest_string);
	
		// Incase this gets 'fixed' at the SECPay end do a search and replace on the trans_id too
			$params = array(
				'HashDigest' => $digest,
				'MerchantID' => MODULE_PAYMENT_CARDSAVE_REDIRECT_MERCHANT_ID,
				'Amount' =>$OrderAmount,
				'CurrencyCode' =>  $cs_currency,
				'OrderID' => $this->_order_id,
				'TransactionType' => "SALE",
				'TransactionDateTime' => $gatewaydatetime,
				'CallbackURL' =>  $CallbackURL,
				'OrderDescription' =>  $OrderDescription,
				'CustomerName' =>  $CustomerName,
				'Address1' => $Address1,
				'Address2' => $Address2,
				'Address3' => $Address3,
				'Address4' => $Address4,
				'City' => $City,
				'State' => $State,
				'PostCode' => $PostCode,
				'CountryCode' => $CountryCode,
				'CV2Mandatory' => $CV2Mandatory,
				'Address1Mandatory' => $Address1Mandatory,
				'CityMandatory' => $CityMandatory,
				'PostCodeMandatory' => $PostCodeMandatory,
				'StateMandatory' => $StateMandatory,
				'CountryMandatory' => $CountryMandatory,
				'ResultDeliveryMethod' => "SERVER",
				'ServerResultURL' => $ServerResultURL,
				'PaymentFormDisplaysResult' => "FALSE",
				'ServerResultURLCookieVariables' => "",
				'ServerResultURLFormVariables' => "",
				'ServerResultURLQueryStringVariables' => "");
		
      $process_button_string = '';
      foreach ($params as $key => $value) {
        $key = trim($key);
        $value = trim($value);
        $process_button_string .= osc_draw_hidden_field($key, $value);
        $process_button_string .= "\n";
      }

      return $process_button_string;
    }
    

  }
?>
