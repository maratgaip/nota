<?php

if( ! defined( 'GLOBUS' ) ) {

	die( "Hacking attempt!" );

}

if(isset($_GET['action'])) $action = $_GET['action'];

	echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Sidebar</li>
					<li><a href="{$PHP_SELF}?"><i class="icon-home icon-black"></i> Dashboard</a></li>
					<li><a href="{$PHP_SELF}?do=config"><i class="icon-check icon-black"></i> Site Config</a></li>
					<li><a href="{$PHP_SELF}?do=admin"><i class="icon-lock icon-black"></i> Change password</a></li>
					<li><a href="{$PHP_SELF}?do=artists"><i class="icon-star-empty icon-black"></i> Manager artists</a></li>
					<li class="active"><a href="{$PHP_SELF}?do=lyrics"><i class="icon-list-alt icon-while"></i> Manager lyrics</a></li>
				</ul>
			</div>
		</div>
HTML;

if( $action == "edit" ){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	if(isset($_POST)){
		$title = $db->safesql(trim($_POST['title']));
		$artist = $db->safesql(trim($_POST['artist']));
		$lyrics = $db->safesql(strip_tags($_POST['lyrics']));
		if($title){
			$db->query("UPDATE lyrics_lyrics SET title='$title', artist='$artist', lyrics='$lyrics' WHERE id='$id'");
			define ( 'SUBMIT', true );
		}
	}
	
	$row = $db->super_query("SELECT * FROM lyrics_lyrics WHERE id = '$id'");
	
	if(!$row['id']) die("Artist not exits");
	
	
	echo <<<HTML
		<div class="span9">
HTML;
	if( defined( 'SUBMIT' ) ) {
	echo <<<HTML
<div class="alert alert-success">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<strong>Well done!</strong> Saved the <strong>lyrics</strong> successfully!.
</div>
HTML;
}
	echo <<<HTML
		<h3>Edit: {$row['title']}</h3>
			<form method="post" action="">
				<fieldset>
					<label>Title</label>
					<input class="input-xxlarge" type="text" name="title" value="{$row['title']}"/>
					<label>Artist</label>
					<input class="input-xxlarge" type="text" name="artist" value="{$row['artist']}"/>
					<label>Content</label>
					<textarea name="lyrics" class="input-xxlarge" style="height: 200px">{$row['lyrics']}</textarea>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
	</div>
</div>
HTML;
}elseif( $action == "delete" ){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	if($id){
			$db->query("DELETE FROM lyrics_lyrics WHERE id='$id'");
			define ( 'DELETE', true );
	
	
	echo <<<HTML
<div class="span9">
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		<strong>Well done!</strong> Deleted the lyrics!
		</div>
	</div>
</div>
<script>
setTimeout(function() {
      window.location = '{$PHP_SELF}?do=lyrics';
}, 1000);
</script>
HTML;
}
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 50;
	$db->query("SELECT * FROM lyrics_lyrics LIMIT $start,20");
	while($row = $db->get_row()){
	$lyrics_list .= <<<HTML
		<tr>
			<td>{$row['title']}</td>
			<td>{$row['artist']}</td>
			<td>{$row['count']}</td>
			<td><div class="btn-group">
					<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a href="{$PHP_SELF}?do=lyrics&action=edit&id={$row['id']}">Edit</a></li>
						<li><a href="{$PHP_SELF}?do=lyrics&action=delete&id={$row['id']}" data-confirm="Are you sure you want to delete?">Delete</a></li>
					</ul>
				</div>
			</td>
		</tr>

HTML;
}
	$total = $db->super_query("SELECT COUNT(*) AS count FROM lyrics_lyrics");
	$pages = navigation("admin/?do=lyrics&p={page}", $total['count'], 50);
	
	echo <<<HTML
			<div class="span9">
			<h3>Artists Manager: Total {$total['count']} artists</h3>
		<table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>View</th>
            	<th>Action</th>
              </tr>
            </thead>
            <tbody>
				{$lyrics_list}
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