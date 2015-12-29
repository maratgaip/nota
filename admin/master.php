<?php

@session_start ();
@ob_start ();
@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'ROOT_DIR', ".." );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );
@include (INCLUDE_DIR . '/config.inc.php');
require_once INCLUDE_DIR . '/class/_class_mysql.php';
require_once INCLUDE_DIR . '/db.php';

include_once(ROOT_DIR . "/admin/functions.php");


 define ( 'GLOBUS', true );

$_TIME = date ( "Y-m-d H:i:s", time () );

// Check Admin session // Login is required if no cookies found.
if(isset($_POST['login'])) {
	
	include_once(ROOT_DIR . "/includes/member.php");
	
	$user_group = get_vars( "usergroup");
	if( !$user_group ) {
		history.back();
		$user_group = array ();
		$db->query( "SELECT * FROM vass_usergroups ORDER BY id ASC");
		while ( $row = $db->get_row() ) {
		$user_group[$row['id']] = array ();
		foreach ( $row as $key =>$value ) {
		$user_group[$row['id']][$key] = stripslashes($value);
		}
		}
		set_vars( "usergroup",$user_group );
		$db->free();
	}
	
	if(!$logged || !$user_group[$member_id['user_group']]['allow_admin']){
			
		$member_id = array ();
		set_cookie( "user_id", "", 0 );
		set_cookie( "login_pass", "", 0 );
		$_SESSION['user_id'] = 0;
		$_SESSION['login_pass'] = "";
		@session_destroy();
		@session_unset();
		
		$logged = FALSE;
		
		$alert = '<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Warning!</strong> Access Denied !!.
		</div>';
		
	}else{
		
		$db->query("UPDATE vass_users SET  last_date = '$_TIME' WHERE user_id = '" . $member_id['user_id'] . "'");
		
		Header("Location: ?");
		
	}

}



include_once(ROOT_DIR . "/includes/member.php");

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
	
	$member_id = array ();
	
	set_cookie( "user_id", "", 0 );
	
	set_cookie( "login_pass", "", 0 );
	
	$_SESSION['user_id'] = 0;
	
	$_SESSION['login_pass'] = "";
	
	@session_destroy();
	
	@session_unset();
	
	Header("Location: ?");
	
	exit;
}

if($logged) {

	$user_group = get_vars( "usergroup");
	if( !$user_group ) {
		$user_group = array ();
		$db->query( "SELECT * FROM vass_usergroups ORDER BY id ASC");
		while ( $row = $db->get_row() ) {
		$user_group[$row['id']] = array ();
		foreach ( $row as $key =>$value ) {
		$user_group[$row['id']][$key] = stripslashes($value);
		}
		}
		set_vars( "usergroup",$user_group );
		$db->free();
	}

	$header =  <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Nota - Master Control Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="css/bootstrap.css" rel="stylesheet">
<style type="text/css">
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.sidebar-nav {
	padding: 9px 0;
}
</style>
<link href="css/bootstrap-responsive.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/prettify.css"></link>
<link rel="stylesheet" type="text/css" href="css/bootstrap-wysihtml5.css"></link>
<link rel="stylesheet" type="text/css" href="css/bootstrap-tagmanager.css"></link>
<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
<noscript><link rel="stylesheet" href="css/jquery.fileupload-ui-noscript.css"></noscript>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="js/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/bootstrap-transition.js"></script>
<script type="text/javascript" src="js/bootstrap-alert.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/bootstrap-dropdown.js"></script>
<script type="text/javascript" src="js/bootstrap-scrollspy.js"></script>
<script type="text/javascript" src="js/bootstrap-tab.js"></script>
<script type="text/javascript" src="js/bootstrap-tooltip.js"></script>
<script type="text/javascript" src="js/bootstrap-popover.js"></script>
<script type="text/javascript" src="js/bootstrap-button.js"></script>
<script type="text/javascript" src="js/bootstrap-collapse.js"></script>
<script type="text/javascript" src="js/bootstrap-carousel.js"></script>
<script type="text/javascript" src="js/bootstrap-tagmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5.js"></script>
<script type="text/javascript" src="js/jqueryForm.js"></script>
<script type="text/javascript" src="js/filetree.js"></script>
<script type="text/javascript" src="js/codemirror/js/codemirror.js"></script>
<script type="text/javascript" src="js/prettify.js"></script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container" style="width: auto;"> <a class="brand" href="{$PHP_SELF}?">Master Control Panel</a>
			<ul class="nav">
              <li><a href="{$PHP_SELF}?do=config"><i class="icon-check icon-white"></i> Site Management</a></li>
              <li><a href="{$PHP_SELF}?do=templates"><i class="icon-eye-open icon-white"></i> Site Editing</a></li>
            </ul>
			<ul class="nav pull-right">
				<li id="fat-menu" class="dropdown"> <a href="#" id="user-menu" role="button" class="dropdown-toggle" data-toggle="dropdown">{$member_id['username']} <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="user-menu">
						<li><a tabindex="-1" href="{$PHP_SELF}?do=users">Members management</a></li>
						<li><a tabindex="-1" href="{$PHP_SELF}?do=config">Site management</a></li>
						<li class="divider"></li>
						<li><a tabindex="-1" href="{$PHP_SELF}?action=logout">Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
HTML;
	
	
	$footer =  <<<HTML
      <hr>
    <div id="footer" style="text-align:center">
      <div class="container">
        <p class="muted credit" style="text-align:center;">Copyright &copy; 2015 Nota</a></p>
      </div>
    </div>
    </div>
	
    <script src="js/common.js"></script>
	<script>
		$('.textarea').wysihtml5();
	</script>
	
	<script type="text/javascript" charset="utf-8">
		$(prettyPrint);
		$(document).ready(function() {
			$('a[data-confirm]').click(function(ev) {
				var href = $(this).attr('href');
				if (!$('#dataConfirmModal').length) {
					$('body').append('<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="dataConfirmLabel">Please Confirm</h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button><a class="btn btn-primary" id="dataConfirmOK">OK</a></div></div>');
				} 
				$('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
				$('#dataConfirmOK').attr('href', href);
				$('#dataConfirmModal').modal({show:true});
				return false;
			});
		});
	</script>
  </body>
</html>
HTML;

$menu_bar = array(
	0 => array(
				"icon" => "icon-home",
				"text" => "Dashboard",
				"link" => ""
				)
);

if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
				"icon" => "icon-check",
				"text" => "Site Management",
				"link" => "config"
				);
}

