<?php
require('includes/application_top.php');
$osC_ShoppingCart->reset(true);

// unregister session variables used during checkout
unset($_SESSION['comments']);
osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'success', 'SSL'));