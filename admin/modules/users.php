<?php

if( ! defined( 'GLOBUS' )  || ! $user_group[$member_id['user_group']]['allow_m_users']) {

	msg_page("error", "<strong>Error!</strong> You don't have permission in this place!", "");

}

if(isset($_GET['action'])) $action = $_GET['action'];

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

if( $action == "edit" ){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	if($_POST['m_username']){
		
		$username = $db->safesql($_POST['m_username']);
		$email = $db->safesql($_POST['email']);
		$user_group = $db->safesql($_POST['usergroup']);
		$name = $db->safesql($_POST['name']);
		$bio = $db->safesql($_POST['bio']);
		if($_POST['m_password']) $password = md5($_POST['m_password']);
		
		if($username && $email){
			
			if($_POST['m_password']) $db->query("UPDATE vass_users SET username='$username', password='$password', email='$email', user_group='$user_group', name='$name', bio='$bio' WHERE user_id='$id'");
			else $db->query("UPDATE vass_users SET username='$username', email='$email', user_group='$user_group', name='$name', bio='$bio' WHERE user_id='$id'");
			
			msg_page("success", "<strong>Success!</strong> Saved member information!", "do=users&action=edit&id=$id");
			
		}else{
			
			msg_page("error", "<strong>Error!</strong> Username, email must be filled!", "do=users&action=edit&id=$id");
			
		}
	}
	
	$row = $db->super_query("SELECT * FROM vass_users WHERE user_id = '$id'");
	
	if(!$row['user_id']) die("User not exits");
	
	$group_control = get_groups($row['user_group']);
	
	echo <<<HTML
	<div class="span9">
		<h3>Edit user: {$row['username']}</h3>
			<form method="post" action="">
				<fieldset>
					<label>Username</label>
					<input class="input-xxlarge" type="text" name="m_username" value="{$row['username']}" required/>
					<label>New password</label>
					<input class="input-xxlarge" type="password" name="m_password"/>
					<label>Email</label>
					<input class="input-xxlarge" type="text" name="email" value="{$row['email']}" required/>
					<label>Usergroup</label>
					<select name="usergroup">$group_control</select>
					<label>Name</label>
					<input class="input-xxlarge" type="text" name="name" value="{$row['name']}"/>
					<!--<label>Avatar</label>
					<input type="file" id="fileInput" class="input-file">-->
					<label>Bio</label>
					<textarea class="textarea" name="bio" style="width: 530px; height: 200px">{$row['bio']}</textarea>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
	</div>
</div>
HTML;
}elseif($action == "delete"){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_users WHERE user_id = '$id'");
	
	msg_page("success", "<strong>Success!</strong> Deleted member information!", "do=users");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	if($q){
		$db->query("SELECT * FROM vass_users WHERE username LIKE '%$q%' LIMIT $start,20");
	}else{
		$db->query("SELECT * FROM vass_users ORDER BY user_group ASC LIMIT $start,20");
	}
	
	$i = 1;
	while($row = $db->get_row()){
	$bio = shorter($row['bio'],150);
	$user_list .= <<<HTML
              <tr>
        		<td>
    			{$i}
                </td>
                <td>{$row['username']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
               <td>{$row['reg_date']}</td>
               	   <td>{$row['user_group']}</td>
 				<td><div class="btn-group">
                <button class="btn dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="{$PHP_SELF}?do=users&action=edit&id={$row['user_id']}">Edit</a></li>
                  <li><a onclick="var r=confirm('Are you sure by deleting this member?');if (r==true){window.location='{$PHP_SELF}?do=users&action=delete&id={$row['user_id']}'}; return false;" href="{$PHP_SELF}?do=users&action=delete&id={$row['user_id']}">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>
HTML;
				$i++;
}
	$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_users");
	$pages = navigation("admin/index.php?do=users&p={page}", $total['count'], 20);
	
	echo <<<HTML
<div class="span9">
	<h3>Users Manager: Total {$total['count']} users</h3>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="users">
    	<input name="q" type="text" class="input-medium search-query" value="$q">
    	<button type="submit" class="btn">Search</button>
    </form>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>No.</th>
				<th>Username</th>
				<th>Name</th>
				<th>Email</th>
				<th>Join date</th>
				<th>Group</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$user_list}
		</tbody>
	</table>
	<div class="pagination pagination-right">
		<ul>
			{$pages}
		</ul>
	</div>
</div>
</div>
</div>


HTML;
}
?>