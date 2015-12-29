<?php

if( !$user_group[$member_id['user_group']]['admin_banners'] ) {
	//msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] );
else $id = "";

echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
HTML;

if( $_POST['action'] == "doadd" ) {

	$banner_tag = removetype( strip_tags( trim( $_POST['banner_tag'] ) ) );
	$banner_descr = $db->safesql( strip_tags( trim( $_POST['banner_descr'] ) ) );
	$banner_code = $db->safesql( trim( $_POST['banner_code'] ) );
	$approve = intval( $_REQUEST['approve'] );
	
	print_r($_POST);
	
	if( $banner_tag == "" or $banner_descr == "" ) {
		echo <<<HTML
		<div class="span12">
			<div class="alert alert-error">
			<button data-dismiss="alert" class="close" type="button">×</button>
			<strong>Error!</strong> You need to enter banner tag and banner description!.
		</div>
	</div>
</div>
HTML;
	echo $footer;
	die();
	}else{
	
		$db->query( "INSERT INTO vass_banners (banner_tag, descr, code, approve) values ('$banner_tag', '$banner_descr', '$banner_code', '$approve')" );
		@unlink( ROOT_DIR . '/cache/admin/banners.php' );
		clear_cache();
		header( "Location: " . $PHP_SELF . "?do=banners" );
	}
}
if( $_POST['action'] == "doedit" ) {
	
	if (!$id) die( "ID not valid" );
	
	$banner_tag = removetype( strip_tags( trim( $_POST['banner_tag'] ) ) );
	$banner_descr = $db->safesql( strip_tags( trim( $_POST['banner_descr'] ) ) );
	$banner_code = $db->safesql( trim( $_POST['banner_code'] ) );
	$approve = intval( $_REQUEST['approve'] );
	
	if( $banner_tag == "" or $banner_descr == "" ) {
		echo <<<HTML
				<div class="span12">
					<div class="alert alert-error">
					<button data-dismiss="alert" class="close" type="button">×</button>
					<strong>Error!</strong> You need to enter banner tag and banner description!.
				</div>
			</div>
		</div>
HTML;
	echo $footer;
	die();
	
	}
	$db->query( "UPDATE vass_banners SET banner_tag='$banner_tag', descr='$banner_descr', code='$banner_code', approve='$approve' WHERE id='$id'" );
	@unlink( ROOT_DIR . '/cache/admin/banners.php' );
	clear_cache();
	header( "Location: " . $PHP_SELF . "?do=banners" );
}

if( $_GET['action'] == "off" ) {
	
	if (!$id) die( "ID not valid" );
	
	$db->query( "UPDATE vass_banners set approve='0' WHERE id='$id'" );
	@unlink( ROOT_DIR . '/cache/admin/banners.php' );
	clear_cache();
}
if( $_GET['action'] == "on" ) {
	
	if (!$id) die( "ID not valid" );
	
	$db->query( "UPDATE vass_banners set approve='1' WHERE id='$id'" );
	@unlink( ROOT_DIR . '/cache/admin/banners.php' );
	clear_cache();
}

if( $_GET['action'] == "delete" ) {
	
	if (!$id) die( "ID not valid" );
	
	$db->query( "DELETE FROM vass_banners WHERE id='$id'" );
	@unlink( ROOT_DIR . '/cache/admin/banners.php' );
	clear_cache();
}

if( $_REQUEST['action'] == "add" or $_REQUEST['action'] == "edit" ) {
	
	$start_date = "";
	$stop_date  = "";

	$js_array[] = "engine/skins/calendar.js";

	if( $_REQUEST['action'] == "add" ) {
		$checked = "checked";
		$doaction = "doadd";
		$all_cats = "selected";
		$check_all = "selected";
		$groups = get_groups();
	
	} else {
		
		$row = $db->super_query( "SELECT * FROM vass_banners WHERE id='$id' LIMIT 0,1" );
		$banner_tag = $row['banner_tag'];
		$banner_descr = htmlspecialchars( stripslashes( $row['descr'] ) );
		$banner_code = htmlspecialchars( stripslashes( $row['code'] ) );
		$short_place = $row['short_place'];
		$checked = ($row['approve']) ? "checked" : "";
		$doaction = "doedit";
	
	}
	
	echo <<<HTML
	<div class="span12">
		<h3>Add new banner</h3>
			<form method="post" action="" name="bannersform">
				<fieldset>
					<input type="hidden" name="mod" value="banners">
      				<input type="hidden" name="action" value="{$doaction}">
					<label>Tags</label>
					<input class="input-xxlarge" type="text" name="banner_tag" value="{$row['banner_tag']}" required/>
					<label>Description</label>
					<input class="input-xxlarge" type="text" name="banner_descr" value="{$row['banner_descr']}" required/>
					<label>HTML code</label>
					<textarea name="banner_code" style="width: 530px; height: 200px">{$banner_code}</textarea>
					<label class="checkbox">
					<input type="checkbox" name="approve" value="1" {$checked}> Active?
					</label>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
	</div>
</div>
HTML;
} else {
	
	$db->query( "SELECT * FROM vass_banners ORDER BY id DESC" );
	
	$entries = "";
	
	while ( $row = $db->get_row() ) {
		
		$row['descr'] = stripslashes( $row['descr'] );
		$row['banner_tag'] = "{banner_" . $row['banner_tag'] . "}";
		$row['code'] = stripslashes( $row['code'] );

		if ( $row['start'] ) $start_date = date( "d.m.Y H:i", $row['start'] ); else $start_date = "--";
		if ( $row['end'] ) $end_date = date( "d.m.Y H:i", $row['end'] ); else $end_date = "--";

		
		if( $row['approve'] ) {
			$row['approve'] = '<p class="text-success">Yes</p>';
			$led_action = "off";
			$led_text = "Disable";
			$led_class = "btn-warning";
		} else {
			$row['approve'] = '<p class="text-error">No</p>';
			$led_action = "on";
			$led_text = "Enable";
			$led_class = "btn-success";
		}
		
		$banner_list .= "
				<tr>
                <td>{$row['banner_tag']}</td>
                <td>{$row['approve']}</td>
                <td>{$row['code']}</td>
               <td><p><button onClick=\"document.location='?do=banners&action=$led_action&id={$row['id']}'; return(false)\" class=\"btn {$led_class}\" type=\"button\">{$led_text}</button>
					<button onClick=\"document.location='?do=banners&action=edit&id={$row['id']}'; return(false)\" class=\"btn btn-info\" type=\"button\">Edit</button>
					<button onClick=\"document.location='?do=banners&action=delete&id={$row['id']}'; return(false)\" class=\"btn btn-danger\" type=\"button\">Delete</button>
				</p></td>
              </tr>";
	}
	$db->free();
	
	echo <<<HTML
		
<div class="span12">
	<h3>Banner Manager</h3>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>Tag</th>
				<th>Active</th>
				<th>Preview</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$banner_list}
		</tbody>
	</table>
<p>
<button onclick="document.location='?do=banners&action=add'" class="btn btn-large btn-primary" type="button">Create new banner</button>
</p>
</div>
</div>
</div>
HTML;
	
}
?>