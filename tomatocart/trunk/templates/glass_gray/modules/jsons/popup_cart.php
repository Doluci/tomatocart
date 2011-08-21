<?php
/*
  $Id: popup_cart.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class toC_Json_Popup_Cart {
  
    function getCartContents() {
      global  $toC_Json;
      
      $cart_contents = self::_getShoppingCart();
      $response = array('success' => true, 'content' => $cart_contents);
      
      echo $toC_Json->encode($response);
    }

    function addProduct() {
      global $osC_ShoppingCart, $toC_Json, $osC_Language, $toC_Customization_Fields;
      
      $osC_Language->load('products');
      
      if ( is_numeric($_REQUEST['pID']) && osC_Product::checkEntry($_REQUEST['pID']) ) {
        $osC_Product = new osC_Product($_REQUEST['pID']);
        
        //gift certificate check
        if ($osC_Product->isGiftCertificate() && !isset($_POST['senders_name'])) {
          $response = array('success' => false, 
                            'feedback' => $osC_Language->get('error_gift_certificate_data_missing'));
        }
        //customization fields check
         else if ( $osC_Product->hasRequiredCustomizationFields() && !$toC_Customization_Fields->exists($osC_Product->getID()) ) {
          $response = array('success' => false, 
                            'feedback' => $osC_Language->get('error_customization_fields_missing'));
        } else {
          $variants = null;
          if (isset($_REQUEST['variants']) && !empty($_REQUEST['variants'])) {
            $variants = osc_parse_variants_string($_REQUEST['variants']);
          }      
                  
          $gift_certificate_data = null;
          if($osC_Product->isGiftCertificate() && isset($_POST['senders_name']) && isset($_POST['recipients_name']) && isset($_POST['message'])) {
            if ($osC_Product->isEmailGiftCertificate()) {
              $gift_certificate_data = array('senders_name' => $_POST['senders_name'],
                                             'senders_email' => $_POST['senders_email'],
                                             'recipients_name' => $_POST['recipients_name'],
                                             'recipients_email' => $_POST['recipients_email'],
                                             'message' => $_POST['message']);
            } else {
              $gift_certificate_data = array('senders_name' => $_POST['senders_name'],
                                             'recipients_name' => $_POST['recipients_name'],
                                             'message' => $_POST['message']);
            }
            
            if ($osC_Product->isOpenAmountGiftCertificate()) {
              $gift_certificate_data['price'] = $_POST['gift_certificate_amount']; 
            }
            
            $gift_certificate_data['type'] = $osC_Product->getGiftCertificateType();
          }
          
          $osC_ShoppingCart->add($_REQUEST['pID'], $variants, $_REQUEST['pQty'], $gift_certificate_data);
          
          $content = self::_getShoppingCart();
          
          $response = array('success' => true, 'content' => $content, 'cart_items' => $osC_ShoppingCart->numberOfItems());
        }
      } else {
        $response = array('success' => false);
      }
      
      echo $toC_Json->encode($response);
    }
    
    function _getShoppingCart() {
      global $osC_Language, $osC_ShoppingCart, $osC_Currencies, $toC_Json, $osC_Image;
      
      $content =  '<h6>' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), $osC_Language->get('box_shopping_cart_heading')) . '</h6>' . 
                    '<div class="content">';
                      
      
      if ($osC_ShoppingCart->hasContents()) {
        $content .= '<table border="0" width="100%" cellspacing="4" cellpadding="2">';
        
        foreach ($osC_ShoppingCart->getProducts() as $product) {
          $content .= '  <tr>' .
                      '    <td valign="top" align="center" width="60">' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), $osC_Image->show($product['image'], $product['name'], '', 'mini')) . '</td>' .
                      '    <td valign="top">' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), $product['name']). '<br/>Quantity:' . $product['quantity'] . '<br/><span class="price">' . $osC_Currencies->format($product['price']) . '</span><br/><span>' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $product['id']), 'More Info') . '</span></td>' .
                      '  </tr>';
        }

        $content .= '</table>';
      } else {
        $content .= $osC_Language->get('box_shopping_cart_empty');
      }
      
      $content .= '<p class="subtotal">' . $osC_Language->get('box_shopping_cart_subtotal') . '&nbsp;&nbsp;' . $osC_Currencies->format($osC_ShoppingCart->getSubTotal()) . '</p>
                  </div>';
      
      
      return $content;
    }
  }
  