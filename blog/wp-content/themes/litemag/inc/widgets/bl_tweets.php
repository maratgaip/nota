<?php
/*
Plugin Name: bl Tweets
Description: Box with your recent tweets
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_tweets extends WP_Widget
{
  function bl_tweets(){
    $widget_ops = array('classname' => 'bl_tweets', 'description' => 'Displays recent tweets' );
    $this->WP_Widget('bl_tweets', 'Bluthemes - Tweets', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    
    $title            = !empty($instance['title']) ? $instance['title'] : '';
    $username         = !empty($instance['username']) ? $instance['username'] : '';
    $author_username  = !empty($instance['author_username']) ? $instance['author_username'] : 'no';
    $twitter_count  = !empty($instance['twitter_count']) ? $instance['twitter_count'] : 4;
  ?>
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
    <input type="text" style="width:216px" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
  </p>
  <strong>Instructions</strong>
  <ol>
    <li>Follow the instructions in the Theme Options Panel -> Social</li>
    <li>Put in your username below <strong>or</strong> check the "Get authors username" box and place it in the Post Sidebar</li>
  </ol>
  <p>
    <label for="<?php echo $this->get_field_id('username'); ?>">Twitter Username</label><br>
    <input type="text" style="width:216px" id="<?php echo $this->get_field_id('username'); ?>" onClick="jQuery(this).select();" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo $username; ?>"></textarea>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('author_username'); ?>">Get authors username</label>
    <select id="<?php echo $this->get_field_id('author_username'); ?>" name="<?php echo $this->get_field_name('author_username'); ?>">
      <option value="no" <?php echo ($author_username == 'no') ? 'selected' : ''; ?>>No</option>
      <option value="yes" <?php echo ($author_username == 'yes') ? 'selected' : ''; ?>>Yes</option>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('twitter_count'); ?>">Number of tweets to show</label>
    <select id="<?php echo $this->get_field_id('twitter_count'); ?>" name="<?php echo $this->get_field_name('twitter_count'); ?>">
      <option value="1" <?php echo ($twitter_count == '1') ? 'selected' : ''; ?>>1</option>
      <option value="2" <?php echo ($twitter_count == '2') ? 'selected' : ''; ?>>2</option>
      <option value="3" <?php echo ($twitter_count == '3') ? 'selected' : ''; ?>>3</option>
      <option value="4" <?php echo ($twitter_count == '4') ? 'selected' : ''; ?>>4</option>
      <option value="5" <?php echo ($twitter_count == '5') ? 'selected' : ''; ?>>5</option>
    </select>
  </p>
  <?php
  }
 
  function update($new_instance, $old_instance){

    $instance = $old_instance;
    $instance['title']              = strip_tags($new_instance['title']);
    $instance['username']           = $new_instance['username'];
    $instance['author_username']    = strip_tags($new_instance['author_username']);
    $instance['twitter_count']    = strip_tags($new_instance['twitter_count']);

    $username = $instance['username'];

    delete_transient( 'blu_get_twitter_feed_'.$username );

    return $instance;
  }
 
    function widget($args, $instance){

        extract($args, EXTR_SKIP);
        $title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;

        if(!empty($title)){

            echo $before_title.$title.$after_title; 

        }

        $username = $instance['author_username'] == 'yes' ? of_get_option('author_twitter_username_'.get_the_author_meta('ID')) : $instance['username'];

        if($username and !empty($username)){
            
            $response = blu_get_twitter_feed($username, $instance['twitter_count']);   

            if(!empty($response['tweets'])){  ?>
                
                <div class="widget-body">
                    <div class="twitter-user-info" style="<?php echo isset($response['user']['profile_banner_url']) ? "background-image:url('".$response['user']['profile_banner_url']."')" : ''; ?>">
                        <div class="user-image"><img src="<?php echo $response['user']['profile_image_url']; ?>"></div>
                        <div class="user-info-wrapper">
                            <div class="user-name"><h4 class="uname"><?php echo $response['user']['screen_name']; ?></h4></div>
                            <div class="user-description"><p><?php echo $response['user']['description']; ?></p></div>
                            <div class="user-location"><p><i class="fa fa-map-marker"></i> <?php echo $response['user']['location']; ?></p></div>
                        </div>
                    </div><?php 
                    foreach($response['tweets'] as $tweet){ ?>
                        <div class="twitter-status">
                            <time class="timeago tips" data-placement="right" title="<?php echo $tweet['created_at']; ?>" datetime="<?php echo $tweet['created_at']; ?>"><?php echo date('M d h:i', strtotime( $tweet['created_at'] ) ); /*echo date('M t h:i', time( $tweet['created_at']) );*/ ?></time>
                            <p><?php echo $tweet['text']; ?></p>
                            <a class="reply-to" href="http://www.twitter.com/<?php echo $response['user']['screen_name']; ?>/status/<?php echo (string)$tweet['id']; ?>"><i class="fa fa-mail-reply"></i> <?php echo __('Reply', 'bluth'); ?></a>
                        </div><?php
                    } ?>

                </div><!-- widget-body --> <?php

            }else{

                if(!of_get_option('twitter_api_key') and is_super_admin()){
                    echo '<small class="alert alert-warning" style="display: block; text-align: center;">Setup your Twitter API options in Theme Options -> Social</small>';
                }

            }

        }
        
        echo $after_widget.'<!-- after-widget -->';

    }

}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_tweets");') );