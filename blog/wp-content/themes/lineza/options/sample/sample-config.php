<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('Redux_Framework_sample_config')) {

    class Redux_Framework_sample_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            //add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css, $changed_values) {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r($changed_values); // Values that have changed since the last save
            echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

            /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'redux-framework-demo'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'redux-framework-demo'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'redux-framework-demo'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'redux-framework-demo'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'redux-framework-demo') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'redux-framework-demo'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                /** @global WP_Filesystem_Direct $wp_filesystem  */
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
				'icon' => 'el-icon-cogs',
				'icon_class' => 'icon-large',
				'title' => __('General Settings', 'bloompixel'),
				'fields' => array(
					array(
						'id'=>'bpxl_logo',
						'type' => 'media', 
						'url'=> true,
						'title' => __('Custom Logo', 'bloompixel'),
						'subtitle' => __('Upload a custom logo for your site.', 'bloompixel'),
						),	
					array(
						'id'=>'bpxl_favicon',
						'type' => 'media', 
						'url'=> true,
						'title' => __('Custom Favicon', 'bloompixel'),
						'subtitle' => __('Upload a custom favicon for your site.', 'bloompixel'),
						),	
					array(
						'id'=>'bpxl_pagination_type',
						'type' => 'button_set',
						'title' => __('Pagination Type', 'bloompixel'), 
						'subtitle' => __('Select the type of pagination for your site. Choose between Wide and Boxed.', 'bloompixel'),
						'options' => array('1' => 'Numbered','2' => 'Next/Prev'),//Must provide key => value pairs for radio options
						'default' => '1'
						),	
					array(
						'id'=>'bpxl_scroll_btn',
						'type' => 'switch', 
						'title' => __('Scroll to Top Button', 'bloompixel'),
						'subtitle'=> __('Choose this option to show or hide scroll to top button.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_rtl',
						'type' => 'button_set',
						'title' => __('RTL Language', 'bloompixel'), 
						'subtitle' => __('Choose the option if your blog\'s language is rtl.', 'bloompixel'),
						'options' => array('1' => 'Yes','2' => 'No'),//Must provide key => value pairs for radio options
						'default' => '2'
						),	
					array(
						'id'=>'bpxl_footer_code',
						'type' => 'textarea',
						'title' => __('Tracking Code', 'bloompixel'), 
						'subtitle' => __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'bloompixel'),
						)
				)
			);
			
            $this->sections[] = array(
				'icon' => 'el-icon-website',
				'icon_class' => 'icon-large',
				'title' => __('Layout Settings', 'bloompixel'),
				'fields' => array(
					array(
						'id'=>'bpxl_layout',
						'type' => 'image_select',
						'compiler'=>true,
						'title' => __('Main Layout', 'bloompixel'), 
						'subtitle' => __('Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'bloompixel'),
						'options' => array(
								'csb_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/csb.png'),
								'bcs_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/bcs.png'),
								'bsc_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/bsc.png'),
								'scb_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/scb.png'),
								'cb_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/cb.png'),
								'bc_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/bc.png')
							),
						'default' => 'csb_layout'
						),
					array(
						'id'=>'bpxl_layout_type',
						'type' => 'button_set',
						'title' => __('Layout Type', 'bloompixel'), 
						'subtitle' => __('Select the main layout for your site. Choose between Wide and Boxed.', 'bloompixel'),
						'options' => array('1' => 'Full Width','2' => 'Boxed'),//Must provide key => value pairs for radio options
						'default' => '2'
						),		
					array(
						'id'=>'bpxl_blog_style',
						'type' => 'radio',
						'title' => __('Blog Style', 'bloompixel'), 
						'subtitle' => __('Select a style for latest blog posts.', 'bloompixel'),
						'options' => array('1' => 'Blog Style 1', '2' => 'Blog Style 2', '3' => 'Blog Style 3'),//Must provide key => value pairs for radio options
						'default' => '2'
						),
					array(
						'id'=>'bpxl_archive_style',
						'type' => 'radio',
						'title' => __('Archive Style', 'bloompixel'), 
						'subtitle' => __('Select a style for archives.', 'bloompixel'),
						'options' => array('1' => 'Archive Style 1', '2' => 'Archive Style 2', '3' => 'Archive Style 3'),//Must provide key => value pairs for radio options
						'default' => '2'
						),
					array(
						'id'=>'bpxl_post_meta',
						'type' => 'switch', 
						'title' => __('Post Meta (Homepage)', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide post meta on homepage/blog pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'=>'bpxl_post_meta_options',
						'type' => 'checkbox',
						'title' => __('Post Meta Info Options', 'bloompixel'), 
						'required' => array('bpxl_post_meta','=','1'),
						'subtitle' => __('Select which items to show for post meta on homepage.', 'bloompixel'),
						'options' => array('1' => 'Post Author','2' => 'Date','3' => 'Post Category', '4' => 'Post Comments'),//Must provide key => value pairs for multi checkbox options
						'default' => array('1' => '1', '2' => '1', '3' => '1', '4' => '0')//See how std has changed? you also don't need to specify opts that are 0.
						),
				)
			);
			
            $this->sections[] = array(
				'icon' => 'el-icon-brush',
				'title' => __('Styling Options', 'bloompixel'),
				'fields' => array(
					array(
						'id'=>'bpxl_responsive_layout',
						'type' => 'switch', 
						'title' => __('Enable Responsive Layout?', 'bloompixel'),
						'subtitle'=> __('This theme can adopt to different screen resolutions automatically when rsponsive layout is enabled. You can enable or disable the responsive layout.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Enabled',
						'off' => 'Disabled',
						),	
					array(
						'id'=>'bpxl_color_one',
						'type' => 'color',
						'title' => __('Theme Color 1', 'bloompixel'), 
						'subtitle' => __('Pick color scheme 1 for the theme.', 'bloompixel'),
						'default' => '#e91b23',
						'validate' => 'color',
						'transparent' => false,
						),
					array(
						'id'=>'bpxl_color_two',
						'type' => 'color',
						'title' => __('Theme Color 2', 'bloompixel'), 
						'subtitle' => __('Pick color scheme 2 for the theme.', 'bloompixel'),
						'default' => '#cc0000',
						'validate' => 'color',
						'transparent' => false,
						),
					array(
						'id'=>'bpxl_nav_break',
						'type' => 'info',
						'desc' => __('Navigation Menu', 'bloompixel')
						),
					array(
						'id'=>'bpxl_nav_color',
						'type' => 'color',
						'title' => __('Navigation Background Color', 'bloompixel'), 
						'subtitle' => __('Pick a background color for the navigation.', 'bloompixel'),
						'default' => '#262626',
						'validate' => 'color',
						'transparent' => false,
						),
					array(
						'id'=>'bpxl_nav_drop_bg_color',
						'type' => 'color',
						'title' => __('Navigation Dropdown Background Color', 'bloompixel'), 
						'subtitle' => __('Pick a dropdown background color for the navigation.', 'bloompixel'),
						'default' => '#e91b23',
						'validate' => 'color',
						'transparent' => false,
						),
					array(
						'id'=>'bpxl_nav_drop_hover_bg_color',
						'type' => 'color',
						'title' => __('Navigation Link Hover Background Color', 'bloompixel'), 
						'subtitle' => __('Pick a background color hover state of dropdown links for the navigation.', 'bloompixel'),
						'default' => '#cc0000',
						'validate' => 'color',
						'transparent' => false,
						),
					array(
						'id'=>'bpxl_nav_break',
						'type' => 'info',
						'desc' => __('Header', 'bloompixel')
						),
					array( 
						'id'       => 'bpxl_header_bg',
						'type'     => 'background',
						'title'    => __('Header Background', 'bloompixel'),
						'subtitle' => __('Background options for header.', 'bloompixel'),
						'output'   => array('.header'),
						'preview_media' => true,
						'preview' => false,
						'background-attachment' => false,
						'background-size' => false,
						'default' => array(
								'background-color'  => '#ffffff', 
							),
						),
					array(
						'id'             => 'bpxl_header_spacing',
						'type'           => 'spacing',
						'output'         => array('.header'),
						'mode'           => 'padding',
						'units'          => array('px'),
						'units_extended' => 'false',
						'left' => 'false',
						'right' => 'false',
						'title'          => __('Header Padding', 'bloompixel'),
						'subtitle'       => __('Change top and bottom padding of header.', 'bloompixel'),
						'default'            => array(
							'padding-top'     => '15px',
							'padding-bottom'  => '15px',
							'units'          => 'px',
							)
						),
					array(
						'id'             => 'bpxl_logo_margin',
						'type'           => 'spacing',
						'output'         => array('.header #logo'),
						'mode'           => 'margin',
						'units'          => array('px'),
						'units_extended' => 'false',
						'left' => 'false',
						'right' => 'false',
						'title'          => __('Logo Margin', 'bloompixel'),
						'subtitle'       => __('Change top and bottom margin of logo.', 'bloompixel'),
						'default'            => array(
							'margin-top'     => '20px',
							'margin-bottom'  => '20px',
							'units'          => 'px',
							)
						),
					array(
						'id'=>'bpxl_nav_break',
						'type' => 'info',
						'desc' => __('Body', 'bloompixel')
						),
					array( 
						'id'       => 'bpxl_body_bg',
						'type'     => 'background',
						'title'    => __('Body Background', 'bloompixel'),
						'subtitle' => __('Background options for body.', 'bloompixel'),
						'preview_media' => true,
						'preview' => false,
						'default' => array(
								'background-color'  => '#eeeded', 
							),
						),
					array(
						'id'=>'bpxl_bg_pattern',
						'type' => 'image_select',
						'title' => __('Background Pattern', 'bloompixel'), 
						'subtitle' => __('Choose a background pattern for the theme.', 'bloompixel'),
						'options' => array(
										'nopattern' => array('alt' => 'nopattern', 'img' => ReduxFramework::$_url.'assets/img/patterns/nopattern.png'),
										'pattern0' => array('alt' => 'pattern0', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern0.png'),
										'pattern1' => array('alt' => 'pattern1', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern1.png'),
										'pattern2' => array('alt' => 'pattern2', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern2.png'),
										'pattern3' => array('alt' => 'pattern3', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern3.png'),
										'pattern4' => array('alt' => 'pattern4', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern4.png'),
										'pattern5' => array('alt' => 'pattern5', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern5.png'),
										'pattern6' => array('alt' => 'pattern6', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern6.png'),
										'pattern7' => array('alt' => 'pattern7', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern7.png'),
										'pattern8' => array('alt' => 'pattern8', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern8.png'),
										'pattern9' => array('alt' => 'pattern9', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern9.png'),
										'pattern10' => array('alt' => 'pattern10', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern10.png'),
										'pattern11' => array('alt' => 'pattern11', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern11.png'),
										'pattern12' => array('alt' => 'pattern12', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern12.png'),
										'pattern13' => array('alt' => 'pattern13', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern13.png'),
										'pattern14' => array('alt' => 'pattern14', 'img' => ReduxFramework::$_url.'assets/img/patterns/pattern14.png')
											),//Must provide key => value(array:title|img) pairs for radio options
						'default' => 'nopattern'
						),
					array(
						'id'=>'bpxl_nav_break',
						'type' => 'info',
						'desc' => __('Footer', 'bloompixel')
						),
					array( 
						'id'       => 'bpxl_footer_color',
						'type'     => 'background',
						'title'    => __('Footer Background Color', 'bloompixel'),
						'subtitle' => __('Pick a background color for the footer.', 'bloompixel'),
						'output'   => array('.footer, .copyright'),
						'preview_media' => true,
						'preview' => false,
						'transparent' => false,
						'background-image' => false,
						'background-position' => false,
						'background-repeat' => false,
						'background-attachment' => false,
						'background-size' => false,
						'default' => array(
								'background-color'  => '#262626', 
							),
						),
					array(
						'id'=>'bpxl_nav_break',
						'type' => 'info',
						'desc' => __('Custom CSS', 'bloompixel')
						),
					array(
						'id'=>'bpxl_custom_css',
						'type' => 'ace_editor',
						'title' => __('Custom CSS', 'bloompixel'), 
						'subtitle' => __('Quickly add some CSS to your theme by adding it to this block.', 'bloompixel'),
						'mode' => 'css',
						'theme' => 'monokai',
						'default' => ""
						)
				)
			);
			
            $this->sections[] = array(
				'icon' => 'el-icon-font',
				'icon_class' => 'icon-large',
				'title' => __('Typography', 'bloompixel'),
				'fields' => array(										
					array(
						'id'=>'bpxl_body_font',
						'type' => 'typography',
						'title' => __('Body Font', 'bloompixel'),
						'subtitle' => __('Select the main body font for your theme.', 'bloompixel'),
						'output' => array('body'),
						'google'=>true,
						'subsets'=>false,
						'text-align'=>false,
						'color'=>false,
						'font-size'=>false,
						'line-height'=>false,
						'default' => array(
							'font-family'=>'Droid Sans',
							'font-weight'=>'400',
							),
						),
					array(
						'id'=>'bpxl_headings_font',
						'type' => 'typography',
						'title' => __('Headings Font', 'bloompixel'),
						'subtitle' => __('Select the font for headings for your theme.', 'bloompixel'),
						'output' => array('h1,h2,h3,h4,h5,h6, .top-menu, .header, .post-type, .read-more, .article-heading, .slidertitle, .widget-title, .carousel, #tabs li a, .widgettitle, input, .review-item, .final-score-box, .social-widget a, .post-navigation, .comment-reply-link, .fn, #commentform input, #commentform textarea, .pagination, .footer-subscribe'),
						'google'=>true,
						'subsets'=>false,
						'text-align'=>false,
						'color'=>false,
						'font-size'=>false,
						'line-height'=>false,
						'default' => array(
							'font-family'=>'Oswald',
							'font-weight'=>'400',
							),
						),
					array(
						'id'=>'bpxl_menu_font',
						'type' => 'typography',
						'title' => __('Navigation Menu Font', 'bloompixel'),
						'subtitle' => __('Select the font for navigation menu for your theme.', 'bloompixel'),
						'output' => array('#navigation a'),
						'google'=>true,
						'subsets'=>false,
						'text-align'=>false,
						'color'=>false,
						'font-size'=>false,
						'line-height'=>false,
						'default' => array(
							'font-family'=>'Oswald',
							'font-weight'=>'400',
							),
						),
					array(
						'id'=>'bpxl_logo_font',
						'type' => 'typography',
						'title' => __('Logo Font', 'bloompixel'),
						'subtitle' => __('Select the font for logo for your theme.', 'bloompixel'),
						'output' => array('.header #logo a'),
						'google'=>true,
						'subsets'=>false,
						'text-align'=>false,
						'color'=>true,
						'font-size'=>true,
						'line-height'=>true,
						'default' => array(
							'font-family'=>'Oswald',
							'font-size'=>'36px',
							'font-weight'=>'400',
							'line-height'=>'42px',
							'color'=>'#060606',
							),
						),
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-check-empty',
				'icon_class' => 'icon-large',
				'title' => __('Header', 'bloompixel'),
				'fields' => array(	
					array(
						'id'=>'bpxl_current_date',
						'type' => 'switch', 
						'title' => __('Current Date on Header', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide current date on header.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_social_links',
						'type' => 'switch', 
						'title' => __('Social Links on Header', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide social links on header.', 'bloompixel'),
						"default" 		=> 0,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'=>'bpxl_news_ticker',
						'type' => 'switch', 
						'title' => __('Show News Ticker', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide news ticker on header.', 'bloompixel'),
						"default" 		=> 0,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'		=> 'bpxl_news_ticker_cat',
						'type'     => 'select',
						'multi'    => true,
						'data' => 'categories',
						'title'    => __('News Ticker Category', 'bloompixel'), 
						'required' => array('bpxl_news_ticker','=','1'),
						'subtitle' => __('Select category/categories for news ticker.', 'bloompixel'),
						),
					array(
						'id'=>'bpxl_ticker_posts_count',
						'type' => 'slider', 
						'title' => __('Number of Ticker Posts', 'bloompixel'),
						'required' => array('bpxl_news_ticker','=','1'),
						'subtitle'=> __('Choose the number of ticker posts you want to show.', 'bloompixel'),
						"default" 	=> "5",
						"min" 		=> "1",
						"step"		=> "1",
						"max" 		=> "20",
						),
					array(
						'id'=>'bpxl_header_search',
						'type' => 'switch', 
						'title' => __('Search Box on Header', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide search box on header.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_sticky_menu',
						'type' => 'switch', 
						'title' => __('Sticky Navigation Menu', 'bloompixel'),
						'subtitle'=> __('Choose the option to enable or disable the sticky navigation menu.', 'bloompixel'),
						"default" 		=> 0,
						'on' => 'Enabled',
						'off' => 'Disabled',
						),	
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-minus',
				'icon_class' => 'icon-large',
				'title' => __('Footer', 'bloompixel'),
				'fields' => array(
					array(
						'id'=>'bpxl_footer_text',
						'type' => 'editor',
						'title' => __('Copyright Text', 'bloompixel'), 
						'subtitle' => __('Enter copyright text to be shown on footer or you can keep it blank to show nothing.', 'bloompixel'),
						'default' => '&copy; Copyright 2013. Theme by <a href="http://themeforest.net/item/revista-ultimate-flat-magazine-wordpress-theme/5786809?ref=bloompixel">BloomPixel</a>.',
						),
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-folder-open',
				'icon_class' => 'icon-large',
				'title' => __('Single Post Options', 'bloompixel'),
				'fields' => array(	
					array(
						'id'=>'bpxl_breadcrumbs',
						'type' => 'switch', 
						'title' => __('Breadcrumbs', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide breadcrumbs on single pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'=>'bpxl_single_post_meta',
						'type' => 'switch', 
						'title' => __('Single Post Meta', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide post meta on single pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'=>'bpxl_single_post_meta_options',
						'type' => 'checkbox',
						'title' => __('Post Meta Info Options', 'bloompixel'), 
						'required' => array('bpxl_single_post_meta','=','1'),
						'subtitle' => __('Select which items to show for post meta.', 'bloompixel'),
						'options' => array('1' => 'Post Author','2' => 'Date','3' => 'Post Category', '4' => 'Post Comments'),//Must provide key => value pairs for multi checkbox options
						'default' => array('1' => '1', '2' => '1', '3' => '1', '4' => '0')//See how std has changed? you also don't need to specify opts that are 0.
						),
					array(
						'id'=>'bpxl_single_tags',
						'type' => 'switch', 
						'title' => __('Tags', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide tags on single pages.', 'bloompixel'),
						"default" 		=> 0,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_author_box',
						'type' => 'switch', 
						'title' => __('Author Info Box', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide author info box on single pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_next_prev_article',
						'type' => 'switch', 
						'title' => __('Next/Prev Article Links', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide links to Next/Prev articles on single pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_related_posts',
						'type' => 'switch', 
						'title' => __('Related Posts', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide related posts on single pages.', 'bloompixel'),
						"default" 		=> 1,
						'on' => 'Show',
						'off' => 'Hide',
						),	
					array(
						'id'=>'bpxl_related_posts_count',
						'type' => 'slider', 
						'title' => __('Number of Related Posts', 'bloompixel'),
						'required' => array('bpxl_related_posts','=','1'),
						'subtitle'=> __('Choose the number of related posts you want to show.', 'bloompixel'),
						"default" 	=> "3",
						"min" 		=> "3",
						"step"		=> "1",
						"max" 		=> "20",
						),		
					array(
						'id'=>'bpxl_related_posts_by',
						'type' => 'radio',
						'title' => __('Related Posts By', 'bloompixel'), 
						'subtitle' => __('Choose whther to show related posts by categories or tags.', 'bloompixel'),
						'options' => array('1' => 'Categories', '2' => 'Tags'),//Must provide key => value pairs for radio options
						'default' => '1'
						),
					array(
						'id'=>'bpxl_show_share_buttons',
						'type' => 'switch', 
						'title' => __('Social Media Share Buttons', 'bloompixel'),
						'subtitle'=> __('Choose the option to show or hide social media share buttons on single pages.', 'bloompixel'),
						"default" 		=> 0,
						'on' => 'Show',
						'off' => 'Hide',
						),
					array(
						'id'       => 'bpxl_share_buttons_pos',
						'type'     => 'select',
						'title'    => __('Social Buttons Position', 'bloompixel'),
						'subtitle' => __('Select position for social buttons.', 'bloompixel'),
						'required' => array('bpxl_show_share_buttons','=','1'),
						// Must provide key => value pairs for select options
						'options'  => array(
								'above_content' => 'Above Post Content',
								'below_content' => 'Below Post Content',
							),
						'default'  => 'below_content',
						),
					array(
						'id'=>'bpxl_share_buttons',
						'type'     => 'sortable',
						'title' => __('Select Share Buttons', 'bloompixel'), 
						'required' => array('bpxl_show_share_buttons','=','1'),	
						'subtitle' => __('Select which button you want to show.', 'bloompixel'),
						'mode'     => 'checkbox',
						'options'  => array(
							'fb'     => 'Facebook',
							'twitter'    => 'Twitter',
							'gplus'  => 'Google+',
							'linkedin'  => 'LinkedIn',
							'pinterest'  => 'Pinterest',
							'stumbleupon'  => 'StumbleUpon',
							'reddit'  => 'Reddit',
							'tumblr'  => 'Tumblr',
							'delicious'  => 'Delicious',
						),
						// For checkbox mode
						'default' => array(
							'fb' => true,
							'twitter' => true,
							'gplus' => true,
							'linkedin' => false,
							'pinterest' => false,
							'stumbleupon' => false,
							'reddit' => false,
							'tumblr' => false,
							'delicious' => false,
						),
					),
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-eur',
				'icon_class' => 'icon-large',
				'title' => __('Ad Management', 'bloompixel'),
				'fields' => array(	
					array(
						'id'=>'bpxl_below_title_ad',
						'type' => 'textarea',
						'title' => __('Below Post Title Ad', 'bloompixel'), 
						'subtitle' => __('Paste your ad code here.', 'bloompixel'),
						'default' => ''
						),
					array(
						'id'=>'bpxl_below_content_ad',
						'type' => 'textarea',
						'title' => __('Below Post Content Ad', 'bloompixel'), 
						'subtitle' => __('Paste your ad code here.', 'bloompixel'),
						'default' => ''
						),
					array(
						'id'=>'bpxl_para_ad',
						'type' => 'text',
						'title' => __('Show Ad after "X" paragraphs', 'bloompixel'),
						'subtitle' => __('Enter number of paragraphs here.', 'bloompixel'),
						'default' => ''
						),
					array(
						'id'=>'bpxl_para_ad_code',
						'type' => 'textarea',
						'title' => __('Ad Code', 'bloompixel'), 
						'subtitle' => __('Paste your ad code here.', 'bloompixel'),
						'default' => ''
						),
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-twitter',
				'icon_class' => 'icon-large',
				'title' => __('Social Profiles', 'bloompixel'),
				'fields' => array(	
					array(
						'id'=>'bpxl_facebook',
						'type' => 'text',
						'title' => __('Facebook', 'bloompixel'),
						'subtitle' => __('Enter your Facebook URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_twitter',
						'type' => 'text',
						'title' => __('Twitter', 'bloompixel'),
						'subtitle' => __('Enter your Twitter URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_googleplus',
						'type' => 'text',
						'title' => __('Google Plus', 'bloompixel'),
						'subtitle' => __('Enter your Google Plus URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_instagram',
						'type' => 'text',
						'title' => __('Instagram', 'bloompixel'),
						'subtitle' => __('Enter your Instagram URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_youtube',
						'type' => 'text',
						'title' => __('YoutTube', 'bloompixel'),
						'subtitle' => __('Enter your YoutTube URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_pinterest',
						'type' => 'text',
						'title' => __('Pinterest', 'bloompixel'),
						'subtitle' => __('Enter your Pinterest URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_flickr',
						'type' => 'text',
						'title' => __('Flickr', 'bloompixel'),
						'subtitle' => __('Enter your Flickr URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_rss',
						'type' => 'text',
						'title' => __('RSS', 'bloompixel'),
						'subtitle' => __('Enter your RSS URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id'=>'bpxl_linked',
						'type' => 'text',
						'title' => __('Linked in', 'bloompixel'),
						'subtitle' => __('Enter your Linked in URL here.', 'bloompixel'),
						'validate' => 'url',
						'default' => ''
						),
				)
			);	

            $this->sections[] = array(
				'icon' => 'el-icon-shopping-cart',
				'icon_class' => 'icon-large',
				'title' => __('WooCommerce', 'bloompixel'),
				'fields' => array(	
					array(
						'id'=>'bpxl_shop_layout',
						'type' => 'image_select',
						'compiler'=>true,
						'title' => __('Shop and Product Page Layout', 'bloompixel'), 
						'subtitle' => __('Select sidebar alignment for shop and product page. Choose between 2 or 3 column layout.', 'bloompixel'),
						'options' => array(
								'shop_csb_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/csb.png'),
								'shop_cb_layout' => array('alt' => '1 Column', 'img' => ReduxFramework::$_url.'assets/img/layouts/cb.png'),
							),
						'default' => 'shop_csb_layout'
						),
				)
			);

            $this->sections[] = array(
				'icon' => 'el-icon-check',
				'icon_class' => 'icon-large',
				'title' => __('Updates', 'bloompixel'),
				'fields' => array(
					array(
						'id'=>'bpxl_envato_user_name',
						'type' => 'text',
						'title' => __('Envato Username', 'bloompixel'), 
						'subtitle' => __('Enter your Envato (ThemeForest) username here.', 'bloompixel'),
						'default' => ""
						),
					array(
						'id'=>'bpxl_envato_api_key',
						'type' => 'text',
						'title' => __('Envato API Key', 'bloompixel'), 
						'subtitle' => __('Enter your Envato API key here.', 'bloompixel'),
						'default' => ""
						),
				)
			);	

            $this->sections[] = array(
                'type' => 'divide',
            );

            $theme_info  = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __('<strong>Theme URL:</strong> ', 'redux-framework-demo') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __('<strong>Author:</strong> ', 'redux-framework-demo') . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __('<strong>Version:</strong> ', 'redux-framework-demo') . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __('<strong>Tags:</strong> ', 'redux-framework-demo') . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';

            if (file_exists(dirname(__FILE__) . '/../README.md')) {
                $this->sections['theme_docs'] = array(
                    'icon'      => 'el-icon-list-alt',
                    'title'     => __('Documentation', 'redux-framework-demo'),
                    'fields'    => array(
                        array(
                            'id'        => '17',
                            'type'      => 'raw',
                            'markdown'  => true,
                            'content'   => file_get_contents(dirname(__FILE__) . '/../README.md')
                        ),
                    ),
                );
            }


            $this->sections[] = array(
                'title'     => __('Import / Export', 'redux-framework-demo'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'redux-framework-demo'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            ); 

            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'redux-framework-demo'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'redux-framework-demo');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'bpxl_theme_options',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $theme->get('Name'),     // Name that appears at the top of your panel
                'display_version'   => $theme->get('Version'),  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('Theme Options', 'redux-framework-demo'),
                'page_title'        => __('Theme Options', 'redux-framework-demo'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => 'AIzaSyAX_2L_UzCDPEnAHTG7zhESRVpMPS4ssII', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => true,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.


                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => '_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
                'title' => 'Visit us on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://twitter.com/reduxframework',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://www.linkedin.com/company/redux-framework',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );

            // Panel Intro text -> before the form
            if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false) {
                if (!empty($this->args['global_variable'])) {
                    $v = $this->args['global_variable'];
                } else {
                    $v = str_replace('-', '_', $this->args['opt_name']);
                }
                //$this->args['intro_text'] = sprintf(__('<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'redux-framework-demo'), $v);
            } else {
                $this->args['intro_text'] = __('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'redux-framework-demo');
            }

            // Add content after the form.
            //$this->args['footer_text'] = __('<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'redux-framework-demo');
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new Redux_Framework_sample_config();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;
