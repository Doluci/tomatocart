<?php
/*
  $Id: available_products_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.google_base.AvailableProductsDialog = function(config) {
  config = config || {}; 
  
  config.id = 'google_base_available_products-dialog-win';
  config.title = '<?php echo $osC_Language->get('heading_title_google_base_available_products'); ?>';
  config.modal = true;
  config.width = 600;
  config.height = 400;
  config.iconCls = 'icon-google_base_available_products-dialog-win';
    
  config.buttons = [
    {
      text: TocLanguage.btnClose,
      handler: function () {
        this.close();
      },
      scope: this
    }
  ];
  
  Toc.google_base.AvailableProductsDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.google_base.AvailableProductsDialog, Ext.Window, {
});
