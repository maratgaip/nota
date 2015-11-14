<?php

function set_vars($file, $data) {
	
	$filename = ROOT_DIR . '/cache/admin/' . $file . '.php';
	
	$fp = @fopen( $filename, 'wb+' );
	@fwrite( $fp, serialize( $data ) );
	@fclose( $fp );
	
	@chmod( $filename, 0666 );
}

function get_vars($file) {
	$filename = ROOT_DIR . '/cache/admin/' . $file . '.php';
	
	if( ! @filesize( $filename ) ) {
		return false;
	}
	
	return unserialize( file_get_contents( $filename ) );

}
function get_groups($id = false) {
	global $user_group;
	
	$returnstring = "";
	
	foreach ( $user_group as $group ) {
		$returnstring .= '<option value="' . $group['id'] . '" ';
		
		if( is_array( $id ) ) {
			foreach ( $id as $element ) {
				if( $element == $group['id'] ) $returnstring .= 'SELECTED';
			}
		} elseif( $id and $id == $group['id'] ) $returnstring .= 'SELECTED';
		
		$returnstring .= ">" . $group['group_name'] . "</option>\n";
	}
	
	return $returnstring;

}
function convert_unicode($t, $to = 'windows-1251') {
	$to = strtolower( $to );

	if( $to == 'utf-8' ) {
		
		return $t;
	
	} else {
		
		if( function_exists( 'iconv' ) ) $t = iconv( "UTF-8", $to . "//IGNORE", $t );
		else $t = "The library iconv is not supported by your server";
	
	}

	return $t;
}
function makeDropDown($options, $name, $selected) {
	$output = "<select name=\"$name\" class=\"box2\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function getSlug($str = null)
  {
    if( null === $str ) {
      $str = $this->getTitle();
    }
    if( strlen($str) > 32 ) {
      $str = substr($str, 0, 32) . '...';
    }
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');
    if( !$str ) {
      $str = '-';
    }
    return $str;
}

function shorter($text, $chars_limit)
{
    if (strlen($text) > $chars_limit)
    {
        $new_text = substr($text, 0, $chars_limit);
        $new_text = trim($new_text);
        return $new_text . "...";
    }
    else
    {
    return $text;
    }
}

function beautifier($string, $plus = FALSE) {
  $string = strip_tags( trim( $string) );
	$string = htmlkarakter( $string );
	$string = ucwords(strtolower($string));
	
	if($plus) $string = str_replace( " ", "+", $string );
	
  return $string;
}

function htmlkarakter($string)
{
  $string = str_replace(array("&lt;", "&gt;", '&amp;', '&#039;', '&quot;','&lt;', '&gt;'), array("<", ">",'&','\'','"','<','>'), htmlspecialchars_decode($string, ENT_NOQUOTES));

    return $string;
 
}

function gen_uuid($string, $len=8)
{
  $hex = md5($string);
  $pack = pack('H*', $hex);
  $uid = base64_encode($pack);
	$uid = preg_replace('/[^a-zA-Z 0-9]+/', "", $uid);
  if ($len<4)
    $len=4;
  if ($len>128)
    $len=128;
  while (strlen($uid)<$len)
    $uid = $uid . gen_uuid(22);
  return substr($uid, 0, $len);
}

function play_url($hash, $source, $name){
	
	global $config;
	
	$name = trim(strip_tags($name));
	
	$name = preg_replace("/[^a-zA-Z 0-9]+/", " ", $name);
	
	$name = str_replace(" ","-",$name);
	
	$url = $config['siteurl'] . "mp3/$source/$name/$hash/";
	
  return $url;
  
}

function lyrics_url($title, $artist, $hash){
	
	global $config;
	
	$title = trim(strip_tags($title));
	
	$title = preg_replace("/[^a-zA-Z 0-9]+/", "", $title);
	
	$title = preg_replace("/[[:blank:]]+/"," ",$title);
	
	$title = str_replace(" ","-",$title);
	
	$artist = trim(strip_tags($artist));
	
	$artist = preg_replace("/[^a-zA-Z 0-9]+/", "", $artist);
	
	$artist = preg_replace("/[[:blank:]]+/"," ",$artist);
	
	$artist = str_replace(" ","-",$artist);
	
	$url = $config['siteurl'] . "lyrics/$title-$artist-$hash.html";
	
  return $url;
  
}

function file_name($string){
	
	global $config;
	
	$string = trim(strip_tags($string));
	
	$string = preg_replace("/[^a-zA-Z 0-9]+/", "", $string);
	
	$string = preg_replace("/[[:blank:]]+/"," ",$string);
	
	$string = str_replace(" ","_",$string);
	
  return $string;
  
}

function remove_symbol($string){
	
	$string = preg_replace("/[^a-zA-Z 0-9]+/", " ", $string);
	
	$string = trim(strip_tags($string));
	
	$string = str_replace(" ","-",$string);
	
  return $string;
  
}

function get_content($url){
	global $config;
	if($config['content_type']){
		require_once INCLUDE_DIR . '/class/_class_curl.php';
		$curl = new Curl;
		return $curl->get($url);
	}else{
		return file_get_contents($url);
	}
}

function clear_cache($cache_area = false) {
	
	$fdir = opendir( ROOT_DIR . '/cache' );
	
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.' and $file != '..' and $file != '.htaccess' and $file != 'admin' ) {
			
			if( $cache_area ) {
				
				if( strpos( $file, $cache_area ) !== false ) @unlink( ROOT_DIR . '/cache/' . $file );
			
			} else {
				
				@unlink( ROOT_DIR . '/cache/' . $file );
			
			}
		}
	}
}
function clean_url($url) {
	
	if( $url == '' ) return;
	
	$url = str_replace( "http://", "", strtolower( $url ) );
	if( substr( $url, 0, 4 ) == 'www.' ) $url = substr( $url, 4 );
	$url = explode( '/', $url );
	$url = reset( $url );
	$url = explode( ':', $url );
	$url = reset( $url );
	
	return $url;
}
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}else{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function navigation( $alternative_link, $total, $per_pages) {
	global $tpl, $config; 
	$alternative_link = $config['siteurl'] . $alternative_link;
	if( $total < $per_pages ) return;
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	if( $page > 1 ) {
		$prev = $page - 1;
		$url = str_replace ("{page}", $prev, $alternative_link );
	} else {
		$no_prev = TRUE;
	}
	if( $per_pages ) {
		$enpages_count = @ceil( $total / $per_pages );
		$pages = "";
		if( $enpages_count <= 10 ) {
			for($j = 1; $j <= $enpages_count; $j ++) {
				
				if( $j != $page ) {
						$url = str_replace ("{page}", $j, $alternative_link );
						$pages .= "<li><a href=\"" . $url . "\">$j</a></li> ";
				} else {
					$pages .= "<li class=\"active\"><a>$j</a></li> ";
				}
			}
		} else {
			$start = 1;
			$end = 10;
			if( $page > 0 ) {
				if( $page > 6 ) {
					$start = $page - 4;
					$end = $start + 8;
					if( $end >= $enpages_count ) {
						$start = $enpages_count - 9;
						$end = $enpages_count - 1;
						$nav_prefix = "";
					} else
						$nav_prefix = "";
				}
			}
			if( $start >= 2 ) {
					$url = str_replace ("{page}", "1", $alternative_link );
					$pages .= "<li><a href=\"" . $url . "\">1</a></li>";
			}
			
			for($j = $start; $j <= $end; $j ++) {
				if( $j != $page ) {
						$url = str_replace ("{page}", $j, $alternative_link );
						$pages .= "<li><a href=\"" . $url . "\">$j</a></li> ";
				} else {
					$pages .= "<li class=\"active\"><a>$j</a></li> ";
				}
			}
			if( $page != $enpages_count ) {
					$url = str_replace ("{page}", $enpages_count, $alternative_link );
					$pages .= $nav_prefix . "";
			} else
				$pages .= "";
		}
	}
	if( $page < $enpages_count ) {
		$next_page = $page + 1;
		$url = str_replace ("{page}", $next_page, $alternative_link );
	} else {
		$no_next = TRUE;
	}
	return $pages;
}
function clear_url_dir($var) {
	if ( is_array($var) ) return "";

	$var = str_replace( ".php", "", $var );
	$var = trim( strip_tags( $var ) );
	$var = str_replace( "\\", "/", $var );
	$var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", "", $var );
	return $var;

}

$domain_cookie = explode (".", clean_url( $_SERVER['HTTP_HOST'] ));
$domain_cookie_count = count($domain_cookie);
$domain_allow_count = -2;

if ( $domain_cookie_count > 2 ) {

	if ( in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') )) $domain_allow_count = -3;
	if ( $domain_cookie[$domain_cookie_count-1] == 'ua' ) $domain_allow_count = -3;
	$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
}

$domain_cookie = "." . implode (".", $domain_cookie);

define( 'DOMAIN', $domain_cookie );

function set_cookie($name, $value, $expires) {
	
	if( $expires ) {
		
		$expires = time() + ($expires * 86400);
	
	} else {
		
		$expires = FALSE;
	
	}
	
	if( PHP_VERSION < 5.2 ) {
		
		setcookie( $name, $value, $expires, "/", DOMAIN . "; HttpOnly" );
	
	} else {
		
		setcookie( $name, $value, $expires, "/", DOMAIN, NULL, TRUE );
	
	}
}

function removetype($var, $lower = true, $punkt = true) {
	
	if ( is_array($var) ) return "";
	
	$var = str_replace( ".php", "", $var );
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/ms", "-", $var );

	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	$var = preg_replace( '#[\-]+#i', '-', $var );

	if ( $lower ) $var = strtolower( $var );
	
	if( strlen( $var ) > 200 ) {
		
		$var = substr( $var, 0, 200 );
		
		if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );
	
	}
	
	return $var;
}
function msg_page($type, $message, $link) {
	echo <<<HTML
		<div class="span9">
			<div class="alert alert-{$type}">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{$message}
			</div>
		</div>
	</div>
</div>
<script>
setTimeout(function() {
      window.location = '{$PHP_SELF}?{$link}';
}, 1000);
</script>
HTML;
	die();
}
?>