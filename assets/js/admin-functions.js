jQuery(document).ready(function($) {
		
	jQuery("#email").on( "change", function() {
		$.post( "/wp-admin/admin-ajax.php", { email: $(this).val(), action: "account_checkemail" })
	        .done(function( data ) {
	        console.log(data);
	    });
    });
    jQuery("#user").on( "change", function() {
		$.post( "/wp-admin/admin-ajax.php", { user: $(this).val(), action: "account_checkusername" })
	        .done(function( data ) {
	        console.log( data );
	    });
    });
    
	jQuery(".notice-dismiss").live( "click", function() {
		jQuery(".notice.is-dismissible").slideUp(150);
    });
});

(function($){
	
	 $.fn.extend({
        api_ajax: function(target_div,success_msg){
			$.post( "/wp-admin/admin-ajax.php", this.serialize())
				.done(function( data ) {
					var msg_ret = '<div id="message" class="updated notice is-dismissible">';
						msg_ret += '<p>'+success_msg+'</p>';
						msg_ret += '<button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>';
						msg_ret += '</div>';
						$('html, body').animate({
							scrollTop: $(target_div).offset().top - 100
						}, 1000);
						
					$(target_div).fadeOut().html(msg_ret).fadeIn();
					return data;
			});
        }
    });
    
    
}( jQuery ));