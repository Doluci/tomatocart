<?php
/*
  $Id: google_base.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_Google_base extends osC_Access {
    var $_module = 'google_base',
        $_group = 'content',
        $_icon = 'google_base.png',
        $_title,
        $_sort_order = 500;

    function osC_Access_Google_base() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_google_base_title');

      $this->_subgroups = array(array('iconCls' => 'icon-google_base_manage_items-win',
                                      'shortcutIconCls' => 'icon-google_base_manage_items-shortcut',
                                      'title' => $osC_Language->get('access_google_base_manage_items_title'), 
                                      'identifier' => 'google_base_manage_items-win'));
    }
  }
?>
