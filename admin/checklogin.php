<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Masters Panel Login | Nota</title>
</head>

<body>

<?php

$host="admindb.db.11192847.hostedresource.com"; // Host name 
//$host="localhost"; // Host name 
$username="admindb"; // Mysql username 
$password="Kianda123!"; // Mysql password 
$db_name="admindb"; // Database name 
$tbl_name="members"; // Table name 

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("Connection to database failed"); 
mysql_select_db("$db_name")or die("Database selection failed!");

// username and password sent from form 
$myusername=$_POST['myusername']; 
$mypassword=$_POST['mypassword']; 

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);
$sql="SELECT * FROM $tbl_name WHERE username='$myusername' and password='$mypassword'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){

// Register $myusername, $mypassword and redirect to file "login_success.php"
//session_register("myusername");
//session_register("mypassword"); 
//header("location:master.php");
echo '<script language="javascript">';
	echo 'setTimeout(function () {
   window.location.href = "master.php";
   }, 30)';
   	echo '</script>';
}
else {
	
	//echo "Wrong Username or Password";
	echo '<script language="javascript">';
	echo 'alert("Wrong Username or Password! - Try again")';
	echo '</script>';
	
	print "Redirecting you back to the master login page...";
	
	echo '<script language="javascript">';
	echo 'setTimeout(function () {
   window.location.href = "index.php";
   }, 1000)';
   	echo '</script>';

}

?>

</body>

</html>