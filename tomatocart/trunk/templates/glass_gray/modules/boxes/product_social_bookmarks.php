<?php
/*
  $Id: product_social_bookmarks.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $outputs = $osC_Box->getOutputs();
  
  if (!empty($outputs)) {
?>

   <!-- box social bookmarks start //-->
   
   <div class="boxNew">
     <div class="boxTitle"><?php echo $osC_Box->getTitle(); ?></div>
     
   
     <div class="boxContents">
       <?php
         ksort($outputs);
         
         echo '<div style="text-align: center">' . implode(' ', $outputs) . '</div>';   
       ?>
     </div>
   </div>

<!-- box social bookmarks end //-->

<?php 
  }
?>