<?php
/*
  $Id: google_adsense.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Content_google_adsense extends osC_Modules {
    var $_title,
        $_code = 'google_adsense',
        $_author_name = 'TomatoCart',
        $_author_www = 'http://www.tomatocart.com',
        $_group = 'content';

/* Class constructor */

    function osC_Content_google_adsense() {
      global $osC_Language;

      $this->_title = $osC_Language->get('content_google_adsense_heading');
    }
    
    function initialize() {
      return true;
    }

    function install() {
      global $osC_Database, $osC_Language;

      parent::install();
      
      $image = osc_image('images/adsense_script.gif');
      
      $osC_Database->simpleQuery("INSERT INTO " .  TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . $osC_Language->get('google_adsense_code') . "', 'MODULE_GOOGLE_ADSENSE_CODE', '', 'Please add the google javasript block', '6', '0', 'osc_cfg_set_textarea_field', now())");
      $osC_Database->simpleQuery("INSERT INTO " .  TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . $osC_Language->get('google_adsense_example_code') . "', 'MODULE_GOOGLE_ADSENSE_EXAMPLE_CODE', '" . $image . "' , 'Please add the google javasript block as this example code', '6', '0', 'osc_cfg_set_panel', now())");
    }
    
    function getOutputs() {
      if (defined('MODULE_GOOGLE_ADSENSE_CODE')) {
        $outputs = MODULE_GOOGLE_ADSENSE_CODE;
        
        if (!empty($outputs)) {
          return $outputs;
        }
      }
           
      return false;
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_GOOGLE_ADSENSE_CODE', 'MODULE_GOOGLE_ADSENSE_EXAMPLE_CODE');
      }

      return $this->_keys;
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
