<?php
/*
  $Id: available_products_grid.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.google_base.AvailableProductsGrid = function(config) {

  config = config || {};
  
  config.height = 330;
  
  config.border = false;
  config.viewConfig = {emptyText: TocLanguage.gridNoRecords};
  
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'google_base',
      action: 'get_available_products'
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'products_id'
    }, [
       'products_id',
       'products_name',
       'products_type',
       'products_sku',
       'products_price'
    ]),
    autoLoad: true
  });
  
  config.rowActions = new Ext.ux.grid.RowActions({
    actions:[{iconCls: 'icon-add-record', qtip: TocLanguage.tipAdd}],
    widthIntercept: Ext.isSafari ? 4 : 2
  });
  config.rowActions.on('action', this.onRowAction, this);    
  config.plugins = config.rowActions;
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {id:'available_product_name', header: '<?php echo $osC_Language->get('table_heading_available_products_name'); ?>', dataIndex: 'products_name'},
    {header: '<?php echo $osC_Language->get('table_heading_available_products_type'); ?>', dataIndex: 'products_type', align: 'center'},
    {header: '<?php echo $osC_Language->get('table_heading_available_products_sku'); ?>', dataIndex: 'products_sku', align: 'center'},
    {header: '<?php echo $osC_Language->get('table_heading_available_products_price'); ?>', dataIndex: 'products_price', align: 'center'},
    config.rowActions
  ]);
  config.autoExpandColumn = 'available_product_name';
  
  config.tbar = [
    {
      text: TocLanguage.btnBatchAdd,
      iconCls : 'add',
      handler: this.onBatchInsert,
      scope: this
    }
  ];
  
  var thisObj = this;
  config.bbar = new Ext.PageToolbar({
    pageSize: Toc.CONF.GRID_PAGE_SIZE,
    store: config.ds,
    steps: Toc.CONF.GRID_STEPS,
    beforePageText: TocLanguage.beforePageText,
    firstText: TocLanguage.firstText,
    lastText: TocLanguage.lastText,
    nextText: TocLanguage.nextText,
    prevText: TocLanguage.prevText,
    afterPageText: TocLanguage.afterPageText,
    refreshText: TocLanguage.refreshText,
    displayInfo: true,
    displayMsg: TocLanguage.displayMsg,
    emptyMsg: TocLanguage.emptyMsg,
    prevStepText: TocLanguage.prevStepText,
    nextStepText: TocLanguage.nextStepText
  });
  
  Toc.google_base.AvailableProductsGrid.superclass.constructor.call(this, config);
};

Ext.extend(Toc.google_base.AvailableProductsGrid, Ext.grid.GridPanel, {
  onRowAction: function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-add-record':
        var productsId = record.get('products_id');
    
        Ext.MessageBox.confirm(
          TocLanguage.msgWarningTitle, 
          '<?php echo $osC_Language->get('msgUploadConfirmation'); ?>',
          function(btn) {
            if (btn == 'yes') {
              Ext.Ajax.request({
                url: Toc.CONF.CONN_URL,
                params: {
                  module: 'google_base',
                  action: 'upload_single_product',
                  products_id: productsId
                },
                callback: function(options, success, response) {
                  var result = Ext.decode(response.responseText);
                  
                  if (result.success == true) {
                    this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                    
                    this.owner.dlgAvailableProducts.fireEvent('uploadSuccess');
                  }else{
                    Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
                  }
                },
                scope: this
              });   
            }
          }, this);
        break;
    }
  },
  
  onBatchInsert: function() {
    var keys = this.getSelectionModel().selections.keys;
    
    if (keys.length > 0) {    
      var batch = keys.join(',');
      
      Ext.MessageBox.confirm(
        TocLanguage.msgWarningTitle, 
        '<?php echo $osC_Language->get('msgUploadConfirmation'); ?>',
        function(btn) {
          if (btn == 'yes') {
            Ext.Ajax.request({
              url: Toc.CONF.CONN_URL,
              params: {
                module: 'google_base',
                action: 'upload_products',
                batch: batch
              },
              callback: function(options, success, response){
                result = Ext.decode(response.responseText);
                
                if (result.success == true) {
                  this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                
                  this.owner.dlgAvailableProducts.fireEvent('uploadSuccess');
                } else {
                  Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
                  
                  this.owner.dlgAvailableProducts.fireEvent('uploadSuccess');
                }
              },
              scope: this
            });   
          }
        }, 
        this
      );
    } else { 
      Ext.MessageBox.alert(TocLanguage.msgInfoTitle, TocLanguage.msgMustSelectOne);
    }
  }    
});