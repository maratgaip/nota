
<?php

// load libraries

require_once('src/Facebook/FacebookSession.php');
require_once('src/Facebook/FacebookRequest.php');
require_once('src/Facebook/FacebookResponse.php');
require_once('src/Facebook/FacebookSDKException.php');
require_once('src/Facebook/FacebookRequestException.php');
require_once('src/Facebook/FacebookRedirectLoginHelper.php');
require_once('src/Facebook/FacebookAuthorizationException.php');
require_once('src/Facebook/GraphObject.php');
require_once('src/Facebook/GraphSessionInfo.php');
require_once('src/Facebook/GraphUser.php');
require_once('src/Facebook/Entities/AccessToken.php');
require_once('src/Facebook/HttpClients/FacebookCurl.php');
require_once('src/Facebook/HttpClients/FacebookHttpable.php');
require_once('src/Facebook/HttpClients/FacebookCurlHttpClient.php');


// use namespaces

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\SDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookCurl;

//wrap in try and catch just in case any error might occour!
try{
	//start session
	session_start();

	if(isset($_REQUEST['logout'])){

		unset($_SESSION['fb_token']);
	}

	//my facebook app id
	$app_id ='869447339769084';

	//my facebook app secret
	$app_secret = '637cddd0a52beccdc0b5d549927a9dbb';

	//url redirect
	$redirect_url ='http://kiandastream.globusapps.com/apps/login';

	FacebookSession::setDefaultApplication($app_id, $app_secret);
	$helper = new FacebookRedirectLoginHelper($redirect_url);
	$sess = $helper->getSessionFromRedirect();


	//check existing session
	if(isset($_SESSION['fb_token'])){

		$sess = new FacebookSession($_SESSION['fb_token']);
	}

	//logout
	$logout = 'http://kiandastream.globusapps.com//apps/login?logout=true';

	if(isset($sess)){

		//store token in session
		$accessToken = $sess->getAccessToken();
		$_SESSION['fb_token'] = $accessToken->extend();

		//capture response create object
		$request = new FacebookRequest($sess, 'GET','/me');

		//get the graph object
		$response = $request->execute();
		$graph = $response->getGraphObject(GraphUser::classname());

		//get user details from graph api
		$name = $graph->getName();
		$id = $graph->getId();
		$email = $graph->getProperty('email');
		//getting user image
		$image = 'http://graph.facebook.com/'.$id.'/picture?width=300';

		//displaying user details
		echo "<img src='".$image."'/>";
		echo "Hello ".$name." </br> ";
		echo "Email ".$email." </br> ";
		echo "Your Facebook ID ".$id." </br> ";
		echo "<a href='".$logout."'>Logout</a>";

	}else{

		echo "<a href='".$helper->getLoginUrl(array('email','user_about_me'))."'>Login With Facebook</a>";
	}

}
catch(FacebookRequestException $ex){

	echo "Error Occurred, Code: ". $ex->getCode();

}
?>

