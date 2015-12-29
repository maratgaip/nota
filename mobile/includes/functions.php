<?php
function ae_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
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

function getRealIpAddress() {
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}		
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
}
function hyperlink($string){
	$string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
	$string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$string);
	$string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\">$1</a>",$string);
	return $string;
}

function lyrics_navigation( $template, $alternative_link, $link, $total, $per_pages, $compile, $attmp3ajax = FALSE, $ajaxlink, $ajaxquery, $ajaxtype) {
		global $tpl, $config; 
		if($attmp3ajax == TRUE)
		{
		if( $total < $per_pages ) return;

		if( isset( $_GET['page'] ) ) $page = intval( $_GET['page'] );
		if( !$page OR $page < 0 ) $page = 1;

		$tpl->load_template( $template );

		if( $page > 1 ) {
			$prev = $page - 1;
				$url = str_replace ("{page}", $prev, $alternative_link );
				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$prev. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\">\\1</a></li>" );
		
		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<li><a style=\"cursor:pointer\" class=\"gray12 active\">\\1</a></li>" );
			$no_prev = TRUE;
		}

		if( $per_pages ) {
			
			$enpages_count = @ceil( $total / $per_pages );
			$pages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $j != $page  ) {
						
							$url = str_replace ("{page}", $j, $alternative_link );
							$pages .= "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$j. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\">$j</a></li> ";
					
					} else {
						
						$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">$j</a></li> ";
					}
				
				}
			
			} else {
				
				$start = 1;
				$end = 10;
				//$nav_prefix = "<li><a style=\"cursor:pointer\" >...</a></li> ";
				
				if( $page  > 0 ) {
					
					if( $page  > 6 ) {
						
						$start = $page  - 4;
						$end = $start + 8;
						
						if( $end >= $enpages_count ) {
							$start = $enpages_count - 9;
							$end = $enpages_count - 1;
							$nav_prefix = "";
						} else $nav_prefix = "";
							//$nav_prefix = "<li><a style=\"cursor:pointer\" >...</a></li> ";
					
					}
				
				}
				
				if( $start >= 2 ) {
					
						$url = str_replace ("{page}", "1", $alternative_link );
						$pages .= "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$j. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\">1</a></li>";
				
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $j != $page ) {
						
							$url = str_replace ("{page}", $j, $alternative_link );
							$pages .= "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$j. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\">$j</a></li> ";
					
					} else {
						
						$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">$j</a></li> ";
					}
				
				}
				
				if( $page != $enpages_count ) {
					
						$url = str_replace ("{page}", $enpages_count, $alternative_link );
						$pages .= $nav_prefix . "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$enpages_count. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\">{$enpages_count}</a></li>";

				} else
					$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">{$enpages_count}</a></li> ";
			
			}
			
			$tpl->set( '{pages}', $pages );
		
		}
		if( $page < $enpages_count ) {


			$next_page = $page + 1;

				$url = str_replace ("{page}", $next_page, $alternative_link );
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<li><a onclick=\"".$ajaxquery."('" . $ajaxlink . "&amp;page=".$next_page. "','".$ajaxtype."'); return false;\" href=\"" . $url . "\" class=\"btn_next\">\\1</a></li>" );

		} else {
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<li><a style=\"cursor:pointer\" class=\"btn_next\">\\1</a></li>" );
			$no_next = TRUE;
		}
		
		if( ! $no_prev or ! $no_next ) {
			$tpl->compile( $compile );
		}
		
		$tpl->clear();
		}else {
		
				if( $total < $per_pages ) return;

		if( isset( $_GET['page'] ) ) $page = intval( $_GET['page'] );
		if( !$page OR $page < 0 ) $page = 1;

		$tpl->load_template( $template );

		if( $page > 1 ) {
			$prev = $page - 1;
				$url = str_replace ("{page}", $prev, $alternative_link );
				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<li><a  class=\"gray12 bgrep\" href=\"" . $url . "\">\\1</a></li>" );

		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<li><a style=\"cursor:pointer\" class=\"gray12 active\">\\1</a></li>" );
			$no_prev = TRUE;
		}

		if( $per_pages ) {
			
			$enpages_count = @ceil( $total / $per_pages );
			$pages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $j != $page  ) {
						
							$url = str_replace ("{page}", $j, $alternative_link );
							$pages .= "<li><a  class=\"gray12 bgrep\" href=\"" . $url . "\">$j</a></li> ";

				
					} else {
						
						$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">$j</a></li> ";
					}
				
				}
			
			} else {
				
				$start = 1;
				$end = 10;
				//$nav_prefix = "<li><a style=\"cursor:pointer\" >...</a></li> ";
				
				if( $page  > 0 ) {
					
					if( $page  > 6 ) {
						
						$start = $page  - 4;
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
						$pages .= "<li><a  class=\"gray12 bgrep\" href=\"" . $url . "\">1</a></li>";
		
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $j != $page ) {
						
							$url = str_replace ("{page}", $j, $alternative_link );
							$pages .= "<li><a  class=\"gray12 bgrep\" href=\"" . $url . "\">$j</a></li> ";
					
					} else {
						
						$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">$j</a></li> ";
					}
				
				}
				
				if( $page != $enpages_count ) {
					
						$url = str_replace ("{page}", $enpages_count, $alternative_link );
						$pages .= $nav_prefix . "";
				
				} else
					//$pages .= "<li><a style=\"cursor:pointer\" class=\"gray12 active\">{$enpages_count}</a></li> ";
					$pages .= "";
			
			}
			
			$tpl->set( '{pages}', $pages );
		
		}
		if( $page < $enpages_count ) {


			$next_page = $page + 1;

				$url = str_replace ("{page}", $next_page, $alternative_link );
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<li><a  class=\"gray12 bgrep\" href=\"" . $url . "\" class=\"btn_next\">\\1</a></li>" );
		
		} else {
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<li><a style=\"cursor:pointer\" class=\"btn_next\">\\1</a></li>" );
			$no_next = TRUE;
		}
		
		if( ! $no_prev or ! $no_next ) {
			$tpl->compile( $compile );
		}
		
		$tpl->clear();
		
		}
	}

