/*
  $Id: variants.js $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2010 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

var TocVariants = new Class({
  Implements: [Options],
  options: {
    hasSpecial: 0,
    lang: {
      txtInStock: 'In Stock',
      txtOutOfStock: 'Out Of Stock',
      txtNotAvailable: 'Not Available',
      txtTaxText: 'incl. tax'
    }
  },
  
  initialize: function(options) {
    this.setOptions(options);
    this.initializeComboBox();
    this.updateView();
  },
  
  initializeComboBox: function() {
    this.options.combVariants.each(function(combobox) {
      combobox.addEvent('change', function() {
        this.updateView();
      }.bind(this));
    }.bind(this));
  },
  
  getProductsIdString: function() {
    var groups = [];
    this.options.combVariants.each(function(combobox) {
      var id = combobox.id.toString();
      var groups_id = id.substring(9, id.indexOf(']'));
      
      groups.push(groups_id + ':' + combobox.value);
    }.bind(this));
    
    return this.options.productsId + '#' + groups.join(';');
  },
    
  updateView: function(choice) {
    var product = this.options.variants[this.getProductsIdString()];
    
    if (product == undefined || (product['status'] == 0)) {
      $('productInfoAvailable').innerHTML = '<font color="red">' + this.options.lang.txtNotAvailable + '</font>';
      this.disableInfoBox();
    } else {
      if (product['quantity'] > 0) {
        if (this.options.hasSpecial == 0) {
          $('productInfoPrice').set('text', product['display_price'] + ' ' + this.options.lang.txtTaxText);
        }
        $('productInfoSku').set('text', product['sku']);
        if (this.options.displayQty == true) {
          $('productInfoQty').set('text', product['quantity'] + ' ' + this.options.unitClass);
        }
        $('productInfoAvailable').set('text', this.options.lang.txtInStock);
        
        $('shoppingCart').fade('in');
        $('shoppingAction').fade('in');
        
        this.changeImage(product['image']);
      } else {
          $('productInfoAvailable').set('text', this.options.lang.txtOutOfStock);
          $('productInfoQty').set('text', product['quantity'] + ' ' + this.options.unitClass);
          
          //if not allow checkout then disable the info box
          if (!this.options.allowCheckout) {
            this.disableInfoBox();
          }
      }
    }
  },
  
  disableInfoBox: function() {
    $('productInfoPrice').set('text', '--');
    $('productInfoSku').set('text', '--');
    if (this.options.displayQty == true) {
      $('productInfoQty').set('text', '--');
    } 
    $('shoppingCart').fade('out');
    $('shoppingAction').fade('out');
  },
  
  changeImage: function(image) {
    $$('.mini').each(function(link) {
      var href = link.getProperty('href');
      if (href.indexOf(image) > -1) {
        link.fireEvent('mouseover');
      }
    });
  }
});