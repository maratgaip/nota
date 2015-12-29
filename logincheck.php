<?php


define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

@include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

require_once INCLUDE_DIR . '/member.php';

// Getting username and password from login form




$password1 = mysql_real_escape_string($_POST['password']);

$username = mysql_real_escape_string($_POST['username']);

$password = md5($password1);

// To protect MySQL injection

//$username = stripslashes($username);

//$password = stripslashes($password);

//$username = mysql_real_escape_string($username);

//$password = mysql_real_escape_string($password);

$sql="SELECT * FROM vass_users WHERE username='$username' and password='$password'";

$result=mysql_query($sql);

// Mysql_num_row is to count number of row from the above query
$count=mysql_num_rows($result);

// count is 1 if the above username and password matches
if($count==1){

// now redirect to dashboard page, we also store the username in session for further use in dashboard
header("Location: index.php");
die();
}

//if the username and password doesn't match redirect to homepage with message=1
else {

	header("Location: landing.php");
	die();

}

?>