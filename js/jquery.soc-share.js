(function($, window){
    
    "use strict";

    $.fn.socShare = function(opts) {
    	var $this = this;
    	var $win = $(window);
    	
    	opts = $.extend({
    		attr : 'href',
    		facebook : false,
    		google_plus : false,
    		twitter : false,
    		linked_in : false,
    		pinterest : false,
    		email : false
    	}, opts);
    	
    	for(var opt in opts) {
    		switch (opt) {
    			case 'facebook':
    				var url = 'https://www.facebook.com/sharer/sharer.php?u=';
    				var name = 'Facebook';
    				_popup(url, name, opts[opt], 400, 640);
    				break;
    			
    			case 'twitter':
    				var url = 'https://twitter.com/share?url=';
    				var name = 'Twitter';
    				_popup(url, name, opts[opt], 440, 600);
    				break;
    			
				case 'google_plus':
    				var url = 'https://plus.google.com/share?url=';
    				var name = 'Google+';
    				_popup(url, name, opts[opt], 600, 600);
    				break;
    			
    			case 'linked_in':
    				var url = 'http://www.linkedin.com/shareArticle?mini=true&url=';
    				var name = 'LinkedIn';
    				_popup(url, name, opts[opt], 570, 520);
    				break;
				
				case 'pinterest':
    				var url = 'http://www.pinterest.com/pin/find/?url=';
    				var name = 'Pinterest';
    				_popup(url, name, opts[opt], 500, 800);
    				break;
    				
    			case 'email':
					var hidden = $('.hidden');
					
					$(opts[opt]).on('click', function(e){
						e.preventDefault();
						
						hidden.addClass('mod');
						hidden.find('textarea').val($(this).attr('href'));
						
						return false;
					});
					
					hidden.on('click', '.close', function(e){
						e.preventDefault();
						
						hidden.removeClass('mod');
						
						hidden.find('.error').removeClass('error');
						
						hidden.find('input, textarea').val('');
						
						hidden.find('.notif').html('');
						
						return false;
					});
					
					$( $(opts[opt]).attr('data-form') )
						.isValid({
							to : function(data) {
								return this.isEmail(data);
							},
							from : function(data) {
								return this.isEmail(data);
							},
							subject : function(data) {
								return this.notEmpty(data);
							},
							message : function(data) {
								return this.notEmpty(data);
							}
						})
						.submit(function(e){
							e.preventDefault();
							
							var $this = $(this);
							
							$this.find('.notif').html('');
							
							app.sendEmail($this.attr('action'), $this.serialize()).done(function(resp) {
								if(resp.success == true) {
									if(typeof resp.data.error != 'undefined') {
										$this.find('.notif').html('<span class="red">Error sending email</span>');
									} else if(typeof resp.data.sent != 'undefined') {
										$this.find('.notif').html('Succesfully sent email');
									} 
								}
							});
							
							return false;
						});
					
    				break;
				default:
					break;
    		}
    	}
		
		function isUrl(data) {

            var regexp = new RegExp( '(^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|(www\\.)?))[\\w-]+(\\.[\\w-]+)+([\\w-.,@?^=%&:/~+#-]*[\\w@?^=%&;/~+#-])?', 'gim' );

            return regexp.test(data);

        }
    	
    	function _popup(url, name, opt, height, width) {
    		if(opt !== false && $this.find(opt).length) {				
				$this.on('click', opt, function(e){
					e.preventDefault();
					
					var top = (screen.height/2) - height/2;
					var left = (screen.width/2) - width/2;
					var share_link = $(this).attr(opts.attr);
					
					if(!isUrl(share_link)) {
						share_link = window.location.href;
					}
					
					window.open(
						url+encodeURIComponent(share_link),
						name,
						'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='+height+',width='+width+',top='+top+',left='+left
					);
					
					return false;
				});
			}
    	}
    	
    	
    	
    	return;
	};
		

})(jQuery, window);