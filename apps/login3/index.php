<?php
error_reporting(0);
include 'library.php';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Asif18 tutorial about facebook login for mywebsite using php sdk</title>
<script type="text/javascript">
window.fbAsyncInit = function() {
	FB.init({
	appId      : 'XXXXXXXXXXXXXXX', // replace your app id here
	channelUrl : '//WWW.YOUR_DOMAIN.COM/channel.html', 
	status     : true, 
	cookie     : true, 
	xfbml      : true  
	});
};
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

function FBLogin(){
	FB.login(function(response){
		if(response.authResponse){
			window.location.href = "actions.php?action=fblogin";
		}
	}, {scope: 'email,user_likes'});
}
</script>
<style>
body{
	font-family:Arial;
	color:#333;
	font-size:14px;
}
</style>
</head>

<body>
<h1>Asif18 tutorial for facebook ling using php, javascript sdk dymaically</h1>
<img src="facebook-connect.png" alt="Fb Connect" title="Login with facebook" onclick="FBLogin();"/>
</body>
</html>
