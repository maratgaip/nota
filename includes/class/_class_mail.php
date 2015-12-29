<?php
class class_mail {
	
	var $site_name = "";
	var $from = "";
	var $to = "";
	var $subject = "";
	var $message = "";
	var $header = "";
	var $error = "";
	var $bcc = array ();
	var $mail_headers = "";
	var $html_mail = 0;
	var $charset = 'windows-1251';
	
	var $smtp_fp = FALSE;
	var $smtp_msg = "";
	var $smtp_port = "";
	var $smtp_host = "localhost";
	var $smtp_user = "";
	var $smtp_pass = "";
	var $smtp_code = "";
	var $send_error = FALSE;
	
	var $eol = "\n";
	
	var $mail_method = 'php';
	
	function class_mail($config, $is_html = false) {
		$this->mail_method = "php";
		
		$this->from = $config['email'];
		$this->charset = $config['charset'];
		$this->site_name = $config['sitetitle'];
		
		$this->smtp_host = "localhost";
		$this->smtp_port = intval( "25" );
		$this->smtp_user = "";
		$this->smtp_pass = "";
		
		$this->html_mail = $is_html;
	}
	
	function compile_headers() {
		
		$this->subject = "=?" . $this->charset . "?b?" . base64_encode( $this->subject ) . "?=";
		$from = "=?" . $this->charset . "?b?" . base64_encode( $this->site_name ) . "?=";
		
		if( $this->html_mail ) {
			$this->mail_headers .= "MIME-Version: 1.0" . $this->eol;
			$this->mail_headers .= "Content-type: text/html; charset=\"" . $this->charset . "\"" . $this->eol;
		} else {
			$this->mail_headers .= "MIME-Version: 1.0" . $this->eol;
			$this->mail_headers .= "Content-type: text/plain; charset=\"" . $this->charset . "\"" . $this->eol;
		}
		
		if( $this->mail_method != 'smtp' ) {
			
			if( count( $this->bcc ) ) {
				$this->mail_headers .= "Bcc: " . implode( ",", $this->bcc ) . $this->eol;
			}
		
		} else {
			
			$this->mail_headers .= "Subject: " . $this->subject . $this->eol;
			
			if( $this->to ) {
				
				$this->mail_headers .= "To: " . $this->to . $this->eol;
			}
		
		}
		
		$this->mail_headers .= "From: \"" . $from . "\" <" . $this->from . ">" . $this->eol;
		
		$this->mail_headers .= "Return-Path: <" . $this->from . ">" . $this->eol;
		$this->mail_headers .= "X-Priority: 3" . $this->eol;
		$this->mail_headers .= "X-Mailer: DLE PHP" . $this->eol;
	
	}
	
	function send($to, $subject, $message) {
		$this->to = preg_replace( "/[ \t]+/", "", $to );
		$this->from = preg_replace( "/[ \t]+/", "", $this->from );
		
		$this->to = preg_replace( "/,,/", ",", $this->to );
		$this->from = preg_replace( "/,,/", ",", $this->from );
		
		if( $this->mail_method != 'smtp' )
			$this->to = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#", "", $this->to );
		else
			$this->to = '<' . preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#", "", $this->to ) . '>';
		$this->from = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#", "", $this->from );
		
		$this->subject = $subject;
		$this->message = $message;
		
		$this->message = str_replace( "\r", "", $this->message );
		
		$this->compile_headers();
		
		if( ($this->to) and ($this->from) and ($this->subject) ) {
			if( $this->mail_method != 'smtp' ) {
				if( ! @mail( $this->to, $this->subject, $this->message, $this->mail_headers ) ) {
					$this->smtp_msg = "PHP Mail Error.";
					$this->send_error = true;
				}
			
			} else {
				$this->smtp_send();
			}
		
		}
		
		$this->mail_headers = "";
	
	}
	
	function smtp_get_line() {
		$this->smtp_msg = "";
		
		while ( $line = fgets( $this->smtp_fp, 515 ) ) {
			$this->smtp_msg .= $line;
			
			if( substr( $line, 3, 1 ) == " " ) {
				break;
			}
		}
	}
	
	function smtp_send() {
		$this->smtp_fp = @fsockopen( $this->smtp_host, intval( $this->smtp_port ), $errno, $errstr, 30 );
		
		if( ! $this->smtp_fp ) {
			$this->smtp_error( "Could not open a socket to the SMTP server" );
			return;
		}
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		if( $this->smtp_code == 220 ) {
			$data = $this->smtp_crlf_encode( $this->mail_headers . "\n" . $this->message );
			
			$this->smtp_send_cmd( "HELO " . $this->smtp_host );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error( "HELO" );
				return;
			}
			
			if( $this->smtp_user and $this->smtp_pass ) {
				$this->smtp_send_cmd( "AUTH LOGIN" );
				
				if( $this->smtp_code == 334 ) {
					$this->smtp_send_cmd( base64_encode( $this->smtp_user ) );
					
					if( $this->smtp_code != 334 ) {
						$this->smtp_error( "Username not accepted from the server" );
						return;
					}
					
					$this->smtp_send_cmd( base64_encode( $this->smtp_pass ) );
					
					if( $this->smtp_code != 235 ) {
						$this->smtp_error( "Password not accepted from the SMTP server" );
						return;
					}
				} else {
					$this->smtp_error( "This SMTP server does not support authorisation" );
					return;
				}
			}
			
			$this->smtp_send_cmd( "MAIL FROM:" . $this->from );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error( "Incorrect FROM address: $this->from" );
				return;
			}
			
			$to_arry = array ($this->to );
			
			if( count( $this->bcc ) ) {
				foreach ( $this->bcc as $bcc ) {
					if( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", str_replace( " ", "", $bcc ) ) ) {
						$to_arry[] = $bcc;
					}
				}
			}
			
			foreach ( $to_arry as $to_email ) {
				$this->smtp_send_cmd( "RCPT TO:" . $to_email );
				
				if( $this->smtp_code != 250 ) {
					$this->smtp_error( "Incorrect email address: $to_email" );
					return;
					break;
				}
			}
			
			$this->smtp_send_cmd( "DATA" );
			
			if( $this->smtp_code == 354 ) {
				fputs( $this->smtp_fp, $data . "\r\n" );
			} else {
				$this->smtp_error( "Error on write to SMTP server" );
				return;
			}
			
			$this->smtp_send_cmd( "." );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error();
				return;
			}
			
			$this->smtp_send_cmd( "quit" );
			
			if( $this->smtp_code != 221 ) {
				$this->smtp_error();
				return;
			}
			
			@fclose( $this->smtp_fp );
		} else {
			$this->smtp_error( "SMTP service unaviable" );
			return;
		}
	}
	
	function smtp_send_cmd($cmd) {
		$this->smtp_msg = "";
		$this->smtp_code = "";
		
		fputs( $this->smtp_fp, $cmd . "\r\n" );
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		return $this->smtp_code == "" ? FALSE : TRUE;
	}
	
	function smtp_error($err = "") {
		$this->smtp_msg = $err;
		$this->send_error = true;
		return;
	}
	
	function smtp_crlf_encode($data) {
		$data .= "\n";
		$data = str_replace( "\n", "\r\n", str_replace( "\r", "", $data ) );
		$data = str_replace( "\n.\r\n", "\n. \r\n", $data );
		
		return $data;
	}
}
?>