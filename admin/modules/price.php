<?php

if( ! defined( 'GLOBUS' ) || ! $user_group[$member_id['user_group']]['allow_m_radioprofile']) {

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
                
             
		
		
		if(isset($_POST['content'])){
                  
			
			
			$content = $db->safesql($_POST['content']);
			$price = $db->safesql($_POST['price']);
			
					
			
			$db->query("UPDATE vass_pricedetails SET content = '$content',  price = '$price' WHERE id = '$id'");
			
			
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Saved the <strong>content price information</strong> successfully!.
						</div>';
                          define ( 'SUBMIT', true );
		}
		
		$row = $db->super_query("SELECT * FROM vass_pricedetails WHERE id = '$id'");
		if(!$row['id']) die("station not exits");
	
                	
		$txt = "Edit Price: {$row['content']}";

		
	}
	
	echo <<<HTML
		<div class="span6">
HTML;
	if( defined( 'SUBMIT' ) ) {
		echo $message;
	}	
	
	echo <<<HTML
		<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
		<h3>{$txt}</h3>
			<form method="post" action="" enctype="multipart/form-data">
				<fieldset>
					<label>Content Name</label>
					<input class="input-xxlarge" type="text" name="content" value="{$row['content']}" placeholder="content name" autocomplete="off" required/>
					<label>Price</label>
					<input class="input-xxlarge" type="text" name="price" value="{$row['price']}" placeholder="Content price" autocomplete="off" required/>
                                      
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
</div>

HTML;
}else{
		$db->query("SELECT id, content, price FROM vass_pricedetails ");
	
	
	
	
	while($row = $db->get_row()){
	
		$entries .= "
              <tr>
                <td>
    		
			<div class=\"media-body\">
				<h4 class=\"media-heading\"><a href=\"{$PHP_SELF}?do=price&action=edit&id={$row['id']}\">{$row['content']}</a></h4>
				
			</div>
                </td>
                 <td>{$row['price']}dollar</td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=price&action=edit&id={$row['id']}\">Edit</a></li>
                </ul>
              </div>
				</td>
              </tr>";
}
	
	
	
	echo <<<HTML
			<div class="span9">
			
			<h3>Price Manager</h3>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>Title</th>
                                <th>Price</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$entries}
		</tbody>
	</table>
			
		
	</div>

HTML;
}
?>