function CutContent($content, $from, $end){
	if ((($content and $from) and $end)){
		$r = explode($from, $content);
		if (isset($r[1])){
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return;
	}
}

class Curl {
	var $callback = false;
	var $secure = false;
	var $conn = false;
	var $cookiefile =false;
	var $header = false;
	var $cookie = false;
	var $follow = true;
	

	function Curl($u = false) {
		$this->conn = curl_init();
		if (!$u) {
			$u = rand(0,100000);
		}

		$this->cookiefile= INCLUDE_DIR.'/cache/'.md5($u);
	}

	function setCallback($func_name) {
		$this->callback = $func_name;
	}

	function close() {
		curl_close($this->conn);
		if (is_file($this->cookiefile)) {
			unlink($this->cookiefile);
		}

	}

	function doRequest($method, $url, $vars) {

		$ch = $this->conn;

		$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

		curl_setopt($ch, CURLOPT_URL, $url);
		if ($this->header) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		} else {
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		}
		curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);



		if($this->secure) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		
		if ($this->cookie) 
        {
        	curl_setopt($ch, CURLOPT_COOKIE,$this->cookie);
        }

        if ($this->follow) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        }

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); // lighttpd fix
		}

		$data = curl_exec($ch);



		if ($data) {
			if ($this->callback)
			{
				$callback = $this->callback;
				$this->callback = false;
				return call_user_func($callback, $data);
			} else {
				return $data;
			}
		} else {
			return false;
		}
	}

	function get($url) {
		return $this->doRequest('GET', $url, 'NULL');
	}

	function getError()
	{
		return curl_error($ch);
	}

	function post($url, $params = false) {

		$post_data = array(
                        'login'=>urlencode('donghungx'),
                      'password'=>urlencode('anhyeuem'),
               );

		if (is_array($params)) {

			foreach($params as $var=>$val) {
				if(!empty($post_data))$post_data.='&';
				$post_data.= $var.'='.urlencode($val);
			}

		} else {
			$post_data = $params;
		}

		return $this->doRequest('POST', $url, $post_data);
	}
}

