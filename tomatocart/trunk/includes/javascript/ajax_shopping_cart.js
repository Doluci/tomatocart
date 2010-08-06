/*
  $Id: ajax_shipping_cart.js $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

var AjaxShoppingCart = new Class({
  Implements: [Options, Events],

  options: {
    sessionName: 'sid',
    sessionId: '',
    jsonUrl: 'json.php',
    redirect: 'checkout.php',
    movedPicSize: 2
  },

  initialize: function(options) {
    this.setOptions(options);

    this.initializeCart();
  },

  initializeCart: function() {
    this.products = [];
    this.attachAddToCartEvent();

    $('ajaxCartCollapse').addEvent('click', function(e) {
      e.stop();

      this.collapse();
    }.bind(this));

    $('ajaxCartExpand').addEvent('click', function(e) {
      e.stop();

      this.expand();
    }.bind(this));


    this.loadCart();
  },

  //attach click event for the add to cart buttons
  attachAddToCartEvent: function() {
    if ( $defined($$('.ajaxAddToCart')) ) {
      $$('.ajaxAddToCart').each(function(addToCartButton) {
        addToCartButton.addEvent('click', function(e) {
          e.stop();

          addToCartButton.set('disabled', 'disabled');

          //send request
          var pID = addToCartButton.get('pid');
          
          var params = {action: 'add_product', pID: pID};
          if ( $defined($('quantity')) ) {
            params.pQty = $('quantity').get('value');  
          }
          
          this.sendRequest(params, function(response) {
            var result = JSON.decode(response);

            //move image
            if (result.success == true) {
              if ( $defined($('defaultProductImage')) ) {
                if ( $defined($('productImages')) ) {
                  var srcImage = $('productImages').getElement('#defaultProductImage');
                }else {
                  var srcImage = $('defaultProductImage').getElement('img.productImage');
                }
              }else if ( $defined($('productImage' + pID)) ) {
                var srcImage = $('productImage' + pID).getElement('img.productImage');
              }

              var srcPos = srcImage.getCoordinates();
              var destPos = $('ajaxCartContent').getParent().getCoordinates();

              var floatImage = srcImage.clone().setStyles({
                'position': 'absolute',
                'width': srcPos.width * this.options.movedPicSize,
                'height': srcPos.height * this.options.movedPicSize,
                'left': srcPos.left,
                'top': srcPos.top
              });

              floatImage.injectAfter(srcImage).setStyles({position: 'absolute'}).set('morph', {
                duration: 700,
                onComplete: function() {
                  floatImage.fade('out');

                  this.updateCart(result.content);

                  (function() {floatImage.destroy()}).delay(1000);

                  addToCartButton.erase('disabled');
                }.bind(this)
              }).morph({width: srcPos.width / 2, height: srcPos.height / 2, top: destPos.top + destPos.height / 4, left: destPos.left + destPos.width / 4});
            }
          });
        }.bind(this));
      }.bind(this));
    }
  },

  collapse: function() {
    if ($('ajaxCartContentLong').hasClass('expanded')) {
      $('ajaxCartContentLong').set('tween', {
        duration: 500,
        property: 'height',
        onComplete: function() {
          $('ajaxCartContentLong').addClass('collapsed').removeClass('expanded');
          $('ajaxCartContentProducts').fade('out');
          $('ajaxCartOrderTotals').fade('out');
          $('ajaxCartButtons').fade('out');

           $('ajaxCartContentShort').set('slide', {
            onComplete: function() {
              $('ajaxCartContentShort').addClass('expanded').removeClass('collapsed').slide('in');
            }.bind(this)
          }).slide('in').fade('in');
        }.bind(this)
      }).tween(this.cartHeight, 0);

      $('ajaxCartCollapse').set('tween' , {
        duration: 500,
        property: 'opacity',
        onComplete: function() {
          $('ajaxCartCollapse').addClass('collapsed');

          $('ajaxCartExpand').removeClass('hidden'). setStyle('opacity', 0).set('tween', {
            duration: 1000,
            property: 'opacity'
          }).tween(0,100);
        }
      }).tween(100, 0);
    }
  },

  expand: function() {
    if ($('ajaxCartContentLong').hasClass('collapsed')) {
      $('ajaxCartContentShort').set('slide', {
        duration: 600,
        onComplete: function() {
          $('ajaxCartContentShort').addClass('collapsed').removeClass('expanded');

          $('ajaxCartContentLong').removeClass('collapsed').addClass('expanded');
          $('ajaxCartContentLong').set('tween', {
            duration: 500,
            property: 'height',
            onComplete: function() {
              $('ajaxCartContentProducts').fade('in');
              $('ajaxCartOrderTotals').fade('in');
              $('ajaxCartButtons').fade('in');
              $('ajaxCartContentLong').setStyle('height', 'auto');
            }
          }).tween(0, this.cartHeight);
        }.bind(this)
      }).slide('out');

      $('ajaxCartExpand').set('tween', {
        duration: 800,
        property: 'opacity',
        onComplete: function() {
          $('ajaxCartExpand').addClass('hidden');

          $('ajaxCartCollapse').removeClass('collapsed').setStyle('opacity', 0).set('tween', {
            duration: 10000,
            property: 'opacity'
          }).tween(0, 100);
        }
      }).tween(100, 0);
    }
  },

  loadCart: function() {
    this.sendRequest({action: 'load_cart'}, function(response) {
      var json = JSON.decode(response);

      this.updateCart(json);
    });
  },

  updateCart: function(json) {
    //popup shopping cart view
    $('popupCartItems').set('text', json.numberOfItems);
    
    //shopping cart short view
    $('ajaxCartContentShort').getElement('.quantity').set('html', json.numberOfItems);
    $('ajaxCartContentShort').getElement('.cartTotal').set('html', json.total);

    //shopping cart long view
    this.updateProductsContent(json);
    this.updateOrderTotals(json);

    this.cartHeight = $('ajaxCartContentLong').getSize().y;
  },

  //if the product has been removed, We must delete the product from the shopping cart
  removeProducts: function(json) {
    if (this.products.length > 0) {
      //get all the products to be removed
      var products = [];

      this.products.each(function(id) {
        var found = false;
        if ($defined(json.products)) {
          json.products.each(function(product) {
            if (product.id == id) {
              found = true;
            }
          });
        }

        if (!found) {products.push(id);}
      });

      //play animation to remove products
      if (products.length > 0) {
        products.each(function(pID, index) {
          $('ajaxCartProduct' + pID).addClass('strike').set('tween', {
            duration: 1000,
            property: 'opacity',
            onComplete: function() {
              $('ajaxCartProduct' + pID).destroy();
              this.products.erase(pID);

              if (this.products.length == 0) {
                $('ajaxCartContentNoProducts').removeClass('collapsed').addClass('expanded').slide('in');
                $('ajaxCartContentProducts').removeClass('expanded').addClass('collapsed');
              }
            }.bind(this)
          }).tween(100, 0);
        }.bind(this));
      }
    }
  },

  //update Products Content
  updateProductsContent: function(json) {
    //remove products
    if ($defined(json.products)) {
     this.removeProducts(json);
       
      if (json.products.length > 0 ) {
        $('ajaxCartContentNoProducts').removeClass('expanded').addClass('collapsed').slide('out');
        
        //add products
        json.products.each(function(product) {
          if ( this.products.indexOf(product.id) == -1 ) {
            this.products.push(product.id);
  
            var rowEl = new Element('li', {'id': 'ajaxCartProduct' + product.id});
            var quantityEl = new Element('span', {'class': 'quantity', 'html': product.quantity});
            var productEl = new Element('a', {'href': product.link, 'title': product.title, 'html': product.name});
            var priceEl = new Element('span', {'class': 'price', 'html': product.price});
            var deleteEl = new Element('span', {'class': 'removeProduct'});
  
            $('ajaxCartContentProducts').grab(rowEl.grab(quantityEl).grab(productEl).grab(priceEl).grab(deleteEl));
  
            //delete product
            deleteEl.addEvent('click', function(e) {
              e.stop();
  
              this.sendRequest({action: 'remove_product', pID: product.id}, function(response) {
                var result = JSON.decode(response);
  
                if (result.success == true) {
                  this.loadCart();
                }
              });
            }.bind(this));
  
            $('ajaxCartContentProducts').removeClass('collapsed');
          } else {
             $('ajaxCartProduct' + product.id ).getElement('.price').set('text', product.price);
             $('ajaxCartProduct' + product.id).getElement('.quantity').set('html', product.quantity);
          }
        }.bind(this));
      }else {
        $('ajaxCartContentNoProducts').removeClass('collapsed').addClass('expanded').slide('in');
      }
    }
  },

  updateOrderTotals: function(json) {
    if ( $defined($('ajaxCartOrderTotals')) ) {
      $('ajaxCartOrderTotals').destroy();
    }

    if ($type(json.orderTotals) == 'array') {
      var orderTotalsEl = new Element('ul', {'id': 'ajaxCartOrderTotals'});

      var html = '';
      json.orderTotals.each(function(orderTotal) {
        html += '<li><span class="orderTotalText">' + orderTotal.text + '</span><span>' + orderTotal.title + '</span></li>'
      });

      orderTotalsEl.set('html', html);
      orderTotalsEl.inject($('ajaxCartButtons'), 'before');
    }
  },

  sendRequest: function(data, fnSuccess) {
    data.module = 'ajax_shopping_cart';
    data[this.options.sessionName] = this.options.sessionId;

    new Request({
      url: this.options.jsonUrl,
      method: 'post',
      data: data,
      onSuccess: fnSuccess.bind(this)
    }).send();
  }
});