
$(window).load(function(){
    	$(".loader").fadeOut("slow");
 });
/*
jQuery(document).ready(function(){
  
  delayShow();
});
 
function delayShow() {
  var secsd = 1000;
  setTimeout('initFadeIn1()', secsd);
}

function initFadeIn1(){
	jQuery("body").css("visibility","visible");
	jQuery("body").css("display", "none");
	jQuery("body").fadeIn(5000);
}*/

var startedInstagram = 0;

function goToByScroll(id, speed){
	$('html,body').animate({scrollTop: ($(".slide."+id).offset().top) }, speed);
}


$(document).ready(function(){

	
	
	// Navigation Animation
	$(window).scroll(function(){

		if( $("#header2").hasClass("minimized") ){

			if( $(this).scrollTop() < 800 ){
				$("#header2").stop().animate({ top: "0" });
				$("#header2").removeClass("minimized");
                 $("#header2").css("background", "rgba(255, 255, 255, 0)");
			}

		} else {
			if($(this).scrollTop() > 800){
				$("#header2").stop().animate({ top: "0px" });
				$("#header2").addClass("minimized");
                $("#header2").animate({backgroundColor: "#141318" });
			}
			
			if($(this).scrollTop() > 200){
				$("#header2 .scroll-glyph").animate({ top: "1000px" }, 1000, function(){
					$("#header2 .scroll-glyph").remove();
					clearTimeout(interval);
				});
			}
		}
		
		if($(this).scrollTop() > 9452){
			if( startedInstagram == 0 ){
				// Instagram Theatre
				$('.instagram-theatre').instagramTheatre({
					mode : 'popular',
				});
				
				startedInstagram = 1;
			}
		}

	});
	
	// Adds flexiblity for slide backgrounds.
	$("#middle2 .slide").not(".no-bg").each(function(){
		$(this).css("background-image", "url("+ $(this).find(".slide-bg img").attr("src") +")");
	});
	
	// Navigation
	var header2NavSpeed = parseInt($("#header2 .navigation").attr("rel"));
	if( header2NavSpeed == ""){ header2NavSpeed = 1000; } // Default
		
	$("#header2 .navigation a, #header2 .logo a, a.scrollTo").click(function(){
		goToByScroll( ($(this).attr("rel")), header2NavSpeed );
		return false;
	});
	
	// Scroll Glyph
	var interval = setInterval(animateGlyph, 700);
	function animateGlyph() { $("#header2 .scroll-glyph").toggleClass('on'); }
    
    //scrool smooth
    $(".scroll1").click(function(event){
         event.preventDefault();
         //calculate destination place
         var dest=0;
         if($(this.hash).offset().top > $(document).height()-$(window).height()){
              dest=$(document).height()-$(window).height();
         }else{
              dest=$(this.hash).offset().top;
         }
         //go to destination
         $('html,body').animate({scrollTop:dest}, 3000,'swing');
     });
    
});