if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
				"icon" => "icon-flag",
				"text" => "Manage genres",
				"link" => "genres"
				);
}

if( $user_group[$member_id['user_group']]['allow_m_artists'] ){
	$menu_bar[] = array(
				"icon" => "icon-star-empty",
				"text" => "Manage artists",
				"link" => "artists"
				);
} 
if( $user_group[$member_id['user_group']]['allow_m_albums'] ) {
	$menu_bar[] = array(
				"icon" => "icon-book",
				"text" => "Manage albums",
				"link" => "albums"
				);
} 
if( $user_group[$member_id['user_group']]['allow_m_songs'] ) {
	$menu_bar[] = array(
				"icon" => "icon-list-alt",
				"text" => "Manage songs",
				"link" => "songs"
				);
}
if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
				"icon" => "icon-upload",
				"text" => "Upload multiple songs",
				"link" => "upload"
				);
}
if( $user_group[$member_id['user_group']]['allow_m_users'] ) {
	$menu_bar[] =  array(
				"icon" => "icon-user",
				"text" => "Manage users",
				"link" => "users"
				);
} 
if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
					"icon" => "icon-fire",
					"text" => "Usergroup",
					"link" => "usergroup"
					);
}

if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
				"icon" => "icon-eye-open",
				"text" => "Site editing",
				"link" => "templates"
				);
}
if( $member_id['user_group'] == 1 ) {
	$menu_bar[] = array(
				"icon" => "icon-briefcase",
				"text" => "Backup & Restore Site",
				"link" => "database"
				);
}
if(!isset($do) && isset($_REQUEST['do'])) $do = $_REQUEST['do'];

for($i = 0; $i< count($menu_bar); $i++){
	
	if($menu_bar[$i]['link'] == $do) {
		$acitve_menu = "active";
		$icon_style = "icon-white";
	}else{
		$acitve_menu = "";
		$icon_style = "icon-black";
	}
	
	$menu_li .= "<li class=\"$acitve_menu\"><a href=\"{$PHP_SELF}?do={$menu_bar[$i]['link']}\"><i class=\"{$menu_bar[$i]['icon']} $icon_style\"></i> {$menu_bar[$i]['text']}</a></li>";
	
}

echo $header;

	if ( ! $do ) {
	
		include ( 'modules/main.php');

	} elseif ( @file_exists( 'modules/' . $do . '.php' ) ) {
		
		include ( 'modules/' . $do . '.php');

	} else {
		
		$db->close ();
		die("No permission!");
	}
	
	echo $footer;
	
}else {
	if(!isset($alert)) $alert = '';

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sign in &middot; Nota Master Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }
      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <form class="form-signin" ACTION="{$PHP_SELF}" METHOD="POST">
		{$alert}
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" class="input-block-level" placeholder="Username" name="username" required>
        <input type="password" class="input-block-level" placeholder="Password" name="password" required>
        <input type="hidden" name="login" value="login">
        <button class="btn btn-large btn-primary" type="submit">Sign in</button>
      </form>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/jqueryForm.js"></script>
  </body>
</html>
HTML;
}

$db->close();
?>