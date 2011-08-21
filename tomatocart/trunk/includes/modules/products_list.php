<?php
/*
  $Id: products_list.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="productListing">
  <tr>
    <?php 
      foreach($list_headings as $list_head) {
        echo '<td align="' . $list_head['lc_align'] . '" class="productListing-heading">&nbsp;' . $list_head['lc_text'] . '&nbsp;</td>' . "\n";
      }
    ?>
  </tr>
    
  <?php
     $rows = 0;
   
     while ($Qlisting->next()) {
       $osC_Product = new osC_Product($Qlisting->value('products_id'));
       $rows++;
       
       echo '    <tr class="' . ((($rows/2) == floor($rows/2)) ? 'productListing-even' : 'productListing-odd') . '">' . "\n";
   
       for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
         $lc_align = '';
         $lc_class = '';
   
         switch ($column_list[$col]) {
           case 'PRODUCT_LIST_SKU':
             $lc_text = '&nbsp;' . $Qlisting->value('products_sku') . '&nbsp;';
             
             break;
           case 'PRODUCT_LIST_NAME':
             $lc_class = 'productListing-name';
             
             if (isset($_GET['manufacturers'])) {
               $lc_text = osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . '&manufacturers=' . $_GET['manufacturers']), $Qlisting->value('products_name')) . (($Qlisting->value('products_short_description') === NULL) || ($Qlisting->value('products_short_description') === '') ? '' : '<p>' . $Qlisting->value('products_short_description') . '</p>');
             } else {
               $lc_text = '&nbsp;' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . ($cPath ? '&cPath=' . $cPath : '')), $Qlisting->value('products_name')) . (($Qlisting->value('products_short_description') === NULL) || ($Qlisting->value('products_short_description') === '') ? '' : '<p>' . $Qlisting->value('products_short_description') . '</p>') . '&nbsp;';
             }
             
             break;
           case 'PRODUCT_LIST_REVIEWS':
             $lc_align = 'left';
             
             if ($osC_Product->getData('reviews_average_rating') > 0) {
               $lc_text = '<div>' . osc_image(DIR_WS_IMAGES . 'stars_' . $osC_Product->getData('reviews_average_rating') . '.png', sprintf($osC_Language->get('rating_of_5_stars'), $osC_Product->getData('reviews_average_rating'))) . '</div>';
               $lc_text .= '<div class="reviews_total">' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . ($cPath ? '&cPath=' . $cPath : '')), $osC_Product->getData('reviews_total') . ' Reviews') . '</div>';
             }else {
               $lc_text = osc_image(DIR_WS_IMAGES . 'stars_0.png', 'There is not any reviews for this product');
             }
             
             break;
           case 'PRODUCT_LIST_MANUFACTURER':
             $lc_text = '&nbsp;' . osc_link_object(osc_href_link(FILENAME_DEFAULT, 'manufacturers=' . $Qlisting->valueInt('manufacturers_id')), $Qlisting->value('manufacturers_name')) . '&nbsp;';
             
             break;
           case 'PRODUCT_LIST_PRICE':
             $lc_align = 'right';
             $lc_text = $osC_Product->getPriceFormated(true);
             
             break;
           case 'PRODUCT_LIST_QUANTITY':
             $lc_align = 'right';
             $lc_text = '&nbsp;' . $Qlisting->valueInt('products_quantity') . '&nbsp;';
             
             break;
           case 'PRODUCT_LIST_WEIGHT':
             $lc_align = 'right';
             $lc_text = '&nbsp;' . $osC_Weight->display($Qlisting->value('products_weight'), $Qlisting->value('products_weight_class')) . '&nbsp;';
             
             break;
           case 'PRODUCT_LIST_IMAGE':
             $lc_align = 'center';
             
             if (isset($_GET['manufacturers'])) {
               if ($Qlisting->value('products_type') == PRODUCT_TYPE_SIMPLE) {
                 $lc_text = osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . '&manufacturers=' . $_GET['manufacturers']), $osC_Image->show($Qlisting->value('image'), $Qlisting->value('products_name')), 'id="productImage'. $Qlisting->value('products_id') . '"');
               }else {
                 $lc_text = osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . '&manufacturers=' . $_GET['manufacturers']), $osC_Image->show($Qlisting->value('image'), $Qlisting->value('products_name')));
               }  
             } else {
               if ($Qlisting->value('products_type') == PRODUCT_TYPE_SIMPLE) {
                 $lc_text = osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($Qlisting->value('image'), $Qlisting->value('products_name')), 'id="productImage'. $Qlisting->value('products_id') . '"');
               }else {
                 $lc_text = osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_id') . ($cPath ? '&cPath=' . $cPath : '')), $osC_Image->show($Qlisting->value('image'), $Qlisting->value('products_name')));
               }                
             }
             
             break;
           case 'PRODUCT_LIST_BUY_NOW':
             $lc_align = 'center';
             
             if ($Qlisting->value('products_type') == PRODUCT_TYPE_SIMPLE) {
               $lc_text = osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now'), 'class="ajaxAddToCart" id="ac_productlisting_'. $Qlisting->value('products_id') . '"')) . '&nbsp;<br />';
             }else {
               $lc_text = osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=cart_add'), osc_draw_image_button('button_buy_now.gif', $osC_Language->get('button_buy_now'))) . '&nbsp;<br />';
             }  
             $lc_text .= osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=compare_products_add'), $osC_Language->get('add_to_compare')) . '&nbsp;<br />';
             $lc_text .= osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $Qlisting->value('products_id') . '&' . osc_get_all_get_params(array('action')) . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist'));
              
             break;
         }
         
         echo '      <td ' . ((empty($lc_align) === false) ? 'align="' . $lc_align . '" ' : '') . ' valign="top" class="productListing-data' . ((!empty($lc_class)) ? " " . $lc_class : "") .'">' . $lc_text . '</td>' . "\n";
       }
       
       echo '    </tr>' . "\n";
     }
 ?>
</table>
