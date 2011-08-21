<?php
/*
  $Id: livezilla.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $outputs = $osC_Box->getOutputs();
?>

   <!-- box livezilla start //-->
   
   <div class="boxNew">
     <div class="boxTitle"><?php echo $osC_Box->getTitle(); ?></div>
   
     <div class="boxContents">
       <?php
         echo '<div style="text-align: center;padding-left:35px;">' . $outputs . '</div>';   
       ?>
     </div>
   </div>

<!-- box livezilla  end //-->
