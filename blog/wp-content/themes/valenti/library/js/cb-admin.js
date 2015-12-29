(function( $ ){
         $.fn.cbSectionCode = function(cb_current_section, cb_block_type, cbFilterCheck ) {

            var cbCurrentBlock = $(this),
              cbFilterSelect = cbCurrentBlock.find($("[id^='cb_section_" + cb_current_section + "_cb_filter_']")),
              cbTagInput = cbCurrentBlock.find($("[id^='cb_section_" + cb_current_section + "_ta']")),
              cbPostInput = cbCurrentBlock.find($("[id^='cbaj_']")),
              cbFilterSelectOption = $('option:selected', cbFilterSelect).attr('value'),
              cbAdBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_ad_code_" + cb_current_section + "_']")),
              cbSubtitleBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_subtitle_" + cb_current_section + "_']")),
              cbSliderBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_slider_" + cb_current_section + "_']")),
              cbLatestBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_" + cb_current_section + "_latest_posts_']")),
              cbLatestTagsBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_ta']")),
              cbLatestIdsBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_id']")),
              cbStyleBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_style_" + cb_current_section + "_']")),
              cbOrderBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_order_']")),
              cbOffsetBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_offset_']")),
              cbCustomBlock = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_custom_" + cb_current_section + "_']")),
              cbFilter = cbCurrentBlock.find($("[id^='setting_cb_section_" + cb_current_section + "_cb_filter_']"));

              cbTagInput.suggest( ajaxurl + '?action=ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2, multiple: true, multipleSep: postL10n.comma } );
              cbPostInput.suggest( ajaxurl + '?action=cb-ajax-post-search', { delay: 500, minchars: 2, multiple: true, multipleSep: ' ' } );

              cbPostInput.change(function() {

                var cbInputChange = $(this);
                setTimeout(function () {

                    var cbPostInputVal = cbInputChange.val().trim(),
                        cbRealInput = cbInputChange.next(),
                        cbRealInputVal = cbRealInput.val();

                        if ( cbPostInputVal.trim() ) {
                          cbInputChange.before( '<span class="cb-post-added">'+ cbPostInputVal +'</span>' );
                          cbRealInput.val( cbRealInputVal + '<cb>' + cbPostInputVal );
                          cbInputChange.val( '' );
                        }
                  }, 600);

              });

            cbStyleBlock.add(cbOrderBlock).add(cbOffsetBlock).add(cbSliderBlock).addClass('cb-half-options');

            if ( cb_block_type === 'cbAd')  {

              cbAdBlock.slideDown();
              cbSubtitleBlock.add(cbOrderBlock).add(cbOffsetBlock).add(cbSliderBlock).add(cbStyleBlock).add(cbCustomBlock).add(cbFilter).add(cbLatestIdsBlock).add(cbLatestBlock).add(cbLatestTagsBlock).slideUp();
              cbFilterCheck = false;

          } else if ( cb_block_type === 'cbModule')  {

              cbSubtitleBlock.add(cbSliderBlock).add(cbStyleBlock).add(cbFilter).add(cbFilter).add(cbOrderBlock).add(cbOffsetBlock).slideDown();
              cbAdBlock.add(cbCustomBlock).slideUp();
              cbFilterCheck = true;

          }  else if ( cb_block_type === 'cbCode')  {

              cbCustomBlock.add(cbSubtitleBlock).slideDown();
              cbSliderBlock.add(cbStyleBlock).add(cbOrderBlock).add(cbOffsetBlock).add(cbAdBlock).add(cbFilter).add(cbLatestIdsBlock).add(cbLatestBlock).add(cbLatestTagsBlock).slideUp();
              cbFilterCheck = false;

          } else if ( cb_block_type === 'cbSlider' ) {

              cbSubtitleBlock.add(cbStyleBlock).add(cbOrderBlock).add(cbOffsetBlock).add(cbFilter).add(cbFilter).slideDown();
              cbAdBlock.add(cbCustomBlock).add(cbSliderBlock).slideUp();
              cbFilterCheck = true;

          } else {

              cbSubtitleBlock.add(cbStyleBlock).add(cbOrderBlock).add(cbOffsetBlock).add(cbFilter).add(cbFilter).slideDown();
              cbAdBlock.add(cbCustomBlock).add(cbSliderBlock).slideUp();
              cbFilterCheck = true;
          }

          if ( cbFilterCheck === true ) {

              if ( cbFilterSelectOption === 'cb_filter_category' ) {
                cbLatestBlock.show();
                cbLatestTagsBlock.add(cbLatestIdsBlock).hide();
              } else if ( cbFilterSelectOption === 'cb_filter_tags' ) {
                cbLatestTagsBlock.show();
                cbLatestBlock.add(cbLatestIdsBlock).hide();
              } else if ( cbFilterSelectOption === 'cb_filter_postid' ) {
                cbLatestIdsBlock.show();
                cbLatestTagsBlock.add(cbLatestBlock).hide();
              }
          }
            cbFilterSelect.change(function() {

              if ( this.value === 'cb_filter_category' ) {
                cbLatestBlock.slideDown();
                cbLatestTagsBlock.add(cbLatestIdsBlock).slideUp();
              } else if ( this.value === 'cb_filter_tags' ) {
                cbLatestTagsBlock.slideDown();
                cbLatestBlock.add(cbLatestIdsBlock).slideUp();
              } else if ( this.value === 'cb_filter_postid' ) {
                cbLatestIdsBlock.slideDown();
                cbLatestTagsBlock.add(cbLatestBlock).slideUp();
              }

            });

            return this;
         };
      })( jQuery );

jQuery(document).ready(function($){"use strict";

   $('#cb_final_score').attr('readonly', true);
   $("#cb_review .inside .rwmb-meta-box > div:gt(0)").wrapAll('<div class="cb-enabled-review">');
   $('.cb-enabled-review > div:gt(4):odd:lt(7)').each(function() {
        $(this).prev().addBack().wrapAll($('<div/>',{'class': 'cb-criteria'}));
    });
    var cbReviewCheckbox = $('#cb_review_checkbox'),
        cbReviewBox = $('.cb-enabled-review');

        if ( cbReviewCheckbox.is(":checked") ) {
                cbReviewBox.show();
            }

        cbReviewCheckbox.click(function(){
            cbReviewBox.slideToggle('slow');
        });

        function cbScoreCalc() {

            var i = 0;
            var cb_cs1 = parseFloat($('input[name=cb_cs1]').val());
            var cb_cs2 = parseFloat($('input[name=cb_cs2]').val());
            var cb_cs3 = parseFloat($('input[name=cb_cs3]').val());
            var cb_cs4 = parseFloat($('input[name=cb_cs4]').val());
            var cb_cs5 = parseFloat($('input[name=cb_cs5]').val());
            var cb_cs6 = parseFloat($('input[name=cb_cs6]').val());
            if (cb_cs1) { i+=1; } else { cb_cs1 = 0; }
            if (cb_cs2) { i+=1; } else { cb_cs2 = 0; }
            if (cb_cs3) { i+=1; } else { cb_cs3 = 0; }
            if (cb_cs4) { i+=1; } else { cb_cs4 = 0; }
            if (cb_cs5) { i+=1; } else { cb_cs5 = 0; }
            if (cb_cs6) { i+=1; } else { cb_cs6 = 0; }

            var cbTempTotal = (cb_cs1 + cb_cs2 + cb_cs3 + cb_cs4 + cb_cs5 + cb_cs6);
            var cbTotal = Math.round(cbTempTotal / i);

            $("#cb_final_score").val(cbTotal);

            if ( isNaN(cbTotal) ) { $("#cb_final_score").val(''); }

        }

       $('#cb_cs1, #cb_cs2, #cb_cs3, #cb_cs4, #cb_cs5, #cb_cs6').on('slidechange', cbScoreCalc);

       cbReviewCheckbox.after('<label for="cb_review_checkbox"></label>');

       $('.cb-about-options-title').after($('.cb-about-options'));

      // Theme Option Functions
      var cbHpb = $('#cb_hpb'),
          cbSectionA = $('#setting_cb_section_a'),
          cbSectionB = $('#setting_cb_section_b'),
          cbSectionC = $('#setting_cb_section_c'),
          cbSectionD = $('#setting_cb_section_d'),
          cbSelectedAd = cbHpb.find('.option-tree-ui-radio-image-selected[title^="Ad"]').closest('.option-tree-setting-body'),
          cbSelectedModule = cbHpb.find('.option-tree-ui-radio-image-selected[title^="Mo"]').closest('.option-tree-setting-body'),
          cbSelectedSlider = cbHpb.find('.option-tree-ui-radio-image-selected[title^="Sl"]').closest('.option-tree-setting-body'),
          cbSelectedCustom = cbHpb.find('.option-tree-ui-radio-image-selected[title^="Cu"]').closest('.option-tree-setting-body'),
          cbSelectedGrid = cbHpb.find('.option-tree-ui-radio-image-selected[title^="Gr"]').closest('.option-tree-setting-body'),
          cbLoadPostInput = cbHpb.find($("[id^='cbraj_']")),
          cbLoadPostInputVals = cbLoadPostInput.val();

      cbSectionA.before('<div id="setting_cb_title" class="format-settings"><div class="format-setting-wrap"><div class="format-setting-label"><h3 class="label">Valenti Homepage Builder</h3></div><div class="list-item-description">All the sections below are optional, allowing you to build any type of homepage you want. Remember to set "Page Attributes: Template" to "Valenti Drag & Drop Builder" and <strong>GET CREATIVE!</strong></div></div></div>');

      cbSelectedAd.each(function () {

         var cbCurrentSection = $(this).closest("[id^=setting_cb_section]").attr('id'),
            cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1);
        $(this).cbSectionCode(cbCurrentSectionID, 'cbAd');

      });

      cbSelectedCustom.each(function () {

        var cbCurrentSection = $(this).closest("[id^=setting_cb_section]").attr('id'),
            cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1);
        $(this).cbSectionCode(cbCurrentSectionID, 'cbCode');

      });

      cbSelectedModule.each(function () {

        var cbCurrentSection = $(this).closest("[id^=setting_cb_section]").attr('id'),
            cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1);
        $(this).cbSectionCode(cbCurrentSectionID, 'cbModule');

      });

      cbSelectedSlider.each(function () {

        var cbCurrentSection = $(this).closest("[id^=setting_cb_section]").attr('id'),
            cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1);
        $(this).cbSectionCode(cbCurrentSectionID, 'cbSlider');

      });

      cbSelectedGrid.each(function () {

        var cbCurrentSection = $(this).closest("[id^=setting_cb_section]").attr('id'),
            cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1);
        $(this).cbSectionCode(cbCurrentSectionID, 'cbGrid');

      });


      cbLoadPostInput.each(function () {

        var cbCurrentPost = $(this),
            cbAllPosts = cbCurrentPost.val().split('<cb>'),
            cbCurrentPostPrev = cbCurrentPost.prev(),
            cbAllPostsLength = cbAllPosts.length;



            for ( var i = 0; i < cbAllPostsLength; i++ ) {
              if (cbAllPosts[i].trim()) {
                cbCurrentPostPrev.before('<span class="cb-post-added">' + cbAllPosts[i] + '</span>');
              }
            }

      });

      $(document).on('click', '.cb-post-added', function () {

                var cbCurrentPostAdded = $(this),
                    cbCurrentParent = cbCurrentPostAdded.parent(),
                    cbCurrentParentFind = cbCurrentParent.find('.cb-post-added'),
                    cbLastInput = $(':last-child', cbCurrentParent),
                    cbCurrentText;

                cbCurrentPostAdded.remove();

                var cbLastTest = cbCurrentParent.find('.cb-post-added');
                cbLastInput.val( '' );

                cbLastTest.each(function () {
                  cbCurrentText = $(this).text();
                  var cbCurrentInputVal = cbLastInput.val();
                  cbLastInput.val( cbCurrentInputVal + cbCurrentText + '<cb>' );
                });
      });

      $('#setting_cb_how_to_get_support').find('img[title="Documentation"]').wrap('<a href="http://docs.cubellthemes.com/valenti/" class="cb-pointer" target="_blank"></a>');
      cbHpb.find('.option-tree-ui-radio-image-selected[title="Module B"]').closest('.ui-state-default').addClass('cb-half-width');
      cbHpb.find('.option-tree-ui-radio-image-selected[title="Module C"]').closest('.ui-state-default').addClass('cb-half-width');
      cbHpb.find('.option-tree-ui-radio-image-selected[title="Module D"]').closest('.ui-state-default').addClass('cb-half-width');
      cbHpb.find('.option-tree-ui-radio-image-selected[title="Module G"]').closest('.ui-state-default').addClass('cb-half-width');
      cbHpb.find('.option-tree-ui-radio-image-selected[title="Ad: 336x280"]').closest('.ui-state-default').addClass('cb-half-width');

      cbSectionA.add(cbSectionB).add(cbSectionC).add(cbSectionD).on('click', '.option-tree-ui-radio-image', function() {

          var cbCurrentBlock = $(this).closest('.option-tree-setting-body'),
              cbCurrentSection = $(this).closest("[id^=setting_cb_section]").parent().closest("[id^=setting_cb_section]").attr('id'),
              cbCurrentSectionID = cbCurrentSection.substr(cbCurrentSection.length - 1),
              cbCurrentModuleTitle = $(this).attr('title'),
              cbCurrentModuleTitleTrim = cbCurrentModuleTitle.substring(0, 2),
              cbCurrentModule = '';

          if ( cbCurrentModuleTitleTrim === 'Ad' )  {
            cbCurrentModule = 'cbAd';
          } else if ( cbCurrentModuleTitleTrim === 'Mo' ) {
            cbCurrentModule = 'cbModule';
          } else if ( cbCurrentModuleTitleTrim === 'Sl' )  {
            cbCurrentModule = 'cbSlider';
          } else if ( cbCurrentModuleTitleTrim === 'Gr' ) {
            cbCurrentModule = 'cbGrid';
          } else if ( cbCurrentModuleTitleTrim === 'Cu') {
            cbCurrentModule = 'cbCode';
          }

          cbCurrentBlock.cbSectionCode(cbCurrentSectionID, cbCurrentModule);

           if ( ( cbCurrentModuleTitle === 'Ad: 336x280') || ( cbCurrentModuleTitle === 'Module B') || ( cbCurrentModuleTitle === 'Module C') || ( cbCurrentModuleTitle === 'Module D') )  {
            $(this).closest('.ui-state-default').addClass('cb-half-width');
          } else {
            $(this).closest('.ui-state-default').removeClass('cb-half-width');
          }

      });

        var cbCatOffset = $(".at-select[name='cb_cat_offset']").closest('.form-field'),
            cbCatGridSlider = $(".at-select[name='cb_cat_featured_op']"),
            cbCatGridSliderValue = $(".at-select[name='cb_cat_featured_op'] option:selected").text();

        if ( cbCatGridSliderValue === 'Off' ) {
          cbCatOffset.hide();
        }

        cbCatGridSlider.change(function() {

          if ( this.value === 'Off' ) {
            cbCatOffset.slideUp();
          } else {
            cbCatOffset.slideDown();
          }

        });

var cbPostFormatBox = $( '#cb_format_options' );
    $('#cb_format_options-hide').parent().remove();
    cbPostFormatBox.children(":lt(2)").remove();
    cbPostFormatBox.removeClass('postbox').appendTo("#ot-post-format-gallery");
});