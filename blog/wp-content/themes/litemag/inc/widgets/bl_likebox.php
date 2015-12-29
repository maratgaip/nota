<?php
/*
Plugin Name: bl Likebox
Description: Facebook Like Box
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_likebox extends WP_Widget
{
  function bl_likebox(){
    $widget_ops = array('classname' => 'bl_likebox', 'description' => 'Display a Facebook Like Box' );
    $this->WP_Widget('bl_likebox', 'Bluthemes - Likebox', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 
    	'title' => '',
    	'url' => '',
    	'show_faces' => '',
    	'color_scheme' => '',
    	'show_stream' => ''
    ));

  ?>
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
    <input type="text" style="width:216px" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
  </p>
  <strong>Instructions</strong>
  <ol>
    <li>Copy the link to you facebook page</li>
    <li>Paste it in the input box below</li>
  </ol>
  <p>
    <label for="<?php echo $this->get_field_id('url'); ?>">Url for like box</label><br>
    <input type="text" style="width:216px" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $instance['url']; ?>">
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('url'); ?>">Show stream</label><br>
	<select style="width:216px" id="<?php echo $this->get_field_id('show_stream'); ?>" name="<?php echo $this->get_field_name('show_stream'); ?>">
	  	<option value="true" <?php echo ($instance['show_stream'] == 'true') ? 'selected=""' : ''; ?>>Yes</option> 
	  	<option value="false" <?php echo ($instance['show_stream'] == 'false') ? 'selected=""' : ''; ?>>No</option> 
	</select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('url'); ?>">Show faces</label><br>
	<select style="width:216px" id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>">
	  	<option value="true" <?php echo ($instance['show_faces'] == 'true') ? 'selected=""' : ''; ?>>Yes</option> 
	  	<option value="false" <?php echo ($instance['show_faces'] == 'false') ? 'selected=""' : ''; ?>>No</option> 
	</select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('url'); ?>">Color Scheme</label><br>
  	<select style="width:216px" id="<?php echo $this->get_field_id('color_scheme'); ?>" name="<?php echo $this->get_field_name('color_scheme'); ?>">
  	  	<option value="light" <?php echo ($instance['color_scheme'] == 'light') ? 'selected=""' : ''; ?>>Light</option> 
  	  	<option value="dark" <?php echo ($instance['color_scheme'] == 'dark') ? 'selected=""' : ''; ?>>Dark</option> 
  	</select>
  </p>
  <?php
  }
 
  function update($new_instance, $old_instance){

    $instance = $old_instance;
    $instance['title']         = strip_tags($new_instance['title']);
    $instance['url']     		   = esc_url($new_instance['url']);
    $instance['show_faces']    = strip_tags($new_instance['show_faces']);
    $instance['color_scheme']  = strip_tags($new_instance['color_scheme']);
    $instance['show_stream']   = strip_tags($new_instance['show_stream']);
    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);
    $title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
    echo $before_widget;
    $height = 65;
    if($instance['show_faces'] == 'true'){
      $height += 175;
    }
    if($instance['show_stream'] == 'true'){
      $height += 350;
    }
    ?>
      <?php echo !empty($title) ? $before_title.$title.$after_title : ''; ?>
      <div class="widget-body" id="bl_likebox">
        <iframe src="https://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode($instance['url']) ?>&amp;width=270&amp;height=<?php echo $height; ?>&amp;show_faces=<?php echo $instance['show_faces']; ?>&amp;colorscheme=<?php echo $instance['color_scheme']; ?>&amp;stream=<?php echo $instance['show_stream']; ?>&amp;show_border=false&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:<?php echo $height; ?>px;" allowTransparency="true"></iframe>
      </div>
    <?php
    echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_likebox");') );