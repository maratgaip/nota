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

$social = $db->safesql( $_GET['social'] );

$REGISTER = TRUE;

if( $email && $username && $password ){
	
	if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
		
		$buffer =  '{"status_code": 400, "status_text": "Email không hợp lệ."}';
		
		$REGISTER = FALSE;
		
	}elseif( preg_match('/[^0-9A-Za-z]/', $username)){
		
		$buffer =  '{"status_code": 400, "status_text": "Tài khoản phải là tiếng việt không dấu, không chứa dấu cách, ký tự đặc biệt!"}';
		
		$REGISTER = FALSE;
		
	}elseif( strlen( $password ) < 6 ){
		
		$buffer =  '{"status_code": 400, "status_text": "Mật khẩu phải chứa ít nhất 6 ký tự."}';
		
		$REGISTER = FALSE;
		
	}else{
		
		$row = $db->super_query("SELECT user_id FROM vass_users WHERE email = '" . $email . "' LIMIT 0,1");
		
		if( $row['user_id'] ){
		
			$buffer =  '{"status_code": 400, "status_text": "Email này đã được sử dụng. Nếu bạn đã đăng ký trước đó, vui lòng sử dụng chức năng đăng nhập."}';
			
			$REGISTER = FALSE;
			
		}else{
		
			$row = $db->super_query("SELECT user_id FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1");
			
			if( $row['user_id'] ){
			
				$buffer =  '{"status_code": 400, "status_text": "Tài khoản này đã được sử dụng. Nếu bạn đã đăng ký trước đó, vui lòng sử dụng chức năng đăng nhập."}';
				
				$REGISTER = FALSE;
				
			}
			
		}
		
		if( $REGISTER ) {
			
			if( $social == 'facebook' && $_SESSION['facebook'] ){
				
				$user = $_SESSION['facebook'];
				
				$avatar = 'https://graph.facebook.com/' . $user->id . '/picture?type=large';
				
				$avatar = file_get_contents($avatar);
				
				$fp = fopen( ROOT_DIR . "/uploadfiles/avatar_full_" . $username , "w");
				
				fwrite($fp, $avatar);
				
				fclose($fp);
				
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->size_auto('500');
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_original_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->crop('75', '75' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_medium_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->crop('35', '35' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_small_" . $username . ".jpg" );
				
				@unlink ( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "', name = '" . $db->safesql( $user->name ) . "', location = '" . $db->safesql( $user->location->name ) . "', bio  = '" . $db->safesql( $user->bio ) . "', avatar = '1'");
				
			}elseif( $social == 'twitter' && $_SESSION['twitter'] ){
				
				$twitterInfo = $_SESSION['twitter'];
				
				$avatar = $twitterInfo['profile_image_url'];
				
				$avatar = file_get_contents($avatar);
				
				$fp = fopen( ROOT_DIR . "/uploadfiles/avatar_full_" . $username , "w");
				
				fwrite($fp, $avatar);
				
				fclose($fp);
				
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->size_auto('500');
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_original_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->crop('75', '75' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_medium_" . $username . ".jpg" );
				
				$thumb = new thumbnail( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				$thumb->crop('35', '35' );
				
				$thumb->jpeg_quality( 100 );
				
				$thumb->save( ROOT_DIR . "/uploadfiles/avatar_small_" . $username . ".jpg" );
				
				@unlink ( ROOT_DIR . "/uploadfiles/avatar_full_" . $username );
				
				//echo ROOT_DIR . "/uploadfiles/avatar_small_" . $username . ".jpg";
				
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "', name = '" . $db->safesql( $twitterInfo['name'] ) . "', location = '" . $db->safesql( $twitterInfo['location'] ) . "', bio  = '" . $db->safesql( $twitterInfo['description'] ) . "', avatar = '1'");
				
			}else{
			
				$db->query("INSERT INTO vass_users SET username = '" . $username . "', email = '" . $email . "', password = '" . md5( $password ) . "', reg_date = '" . date( "Y-m-d H:i:s", time() ) . "'");
				
			}
			
			$login_user = $username;
			
			$login_pass = md5( $password );
			
			$member_id = array ();
			
			$member_id = $db->super_query("SELECT * FROM " . PREFIX . "_users WHERE username = '" . $login_user . "' AND password ='" . $login_pass . "'");
			
			@session_register( 'logged' );
			
			@session_register( 'member' );
			
			$_SESSION['logged'] = TRUE;
			
			$_SESSION['member'] = $member_id;
			
			set_cookie( "user_id", $member_id['user_id'], 365 );
			
			set_cookie( "login_user", $login_user, 365 );
			
			set_cookie( "login_pass", $login_pass, 365 );
			
			$row = $db->super_query("SELECT vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id WHERE vass_users.user_id = '" . $_SESSION['member']['user_id'] . "';");
			
			if( $social == 'facebook' && $_SESSION['facebook'] ) $db->query("INSERT INTO vass_facebook SET user_id = '" . $_SESSION['member']['user_id'] . "', screen_id = '" . $db->safesql( $user->id ) . "', screen_name = '" . $db->safesql( $user->username ) . "', name = '" . $db->safesql( $user->name ) . "', profile_image_url = 'https://graph.facebook.com/" . $user->id . "/picture?type=large', token  = '" . $db->safesql( $_SESSION['access_token'] ) . "', date = '" . date( "Y-m-d H:i:s", time() ) . "'");
			
			if( $social == 'twitter' && $_SESSION['twitter'] ) $db->query("INSERT INTO vass_twitter SET user_id = '" . $_SESSION['member']['user_id'] . "', screen_id = '" . $db->safesql( $twitterInfo['id'] ) . "', screen_name = '" . $db->safesql( $twitterInfo['screen_name'] ) . "', name = '" . $db->safesql( $twitterInfo['name'] ) . "', profile_image_url = '" . $db->safesql( $twitterInfo['profile_image_url'] ) . "', oauth_token  = '" . $db->safesql( $_SESSION['oauth_token'] ) . "', oauth_token_secret = '" . $db->safesql( $_SESSION['oauth_token_secret'] ) . "', date = '" . date( "Y-m-d H:i:s", time() ) . "'");
			
			if( $row['image'] ) {
				
				$use_image = "true";
				
				$is_default = "false";
				
			} else {
				
				$is_default = "true";
				
				$use_image = "false";
				
			}
			
			$buffer =  '{
						    "status_code": 200, 
						    "status_text": "OK", 
						    "user": {
						        "username": ' . json_encode ( $_SESSION['member']['username'] ) . ',
						        "bio": ' . json_encode ( $_SESSION['member']['bio'] ) . ',
						        "image": {
					            	';
					        
					        $buffer .= avatar( $_SESSION['member']['avatar'], $_SESSION['member']['username'] );
					        
					        $buffer .='
						        }, 
						        "import_feeds": [], 
						        "background": {
						        	"repeat": ' . json_encode ( $row['repeat'] ) . ',
						        	"color": ' . json_encode ( $row['color'] ) . ',
						        	"image": ' . json_encode ( $row['image'] ) . ',
						        	"is_default": ' . $is_default . ',
						        	"use_image": ' . $use_image . ',
						        	"position": ' . json_encode ( $row['position'] ) . '
						        }, 
						        "name": ' . json_encode ( $_SESSION['member']['name'] ) . ',
						        "is_beta_tester": false, 
						        "website": ' . json_encode ( $_SESSION['member']['site'] ) . ',
						        "total_loved": ' . json_encode ( $_SESSION['member']['total_loved'] ) . ',
						        "total_following": ' . json_encode ( $_SESSION['member']['total_following'] ) . ',
						        "total_followers": ' . json_encode ( $_SESSION['member']['total_followers'] ) . ',
						        "viewer_following": false, 
						        "location": ' . json_encode ( $_SESSION['member']['location'] ) . '
						    }
						}';
			
		}
		
	}
	
}

header('Cache-Control: no-cache, must-revalidate');

header('Content-type: application/json');

print $buffer;
?>