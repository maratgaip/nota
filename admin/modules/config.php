<?php

if( ! defined( 'GLOBUS' ) || $member_id['user_group'] != 1 ) {

	msg_page("error", "<strong>Error!</strong> You don't have permission in this place!", "");

}

if($_POST['send'] == "changesettings") {
	
	$username = $admininfo["name"];
	$password = $admininfo["pass"];
	$save_con = $_POST['save_con'];
//        echo"<pre>";print_r($save_con);echo"<pre>";die;
	
	$handler = fopen( ROOT_DIR . "/includes/config.inc.php", "w" );
	
	fwrite( $handler, "<?PHP \n\n//Configurations\n\n\$config = array (\n\n" );
	foreach ( $save_con as $name => $value ) {
		
		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		$value = str_replace( '"', '\"', $value );
		
		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		
		
		fwrite( $handler, "'{$name}' => \"{$value}\",\n\n" );
	
	}

	
	fwrite( $handler, ");\n\n" );
	//fwrite( $handler, '$admininfo["name"]			= "'.$username.'";' );
	//fwrite( $handler, '$admininfo["pass"]			= "'.$password.'";' );
	fwrite( $handler,"\n\n?>" );
	fclose( $handler );
	
	msg_page("success", "<strong>Well done!</strong> Saved the <strong>configuration</strong> successfully!.", "do=config");
	
	//exit;
	
}

