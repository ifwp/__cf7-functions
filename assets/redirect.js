
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

jQuery(function($){
	$('.wpcf7').on({
		wpcf7mailsent: function(event){
			var __message = __cf7_redirect.message,
				__redirect = '';
			if(typeof __cf7_redirect.messages[event.detail.contactFormId] !== 'undefined'){
				__message = __cf7_redirect.messages[event.detail.contactFormId];
			}
			if(typeof __cf7_redirect.redirects[event.detail.contactFormId] !== 'undefined'){
				__redirect = __cf7_redirect.redirects[event.detail.contactFormId];
				if(__redirect === ''){
					__redirect = jQuery(location).attr('href');
				}
			}
			if(__redirect !== ''){
				jQuery(this).find('form').children().hide();
			} else {
				jQuery(this).find('form').children().not('.wpcf7-response-output').hide();
			}
			jQuery(this).find('form').append('<div class="__redirect_message">' + __message + '</div>');
		},
		wpcf7reset: function(event){
			var __redirect = '';
			if(typeof __cf7_redirect.redirects[event.detail.contactFormId] !== 'undefined'){
				__redirect = __cf7_redirect.redirects[event.detail.contactFormId];
				if(__redirect === ''){
					__redirect = jQuery(location).attr('href');
				}
			}
			if(__redirect !== ''){
				if(jQuery(this).find('input[name="__uniqid"]').length){
					__redirect = __add_query_arg('__referer', jQuery(this).find('input[name="__uniqid"]').val(), __redirect);
				}
				jQuery(location).attr('href', __redirect);
			} else {
				jQuery(this).find('.__redirect_message').remove();
			}
		},
	});
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
