<?php

if( $member_id['user_group'] != 1 ) {
	msg_page("error", "<strong>Error!</strong> You don't have permission in this place!", "");
}

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
HTML;

if( isset( $_REQUEST['restore'] ) ) $restore = $_REQUEST['restore']; else $restore = "";

if( $action == "dboption" and count( $_REQUEST['ta'] ) ) {
	$arr = $_REQUEST['ta'];
	reset( $arr );
	
	$tables = "";
	
	while ( list ( $key, $val ) = each( $arr ) ) {
		$tables .= ", `" . $db->safesql( $val ) . "`";
	}
	
	$tables = substr( $tables, 1 );
	if( $_REQUEST['whattodo'] == "optimize" ) {
		$query = "OPTIMIZE TABLE  ";
	} else {
		$query = "REPAIR TABLE ";
	}
	$query .= $tables;
	
	if( $db->query( $query ) ) {
		msg( "info", $lang['db_ok'], $lang['db_ok_1'] . "<br /><br /><a href=$PHP_SELF?do=dboption>" . $lang['db_prev'] . "</a>" );
	} else {
		msg( "error", $lang['db_err'], $lang['db_err_1'] . "<br /><br /><a href=$PHP_SELF?do=dboption>" . $lang['db_prev'] . "</a>" );
	}

}

echo <<<HTML
	<div class="span9">
HTML;
	if( defined( 'SUBMIT' ) ) {
	echo <<<HTML
<div class="alert alert-success">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<strong>Well done!</strong> Saved the <strong>artist information</strong> successfully!.
</div>
HTML;
}

$tabellen = "";

$db->query( "SHOW TABLES" );
while ( $row = $db->get_array() ) {
	$titel = $row[0];
	if( substr( $titel, 0, strlen( vass_ ) ) == vass_ ) {
		$tabellen .= "<option value=\"$titel\" selected>$titel</option>\n";
	}
}
$db->free();

if( function_exists( "bzopen" ) ) {
	$comp_methods[2] = 'BZip2';
}
if( function_exists( "gzopen" ) ) {
	$comp_methods[1] = 'GZip';
}
$comp_methods[0] = "No compress";

function fn_select($items, $selected) {
	$select = '';
	foreach ( $items as $key => $value ) {
		$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
	}
	return $select;
}
$comp_methods = fn_select( $comp_methods, '' );

echo <<<HTML
    <SCRIPT LANGUAGE="JavaScript">
    function save(){
        dd=window.open('$PHP_SELF?do=dumper&action=backup','bcp','height=470,width=730,resizable=1,scrollbars=1')
        document.backup.target='bcp';
        document.backup.submit();
        dd.focus();
    }
    </SCRIPT>
		<div class="alert alert-block">
			<h4>Warning!</h4>
			In this section you can config your site, please make sure all content is correct!
		</div>
		<ul class="nav nav-tabs">
			<li><a href="#config" data-toggle="pill">Database Option</a></li>
			<li class="active"><a href="#social" data-toggle="pill">Backup</a></li>
			<li><a href="#music" data-toggle="pill">Restore</a></li>
		</ul>
		<form method="post" action="">
			<div class="tab-content" id="myTabContent">
				<div id="config" class="tab-pane fade">
					<form action="" method="post" >
						<fieldset>
							<label>Database</label>
							<select name="ta[]" multiple="multiple">{$tabellen}</select>
							<label class="checkbox">
							<input type="checkbox" name="allow_admin" {$allow_admin} value="1" {$admingroup}> OPTIMIZE Database?
							</label>
							<label class="checkbox">
							<input type="checkbox" name="allow_m_artists" {$allow_m_artists} value="1" {$admingroup}> REPAIR Database?
							</label>
							<label></label>
							<button type="button" class="btn" onclick="save();return false;">Apply</button>
						</fieldset>
					</form>
				</div>
				<div id="social" class="tab-pane fade  active in">
					<form action="$PHP_SELF?do=dumper&action=backup" name="backup" id="backup" method="post">
						<fieldset>
							<label>Choose backup format</label>
							<SELECT NAME=comp_method>{$comp_methods}</SELECT>
							<label></label>
							<button type="button" class="btn" onclick="save();return false;">Backup</button>
						</fieldset>
					</form>
				</div>
HTML;

define( 'PATH', ROOT_DIR . '/backup/' );

function file_select() {
	$files = array ('' );
	if( is_dir( PATH ) && $handle = opendir( PATH ) ) {
		while ( false !== ($file = readdir( $handle )) ) {
			if( preg_match( "/^.+?\.sql(\.(gz|bz2))?$/", $file ) ) {
				$files[$file] = $file;
			}
		}
		closedir( $handle );
	}
	return $files;
}

$files = fn_select( file_select(), '' );

echo <<<HTML
    <SCRIPT LANGUAGE="JavaScript">
    function dbload(){
        dd=window.open('$PHP_SELF?do=dumper&action=restore','bcp','height=370,width=530,resizable=1,scrollbars=1')
        document.restore.target='bcp';
        document.restore.submit();dd.focus();
    }
    </SCRIPT>
				<div id="music" class="tab-pane fade">
					<form action="$PHP_SELF?do=dumper&action=restore" name="restore" id="restore" method="post" >
						<fieldset>
							<label>Restore database from a backup</label>
							<SELECT NAME=file>{$files}</SELECT>
							<label></label>
							<button type="button" class="btn" onclick="dbload();return false;">Restore</button>
						</fieldset>
					</form>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
HTML;

?>