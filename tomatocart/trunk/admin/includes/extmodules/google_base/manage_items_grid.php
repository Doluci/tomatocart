<?php
/*
  $Id: manage_items_grid.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.google_base.ManageItemsGrid = function(config) {

  config = config || {};
  
  config.ds = new Ext.data.Store({
    url: Toc.CONF.CONN_URL,
    baseParams: {
      module: 'google_base',
      action: 'get_manage_items'
    },
    reader: new Ext.data.JsonReader({
      root: Toc.CONF.JSON_READER_ROOT,
      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
      id: 'google_base_id'
    }, [
       'google_base_id',
       'items_name',
       'items_target_country',
       'items_edit_link',
       'items_expires',
       'impressions',
       'clicks'
    ]),
    autoLoad: true
  });
  
  config.rowActions = new Ext.ux.grid.RowActions({
    actions:[{iconCls: 'icon-delete-record', qtip: TocLanguage.tipDelete}],
    widthIntercept: Ext.isSafari ? 4 : 2
  });
  config.rowActions.on('action', this.onRowAction, this);    
  config.plugins = config.rowActions;
  
  config.sm = new Ext.grid.CheckboxSelectionModel();
  config.cm = new Ext.grid.ColumnModel([
    config.sm,
    {id:'google_base_product_name', header: '<?php echo $osC_Language->get('table_heading_google_base_product_name'); ?>', dataIndex: 'items_name'},
    {header: '<?php echo $osC_Language->get('table_heading_google_base_id'); ?>', dataIndex: 'google_base_id', align: 'center'},
    {header: '<?php echo $osC_Language->get('table_heading_items_target_country'); ?>', dataIndex: 'items_target_country', align: 'center', width:150},
    {header: '<?php echo $osC_Language->get('table_heading_items_expires'); ?>', dataIndex: 'items_expires', align: 'center'},
    {header: '<?php echo $osC_Language->get('table_heading_impressions'); ?>', dataIndex: 'impressions', align: 'center'},
    {header: '<?php echo $osC_Language->get('table_heading_google_base_item_clicks'); ?>', dataIndex: 'clicks', align: 'center'},
    config.rowActions
  ]);
  config.autoExpandColumn = 'google_base_product_name';
  
  config.tbar = [
//     {
//      text: '<?php echo $osC_Language->get('button_synchronous'); ?>',
//      iconCls : 'add',
//      handler: this.onSynchronous,
//      scope: this
//    },
//    '-',
    {
      text: TocLanguage.btnAdd,
      iconCls : 'add',
      handler: this.onShowAvailableProducts,
      scope: this
    },
    '-',
    {
      text: TocLanguage.btnDelete,
      iconCls: 'remove',
      handler: this.onBatchDelete,
      scope: this
    },
    '-',
    {
      text: TocLanguage.btnRefresh,
      iconCls: 'refresh',
      handler: this.onRefresh,
      scope: this
    }
  ];
  
  Toc.google_base.ManageItemsGrid.superclass.constructor.call(this, config);
};

Ext.extend(Toc.google_base.ManageItemsGrid, Ext.grid.GridPanel, {
  onShowAvailableProducts: function() {
    var dlg = this.owner.createAvailableProductsDialog();
    
    dlg.on('uploadSuccess', function() {this.onRefresh();}, this);
    
    dlg.show();
  },
  
  onRowAction: function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-delete-record':
        this.onDelete(record);
        break; 
    }
  },
  
  onDelete: function(record) {
    var itemId = record.get('google_base_id');
    
    Ext.MessageBox.confirm(
      TocLanguage.msgWarningTitle, 
      TocLanguage.msgDeleteConfirm,
      function(btn) {
        if ( btn == 'yes' ) {
          Ext.Ajax.request({
            url: Toc.CONF.CONN_URL,
            params: {
              module: 'google_base',
              action: 'delete_product',
              itemId: itemId
            }, 
            callback: function(options, success, response) {
              result = Ext.decode(response.responseText);
              
              if (result.success == true) {
                this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                
                this.onRefresh();
              } else {
                Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
              }
            },
            scope: this
          });   
        }
      }, 
      this
    );
  },
  
  onBatchDelete: function() {
    var keys = this.getSelectionModel().selections.keys;
    
    if (keys.length > 0) {    
      var batch = keys.join(',');
      
      Ext.MessageBox.confirm(
        TocLanguage.msgWarningTitle, 
        TocLanguage.msgDeleteConfirm,
        function(btn) {
          if (btn == 'yes') {
            Ext.Ajax.request({
              url: Toc.CONF.CONN_URL,
              params: {
                module: 'google_base',
                action: 'delete_products',
                batch: batch
              },
              callback: function(options, success, response){
                result = Ext.decode(response.responseText);
                
                if (result.success == true) {
                  this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
                
                  this.onRefresh();
                } else {
                  Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
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
  },
    
//  onSynchronous: function() {
//    Ext.MessageBox.confirm(
//      TocLanguage.msgWarningTitle, 
//      TocLanguage.msgDeleteConfirm,
//      function(btn) {
//        if ( btn == 'yes' ) {
//          Ext.Ajax.request({
//            url: Toc.CONF.CONN_URL,
//            params: {
//              module: 'google_base',
//              action: 'synchronous'
//            }, 
//            callback: function(options, success, response) {
//              result = Ext.decode(response.responseText);
//              
//              if (result.success == true) {
//                this.owner.app.showNotification({title: TocLanguage.msgSuccessTitle, html: result.feedback});
//                
//                this.onRefresh();
//              } else {
//                Ext.MessageBox.alert(TocLanguage.msgErrTitle, result.feedback);
//              }
//            },
//            scope: this
//          });   
//        }
//      }, 
//      this
//    );
//  },
  
  onRefresh: function() {
    this.getStore().reload();
  }
});