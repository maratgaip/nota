jQuery(function($) {
	if( $('.blu-color-picker-field').length ){
	    $('.blu-color-picker-field').each(function(i){
	    	var $that = $(this);
		    $(this).wpColorPicker();
		});
	}
	if($('.submit-add-to-menu').length){
		$('.submit-add-to-menu').click(function(){
			setTimeout(function() {
				$('.blu-color-picker-field').each(function(i){
			    	var $that = $(this);
				    $(this).wpColorPicker();
				});
			}, 3000);
		});
	}
});