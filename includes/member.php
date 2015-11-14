<?php
$logged = 0;

$member_id = array ();

if( isset( $_POST['username'] ) and isset( $_POST['password'] ) ) {
	
	$_POST['username'] = $db->safesql( $_POST['username'] );
	
	$_POST['password'] = md5 ( $_POST['password'] );
	
	if( $_POST['username'] ) {
		
		$member_id = $db->super_query( "SELECT * FROM vass_users where Email ='{$_POST['username']}' and password='" . $_POST['password'] . "'" );
		if( ! $member_id['user_id'] ) $member_id = $db->super_query( "SELECT * FROM vass_users where username ='{$_POST['username']}' and password='" . $_POST['password'] . "'" );
		
		if( $member_id['user_id'] ) {
			
			set_cookie( "user_id", $member_id['user_id'], 365 );
			set_cookie( "login_pass", $_POST['password'], 365 );
			$_SESSION['user_id'] = $member_id['user_id'];
			$_SESSION['login_pass'] = $_POST['password'];
			$logged = TRUE;
		}
	}

} elseif( isset( $_SESSION['user_id'] ) AND  intval( $_SESSION['user_id'] ) > 0 AND $_SESSION['login_pass'] ) {
	
		$member_id = $db->super_query( "SELECT * FROM vass_users WHERE user_id='" . intval( $_SESSION['user_id'] ) . "'" );
		
		if( $member_id['password'] == $_SESSION['login_pass'] ) {
			
			$logged = TRUE;
		
		} else {
			
			$member_id = array ();
			$logged = false;
		}

} elseif( isset( $_COOKIE['user_id'] ) AND intval( $_COOKIE['user_id'] ) > 0 ) {
	
		$member_id = $db->super_query( "SELECT * FROM vass_users WHERE user_id='" . intval( $_COOKIE['user_id'] ) . "'" );
		
		if( $member_id['password'] == $_COOKIE['login_pass'] ) {
			
			$logged = TRUE;
			
			$_SESSION['user_id'] = $member_id['user_id'];
			$_SESSION['login_pass'] = $_COOKIE['login_pass'];
		
		} else {
			
			$member_id = array ();
			$logged = false;
		
		}

}

if($logged){
	
	unset($member_id['password'], $member_id['logged_ip'], $member_id['hash'], $member_id['token']);
}
?>