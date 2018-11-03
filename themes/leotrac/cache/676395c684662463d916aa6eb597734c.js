if(typeof baseUri==="undefined"&&typeof baseDir!=="undefined")
baseUri=baseDir;var ajaxCart={nb_total_products:0,overrideButtonsInThePage:function(){$('.ajax_add_to_cart_button').unbind('click').click(function(){var idProduct=$(this).attr('rel').replace('nofollow','').replace('ajax_id_product_','');if($(this).attr('disabled')!='disabled')
ajaxCart.add(idProduct,null,false,this);return false;});$('#add_to_cart input').unbind('click').click(function(){ajaxCart.add($('#product_page_product_id').val(),$('#idCombination').val(),true,null,$('#quantity_wanted').val(),null);return false;});$('#cart_block_list .ajax_cart_block_remove_link').unbind('click').click(function(){var customizationId=0;var productId=0;var productAttributeId=0;var customizableProductDiv=$($(this).parent().parent()).find("div[id^=deleteCustomizableProduct_]");if(customizableProductDiv&&$(customizableProductDiv).length)
{$(customizableProductDiv).each(function(){var ids=$(this).attr('id').split('_');if(typeof(ids[1])!='undefined')
{customizationId=parseInt(ids[1]);productId=parseInt(ids[2]);if(typeof(ids[3])!='undefined')
productAttributeId=parseInt(ids[3]);return false;}});}
if(!customizationId)
{var firstCut=$(this).parent().parent().attr('id').replace('cart_block_product_','');firstCut=firstCut.replace('deleteCustomizableProduct_','');ids=firstCut.split('_');productId=parseInt(ids[0]);if(typeof(ids[1])!='undefined')
productAttributeId=parseInt(ids[1]);}
var idAddressDelivery=$(this).parent().parent().attr('id').match(/.*_\d+_\d+_(\d+)/)[1];ajaxCart.remove(productId,productAttributeId,customizationId,idAddressDelivery);return false;});},expand:function(){if($('#cart_block_list').hasClass('collapsed'))
{$('#cart_block_summary').slideUp(200,function(){$(this).addClass('collapsed').removeClass('expanded');$('#cart_block_list').slideDown({duration:450,complete:function(){$(this).addClass('expanded').removeClass('collapsed');}});});$('#block_cart_expand').fadeOut('slow',function(){$('#block_cart_collapse').fadeIn('fast');});$.ajax({type:'POST',headers:{"cache-control":"no-cache"},url:baseDir+'modules/blockcart/blockcart-set-collapse.php'+'?rand='+new Date().getTime(),async:true,cache:false,data:'ajax_blockcart_display=expand'});}},refresh:function(){$.ajax({type:'POST',headers:{"cache-control":"no-cache"},url:baseUri+'?rand='+new Date().getTime(),async:true,cache:false,dataType:"json",data:'controller=cart&ajax=true&token='+static_token,success:function(jsonData)
{ajaxCart.updateCart(jsonData);},error:function(XMLHttpRequest,textStatus,errorThrown){alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: "+XMLHttpRequest+"\n"+'Text status: '+textStatus);}});},collapse:function(){if($('#cart_block_list').hasClass('expanded'))
{$('#cart_block_list').slideUp('slow',function(){$(this).addClass('collapsed').removeClass('expanded');$('#cart_block_summary').slideDown(450,function(){$(this).addClass('expanded').removeClass('collapsed');});});$('#block_cart_collapse').fadeOut('slow',function(){$('#block_cart_expand').fadeIn('fast');});$.ajax({type:'POST',headers:{"cache-control":"no-cache"},url:baseDir+'modules/blockcart/blockcart-set-collapse.php'+'?rand='+new Date().getTime(),async:true,cache:false,data:'ajax_blockcart_display=collapse'+'&rand='+new Date().getTime()});}},updateCartInformation:function(jsonData,addedFromProductPage)
{ajaxCart.updateCart(jsonData);if(addedFromProductPage)
$('#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');else
$('.ajax_add_to_cart_button').removeAttr('disabled');},add:function(idProduct,idCombination,addedFromProductPage,callerElement,quantity,wishlist){if(addedFromProductPage&&!checkCustomizations())
{alert(fieldRequired);return;}
emptyCustomizations();if(addedFromProductPage)
{$('#add_to_cart input').attr('disabled',true).removeClass('exclusive').addClass('exclusive_disabled');$('.filled').removeClass('filled');}
else
$(callerElement).attr('disabled',true);if($('#cart_block_list').hasClass('collapsed'))
this.expand();$.ajax({type:'POST',headers:{"cache-control":"no-cache"},url:baseUri+'?rand='+new Date().getTime(),async:true,cache:false,dataType:"json",data:'controller=cart&add=1&ajax=true&qty='+((quantity&&quantity!=null)?quantity:'1')+'&id_product='+idProduct+'&token='+static_token+((parseInt(idCombination)&&idCombination!=null)?'&ipa='+parseInt(idCombination):''),success:function(jsonData,textStatus,jqXHR)
{if(wishlist&&!jsonData.errors)
WishlistAddProductCart(wishlist[0],idProduct,idCombination,wishlist[1]);var $element=$(callerElement).parent().parent().find('a.product_image img,a.product_img_link img');if(!$element.length)
$element=$('#bigpic');var $picture=$element.clone();var pictureOffsetOriginal=$element.offset();pictureOffsetOriginal.right=$(window).innerWidth()-pictureOffsetOriginal.left-$element.width();if($picture.length)
{$picture.css({position:'absolute',top:pictureOffsetOriginal.top,right:pictureOffsetOriginal.right});}
var pictureOffset=$picture.offset();var cartBlock=$('#cart_block');if(!$('#cart_block')[0]||!$('#cart_block').offset().top||!$('#cart_block').offset().left)
cartBlock=$('#shopping_cart');var cartBlockOffset=cartBlock.offset();cartBlockOffset.right=$(window).innerWidth()-cartBlockOffset.left-cartBlock.width();if(cartBlockOffset!=undefined&&$picture.length)
{$picture.appendTo('body');$picture.css({position:'absolute',top:pictureOffsetOriginal.top,right:pictureOffsetOriginal.right,zIndex:4242}).animate({width:$element.attr('width')*0.66,height:$element.attr('height')*0.66,opacity:0.2,top:cartBlockOffset.top+30,right:cartBlockOffset.right+15},1000).fadeOut(100,function(){ajaxCart.updateCartInformation(jsonData,addedFromProductPage);$(this).remove();});}
else
ajaxCart.updateCartInformation(jsonData,addedFromProductPage);},error:function(XMLHttpRequest,textStatus,errorThrown)
{alert("Impossible to add the product to the cart.\n\ntextStatus: '"+textStatus+"'\nerrorThrown: '"+errorThrown+"'\nresponseText:\n"+XMLHttpRequest.responseText);if(addedFromProductPage)
$('#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');else
$(callerElement).removeAttr('disabled');}});},remove:function(idProduct,idCombination,customizationId,idAddressDelivery){$.ajax({type:'POST',headers:{"cache-control":"no-cache"},url:baseUri+'?rand='+new Date().getTime(),async:true,cache:false,dataType:"json",data:'controller=cart&delete=1&id_product='+idProduct+'&ipa='+((idCombination!=null&&parseInt(idCombination))?idCombination:'')+((customizationId&&customizationId!=null)?'&id_customization='+customizationId:'')+'&id_address_delivery='+idAddressDelivery+'&token='+static_token+'&ajax=true',success:function(jsonData){ajaxCart.updateCart(jsonData);if($('body').attr('id')=='order'||$('body').attr('id')=='order-opc')
deleteProductFromSummary(idProduct+'_'+idCombination+'_'+customizationId+'_'+idAddressDelivery);},error:function(){alert('ERROR: unable to delete the product');}});},hideOldProducts:function(jsonData){if($('#cart_block_list dl.products').length>0)
{var removedProductId=null;var removedProductData=null;var removedProductDomId=null;$('#cart_block_list dl.products dt').each(function(){var domIdProduct=$(this).attr('id');var firstCut=domIdProduct.replace('cart_block_product_','');var ids=firstCut.split('_');var stayInTheCart=false;for(aProduct in jsonData.products)
{if(jsonData.products[aProduct]['id']==ids[0]&&(!ids[1]||jsonData.products[aProduct]['idCombination']==ids[1]))
{stayInTheCart=true;ajaxCart.hideOldProductCustomizations(jsonData.products[aProduct],domIdProduct);}}
if(!stayInTheCart)
{removedProductId=$(this).attr('id');if(removedProductId!=null)
{var firstCut=removedProductId.replace('cart_block_product_','');var ids=firstCut.split('_');$('#'+removedProductId).addClass('strike').fadeTo('slow',0,function(){$(this).slideUp('slow',function(){$(this).remove();if($('#cart_block dl.products dt').length==0)
{$("#header #cart_block").stop(true,true).slideUp(200);$('#cart_block_no_products:hidden').slideDown(450);$('#cart_block dl.products').remove();}});});$('#cart_block_combination_of_'+ids[0]+(ids[1]?'_'+ids[1]:'')+(ids[2]?'_'+ids[2]:'')).fadeTo('fast',0,function(){$(this).slideUp('fast',function(){$(this).remove();});});}}});}},hideOldProductCustomizations:function(product,domIdProduct)
{var customizationList=$('#customization_'+product['id']+'_'+product['idCombination']);if(customizationList.length>0)
{$(customizationList).find("li").each(function(){$(this).find("div").each(function(){var customizationDiv=$(this).attr('id');var tmp=customizationDiv.replace('deleteCustomizableProduct_','');var ids=tmp.split('_');if((parseInt(product.idCombination)==parseInt(ids[2]))&&!ajaxCart.doesCustomizationStillExist(product,ids[0]))
$('#'+customizationDiv).parent().addClass('strike').fadeTo('slow',0,function(){$(this).slideUp('slow');$(this).remove();});});});}
var removeLinks=$('#'+domIdProduct).find('.ajax_cart_block_remove_link');if(!product.hasCustomizedDatas&&!removeLinks.length)
$('#'+domIdProduct+' span.remove_link').html('<a class="ajax_cart_block_remove_link" rel="nofollow" href="'+baseUri+'?controller=cart&amp;delete=1&amp;id_product='+product['id']+'&amp;ipa='+product['idCombination']+'&amp;token='+static_token+'"> </a>');if(product.is_gift)
$('#'+domIdProduct+' span.remove_link').html('');},doesCustomizationStillExist:function(product,customizationId)
{var exists=false;$(product.customizedDatas).each(function(){if(this.customizationId==customizationId)
{exists=true;return false;}});return(exists);},refreshVouchers:function(jsonData){if(typeof(jsonData.discounts)=='undefined'||jsonData.discounts.length==0)
$('#vouchers').hide();else
{$('#vouchers tbody').html('');for(i=0;i<jsonData.discounts.length;i++)
{if(parseFloat(jsonData.discounts[i].price_float)>0)
{var delete_link='';if(jsonData.discounts[i].code.length)
delete_link='<a class="delete_voucher" href="'+jsonData.discounts[i].link+'" title="'+delete_txt+'"><img src="'+img_dir+'icon/delete.gif" alt="'+delete_txt+'" class="icon" /></a>';$('#vouchers tbody').append($('<tr class="bloc_cart_voucher" id="bloc_cart_voucher_'+jsonData.discounts[i].id+'">'
+'<td class="quantity">1x</td>'
+'<td class="name" title="'+jsonData.discounts[i].description+'">'+jsonData.discounts[i].name+'</td>'
+'<td class="price">-'+jsonData.discounts[i].price+'</td>'
+'<td class="delete">'+delete_link+'</td>'
+'</tr>'));}}
$('#vouchers').show();}},updateProductQuantity:function(product,quantity){$('#cart_block_product_'+product.id+'_'+(product.idCombination?product.idCombination:'0')+'_'+(product.idAddressDelivery?product.idAddressDelivery:'0')+' .quantity').fadeTo('fast',0,function(){$(this).text(quantity);$(this).fadeTo('fast',1,function(){$(this).fadeTo('fast',0,function(){$(this).fadeTo('fast',1,function(){$(this).fadeTo('fast',0,function(){$(this).fadeTo('fast',1);});});});});});},displayNewProducts:function(jsonData){$(jsonData.products).each(function(){if(this.id!=undefined)
{if($('#cart_block dl.products').length==0)
{$('#cart_block_no_products').before('<dl class="products"></dl>');$('#cart_block_no_products').hide();}
var domIdProduct=this.id+'_'+(this.idCombination?this.idCombination:'0')+'_'+(this.idAddressDelivery?this.idAddressDelivery:'0');var domIdProductAttribute=this.id+'_'+(this.idCombination?this.idCombination:'0');if($('#cart_block_product_'+domIdProduct).length==0)
{var productId=parseInt(this.id);var productAttributeId=(this.hasAttributes?parseInt(this.attributes):0);var content='<dt class="hidden" id="cart_block_product_'+domIdProduct+'">';content+='<span class="quantity-formated"><span class="quantity">'+this.quantity+'</span>x</span>';var name=$('<span />').html(this.name).text();name=(name.length>12?name.substring(0,10)+'...':name);content+='<a href="'+this.link+'" title="'+this.name+'" class="cart_block_product_name">'+name+'</a>';if(typeof(this.is_gift)=='undefined'||this.is_gift==0)
content+='<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="'+baseUri+'?controller=cart&amp;delete=1&amp;id_product='+productId+'&amp;token='+static_token+(this.hasAttributes?'&amp;ipa='+parseInt(this.idCombination):'')+'"> </a></span>';else
content+='<span class="remove_link"></span>';if(typeof(freeProductTranslation)!='undefined')
content+='<span class="price">'+(parseFloat(this.price_float)>0?this.priceByLine:freeProductTranslation)+'</span>';content+='</dt>';if(this.hasAttributes)
content+='<dd id="cart_block_combination_of_'+domIdProduct+'" class="hidden"><a href="'+this.link+'" title="'+this.name+'">'+this.attributes+'</a>';if(this.hasCustomizedDatas)
content+=ajaxCart.displayNewCustomizedDatas(this);if(this.hasAttributes)content+='</dd>';$('#cart_block dl.products').append(content);}
else
{var jsonProduct=this;if($.trim($('#cart_block_product_'+domIdProduct+' .quantity').html())!=jsonProduct.quantity||$.trim($('#cart_block_product_'+domIdProduct+' .price').html())!=jsonProduct.priceByLine)
{if(!this.is_gift)
$('#cart_block_product_'+domIdProduct+' .price').text(jsonProduct.priceByLine);else
$('#cart_block_product_'+domIdProduct+' .price').html(freeProductTranslation);ajaxCart.updateProductQuantity(jsonProduct,jsonProduct.quantity);if(jsonProduct.hasCustomizedDatas)
{customizationFormatedDatas=ajaxCart.displayNewCustomizedDatas(jsonProduct);if(!$('#customization_'+domIdProductAttribute).length)
{if(jsonProduct.hasAttributes)
$('#cart_block_combination_of_'+domIdProduct).append(customizationFormatedDatas);else
$('#cart_block dl.products').append(customizationFormatedDatas);}
else
{$('#customization_'+domIdProductAttribute).html('');$('#customization_'+domIdProductAttribute).append(customizationFormatedDatas);}}}}
$('#cart_block dl.products .hidden').slideDown(450).removeClass('hidden');var removeLinks=$('#cart_block_product_'+domIdProduct).find('a.ajax_cart_block_remove_link');if(this.hasCustomizedDatas&&removeLinks.length)
$(removeLinks).each(function(){$(this).remove();});}});},displayNewCustomizedDatas:function(product)
{var content='';var productId=parseInt(product.id);var productAttributeId=typeof(product.idCombination)=='undefined'?0:parseInt(product.idCombination);var hasAlreadyCustomizations=$('#customization_'+productId+'_'+productAttributeId).length;if(!hasAlreadyCustomizations)
{if(!product.hasAttributes)
content+='<dd id="cart_block_combination_of_'+productId+'" class="hidden">';if($('#customization_'+productId+'_'+productAttributeId).val()==undefined)
content+='<ul class="cart_block_customizations" id="customization_'+productId+'_'+productAttributeId+'">';}
$(product.customizedDatas).each(function()
{var done=0;customizationId=parseInt(this.customizationId);productAttributeId=typeof(product.idCombination)=='undefined'?0:parseInt(product.idCombination);content+='<li name="customization"><div class="deleteCustomizableProduct" id="deleteCustomizableProduct_'+customizationId+'_'+productId+'_'+(productAttributeId?productAttributeId:'0')+'"><a rel="nofollow" class="ajax_cart_block_remove_link" href="'+baseUri+'?controller=cart&amp;delete=1&amp;id_product='+productId+'&amp;ipa='+productAttributeId+'&amp;id_customization='+customizationId+'&amp;token='+static_token+'"></a></div><span class="quantity-formated"><span class="quantity">'+parseInt(this.quantity)+'</span>x</span>';$(this.datas).each(function(){if(this['type']==CUSTOMIZE_TEXTFIELD)
{$(this.datas).each(function(){if(this['index']==0)
{content+=' '+this.truncatedValue.replace(/<br \/>/g,' ');done=1;return false;}})}});if(!done)
content+=customizationIdMessage+customizationId;if(!hasAlreadyCustomizations)content+='</li>';if(customizationId)
{$('#uploadable_files li div.customizationUploadBrowse img').remove();$('#text_fields input').attr('value','');}});if(!hasAlreadyCustomizations)
{content+='</ul>';if(!product.hasAttributes)content+='</dd>';}
return(content);},updateCart:function(jsonData){if(jsonData.hasError)
{var errors='';for(error in jsonData.errors)
if(error!='indexOf')
errors+=$('<div />').html(jsonData.errors[error]).text()+"\n";alert(errors);}
else
{ajaxCart.updateCartEverywhere(jsonData);ajaxCart.hideOldProducts(jsonData);ajaxCart.displayNewProducts(jsonData);ajaxCart.refreshVouchers(jsonData);$('#cart_block .products dt').removeClass('first_item').removeClass('last_item').removeClass('item');$('#cart_block .products dt:first').addClass('first_item');$('#cart_block .products dt:not(:first,:last)').addClass('item');$('#cart_block .products dt:last').addClass('last_item');ajaxCart.overrideButtonsInThePage();}},updateCartEverywhere:function(jsonData){$('.ajax_cart_total').text($.trim(jsonData.productTotal));if(parseFloat(jsonData.shippingCostFloat)>0||jsonData.nbTotalProducts<1)
$('.ajax_cart_shipping_cost').text(jsonData.shippingCost);else if(typeof(freeShippingTranslation)!='undefined')
$('.ajax_cart_shipping_cost').html(freeShippingTranslation);$('.ajax_cart_tax_cost').text(jsonData.taxCost);$('.cart_block_wrapping_cost').text(jsonData.wrappingCost);$('.ajax_block_cart_total').text(jsonData.total);this.nb_total_products=jsonData.nbTotalProducts;if(parseInt(jsonData.nbTotalProducts)>0)
{$('.ajax_cart_no_product').hide();$('.ajax_cart_quantity').text(jsonData.nbTotalProducts);$('.ajax_cart_quantity').fadeIn('slow');$('.ajax_cart_total').fadeIn('slow');if(parseInt(jsonData.nbTotalProducts)>1)
{$('.ajax_cart_product_txt').each(function(){$(this).hide();});$('.ajax_cart_product_txt_s').each(function(){$(this).show();});}
else
{$('.ajax_cart_product_txt').each(function(){$(this).show();});$('.ajax_cart_product_txt_s').each(function(){$(this).hide();});}}
else
{$('.ajax_cart_quantity, .ajax_cart_product_txt_s, .ajax_cart_product_txt, .ajax_cart_total').each(function(){$(this).hide();});$('.ajax_cart_no_product').show('slow');}}};function HoverWatcher(selector){this.hovering=false;var self=this;this.isHoveringOver=function(){return self.hovering;}
$(selector).hover(function(){self.hovering=true;},function(){self.hovering=false;})}
$(document).ready(function(){$('#block_cart_collapse').click(function(){ajaxCart.collapse();});$('#block_cart_expand').click(function(){ajaxCart.expand();});ajaxCart.overrideButtonsInThePage();var cart_qty=0;var current_timestamp=parseInt(new Date().getTime()/1000);if(typeof $('.ajax_cart_quantity').html()=='undefined'||(typeof generated_date!='undefined'&&generated_date!=null&&(parseInt(generated_date)+30)<current_timestamp))
ajaxCart.refresh();else
cart_qty=parseInt($('.ajax_cart_quantity').html());var cart_block=new HoverWatcher('#header #cart_block');var shopping_cart=new HoverWatcher('#shopping_cart');$("#shopping_cart a:first").hover(function(){$(this).css('border-radius','3px 3px 0px 0px');if(ajaxCart.nb_total_products>0||cart_qty>0)
$("#header #cart_block").stop(true,true).slideDown(450);},function(){$('#shopping_cart a').css('border-radius','3px');setTimeout(function(){if(!shopping_cart.isHoveringOver()&&!cart_block.isHoveringOver())
$("#header #cart_block").stop(true,true).slideUp(450);},200);});$("#header #cart_block").hover(function(){$('#shopping_cart a').css('border-radius','3px 3px 0px 0px');},function(){$('#shopping_cart a').css('border-radius','3px');setTimeout(function(){if(!shopping_cart.isHoveringOver())
$("#header #cart_block").stop(true,true).slideUp(450);},200);});$('.delete_voucher').live('click',function(){$.ajax({type:'POST',headers:{"cache-control":"no-cache"},async:true,cache:false,url:$(this).attr('href')+'?rand='+new Date().getTime(),error:function(XMLHttpRequest,textStatus,errorThrown){alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: "+XMLHttpRequest+"\n"+'Text status: '+textStatus);}});$(this).parent().parent().remove();if($('body').attr('id')=='order'||$('body').attr('id')=='order-opc')
{if(typeof(updateAddressSelection)!='undefined')
updateAddressSelection();else
location.reload();}
return false;});$('#cart_navigation input').click(function(){$(this).attr('disabled',true).removeClass('exclusive').addClass('exclusive_disabled');$(this).closest("form").get(0).submit();});});;function openBranch(jQueryElement,noAnimation){jQueryElement.addClass('OPEN').removeClass('CLOSE');if(noAnimation)
jQueryElement.parent().find('ul:first').show();else
jQueryElement.parent().find('ul:first').slideDown();}
function closeBranch(jQueryElement,noAnimation){jQueryElement.addClass('CLOSE').removeClass('OPEN');if(noAnimation)
jQueryElement.parent().find('ul:first').hide();else
jQueryElement.parent().find('ul:first').slideUp();}
function toggleBranch(jQueryElement,noAnimation){if(jQueryElement.hasClass('OPEN'))
closeBranch(jQueryElement,noAnimation);else
openBranch(jQueryElement,noAnimation);}
$(document).ready(function(){if(!$('ul.tree.dhtml').hasClass('dynamized'))
{$('ul.tree.dhtml ul').prev().before("<span class='grower OPEN'> </span>");$('ul.tree.dhtml ul li:last-child, ul.tree.dhtml li:last-child').addClass('last');$('ul.tree.dhtml span.grower.OPEN').addClass('CLOSE').removeClass('OPEN').parent().find('ul:first').hide();$('ul.tree.dhtml').show();$('ul.tree.dhtml .selected').parents().each(function(){if($(this).is('ul'))
toggleBranch($(this).prev().prev(),true);});toggleBranch($('ul.tree.dhtml .selected').prev(),true);$('ul.tree.dhtml span.grower').click(function(){toggleBranch($(this));});$('ul.tree.dhtml').addClass('dynamized');$('ul.tree.dhtml').removeClass('dhtml');}});;!function($){"use strict";$(function(){$.support.transition=(function(){var transitionEnd=(function(){var el=document.createElement('bootstrap'),transEndEventNames={'WebkitTransition':'webkitTransitionEnd','MozTransition':'transitionend','OTransition':'oTransitionEnd otransitionend','transition':'transitionend'},name
for(name in transEndEventNames){if(el.style[name]!==undefined){return transEndEventNames[name]}}}())
return transitionEnd&&{end:transitionEnd}})()})}(window.jQuery);!function($){"use strict";var dismiss='[data-dismiss="alert"]',Alert=function(el){$(el).on('click',dismiss,this.close)}
Alert.prototype.close=function(e){var $this=$(this),selector=$this.attr('data-target'),$parent
if(!selector){selector=$this.attr('href')
selector=selector&&selector.replace(/.*(?=#[^\s]*$)/,'')}
$parent=$(selector)
e&&e.preventDefault()
$parent.length||($parent=$this.hasClass('alert')?$this:$this.parent())
$parent.trigger(e=$.Event('close'))
if(e.isDefaultPrevented())return
$parent.removeClass('in')
function removeElement(){$parent.trigger('closed').remove()}
$.support.transition&&$parent.hasClass('fade')?$parent.on($.support.transition.end,removeElement):removeElement()}
var old=$.fn.alert
$.fn.alert=function(option){return this.each(function(){var $this=$(this),data=$this.data('alert')
if(!data)$this.data('alert',(data=new Alert(this)))
if(typeof option=='string')data[option].call($this)})}
$.fn.alert.Constructor=Alert
$.fn.alert.noConflict=function(){$.fn.alert=old
return this}
$(document).on('click.alert.data-api',dismiss,Alert.prototype.close)}(window.jQuery);!function($){"use strict";var Button=function(element,options){this.$element=$(element)
this.options=$.extend({},$.fn.button.defaults,options)}
Button.prototype.setState=function(state){var d='disabled',$el=this.$element,data=$el.data(),val=$el.is('input')?'val':'html'
state=state+'Text'
data.resetText||$el.data('resetText',$el[val]())
$el[val](data[state]||this.options[state])
setTimeout(function(){state=='loadingText'?$el.addClass(d).attr(d,d):$el.removeClass(d).removeAttr(d)},0)}
Button.prototype.toggle=function(){var $parent=this.$element.closest('[data-toggle="buttons-radio"]')
$parent&&$parent.find('.active').removeClass('active')
this.$element.toggleClass('active')}
var old=$.fn.button
$.fn.button=function(option){return this.each(function(){var $this=$(this),data=$this.data('button'),options=typeof option=='object'&&option
if(!data)$this.data('button',(data=new Button(this,options)))
if(option=='toggle')data.toggle()
else if(option)data.setState(option)})}
$.fn.button.defaults={loadingText:'loading...'}
$.fn.button.Constructor=Button
$.fn.button.noConflict=function(){$.fn.button=old
return this}
$(document).on('click.button.data-api','[data-toggle^=button]',function(e){var $btn=$(e.target)
if(!$btn.hasClass('btn'))$btn=$btn.closest('.btn')
$btn.button('toggle')})}(window.jQuery);!function($){"use strict";var Carousel=function(element,options){this.$element=$(element)
this.$indicators=this.$element.find('.carousel-indicators')
this.options=options
this.options.pause=='hover'&&this.$element.on('mouseenter',$.proxy(this.pause,this)).on('mouseleave',$.proxy(this.cycle,this))}
Carousel.prototype={cycle:function(e){if(!e)this.paused=false
if(this.interval)clearInterval(this.interval);this.options.interval&&!this.paused&&(this.interval=setInterval($.proxy(this.next,this),this.options.interval))
return this},getActiveIndex:function(){this.$active=this.$element.find('.item.active')
this.$items=this.$active.parent().children()
return this.$items.index(this.$active)},to:function(pos){var activeIndex=this.getActiveIndex(),that=this
if(pos>(this.$items.length-1)||pos<0)return
if(this.sliding){return this.$element.one('slid',function(){that.to(pos)})}
if(activeIndex==pos){return this.pause().cycle()}
return this.slide(pos>activeIndex?'next':'prev',$(this.$items[pos]))},pause:function(e){if(!e)this.paused=true
if(this.$element.find('.next, .prev').length&&$.support.transition.end){this.$element.trigger($.support.transition.end)
this.cycle(true)}
clearInterval(this.interval)
this.interval=null
return this},next:function(){if(this.sliding)return
return this.slide('next')},prev:function(){if(this.sliding)return
return this.slide('prev')},slide:function(type,next){var $active=this.$element.find('.item.active'),$next=next||$active[type](),isCycling=this.interval,direction=type=='next'?'left':'right',fallback=type=='next'?'first':'last',that=this,e
this.sliding=true
isCycling&&this.pause()
$next=$next.length?$next:this.$element.find('.item')[fallback]()
e=$.Event('slide',{relatedTarget:$next[0],direction:direction})
if($next.hasClass('active'))return
if(this.$indicators.length){this.$indicators.find('.active').removeClass('active')
this.$element.one('slid',function(){var $nextIndicator=$(that.$indicators.children()[that.getActiveIndex()])
$nextIndicator&&$nextIndicator.addClass('active')})}
if($.support.transition&&this.$element.hasClass('slide')){this.$element.trigger(e)
if(e.isDefaultPrevented())return
$next.addClass(type)
$next[0].offsetWidth
$active.addClass(direction)
$next.addClass(direction)
this.$element.one($.support.transition.end,function(){$next.removeClass([type,direction].join(' ')).addClass('active')
$active.removeClass(['active',direction].join(' '))
that.sliding=false
setTimeout(function(){that.$element.trigger('slid')},0)})}else{this.$element.trigger(e)
if(e.isDefaultPrevented())return
$active.removeClass('active')
$next.addClass('active')
this.sliding=false
this.$element.trigger('slid')}
isCycling&&this.cycle()
return this}}
var old=$.fn.carousel
$.fn.carousel=function(option){return this.each(function(){var $this=$(this),data=$this.data('carousel'),options=$.extend({},$.fn.carousel.defaults,typeof option=='object'&&option),action=typeof option=='string'?option:options.slide
if(!data)$this.data('carousel',(data=new Carousel(this,options)))
if(typeof option=='number')data.to(option)
else if(action)data[action]()
else if(options.interval)data.pause().cycle()})}
$.fn.carousel.defaults={interval:5000,pause:'hover'}
$.fn.carousel.Constructor=Carousel
$.fn.carousel.noConflict=function(){$.fn.carousel=old
return this}
$(document).on('click.carousel.data-api','[data-slide], [data-slide-to]',function(e){var $this=$(this),href,$target=$($this.attr('data-target')||(href=$this.attr('href'))&&href.replace(/.*(?=#[^\s]+$)/,'')),options=$.extend({},$target.data(),$this.data()),slideIndex
$target.carousel(options)
if(slideIndex=$this.attr('data-slide-to')){$target.data('carousel').pause().to(slideIndex).cycle()}
e.preventDefault()})}(window.jQuery);!function($){"use strict";var Collapse=function(element,options){this.$element=$(element)
this.options=$.extend({},$.fn.collapse.defaults,options)
if(this.options.parent){this.$parent=$(this.options.parent)}
this.options.toggle&&this.toggle()}
Collapse.prototype={constructor:Collapse,dimension:function(){var hasWidth=this.$element.hasClass('width')
return hasWidth?'width':'height'},show:function(){var dimension,scroll,actives,hasData
if(this.transitioning||this.$element.hasClass('in'))return
dimension=this.dimension()
scroll=$.camelCase(['scroll',dimension].join('-'))
actives=this.$parent&&this.$parent.find('> .accordion-group > .in')
if(actives&&actives.length){hasData=actives.data('collapse')
if(hasData&&hasData.transitioning)return
actives.collapse('hide')
hasData||actives.data('collapse',null)}
this.$element[dimension](0)
this.transition('addClass',$.Event('show'),'shown')
$.support.transition&&this.$element[dimension](this.$element[0][scroll])},hide:function(){var dimension
if(this.transitioning||!this.$element.hasClass('in'))return
dimension=this.dimension()
this.reset(this.$element[dimension]())
this.transition('removeClass',$.Event('hide'),'hidden')
this.$element[dimension](0)},reset:function(size){var dimension=this.dimension()
this.$element.removeClass('collapse')
[dimension](size||'auto')
[0].offsetWidth
this.$element[size!==null?'addClass':'removeClass']('collapse')
return this},transition:function(method,startEvent,completeEvent){var that=this,complete=function(){if(startEvent.type=='show')that.reset()
that.transitioning=0
that.$element.trigger(completeEvent)}
this.$element.trigger(startEvent)
if(startEvent.isDefaultPrevented())return
this.transitioning=1
this.$element[method]('in')
$.support.transition&&this.$element.hasClass('collapse')?this.$element.one($.support.transition.end,complete):complete()},toggle:function(){this[this.$element.hasClass('in')?'hide':'show']()}}
var old=$.fn.collapse
$.fn.collapse=function(option){return this.each(function(){var $this=$(this),data=$this.data('collapse'),options=$.extend({},$.fn.collapse.defaults,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('collapse',(data=new Collapse(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.collapse.defaults={toggle:true}
$.fn.collapse.Constructor=Collapse
$.fn.collapse.noConflict=function(){$.fn.collapse=old
return this}
$(document).on('click.collapse.data-api','[data-toggle=collapse]',function(e){var $this=$(this),href,target=$this.attr('data-target')||e.preventDefault()||(href=$this.attr('href'))&&href.replace(/.*(?=#[^\s]+$)/,''),option=$(target).data('collapse')?'toggle':$this.data()
$this[$(target).hasClass('in')?'addClass':'removeClass']('collapsed')
$(target).collapse(option)})}(window.jQuery);!function($){"use strict";var toggle='[data-toggle=dropdown]',Dropdown=function(element){var $el=$(element).on('click.dropdown.data-api',this.toggle)
$('html').on('click.dropdown.data-api',function(){$el.parent().removeClass('open')})}
Dropdown.prototype={constructor:Dropdown,toggle:function(e){var $this=$(this),$parent,isActive
if($this.is('.disabled, :disabled'))return
$parent=getParent($this)
isActive=$parent.hasClass('open')
clearMenus()
if(!isActive){$parent.toggleClass('open')}
$this.focus()
return false},keydown:function(e){var $this,$items,$active,$parent,isActive,index
if(!/(38|40|27)/.test(e.keyCode))return
$this=$(this)
e.preventDefault()
e.stopPropagation()
if($this.is('.disabled, :disabled'))return
$parent=getParent($this)
isActive=$parent.hasClass('open')
if(!isActive||(isActive&&e.keyCode==27)){if(e.which==27)$parent.find(toggle).focus()
return $this.click()}
$items=$('[role=menu] li:not(.divider):visible a',$parent)
if(!$items.length)return
index=$items.index($items.filter(':focus'))
if(e.keyCode==38&&index>0)index--
if(e.keyCode==40&&index<$items.length-1)index++
if(!~index)index=0
$items.eq(index).focus()}}
function clearMenus(){$(toggle).each(function(){getParent($(this)).removeClass('open')})}
function getParent($this){var selector=$this.attr('data-target'),$parent
if(!selector){selector=$this.attr('href')
selector=selector&&/#/.test(selector)&&selector.replace(/.*(?=#[^\s]*$)/,'')}
$parent=selector&&$(selector)
if(!$parent||!$parent.length)$parent=$this.parent()
return $parent}
var old=$.fn.dropdown
$.fn.dropdown=function(option){return this.each(function(){var $this=$(this),data=$this.data('dropdown')
if(!data)$this.data('dropdown',(data=new Dropdown(this)))
if(typeof option=='string')data[option].call($this)})}
$.fn.dropdown.Constructor=Dropdown
$.fn.dropdown.noConflict=function(){$.fn.dropdown=old
return this}
$(document).on('click.dropdown.data-api',clearMenus).on('click.dropdown.data-api','.dropdown form',function(e){e.stopPropagation()}).on('click.dropdown-menu',function(e){e.stopPropagation()}).on('click.dropdown.data-api',toggle,Dropdown.prototype.toggle).on('keydown.dropdown.data-api',toggle+', [role=menu]',Dropdown.prototype.keydown)}(window.jQuery);!function($){"use strict";var Modal=function(element,options){this.options=options
this.$element=$(element).delegate('[data-dismiss="modal"]','click.dismiss.modal',$.proxy(this.hide,this))
this.options.remote&&this.$element.find('.modal-body').load(this.options.remote)}
Modal.prototype={constructor:Modal,toggle:function(){return this[!this.isShown?'show':'hide']()},show:function(){var that=this,e=$.Event('show')
this.$element.trigger(e)
if(this.isShown||e.isDefaultPrevented())return
this.isShown=true
this.escape()
this.backdrop(function(){var transition=$.support.transition&&that.$element.hasClass('fade')
if(!that.$element.parent().length){that.$element.appendTo(document.body)}
that.$element.show()
if(transition){that.$element[0].offsetWidth}
that.$element.addClass('in').attr('aria-hidden',false)
that.enforceFocus()
transition?that.$element.one($.support.transition.end,function(){that.$element.focus().trigger('shown')}):that.$element.focus().trigger('shown')})},hide:function(e){e&&e.preventDefault()
var that=this
e=$.Event('hide')
this.$element.trigger(e)
if(!this.isShown||e.isDefaultPrevented())return
this.isShown=false
this.escape()
$(document).off('focusin.modal')
this.$element.removeClass('in').attr('aria-hidden',true)
$.support.transition&&this.$element.hasClass('fade')?this.hideWithTransition():this.hideModal()},enforceFocus:function(){var that=this
$(document).on('focusin.modal',function(e){if(that.$element[0]!==e.target&&!that.$element.has(e.target).length){that.$element.focus()}})},escape:function(){var that=this
if(this.isShown&&this.options.keyboard){this.$element.on('keyup.dismiss.modal',function(e){e.which==27&&that.hide()})}else if(!this.isShown){this.$element.off('keyup.dismiss.modal')}},hideWithTransition:function(){var that=this,timeout=setTimeout(function(){that.$element.off($.support.transition.end)
that.hideModal()},500)
this.$element.one($.support.transition.end,function(){clearTimeout(timeout)
that.hideModal()})},hideModal:function(){var that=this
this.$element.hide()
this.backdrop(function(){that.removeBackdrop()
that.$element.trigger('hidden')})},removeBackdrop:function(){this.$backdrop&&this.$backdrop.remove()
this.$backdrop=null},backdrop:function(callback){var that=this,animate=this.$element.hasClass('fade')?'fade':''
if(this.isShown&&this.options.backdrop){var doAnimate=$.support.transition&&animate
this.$backdrop=$('<div class="modal-backdrop '+animate+'" />').appendTo(document.body)
this.$backdrop.click(this.options.backdrop=='static'?$.proxy(this.$element[0].focus,this.$element[0]):$.proxy(this.hide,this))
if(doAnimate)this.$backdrop[0].offsetWidth
this.$backdrop.addClass('in')
if(!callback)return
doAnimate?this.$backdrop.one($.support.transition.end,callback):callback()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass('in')
$.support.transition&&this.$element.hasClass('fade')?this.$backdrop.one($.support.transition.end,callback):callback()}else if(callback){callback()}}}
var old=$.fn.modal
$.fn.modal=function(option){return this.each(function(){var $this=$(this),data=$this.data('modal'),options=$.extend({},$.fn.modal.defaults,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('modal',(data=new Modal(this,options)))
if(typeof option=='string')data[option]()
else if(options.show)data.show()})}
$.fn.modal.defaults={backdrop:true,keyboard:true,show:true}
$.fn.modal.Constructor=Modal
$.fn.modal.noConflict=function(){$.fn.modal=old
return this}
$(document).on('click.modal.data-api','[data-toggle="modal"]',function(e){var $this=$(this),href=$this.attr('href'),$target=$($this.attr('data-target')||(href&&href.replace(/.*(?=#[^\s]+$)/,''))),option=$target.data('modal')?'toggle':$.extend({remote:!/#/.test(href)&&href},$target.data(),$this.data())
e.preventDefault()
$target.modal(option).one('hide',function(){$this.focus()})})}(window.jQuery);!function($){"use strict";var Tooltip=function(element,options){this.init('tooltip',element,options)}
Tooltip.prototype={constructor:Tooltip,init:function(type,element,options){var eventIn,eventOut,triggers,trigger,i
this.type=type
this.$element=$(element)
this.options=this.getOptions(options)
this.enabled=true
triggers=this.options.trigger.split(' ')
for(i=triggers.length;i--;){trigger=triggers[i]
if(trigger=='click'){this.$element.on('click.'+this.type,this.options.selector,$.proxy(this.toggle,this))}else if(trigger!='manual'){eventIn=trigger=='hover'?'mouseenter':'focus'
eventOut=trigger=='hover'?'mouseleave':'blur'
this.$element.on(eventIn+'.'+this.type,this.options.selector,$.proxy(this.enter,this))
this.$element.on(eventOut+'.'+this.type,this.options.selector,$.proxy(this.leave,this))}}
this.options.selector?(this._options=$.extend({},this.options,{trigger:'manual',selector:''})):this.fixTitle()},getOptions:function(options){options=$.extend({},$.fn[this.type].defaults,this.$element.data(),options)
if(options.delay&&typeof options.delay=='number'){options.delay={show:options.delay,hide:options.delay}}
return options},enter:function(e){var defaults=$.fn[this.type].defaults,options={},self
this._options&&$.each(this._options,function(key,value){if(defaults[key]!=value)options[key]=value},this)
self=$(e.currentTarget)[this.type](options).data(this.type)
if(!self.options.delay||!self.options.delay.show)return self.show()
clearTimeout(this.timeout)
self.hoverState='in'
this.timeout=setTimeout(function(){if(self.hoverState=='in')self.show()},self.options.delay.show)},leave:function(e){var self=$(e.currentTarget)[this.type](this._options).data(this.type)
if(this.timeout)clearTimeout(this.timeout)
if(!self.options.delay||!self.options.delay.hide)return self.hide()
self.hoverState='out'
this.timeout=setTimeout(function(){if(self.hoverState=='out')self.hide()},self.options.delay.hide)},show:function(){var $tip,pos,actualWidth,actualHeight,placement,tp,e=$.Event('show')
if(this.hasContent()&&this.enabled){this.$element.trigger(e)
if(e.isDefaultPrevented())return
$tip=this.tip()
this.setContent()
if(this.options.animation){$tip.addClass('fade')}
placement=typeof this.options.placement=='function'?this.options.placement.call(this,$tip[0],this.$element[0]):this.options.placement
$tip.detach().css({top:0,left:0,display:'block'})
this.options.container?$tip.appendTo(this.options.container):$tip.insertAfter(this.$element)
pos=this.getPosition()
actualWidth=$tip[0].offsetWidth
actualHeight=$tip[0].offsetHeight
switch(placement){case'bottom':tp={top:pos.top+pos.height,left:pos.left+pos.width/2-actualWidth/2}
break
case'top':tp={top:pos.top-actualHeight,left:pos.left+pos.width/2-actualWidth/2}
break
case'left':tp={top:pos.top+pos.height/2-actualHeight/2,left:pos.left-actualWidth}
break
case'right':tp={top:pos.top+pos.height/2-actualHeight/2,left:pos.left+pos.width}
break}
this.applyPlacement(tp,placement)
this.$element.trigger('shown')}},applyPlacement:function(offset,placement){var $tip=this.tip(),width=$tip[0].offsetWidth,height=$tip[0].offsetHeight,actualWidth,actualHeight,delta,replace
$tip.offset(offset).addClass(placement).addClass('in')
actualWidth=$tip[0].offsetWidth
actualHeight=$tip[0].offsetHeight
if(placement=='top'&&actualHeight!=height){offset.top=offset.top+height-actualHeight
replace=true}
if(placement=='bottom'||placement=='top'){delta=0
if(offset.left<0){delta=offset.left*-2
offset.left=0
$tip.offset(offset)
actualWidth=$tip[0].offsetWidth
actualHeight=$tip[0].offsetHeight}
this.replaceArrow(delta-width+actualWidth,actualWidth,'left')}else{this.replaceArrow(actualHeight-height,actualHeight,'top')}
if(replace)$tip.offset(offset)},replaceArrow:function(delta,dimension,position){this.arrow().css(position,delta?(50*(1-delta/dimension)+"%"):'')},setContent:function(){var $tip=this.tip(),title=this.getTitle()
$tip.find('.tooltip-inner')[this.options.html?'html':'text'](title)
$tip.removeClass('fade in top bottom left right')},hide:function(){var that=this,$tip=this.tip(),e=$.Event('hide')
this.$element.trigger(e)
if(e.isDefaultPrevented())return
$tip.removeClass('in')
function removeWithAnimation(){var timeout=setTimeout(function(){$tip.off($.support.transition.end).detach()},500)
$tip.one($.support.transition.end,function(){clearTimeout(timeout)
$tip.detach()})}
$.support.transition&&this.$tip.hasClass('fade')?removeWithAnimation():$tip.detach()
this.$element.trigger('hidden')
return this},fixTitle:function(){var $e=this.$element
if($e.attr('title')||typeof($e.attr('data-original-title'))!='string'){$e.attr('data-original-title',$e.attr('title')||'').attr('title','')}},hasContent:function(){return this.getTitle()},getPosition:function(){var el=this.$element[0]
return $.extend({},(typeof el.getBoundingClientRect=='function')?el.getBoundingClientRect():{width:el.offsetWidth,height:el.offsetHeight},this.$element.offset())},getTitle:function(){var title,$e=this.$element,o=this.options
title=$e.attr('data-original-title')||(typeof o.title=='function'?o.title.call($e[0]):o.title)
return title},tip:function(){return this.$tip=this.$tip||$(this.options.template)},arrow:function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},validate:function(){if(!this.$element[0].parentNode){this.hide()
this.$element=null
this.options=null}},enable:function(){this.enabled=true},disable:function(){this.enabled=false},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(e){var self=e?$(e.currentTarget)[this.type](this._options).data(this.type):this
self.tip().hasClass('in')?self.hide():self.show()},destroy:function(){this.hide().$element.off('.'+this.type).removeData(this.type)}}
var old=$.fn.tooltip
$.fn.tooltip=function(option){return this.each(function(){var $this=$(this),data=$this.data('tooltip'),options=typeof option=='object'&&option
if(!data)$this.data('tooltip',(data=new Tooltip(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.tooltip.Constructor=Tooltip
$.fn.tooltip.defaults={animation:true,placement:'top',selector:false,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:'hover focus',title:'',delay:0,html:false,container:false}
$.fn.tooltip.noConflict=function(){$.fn.tooltip=old
return this}}(window.jQuery);!function($){"use strict";var Popover=function(element,options){this.init('popover',element,options)}
Popover.prototype=$.extend({},$.fn.tooltip.Constructor.prototype,{constructor:Popover,setContent:function(){var $tip=this.tip(),title=this.getTitle(),content=this.getContent()
$tip.find('.popover-title')[this.options.html?'html':'text'](title)
$tip.find('.popover-content')[this.options.html?'html':'text'](content)
$tip.removeClass('fade top bottom left right in')},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var content,$e=this.$element,o=this.options
content=(typeof o.content=='function'?o.content.call($e[0]):o.content)||$e.attr('data-content')
return content},tip:function(){if(!this.$tip){this.$tip=$(this.options.template)}
return this.$tip},destroy:function(){this.hide().$element.off('.'+this.type).removeData(this.type)}})
var old=$.fn.popover
$.fn.popover=function(option){return this.each(function(){var $this=$(this),data=$this.data('popover'),options=typeof option=='object'&&option
if(!data)$this.data('popover',(data=new Popover(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.popover.Constructor=Popover
$.fn.popover.defaults=$.extend({},$.fn.tooltip.defaults,{placement:'right',trigger:'click',content:'',template:'<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'})
$.fn.popover.noConflict=function(){$.fn.popover=old
return this}}(window.jQuery);!function($){"use strict";function ScrollSpy(element,options){var process=$.proxy(this.process,this),$element=$(element).is('body')?$(window):$(element),href
this.options=$.extend({},$.fn.scrollspy.defaults,options)
this.$scrollElement=$element.on('scroll.scroll-spy.data-api',process)
this.selector=(this.options.target||((href=$(element).attr('href'))&&href.replace(/.*(?=#[^\s]+$)/,''))||'')+' .nav li > a'
this.$body=$('body')
this.refresh()
this.process()}
ScrollSpy.prototype={constructor:ScrollSpy,refresh:function(){var self=this,$targets
this.offsets=$([])
this.targets=$([])
$targets=this.$body.find(this.selector).map(function(){var $el=$(this),href=$el.data('target')||$el.attr('href'),$href=/^#\w/.test(href)&&$(href)
return($href&&$href.length&&[[$href.position().top+(!$.isWindow(self.$scrollElement.get(0))&&self.$scrollElement.scrollTop()),href]])||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){self.offsets.push(this[0])
self.targets.push(this[1])})},process:function(){var scrollTop=this.$scrollElement.scrollTop()+this.options.offset,scrollHeight=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,maxScroll=scrollHeight-this.$scrollElement.height(),offsets=this.offsets,targets=this.targets,activeTarget=this.activeTarget,i
if(scrollTop>=maxScroll){return activeTarget!=(i=targets.last()[0])&&this.activate(i)}
for(i=offsets.length;i--;){activeTarget!=targets[i]&&scrollTop>=offsets[i]&&(!offsets[i+1]||scrollTop<=offsets[i+1])&&this.activate(targets[i])}},activate:function(target){var active,selector
this.activeTarget=target
$(this.selector).parent('.active').removeClass('active')
selector=this.selector
+'[data-target="'+target+'"],'
+this.selector+'[href="'+target+'"]'
active=$(selector).parent('li').addClass('active')
if(active.parent('.dropdown-menu').length){active=active.closest('li.dropdown').addClass('active')}
active.trigger('activate')}}
var old=$.fn.scrollspy
$.fn.scrollspy=function(option){return this.each(function(){var $this=$(this),data=$this.data('scrollspy'),options=typeof option=='object'&&option
if(!data)$this.data('scrollspy',(data=new ScrollSpy(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.scrollspy.Constructor=ScrollSpy
$.fn.scrollspy.defaults={offset:10}
$.fn.scrollspy.noConflict=function(){$.fn.scrollspy=old
return this}
$(window).on('load',function(){$('[data-spy="scroll"]').each(function(){var $spy=$(this)
$spy.scrollspy($spy.data())})})}(window.jQuery);!function($){"use strict";var Tab=function(element){this.element=$(element)}
Tab.prototype={constructor:Tab,show:function(){var $this=this.element,$ul=$this.closest('ul:not(.dropdown-menu)'),selector=$this.attr('data-target'),previous,$target,e
if(!selector){selector=$this.attr('href')
selector=selector&&selector.replace(/.*(?=#[^\s]*$)/,'')}
if($this.parent('li').hasClass('active'))return
previous=$ul.find('.active:last a')[0]
e=$.Event('show',{relatedTarget:previous})
$this.trigger(e)
if(e.isDefaultPrevented())return
$target=$(selector)
this.activate($this.parent('li'),$ul)
this.activate($target,$target.parent(),function(){$this.trigger({type:'shown',relatedTarget:previous})})},activate:function(element,container,callback){var $active=container.find('> .active'),transition=callback&&$.support.transition&&$active.hasClass('fade')
function next(){$active.removeClass('active').find('> .dropdown-menu > .active').removeClass('active')
element.addClass('active')
if(transition){element[0].offsetWidth
element.addClass('in')}else{element.removeClass('fade')}
if(element.parent('.dropdown-menu')){element.closest('li.dropdown').addClass('active')}
callback&&callback()}
transition?$active.one($.support.transition.end,next):next()
$active.removeClass('in')}}
var old=$.fn.tab
$.fn.tab=function(option){return this.each(function(){var $this=$(this),data=$this.data('tab')
if(!data)$this.data('tab',(data=new Tab(this)))
if(typeof option=='string')data[option]()})}
$.fn.tab.Constructor=Tab
$.fn.tab.noConflict=function(){$.fn.tab=old
return this}
$(document).on('click.tab.data-api','[data-toggle="tab"], [data-toggle="pill"]',function(e){e.preventDefault()
$(this).tab('show')})}(window.jQuery);!function($){"use strict";var Typeahead=function(element,options){this.$element=$(element)
this.options=$.extend({},$.fn.typeahead.defaults,options)
this.matcher=this.options.matcher||this.matcher
this.sorter=this.options.sorter||this.sorter
this.highlighter=this.options.highlighter||this.highlighter
this.updater=this.options.updater||this.updater
this.source=this.options.source
this.$menu=$(this.options.menu)
this.shown=false
this.listen()}
Typeahead.prototype={constructor:Typeahead,select:function(){var val=this.$menu.find('.active').attr('data-value')
this.$element.val(this.updater(val)).change()
return this.hide()},updater:function(item){return item},show:function(){var pos=$.extend({},this.$element.position(),{height:this.$element[0].offsetHeight})
this.$menu.insertAfter(this.$element).css({top:pos.top+pos.height,left:pos.left}).show()
this.shown=true
return this},hide:function(){this.$menu.hide()
this.shown=false
return this},lookup:function(event){var items
this.query=this.$element.val()
if(!this.query||this.query.length<this.options.minLength){return this.shown?this.hide():this}
items=$.isFunction(this.source)?this.source(this.query,$.proxy(this.process,this)):this.source
return items?this.process(items):this},process:function(items){var that=this
items=$.grep(items,function(item){return that.matcher(item)})
items=this.sorter(items)
if(!items.length){return this.shown?this.hide():this}
return this.render(items.slice(0,this.options.items)).show()},matcher:function(item){return~item.toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(items){var beginswith=[],caseSensitive=[],caseInsensitive=[],item
while(item=items.shift()){if(!item.toLowerCase().indexOf(this.query.toLowerCase()))beginswith.push(item)
else if(~item.indexOf(this.query))caseSensitive.push(item)
else caseInsensitive.push(item)}
return beginswith.concat(caseSensitive,caseInsensitive)},highlighter:function(item){var query=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,'\\$&')
return item.replace(new RegExp('('+query+')','ig'),function($1,match){return'<strong>'+match+'</strong>'})},render:function(items){var that=this
items=$(items).map(function(i,item){i=$(that.options.item).attr('data-value',item)
i.find('a').html(that.highlighter(item))
return i[0]})
items.first().addClass('active')
this.$menu.html(items)
return this},next:function(event){var active=this.$menu.find('.active').removeClass('active'),next=active.next()
if(!next.length){next=$(this.$menu.find('li')[0])}
next.addClass('active')},prev:function(event){var active=this.$menu.find('.active').removeClass('active'),prev=active.prev()
if(!prev.length){prev=this.$menu.find('li').last()}
prev.addClass('active')},listen:function(){this.$element.on('focus',$.proxy(this.focus,this)).on('blur',$.proxy(this.blur,this)).on('keypress',$.proxy(this.keypress,this)).on('keyup',$.proxy(this.keyup,this))
if(this.eventSupported('keydown')){this.$element.on('keydown',$.proxy(this.keydown,this))}
this.$menu.on('click',$.proxy(this.click,this)).on('mouseenter','li',$.proxy(this.mouseenter,this)).on('mouseleave','li',$.proxy(this.mouseleave,this))},eventSupported:function(eventName){var isSupported=eventName in this.$element
if(!isSupported){this.$element.setAttribute(eventName,'return;')
isSupported=typeof this.$element[eventName]==='function'}
return isSupported},move:function(e){if(!this.shown)return
switch(e.keyCode){case 9:case 13:case 27:e.preventDefault()
break
case 38:e.preventDefault()
this.prev()
break
case 40:e.preventDefault()
this.next()
break}
e.stopPropagation()},keydown:function(e){this.suppressKeyPressRepeat=~$.inArray(e.keyCode,[40,38,9,13,27])
this.move(e)},keypress:function(e){if(this.suppressKeyPressRepeat)return
this.move(e)},keyup:function(e){switch(e.keyCode){case 40:case 38:case 16:case 17:case 18:break
case 9:case 13:if(!this.shown)return
this.select()
break
case 27:if(!this.shown)return
this.hide()
break
default:this.lookup()}
e.stopPropagation()
e.preventDefault()},focus:function(e){this.focused=true},blur:function(e){this.focused=false
if(!this.mousedover&&this.shown)this.hide()},click:function(e){e.stopPropagation()
e.preventDefault()
this.select()
this.$element.focus()},mouseenter:function(e){this.mousedover=true
this.$menu.find('.active').removeClass('active')
$(e.currentTarget).addClass('active')},mouseleave:function(e){this.mousedover=false
if(!this.focused&&this.shown)this.hide()}}
var old=$.fn.typeahead
$.fn.typeahead=function(option){return this.each(function(){var $this=$(this),data=$this.data('typeahead'),options=typeof option=='object'&&option
if(!data)$this.data('typeahead',(data=new Typeahead(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.typeahead.defaults={source:[],items:8,menu:'<ul class="typeahead dropdown-menu"></ul>',item:'<li><a href="#"></a></li>',minLength:1}
$.fn.typeahead.Constructor=Typeahead
$.fn.typeahead.noConflict=function(){$.fn.typeahead=old
return this}
$(document).on('focus.typeahead.data-api','[data-provide="typeahead"]',function(e){var $this=$(this)
if($this.data('typeahead'))return
$this.typeahead($this.data())})}(window.jQuery);!function($){"use strict";var Affix=function(element,options){this.options=$.extend({},$.fn.affix.defaults,options)
this.$window=$(window).on('scroll.affix.data-api',$.proxy(this.checkPosition,this)).on('click.affix.data-api',$.proxy(function(){setTimeout($.proxy(this.checkPosition,this),1)},this))
this.$element=$(element)
this.checkPosition()}
Affix.prototype.checkPosition=function(){if(!this.$element.is(':visible'))return
var scrollHeight=$(document).height(),scrollTop=this.$window.scrollTop(),position=this.$element.offset(),offset=this.options.offset,offsetBottom=offset.bottom,offsetTop=offset.top,reset='affix affix-top affix-bottom',affix
if(typeof offset!='object')offsetBottom=offsetTop=offset
if(typeof offsetTop=='function')offsetTop=offset.top()
if(typeof offsetBottom=='function')offsetBottom=offset.bottom()
affix=this.unpin!=null&&(scrollTop+this.unpin<=position.top)?false:offsetBottom!=null&&(position.top+this.$element.height()>=scrollHeight-offsetBottom)?'bottom':offsetTop!=null&&scrollTop<=offsetTop?'top':false
if(this.affixed===affix)return
this.affixed=affix
this.unpin=affix=='bottom'?position.top-scrollTop:null
this.$element.removeClass(reset).addClass('affix'+(affix?'-'+affix:''))}
var old=$.fn.affix
$.fn.affix=function(option){return this.each(function(){var $this=$(this),data=$this.data('affix'),options=typeof option=='object'&&option
if(!data)$this.data('affix',(data=new Affix(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.affix.Constructor=Affix
$.fn.affix.defaults={offset:0}
$.fn.affix.noConflict=function(){$.fn.affix=old
return this}
$(window).on('load',function(){$('[data-spy="affix"]').each(function(){var $spy=$(this),data=$spy.data()
data.offset=data.offset||{}
data.offsetBottom&&(data.offset.bottom=data.offsetBottom)
data.offsetTop&&(data.offset.top=data.offsetTop)
$spy.affix(data)})})}(window.jQuery);;(function($){$.fn.flexisel=function(options){var defaults=$.extend({visibleItems:4,animationSpeed:200,autoPlay:false,autoPlaySpeed:3000,pauseOnHover:true,setMaxWidthAndHeight:false,enableResponsiveBreakpoints:false,responsiveBreakpoints:{portrait:{changePoint:480,visibleItems:1},landscape:{changePoint:640,visibleItems:2},tablet:{changePoint:768,visibleItems:3}}},options);var object=$(this);var settings=$.extend(defaults,options);var itemsWidth;var canNavigate=true;var itemsVisible=settings.visibleItems;var responsivePoints=[];var methods={init:function(){return this.each(function(){methods.appendHTML();methods.setEventHandlers();methods.initializeItems();});},initializeItems:function(){var listParent=object.parent();var innerHeight=listParent.height();var childSet=object.children();methods.sortResponsiveObject(settings.responsiveBreakpoints);var innerWidth=listParent.width();itemsWidth=(innerWidth)/itemsVisible;childSet.width(itemsWidth);childSet.last().insertBefore(childSet.first());childSet.last().insertBefore(childSet.first());object.css({'left':-itemsWidth});object.fadeIn();$(window).trigger("resize");},appendHTML:function(){object.addClass("nbs-flexisel-ul");object.wrap("<div class='nbs-flexisel-container'><div class='nbs-flexisel-inner'></div></div>");object.find("li").addClass("nbs-flexisel-item");if(settings.setMaxWidthAndHeight){var baseWidth=$(".nbs-flexisel-item img").width();var baseHeight=$(".nbs-flexisel-item img").height();$(".nbs-flexisel-item img").css("max-width",baseWidth);$(".nbs-flexisel-item img").css("max-height",baseHeight);}
$("<div class='nbs-flexisel-nav-left'></div><div class='nbs-flexisel-nav-right'></div>").insertAfter(object);var cloneContent=object.children().clone();object.append(cloneContent);},setEventHandlers:function(){var listParent=object.parent();var childSet=object.children();var leftArrow=listParent.find($(".nbs-flexisel-nav-left"));var rightArrow=listParent.find($(".nbs-flexisel-nav-right"));$(window).on("resize",function(event){methods.setResponsiveEvents();var innerWidth=$(listParent).width();var innerHeight=$(listParent).height();itemsWidth=(innerWidth)/itemsVisible;childSet.width(itemsWidth);object.css({'left':-itemsWidth});var halfArrowHeight=(leftArrow.height())/2;var arrowMargin=(innerHeight/2)-halfArrowHeight;leftArrow.css("top",arrowMargin+"px");rightArrow.css("top",arrowMargin+"px");});$(leftArrow).on("click",function(event){methods.scrollLeft();});$(rightArrow).on("click",function(event){methods.scrollRight();});if(settings.pauseOnHover==true){$(".nbs-flexisel-item").on({mouseenter:function(){canNavigate=false;},mouseleave:function(){canNavigate=true;}});}
if(settings.autoPlay==true){setInterval(function(){if(canNavigate==true)
methods.scrollRight();},settings.autoPlaySpeed);}},setResponsiveEvents:function(){var contentWidth=$('html').width();if(settings.enableResponsiveBreakpoints==true){var largestCustom=responsivePoints[responsivePoints.length-1].changePoint;for(var i in responsivePoints){if(contentWidth>=largestCustom){itemsVisible=settings.visibleItems;break;}
else{if(contentWidth<responsivePoints[i].changePoint){itemsVisible=responsivePoints[i].visibleItems;break;}
else
continue;}}}},sortResponsiveObject:function(obj){var responsiveObjects=[];for(var i in obj){responsiveObjects.push(obj[i]);}
responsiveObjects.sort(function(a,b){return a.changePoint-b.changePoint;});responsivePoints=responsiveObjects;},scrollLeft:function(){if(canNavigate==true){canNavigate=false;var listParent=object.parent();var innerWidth=listParent.width();itemsWidth=(innerWidth)/itemsVisible;var childSet=object.children();object.animate({'left':"+="+itemsWidth},{queue:false,duration:settings.animationSpeed,easing:"linear",complete:function(){childSet.last().insertBefore(childSet.first());methods.adjustScroll();canNavigate=true;}});}},scrollRight:function(){if(canNavigate==true){canNavigate=false;var listParent=object.parent();var innerWidth=listParent.width();itemsWidth=(innerWidth)/itemsVisible;var childSet=object.children();object.animate({'left':"-="+itemsWidth},{queue:false,duration:settings.animationSpeed,easing:"linear",complete:function(){childSet.first().insertAfter(childSet.last());methods.adjustScroll();canNavigate=true;}});}},adjustScroll:function(){var listParent=object.parent();var childSet=object.children();var innerWidth=listParent.width();itemsWidth=(innerWidth)/itemsVisible;childSet.width(itemsWidth);object.css({'left':-itemsWidth});}};if(methods[options]){return methods[options].apply(this,Array.prototype.slice.call(arguments,1));}else if(typeof options==='object'||!options){return methods.init.apply(this);}else{$.error('Method "'+method+'" does not exist in flexisel plugin!');}};})(jQuery);;(function(e){var t=function(t,n){var r=e.extend({},e.fn.nivoSlider.defaults,n);var i={currentSlide:0,currentImage:"",totalSlides:0,running:false,paused:false,stop:false,controlNavEl:false};var s=e(t);s.data("nivo:vars",i).addClass("nivoSlider");var o=s.children();o.each(function(){var t=e(this);var n="";if(!t.is("img")){if(t.is("a")){t.addClass("nivo-imageLink");n=t}t=t.find("img:first")}var r=r===0?t.attr("width"):t.width(),s=s===0?t.attr("height"):t.height();if(n!==""){n.css("display","none")}t.css("display","none");i.totalSlides++});if(r.randomStart){r.startSlide=Math.floor(Math.random()*i.totalSlides)}if(r.startSlide>0){if(r.startSlide>=i.totalSlides){r.startSlide=i.totalSlides-1}i.currentSlide=r.startSlide}if(e(o[i.currentSlide]).is("img")){i.currentImage=e(o[i.currentSlide])}else{i.currentImage=e(o[i.currentSlide]).find("img:first")}if(e(o[i.currentSlide]).is("a")){e(o[i.currentSlide]).css("display","block")}var u=e("<img/>").addClass("nivo-main-image");u.attr("src",i.currentImage.attr("src")).show();s.append(u);e(window).resize(function(){s.children("img").width(s.width());u.attr("src",i.currentImage.attr("src"));u.stop().height("auto");e(".nivo-slice").remove();e(".nivo-box").remove()});s.append(e('<div class="nivo-caption"></div>'));var a=function(t){var n=e(".nivo-caption",s);if(i.currentImage.attr("title")!=""&&i.currentImage.attr("title")!=undefined){var r=i.currentImage.attr("title");if(r.substr(0,1)=="#")r=e(r).html();if(n.css("display")=="block"){setTimeout(function(){n.html(r)},t.animSpeed)}else{n.html(r);n.stop().fadeIn(t.animSpeed)}}else{n.stop().fadeOut(t.animSpeed)}};a(r);var f=0;if(!r.manualAdvance&&o.length>1){f=setInterval(function(){d(s,o,r,false)},r.pauseTime)}if(r.directionNav){s.append('<div class="nivo-directionNav"><a class="nivo-prevNav">'+r.prevText+'</a><a class="nivo-nextNav">'+r.nextText+"</a></div>");e(s).on("click","a.nivo-prevNav",function(){if(i.running){return false}clearInterval(f);f="";i.currentSlide-=2;d(s,o,r,"prev")});e(s).on("click","a.nivo-nextNav",function(){if(i.running){return false}clearInterval(f);f="";d(s,o,r,"next")})}if(r.controlNav){i.controlNavEl=e('<div class="nivo-controlNav"></div>');s.after(i.controlNavEl);for(var l=0;l<o.length;l++){if(r.controlNavThumbs){i.controlNavEl.addClass("nivo-thumbs-enabled");var c=o.eq(l);if(!c.is("img")){c=c.find("img:first")}if(c.attr("data-thumb"))i.controlNavEl.append('<a class="nivo-control" rel="'+l+'"><img src="'+c.attr("data-thumb")+'" alt="" /></a>')}else{i.controlNavEl.append('<a class="nivo-control" rel="'+l+'">'+(l+1)+"</a>")}}e("a:eq("+i.currentSlide+")",i.controlNavEl).addClass("active");e("a",i.controlNavEl).bind("click",function(){if(i.running)return false;if(e(this).hasClass("active"))return false;clearInterval(f);f="";u.attr("src",i.currentImage.attr("src"));i.currentSlide=e(this).attr("rel")-1;d(s,o,r,"control")})}if(r.pauseOnHover){s.hover(function(){i.paused=true;clearInterval(f);f=""},function(){i.paused=false;if(f===""&&!r.manualAdvance){f=setInterval(function(){d(s,o,r,false)},r.pauseTime)}})}s.bind("nivo:animFinished",function(){u.attr("src",i.currentImage.attr("src"));i.running=false;e(o).each(function(){if(e(this).is("a")){e(this).css("display","none")}});if(e(o[i.currentSlide]).is("a")){e(o[i.currentSlide]).css("display","block")}if(f===""&&!i.paused&&!r.manualAdvance){f=setInterval(function(){d(s,o,r,false)},r.pauseTime)}r.afterChange.call(this)});var h=function(t,n,r){if(e(r.currentImage).parent().is("a"))e(r.currentImage).parent().css("display","block");e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").width(t.width()).css("visibility","hidden").show();var i=e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").parent().is("a")?e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").parent().height():e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").height();for(var s=0;s<n.slices;s++){var o=Math.round(t.width()/n.slices);if(s===n.slices-1){t.append(e('<div class="nivo-slice" name="'+s+'"><img src="'+r.currentImage.attr("src")+'" style="position:absolute; width:'+t.width()+"px; height:auto; display:block !important; top:0; left:-"+(o+s*o-o)+'px;" /></div>').css({left:o*s+"px",width:t.width()-o*s+"px",height:i+"px",opacity:"0",overflow:"hidden"}))}else{t.append(e('<div class="nivo-slice" name="'+s+'"><img src="'+r.currentImage.attr("src")+'" style="position:absolute; width:'+t.width()+"px; height:auto; display:block !important; top:0; left:-"+(o+s*o-o)+'px;" /></div>').css({left:o*s+"px",width:o+"px",height:i+"px",opacity:"0",overflow:"hidden"}))}}e(".nivo-slice",t).height(i);u.stop().animate({height:e(r.currentImage).height()},n.animSpeed)};var p=function(t,n,r){if(e(r.currentImage).parent().is("a"))e(r.currentImage).parent().css("display","block");e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").width(t.width()).css("visibility","hidden").show();var i=Math.round(t.width()/n.boxCols),s=Math.round(e('img[src="'+r.currentImage.attr("src")+'"]',t).not(".nivo-main-image,.nivo-control img").height()/n.boxRows);for(var o=0;o<n.boxRows;o++){for(var a=0;a<n.boxCols;a++){if(a===n.boxCols-1){t.append(e('<div class="nivo-box" name="'+a+'" rel="'+o+'"><img src="'+r.currentImage.attr("src")+'" style="position:absolute; width:'+t.width()+"px; height:auto; display:block; top:-"+s*o+"px; left:-"+i*a+'px;" /></div>').css({opacity:0,left:i*a+"px",top:s*o+"px",width:t.width()-i*a+"px"}));e('.nivo-box[name="'+a+'"]',t).height(e('.nivo-box[name="'+a+'"] img',t).height()+"px")}else{t.append(e('<div class="nivo-box" name="'+a+'" rel="'+o+'"><img src="'+r.currentImage.attr("src")+'" style="position:absolute; width:'+t.width()+"px; height:auto; display:block; top:-"+s*o+"px; left:-"+i*a+'px;" /></div>').css({opacity:0,left:i*a+"px",top:s*o+"px",width:i+"px"}));e('.nivo-box[name="'+a+'"]',t).height(e('.nivo-box[name="'+a+'"] img',t).height()+"px")}}}u.stop().animate({height:e(r.currentImage).height()},n.animSpeed)};var d=function(t,n,r,i){var s=t.data("nivo:vars");if(s&&s.currentSlide===s.totalSlides-1){r.lastSlide.call(this)}if((!s||s.stop)&&!i){return false}r.beforeChange.call(this);if(!i){u.attr("src",s.currentImage.attr("src"))}else{if(i==="prev"){u.attr("src",s.currentImage.attr("src"))}if(i==="next"){u.attr("src",s.currentImage.attr("src"))}}s.currentSlide++;if(s.currentSlide===s.totalSlides){s.currentSlide=0;r.slideshowEnd.call(this)}if(s.currentSlide<0){s.currentSlide=s.totalSlides-1}if(e(n[s.currentSlide]).is("img")){s.currentImage=e(n[s.currentSlide])}else{s.currentImage=e(n[s.currentSlide]).find("img:first")}if(r.controlNav){e("a",s.controlNavEl).removeClass("active");e("a:eq("+s.currentSlide+")",s.controlNavEl).addClass("active")}a(r);e(".nivo-slice",t).remove();e(".nivo-box",t).remove();var o=r.effect,f="";if(r.effect==="random"){f=new Array("sliceDownRight","sliceDownLeft","sliceUpRight","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","fade","boxRandom","boxRain","boxRainReverse","boxRainGrow","boxRainGrowReverse");o=f[Math.floor(Math.random()*(f.length+1))];if(o===undefined){o="fade"}}if(r.effect.indexOf(",")!==-1){f=r.effect.split(",");o=f[Math.floor(Math.random()*f.length)];if(o===undefined){o="fade"}}if(s.currentImage.attr("data-transition")){o=s.currentImage.attr("data-transition")}s.running=true;var l=0,c=0,d="",m="",g="",y="";if(o==="sliceDown"||o==="sliceDownRight"||o==="sliceDownLeft"){h(t,r,s);l=0;c=0;d=e(".nivo-slice",t);if(o==="sliceDownLeft"){d=e(".nivo-slice",t)._reverse()}d.each(function(){var n=e(this);n.css({top:"0px"});if(c===r.slices-1){setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed,"",function(){t.trigger("nivo:animFinished")})},100+l)}else{setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed)},100+l)}l+=50;c++})}else if(o==="sliceUp"||o==="sliceUpRight"||o==="sliceUpLeft"){h(t,r,s);l=0;c=0;d=e(".nivo-slice",t);if(o==="sliceUpLeft"){d=e(".nivo-slice",t)._reverse()}d.each(function(){var n=e(this);n.css({bottom:"0px"});if(c===r.slices-1){setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed,"",function(){t.trigger("nivo:animFinished")})},100+l)}else{setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed)},100+l)}l+=50;c++})}else if(o==="sliceUpDown"||o==="sliceUpDownRight"||o==="sliceUpDownLeft"){h(t,r,s);l=0;c=0;var b=0;d=e(".nivo-slice",t);if(o==="sliceUpDownLeft"){d=e(".nivo-slice",t)._reverse()}d.each(function(){var n=e(this);if(c===0){n.css("top","0px");c++}else{n.css("bottom","0px");c=0}if(b===r.slices-1){setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed,"",function(){t.trigger("nivo:animFinished")})},100+l)}else{setTimeout(function(){n.animate({opacity:"1.0"},r.animSpeed)},100+l)}l+=50;b++})}else if(o==="fold"){h(t,r,s);l=0;c=0;e(".nivo-slice",t).each(function(){var n=e(this);var i=n.width();n.css({top:"0px",width:"0px"});if(c===r.slices-1){setTimeout(function(){n.animate({width:i,opacity:"1.0"},r.animSpeed,"",function(){t.trigger("nivo:animFinished")})},100+l)}else{setTimeout(function(){n.animate({width:i,opacity:"1.0"},r.animSpeed)},100+l)}l+=50;c++})}else if(o==="fade"){h(t,r,s);m=e(".nivo-slice:first",t);m.css({width:t.width()+"px"});m.animate({opacity:"1.0"},r.animSpeed*2,"",function(){t.trigger("nivo:animFinished")})}else if(o==="slideInRight"){h(t,r,s);m=e(".nivo-slice:first",t);m.css({width:"0px",opacity:"1"});m.animate({width:t.width()+"px"},r.animSpeed*2,"",function(){t.trigger("nivo:animFinished")})}else if(o==="slideInLeft"){h(t,r,s);m=e(".nivo-slice:first",t);m.css({width:"0px",opacity:"1",left:"",right:"0px"});m.animate({width:t.width()+"px"},r.animSpeed*2,"",function(){m.css({left:"0px",right:""});t.trigger("nivo:animFinished")})}else if(o==="boxRandom"){p(t,r,s);g=r.boxCols*r.boxRows;c=0;l=0;y=v(e(".nivo-box",t));y.each(function(){var n=e(this);if(c===g-1){setTimeout(function(){n.animate({opacity:"1"},r.animSpeed,"",function(){t.trigger("nivo:animFinished")})},100+l)}else{setTimeout(function(){n.animate({opacity:"1"},r.animSpeed)},100+l)}l+=20;c++})}else if(o==="boxRain"||o==="boxRainReverse"||o==="boxRainGrow"||o==="boxRainGrowReverse"){p(t,r,s);g=r.boxCols*r.boxRows;c=0;l=0;var w=0;var E=0;var S=[];S[w]=[];y=e(".nivo-box",t);if(o==="boxRainReverse"||o==="boxRainGrowReverse"){y=e(".nivo-box",t)._reverse()}y.each(function(){S[w][E]=e(this);E++;if(E===r.boxCols){w++;E=0;S[w]=[]}});for(var x=0;x<r.boxCols*2;x++){var T=x;for(var N=0;N<r.boxRows;N++){if(T>=0&&T<r.boxCols){(function(n,i,s,u,a){var f=e(S[n][i]);var l=f.width();var c=f.height();if(o==="boxRainGrow"||o==="boxRainGrowReverse"){f.width(0).height(0)}if(u===a-1){setTimeout(function(){f.animate({opacity:"1",width:l,height:c},r.animSpeed/1.3,"",function(){t.trigger("nivo:animFinished")})},100+s)}else{setTimeout(function(){f.animate({opacity:"1",width:l,height:c},r.animSpeed/1.3)},100+s)}})(N,T,l,c,g);c++}T--}l+=100}}};var v=function(e){for(var t,n,r=e.length;r;t=parseInt(Math.random()*r,10),n=e[--r],e[r]=e[t],e[t]=n);return e};var m=function(e){if(this.console&&typeof console.log!=="undefined"){console.log(e)}};this.stop=function(){if(!e(t).data("nivo:vars").stop){e(t).data("nivo:vars").stop=true;m("Stop Slider")}};this.start=function(){if(e(t).data("nivo:vars").stop){e(t).data("nivo:vars").stop=false;m("Start Slider")}};r.afterLoad.call(this);return this};e.fn.nivoSlider=function(n){return this.each(function(r,i){var s=e(this);if(s.data("nivoslider")){return s.data("nivoslider")}var o=new t(this,n);s.data("nivoslider",o)})};e.fn.nivoSlider.defaults={effect:"random",slices:15,boxCols:8,boxRows:4,animSpeed:500,pauseTime:3e3,startSlide:0,directionNav:true,controlNav:true,controlNavThumbs:false,pauseOnHover:true,manualAdvance:false,prevText:"Prev",nextText:"Next",randomStart:false,beforeChange:function(){},afterChange:function(){},slideshowEnd:function(){},lastSlide:function(){},afterLoad:function(){}};e.fn._reverse=[].reverse})(jQuery);