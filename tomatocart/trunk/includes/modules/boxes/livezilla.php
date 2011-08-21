<?php
/*
  $Id: livezilla.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Boxes_livezilla extends osC_Modules {
    var $_title,
        $_code = 'livezilla',
        $_author_name = 'tomatocart',
        $_author_www = 'http://www.tomatocart.com',
        $_group = 'boxes';

    function osC_Boxes_livezilla() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_livezilla_heading');
    }
    
    function initialize() {
      return true;
    }

    function install() {
      global $osC_Database, $osC_Language;
      
      parent::install();
      
      $osC_Database->simpleQuery("INSERT INTO " .  TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . $osC_Language->get('box_livezilla_code') . "', 'BOX_LIVEZILLA_CODE', '', 'Please add the livezilla javasript block', '6', '0', 'osc_cfg_set_textarea_field', now())");
    }
    
    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_LIVEZILLA_CODE');
      }
      
      return $this->_keys;
    }
    
    function getOutputs() {
      if (defined('BOX_LIVEZILLA_CODE')) {
        $outputs = BOX_LIVEZILLA_CODE;
        
        if (!empty($outputs)) {
          return $outputs;
        }
      }
           
      return false;
    }
    
    function hasContent() {
      $outputs = $this->getOutputs();
      
      if ( !empty($outputs) ) {
        return true;
      }else {
        return false;
      }
    }
  }
?>
