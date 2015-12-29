<?php

@session_start ();

define ( 'ROOT_DIR' , '..' );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

$_POST['login'] = TRUE;

$_POST['login'] = "submit";

require_once INCLUDE_DIR . '/member.php';

if($logged){
	
	$buffer['status_code'] = 200;
	
	$buffer['status_text'] = "OK";
	
	$buffer['user'] = $member_id;
	$buffer['user']['is_beta_tester'] = false;
	$buffer['user']['viewer_following'] = false;
	$buffer['user']['import_feeds'] = import_feeds($member_id['user_id']);
	$buffer['user']['image'] = avatar( $member_id['avatar'], $member_id['username'] );
	
	
	$row = $db->super_query("SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id['user_id'] . "';");
		
	if( $row['image'] ) {
		$use_image = true;
		$is_default = false;
	} else {
		$is_default = true;
		$use_image = false;
	}
		
	$buffer['user']['background'] = $row;
	$buffer['user']['background']['is_default'] = $is_default;
	$buffer['user']['background']['use_image'] = $use_image;

	unset($buffer['user']['password']);
	
}

if(!$logged){
	
	$buffer['status_code'] = 400;
	
	$buffer['status_text'] = "Invalid username or password.";
	
}

header('Cache-Control: no-cache, must-revalidate');

header('Content-type: application/json');

print json_encode($buffer);

?>