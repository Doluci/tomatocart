<?php
/*
  $Id: specials.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/classes/specials.php');
  require('includes/classes/manufacturers.php');
  require('includes/classes/category_tree.php');
  require('includes/classes/tax.php');
  
  class toC_Json_Specials {
      
  function listSpecials() {
    global $toC_Json, $osC_Language, $osC_Database;
    
    require_once('includes/classes/currencies.php');
    $osC_Currencies = new osC_Currencies();
    $osC_CategoryTree = new osC_CategoryTree_Admin();

    $start = empty($_REQUEST['start']) ? 0 : $_REQUEST['start']; 
    $limit = empty($_REQUEST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_REQUEST['limit']; 
      
    $current_category_id = end(explode( '_' ,(empty($_REQUEST['category_id']) ? 0 : $_REQUEST['category_id'])));
    
    if ( $current_category_id > 0 ) {
      $osC_CategoryTree->setBreadcrumbUsage(false);
  
      $in_categories = array($current_category_id);
  
      foreach($osC_CategoryTree->getTree($current_category_id) as $category) {
        $in_categories[] = $category['id'];
      }
      
      $Qspecials = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from :table_products p, :table_specials s, :table_products_description pd, :table_products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = s.products_id and p.products_id = p2c.products_id and p2c.categories_id in (:categories_id)');
      $Qspecials->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
      $Qspecials->bindRaw(':categories_id', implode(',', $in_categories));
      
    } else {
      $Qspecials = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from :table_products p, :table_specials s, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = s.products_id');
    }
  
    if ( !empty($_REQUEST['search']) ) {
      $Qspecials->appendQuery('and pd.products_name like :products_name');
      $Qspecials->bindValue(':products_name', '%' . $_REQUEST['search'] . '%');
    }
  
    if ( !empty($_REQUEST['manufacturers_id']) ) {
      $Qspecials->appendQuery('and p.manufacturers_id = :manufacturers_id');
      $Qspecials->bindValue(':manufacturers_id', $_REQUEST['manufacturers_id']);
    }
  
    $Qspecials->appendQuery(' order by pd.products_name');
    $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
    $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
    $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecials->bindInt(':language_id', $osC_Language->getID());
    $Qspecials->setExtBatchLimit($start, $limit);
    $Qspecials->execute();
      
    $records = array();
    while ($Qspecials->next()) {
      $records[] = array('specials_id' => $Qspecials->value('specials_id'),
                         'products_id' => $Qspecials->value('products_id'),
                         'products_name' => $Qspecials->value('products_name'),
                         'products_price' => $Qspecials->value('products_price'),
                         'specials_new_products_price' => '<span class="oldPrice">' . $osC_Currencies->format($Qspecials->value('products_price')) . '</span> <span class="specialPrice">' . $osC_Currencies->format($Qspecials->value('specials_new_products_price')) . '</span>');         
    }
      
    $response = array(EXT_JSON_READER_TOTAL => $Qspecials->getBatchSize(),
                      EXT_JSON_READER_ROOT => $records); 
                        
    echo $toC_Json->encode($response);
  }
  
  function saveSpecials() {
    global $toC_Json, $osC_Language;
      
    $data = array('products_id' => $_REQUEST['products_id'], 
                  'specials_price' => $_REQUEST['specials_new_products_price'],
                  'start_date' => $_REQUEST['start_date'], 
                  'expires_date' => $_REQUEST['expires_date'], 
                  'specials_date_added' => $_REQUEST['specials_date_added'], 
                  'status' => (isset($_REQUEST['status']) ? 1 : null) );
    
    if ( osC_Specials_Admin::save((isset($_REQUEST['specials_id']) && is_numeric($_REQUEST['specials_id']) ? $_REQUEST['specials_id'] : null ), $data) ) {
      $response = array('success' => true ,'feedback' => $osC_Language->get('ms_success_action_performed'));
    } else {
      $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));    
    }
    
    echo $toC_Json->encode($response);
  }
  
  function saveBatchSpecials() {
    global $toC_Json, $osC_Language;
    
    if( isset($_REQUEST['products']) && !empty($_REQUEST['products'])){
      $products = $toC_Json->decode($_REQUEST['products']);
      
      foreach($products as $product) {
        $data = array('products_id' => $product->products_id, 
                      'specials_price' => $product->special_price,
                      'start_date' => date('Y-m-d' , strtotime($product->start_date)), 
                      'expires_date' => date('Y-m-d' , strtotime($product->expires_date)), 
                      'specials_date_added' => '', 
                      'status' => $product->status == true ? 1 : 0);

        if ( osC_Specials_Admin::save(null, $data) ) {
          $response = array('success' => true ,'feedback' => $osC_Language->get('ms_success_action_performed'));
        } else {
          $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
          break;    
        } 
      }
    } else {
      $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));
    }

    echo $toC_Json->encode($response);
  }
  
  function deleteSpecial() {
    global $toC_Json, $osC_Language;
    
    if ( isset($_REQUEST['specials_id']) && is_numeric($_REQUEST['specials_id']) && osC_Specials_Admin::delete($_REQUEST['specials_id']) ) {
      $response = array('success' => true ,'feedback' => $osC_Language->get('ms_success_action_performed'));
    } else {
      $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));    
    }
    
    echo $toC_Json->encode($response);
  }

  function deleteSpecials() {
    global $toC_Json, $osC_Language;
    
    $error = false;
    $batch = explode(',', $_REQUEST['batch']);
    foreach ($batch as $id) {
      if (!osC_Specials_Admin::delete($id)) {
        $error = true;
        break;
      }
    }
    
    if (!$error) {
      $response = array('success' => true, 'feedback' => $osC_Language->get('ms_success_action_performed'));
    } else {
      $response = array('success' => false, 'feedback' => $osC_Language->get('ms_error_action_not_performed'));               
    }
    
    echo $toC_Json->encode($response);
  }
    
  function listManufacturers() {
    global $toC_Json, $osC_Language;
    
    $Qentries = osC_Manufacturers_Admin::getManufacturersData();
    
    $records = array(array('manufacturers_id' => '',
                           'manufacturers_name' => $osC_Language->get('top_manufacturers')));
    
    while ($Qentries->next()) {       
      $records[] = array('manufacturers_id' => $Qentries->value('manufacturers_id'),
                         'manufacturers_name' => $Qentries->value('manufacturers_name'));
    }
    
    $response = array(EXT_JSON_READER_ROOT => $records); 
                      
    echo $toC_Json->encode($response);
  }
    
  function listCategories() {
    global $toC_Json, $osC_Language;
    
    $osC_CategoryTree = new osC_CategoryTree_Admin();
    
    $records = array(array('id' => '',
                           'text' => $osC_Language->get('top_category')));
    
    foreach ($osC_CategoryTree->getTree() as $value) {
      $records[] = array('id' => $value['id'],
                         'text' => $value['title']);
    }
     
    $response = array(EXT_JSON_READER_ROOT => $records); 
                
    echo $toC_Json->encode($response);
  }
    
  function listProducts() {
    global $toC_Json, $osC_Database, $osC_Language;
    
    $start = empty($_REQUEST['start']) ? 0 : $_REQUEST['start']; 
    $limit = empty($_REQUEST['limit']) ? MAX_DISPLAY_SEARCH_RESULTS : $_REQUEST['limit']; 
    
    $osC_Tax = new osC_Tax_Admin();
    
    $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
    $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
    $Qtc->execute();
  
    $tax_class_array = array();
    while ($Qtc->next()) {
      $tax_class_array[$Qtc->valueInt('tax_class_id')] = $osC_Tax->getTaxRate($Qtc->valueInt('tax_class_id'));
    }    
    
    $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_tax_class_id from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_type <> :products_type');
    $Qproducts->appendQuery(' order by pd.products_name');
    $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qproducts->bindInt(':language_id', $osC_Language->getID());
    $Qproducts->bindInt(':products_type', PRODUCT_TYPE_GIFT_CERTIFICATE);
    $Qproducts->setExtBatchLimit($start, $limit);
    $Qproducts->execute();
    
    $records = array();
    while ($Qproducts->next()) {
      $rate = ($Qproducts->valueInt('products_tax_class_id') == 0) ? 0 : $tax_class_array[$Qproducts->valueInt('products_tax_class_id')];
      
      $records[] = array('products_id' => $Qproducts->value('products_id'),
                         'products_name' => $Qproducts->value('products_name'),
                         'rate' => $rate);
    }
    
    $response = array(EXT_JSON_READER_TOTAL => $Qproducts->getBatchSize(),
                      EXT_JSON_READER_ROOT => $records); 
                      
    echo $toC_Json->encode($response);
  }
    
  function loadSpecials() {
    global $toC_Json;
    
    $data = osC_Specials_Admin::getData($_REQUEST['specials_id']);
    
    $data['start_date'] = osC_DateTime::getDate($data['start_date']);
    $data['expires_date'] = osC_DateTime::getDate($data['expires_date']);
    
    $response = array('success' => true, 'data' => $data); 

    echo $toC_Json->encode($response);
  }
  
  function loadProducts() {
    global $toC_Json, $osC_Database, $osC_Language;

    $osC_CategoryTree = new osC_CategoryTree_Admin();
    
    $current_category_id = isset($_REQUEST['categories_id']) && !empty($_REQUEST['categories_id']) ? $_REQUEST['categories_id'] : 0;

    if ( $current_category_id > 0 ) {
      $osC_CategoryTree->setBreadcrumbUsage(false);

      $in_categories = array($current_category_id);

      foreach($osC_CategoryTree->getTree($current_category_id) as $category) {
        $in_categories[] = $category['id'];
      }

      $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_price from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (:categories_id)');
      $Qproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
      $Qproducts->bindRaw(':categories_id', implode(',', $in_categories));
    } else {
      $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_price from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id');
    }

    if ( !empty($_REQUEST['cManufacturer']) ) {
      $Qproducts->appendQuery('and p.manufacturers_id = :manufacturers_id');
      $Qproducts->bindValue(':manufacturers_id', $_REQUEST['cManufacturer']);
    }

    if ( !empty($_REQUEST['products_sku']) ) {
      $Qproducts->appendQuery('and p.products_sku like :products_sku');
      $Qproducts->bindValue(':products_sku', '%' . $_REQUEST['products_sku'] . '%');
    }
    
    if ( !empty($_REQUEST['products_name']) ) {
      $Qproducts->appendQuery('and pd.products_name like :products_name');
      $Qproducts->bindValue(':products_name', '%' . $_REQUEST['products_name'] . '%');
    }
  
    $Qproducts->appendQuery(' and p.products_type <> :products_type');
    $Qproducts->appendQuery('order by pd.products_name');
    $Qproducts->bindTable(':table_specials', TABLE_SPECIALS);
    $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qproducts->bindInt(':language_id', $osC_Language->getID());
    $Qproducts->bindInt(':products_type', PRODUCT_TYPE_GIFT_CERTIFICATE);
    $Qproducts->execute();

    $products = array();
    while($Qproducts->next()) {
      $products[] = array('products_id' => $Qproducts->value('products_id'),
                          'products_name' => $Qproducts->value('products_name'),
                          'products_price' => $Qproducts->value('products_price'),
                          'special_price' => 0);
    }

    $response = array(EXT_JSON_READER_TOTAL => $Qproducts->getBatchSize(),
                      EXT_JSON_READER_ROOT => $products); 
  
    echo $toC_Json->encode($response);
  }
  
}
?>
  