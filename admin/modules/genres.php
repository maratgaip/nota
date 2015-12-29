<?php

if( ! defined( 'GLOBUS' ) || $member_id['user_group'] != 1 ) {

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

if( $action == "edit" || $action == "add" ){
	
	if($action == "edit"){
	
		if(isset($_GET['id'])) $id = intval($_GET['id']);
		
		if($_POST['name']){
			$name = $db->safesql($_POST['name']);
			$stick = ($_POST['stick']) ? 1:0;
			if($name){
				$db->query("UPDATE vass_genres SET name='$name', stick='$stick' WHERE id='$id'");
				define ( 'SUBMIT', true );
			}
			msg_page("success", "<strong>Well done!</strong> Saved the <strong>genre information</strong> successfully!.", "do=genres");
		}
		
		$row = $db->super_query("SELECT * FROM vass_genres WHERE id = '$id'");
		$row['name'] = stripslashes($row['name']);
		if(!$row['id']) die("Genre not exits");
		
		$txt = "Edit artist: {$row['name']}";
		
		$checked = ($row['stick']) ? "checked" : "";
		
	}elseif($action == "add"){
		
		if(isset($_POST['name'])){
			$name = $db->safesql($_POST['name']);
			$stick = ($_POST['stick']) ? 1:0;
			
			$db->query("INSERT IGNORE INTO vass_genres SET name='$name', stick= '$stick'");
			
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Added genre successfully!.
						</div>';
			define ( 'SUBMIT', true );
		}
		
		$txt = "Add new genre";
	}
	
	echo <<<HTML
		<div class="span6">
HTML;
	if( defined( 'SUBMIT' ) ) echo $message; 
	echo <<<HTML
		<h3>{$txt}</h3>
			<form method="post" action="">
				<fieldset>
					<label>Name</label>
					<input class="input-xxlarge" type="text" name="name" value="{$row['name']}" required/>
					<label class="checkbox">
					<input type="checkbox" name="stick" value="1" {$checked}> Stick?
					</label>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
		<div class="span3">
			{$image}
		</div>
	</div>
</div>
HTML;
}elseif( $action == "delete"){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_genres WHERE id = '$id'");
	
	msg_page("success", "<strong>Well done!</strong> Deleted the genres!", "do=genres");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	if($q){
		$db->query("SELECT * FROM vass_genres WHERE name LIKE '%$q%' ORDER BY stick DESC LIMIT $start,20");
	}else{
		$db->query("SELECT * FROM vass_genres ORDER BY stick DESC LIMIT $start,20");
	}
	
	while($row = $db->get_row()){
	
	$row['name'] = stripslashes($row['name']);
	
	$stick_text = ($row['stick']) ? "<span class=\"label label-success\">Sticked</span>" : "";
	$genre_list .= "
              <tr>
                <td>{$row['name']}</td>
				<td>{$stick_text}</td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=genres&action=edit&id={$row['id']}\">Edit</a></li>
                  <li><a href=\"{$PHP_SELF}?do=genres&action=delete&id={$row['id']}\">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>";
	}
	
	if($q){
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_genres WHERE name LIKE '%$q%'");
		$pages = navigation("admin/index.php?do=genres&q=" . urlencode($q) . "&p={page}", $total['count'], 20);
	}else{
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_genres");
		$pages = navigation("admin/index.php?do=genres&p={page}", $total['count'], 20);
	}
	
	echo <<<HTML
			<div class="span9">
			<p style="float:right;20px;">
				<button onClick="document.location='?do=genres&action=add'; return(false)" id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new genre</button>
			</p>
			<h3>Genres Manager: Total {$total['count']} genres</h3>
	<div class="alert alert-info">
    	<button type="button" class="close" data-dismiss="alert">&times;</button>
    	<strong>Sticked</strong> mean genre will show on Explore/Genres.
    </div>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="genres">
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
				<th>Title</th>
				<th>Stick</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$genre_list}
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