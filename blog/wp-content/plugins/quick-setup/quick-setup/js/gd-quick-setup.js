/*jslint nomen: true, plusplus: true, sloppy: true, vars: true, white: true, browser: true */

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

jQuery(document).ready(function($) {

	/*
	 * Step 1
	 */

	// Save the site type, then submit
    $("#form-step1 input[type=submit]").on( 'mousedown', function() {
        $("#site_type").val( $(this).data("site-type") );
    });

	/*
	 * Step 2
	 */

	// 
    $("#form-step2 a.form2-submit").on( 'mousedown', function() {
        $("#theme_slug").val( $(this).data("theme-slug") );
        $("#form-step2").trigger("submit");
    });

	/*
	 * Step 3
	 */

	// "Plugin settings" pane
    $(".q-setup-expand").on( 'click', function(){
        $(this).toggleClass("q-setup-contract");
        $(".q-setup-optionals-list").slideToggle("300");
    });

	// Enforce the "I understand this will nuke my site" checkbox
	$("#form-step3 #q-setup-final-warning").on( 'click', function() {
        if ($(this).is(":checked")) {
			$("#form-step3 a.gd-quicksetup-wizard-submit").removeClass("button-disabled");
			$("#form-step3 a.gd-quicksetup-wizard-submit").removeAttr("disabled");
		} else {
			$("#form-step3 a.gd-quicksetup-wizard-submit").addClass("button-disabled");
			$("#form-step3 a.gd-quicksetup-wizard-submit").attr("disabled", true);
		}
    });

	// Pre-load ajax-loader.gif
	var ajax_loader = new Image();
	ajax_loader.src = gd_quicksetup_img_dir + '/ajax-loader.gif';

	// Disable submit button (don't double-submit)
    $("#form-step3 a.gd-quicksetup-wizard-submit").on( "click", function() {
		if ( $(this).hasClass("button-disabled") ) {
			return;
		}
        $("#form-step3 a.gd-quicksetup-wizard-submit").addClass("button-disabled");
		$("#form-step3 a.gd-quicksetup-wizard-submit").attr("disabled", true);
		$("#form-step3 #q-setup-final-warning").prop("disabled", true);
		$("#form-step3 a.gd-quicksetup-wizard-submit").html('<img style="position: relative; top: 3px;" src="' + ajax_loader.src + '" />&nbsp;' + objectL10n.building );
		$("a.gd-quicksetup-wizard-start-over").hide();
		$("#form-step3").trigger("submit");
    });

	// Add/remove feature functionality
    $("div.q-setup-steps-wrap").on( "click", "a.qs-add-remove", function() {
        if ( objectL10n.add === $(this).html() ) {
            $(this).html( objectL10n.remove );
            $(this).parents("div.page-container").find("input").prop("disabled", false);
            $(this).parents("div.page-container").find("textarea").prop("disabled", false);
            $(this).parents("div.page-container").find("input[type=hidden]").each( function( idx, el ) {
                if ( $(el).attr("name").match(/enabled[\d]/) ) {
                    $(el).val("true");
                }
            });
        } else {
            $(this).html( objectL10n.add );
            $(this).parents("div.page-container").find("input").prop("disabled", true);
            $(this).parents("div.page-container").find("textarea").prop("disabled", true);
            $(this).parents("div.page-container").find("input[type=hidden]").each( function( idx, el ) {
                if ( $(el).attr("name").match(/enabled[\d]/) ) {
                    $(el).val("false");
                }
            });
        }
        $(this).parents("div.page-container").find("ul a").toggleClass("button-disabled");
        $(this).toggleClass("button-primary");
    });

	// Remove files from gallery
    $("ul.q-setup-page-info").on( "click", "a.q-setup-remove-gallery-file", function() {
        if ( $(this).hasClass("button-disabled") ) {
            return false;
        }
        $(this).parent().fadeOut();
        $(this).parent().remove();
    });

	// Add files to gallery
    $("a.qs-add-images").on( "click", function() {
        if ( $(this).hasClass("button-disabled") ) {
            return false;
        }
        var idx = 0;
        var el = $(this).parents("ul.q-setup-page-info").find("input[type=file]:last");
        var _idx = $(el).data("idx");
        idx = $(el).data("index");
        idx++;
        var $li = $(this).parent();
        var i = 0;
        for ( i = idx ; i < idx + 3 ; i++ ) {
            $li.before($("<li style=\"display: none;\"><label for=\"upload_image_" + _idx + "_" + i + "\"><input id=\"upload_image_" + _idx + "_" + i + "\" name=\"upload_image_" + _idx + "_" + i + "\" data-idx=\"" + _idx + "\" data-index=\"" + i + "\" type=\"file\" size=\"36\" value=\"\" /></label> <a href=\"javascript:;\" class=\"q-setup-remove-gallery-file\">" + objectL10n.remove + "</a></li>"));
        }
        $(this).parents("ul.q-setup-page-info").find("li").fadeIn();
    });

	// Add custom pages
    $("a.qs-add-custom-page").on( "click", function() {
        var idx = 0;
        $("div.q-setup-item").each( function( index, el ) {
            if ( undefined !== $(el).data("index") && parseInt( $(el).data("index") ) > idx ) {
                idx = $(el).data("index");
            }
        });
        var $template = $("#qs-custom-page-template").clone().html();
        $template = $template.replace(/\{\{idx\}\}/g, (idx+1));
        $("#q-setup-panel-" + idx).after($template);
        $("#q-setup-panel-" + (idx+1)).fadeIn();
        $("#q-setup-panel-" + (idx+1)).find("input[type=text]").placeholder();
        $("#q-setup-panel-" + (idx+1)).find("textarea").placeholder();
		$("#q-setup-panel-" + (idx+1)).find("input[type=hidden]").each( function( idx, el ) {
			if ( $(el).attr("name").match(/enabled/) ) {
				$(el).val("true");
			}
		});
    });

	/**
	 * General
	 */

	// If the user clicks an input that's inside a <label> markup, it will toggle
	// the control connected to the <label>.  This prevents that, as long as the
	// event target is the input.  Needed for the Google Analytics input.
    $("label").on('click', 'input[type=text]', function(e) {
        if( e.target.nodeName === 'INPUT') {
            e.preventDefault();
            return false;
        }
    });

	// Tiptip!
    $(".q-setup-info-icn").tipTip();
	
	// Lazy load thickbox images
	$(".show-thickbox-image").on("click", function() {
		var id     = $(this).data("lazy-load-target");
		var $img   = $("#" + id);
		var height = $img.data("height");
		var width  = $img.data("width");
		var src    = $img.data("src");
		$img.attr("src", src).attr("height", height).attr("width", width);
	});

	// Always reset the "finish" checkbox and "publish website" controls. These
	// can be stuck to an incorrect state if the user hits the "back" button
	// in their browser
	$("#q-setup-final-warning").removeAttr('checked');
	$("#form-step3 a.gd-quicksetup-wizard-submit").html( objectL10n.publish ).addClass("button-disabled").attr("disabled", true);
});
