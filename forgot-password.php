<?php
/*
@session_start ();

define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

*/

header('HTTP/1.0 400 BAD REQUEST');

$buffer =  '{
"status_code": 400, 
"status_text": "Email is not in our database, please check."
}';

print $buffer;

?>