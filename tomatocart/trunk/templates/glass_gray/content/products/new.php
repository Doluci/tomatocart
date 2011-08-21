<?php
/*
  $Id: new.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $Qlisting = osC_Product::getListingNew();
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php 
  require_once('includes/modules/product_listing.php');
?>

