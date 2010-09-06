<?php
/*
  $Id: reorder_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

?>

Toc.orders.ReorderDialog = function(config) {

  config = config || {};
  
  config.id = 'reorder-dialog-win';
  config.title = '<?php echo $osC_Language->get('heading_title'); ?>';
  config.width = 700;
  config.height = 520;
  config.layout = 'fit';
  config.modal = true;
  config.iconCls = 'icon-orders-win';
  config.items = this.buildForm(config.ordersId);
    
  config.buttons = [
    {
      text: TocLanguage.btnClose,
      handler: function() { 
        this.close();
      },
      scope: this
    }
  ];
  
  Toc.orders.ReorderDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.orders.ReorderDialog, Ext.Window, {
  buildForm: function(ordersId) {
	  var store = new Ext.data.Store({
	    url: Toc.CONF.CONN_URL,
	    baseParams: {
	      module: 'orders',
	      action: 'list_customers'        
	    },
	    reader: new Ext.data.JsonReader({
	      root: Toc.CONF.JSON_READER_ROOT,
	      totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
	      id: 'customers_id'
	    },[
	      'customers_id',
	      'customers_lastname',
	      'customers_firstname',
	      'customers_credits',
	      'date_account_created',
	      'customers_status',
	      'customers_email_address',
	      'customers_info'
	    ]),
	    autoLoad: true
	  });
	 
	  var rowActions = new Ext.ux.grid.RowActions({
      actions: [
        {iconCls: 'icon-add-record', qtip: TocLanguage.tipAdd},
      ],
      widthIntercept: Ext.isSafari ? 4 : 1
    });
    
    this.grdReorder = new Ext.grid.GridPanel({
      store: store,
      border: false,
      viewConfig: {emptyText: TocLanguage.gridNoRecords},
      columns:[
        new Ext.grid.CheckboxSelectionModel(),
        {id: 'customers_id', header: '<?php echo $osC_Language->get('table_heading_first_name'); ?>', dataIndex: 'customers_firstname'},
        {header: '<?php echo $osC_Language->get('table_heading_last_name'); ?>', dataIndex: 'customers_lastname'},
        {header: '<?php echo $osC_Language->get('table_heading_email'); ?>', dataIndex: 'customers_email_address'},
        {header: '<?php echo $osC_Language->get('table_heading_credit'); ?>', dataIndex: 'customers_credits'},
        rowActions
      ],
      tbar: [
		    '->',
		    this.search = new Ext.form.TextField({width: 150}),
		    { 
		      text: '',
		      iconCls: 'search',
		      handler: this.onSearch,
		      scope: this
		    }
		  ],
      bbar: new Ext.PageToolbar({
		    pageSize: Toc.CONF.GRID_PAGE_SIZE,
		    store: store,
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
		  }),
      autoExpandColumn: 'customers_id',
      plugins: rowActions
    });
    
    rowActions.on('action', this.onRowAction, this);  
      
    return this.grdReorder;	  
  },
  
  onAddReorder: function(record) {
    this.el.mask(TocLanguage.loadingText, 'x-mask-loading');
    
    Ext.Ajax.request({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      url: Toc.CONF.CONN_URL,
      params: {
        module: 'orders',
        action: 'create_order',
        customers_id: record.get('customers_id'),
        customers_firstname: record.get('customers_firstname'),
        customers_lastname: record.get('customers_lastname'),
        customers_email_address: record.get('customers_email_address'),
        customers_gender: record.get('customers_gender')
      },
      callback: function (options, success, response) {
        this.el.unmask();
        
        var result = Ext.decode(response.responseText);
        if (result.success == true) {
          this.onReorder(result, record.get('customers_firstname'));       
          this.fireEvent('saveSuccess', result.orders_id, record.get('customers_firstname') + ', ' + record.get('customers_lastname'));
          this.close();
        } else {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, response.responseText);
        }
      },
      scope: this
    });
  },
  
  onReorder: function(config, firstname) {
    this.el.mask(TocLanguage.loadingText, 'x-mask-loading');

    Ext.Ajax.request({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      url: Toc.CONF.CONN_URL,
      params: {
        module: 'orders',
        action: 'create_reorder',
        orders_id: config.orders_id,
        reorders_Id: this.ordersId
      },
      callback: function (options, success, response) {
        this.el.unmask();
        
        var result = Ext.decode(response.responseText);
        if (result.success == true) {
          var dlg = this.owner.createOrdersEditDialog({ordersId: config.orders_id, outStockProduct: result.out_stock_product});
          
          dlg.setTitle(config.orders_id + ': ' + firstname);
          
          dlg.on('saveSuccess', function() {
            this.onRefresh();
          }, this);
          
          dlg.show();                              
        } else {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, response.responseText);
        }
      },
      scope: this
    });    
  },
  
  onSearch: function() {
    this.grdReorder.store.baseParams['filter'] = this.search.getValue() || null;
    this.grdReorder.store.load();
  },
    
  onRowAction: function(grid, record, action, row, col) {
    switch(action) {
      case 'icon-add-record':
        this.onAddReorder(record);
        break;
    }
  }
});