function getPage($url,$post = false,$cookie = false)
{
    $pURL = parse_url($url);    
       
    $curl = new Curl($pURL['host']);
                    
    if (strstr($url,'https://')) 
    {
        $curl->secure = true;	
    }
    
    if ($post) {
    	return $curl->post($url,$post);
    } else {
        return $curl->get($url);
    }
    
}


function hms2sec ($hms) {
     list($m, $s) = explode (":", $hms);
     $seconds = 0;
     $seconds += (intval($m) * 60);
     $seconds += (intval($s));
     return $seconds;
}

function enc($plainText) {
   
    $base64 = base64_encode($plainText);
    $base64url = strtr($base64, '+/=', '-_,');
    return $base64url;  
}
function dec($plainText) {
   
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;  
}

function stripUnicode($str){
        if(!$str) return false;
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni) $str = preg_replace("/($uni)/i",$nonUnicode,$str);
		return $str;
}
function makekeysearch($column, $data){	
	$split_stemmed = explode(" ",$data);
	while(list($key,$val)=each($split_stemmed)){
		if($val<>" " and strlen($val) > 0){
		$sql .= $column." LIKE '%".$val."%' AND ";
		}
	}	
		$sql=substr($sql,0,(strLen($sql)-4));//this will eat the last AND
		$sql .= "";
	return $sql;
}
function removetype($data) 
{	
	$data = html_entity_decode($data);
	$data=totranslit( $data , true, false );
	return $data;
}
function makehash($data) 
{	
	$data = html_entity_decode($data);
	$data=totranslit( $data , true, false );
	$data=str_replace('-',"",$data );
	return $data;
}
function artisthash($data){	
	$data = html_entity_decode($data);
	$data=totranslit( $data , true, false );
	$data=str_replace('-',"",$data );
	$data = md5(md5($data));
	$data = substr($data, -6);
	return $data;
}
function makekeyword($data) {
	$data=str_replace('$',"s",$data );
	$data= stripUnicode($data);
	$data=totranslit( $data , false, false );
	$data=ucwords($data);
	$data=str_replace('-',"+",$data );
	return $data;
}

function formatsize($file_size) {
	if( $file_size >= 1073741824 ) {
		$file_size = round( $file_size / 1073741824 * 100 ) / 100 . " Gb";
	} elseif( $file_size >= 1048576 ) {
		$file_size = round( $file_size / 1048576 * 100 ) / 100 . " Mb";
	} elseif( $file_size >= 1024 ) {
		$file_size = round( $file_size / 1024 * 100 ) / 100 . " Kb";
	} else {
		$file_size = $file_size . " b";
	}
	return $file_size;
}

function totranslit($var, $lower = true, $punkt = true) {
	$NpjLettersFrom = "àáâăäåçèêë́íîïđṇ̃óôöû³";
	$NpjLettersTo = "abvgdeziklmnoprstufcyi";
	$NpjBiLetters = array ("é" => "j", "¸" => "yo", "æ" => "zh", "ơ" => "x", "÷" => "ch", "ø" => "sh", "ù" => "shh", "ư" => "ye", "₫" => "yu", "ÿ" => "ya", "ú" => "", "ü" => "", "¿" => "yi", "º" => "ye" );
	
	$NpjCaps = "ÀÁÂĂÄÅ¨ÆÇÈÉÊË̀ÍÎÏĐÑ̉ÓÔƠÖ×ØÙÜÚÛỮß¯ª²";
	$NpjSmall = "àáâăäå¸æçèéêë́íîïđṇ̃óôơö÷øùüúûư₫ÿ¿º³";
	
	$var = str_replace( ".php", "", $var );
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/ms", "-", $var );
	$var = strtr( $var, $NpjCaps, $NpjSmall );
	$var = strtr( $var, $NpjLettersFrom, $NpjLettersTo );
	$var = strtr( $var, $NpjBiLetters );
	
	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	$var = preg_replace( '#[\-]+#i', '-', $var );

	if ( $lower ) $var = strtolower( $var );
	
	if( strlen( $var ) > 50 ) {
		
		$var = substr( $var, 0, 50 );
		
		if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );
	
	}
	
	return $var;
}

