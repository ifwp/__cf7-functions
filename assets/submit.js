
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

jQuery(function($){
	$('.wpcf7').on('keydown', 'input[type!="textarea"]', function(event){
		switch(event.keyCode){
			case 13:
				event.preventDefault();
				break;
			case 32:
				if($(this).is('.wpcf7-submit')){
					event.preventDefault();
				}
				break;
		}
	});
	$('.wpcf7').on('wpcf7submit', function(){
		$('.wpcf7-submit').removeClass('disabled');
	});
	$('.wpcf7-submit').on('click', function(){
		$('.wpcf7-submit').addClass('disabled');
	});
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
