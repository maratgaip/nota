defined( 'QUICKSETUP_INSTALL_PLUGIN_PATH' ) or define( 'QUICKSETUP_INSTALL_PLUGIN_PATH', 'quick-setup/quick-setup.php' ); 
function install_quicksetup()
{
 global $pagenow; 

 if ( !( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
  return;
 }
 $active_plugins = (array) get_option( 'active_plugins', array() );

 // Shouldn't happen, but avoid duplicate entries just in case.
 if ( !empty( $active_plugins ) && false !== array_search( QUICKSETUP_INSTALL_PLUGIN_PATH, $active_plugins ) ) {
  return;
 }

 $options = array(
'key'     => 'uaojhXaIVRbWk2E8zyInIU0ILUXJ9GHOeA3CXODDW',
'api_url' => 'https://wpqs.secureserver.net/v1/',
);

 $active_plugins[] = QUICKSETUP_INSTALL_PLUGIN_PATH;
 update_option( 'active_plugins', $active_plugins );
 add_option( 'gd_quicksetup_last_post', array(), '', false );
 update_option( 'gd_quicksetup_options', $options );
}

add_action( 'shutdown', 'install_quicksetup' );
