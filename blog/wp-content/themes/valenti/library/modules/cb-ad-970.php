 <?php /* AD: 970x90 */ 
 
    if ($cb_title != NULL) {
        $cb_title_header = '<div class="cb-module-title h6" >'.$cb_title.'</div>';
    } else {
        $cb_title_header = NULL;
    }
 ?>
     <div class="cb-a-large cb-box clearfix">
         
	   <?php echo $cb_title_header . do_shortcode( $cb_ad_code ) ; ?>
       
	</div>