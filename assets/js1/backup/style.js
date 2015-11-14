/*$("#user_nav a").click(function() {
    $("#user_nav a").removeClass("user_menu_active");
    $(this).addClass("user_menu_active");
});
    
  
    $(function () {
	$("#nav li a").click(function (e) {
		e.preventDefault();
		$("#nav li a").addClass("active").not(this).removeClass("active");
	});
});
    */

$(document).ready(function(){
    //alert("Hi C. I'm working");
   

    $('nav li').click(function(e){


            e.preventDefault();
            $("nav li a.left_row_custom.left_row_text.left_row_text2.nnnn.active").removeClass("active");
            $("a", this).addClass("active");
    });

    $('#logo').click(function(event){
        $("nav li a.left_row_custom.left_row_text.left_row_text2.nnnn.active").removeClass("active");
    });

    $('.theuser').click(function(event){
        $("nav li a.left_row_custom.left_row_text.left_row_text2.nnnn.active").removeClass("active");
    });


    $(document).snowfall();
    $(document).snowfall({collection : '.collectsnow', flakeCount : 50, minSpeed : 2});


    

});


