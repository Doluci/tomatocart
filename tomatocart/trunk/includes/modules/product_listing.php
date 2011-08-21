<?php
/*
  $Id: product_listing.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  // create column list
  $define_list = array('PRODUCT_LIST_SKU' => PRODUCT_LIST_SKU, 
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_REVIEWS' => PRODUCT_LIST_REVIEWS,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE);
  
  if (isset($osC_Category) && !empty($osC_Category)) {
    if (($osC_Category->getMode() == CATEGORIES_MODE_AVAILABLE_FOR_ORDER) || ($osC_Category->getMode() == CATEGORIES_MODE_SHOW_PRICE) ) {
      $define_list['PRODUCT_LIST_PRICE'] = PRODUCT_LIST_PRICE;
    }
    
    if ($osC_Category->getMode() == CATEGORIES_MODE_AVAILABLE_FOR_ORDER) {
      $define_list['PRODUCT_LIST_BUY_NOW'] = PRODUCT_LIST_BUY_NOW;
    }
  }else {
    $define_list['PRODUCT_LIST_PRICE'] = PRODUCT_LIST_PRICE;
    $define_list['PRODUCT_LIST_BUY_NOW'] = PRODUCT_LIST_BUY_NOW;
  }
  
  asort($define_list);
  
  $column_list = array();
  reset($define_list);
  while (list($key, $value) = each($define_list)) {
    if ($value > 0) $column_list[] = $key;
  }
?>

<div>
  <?php
    if ($Qlisting->numberOfRows() > 0) {
      $sortings = array();
      $list_headings = array();
      
      for ($col = 0, $n = sizeof($column_list); $col < $n; $col++) {
        $lc_key = false;
        $lc_align = 'center';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_SKU':
            $lc_text = $osC_Language->get('listing_sku_heading');
            $lc_key = 'sku';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_text = $osC_Language->get('listing_products_heading');
            $lc_key = 'name';
            break;
          case 'PRODUCT_LIST_REVIEWS':
            $lc_text = $osC_Language->get('listing_products_reviews_heading');
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_text = $osC_Language->get('listing_manufacturer_heading');
            $lc_key = 'manufacturer';
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_text = $osC_Language->get('listing_price_heading');
            $lc_key = 'price';
            $lc_align = 'right';
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_text = $osC_Language->get('listing_quantity_heading');
            $lc_key = 'quantity';
            $lc_align = 'right';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_text = $osC_Language->get('listing_weight_heading');
            $lc_key = 'weight';
            $lc_align = 'right';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_text = $osC_Language->get('listing_image_heading');
            $lc_align = 'center';
            break;
          case 'PRODUCT_LIST_BUY_NOW':
            $lc_text = $osC_Language->get('listing_buy_now_heading');
            $lc_align = 'center';
            break;
        }
        
        $list_headings[] = array('lc_align' => $lc_align, 'lc_text' => $lc_text);
        
        if ($lc_key !== false) {
          // Put sortable field into array
          $sortings[] = array('id' => $lc_key, 'text' => $lc_text . '(asc)');
          $sortings[] = array('id' => $lc_key . '|d', 'text' => $lc_text . '(desc)');
        }
      }
      
      if (!empty($sortings)) {
        $frm_sort = '<div class="productSorts">' .
        
        $frm_sort .= '<form name="sort" action="' . osc_href_link(basename($_SERVER['SCRIPT_FILENAME'])) . '" method="get">';
        $frm_sort .= '<label>' . $osC_Language->get('products_sort_label') . '</label>';
        
        $params = explode('&', osc_get_all_get_params(array('page', 'sort')));
        foreach ($params as $key => $value) {
          $key_value = explode('=', $value);
          $frm_sort .= osc_draw_hidden_field($key_value[0], $key_value[1]);
        }
        
        $frm_sort .= osc_draw_pull_down_menu('sort', $sortings, $_GET['sort'], ' onchange="this.form.submit()"');
        
        $frm_sort .= '</form>';
        $frm_sort .= '</div>';
        
        echo $frm_sort;
      }
      ?>
      
      <!-- AddThis Button BEGIN -->
      <?php 
        if (defined('PRODUCT_LIST_SOCIAL_BOOKMARKS') && PRODUCT_LIST_SOCIAL_BOOKMARKS == 1) {
      ?>
          <div class="addthis_toolbox addthis_default_style" style="float: right;">
            <a href="http://www.addthis.com/bookmark.php?v=250&amp;username=jackyin" class="addthis_button_compact">Share</a>
            <span class="addthis_separator">|</span>
            <a class="addthis_button_preferred_1"></a>
            <a class="addthis_button_preferred_2"></a>
            <a class="addthis_button_preferred_3"></a>
            <a class="addthis_button_preferred_4"></a>
          </div>
          <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
          <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=jackyin"></script>
      
      <?php 
        }
      ?>
      <!-- AddThis Button END -->
       
   <?php
      if ($osC_Template->getGroup() == substr(FILENAME_DEFAULT, 0, strpos(FILENAME_DEFAULT, '.php'))) {
        $group = FILENAME_DEFAULT;
        
        if (isset($_GET['manufacturers']) && !empty($_GET['manufacturers'])) {
          $params = 'manufacturers=' . $_GET['manufacturers'];
        }else {
          $params = 'cPath=' . $cPath;
        }
      }else if ($osC_Template->getGroup() == substr(FILENAME_SEARCH, 0, strpos(FILENAME_SEARCH, '.php'))) {
        $group = FILENAME_SEARCH;
        $params =  'keywords=' . $_GET['keywords'];
      }else if ($osC_Template->getGroup() == substr(FILENAME_PRODUCTS, 0, strpos(FILENAME_PRODUCTS, '.php'))) {
        $group = FILENAME_PRODUCTS;
        $params = 'new';
      }
      
      if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        $params .= '&sort=' . $_GET['sort'];
      }
      
      $list_view = '<span><strong>' . $osC_Language->get('products_list_view') . '</strong></span>' . 
                   '<span>' . osc_link_object(osc_href_link($group, $params . '&view=grid'), $osC_Language->get('products_grid_view')) . '</span>';
        
      $grid_view = '<span>' . osc_link_object(osc_href_link($group, $params . '&view=list'), $osC_Language->get('products_list_view')) . '</span>' .
                   '<span><strong>' . $osC_Language->get('products_grid_view') . '</strong></span>';
      
      
      $product_view_style = '<div class="productViewStyle">' .
                              '<span>' . $osC_Language->get('products_views_label') . '</span>';
      
      if ( isset($_GET['view']) && $_GET['view'] == 'list' ) {
        $_SESSION['product_view_style'] = 'list';
        
        $product_view_style .= $list_view;
      }else if ( isset($_GET['view']) && $_GET['view'] == 'grid') {
        $_SESSION['product_view_style'] = 'grid';
        
        $product_view_style .= $grid_view;
      }else if ( isset($_SESSION['product_view_style']) && $_SESSION['product_view_style'] == 'list' ) {
        $product_view_style .= $list_view;
      }else if ( isset($_SESSION['product_view_style']) && $_SESSION['product_view_style'] == 'grid' ) {
        $product_view_style .= $grid_view;
      }else {
        $_SESSION['product_view_style'] = 'list';
        
        $product_view_style .= $list_view;
      }

      $product_view_style .= '</div>';
      
      echo $product_view_style;
      
      if ( ($Qlisting->numberOfRows() > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
    ?>
    
        <div class="listingPageLinks">
          <span style="float: right;"><?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>
        
          <?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
        </div>
      
    <?php
      }
      
      if (isset($_SESSION['product_view_style']) && !empty($_SESSION['product_view_style'])) {
        if ($_SESSION['product_view_style'] == 'list') {
          require('includes/modules/products_list.php');
        }else if ($_SESSION['product_view_style'] == 'grid') {
          require('includes/modules/products_grid.php');
        }
      }else {
        if (!isset($_GET['view']) || (isset($_GET['view']) && $_GET['view'] == 'list')) {
          require('includes/modules/products_list.php');
        }else if (isset($_GET['view']) && $_GET['view'] == 'grid') {
          require('includes/modules/products_grid.php');
        }
      }
    }else {
      echo $osC_Language->get('no_products_in_category');
    }
  ?>
</div>

<?php 
  echo $frm_sort;
  echo $product_view_style;
?>

<?php
  if ( ($Qlisting->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
    <div class="listingPageLinks">
      <span style="float: right;"><?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>
    
      <?php echo $Qlisting->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
    </div>
<?php
  }
?>
