<?php
/*
  $Id: visit_location.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $date = toC_Piwik::getWebsiteDateCreated();
  $year = substr($date, 0, 4);
  $month = (int)substr($date, 5, 2) - 1;
  $day = (int)substr($date, 8, 2);
?>

Toc.reports_web.VisitLocationPanel = function(config) {
  config = config || {};

  config.width = 800;
  config.height = 350;
  config.modal = true;
  config.layout = 'column';
  config.border = false;
  
  var today = new Date();
  var start_date = today.add(Date.MONTH, -1).add(Date.DAY, -1);
  var date_created = new Date(<?php echo $year; ?>, <?php echo $month; ?>, <?php echo $day; ?>, 0, 0, 0);
  
  if (start_date < date_created) {
    start_date = date_created;
  }
  
  this.dtStart = new Ext.form.DateField({format: 'Y-m-d', readOnly: true, value: start_date, minValue: date_created, maxValue: today});
  this.dtEnd = new Ext.form.DateField({format: 'Y-m-d', readOnly: true, value: today, minValue: date_created, maxValue: today});
  
  config.items = this.buildPanel();
  config.tbar = [
    '->',
    '<?php echo $osC_Language->get('field_start_date'); ?>',
    this.dtStart,
    '-', 
    '<?php echo $osC_Language->get('field_end_date'); ?>',
    this.dtEnd,
    '-', 
    {
      iconCls: 'search',
      handler: this.onSearch,
      scope: this
    }
  ];
  
  Toc.reports_web.VisitLocationPanel.superclass.constructor.call(this, config);
};

Ext.extend(Toc.reports_web.VisitLocationPanel, Ext.Panel, {
  buildPanel: function() {
    this.grdCountries = new Ext.grid.GridPanel({
      loadMask: true,
      border: true,
      height: 300,
      style: 'margin: 10px;',
      ds: new Ext.data.Store({
        url: Toc.CONF.CONN_URL,
        baseParams: {
          module: 'reports_web', 
          action: 'get_country_data'
        },
        reader: new Ext.data.JsonReader({
          root: Toc.CONF.JSON_READER_ROOT,
          totalProperty: Toc.CONF.JSON_READER_TOTAL_PROPERTY,
          id: 'country'
         },
         ['country','visitors']
        ),
        autoLoad: false
      }),
      cm: new Ext.grid.ColumnModel([
        {id: 'country',header: '<?php echo $osC_Language->get('table_heading_country'); ?>',dataIndex: 'country'},
        {header: '',dataIndex: 'visitors',align: 'center',width: 60}
      ]),
      autoExpandColumn: 'country'
    });         
      
    return [
      {
        columnWidth: .49,
        border: false,
        items: [
          this.pnlPieFlash = new Ext.Panel({
            title: '<?php echo $osC_Language->get('flash_chart_heading_continent_title'); ?>',
            border: true,
            height: 300,
            style: 'margin: 10px;',
            swf: 'external/open-flash-chart/open-flash-chart.swf',
            flashvars: {},
            plugins: new Ext.ux.FlashPlugin()
          })
        ]
      },      
      {
        columnWidth: .49,
        border: false,
        items: [
          this.grdCountries
        ]
      }
    ];
  },
 
  onSearch: function() {
    if (this.dtStart.getValue() > this.dtEnd.getValue()) {
      alert('<?php echo $osC_Language->get('ms_error_end_date_smaller_than_start_date'); ?>');
      return;
    }
    
    var start_date = this.dtStart.getValue().format('Y-m-d');
    var end_date = this.dtEnd.getValue().format('Y-m-d');
    
    this.grdCountries.store.baseParams['start_date'] = start_date;
    this.grdCountries.store.baseParams['end_date'] = end_date;
    this.grdCountries.store.reload();
    
    this.pnlPieFlash.flashvars.data = Toc.CONF.CONN_URL + '?module=reports_web&action=render_continent_pie_chart_data&start_date=' + start_date + '&end_date=' + end_date;     
    this.pnlPieFlash.renderFlash();
  }
});