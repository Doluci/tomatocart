<?php
/*
  $Id: product_social_bookmarks.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  define('TOC_SOCIAL_BOOKMARKS_MODULES', DIR_FS_CATALOG . 'includes/modules/social_bookmarks');
  
  require_once('includes/classes/directory_listing.php');

  class osC_Boxes_product_social_bookmarks extends osC_Modules {
    var $_title,
        $_code = 'product_social_bookmarks',
        $_author_name = 'tomatocart',
        $_author_www = 'http://www.tomatocart.com',
        $_group = 'boxes';

    function osC_Boxes_product_social_bookmarks() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_product_social_bookmarks_heading');
    }

    function initialize() {
      global $osC_Database, $osC_Language, $current_category_id, $osC_Product;
      
      if (!empty($osC_Product)) {
        $social_bookmarks = $this->_getSocialBookmarks();
        
        if (!empty($social_bookmarks)) {
          $this->_sb_outputs = array();
          foreach($social_bookmarks as $social_bookmarks) {
            if ($social_bookmarks->isEnabled()) {
              $sort_order = $social_bookmarks->getSortOrder();
              $this->_sb_outputs[$sort_order] = $social_bookmarks->getOutput();
            }
          }
        }
      }
    }
    
    function install() {
      parent::install();
      
      $social_bookmarks = $this->_getSocialBookmarks();
      
      if (!empty($social_bookmarks)) {
        foreach($social_bookmarks as $social_bookmark) {
            $social_bookmark->install();
        }
      }
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $social_bookmarks = $this->_getSocialBookmarks();
        
        $this->_keys = array();
        if (!empty($social_bookmarks)) {
          foreach($social_bookmarks as $social_bookmark) {
            $social_bookmark_keys = $social_bookmark->keys();
            
            $this->_keys = array_merge($this->_keys, $social_bookmark_keys);
          }
        }
      }
      
      return $this->_keys;
    }
    
    function getOutputs() {
      if (isset($this->_sb_outputs) && !empty($this->_sb_outputs)) {
        return $this->_sb_outputs;
      }
      
      return false;
    }
    
    function hasContent() {
      return true;
    }
    
    function _getSocialBookmarks() {
      $osC_DirectoryListing = new osC_DirectoryListing(TOC_SOCIAL_BOOKMARKS_MODULES);
      
      $social_bookmarks = array();
      foreach($osC_DirectoryListing->getFiles() as $social_bookmark) {
        $social_bookmark_class = substr($social_bookmark['name'], 0, strrpos($social_bookmark['name'], '.'));
        
        if ( !class_exists($social_bookmark_class) ) {
          include(TOC_SOCIAL_BOOKMARKS_MODULES . '/'. $social_bookmark_class . '.php');
        }
        
        $social_bookmarks[] = new $social_bookmark_class();
      }
      
      return $social_bookmarks;
    }
  }
?>
