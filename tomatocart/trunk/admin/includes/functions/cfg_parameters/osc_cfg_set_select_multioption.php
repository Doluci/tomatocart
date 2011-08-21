<?php
/*
  $Id: osc_cfg_set_select_multioption.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_select_multioption($default, $key = null) {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $options = array('1DM', '1DML', '1DA', '1DAL', '1DAPI', '1DP', '1DPL', '2DM', '2DML', '2DA', '2DAL', '3DS', 'GND', 'STD', 'XPR', 'XPRL', 'XDM', 'XDML', 'XPD');

    $select_options = array();
    foreach($options as $option) {
      $select_options[] = array('id' => $option, 'text' => $option);
    }

    $control = array();
    $control['name'] = $name;
    $control['type'] = 'multiselect';
    $control['values'] = $select_options;

    return $control;
  }
?>
