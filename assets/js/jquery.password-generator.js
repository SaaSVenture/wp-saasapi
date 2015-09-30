( function( window, settings ) {

	"use strict";
	var document = window.document;

	var Cache = {
		pass1 : null,
		pass2 : null,
		send : null
	};

	function initialize() {
		Cache.send = document.getElementById('send_password');
		Cache.pass1 = document.getElementById('pass1');
		Cache.pass2 = document.getElementById('pass2');

		document.getElementById('pass-strength-result').insertAdjacentHTML('beforeBegin','<input type="button" name="simple-user-generate-password" id="simple-user-generate-password" value="' + simple_user_password_generator_l10n.Generate + '" class="button" style="width: auto; margin-top: 13px;" /><br />');
		Cache.send.parentNode.insertAdjacentHTML('afterend','<br /><label for="reset_password_notice"><input type="checkbox" id="reset_password_notice" name="reset_password_notice" /> ' + simple_user_password_generator_l10n.PassChange + '</label>');

		jQuery( document.getElementById('simple-user-generate-password') ).on('click',function(){
			jQuery.post( ajaxurl, { action: 'simple_user_generate_password' }, function(response){
				Cache.pass2.value = response;
				Cache.pass1.value = response;
				jQuery(Cache.pass1).trigger('keyup');
				Cache.send.setAttribute('checked','true');
				document.getElementById('reset_password_notice').setAttribute('checked','true');
			});
		});

		jQuery(Cache.pass1).add(Cache.pass2).on('keyup',function(){
			if ( '' == this.value || Cache.pass1.value != Cache.pass2.value ) {
				Cache.send.setAttribute('disabled','disabled');
			} else {
				Cache.send.removeAttribute('disabled');
			}
		});
	}

	initialize();

} )(window);