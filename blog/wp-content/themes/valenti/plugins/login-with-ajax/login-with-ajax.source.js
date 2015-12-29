/* Customize from here downwards */
jQuery(document).ready( function($) {
    //TODO some backwards compatability here -
    if( $('#LoginWithAjax').length > 0 ){
        $('#LoginWithAjax').addClass('lwa');
        $('#LoginWithAjax_Status').addClass('lwa-status');
        $('#LoginWithAjax_Register').addClass('lwa-register');
        $('#LoginWithAjax_Remember').addClass('lwa-remember');
        $('#LoginWithAjax_Links_Remember').addClass('lwa-links-remember');
        $('#LoginWithAjax_Links_Remember_Cancel').addClass('lwa-links-remember-cancel');
        $('#LoginWithAjax_Form').addClass('lwa-form');
    }
    /*
     * links
     * add action input htmls
     */
    //Remember and register form AJAX
    $('form.lwa-form, form.lwa-remember, form.lwa-register-form').submit(function(event){
        //Stop event, add loading pic...
        event.preventDefault();
        var form = $(this);
        var statusElement = form.find('.lwa-status');
        if( statusElement.length == 0 ){
            statusElement = $('<span class="lwa-status"></span>');
            form.prepend(statusElement);
        }
        var ajaxFlag = form.find('.lwa-ajax');
        if( ajaxFlag.length == 0 ){
            ajaxFlag = $('<input class="lwa-ajax" name="lwa" type="hidden" value="1" />');
            form.prepend(ajaxFlag);
        }
        $(form).before('<div class="lwa-loading"></div>');
        //Make Ajax Call
        $.post(form.attr('action'), form.serialize(), function(data){
            lwaAjax( data, statusElement );
            $(document).trigger('lwa_' + data.action, [data, form]);
        }, "jsonp");
        //trigger event
    });
    
    //Catch login actions
    $(document).on('lwa_login', function(event, data, form){
        if(data.result === true){
            //Login Successful - Extra stuff to do
            if( data.widget != null ){
                $.get( data.widget, function(widget_result) {
                    var newWidget = $(widget_result); 
                    form.parent('.lwa').replaceWith(newWidget);
                    var lwaSub = newWidget.find('.').show();
                    var lwaOrg = newWidget.parent().find('.lwa-title');
                    lwaOrg.replaceWith(lwaSub);
                });
            }else{
                if(data.redirect == null){
                    window.location.reload();
                }else{
                    window.location = data.redirect;
                }
            }
        }
    });
    
 
    //Register
    $('.lwa-links-register-inline').click(function(event){
        event.preventDefault();
        $(this).parents('.lwa').find('.lwa-register').show('slow');
    });
    $('.lwa-links-register-inline-cancel').click(function(event){
        event.preventDefault();
        $(this).parents('.lwa-register').hide('slow');
    });
    
    //Visual Effects for hidden items
    //Remember
    $(document).on('click', '.lwa-links-remember', function(event){
        event.preventDefault();
        
        $(this).parents('.lwa').find('.lwa-form').hide();
        $(this).parents('.lwa').find('.lwa-remember').fadeIn('slow');
    });
    $(document).on('click', '.lwa-links-remember-cancel', function(event){
        event.preventDefault();
        $(this).parents('.lwa').find('.lwa-form').delay(400).fadeIn();
        $(this).parents('.lwa').find('.lwa-remember').delay(400).fadeOut();
    });
    
    $(document).on('click', '.cb-back-login', function(event){
        event.preventDefault();
        $(this).parents('.lwa').find('.lwa-form').fadeIn('slow');
        $(this).parents('.lwa').find('.lwa-remember').hide();
    });
    
    //Handle a AJAX call for Login, RememberMe or Registration
    function lwaAjax( data, statusElement ){
        $('.lwa-loading').remove();
        statusElement = $(statusElement);
        if(data.result === true){
            //Login Successful
            statusElement.attr('class','lwa-status lwa-status-confirm').html(data.message); //modify status content
        }else if( data.result === false ){
            //Login Failed
            statusElement.attr('class','lwa-status lwa-status-invalid').html(data.error); //modify status content
            //We assume a link in the status message is for a forgotten password
            statusElement.find('a').click(function(event){
                event.preventDefault();
                $(this).parents('.lwa').find('form.lwa-remember').show('slow');
            });
        }else{  
            //If there already is an error element, replace text contents, otherwise create a new one and insert it
            statusElement.attr('class','lwa-status lwa-status-invalid').html('An error has occured. Please try again.'); //modify status content
        }
    }

});