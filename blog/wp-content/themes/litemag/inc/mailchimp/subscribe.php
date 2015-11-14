<?php 

	if ( !defined('ABSPATH') ){
		$root = str_replace('/wp-content/themes/litemag/inc/mailchimp', '', dirname(__FILE__));
	}else{
		$root = ABSPATH;
	}

	ob_start();
	define('WP_USE_THEMES', false);
	require_once($root.'/wp-load.php');
	ob_end_clean();

	$options = get_option('widget_bl_newsletter');
	$options = current($options);

	if(!isset($options['list_id'])){
		echo json_encode(array("error" => "No mailing list selected"));
		 die();
	}
	if(!isset($options['api_key'])){
		echo json_encode(array("error" => "API key not defined")); 
		die();
	}

	if(!isset($_POST['email'])){ 
		echo json_encode(array("error" => __('No email address provided','bluth'))); 
		die();
	} 

	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_POST['email'])) {
		echo json_encode(array("error" => __('Email address is invalid','bluth'))); 
	}

	require_once(get_theme_root().'/litemag/inc/mailchimp/MCAPI.class.php');

	$api = new MCAPI($options['api_key']);

	$list_id = $options['list_id'];

	if($api->listSubscribe($list_id, $_POST['email'], '') === true) {
		echo json_encode(array("status" => 'ok'));
	}else{
		echo json_encode(array("error" => 'Error: ' . $api->errorMessage));
	}
	

?>