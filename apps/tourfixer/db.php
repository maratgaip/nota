<?php
define(DB_SERVER, 'youplanitDB.db.11192847.hostedresource.com');
define(DB_USERNAME, 'youplanitDB');
define(DB_PASSWORD, 'Planitdb123!');
define(DB_DATABASE, 'youplanitDB');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die ("Was not able to connect to database server");
$database = mysql_select_db(DB_DATABASE) or die ("Was not able to connect to database");

?>