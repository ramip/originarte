$(document).ready( function(){
	
	$(function(){$(window).scroll(function(){if($(this).scrollTop()!=0){$('#bttop').fadeIn();}else{$('#bttop').fadeOut();}});$('#bttop').click(function(){$('body,html').animate({scrollTop:0},800);});});
	
	$("#productsview a.leo_btn").click( function(){
		if( $(this).attr("rel") == "view-grid" ){
			$("#product_list").addClass("view-grid").removeClass("view-list");
			$("i.icon-th").addClass("active");
			$("i.icon-th-list").removeClass("active");
		} else {
			$("#product_list").addClass("view-list").removeClass("view-grid");
			$("i.icon-th").removeClass("active");
			$("i.icon-th-list").addClass("active");
		}
		return false;
	} );
	
	$(document.body).on('click', '[data-toggle="dropdown"]' ,function(){
		if(!$(this).parent().hasClass('open') && this.href && this.href != '#'){
			window.location.href = this.href;
		}

	});
	
	
	/* off Canvasmenu */
	jQuery("#topnavigation").OffCavasmenu();
	 $('#topnavigation .btn-navbar').click(function () {
     $('body,html').animate({
      scrollTop: 0
     }, 0);
    return false;
   });
	
	
} );

/**************************************/
(function($) {
	$.fn.OffCavasmenu = function(opts) {
		// default configuration
		var config = $.extend({}, {
			opt1: null,
			text_warning_select:'Please select One to remove?',
			text_confirm_remove:'Are you sure to remove footer row?',
			JSON:null
		}, opts);
		// main function
	

		// initialize every element
		this.each(function() {  
			var $btn = $('#topnavigation .btn-navbar');
			var	$nav = null;
			 

			if (!$btn.length) return;
	 	 	var $nav = $('<section id="off-canvas-nav"><nav class="offcanvas-mainnav" ><div id="off-canvas-button"><span class="off-canvas-nav"></span>Close</div></nav></sections>'); 
	 	 	var $menucontent = $($btn.data('target')).find('.megamenu').clone();
			$("body").append( $nav );
	 	 	$("#off-canvas-nav .offcanvas-mainnav").append( $menucontent );
		 
		
 			$('html').addClass ('off-canvas');
			$("#off-canvas-button").click( function(){
				$btn.click();	
			} ); 
			$btn.toggle( function(){
				$("body").removeClass("off-canvas-inactive").addClass("off-canvas-active");
			}, function(){
				$("body").removeClass("off-canvas-active").addClass("off-canvas-inactive");
		 
			} );

		});
		return this;
	}
	
})(jQuery);

/**************************************/

function LeoWishlistCart(id, action, id_product, id_product_attribute, quantity)
{ 
	$.ajax({
		type: 'GET',
		url:	baseDir + 'modules/blockwishlist/cart.php',
		async: true,
		cache: false,
		data: 'action=' + action + '&id_product=' + id_product + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute,
		success: function(data)
		{ 
			if (action == 'add') {
				
				if( $("#wishlistwraning").length <= 0 ) {
				   var html = '';
				   html +=  '<div id="wishlistwraning"><div class="container">';
				   html +=  ' ';
				   html +=  '</div></div>';
				   $("body").append( html );	
				} 
				$("#wishlistwraning .container").html( ' <div class="alert-content"> <button type="button" class="close" data-dismiss="alert">&times;</button><div class="alert">' + data + '</div></div>' );		
				if( $("#wishlistwraning .cart_block_product_name").length > 0 ) {
					$("#wishlistwraning").html('<div class="container"><div class="alert">Done</div></div>').show().delay(1000).fadeOut(300);
				}else {
					$("#wishlistwraning").show();
				}
				
   			}
		
			if($('#' + id).length != 0)
			{ 
				$('#' + id).slideUp('normal');
				document.getElementById(id).innerHTML = data;
				$('#' + id).slideDown('normal');
				
			}
		}
	});
}

