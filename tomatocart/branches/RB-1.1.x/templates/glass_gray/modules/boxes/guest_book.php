<?php
/*
  $Id: guest_book.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<!-- box guest_book start //-->

<div id="boxGuestbook" class="boxNew">
  <div class="boxTitle"><?php echo osc_link_object($osC_Box->getTitleLink(), $osC_Box->getTitle()); ?></div>

  <div class="boxContents"><?php echo $osC_Box->getContent(); ?></div>
</div>
<!-- box guest_book end //-->
