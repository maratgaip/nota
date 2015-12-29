<?php
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
                       'login'=>urlencode('sir.pht@gmail.com'),
                      'password'=>urlencode('anhyeuem'),
            			'submit'=>'submit',
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
?>