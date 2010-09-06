<?php
/*
  $Id: articles_dialog.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

Toc.articles.ArticlesDialog = function(config) {
  
  config = config || {};
  
  config.id = 'articles-dialog-win';
  config.title = '<?php echo $osC_Language->get('heading_title_new_article'); ?>';
  config.layout = 'fit';
  config.width = 850;
  config.height = 580;
  config.modal = true;
  config.iconCls = 'icon-articles-win';
  config.items = this.buildForm();
  
  config.buttons = [
    {
      text:TocLanguage.btnSave,
      handler: function(){
        this.submitForm();
      },
      scope:this
    },
    {
      text: TocLanguage.btnClose,
      handler: function(){
        this.close();
      },
      scope:this
    }
  ];

  this.addEvents({'saveSuccess' : true});  
  
  Toc.articles.ArticlesDialog.superclass.constructor.call(this, config);
}

Ext.extend(Toc.articles.ArticlesDialog, Ext.Window, {

  show: function(id, cId) {
    var articlesId = id || null;
    var categoriesId = cId || null;
    
    this.frmArticle.form.reset();  
    this.frmArticle.form.baseParams['articles_id'] = articlesId;
   
    if (articlesId > 0) { 
      this.frmArticle.load({
        url: Toc.CONF.CONN_URL,
        params:{
          action: 'load_article',
          articles_id: articlesId
        },
        success: function(form, action) {
          var img = action.result.data.articles_image;
          
          if (img != null) {
            var img = '../images/articles/thumbnails/' + img;
            var html = '<div style="margin: 26px 0px 0px 20px"><img src="' + img + '" style="border: solid 1px #B5B8C8;" />&nbsp;&nbsp;<input type="checkbox" name="delimage" id="delimage" /><?php echo $osC_Language->get('field_delete'); ?></div>';
            
            this.frmArticle.findById('article_image_url').body.update(html);
          }
          
          Toc.articles.ArticlesDialog.superclass.show.call(this);
        },
        failure: function(form, action) {
          Ext.Msg.alert(TocLanguage.msgErrTitle, TocLanguage.msgErrLoadData);
        }, 
        scope: this
      });
    } else {
      this.cboCategories.getStore().on('load', function() {
        this.cboCategories.setValue(categoriesId);
      }, this);
         
      Toc.articles.ArticlesDialog.superclass.show.call(this);
    }
  },

  getContentPanel: function() {
    this.tabLanguage = new Ext.TabPanel({
      region: 'center',
      activeTab: 0,
      deferredRender: false
    }); 
    
    <?php 
      list($defaultLanguageCode) = split("_", $osC_Language->getCode());
      
      foreach ($osC_Language->getAll() as $l) {
      
        if(USE_WYSIWYG_TINYMCE_EDITOR == 1) {
          $editor = '
            {
              xtype: \'tinymce\',
              fieldLabel: \'' . $osC_Language->get('filed_article_description') . '\',
              name: \'articles_description[' . $l['id'] . ']\',
              height: 250,
              tinymceSettings: {
                theme : "advanced",
                language: "' . $defaultLanguageCode . '", relative_urls: false, remove_script_host: false,
                plugins: "safari,advimage,preview,media,contextmenu,paste,directionality",
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : false,
                extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
                template_external_list_url : "example_template_list.js"
              }
            }';
        } else {
          $editor = '{xtype: \'htmleditor\', fieldLabel: \'' . $osC_Language->get('filed_article_description') . '\', name: \'articles_description[' . $l['id'] . ']\', height: 230}';
        }
      
        echo 'var pnlLang' . $l['code'] . ' = new Ext.Panel({
          labelWidth: 100,
          title:\'' . $l['name'] . '\',
          iconCls: \'icon-' . $l['country_iso'] . '-win\',
          layout: \'form\',
          labelSeparator: \' \',
          style: \'padding: 6px\',
          defaults: {
            anchor: \'97%\'
          },
          items: [
            {xtype: \'textfield\', fieldLabel: \'' . $osC_Language->get('field_article_name') . '\', name: \'articles_name[' . $l['id'] . ']\', allowBlank: false},
            ' . $editor . ',
            {
              layout: \'column\',
              border: false,
              items:[
                {
                  layout: \'form\',
                  border: false,
                  labelSeparator: \' \',
                  columnWidth: .5,
                  items: {xtype: \'textfield\', fieldLabel: \'' . $osC_Language->get('filed_articles_head_desc_tag') . '\', name: \'articles_head_desc_tag[' . $l['id'] . ']\', anchor: \'97%\'}
                },
                {
                  layout: \'form\',
                  border: false,
                  labelSeparator: \' \',
                  columnWidth: .5,
                  items: {xtype: \'textfield\', fieldLabel: \'' . $osC_Language->get('filed_articles_head_keywords_tag') . '\', name: \'articles_head_keywords_tag[' . $l['id'] . ']\', anchor: \'97%\'}
                }
              ]
            }
          ]
        });
        
        this.tabLanguage.add(pnlLang' . $l['code'] . ');
        ';
      }
    ?>
    return this.tabLanguage;
  },
  
  getDataPanel: function() {
    dsCategories = new Ext.data.Store({
      url:Toc.CONF.CONN_URL,
      baseParams: {
        module: 'articles',
        action: 'get_articles_categories'
      },
      reader: new Ext.data.JsonReader({
          fields: ['id', 'text'],
          root: Toc.CONF.JSON_READER_ROOT
      }),
      autoLoad: true
    });

    this.cboCategories = new Ext.form.ComboBox({
      fieldLabel: '<?php echo $osC_Language->get('field_article_category'); ?>', 
      xtype:'combo', 
      store: dsCategories, 
      name: 'articles_categories', 
      hiddenName: 'articles_categories_id', 
      displayField: 'text', 
      valueField: 'id',
      triggerAction: 'all', 
      editable: false,
      forceSelection: true,
      allowBlank: false
    });
  
    this.pnlData = new Ext.Panel({
      layout: 'column',
      region: 'north',
      border: false,
      autoHeight: true,
      style: 'padding: 6px',
      items: [
        {
          layout: 'form',
          border: false,
          labelSeparator: ' ',
          columnWidth: .7,
          autoHeight: true,
          defaults: {
            anchor: '97%'
          },
          items: [
            {
              layout: 'column',
              border: false,
              items: [
                {
                  layout: 'form',
                  border: false,
                  labelSeparator: ' ',
                  width: 200,
                  items: [
                    {
                      fieldLabel: '<?php echo $osC_Language->get('field_publish'); ?>', 
                      xtype:'radio', 
                      name: 'articles_status',
                      inputValue: '1',
                      checked: true,
                      boxLabel: '<?php echo $osC_Language->get('field_publish_yes'); ?>'
                    }
                  ]
                },
                {
                  layout: 'form',
                  border: false,
                  width: 200,
                  items: [
                    {
                      hideLabel: true,
                      xtype:'radio',
                      inputValue: '0', 
                      name: 'articles_status',
                      boxLabel: '<?php echo $osC_Language->get('field_publish_no'); ?>'
                    }
                  ]
                }
              ]
            },
            this.cboCategories,
            {xtype:'numberfield', fieldLabel: '<?php echo $osC_Language->get('field_order'); ?>', name: 'articles_order', id: 'articles_order'},
            {xtype:'fileuploadfield', fieldLabel: '<?php echo $osC_Language->get('field_image'); ?>', name: 'articles_image'}
          ]
        },
        {
          border: false,
          columnWidth: .3,
          items: [
            {xtype: 'panel', name: 'img_url', id: 'article_image_url', border: false}
          ]
        }
      ]
    });
    
    return this.pnlData;
  },
  
  buildForm: function() {
    this.frmArticle = new Ext.form.FormPanel({
      fileUpload: true,
      layout: 'border',
      title:'<?php echo $osC_Language->get('heading_title_data'); ?>',
      url: Toc.CONF.CONN_URL,
      baseParams: {  
        module: 'articles',
        action : 'save_article'
      },
      deferredRender: false,
      items: [this.getContentPanel(), this.getDataPanel()]
    });  
    
    return this.frmArticle;
  },
  
  submitForm : function() {
    this.frmArticle.form.submit({
      waitMsg: TocLanguage.formSubmitWaitMsg,
      success: function(form, action){
        this.fireEvent('saveSuccess', action.result.feedback);
        this.close();
      },    
      failure: function(form, action) {
        if(action.failureType != 'client') {
          Ext.MessageBox.alert(TocLanguage.msgErrTitle, action.result.feedback);
        }
      }, 
      scope: this
    });   
  }
});