/*
 * Anthony & Sylvan Site Scripts
 * Version: 0.0.0
 *
 * Author: Chris Rivers
 * crivers@mghus.com
 *
 * Changelog: 
 * Version: 1.0.0
 *  Init Build
 *
 */

var startedInstagram = 0;

function goToByScroll(id, speed){
	$('html,body').animate({scrollTop: ($(".slide."+id).offset().top) }, speed);
}

$(document).ready(function(){
	
	// Navigation Animation
	$(window).scroll(function(){

		if( $("#header").hasClass("minimized") ){

			if( $(this).scrollTop() < 800 ){
				$("#header").stop().animate({ top: "0" });
				$("#header").removeClass("minimized");
			}

		} else {
			if($(this).scrollTop() > 800){
				$("#header").stop().animate({ top: "0px" });
				$("#header").addClass("minimized");
			}
			
			if($(this).scrollTop() > 200){
				$("#header .scroll-glyph").animate({ top: "1000px" }, 1000, function(){
					$("#header .scroll-glyph").remove();
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
	$("#middle .slide").not(".no-bg").each(function(){
		$(this).css("background-image", "url("+ $(this).find(".slide-bg img").attr("src") +")");
	});
	
	// Navigation
	var headerNavSpeed = parseInt($("#header .navigation").attr("rel"));
	if( headerNavSpeed == ""){ headerNavSpeed = 1000; } // Default
		
	$("#header .navigation a, #header .logo a, a.scrollTo").click(function(){
		goToByScroll( ($(this).attr("rel")), headerNavSpeed );
		return false;
	});
	
	// Scroll Glyph
	var interval = setInterval(animateGlyph, 700);
	function animateGlyph() { $("#header .scroll-glyph").toggleClass('on'); }
});
