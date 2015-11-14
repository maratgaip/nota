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

if(isset($_GET['action'])) $action = $_GET['action'];

if( $action == "del" ) {
	
	$id = intval( $_REQUEST['id'] );
	
	$grouplevel = intval( $_REQUEST['grouplevel'] );
	
	if($id == 1) msg_page("error", "<strong>Error!</strong> You can't delete Administrator!", "do=usergroup");
	
	echo <<<HTML
<div class="span9">
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<strong>Well done!</strong> Deleted user group</strong>!.
	</div>
</div></div></div>
HTML;
	
	//$row = $db->super_query( "SELECT count(*) as count FROM vass_users WHERE user_group='$id'" );
	
	if( ! $row['count'] ) {
		//$db->query( "DELETE FROM vass_usergroups WHERE id = '$id'" );
		//@unlink( ENGINE_DIR . '/cache/system/usergroup.php' );
		//clear_cache();
		//msg( "info", $lang['all_info'], $lang['group_del'], "$PHP_SELF?mod=usergroup" );
	} else {
		if( $grouplevel and $grouplevel != $id ) {
			//$db->query( "UPDATE vass_users set user_group='$grouplevel' WHERE user_group='$id'" );
			//$db->query( "DELETE FROM vass_usergroups WHERE id = '$id'" );
			//@unlink( ENGINE_DIR . '/cache/system/usergroup.php' );
			//clear_cache();
			//msg( "info", $lang['all_info'], $lang['group_del'], "$PHP_SELF?mod=usergroup" );
		} else{
					//msg( "info", $lang['all_info'], "<form action=\"\" method=\"post\">{$lang['group_move']} <select name=\"grouplevel\">" . get_groups( 4 ) . "</select> <input class=\"edit\" type=\"submit\" value=\"{$lang['b_start']}\"></form>", "$PHP_SELF?mod=usergroup" );

		}
	}

} elseif( $action == "doadd" or $action == "doedit" ) {
	
	$group_name = $db->safesql( strip_tags( $_REQUEST['group_name'] ) );
	
	$allow_admin = intval( $_REQUEST['allow_admin'] );
	$allow_m_artists = intval( $_REQUEST['allow_m_artists'] );
	$allow_m_albums = intval( $_REQUEST['allow_m_albums'] );
	$allow_m_songs = intval( $_REQUEST['allow_m_songs'] );
	$allow_m_users = intval( $_REQUEST['allow_m_users'] );
	$allow_full_artists = intval( $_REQUEST['allow_full_artists'] );
	$allow_full_albums = intval( $_REQUEST['allow_full_albums'] );
	$allow_full_songs = intval( $_REQUEST['allow_full_songs'] );
	
	if( $group_name == "" ) {
		
		msg_page("error", "<strong>Error!</strong> Please fill groupname!", "do=usergroup");
		
	}else{
		
		@unlink( ROOT_DIR . '/cache/admin/usergroup.php' );
		
		$id = intval( $_REQUEST['id'] );
		$db->query( "UPDATE vass_usergroups set group_name='$group_name', allow_admin='$allow_admin', allow_m_artists='$allow_m_artists', 
		allow_m_albums='$allow_m_albums', allow_m_songs='$allow_m_songs', allow_m_users='$allow_m_users', 
		allow_full_artists='$allow_full_artists', allow_full_albums='$allow_full_albums', allow_full_songs='$allow_full_songs' WHERE id='{$id}'" );
		msg_page("success", "<strong>Success!</strong> Added/edited usergroup!", "do=usergroup");
		
		clear_cache();
}
} elseif( $action == "edit" ) {
		
	$id = intval( $_REQUEST['id'] );
	$group_name_value = htmlspecialchars( stripslashes( $user_group[$id]['group_name'] ) );
	
	if( $user_group[$id]['allow_admin'] ) $allow_admin = "checked";
	if( $user_group[$id]['allow_m_artists'] ) $allow_m_artists = "checked";
	if( $user_group[$id]['allow_m_albums'] ) $allow_m_albums = "checked";
	if( $user_group[$id]['allow_m_songs'] ) $allow_m_songs = "checked";
	if( $user_group[$id]['allow_m_users'] ) $allow_m_users = "checked";
	if( $user_group[$id]['allow_full_artists'] ) $allow_full_artists = "checked";
	if( $user_group[$id]['allow_full_albums'] ) $allow_full_albums = "checked";
	if( $user_group[$id]['allow_full_songs'] ) $allow_full_songs = "checked";
	
	if( $id == 1 ) $admingroup = "disabled";
	
	$group_list = get_groups( $user_group[$id]['rid'] );
	
	$form_action = "$PHP_SELF?do=usergroup&amp;action=doedit&amp;id=" . $id;
	
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
	echo <<<HTML
		<h3>Edit usergroup: {$group_name_value}</h3>
			<form method="post" action="{$form_action}">
				<fieldset>
					<label>Group name</label>
					<input class="input-xxlarge" type="text" name="group_name" value="{$group_name_value}" {$admingroup}/>
					<label class="checkbox">
					<input type="checkbox" name="allow_admin" {$allow_admin} value="1" {$admingroup}> Can access admin?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_m_artists" {$allow_m_artists} value="1" {$admingroup}> Can manager artists?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_m_albums" {$allow_m_albums} value="1" {$admingroup}> Can manager albums?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_m_songs" {$allow_m_songs} value="1" {$admingroup}> Can manager songs?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_m_users" {$allow_m_users} value="1" {$admingroup}> Can manager users?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_full_artists" {$allow_full_artists} value="1" {$admingroup}> Add artist do not need approval?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_full_albums" {$allow_full_albums} value="1" {$admingroup}> Add album do not need approval?
					</label>
					<label class="checkbox">
					<input type="checkbox" name="allow_full_songs" {$allow_full_songs} value="1" {$admingroup}> Add song do not need approval?
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
		
	$db->query( "SELECT user_group, count(*) as count FROM vass_users GROUP BY user_group" );
	while ( $row = $db->get_row() )
		$count_list[$row['user_group']] = $row['count'];
	$db->free();
	foreach ( $user_group as $group ) {
		$count = intval( $count_list[$group['id']] );
		$entries .= "
              <tr>
                <td>
    				{$group['group_name']}
                </td>
                <td>{$count}</td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=usergroup&action=edit&id={$group['id']}\">Edit</a></li>
                  <!--<li><a href=\"{$PHP_SELF}?do=usergroup&action=del&id={$group['id']}\">Delete</a></li>-->
                </ul>
              </div>
                	
                	
				</td>
              </tr>";
	}
	
	echo <<<HTML
<div class="span9">
	<h3>Users Manager: Total {$total['count']} artists</h3>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>Group</th>
				<th>Member</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$entries}
		</tbody>
	</table>
</div>
</div>
</div>
HTML;
	
}
?>