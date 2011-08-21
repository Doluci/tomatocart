/*
  $Id: popup_cart.js $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

var PopupCart = new Class({
  Implements: [Options],
  options: {
    remoteUrl: 'json.php',
    sessionName: 'sid',
    sessionId: null,
    isCartExpanded: false,
    triggerEl: $('popupCart'),
    container: $('pageHeader'),
    relativeTop: 20,
    relativeLeft: 242,
    movedPicSize: 2
  },
  
  
  initialize: function(options) {
    this.setOptions(options);
    this.registerEvents();
  },
  
  registerEvents: function() {
    this.options.triggerEl.addEvents({
      'mouseover': function(e) {
        e.stop();
        
        if (this.options.isCartExpanded == false) {
          this.getShoppingCart();
          clearTimeout(this.timer);
        }
      }.bind(this),
      'mouseleave': function(e) {
        if (this.options.isCartExpanded == true && $defined(this.cartContainer)) {
           e.stop();
        
          this.timer = function() {
            this.cartContainer.fade('out');
          }.bind(this).delay(1000);
          
          this.options.isCartExpanded = false;
        }
      }.bind(this)
    });
    
    this.attachAddToCartEvent();
  },
  
  attachAddToCartEvent: function() {
  	//if the ajax shopping cart box have been closed
  	if ( !$defined($('ajaxCartContent')) ) {
    	if ( $defined($$('.ajaxAddToCart')) ) {
	    	$$('.ajaxAddToCart').each(function(addToCartButton) {
	      	addToCartButton.addEvent('click', function(e) {
	        	e.stop();
	        	
	        	var btnId = addToCartButton.get('id');

	        	if (btnId.test("^ac_[a-z]+_[0-9]+$", "i")) {
	        		var pID = btnId.split('_').getLast();
	        	}
	        	
	        	//obtain the params about the product which is being added to the cart
	        	var params = this.obtainParams(addToCartButton, pID);
	        
	        	this.sendRequest(params, function(response) {
	          	var result = JSON.decode(response);
	          	//  this.clearCustomizationForm();
	
	          	//move image
	          	if (result.success == true) {
	            	if ( $defined($('defaultProductImage')) ) {
	            
	              	//in the product info page, copy the product image and move it
	              	var productLink = $('productImages').getElement('#defaultProductImage');
	              	var productImg = $('defaultProductImage').getElement('img.productImage');
	              	var cloneProductImg = productImg.clone();
	              	var srcPos = productLink.getCoordinates();
	              
	              	cloneProductImg.injectAfter(productLink).setStyles({
	                	'position': 'absolute',
	                	'left': productImg.getCoordinates().left,
	                	'top': productImg.getCoordinates().top-5
	              	});
	              
	              	var srcImage = cloneProductImg;
	            	}else if ( $defined($('productImage' + pID)) ) {
	              	var srcImage = $('productImage' + pID).getElement('img.productImage');
	              	var srcPos = srcImage.getCoordinates();
	            	}
	            
	            	var destPos = $('popupCart').getCoordinates();
	            
	            	var floatImage = srcImage.clone().setStyles({
	              	'position': 'absolute',
	              	'width': srcPos.width * this.options.movedPicSize,
	              	'height': srcPos.height * this.options.movedPicSize,
	              	'left': srcPos.left,
	              	'top': srcPos.top,
	              	'border': 0
	            	});
	            
	            	floatImage.injectAfter(srcImage).setStyles({position: 'absolute'}).set('morph', {
	              	duration: 700,
	              	onComplete: function() {
	                	floatImage.fade('out');
	                
	                	$('popupCartItems').set('html', result.cart_items);
	                	
	                	//if want to display the cart automatically as the product have beed added, just enable two lines below
	                  //this.displayCart(response);
	                
                    //(function() {if ($defined($('popupCartContent'))) {$('popupCartContent').fade('out')}}).delay(2000);
	                
	                	(function() {floatImage.destroy()}).delay(1000);
	
	                	addToCartButton.erase('disabled');
	                
	                	if ($defined(cloneProductImg)) {
	                  	cloneProductImg.destroy();
	                	}
	              	}.bind(this)
	            	}).morph({width: srcPos.width / 2, height: srcPos.height / 2, top: destPos.top + destPos.height / 4 - 10, left: destPos.left + destPos.width / 4});
	          	} else {
	            	if ($defined(result.feedback)) {
	              	alert(result.feedback);
	            	}
	            
	            	addToCartButton.erase('disabled');
	          	}
	        	});
	      	}.bind(this));
	    	}.bind(this));
    	}
  	}
  },
  
  obtainParams: function(addToCartButton, pID) {
		addToCartButton.set('disabled', 'disabled');

		var errors = [];

		var params = {action: 'add_product', pID: pID};
		if ( $defined($('quantity')) ) {
			params.pQty = $('quantity').get('value');  
		}

		//variants
		var selects = $$('tr.variantCombobox select');
		if ($defined(selects)) {
			var variants = '';

			selects.each(function(select) {
  			var id = select.id.toString();
  			var groups_id = id.substring(9, id.indexOf(']'));
  
  			variants += groups_id + ':' + select.value + ';';
			}.bind(this));

			params.variants = variants; 
		}

		//gift certificate
		if ($defined($('senders_name')) && $('senders_name').value != '') {
			params.senders_name = $('senders_name').value;
		} else if ($defined($('senders_name')) && $('senders_name').value == '') {
			errors.push(this.options.error_sender_name_empty);
		}

		if ($defined($('senders_email')) && $('senders_email').value != '') {
			params.senders_email = $('senders_email').value;
		} else if ($defined($('senders_email')) && $('senders_email').value == '') {
			errors.push(this.options.error_sender_email_empty);
		}

		if ($defined($('recipients_name')) && $('recipients_name').value != '') {
			params.recipients_name = $('recipients_name').value;
		} else if ($defined($('recipients_name')) && $('recipients_name').value == '') {
			errors.push(this.options.error_recipient_name_empty);
		}

		if ($defined($('recipients_email')) && $('recipients_email').value != '') {
			params.recipients_email = $('recipients_email').value;
		} else if ($defined($('recipients_email')) && $('recipients_email').value == '') {
			errors.push(this.options.error_recipient_email_empty);
		}
  
		if ($defined($('message')) && $('message').value != '') {
			params.message = $('message').value;
		} else if ($defined($('message')) && $('message').value == '') {
			errors.push(this.options.error_message_empty);
		}
  
		if ($defined($('gift_certificate_amount')) && $('gift_certificate_amount').value != '') {
			params.gift_certificate_amount = $('gift_certificate_amount').value;
		} else if ($defined($('gift_certificate_amount')) && $('gift_certificate_amount').value == '') {
			errors.push(this.options.error_message_open_gift_certificate_amount);
		}

		if (errors.length > 0) {
			alert(errors.join('\n'));
			addToCartButton.erase('disabled');
		
			return;
		}
	
		return params;
  },
  
  getShoppingCart: function() {
    var data = {
      action: 'get_cart_contents'
    };
    
    this.sendRequest(data, function(response) {this.displayCart(response);});
  },
  
  displayCart: function(response) {
    var result = JSON.decode(response);

    if (result.success == true) {
      if (!$defined(this.cartContainer)) {
        var pos = this.options.triggerEl.getCoordinates();
        
        this.cartContainer = new Element('div', {
          'html': result.content,
          'id': 'popupCartContent',
          'class': 'moduleBox',
          'styles': {
            'position': 'absolute',
            'top': pos.top + this.options.relativeTop,
            'left': pos.left - this.options.relativeLeft    
          }
        });
      } else {
        this.cartContainer.set('html', result.content);
      }
      
      this.options.container.adopt(this.cartContainer);
      
      this.cartContainer.setStyle('opacity', 0).fade('in').addEvents({
        'mouseleave': function(e) {
          e.stop();
        
          this.timer = function() {
            this.cartContainer.fade('out');
          }.bind(this).delay(1000);
          
          this.options.isCartExpanded = false;
        }.bind(this),
        'mouseover': function(e) {
          e.stop();
          
          clearTimeout(this.timer);
          this.cartContainer.fade('in');
          this.options.isCartExpanded = true;
        }.bind(this)
      });
      
      this.options.isCartExpanded = true;
    }
  },
  
  sendRequest: function(data, fnSuccess) {
    data.module = 'popup_cart';
    data.template = this.options.template;
    data[this.options.sessionName] = this.options.sessionId;

    new Request({
      url: this.options.remoteUrl,
      method: 'post',
      data: data,
      onSuccess: fnSuccess.bind(this)
    }).send();
  }
});