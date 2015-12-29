<?php

require 'db.php';

function checkuser($fbuid,$fbfullname,$fbemail){
	$check = mysql_query("SELECT * FROM users WHERE fbid='$fbuid'");
	$check = mysql_num_rows($check);
	if(empty($check)){
		$query = "INSERT INTO users (fbid,name,email) VALUES ('$fbuid','$fbfullname','$fbemail')";
		mysql_query($query);
	} 
	else{
		$query = "UPDATE users SET name='$fbfullname', email='fbemail' where fbid='$fbuid'";
		mysql_query($query);
	}

}

?>