include_once(ROOT_DIR . "/includes/config.inc.php");

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
		<div class="alert alert-block">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>Warning!</h4>
			In this section you can config your site, please make sure all content is correct!
		</div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#config" data-toggle="pill">Site</a></li>
			<li><a href="#social" data-toggle="pill">Social</a></li>
			<li><a href="#music" data-toggle="pill">Music</a></li>
			<li><a href="#seo" data-toggle="pill">SEO</a></li>
		</ul>
		<form method="post" action="">
			<div class="tab-content" id="myTabContent">
				<div id="config" class="tab-pane fade active in">
					<fieldset>
						<label>LICENSE KEY</label>
						<input class="input-xxlarge" type="text" value="{$config[LICENSE]}" disabled/>
						<input name="save_con[LICENSE]" type="hidden" value="{$config[LICENSE]}"/>
						<label>SITE EMAIL (will user like sender email)</label>
						<input class="input-xxlarge" type="text" name="save_con[email]" value="{$config[email]}" required/>
						<label>SITE TITLE</label>
						<input class="input-xxlarge" type="text" name="save_con[sitetitle]" value="{$config[sitetitle]}" required/>
						<label>SITE URL</label>
						<input class="input-xxlarge" type="text" name="save_con[siteurl]" value="{$config[siteurl]}" required/>
						<label>CHARSET</label>
						<input type="text" name="save_con[charset]" value="{$config[charset]}" required/>
						<label>DESCRIPTION</label>
						<textarea name="save_con[webdesc]" style="width: 530px; height: 100px">{$config[webdesc]}</textarea>
						<label>KEYWORDS</label>
						<textarea name="save_con[keywords]" style="width: 530px; height: 100px">{$config[keywords]}</textarea>
						<label>Google Analytics Code</label>
						<textarea name="save_con[analytics]" style="width: 530px; height: 100px">{$config[analytics]}</textarea>
						<label></label>
						<input name="send" type="hidden" value="changesettings"/>
						<button type="submit" class="btn">Save</button>
					</fieldset>
				</div>
				<div id="social" class="tab-pane fade">
					<fieldset>
						<label>Facebook Client ID</label>
						<input class="input-xxlarge" type="text" name="save_con[facebook_client_id]" value="{$config[facebook_client_id]}"/>
						<label>Facebook Client Secret</label>
						<input class="input-xxlarge" type="text" name="save_con[facebook_client_secret]" value="{$config[facebook_client_secret]}"/>
						<label>Twitter Consumer Key</label>
						<input type="text" name="save_con[t_consumer_key]" value="{$config[t_consumer_key]}"/>
						<label>Twitter Consumer Secret</label>
						<input type="text" name="save_con[t_consumer_secret]" value="{$config[t_consumer_secret]}"/>
						<label>Twitter API Token</label>
						<input type="text" name="save_con[t_api_token]" value="{$config[t_api_token]}"/>
						<label>Twitter API Secret</label>
						<input type="text" name="save_con[t_api_token_secret]" value="{$config[t_api_token_secret]}"/>
						<label></label>
						<button type="submit" class="btn">Save</button>
					</fieldset>
				</div>
				<div id="music" class="tab-pane fade">
					<fieldset>
						<label>Album of the Week (Please fill the album ID you want to set)</label>
						<input class="input-xxlarge" type="text" name="save_con[album_week]" value="{$config[album_week]}"/>
						<label></label>
						<button type="submit" class="btn">Save</button>
					</fieldset>
				</div>
				<div id="seo" class="tab-pane fade">
					<fieldset>
						<label>Website icon</label>
						<input class="input-xxlarge" type="text" name="save_con[site_icon]" value="{$config[site_icon]}" placeholder="Link to your website favorite icon"/>
						<label>Facebook default icon cover</label>
						<input class="input-xxlarge" type="text" name="save_con[facebook_icon]" value="{$config[facebook_icon]}" placeholder="Default cover image will show on facebook when people share your the hompage"/>
						<label>Facebook App Id</label>
						<input class="input-xxlarge" type="text" name="save_con[facebook_app_id]" value="{$config[facebook_app_id]}" placeholder="Your facebook app id, you can get it on http://developers.facebook.com"/>
						<h3>Top of the week option</h3>
						<hr>
						
						<label>Trending web title</label>
						<input class="input-xxlarge" type="text" name="save_con[trending_page]" value="{$config[trending_page]}" placeholder="Trending web title, this will show on google when people search"/>
						<label>Trending description</label>
						<textarea name="save_con[trending_page_descr]" style="width: 530px; height: 100px">{$config[trending_page_descr]}</textarea>
						
						<label>Top of the week web title</label>
						<input class="input-xxlarge" type="text" name="save_con[top_week_page]" value="{$config[top_week_page]}" placeholder="Top of the week web title, this will show on google when people search"/>
						<label>Top of the week description</label>
						<textarea name="save_con[top_week_page_descr]" style="width: 530px; height: 100px">{$config[top_week_page_descr]}</textarea>
						
						<h3>Latest love page option</h3>
						<hr>
						
						<label>Latest love web title</label>
						<input class="input-xxlarge" type="text" name="save_con[latest_love_page]" value="{$config[latest_love_page]}" placeholder="Latest loved songs page option title"/>
						<label>Latest love description</label>
						<textarea name="save_con[latest_love_page_descr]" style="width: 530px; height: 100px">{$config[latest_love_page_descr]}</textarea>
						
						<h3>Genres page option</h3>
						<hr>
						<label>Genre page title</label>
						<input class="input-xxlarge" type="text" name="save_con[genre_page]" value="{$config[genre_page]}" placeholder="Genre web title, this will show on google when people search"/>
						<label>Genre page description</label>
						<textarea name="save_con[genre_page_descr]" style="width: 530px; height: 100px">{$config[genre_page_descr]}</textarea>
						
						<h3>Song page option</h3>
						<hr>
						
						<label>Song page title</label>
						<input class="input-xxlarge" type="text" name="save_con[song_page_title]" value="{$config[song_page_title]}" placeholder="User %name% for song title, %artist% and %sitetitle% for webtitle"/>
						<label>Song page description</label>
						<textarea name="save_con[song_page_descr]" style="width: 530px; height: 100px">{$config[song_page_descr]}</textarea>
						
						<h3>Search page option</h3>
						<hr>
						
						<label>Search page title</label>
						<input class="input-xxlarge" type="text" name="save_con[search_page_title]" value="{$config[search_page_title]}" placeholder="User %name% for song title, %artist% and %sitetitle% for webtitle"/>
						<label>Search page description</label>
						<textarea name="save_con[search_page_descr]" style="width: 530px; height: 100px">{$config[search_page_descr]}</textarea>
						
						<label></label>
						<button type="submit" class="btn">Save</button>
					</fieldset>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
HTML;
?>