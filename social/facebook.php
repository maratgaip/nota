<?php

@session_start();

define('ROOT_DIR', '..');

define('INCLUDE_DIR', ROOT_DIR . '/includes');

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

require_once INCLUDE_DIR . '/member.php';

$_TIME = date("Y-m-d H:i:s", time());

$oauth_token = $_REQUEST['code'];


if ($oauth_token) {

    $token_url = "https://graph.facebook.com/oauth/access_token?"
            . "client_id=" . $config['facebook_client_id'] . "&redirect_uri=" . urlencode($config['siteurl'] . 'social/facebook')
            . "&client_secret=" . $config['facebook_client_secret'] . "&code=" . $oauth_token;


   echo $token_url;


    $response = get_content($token_url);
     //   echo $response;die("sdg");


    $params = null;

    parse_str($response, $params);
     // echo $params;die("sdg");
    $info_url = 'https://graph.facebook.com/me?access_token=' . $params['access_token'];
   
    $response = get_content($info_url);
 // echo $response;die("sdg");
    $user = json_decode($response);
    
    if ($logged) {
        $db->query("INSERT IGNORE INTO vass_facebook SET user_id = '" . $member_id['user_id'] . "', screen_id = '" . $user->id . "',
		screen_name = '" . $db->safesql($user->username) . "', name = '" . $db->safesql($user->name) . "',
		profile_image_url = 'https://graph.facebook.com/" . $user->id . "/picture?type=large', token = '" . $db->safesql($params['access_token']) . "', `date` = '$_TIME'
		");

        $json['obj']['success'] = true;
        $json['obj']['type'] = "facebook";
        $json['obj']['name'] = $user->username;
        $json['obj']['pic'] = 'https://graph.facebook.com/' . $user->id . '/picture?type=large';
        $json['obj']['lookup_id'] = $user->id;
       
        print '
			<html>
			<head>
			    <title>Nota Signup with Facebook or Twitter</title>
			    <script>
					window.opener.SettingsConnections.Add.render(' . json_encode($json) . ');
			       window.close();
			    </script>
			</head>
			<body></body>
			</html>';
    } else {
	
        //ready register so make login for them, fuck
        $row = $db->super_query("SELECT user_id FROM vass_facebook WHERE screen_id  = '" . $user->id . "'");
         
        if ($row['user_id']) {

            $member_id = array();

            $member_id = $db->super_query("SELECT * FROM vass_users WHERE user_id = '" . $row['user_id'] . "'");

            if ($member_id['user_id']) {

                set_cookie("user_id", $member_id['user_id'], 365);
                set_cookie("login_pass", $member_id['password'], 365);
                $_SESSION['user_id'] = $member_id['user_id'];
                $_SESSION['login_pass'] = $member_id['password'];
                $logged = TRUE;

                $buffer ['status_code'] = 200;

                $buffer ['status_text'] = "OK";

                $buffer = $member_id;
                $buffer ['viewer_following'] = false;
                $buffer ['import_feeds'] = import_feeds($member_id ['user_id']);
                $buffer ['image'] = avatar($member_id ['avatar'], $member_id ['username']);

                $row = $db->super_query("SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';");

                if ($row ['image']) {
                    $use_image = true;
                    $is_default = false;
                } else {
                    $is_default = true;
                    $use_image = false;
                }

                $buffer ['background'] = $row;
                $buffer ['background'] ['is_default'] = $is_default;
                $buffer ['background'] ['use_image'] = $use_image;

                unset($buffer ['password']);

                $json['response']['message'] = 'You was signed in successfully.';
                $json['response']['user'] = $buffer;
                $json['response']['service']['name'] = $user->name;
                $json['response']['service']['pic'] = 'https://graph.facebook.com/' . $user->id . '/picture?type=large';
                $json['response']['service']['lookup_id'] = $user->id;
                $json['response']['service']['type'] = "facebook";
                $json['response']['service']['added_on'] = $_TIME;
                $json['success'] = true;

                print '
				<html>
				    <head>
				        <title>Nota sign in</title>
				        <script>
				            window.opener.CreateAccount.AlreadyLoggedIn(' . json_encode($json) . ');
				            window.close();
				        </script>
				    </head>
				    <body></body>
				</html>';

                $db->close();
            }
        } else {

            $_SESSION['facebook'] = array();

            $_SESSION['facebook'] = $user;

            $_SESSION['access_token'] = $params['access_token'];
			
			 

            print '
			<html>
			<head>
			    <title>Nota Signup with Facebook or Twitter</title>
			    <script>
			        window.opener.CreateAccount.Service.listener({"response": {"service": {"info": {"website": null, "bio": "' . $user->bio . '", "is_default_profile_image": false, "name": "' . $user->name . '", "pic": "https:\/\/graph.facebook.com\/' . $user->id . '\/picture?type=large", "username": "' . $user->username . '", "location": "' . $user->location->name . '", "lookup_id": "' . $user->id . '", "service_username": "' . $user->username . '", "email": "' . $user->email . '"}, "social": "facebook"}}, "success": true});
			        window.close();
			    </script>
			</head>
			<body></body>
			</html>';
        }
    }
}
?>
