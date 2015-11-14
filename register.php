<?php
ini_set('session.bug_compat_warn', 0);

ini_set('session.bug_compat_42', 0);

@session_start ();

define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

$email = $db->safesql( $_POST['email'] );

$password = $db->safesql( $_POST['password'] );

$username = $db->safesql( $_POST['username'] );

$social = $db->safesql( $_POST['social'] );


$_TIME = date ( "Y-m-d H:i:s", time () );

$REGISTER = TRUE;

if( $email && $username && $password ){
	
	if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
		
		$buffer['status_code'] = 400;
		$buffer['status_text'] = "Email is not valid";
		$REGISTER = FALSE;
		
	}elseif( preg_match('/[^0-9A-Za-z]/', $username)){
		
		$buffer['status_code'] = 400;
		$buffer['status_text'] = "Username must not contain special word, or symbol";
		
		$REGISTER = FALSE;
		
	}elseif( strlen( $password ) < 6 ){
		
		$buffer['status_code'] = 400;
		$buffer['status_text'] = "Password must have more than 6 words";
		
		$REGISTER = FALSE;
		
	}else{
		
		$row = $db->super_query("SELECT user_id FROM vass_users WHERE email = '" . $email . "' LIMIT 0,1");
		
		if( $row['user_id'] ){
		
			$buffer['status_code'] = 400;
			$buffer['status_text'] = "Email was used by other.";
					
			$REGISTER = FALSE;
			
		}else{
		
			$row = $db->super_query("SELECT user_id FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1");
			
			if( $row['user_id'] ){
			
				$buffer['status_code'] = 400;
				
				$buffer['status_text'] = "Username was used by other.";
				
				$REGISTER = FALSE;
				
			}
			
		}
		
		if( $REGISTER ) {
			
			if( $social == 'facebook' && $_SESSION['facebook'] ){
				
				$user = $_SESSION['facebook'];
				
				$avatar = 'https://graph.facebook.com/' . $user->id . '/picture?type=large';
				
				$avatar = file_get_contents($avatar);
				
				$fp = fopen( ROOT_DIR . "/static/users/avatar_full_" . $username , "w");
				
				fwrite($fp, $avatar);
				
				fclose($fp);
				
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->size_auto('500');
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_original_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->crop('75', '75' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_medium_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->crop('35', '35' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_small_" . $username . ".jpg" );
				
				@unlink ( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "', name = '" . $db->safesql( $user->name ) . "', location = '" . $db->safesql( $user->location->name ) . "', bio  = '" . $db->safesql( $user->bio ) . "', avatar = '1'");
				
				$user_id = $db->insert_id();
				
				$db->query("INSERT IGNORE INTO vass_facebook SET user_id = '" . $user_id . "', screen_id = '" . $user->id . "',
				screen_name = '" . $db->safesql($user->username) . "', name = '" . $db->safesql($user->name) . "',
				profile_image_url = 'https://graph.facebook.com/" . $user->id . "/picture?type=large', token = '" . $_SESSION['access_token'] . "', `date` = '$_TIME'
				");
				
			}elseif( $social == 'twitter' && $_SESSION['twitter'] ){
				
				$twitterInfo = $_SESSION['twitter'];
				
				$avatar = $twitterInfo['profile_image_url'];
				
				$avatar = file_get_contents($avatar);
				
				$fp = fopen( ROOT_DIR . "/static/users/avatar_full_" . $username , "w");
				
				fwrite($fp, $avatar);
				
				fclose($fp);
				
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->size_auto('500');
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_original_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->crop('75', '75' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_medium_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$thumb->crop('35', '35' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/static/users/avatar_small_" . $username . ".jpg" );
				
				@unlink ( ROOT_DIR . "/static/users/avatar_full_" . $username );
				
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "', name = '" . $db->safesql( $twitterInfo['name'] ) . "', location = '" . $db->safesql( $twitterInfo['location'] ) . "', bio  = '" . $db->safesql( $twitterInfo['description'] ) . "', avatar = '1'");
				
				$user_id = $db->insert_id();
				
				$db->query("INSERT IGNORE INTO vass_twitter SET user_id = '" . $user_id . "', screen_id = '" . $twitterInfo['id'] . "',
				screen_name = '" . $db->safesql($twitterInfo['screen_name']) . "', name = '" . $db->safesql($twitterInfo['name']) . "',
				profile_image_url = '" . $db->safesql($twitterInfo['profile_image_url']) . "', `date` = '$_TIME'
				");
				
			}else{
			
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "'");
				
				$user_id = $db->insert_id();
				
			}
			
			$member_id = array ();
			
			$member_id = $db->super_query("SELECT * FROM vass_users WHERE user_id = '" . $user_id . "'");
			
			set_cookie( "user_id", $member_id['user_id'], 365 );
			set_cookie( "login_pass", $member_id['password'], 365 );
			$_SESSION['user_id'] = $member_id['user_id'];
			$_SESSION['login_pass'] = $member_id['password'];
			$logged = TRUE;
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "OK";
			
			$buffer ['user'] = $member_id;
			$buffer ['user'] ['viewer_following'] = false;
			$buffer ['user'] ['import_feeds'] = import_feeds ( $member_id ['user_id'] );
			$buffer ['user'] ['image'] = avatar ( $member_id ['avatar'], $member_id ['username'] );
			
			$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';" );
			
			if ($row ['image']) {
				$use_image = true;
				$is_default = false;
			} else {
				$is_default = true;
				$use_image = false;
			}
			
			$buffer ['user'] ['background'] = $row;
			$buffer ['user'] ['background'] ['is_default'] = $is_default;
			$buffer ['user'] ['background'] ['use_image'] = $use_image;
			
			unset ( $buffer ['user'] ['password'] );
			
		}
		
	}
	
}

header('Cache-Control: no-cache, must-revalidate');

header('Content-type: application/json');

print json_encode($buffer);
?>