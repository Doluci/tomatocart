<?php
/*
  $Id: info.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">

  <div class="content">
    <div style="float: left;">
      <link href="templates/<?php echo $osC_Template->getCode(); ?>/javascript/milkbox/milkbox.css" rel="stylesheet" type="text/css" />
      <link href="templates/<?php echo $osC_Template->getCode(); ?>/javascript/zoom/zoom.css" rel="stylesheet" type="text/css" />
            
      <div id="productImages">
      <?php
        echo osc_link_object($osC_Image->getImageUrl($osC_Product->getImage(), 'originals'), $osC_Image->show($osC_Product->getImage(), $osC_Product->getTitle(), ' large-img="' . $osC_Image->getImageUrl($osC_Product->getImage(), 'large') . '" id="product_image" style="padding:0px;border:0px;"', 'product_info'),'id="defaultProductImage"');
        echo '<div style="clear:both"></div>';
    
        $images = $osC_Product->getImages();
        foreach ($images as $image){
          echo osc_link_object($osC_Image->getImageUrl($image['image'], 'originals'), $osC_Image->show($image['image'], $osC_Product->getTitle(), '', 'mini'), 'product-info-img="' . $osC_Image->getImageUrl($image['image'], 'product_info') . '" large-img="' . $osC_Image->getImageUrl($image['image'], 'large') . '" rel="milkbox:group_products" style="float:left" class="mini"') . "\n";
        }
        
        $price = '';
        if (ALLOW_DISPLAY_PRICE_TO_GUESTS == '1') {
          $price = $osC_Product->getPriceFormated(true). '&nbsp;' . ( (DISPLAY_PRICE_WITH_TAX == '1') ? $osC_Language->get('including_tax') : '');;
        } else {
          if ($osC_Customer->isLoggedOn() === true) {
            $price = $osC_Product->getPriceFormated(true). '&nbsp;' . ( (DISPLAY_PRICE_WITH_TAX == '1') ? $osC_Language->get('including_tax') : '');;
          }
        }
      ?>
      </div>
    </div>

    <form id="cart_quantity" name="cart_quantity" action="<?php echo osc_href_link(FILENAME_PRODUCTS, osc_get_all_get_params(array('action')) . '&action=cart_add'); ?>" method="post">
   
    <table id="productInfo" border="0" cellspacing="0" cellpadding="2" style="float: right; width: 270px">
    
      <tr>
        <td colspan="2" class="productPrice"><?php echo $price; ?></td>
      </tr>
      
  <?php
    if (!$osC_Product->hasVariants()) {
  ?>
      <tr>
        <td class="label" width="45%"><?php echo $osC_Language->get('field_sku'); ?></td>
        <td><?php echo $osC_Product->getSKU(); ?>&nbsp;</td>
      </tr>
  <?php
    }
  ?>
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_availability'); ?></td>
        <td><?php echo $osC_Product->getQuantity() > 0 ? $osC_Language->get('in_stock') : $osC_Language->get('out_of_stock'); ?></td>
      </tr>
      
  <?php
    if (PRODUCT_INFO_QUANTITY == '1') {
  ?>
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_quantity'); ?></td>
        <td><?php echo $osC_Product->getQuantity() . ' ' . $osC_Product->getUnitClass(); ?></td>
      </tr>
  <?php
    }

    if (PRODUCT_INFO_MOQ == '1') {
  ?>
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_moq'); ?></td>
        <td><?php echo $osC_Product->getMOQ() . ' ' . $osC_Product->getUnitClass(); ?></td>
      </tr>
  <?php
    }

    if (PRODUCT_INFO_ORDER_INCREMENT == '1') {
  ?>
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_order_increment'); ?></td>
        <td><?php echo $osC_Product->getOrderIncrement() . ' ' . $osC_Product->getUnitClass(); ?></td>
      </tr>
  <?php
    }
    
    if ($osC_Product->isDownloadable() && $osC_Product->hasSampleFile()) {
  ?>
      <tr>  
        <td class="label"><?php echo $osC_Language->get('field_sample_url'); ?></td>
        <td><?php echo osc_link_object(osc_href_link(FILENAME_DOWNLOAD, 'type=sample&id=' . $osC_Product->getID()), $osC_Product->getSampleFile()); ?></td>
      </tr>     
  <?php
    }

    if ($osC_Product->hasURL()) {
  ?>
      <tr>
        <td colspan="2"><?php echo sprintf($osC_Language->get('go_to_external_products_webpage'), osc_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($osC_Product->getURL()), 'NONSSL', null, false)); ?></td>
      </tr>
      
  <?php
    }
  
    if ($osC_Product->getDateAvailable() > osC_DateTime::getNow()) {
  ?>
      <tr>  
        <td colspan="2" align="center"><?php echo sprintf($osC_Language->get('date_availability'), osC_DateTime::getLong($osC_Product->getDateAvailable())); ?></td>
      </tr>
  <?php
    }
  ?>
      
  <?php
    if ($osC_Product->hasAttributes()) {
      $attributes = $osC_Product->getAttributes();
      
      foreach($attributes as $attribute) {
  ?>
        <tr>          
          <td class="label" valign="top"><?php echo $attribute['name']; ?>:</td>
          <td><?php echo $attribute['value']; ?></td>
        </tr>
  <?php
    }
  }
  ?>  
  
   <?php
    if ($osC_Product->getData('reviews_average_rating') > 0) {
  ?>  
      <tr>      
        <td class="label"><?php echo $osC_Language->get('average_rating'); ?></td>
        <td><?php echo osc_image(DIR_WS_IMAGES . 'stars_' . $osC_Product->getData('reviews_average_rating') . '.png', sprintf($osC_Language->get('rating_of_5_stars'), $osC_Product->getData('reviews_average_rating'))); ?></td>
      </tr>
  <?php
    }
  ?>
        
  <?php
    if ($osC_Product->isGiftCertificate()) {
      if ($osC_Product->isOpenAmountGiftCertificate()) {
  ?>
      <tr>      
        <td class="label"><?php echo $osC_Language->get('field_gift_certificate_amount'); ?></td>
        <td><?php echo osc_draw_input_field('gift_certificate_amount', $osC_Product->getOpenAmountMinValue(), 'size="18"'); ?></td>
      </tr>
  <?php
    }
  ?>
      <tr>      
        <td class="label"><?php echo $osC_Language->get('field_senders_name'); ?></td>
        <td><?php echo osc_draw_input_field('senders_name', null, 'size="18"'); ?></td>
      </tr>
  <?php
    if ($osC_Product->isEmailGiftCertificate()) {
  ?>
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_senders_email'); ?></td>
        <td><?php echo osc_draw_input_field('senders_email', null, 'size="18"'); ?></td>
      </tr>
  <?php
    }
  ?>        
      <tr>
        <td class="label"><?php echo $osC_Language->get('field_recipients_name'); ?></td>
        <td><?php echo osc_draw_input_field('recipients_name', null, 'size="18"'); ?></td>
      </tr>
  <?php
    if ($osC_Product->isEmailGiftCertificate()) {
  ?>  
      <tr>      
        <td class="label"><?php echo $osC_Language->get('field_recipients_email'); ?></td>
        <td><?php echo osc_draw_input_field('recipients_email', null, 'size="18"'); ?></td>
      </tr>
  <?php
    }
  ?>
  
      <tr>          
        <td class="label" valign="top"><?php echo $osC_Language->get('fields_gift_certificate_message'); ?></td>
        <td><?php echo osc_draw_textarea_field('message', null, 15, 2); ?></td>
      </tr>
  <?php
  }
  ?>
      <tr>
        <td colspan="2" align="center" valign="top" style="padding-top: 15px">
          
          <?php
            if (!$osC_Product->hasVariants()) {
              if ($osC_Product->isSimple()) {
                echo '<b>' . $osC_Language->get('field_short_quantity') . '</b>&nbsp;' . osc_draw_input_field('quantity', $osC_Product->getMOQ(), 'size="3"') . '&nbsp;' . osc_draw_image_submit_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart'), 'style="vertical-align:middle;" class="ajaxAddToCart" pid="' . osc_get_product_id($osC_Product->getID()) . '"');
              }else {
                echo '<b>' . $osC_Language->get('field_short_quantity') . '</b>&nbsp;' . osc_draw_input_field('quantity', $osC_Product->getMOQ(), 'size="3"') . '&nbsp;' . osc_draw_image_submit_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart'), 'style="vertical-align:middle;"');
              }  
            }
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <?php
            echo osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $osC_Product->getID() . '&' . '&action=compare_products_add'), $osC_Language->get('add_to_compare')) . '&nbsp;<span>|</span>&nbsp;' . osc_link_object(osc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $osC_Product->getID() . '&action=wishlist_add'), $osC_Language->get('add_to_wishlist'));
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <p class="shortDescription"><?php echo $osC_Product->getShortDescription(); ?></p>
        </td>
      </tr>
    </table>
    </form>
    <div style="clear: both;"></div>
  </div>
  
</div>

  <div id="productInfoTab">
    <?php
      if ($osC_Product->getDescription()) {
        echo '<a tab="tabDescription" href="javascript:void(0);">' . $osC_Language->get('section_heading_products_description'). '</a>'; 
      }
      
      if($osC_Product->hasVariants()) {
        echo '<a tab="tabVariants" href="javascript:void(0);">' . $osC_Language->get('section_heading_variants') . '</a>'; 
      }
      
      echo '<a tab="tabReviews" href="javascript:void(0);">' . $osC_Language->get('section_heading_reviews') . '(' . $osC_Reviews->getReviewsCount($osC_Product->getID()) . ')</a>';
      
      if ($osC_Product->hasQuantityDiscount()) {
        echo '<a tab="tabQuantityDiscount" href="javascript:void(0);">' . $osC_Language->get('section_heading_quantity_discount') . '</a>';         
      }
      
      if ($osC_Product->hasAttributes()) {
        echo '<a tab="tabAttributes" href="javascript:void(0);">' . $osC_Language->get('products_attributes_filter') . '</a>'; 
      }
      
      if ($osC_Product->hasAttachments()) {
        echo '<a tab="tabAttachments" href="javascript:void(0);">' . $osC_Language->get('section_heading_products_attachments') . '</a>'; 
      }
    ?>
    <div style="clear:both;"></div>
  </div> 
  
  
    <?php if ($osC_Product->getDescription()) {?>
      <div id="tabDescription">
        <div class="moduleBox">
          <div class="content"><?php echo $osC_Product->getDescription(); ?></div>
        </div>
      </div>
    <?  } ?>
    
    <div id="tabReviews">
      <div class="moduleBox">
        <div class="content">
          <?php
            if ($osC_Reviews->getReviewsCount($osC_Product->getID())==0) {
              echo '<p>' . $osC_Language->get('no_review') . '</p>';
            } else {
              $Qreviews = osC_Reviews::getListing($osC_Product->getID());
              
              while ($Qreviews->next()) {
          ?>
              <dl class="review">
                <?php
                  echo '<dt>' . osc_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.png', sprintf($osC_Language->get('rating_of_5_stars'), $Qreviews->valueInt('reviews_rating'))).'&nbsp;&nbsp;&nbsp;&nbsp;'.sprintf($osC_Language->get('reviewed_by'), '&nbsp; <b>' . $Qreviews->valueProtected('customers_name')) . '</b>' . '&nbsp;&nbsp;(' . $osC_Language->get('field_posted_on').'&nbsp;' . osC_DateTime::getLong($Qreviews->value('date_added')) . ')' . '</dt>';
                   
                  echo '<dd>';
                  $ratings = osC_Reviews::getCustomersRatings($Qreviews->valueInt('reviews_id'));
                  
                  if (sizeof($ratings) > 0) {
                    echo '<table class="ratingsResult">';
                    foreach ($ratings as $rating) {
                      echo '<tr>
                             <td class="name">' . $rating['name'] . '</td><td>' . osc_image(DIR_WS_IMAGES . 'stars_' . $rating['value'] . '.png', sprintf($osC_Language->get('rating_of_5_stars'), $rating['value'])) . '</td>
                            </tr>';
                    }
                    echo '</table>';
                  }
                  
                  echo '<p>' . $Qreviews->valueProtected('reviews_text') . '</p>';
                  echo '</dd>'; 
                ?>
              </dl>
          <?php
              }
            }
          ?>
          
          <hr />
          
          <h3><?php echo $osC_Language->get('heading_write_review'); ?></h3>
          
          <?php if (!$osC_Customer->isLoggedOn()) { ?>
            <p><?php echo sprintf($osC_Language->get('login_to_write_review'), osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL')); ?></p>
          <?php } else { ?>

            <p><?php echo $osC_Language->get('introduction_rating'); ?></p>
              
            <form id="frmReviews" name="newReview" action="<?php echo osc_href_link(FILENAME_PRODUCTS, 'reviews=new&' . $osC_Product->getID() . '&action=process'); ?>" method="post">
            
            <?php
              $ratings = osC_Reviews::getCategoryRatings($osC_Product->getCategoryID());
              if (sizeof($ratings) == 0) {
            ?>
              <p><?php echo '<b>' . $osC_Language->get('field_review_rating') . '</b>&nbsp;&nbsp;&nbsp;' . $osC_Language->get('review_lowest_rating_title') . ' ' . osc_draw_radio_field('rating', array('1', '2', '3', '4', '5')) . ' ' . $osC_Language->get('review_highest_rating_title'); ?></p>
              <input type="hidden" id="rat_flag" name="rat_flag" value="0" />
            <?php 
            } else {
            ?>
                <table class="ratings" border="1" cellspacing="0" cellpadding="0">
                  <thead>
                    <tr>
                      <td width="45%">&nbsp;</td>
                      <td><?php echo $osC_Language->get('1_star'); ?></td>
                      <td><?php echo $osC_Language->get('2_stars'); ?></td>
                      <td><?php echo $osC_Language->get('3_stars'); ?></td>
                      <td><?php echo $osC_Language->get('4_stars'); ?></td>
                      <td><?php echo $osC_Language->get('5_stars'); ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $i = 0;
                    foreach ( $ratings as $key => $value ) {
                    ?>
                      <tr>
                        <td><?php echo $value;?></td>
                        <td><?php echo osc_draw_radio_field('rating_' . $key, 1, null, ' title="radio' . $i . '" ');?></td>
                        <td><?php echo osc_draw_radio_field('rating_' . $key, 2, null, ' title="radio' . $i . '" ');?></td>
                        <td><?php echo osc_draw_radio_field('rating_' . $key, 3, null, ' title="radio' . $i . '" ');?></td>
                        <td><?php echo osc_draw_radio_field('rating_' . $key, 4, null, ' title="radio' . $i . '" ');?></td>
                        <td><?php echo osc_draw_radio_field('rating_' . $key, 5, null, ' title="radio' . $i . '" ');?></td>
                      </tr>
                    <?php 
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              <?php
                }
              ?>
              
              <h6><?php echo $osC_Language->get('field_review'); ?></h6>
              
              <?php echo osc_draw_textarea_field('review', null, 45, 5); ?>
            
              <div class="submitFormButtons">
                <input type="hidden" id="radio_lines" name="radio_lines" value="<?php echo $i; ?>"/>
                <?php echo osc_draw_image_submit_button('submit_reviews.gif', $osC_Language->get('submit_reviews')); ?>
              </div>
            
            </form>
          <?php } ?>
        </div>  
      </div>
    </div>
      
    <?php if($osC_Product->hasVariants()) { ?>
      <div id="tabVariants">
        <div class="moduleBox">
          <div class="content"><?php echo $osC_Product->renderVariantsTable(); ?></div>
        </div>
      </div>
    <?php } ?>
    
    <?php  if ($osC_Product->hasQuantityDiscount()) { ?>
      <div id="tabQuantityDiscount">
        <div class="moduleBox">
          <div class="content"><?php echo $osC_Product->renderQuantityDiscountTable(); ?></div>
        </div>
      </div>
    <?php } ?>
    
    <?php     
      if ($osC_Product->hasAttributes()) {
      $attributes = $osC_Product->getAttributes();
    ?>
      <div id="tabAttributes">
        <div class="moduleBox">
          <div class="content">
            <?php foreach($attributes as $attribute) {?>
             <tr>          
               <td class="label" valign="top"><?php echo $attribute['name']; ?>:</td>
               <td><?php echo $attribute['value']; ?></td>
            </tr>
        <? } ?>
          </div>
        </div>
      </div>
    <?php }?>
    
    <?php 
    if ($osC_Product->hasAttachments()) {
      $attachments = $osC_Product->getAttachments();
    ?>
    <div id="tabAttachments">
      <div class="moduleBox">
        <div class="content">
          <dl>
          <?php
            foreach($attachments as $key => $attachment) {
              echo '<dt>' . 
                      osc_link_object(osc_href_link(FILENAME_DOWNLOAD, 'type=attachment&aid=' . $attachment['attachments_id']), $attachment['attachment_name']) . 
                   '</dt>' . 
                   '<dd>' . $attachment['description'] . '</dd>';
            } 
          ?>
          <dl>
        </div>
      </div>
    </div>
   <?php }?>

<div style="clear: both;"></div>

<script type="text/javascript" src="includes/javascript/tab_panel.js"></script>
<script type="text/javascript" src="includes/javascript/reviews.js"></script>
<script type="text/javascript" src="templates/<?php echo $osC_Template->getCode(); ?>/javascript/milkbox/milkbox.js"></script>
<script type="text/javascript" src="templates/<?php echo $osC_Template->getCode(); ?>/javascript/zoomer/zoomer.js"></script>
<script type="text/javascript">

window.addEvent('domready', function(){
  //zoom image
  var zoomer = new Zoomer('product_image', {
    big: $('product_image').get('large-img'),
    smooth: 10
  });
  
  //add mouse over events to mini images
  var miniImages = $$(".mini");
  if (miniImages.length > 0) {
    miniImages.each(function(img) {
      img.addEvent('mouseover', function(e) {
        $('product_image').src = this.get("product-info-img");
        $('product_image').set('large-img', this.get("large-img"));
        zoomer.big.src = this.get("large-img"); 
      });
    }, this);
  }
  
  //attach product image click event
  $('defaultProductImage').addEvent('click',function(e){
    e.preventDefault();
    Milkbox.openMilkbox(Milkbox.galleries[0], 0);
  });
  
  //tab panel
  new TabPanel({panel: $('productInfoTab'), activeTab: '<?php echo (isset($_GET['tab']) && !empty($_GET['tab']) ) ? $_GET['tab'] : ''; ?>'});
  
  //reviews
  new Reviews({
    flag: <?php echo (sizeof($ratings) == 0) ? '0' : '1' ?>,
    ratingsCount: <?php echo sizeof($ratings); ?>,
    reviewMinLength: <?php echo REVIEW_TEXT_MIN_LENGTH; ?>,
    ratingsErrMsg: '<?php echo $osC_Language->get('js_review_rating'); ?>',
    reviewErrMsg: '<?php echo sprintf($osC_Language->get('js_review_text'), REVIEW_TEXT_MIN_LENGTH); ?>',
    frmReviews: $('frmReviews')
  });
  
  //gift certificate
  <?php 
  if ($osC_Product->isGiftCertificate()) {
  ?>
    $('addToShoppingCart').addEvent('click', function(e){
      e.preventDefault();
      
      var errors = [];
      
    <?php 
    if ($osC_Product->isOpenAmountGiftCertificate()) {
      $min = $osC_Product->getOpenAmountMinValue();
      $max = $osC_Product->getOpenAmountMaxValue();
    ?>
      var amount = $('gift_certificate_amount').value;
      
      if (amount < <?php echo $min; ?> || amount > <?php echo $max; ?>) {
        errors.push('<?php echo sprintf($osC_Language->get('error_message_open_gift_certificate_amount'), $osC_Currencies->format($min), $osC_Currencies->format($max)); ?>');
      }
    <?php 
    } 
    ?>
    
    <?php 
    if ($osC_Product->isEmailGiftCertificate()) {
    ?>
    
      if ($('senders_name').value == '') {
        errors.push('<?php echo $osC_Language->get('error_sender_name_empty'); ?>');
      }
      
      if ($('senders_email').value == '') {
        errors.push('<?php echo $osC_Language->get('error_sender_email_empty'); ?>');
      }
      
      if ($('recipients_name').value == '') {
        errors.push('<?php echo $osC_Language->get('error_recipient_name_empty'); ?>');
      }
      
      if ($('recipients_email').value == '') {
        errors.push('<?php echo $osC_Language->get('error_recipient_email_empty'); ?>');
      }
      
      if ($('message').value == '') {
        errors.push('<?php echo $osC_Language->get('error_message_empty'); ?>');
      }
      
    <?php 
    } 
    ?>
      
      if (errors.length > 0) {
        alert(errors.join('\n'));
      } else {
        $('cart_quantity').submit();
      }
    });
  <?php 
  } 
  ?>
});
</script>
