<?php
/*
Plugin Name: Demo Tax meta class
Plugin URI: http://en.bainternet.info
Description: Tax meta class usage demo
Version: 1.9.9
Author: Bainternet, Ohad Raz
Author URI: http://en.bainternet.info
*/

//include the main class file
require_once("Tax-meta-class.php");
if (is_admin()){
  /* 
   * prefix of meta keys, optional
   */
  $prefix = 'bpxl_';
  /* 
   * configure your meta box
   */
  $config = array(
    'id' => 'demo_meta_box',          // meta box id, unique per meta box
    'title' => 'Demo Meta Box',          // meta box title
    'pages' => array('category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );
  
  
  /*
   * Initiate your meta box
   */
  $my_meta =  new Tax_Meta_Class($config);
  
  /*
   * Add fields to your meta box
   */
  
  // Category Primary Color
  $my_meta->addColor($prefix.'color_field_id',array('name'=> __('Category Primary Color ','tax-meta')));
  
  // Category Primary Color
  $my_meta->addColor($prefix.'color_secondary_field_id',array('name'=> __('Category Secondary Color ','tax-meta')));
  
  // Category Layout Style
  $my_meta->addSelect($prefix.'category_layout_id',array('select'=>'Select','1'=>'Blog Style 1','2'=>'Blog Style 2','3'=>'Blog Style 3'),array('name'=> __('Category Layout Style ','tax-meta'), 'std'=> array('select')));
  
  //radio field
  //$my_meta->addRadio($prefix.'radio_field_id',array('radiokey1'=>'Radio Value1','radiokey2'=>'Radio Value2'),array('name'=> __('My Radio Filed','tax-meta'), 'std'=> array('radionkey2')));
  
  // Background Image
  $my_meta->addImage($prefix.'bg_field_id',array('name'=> __('Background Image ','tax-meta')));
  
  // Background Repeat
  $my_meta->addSelect($prefix.'category_repeat_id',array('repeat'=>'Repeat','no-repeat'=>'No Repeat'),array('name'=> __('Background Repeat ','tax-meta'), 'std'=> array('repeat')));
  
  // Background Position
  $my_meta->addSelect($prefix.'background_position_id',array('center'=>'Center','fixed'=>'Fixed','left'=>'Left','top'=>'Top'),array('name'=> __('Background Repeat ','tax-meta'), 'std'=> array('repeat')));
  
  /*
   * To Create a reapeater Block first create an array of fields
   * use the same functions as above but add true as a last param
   */
  
  $repeater_fields[] = $my_meta->addText($prefix.'re_text_field_id',array('name'=> __('My Text ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addTextarea($prefix.'re_textarea_field_id',array('name'=> __('My Textarea ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addCheckbox($prefix.'re_checkbox_field_id',array('name'=> __('My Checkbox ','tax-meta')),true);
  $repeater_fields[] = $my_meta->addImage($prefix.'image_field_id',array('name'=> __('My Image ','tax-meta')),true);
  
  /*
   * Then just add the fields to the repeater block
   */
  //repeater block
  //$my_meta->addRepeaterBlock($prefix.'re_',array('inline' => true, 'name' => __('This is a Repeater Block','tax-meta'),'fields' => $repeater_fields));
  /*
   * Don't Forget to Close up the meta box decleration
   */
  //Finish Meta Box Decleration
  $my_meta->Finish();
}
