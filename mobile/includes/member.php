<?php
$logged = 0;

$member_id = array ();


//{"header":{"session":"e37b4b7ba1d5cc3dfde822640b2deb88","serviceVersion":"20100903","prefetchEnabled":true},"result":{"username":"admin","UserID":1,"user_group":"1","total_loved":"0","total_following":"0","total_followers":"0","reg_date":null,"last_date":"2014-06-21 19:28:42","name":"","location":"","website":"","bio":"","avatar":"1","userPrivacyTokens":{"authenticated":"0bc886b2f702d17b4e96a314a858a618797a3e62652c1d","unauthenticated":"0bc886b2f702d17b4e96a314a858a618797a3e62652c1d"},"flags":null,"authToken":"0bc886b2f702d17b4e96a314a858a618797a3e62652c1d","badAuthToken":false,"Picture":"1.jpg"}}

if( isset( $_POST['username'] ) and isset( $_POST['password'] ) ) {

	$_POST['username'] = $db->safesql( $_POST['username'] );
	
	$_POST['password'] = md5 ( $_POST['password'] );
	
	if( $_POST['username'] ) {
	
		$member_id = $db->super_query( "SELECT password AS token, username AS FName, name AS LName, avatar AS Picture, email AS Email, user_id AS UserID, username, total_following AS Following, total_followers AS Followed, total_loved AS FavoriteSong FROM vass_users where username ='{$_POST['username']}' and password='" . $_POST['password'] . "'" );
		
		if( $member_id['UserID'] ) {
			
			set_cookie( "lala_UserID", $member_id['UserID'], 365 );
			set_cookie( "lala_password", $_POST['password'], 365 );
			$_SESSION['lala_UserID'] = $member_id['UserID'];
			$_SESSION['lala_password'] = $_POST['password'];
			$logged = TRUE;
		}
	}

} elseif( isset( $_SESSION['lala_UserID'] ) AND  intval( $_SESSION['lala_UserID'] ) > 0 AND $_SESSION['lala_password'] ) {
		
		$member_id = $db->super_query( "SELECT password AS token, password, username AS FName, name AS LName, avatar AS Picture, email AS Email, user_id AS UserID, username, total_following AS Following, total_followers AS Followed, total_loved AS FavoriteSong FROM vass_users WHERE user_id ='" . intval( $_SESSION['lala_UserID'] ) . "'" );
		
		if( $member_id['password'] == $_SESSION['lala_password'] ) {
			$logged = TRUE;
		
		} else {
			
			$member_id = array ();
			$logged = false;
		}

} elseif( isset( $_COOKIE['lala_UserID'] ) AND intval( $_COOKIE['lala_UserID'] ) > 0 ) {
	
		$member_id = $db->super_query( "SELECT password AS token, password, username AS FName, name AS LName, avatar AS Picture, email AS Email, user_id AS UserID, username, total_following AS Following, total_followers AS Followed, total_loved AS FavoriteSong FROM vass_users WHERE user_id ='" . intval( $_COOKIE['lala_UserID'] ) . "'" );
		
		if( $member_id['password'] == $_COOKIE['lala_password'] ) {
			
			$logged = TRUE;
			
			$_SESSION['lala_UserID'] = $member_id['UserID'];
			$_SESSION['lala_password'] = $_COOKIE['lala_password'];
		
		} else {
			
			$member_id = array ();
			$logged = false;
		
		}

}

if($logged){
	unset($member_id['password'], $member_id['email'], $member_id['logged_ip'], $member_id['hash'], $member_id['token']);
}
?>