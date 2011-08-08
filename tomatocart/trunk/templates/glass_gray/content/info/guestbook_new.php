<?php
/*
  $Id:  new_guestbook.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>
  <h1><?php echo $osC_Template->getPageTitle(); ?></h1>
   
  <?php 
    if ($messageStack->size('guestbook') > 0) {
      echo $messageStack->output('guestbook');
    }
  ?>   
  
  <div class="moduleBox">
    <h6><?php echo $osC_Language->get('guestbook_new_heading'); ?></h6>
    
    <div class="content">
      <form name="guestbook_edit" action="<?php echo osc_href_link(FILENAME_INFO, 'guestbook&save'); ?>" method="post">
        <ol> 
          <li><?php echo osc_draw_label($osC_Language->get('field_title'), 'title', null, true) . osc_draw_input_field('title', null);  ?></li>
          <li><?php echo osc_draw_label($osC_Language->get('field_email'), 'email', null, true) . osc_draw_input_field('email');  ?></li>  
          <li><?php echo osc_draw_label($osC_Language->get('field_url'), 'url') . osc_draw_input_field('url');  ?></li>  
          <li><?php echo osc_draw_label($osC_Language->get('field_content'), 'content', null, true) . osc_draw_textarea_field('content', '', 29);  ?></li>
          <li>
            <?php echo osc_draw_label($osC_Language->get('field_image_verification'), 'verify_code')  . osc_draw_input_field('verify_code') . '&nbsp;&nbsp;' . $osC_Language->get('verification_info_note') . 
            '<br/> <img style="margin-top: 5px;" src="' . osc_href_link(FILENAME_INFO, 'guestbook&captcha') . '" alt="Captcha" />';?>
          </li>
        </ol>
        
        <div class="submitFormButtons">
          <span style="float: right"><?php echo toc_draw_button($osC_Language->get('button_continue')) ?></span>
          
            <?php echo toc_draw_link_button(osc_href_link(FILENAME_INFO, 'guestbook'), $osC_Language->get('button_back')); ?>
        </div>
      </form>
    </div>
  </div>