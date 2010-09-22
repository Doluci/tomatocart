<?php
/*
  $Id: shopping_cart.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Boxes_shopping_cart extends osC_Modules {
    var $_title,
        $_code = 'shopping_cart',
        $_author_name = 'TomatoCart',
        $_author_www = 'http://www.tomatocart.com',
        $_group = 'boxes';

    function osC_Boxes_shopping_cart() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_shopping_cart_heading');
    }

    function initialize() {
      global $osC_Language, $osC_Template, $osC_Session, $osC_Currencies;

      $this->_title_link = osc_href_link(FILENAME_CHECKOUT, null, 'SSL');
      
      $content = '<div id="ajaxCartContent">' .
                  '<div id="ajaxCartContentShort" class="collapsed">' .
                    '<span class="cartTotal"></span>' .  
                    '<span class="quantity"></span> ' . $osC_Language->get('text_items') .
                  '</div>' .
                  '<div id="ajaxCartContentLong" class="expanded">' .
                    '<ul class="products collapsed" id="ajaxCartContentProducts"><li></li></ul>' .
                    '<p id="ajaxCartContentNoProducts" class="collapsed">' . $osC_Language->get('No products') . '</p>' .
                    '<div id="ajaxCartButtons">' .
                      osc_link_object(osc_href_link(FILENAME_CHECKOUT), osc_draw_image_button('button_ajax_cart.png'), 'style="margin-right:30px;"') .
                      osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'payment'), osc_draw_image_button('button_ajax_cart_checkout.png')) .
                      '<div style="visibility:hidden">' . 
                        '<span>clear-bug-div</span>' .
                      '</div>' .
                    '</div>' .
                  '</div>' .
                 '</div>';
                      
      $css = '<style type="text/css">' . "\n" .
              '#ajaxCartContent {overflow: hidden;}' .
              '.boxTitle #ajaxCartCollapse, .boxTitle #ajaxCartExpand {cursor:pointer;position:relative;top:3px;}' .
              '.hidden {display: none;}' .
              '.expanded {display: block;}' .
              '.collapsed {display: none;}' .
              '.strike {text-decoration:line-through;}' .
              '#ajaxCartContentShort span{ padding: 0 2px;}' .
              '#ajaxCartButtons {margin-top:10px;}' .
              '#ajaxCartButtons a {padding: 1px;text-align: center;text-decoration: none;}' .
              '#ajaxCartOrderTotals span.orderTotalText {float: right}' .
              '#ajaxCartContentLong ul.products {text-align: left;}' . 
              '#ajaxCartContentLong ul li {padding: 6px 0;font-size: 9px;position: relative;line-height:16px;}' .
              '#ajaxCartContentLong ul.products span.price {display:block;position:absolute;right:15px;top:8px;}' .
              '#ajaxCartContentLong ul.products .removeProduct {cursor: pointer;display: block;width: 11px;height: 13px;position: absolute;right: 0;top: 8px;background: url(includes/languages/' . $osC_Language->getCode() . '/images/buttons/button_ajax_cart_delete.gif) no-repeat left top;}' .
              '#ajaxCartContentLong #ajax_cart_prices {padding: 5px 0;border-top : 1px dashed #777F7D;}' .
              '#ajaxCartOrderTotals {padding:5px 0;border-top: 1px dashed #CCCCCC;}' .
              '#ajaxCartContentLong #ajaxCartOrderTotals li {padding: 2px;font-size: 11px}' .
              '#ajaxCartContentLong p{color: #616060;font-size: 10px;margin: 0}' .
              '#ajaxCartContentLong p.variants, #ajaxCartContentLong p.customizations { padding: 2px;margin: 0 0 0 5px; }' .
              '#ajaxCartContentShort span.cartTotal {float:right; font-weight: bold}' .
              '#ajaxCartContentProducts dd span {display:block;padding-left:32px;}' .  
             '</style>' . "\n\n";                                 
                      
      $js = $osC_Template->ouputJavascriptFile('includes/javascript/ajax_shopping_cart.js') . "\n\n";
      
      $js .= '<script type="text/javascript">
                window.addEvent("domready",function() {
                  var ajaxCart = new AjaxShoppingCart({
                    sessionId : "' . $osC_Session->getID() . '",
                    error_sender_name_empty: "' . $osC_Language->get('error_sender_name_empty') . '",
                    error_sender_email_empty: "' . $osC_Language->get('error_sender_email_empty') . '",
                    error_recipient_name_empty: "' . $osC_Language->get('error_recipient_name_empty') . '",
                    error_recipient_email_empty: "' . $osC_Language->get('error_recipient_email_empty') . '",
                    error_message_empty: "' . $osC_Language->get('error_message_empty') . '",
                    error_message_open_gift_certificate_amount: "' . $osC_Language->get('error_message_open_gift_certificate_amount') . '"
                  });
                });
              </script>';
      
      $this->_content = $css . "\n" . $content . "\n" . $js;
    }
  }
?>
