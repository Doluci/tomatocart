<?php
/*
  $Id: google_base.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  require('includes/classes/google_base.php');
  
  class toC_Json_Google_Base {
    function getAvailableProducts() {
      global $toC_Json, $osC_Currencies;
      
      require_once('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();
      
      $start = empty($_REQUEST['start']) ? 0 : $_REQUEST['start']; 
      $limit = empty($_REQUEST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_REQUEST['limit'];
      
      $records = osC_GoogleBase_Admin::getAvailableProducts($start, $limit);
      
      $response = array(EXT_JSON_READER_TOTAL => $records['total'],
                        EXT_JSON_READER_ROOT => $records['available_products']);
      
      echo $toC_Json->encode($response);
    }
    
    function getManageItems() {
      global $toC_Json;
      
      $start = empty($_REQUEST['start']) ? 0 : $_REQUEST['start']; 
      $limit = empty($_REQUEST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_REQUEST['limit']; 
      
      $manage_items = osC_GoogleBase_Admin::getManageItems($start, $limit);
      
      $response = array(EXT_JSON_READER_TOTAL => $manage_items['total'],
                        EXT_JSON_READER_ROOT => $manage_items['items']);
      
      echo $toC_Json->encode($response);
    }
    
    function uploadProducts() {
      global $toC_Json, $osC_Language, $osC_Currencies;
      
      require_once('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();
      
      if ( !isset($_SESSION['gContentToken']) && empty($_SESSION['gContentToken']) ) {
        osC_GoogleBase_Admin::clientLoginContentApi();
      }
      
      $batch = explode(',', $_REQUEST['batch']);
      
      $error = false;
      foreach($batch as $id) {
        if (!osC_GoogleBase_Admin::uploadSingleProduct($id)) {
          $error = true;
          break;
        }
      }
      
      if ($error === false) {      
        $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
      } else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
      }
      
      echo $toC_Json->encode($response); 
    
    }
    
    function uploadSingleProduct() {
      global $osC_Database, $toC_Json, $osC_Language, $osC_Currencies;
      
      require_once('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();
      
      $products_id = $_POST['products_id'];
      
      if ( !isset($_SESSION['gContentToken']) && empty($_SESSION['gContentToken']) ) {
        osC_GoogleBase_Admin::clientLoginContentApi();
      }
      
      $upload_state = osC_GoogleBase_Admin::uploadSingleProduct($products_id);
      
      if ($upload_state == true) {
        $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
      }
      
      echo $toC_Json->encode($response);
    }
    
    function deleteProduct() {
      global $toC_Json, $osC_Language;
      
      $item_id = $_POST['itemId'];
      
      if (osC_GoogleBase_Admin::deleteProduct($item_id)) {
        $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
      }else {
        $response = array('success' => true, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
      }
      
      echo $toC_Json->encode($response);
    }
    
    function deleteProducts() {
      global $toC_Json, $osC_Language;
      
      $batch = explode(',', $_REQUEST['batch']);
      
      $error = false;
      foreach($batch as $id) {
        if (!osC_GoogleBase_Admin::deleteProduct($id)) {
          $error = true;
          break;
        }
      }
      
      if ($error === false) {      
        $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
      } else {
        $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
      }
      
      echo $toC_Json->encode($response); 
    }
    
    function synchronous() {
      global $toC_Json, $osC_Language;
      
      if ( !isset($_SESSION['gSearchToken']) && empty($_SESSION['gSearchToken']) ) {
        osC_GoogleBase_Admin::clientLoginSearch();
      }

      $response = osC_GoogleBase_Admin::synchronous();
      
      print_r($response); 
    
    }
  }
?>