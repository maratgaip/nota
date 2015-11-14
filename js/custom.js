/* Fix for older browsers */
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}

(function($){
    
    "use strict";

        $(document).ready(function(){

        /* Loader - Remove it to disable loader
        ================================================== */
        jQuery("body").queryLoader2({
            onComplete: function() {
                $(".ut-loader-overlay").fadeOut(600, "easeInOutExpo", function() {
                    $(this).remove();
                });
                $('#portfolio-wrapper').isotope('reLayout');
                wow.init();
                if( !device.tablet() && !device.mobile() ) {
                    // Play Video on desktops
                    $(".player").mb_YTPlayer();
                } else {
                    $(".herovideo").addClass('no-video');
                }
                
                /* Typed
                ================================================== */
                $(".element").each(function(){
                    var $this = $(this);
                    $this.typed({
                    strings: $this.attr('data-elements').split(','),
                    typeSpeed: 100, // typing speed
                    backDelay: 3000, // pause before backspacing
                    });
                });

            },
            showbar: "on",
            barColor: "#fff",
            textColor: "#fff",
            backgroundColor: "#212121",
            overlayId: 'qLoverlay',
            barHeight: 12,
            percentage: true,
            deepSearch: true,
            completeAnimation: "fade",
            minimumTime: 500  
        });


        /* Header Menu - Show Hide Animation
        ================================================== */
        var $header = $('#header-section');
        var $hclass = $('#header-section').attr('class');

        $( '.top-waypoint' ).each( function(i) {

            var $this = $( this ),
                animClassDown = $this.data( 'animateDown' ),
                animClassUp = $this.data( 'animateUp' );

            $header.attr('class', $hclass + ' header-hide');
            
            $this.waypoint(function(direction) {
                
                if( direction === 'down' && animClassDown ) {
                    $header.removeClass(animClassUp);
                    $header.addClass(animClassDown);
                }
                else if( direction === 'up' && animClassUp ){
                    $header.removeClass(animClassDown);
                    $header.addClass(animClassUp);
                }         
            
            }, { offset: '-1px' } );
        });

        /* Responsive Navigation
        ================================================== */

        function mobile_menu_dim() {
            var nav_width   = $(window).width(),
                nav_height  = $(window).outerHeight();
            $("#navigation").width( nav_width ).height( nav_height - 80 );
        }

        $('#menu-toggle-wrapper').on('click', function(event){
            $('body').toggleClass('mobile');
            $(this).toggleClass('open');
            $('#header-section').toggleClass('fixed-menu');
            $('#navigation').slideToggle(200, function(){
                mobile_menu_dim();
            });
            event.preventDefault();
        });

        $('#navigation li a').on('click', function(event){
            $('#menu-toggle-wrapper').toggleClass('open');
            $('#navigation').slideToggle(200);
        });

         $(window).resize(function() {
            mobile_menu_dim();
         });

        /* Hero height for full and half screen
        ================================================== */
        var windowHeight = $(window).height();
        $('.hero').height( windowHeight );
        $('.hero.mvisible').height( windowHeight - 80 ); // add blog class to hero in order to show menu
        if( !device.tablet() && !device.mobile() ) {
            $('.hero-single').height( windowHeight / 2 );
        } else {
            $('.hero-single').height( windowHeight );
        }

        $(window).resize(function() {
            var windowHeight = $(window).height();
            $('.hero').height( windowHeight );
            $('.hero.mvisible').height( windowHeight - 80 ); // add blog class to hero in order to show menu
            if( !device.tablet() && !device.mobile() ) {
                $('.hero-single').height( windowHeight / 2 );
            } else {
                $('.hero-single').height( windowHeight );
            }
        });

        /* WOW plugin triggers animation.css on scroll
        ================================================== */
        var wow = new WOW(
          {
            boxClass:     'wow', // animated element css class (default is wow)
            offset:       200,   // distance to the element when triggering the animation (default is 0)
            mobile:       false  // trigger animations on mobile devices (true is default)
          }
        );

        /* Tabs
        ================================================== */
         $('.tabs').each(function(){
            var $tabLis = $(this).find('li');            
            var $tabContent = $(this).next('.tab-content-wrap').find('.tab-content');
            $tabContent.hide();
            $tabLis.first().addClass('active').show();
            $tabContent.first().show();
        });

        $('.tabs').on('click', 'li', function(e) {
            var $this = $(this);
            var parentUL = $this.parent();
            var scrollparentURL = $this.parent();

            var tabContent = scrollparentURL.next('.tab-content-wrap');
            parentUL.children().removeClass('active');
            $this.addClass('active');
                
            tabContent.find('.tab-content').hide();
            var showById = $( $this.find('a').attr('href') );
            tabContent.find(showById).fadeIn();            
            e.preventDefault();
        });      

        /* Accordion
        ================================================== */
        $('.accordion').on('click', '.title', function(event) {
            event.preventDefault();
            var $this = $(this);

            if($this.closest('.accordion').hasClass('toggle')) {
                if($this.hasClass('active')) {
                    $this.next().slideUp('normal');
                    $this.removeClass("active");
                }
            } else {
                $this.closest('.accordion').find('.active').next().slideUp('normal');
                $this.closest('.accordion').find('.title').removeClass("active"); 
            }
                   
            if($this.next().is(':hidden') === true) {
                $this.next().slideDown('normal');
                $this.addClass("active");
            }
        });
        $('.accordion .content').hide();
        $('.accordion .active').next().slideDown('normal');

        /* Counter
        ================================================== */
        $('.counter').each(function(){
            var counter = $(this).data('counter');
            var $this = $(this);
            $this.waypoint(function(direction) {
                if( !$(this).hasClass('animated') ) {    
                    $(this).countTo({
                        from: 0,
                        to: counter,
                        speed: 2000,
                        refreshInterval: 100,
                        onComplete: function() {
                            $(this).addClass('animated');
                        }
                    });
                }
            } , { offset: '100%' } );
        });

        /* Scroll to Main Menu
        ================================================== */
        $('#navigation a[href*=#]').click( function(event) {
            var $this = $(this);
            var offset = -80;
            if($this.parent().is(':nth-child(2)')) {
                offset = 2; // for second child dont do offset
            };
            $.scrollTo( $this.attr('href') , 650, { easing: 'swing' , offset: offset , 'axis':'y' } );
            event.preventDefault();
        });

        /* Scroll to Element on Page
        ================================================== */
        $('a#to-blog').click( function(event) {
            event.preventDefault();
            $.scrollTo( $('#blog') , 1250, {  offset: 1 , 'axis':'y' } );
        });

         $('.hero-btn').click( function(event) {
            var $this = $(this);
            var offset = -80;
            if($this.attr('href') == '#about-us' || $('.nomenu').is(':visible')) {
                offset = 0; // for first section dont do offset
            };
            $.scrollTo( $this.attr('href') , 650, { easing: 'swing' , offset: offset , 'axis':'y' } );
            event.preventDefault();
        });

        /* Add active class for each nav depending on scroll
        ================================================== */
        $('section').each(function() {
            $(this).waypoint( function( direction ) {
                if( direction === 'down' ) {
                    var containerID = $(this).attr('id');
                    /* update navigation */
                    $('#navigation a').removeClass('active');
                    $('#navigation a[href*=#'+containerID+']').addClass('active');
                }
            } , { offset: '80px' } );
            
            $(this).waypoint( function( direction ) {
                if( direction === 'up' ) {
                    var containerID = $(this).attr('id');
                    /* update navigation */
                    $('#navigation a').removeClass('active');
                    $('#navigation a[href*=#'+containerID+']').addClass('active');
                }          
            } , { offset: function() { return -$(this).height() - 80; } });
        });

        /* Cycle Through Images on Portfolio
        ================================================== */
        $('.gallery .image-holder').each(function(){
            var $this = $(this);
            
            $this.find('img:first').clone().prependTo(this).addClass('base');
            $this.find('img:eq(1)').addClass('active');
            
            function cycleImages(){
                var $active = ($this.find('.active').length > 0) ? $this.find('.active') : $this.find('img:eq(1)');
                var $next = ($this.find('.active').next().length > 0) ? $this.find('.active').next().not('.base') : $this.find('img').not('.base').first();
                //move the next image up the pile
                $next.css('z-index',2);
                //fade out the top image
                var random = Math.ceil(Math.random() * 10000);
                $active.delay(random).fadeOut(2500,function(){
                //reset the z-index and unhide the image
                $active.css('z-index',1).show().removeClass('active');
                //make the next image the top one
                $next.css('z-index',3).addClass('active');
                cycleImages();
                });
            }
            cycleImages();
        });
        
        /* Notification Messages
        ================================================== */
        $('.success,.errormsg,.notice,.general').on('click',function(){
            var $this = $(this);
            $this.hide();
        });

        /* PARALAX and BG Video
        ================================================== */
        if( !device.tablet() && !device.mobile() ) {

            $('section[data-type="parallax"]').each(function(){
                $(this).parallax("50%", 0.4);
            });

            /* fixed background on mobile devices */
            $('section[data-type="parallax"]').each(function(index, element) {
                $(this).addClass('fixed');
            });

            $('.bannercontainer').addClass('hero-fixed');
            $('#home-content').addClass('hero-fixed');
                
        }


        /* OWL Carousel
        ================================================== */
        $(".home-slider").owlCarousel({
            transitionStyle: "backSlide",
            slideSpeed: 350,
            singleItem: true,
            autoHeight: true,
            navigation: true,
            navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"]
        });

        $(".mfp-iframe").magnificPopup({
            mainClass: 'mfp-carousel',
            gallery: {
                enabled: false
            }
        });

        function twitterCarousel() {
            $("#twitter-container").owlCarousel({
              slideSpeed : 300,
              paginationSpeed : 400,
              singleItem:true,
              autoPlay: 5000,
              autoHeight: true
            });
        }

        $("#testimonial-container").owlCarousel({
          slideSpeed : 300,
          paginationSpeed : 400,
          singleItem:true,
          autoPlay: 5000,
          autoHeight: true
        });

        $(".slider").owlCarousel({
          slideSpeed : 300,
          paginationSpeed : 400,
          singleItem:true,
          autoPlay: 5000,
          autoHeight: true
        });

        $(".clients").owlCarousel({
            autoPlay: 2500,
            stopOnHover: true,
            items: 4,
            itemsDesktop: [1199, 4],
            itemsTabletSmall: [768, 3],
            itemsMobile: [480, 2],
            pagination: false,
            navigation: false,
        });

        /* Circular and Line Progress bar 
        ================================================== */
        $('.chart').each(function(){
            var $this = $(this);
            var color = $(this).data('scale-color');

            setTimeout(function() {
                $this.filter(':visible').waypoint(function(direction) {
                    $(this).easyPieChart({
                        barColor: color,
                        trackColor: '#c4c4c4',
                        onStep: function(from, to, percent) {
                            jQuery(this.el).find('.percent').text(Math.round(percent));
                        }
                    });
                } , { offset: '100%' } );
            }, 500);

        });

        $('.progress-bar > span').each(function(){
            var $this = $(this);
            var width = $(this).data('percent');
            $this.css({
                'transition' : 'width 1.5s'
            });
            
            setTimeout(function() {
                $this.filter(':visible').waypoint(function(direction) {
                    if( direction === 'down' ) {
                        $this.css('width', width + '%');
                    }
                } , { offset: '100%' } );
            }, 500);
        });

        /* Google Map 
        ================================================== */
        google.maps.event.addDomListener(window, 'load', init);

        function init() {
        var mapOptions = {
        scrollwheel: false,
        zoom: 16,
        center: new google.maps.LatLng(44.7679455, 17.1909169), // New York
        styles: [{featureType:"landscape",stylers:[{saturation:-100},{lightness:65},{visibility:"on"}]},{featureType:"poi",stylers:[{saturation:-100},{lightness:51},{visibility:"simplified"}]},{featureType:"road.highway",stylers:[{saturation:-100},{visibility:"simplified"}]},{featureType:"road.arterial",stylers:[{saturation:-100},{lightness:30},{visibility:"on"}]},{featureType:"road.local",stylers:[{saturation:-100},{lightness:40},{visibility:"on"}]},{featureType:"transit",stylers:[{saturation:-100},{visibility:"simplified"}]},{featureType:"administrative.province",stylers:[{visibility:"off"}]/**/},{featureType:"administrative.locality",stylers:[{visibility:"off"}]},{featureType:"administrative.neighborhood",stylers:[{visibility:"on"}]/**/},{featureType:"water",elementType:"labels",stylers:[{visibility:"on"},{lightness:-25},{saturation:-100}]},{featureType:"water",elementType:"geometry",stylers:[{hue:"#ffff00"},{lightness:-25},{saturation:-97}]}]
        };
        var contentString = '<div id="mapcontent">'+
                            '<p>Yup, we are here!</p></div>';
        var infowindow = new google.maps.InfoWindow({
            maxWidth: 320,
            content: contentString
        });
        var mapElement = document.getElementById('map');
        var map = new google.maps.Map(mapElement, mapOptions);
        var image = new google.maps.MarkerImage('images/elements/pin.png',
            null, null, null, new google.maps.Size(116,164))

        var myLatLng = new google.maps.LatLng(44.7679455, 17.1909169);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: image,
            title: 'Hello World!'
        });

        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
        });

        }

        /* Contact Form
        ================================================== */
        (function(e){function n(e,n){this.$form=e;this.indexes={};this.options=t;for(var r in n){if(this.$form.find("#"+r).length&&typeof n[r]=="function"){this.indexes[r]=n[r]}else{this.options[r]=n[r]}}this.init()}var t={_error_class:"error",_onValidateFail:function(){}};n.prototype={init:function(){var e=this;e.$form.on("submit",function(t){e.process();if(e.hasErrors()){e.options._onValidateFail();t.stopImmediatePropagation();return false}return true})},notEmpty:function(e){return e!=""?true:false},isEmail:function(e){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(e)},isUrl:function(e){var t=new RegExp("(^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|(www\\.)?))[\\w-]+(\\.[\\w-]+)+([\\w-.,@?^=%&:/~+#-]*[\\w@?^=%&;/~+#-])?","gim");return t.test(e)},elClass:"",setClass:function(e){this.elClass=e},process:function(){this._errors={};for(var t in this.indexes){this.$el=this.$form.find("#"+t);if(this.$el.length){var n=e.proxy(this.indexes[t],this,e.trim(this.$el.val()))();if(this.elClass){this.elClass.toggleClass(this.options._error_class,!n);this.elClass=""}else{this.$el.toggleClass(this.options._error_class,!n)}if(!n){this._errors[t]=n}}this.$el=null}},_errors:{},hasErrors:function(){return!e.isEmptyObject(this._errors)}};e.fn.isValid=function(t){return this.each(function(){var r=e(this);if(!e.data(r,"is_valid")){e.data(r,"is_valid",new n(r,t))}})}})(jQuery)
        
        //ajax contact form
        $('#forms').isValid({
            'name' : function(data) {
                this.setClass(this.$el.parent());
                return this.notEmpty(data);
            },
            'email' : function(data) {
                this.setClass(this.$el.parent());
                return this.isEmail(data);
            },
            'message' : function(data) {
                this.setClass(this.$el.parent());
                return this.notEmpty(data);
            }
        }).submit(function(e){
            e.preventDefault();
            
            var $this = $(this);
            
            $this.find('.notification')
                .attr('class', 'notification');
            $this.find('.notification').text('');
            
            $this.find('.loading').show();
            
            $.ajax({
                'url' : $(this).attr('action'),
                'type' : 'POST',
                'dataType' : 'json',
                'data' : $(this).serialize()
            }).done(function(response){
                $this.find('.loading').hide();
                if(typeof response.type != 'undefined' && typeof response.message != 'undefined') {
                    $this.find('.notification')
                        .addClass(response.type)
                        .text(response.message);

                    if(response.type == 'success') {
                        $this.find('input[type="text"], input[type="email"], textarea').val('');
                    }
                }
            });
            //return false;
        });

        /* Placeholder fix for IE
        ================================================== */
        if( !Modernizr.csstransforms3d ) {
            $('[placeholder]').focus(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                    input.removeClass('placeholder');
                }
            }).blur(function() {
                var input = $(this);
                if (input.val() == '' || input.val() == input.attr('placeholder')) {
                    input.addClass('placeholder');
                    input.val(input.attr('placeholder'));
                }
            }).blur().parents('form').submit(function() {
                $(this).find('[placeholder]').each(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                })
            });
        }

        /* Filter for What We Do section
        ================================================== */
        $('.filter').each(function(){
            var tabLis = $(this).find('li');            
            var tabContent = $('#option-section').find('.row');
            tabContent.hide();
            tabLis.first().addClass('active');
            tabContent.first().show();
        });

        $('.filter').on('click', 'li', function(e) {
            var $this = $(this);
            var parentUL = $this.parent();
            var tabContent = $('#option-section');

            if($this.hasClass('active')) {
                return false;
            }

             parentUL.children().removeClass('active');
            $this.addClass('active');

            tabContent.find('.row').hide();
            var showById = $( $this.find('a').attr('href') );
            tabContent.find(showById).fadeIn(400, function(){
                
                $(this).find('.progress-bar > span').each(function(){
                
                    var $this = $(this);
                    var width = $this.data('percent');
                    $this.css('width', width + '%');
                });
                
                $(this).find('.chart').each(function(){
                    var $this = $(this);
                    var color = $this.data('scale-color');
                    
                    $this.easyPieChart({
                        barColor: color,
                        trackColor: '#c4c4c4',
                        onStep: function(from, to, percent) {
                            jQuery(this.el).find('.percent').text(Math.round(percent));
                        }
                    });
                    
                });
            });            

            e.preventDefault();
        });



        /* Filter for Home Blog Posts
        ================================================== */
        $('.filter-target').each(function(){
            var $this = $(this);
            var filterLis = $this.find('li');    
            var $target = $('#' + $this.attr('data-target'));
            
            if(!$target.length) {
                e.preventDefault();
                return;
            }
            
            var children = $target.children();
            
            children.hide();
            
            filterLis.on('click', function(e){
                e.preventDefault();
                
                var $this = $(this);
                if($this.hasClass('active')) {
                    return false;
                }
                
                var $a = $this.find('a');
                filterLis.removeClass('active');
                $this.addClass('active');
                
                var target_class = $a.attr('data-target-class');
                if(target_class == '*') {
                    children.fadeIn();
                } else {
                    children.hide();
                
                    $target.find('.'+$a.attr('data-target-class')).fadeIn();
                }

                wow.scrollHandler();

                return false;
            });
            
            setTimeout(function(){
                filterLis.filter('.active')
                    .removeClass('active').find('a').trigger('click');
            },100);
        });

        /* Portfolio Ajax
        ================================================== */
        $('#portfolio-wrapper').magnificPopup({
            delegate: '.block:not(.isotope-hidden) .portfolio-hover',
            closeMarkup:'', 
            closeBtnInside: false, 
            closeOnBgClick:false,
            type: 'ajax',
            fixedContentPos:false,
            mainClass: 'mfp-fade',
            midClick: true,
            gallery: {
                enabled: true, 
                preload: [0,2],
                arrowMarkup:'',
                navigateByImgClick: true,
                tCounter: '<span class="mfp-counter">%curr% of %total%</span>' // markup of counter
            },
            callbacks: {
                parseAjax: function(mfpResponse) {
                    mfpResponse.data = $(mfpResponse.data).siblings('#portfolio-single');
                },
                change: function() {
                    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ){
                       // $.magnificPopup.instance.next = function () {return false}; 
                        //$.magnificPopup.instance.prev = function () {return false}; 
                    }
                },
                open: function() {
                    var main = $('#home-content, .home-slider, .hero, footer, #header-section, .mbYTP_wrapper');
                    main.addClass('hide');
                    
                    var that = this;
                    
                    //listen for click events
                    that.contentContainer.on('click', '.close, .prev, .next', function(e) {
                        e.preventDefault();
                        
                        var $this = $(this);
                        
                        if ($this.hasClass('close')) {
                            that.close();
                        } else if($this.hasClass('prev')) {
                            that.prev();    
                        } else if($this.hasClass('next')) {
                            that.next();
                        }
                        
                        return false;
                    });
                },
                close: function() {
                    var main = $('#home-content, .home-slider, .hero, footer, #header-section, .mbYTP_wrapper');
                    main.removeClass('hide');
                },
                afterClose: function() {
                    $(window).scrollTop(this.st.mainEl.offset().top - 150);
                }
            }

        });


        /* Isotope Portfolio
        ================================================== */
        var $container = $('#portfolio-wrapper');
        $container.isotope({
            itemSelector: '.block',
            layoutMode: 'sloppyMasonry',
            filter: '*',
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
        });
     
        $('#port-filter a').click(function(){
            $('#port-filter li.active').removeClass('active');
            $(this).parent().addClass('active');
     
            var selector = $(this).attr('data-filter');
            $container.isotope({
                filter: selector,
                animationOptions: {
                    duration: 750,
                    easing: 'linear',
                    queue: false
                }
             });
             return false;
        });

        /*
        * get twitter feeds and init carousel
        */
        $.getJSON( "twitter/get_tweets.php", function( data ) {
            
            if(data) {
                var tweets = '';
            
                $.each(data, function(){
                    var tweet = '<div class="tweet">';
                    tweet += '<span class="time">about ' + this.time_ago + ' ago</span>';
                    tweet += '<span class="one-tweet">' + this.text + '</span>';
                    tweet += '<ul class="tweet-actions">' + 
                                '<li><a target="_blank" href="' + this.actions.reply + '">Reply</a></li>' + 
                                '<li><a target="_blank" href="' + this.actions.retweet + '">Retweet</a></li>' + 
                                '<li><a target="_blank" href="' + this.actions.favorite + '">Favorite</a></li>' + 
                              '</ul>'; 
                    tweet += '</div>';  
                
                    tweets += tweet;
                });
                
                $('#twitter-container').append(tweets);
                
                
            }

        }).always(function() {
            twitterCarousel();
       });

    });

    /* Count To Function
    ================================================== */
    $.fn.countTo = function (options) {
        options = options || {};
        
        return $(this).each(function () {
            // set options for current element
            var settings = $.extend({}, $.fn.countTo.defaults, {
                from:            $(this).data('from'),
                to:              $(this).data('to'),
                speed:           $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals:        $(this).data('decimals')
            }, options);
            
            // how many times to update the value, and how much to increment the value on each update
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
                increment = (settings.to - settings.from) / loops;
            
            // references & variables that will change with each update
            var self = this,
                $self = $(this),
                loopCount = 0,
                value = settings.from,
                data = $self.data('countTo') || {};
            
            $self.data('countTo', data);
            
            // if an existing interval can be found, clear it first
            if (data.interval) {
                clearInterval(data.interval);
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);
            
            // initialize the element with the starting value
            render(value);
            
            function updateTimer() {
                value += increment;
                loopCount++;
                
                render(value);
                
                if (typeof(settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value);
                }
                
                if (loopCount >= loops) {
                    // remove the interval
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;
                    
                    if (typeof(settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value);
                    }
                }
            }
            
            function render(value) {
                var formattedValue = settings.formatter.call(self, value, settings);
                $self.html(formattedValue);
            }
        });
    };
    
    $.fn.countTo.defaults = {
        from: 0,               // the number the element should start at
        to: 0,                 // the number the element should end at
        speed: 1000,           // how long it should take to count between the target numbers
        refreshInterval: 100,  // how often the element should be updated
        decimals: 0,           // the number of decimal places to show
        formatter: formatter,  // handler for formatting the value before rendering
        onUpdate: null,        // callback method for every time the element is updated
        onComplete: null       // callback method for when the element finishes updating
    };
    
    function formatter(value, settings) {
        return value.toFixed(settings.decimals);
    }

})(jQuery);