function msgbox($title, $text) {
	global $tpl,$config;
	
	$tpl_2 = new template( );
	$tpl_2->dir = TEMPLATE_DIR;
	
	$tpl_2->load_template( 'infomation.tpl' );
	
	$tpl_2->set( '{error}', $text );
	$tpl_2->set( '{title}', $title );
	$tpl_2->set( '{THEME}', $config['siteurl'].'templates/'.$config['template'] );
	$tpl_2->set( '{homepage}', $config['siteurl'] );

	$tpl_2->compile( 'info' );
	$tpl_2->clear();
	
	$tpl->result['info'] .= $tpl_2->result['info'];
}

function filesize_url($url) {
	return ($data = @file_get_contents( $url )) ? strlen( $data ) : false;
}

function create_keywords($story) {
	global $metatags;
	
	$keyword_count = 20;
	$newarr = array ();
	
	$quotes = array ("\x22", "\x60", "\t", "\n", "\r", ",", ".", "/", "¬", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
	$fastquotes = array ("\x22", "\x60", "\t", "\n", "\r", '"', "\\", '\r', '\n', "/", "{", "}", "[", "]" );
	
	$story = str_replace( $fastquotes, '', trim( strip_tags( str_replace( '<br />', ' ', stripslashes( $story ) ) ) ) );
	
	$metatags['description'] = substr( $story, 0, 190 );
	
	$story = str_replace( $quotes, '', $story );
	
	$arr = explode( " ", $story );
	
	foreach ( $arr as $word ) {
		if( strlen( $word ) > 4 ) $newarr[] = $word;
	}
	
	$arr = array_count_values( $newarr );
	arsort( $arr );
	
	$arr = array_keys( $arr );
	
	$total = count( $arr );
	
	$offset = 0;
	
	$arr = array_slice( $arr, $offset, $keyword_count );
	
	$metatags['keywords'] = implode( ", ", $arr );
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

function convert_unicode($t, $to = 'windows-1251') {
	$to = strtolower( $to );

	if( $to == 'utf-8' ) {
		
		return urldecode( $t );
	
	} else {
		
		if( function_exists( 'iconv' ) ) $t = iconv( "UTF-8", $to . "//IGNORE", $t );
		else $t = "The library iconv is not supported by your server";
	
	}

	return urldecode( $t );
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

function load_cache($prefix) {
	
	//if( $config['allow_cache'] != "yes" ) return false;
	
	$filename = ROOT_DIR . '/cache/' . $prefix . '.tmp';
	
	return @file_get_contents( $filename );
}

function cache($prefix, $cache_text) {
	
	//if( $config['allow_cache'] != "yes" ) return false;
	
	$filename = ROOT_DIR . '/cache/' . $prefix . '.tmp';
	
	$fp = fopen( $filename, 'wb+' );
	fwrite( $fp, $cache_text );
	fclose( $fp );
	
	@chmod( $filename, 0666 );

}

function clear_cache($cache_area = false) {
	
	$fdir = opendir( ROOT_DIR . '/cache' );
	
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system' ) {
			
			if( $cache_area ) {
				
				if( strpos( $file, $cache_area ) !== false ) @unlink( ROOT_DIR . '/cache/' . $file );
			
			} else {
				
				@unlink( ROOT_DIR . '/cache/' . $file );
			
			}
		}
	}
}
?>