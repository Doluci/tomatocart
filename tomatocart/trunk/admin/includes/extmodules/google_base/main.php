<?php
/*
  $Id: google_base.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  echo 'Ext.namespace("Toc.google_base");';
  
  require_once('manage_items_grid.php');
  require_once('available_products_grid.php');
  require_once('available_products_dialog.php');
?>

Ext.override(TocDesktop.GoogleBaseWindow, {

  createWindow: function(){
    switch(this.id) {
      case 'google_base_manage_items-win':
        win = this.createGoogleBaseManageItemsWindow();
        break;
    }
    
    win.show();
  },
  
  createGoogleBaseManageItemsWindow: function() {
    var desktop = this.app.getDesktop();
    var win = desktop.getWindow('google_base_manage_items-win');
    
    grd = new Toc.google_base.ManageItemsGrid({owner: this});
     
    if(!win){
      win = desktop.createWindow({
        id: 'google_base_manage_items-win',
        title: '<?php echo $osC_Language->get('heading_title_google_base_manage_items'); ?>',
        width: 800,
        height: 400,
        iconCls: this.iconCls,
        layout: 'fit',
        items: grd
      });
    }
    
    return win;
  },
  
  createAvailableProductsDialog: function() {
    var desktop = this.app.getDesktop();
    var dlg = desktop.getWindow('google_base_available_products-dialog-win');
    
    var grd = new Toc.google_base.AvailableProductsGrid({owner: this});
    
    if (!dlg) {
      this.dlgAvailableProducts = desktop.createWindow({owner: this, items: grd}, Toc.google_base.AvailableProductsDialog);
      
      return this.dlgAvailableProducts;
    }
  }
});
