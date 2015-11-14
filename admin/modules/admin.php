<?php

if( ! defined( 'TANCODE' ) ) {

	die( "Hacking attempt!" );

}

if($_POST['send'] == "changeadmin") {
	
	$username = $_POST["username"];
	$password = $_POST["password"];
	if(!$password) die("NO PASSWORD");
	$password = md5($_POST["password"]);
	
	$handler = fopen( ROOT_DIR . "/includes/admin.config.php", "w" );
	
	fwrite( $handler, "<?PHP \n\n//Admin Infomation\n\n" );
	fwrite( $handler, '$admininfo["name"]			= "'.$username.'";' );
	fwrite( $handler, '$admininfo["pass"]			= "'.$password.'";' );
	fwrite( $handler,"\n\n?>" );
	fclose( $handler );
	
	define ( 'SUBMIT', true );
	
	//exit;
}

if($_POST["username"]) $admininfo["name"] = $_POST["username"];

echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
			<div class="sidebar-nav">
				<ul class="nav nav-list bs-docs-sidenav affix-top">
					{$menu_li}
				</ul>
			</div>
		</div>
		<div class="span9">
HTML;
if( defined( 'SUBMIT' ) ) {
echo <<<HTML
<div class="alert alert-success">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<strong>Well done!</strong> Saved the <strong>admin login info</strong> successfully!.
</div>
HTML;
}

echo <<<HTML
		<h3>Change password</h3>
			<form method="post" action="">
				<fieldset>
					<label>Admin username</label>
					<input class="input-xxlarge" type="text" name="username" value="{$admininfo["name"]}" required/>
					<label>Password</label>
					<input class="input-xxlarge" type="password" name="password" required/>
					<input name="send" type="hidden" value="changeadmin" placeholder="Password">
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
	</div>
</div>
HTML;
?>
