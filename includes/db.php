<?PHP

define ("MYSQLHOST", "localhost");

define ("MYSQLDBNAME", "notadb");

define ("MYSQLDBUSER", "root");

// mysql info for local
define ("MYSQLDBPASS", "");

// mysql info for server in KG
//define ("MYSQLDBPASS", "Not@db");


define ("COLLATE", "utf8");



$db = new mysqldb;

?>