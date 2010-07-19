<?php
/*
  $Id: ajax_shopping_cart.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class toC_Json_Ajax_shopping_cart {
  	static $str_len = 18;
  	
    function loadCart() {
      global $osC_ShoppingCart, $osC_Currencies, $toC_Json;
      
      $content = self::_getShoppingCart();
      
      echo $toC_Json->encode($content);
    }
    
    function addProduct() {
      global $osC_ShoppingCart, $toC_Json;
      
      if ( is_numeric($_REQUEST['idProduct']) && osC_Product::checkEntry($_REQUEST['idProduct']) ) {
        $osC_ShoppingCart->add($_REQUEST['idProduct']);
        
        $content = self::_getShoppingCart();
        
        echo $toC_Json->encode($content);
      }    
    }
    
    function removeProduct() {
      global $toC_Json, $osC_ShoppingCart;

      $products_id = isset($_REQUEST['idProduct']) ? $_POST['idProduct'] : null;

      if ( (!empty($products_id)) && osC_Product::checkEntry($products_id) ) {
        $osC_ShoppingCart->remove($products_id);        
        $osC_ShoppingCart->resetShippingMethod();
                
        $response = array('success' => true);
      }else {
        $response = array('success' => false);
      }

      echo $toC_Json->encode($response);
    }
    
    function _getShoppingCart() {
      global $osC_ShoppingCart, $osC_Currencies;

      $datas = array();
      
      //products
      $products = array();
      foreach($osC_ShoppingCart->getProducts() as $products_id => $data) {
        if ($data['type'] != PRODUCT_TYPE_SIMPLE) continue;
        
        $products[] = array('id' => $products_id,
                            'link' => osc_href_link(FILENAME_PRODUCTS, osc_get_product_id($products_id)),
                            'name' => (substr($data['name'], 0, self::$str_len)) . (strlen($data['name']) > self::$str_len ? '..' : ''),
                            'title' => $data['name'],
                            'quantity' => $data['quantity'] . ' x ',
                            'price' => $osC_Currencies->format($data['price']));                
      }
      $datas['products'] = $products;
      
      //order totals
      $order_totals = array();
      foreach ($osC_ShoppingCart->getOrderTotals() as $module) {
        $order_totals[] = array('title' => $module['title'], 'text' => $module['text']);
      }
      
      $datas['orderTotals'] = $order_totals;
      //numberOfItems
      $datas['numberOfItems'] = $osC_ShoppingCart->numberOfItems();
      
      //cart total
      $datas['total'] = $osC_Currencies->format($osC_ShoppingCart->getTotal());
      
      return $datas;
    }
  }
?>