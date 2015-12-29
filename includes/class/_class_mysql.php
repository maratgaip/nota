<?php
date_default_timezone_set('US/Central');
if ( extension_loaded('mysqli') AND version_compare("5.0.5", phpversion(), "!=") )
{
	class mysqldb
		{
			var $db_id = false;
			var $query_num = 0;
			var $query_list = array();
			var $mysql_error = '';
			var $mysql_version = '';
			var $mysql_error_num = 0;
			var $mysql_extend = "MySQLi";
			var $MySQL_time_taken = 0;
			var $query_id = false;

			function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error=1)
			{
				$db_location = explode(":", $db_location);
				if (isset($db_location[1])) {
					$this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name, $db_location[1]);
				} else {
					$this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name);
				}
				if(!$this->db_id) {
					if($show_error == 1) {
						$this->display_error(mysqli_connect_error(), '1');
					} else {
						return false;
					}
				} 
				$this->mysql_version = mysqli_get_server_info($this->db_id);
				if(!defined('COLLATE'))
				{ 
					define ("COLLATE", "cp1251");
				}
				mysqli_query($this->db_id, "SET NAMES '" . COLLATE . "'");
				return true;
			}
			
			function query($query, $show_error=true)
			{
				$time_before = $this->get_real_time();
				if(!$this->db_id) $this->connect(MYSQLDBUSER, MYSQLDBPASS, MYSQLDBNAME, MYSQLHOST);

				if(!($this->query_id = mysqli_query($this->db_id, $query) )) {
					$this->mysql_error = mysqli_error($this->db_id);
					$this->mysql_error_num = mysqli_errno($this->db_id);
					if($show_error) {
						$this->display_error($this->mysql_error, $this->mysql_error_num, $query);
					}
				}
					
				$this->MySQL_time_taken += $this->get_real_time() - $time_before;
				
		//			$this->query_list[] = array( 'time'  => ($this->get_real_time() - $time_before), 
		//										 'query' => $query,
		//										 'num'   => (count($this->query_list) + 1));
				
				$this->query_num ++;
				return $this->query_id;
			}
			
			function get_row($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysqli_fetch_assoc($query_id);
			}
			function get_array($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysqli_fetch_array($query_id);
			}
			
			function super_query($query, $multi = false)
			{
				if(!$multi) {
					$this->query($query);
					$data = $this->get_row();
					$this->free();			
					return $data;
				} else {
					$this->query($query);
					
					$rows = array();
					while($row = $this->get_row()) {
						$rows[] = $row;
					}
					$this->free();			
					return $rows;
				}
			}
			
			function num_rows($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysqli_num_rows($query_id);
			}
			
			function insert_id()
			{
				return mysqli_insert_id($this->db_id);
			}
			function get_result_fields($query_id = '') {
				if ($query_id == '') $query_id = $this->query_id;
				while ($field = mysqli_fetch_field($query_id))
				{
		            $fields[] = $field;
				}
				
				return $fields;
		   	}
			function safesql( $source )
			{
				if ($this->db_id) return mysqli_real_escape_string ($this->db_id, $source);
				else return addslashes($source);
				
			}
			function free( $query_id = '' )
			{
				if ($query_id == '') $query_id = $this->query_id;
				@mysqli_free_result($query_id);
			}
			function close()
			{
				@mysqli_close($this->db_id);
			}
			function get_real_time()
			{
				list($seconds, $microSeconds) = explode(' ', microtime());
				return ((float)$seconds + (float)$microSeconds);
			}	
			function display_error($error, $error_num, $query = '')
			{
				if($query) {
					// Safify query
					$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
					$query_str = "$query";
				}
				
				$sqlerror="<table cellpadding=\"0\" cellspacing=\4\" class=\"smallgrey\" border=\"0\" align=\"center\">\n";
				$sqlerror.="<tr><td class=\"smallorange\"><font size=4><B><U>".$error."</U></B></td></tr>\n";
				$sqlerror.="<tr><td height=\"10\"></td></tr>\n";
				$sqlerror.="<tr><td>MySQL Error </td><td>: ".$query_str."</td></tr>\n";
				$sqlerror.="<tr><td>Error Number</td><td>: ".$error_num."</td></tr>\n";
				$sqlerror.="<tr><td>Date</td><td>: ".date("D, F j, Y H:i:s")."</td></tr>\n";
				$sqlerror.="<tr><td>IP</td><td>: ".getenv("REMOTE_ADDR")."</td></tr>\n";
				$sqlerror.="<tr><td>Browser</td><td>: ".getenv("HTTP_USER_AGENT")."</td></tr>\n";
				$sqlerror.="<tr><td>Script</td><td>: ".getenv("REQUEST_URI")."</td></tr>\n";
				$sqlerror.="<tr><td>Referer</td><td>: ".getenv("HTTP_REFERER")."</td></tr>\n";
				$sqlerror.="<tr><td>PHP Version </td><td>: ".PHP_VERSION."</td></tr>\n";
				$sqlerror.="<tr><td>OS</td><td>: ".PHP_OS."</td></tr>\n";
				$sqlerror.="<tr><td>Server</td><td>: ".getenv("SERVER_SOFTWARE")."</td></tr>\n";
				$sqlerror.="<tr><td>Server Name</td><td>: ".getenv("SERVER_NAME")."</td></tr>\n";
				$sqlerror.="</table>";

				echo '<?xml version="1.0" encoding="iso-8859-1"?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<title>MySQL Fatal Error</title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<style type="text/css">
				<!--
					BODY {
					FONT-SIZE: 8pt; COLOR: #000000; FONT-STYLE: normal;  FONT-FAMILY: Arial, Verdana;
					SCROLLBAR-FACE-COLOR: #e8e8e8; SCROLLBAR-HIGHLIGHT-COLOR: #ffffff; OVERFLOW: auto; SCROLLBAR-SHADOW-COLOR: #ffffff; SCROLLBAR-3DLIGHT-COLOR: #cccccc; SCROLLBAR-ARROW-COLOR: #e8e8e8; SCROLLBAR-TRACK-COLOR: #eeeeee; SCROLLBAR-DARKSHADOW-COLOR: #cccccc
					}
					.smallorange {
					FONT-SIZE: 8pt; COLOR: #FF6600; FONT-FAMILY: Arial, Verdana
					}
				-->
				</style>
				</head>
				<body>
				'.$sqlerror.'
				</body>
				</html>';
				
				exit();
			}
		}

}
else
{
	class mysqldb1
		{
			var $db_id = false;
			var $connected = false;
			var $query_num = 0;
			var $query_list = array();
			var $mysql_error = '';
			var $mysql_version = '';
			var $mysql_error_num = 0;
			var $mysql_extend = "MySQL";
			var $MySQL_time_taken = 0;
			var $query_id = false;
			
			function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error=1)
			{
				if(!$this->db_id = @mysql_connect($db_location, $db_user, $db_pass)) {
					if($show_error == 1) {
						$this->display_error(mysql_error(), mysql_errno());
					} else {
						return false;
					}
				} 
				if(!@mysql_select_db($db_name, $this->db_id)) {
					if($show_error == 1) {
						$this->display_error(mysql_error(), mysql_errno());
					} else {
						return false;
					}
				}
				$this->mysql_version = mysql_get_server_info();
				if(!defined('COLLATE'))
				{ 
					define ("COLLATE", "cp1251");
				}
				if (version_compare($this->mysql_version, '4.1', ">=")) mysql_query("/*!40101 SET NAMES '" . COLLATE . "' */");
				$this->connected = true;
				return true;
			}
			
			function query($query, $show_error=true)
			{
				$time_before = $this->get_real_time();
				if(!$this->connected) $this->connect(MYSQLDBUSER, MYSQLDBPASS, MYSQLDBNAME, MYSQLHOST);
				
				if(!($this->query_id = mysql_query($query, $this->db_id) )) {
					$this->mysql_error = mysql_error();
					$this->mysql_error_num = mysql_errno();
					if($show_error) {
						$this->display_error($this->mysql_error, $this->mysql_error_num, $query);
					}
				}
					
				$this->MySQL_time_taken += $this->get_real_time() - $time_before;
				
		//			$this->query_list[] = array( 'time'  => ($this->get_real_time() - $time_before), 
		//										 'query' => $query,
		//										 'num'   => (count($this->query_list) + 1));
				$this->query_num ++;
				return $this->query_id;
			}
			
			function get_row($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysql_fetch_assoc($query_id);
			}
			function get_array($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysql_fetch_array($query_id);
			}
			
			
			function super_query($query, $multi = false)
			{
				if(!$multi) {
					$this->query($query);
					$data = $this->get_row();
					$this->free();			
					return $data;
				} else {
					$this->query($query);
					
					$rows = array();
					while($row = $this->get_row()) {
						$rows[] = $row;
					}
					$this->free();			
					return $rows;
				}
			}
			
			function num_rows($query_id = '')
			{
				if ($query_id == '') $query_id = $this->query_id;
				return mysql_num_rows($query_id);
			}
			
			function insert_id()
			{
				return mysql_insert_id($this->db_id);
			}
			function get_result_fields($query_id = '') {
				if ($query_id == '') $query_id = $this->query_id;
				while ($field = mysql_fetch_field($query_id))
				{
		            $fields[] = $field;
				}
				
				return $fields;
		   	}
			function safesql( $source )
			{
				if ($this->db_id) return mysql_real_escape_string ($source, $this->db_id);
				else return mysql_escape_string($source);
			}
			function free( $query_id = '' )
			{
				if ($query_id == '') $query_id = $this->query_id;
				@mysql_free_result($query_id);
			}
			function close()
			{
				@mysql_close($this->db_id);
			}
			function get_real_time()
			{
				list($seconds, $microSeconds) = explode(' ', microtime());
				return ((float)$seconds + (float)$microSeconds);
			}	
			function display_error($error, $error_num, $query = '')
			{
				if($query) {
					// Safify query
					$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
					$query_str = "$query";
				}
				
				$sqlerror="<table cellpadding=\"0\" cellspacing=\4\" class=\"smallgrey\" border=\"0\" align=\"center\">\n";
				$sqlerror.="<tr><td class=\"smallorange\"><font size=4><B><U>".$error."</U></B></td></tr>\n";
				$sqlerror.="<tr><td height=\"10\"></td></tr>\n";
				$sqlerror.="<tr><td>MySQL Error </td><td>: ".$query_str."</td></tr>\n";
				$sqlerror.="<tr><td>Error Number</td><td>: ".$error_num."</td></tr>\n";
				$sqlerror.="<tr><td>Date</td><td>: ".date("D, F j, Y H:i:s")."</td></tr>\n";
				$sqlerror.="<tr><td>IP</td><td>: ".getenv("REMOTE_ADDR")."</td></tr>\n";
				$sqlerror.="<tr><td>Browser</td><td>: ".getenv("HTTP_USER_AGENT")."</td></tr>\n";
				$sqlerror.="<tr><td>Script</td><td>: ".getenv("REQUEST_URI")."</td></tr>\n";
				$sqlerror.="<tr><td>Referer</td><td>: ".getenv("HTTP_REFERER")."</td></tr>\n";
				$sqlerror.="<tr><td>PHP Version </td><td>: ".PHP_VERSION."</td></tr>\n";
				$sqlerror.="<tr><td>OS</td><td>: ".PHP_OS."</td></tr>\n";
				$sqlerror.="<tr><td>Server</td><td>: ".getenv("SERVER_SOFTWARE")."</td></tr>\n";
				$sqlerror.="<tr><td>Server Name</td><td>: ".getenv("SERVER_NAME")."</td></tr>\n";
				$sqlerror.="</table>";

				echo '<?xml version="1.0" encoding="iso-8859-1"?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<title>MySQL Fatal Error</title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<style type="text/css">
				<!--
					BODY {
					FONT-SIZE: 8pt; COLOR: #000000; FONT-STYLE: normal;  FONT-FAMILY: Arial, Verdana;
					SCROLLBAR-FACE-COLOR: #e8e8e8; SCROLLBAR-HIGHLIGHT-COLOR: #ffffff; OVERFLOW: auto; SCROLLBAR-SHADOW-COLOR: #ffffff; SCROLLBAR-3DLIGHT-COLOR: #cccccc; SCROLLBAR-ARROW-COLOR: #e8e8e8; SCROLLBAR-TRACK-COLOR: #eeeeee; SCROLLBAR-DARKSHADOW-COLOR: #cccccc
					}
					.smallorange {
					FONT-SIZE: 8pt; COLOR: #FF6600; FONT-FAMILY: Arial, Verdana
					}
				-->
				</style>
				</head>
				<body>
				'.$sqlerror.'
				</body>
				</html>';
				
				exit();
			}
		}

}
?>