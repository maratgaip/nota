<?php

if ( ! function_exists( 'ot_get_option' ) ) {

  function ot_get_option( $option_id, $default = '' ) {

    /* get the saved options */
    $options = get_option( 'option_tree' );

    /* look for the saved value */
    if ( isset( $options[$option_id] ) && '' != $options[$option_id] ) {

      return ot_wpml_filter( $options, $option_id );

    }

    return $default;

  }

}

if ( ! function_exists( 'ot_wpml_filter' ) ) {

  function ot_wpml_filter( $options, $option_id ) {

    // Return translated strings using WMPL
    if ( function_exists('icl_t') ) {

      $settings = get_option( 'option_tree_settings' );

      if ( isset( $settings['settings'] ) ) {

        foreach( $settings['settings'] as $setting ) {

          // List Item & Slider
          if ( $option_id == $setting['id'] && in_array( $setting['type'], array( 'list-item', 'slider' ) ) ) {

            foreach( $options[$option_id] as $key => $value ) {

              foreach( $value as $ckey => $cvalue ) {

                $id = $option_id . '_' . $ckey . '_' . $key;
                $_string = icl_t( 'Theme Options', $id, $cvalue );

                if ( ! empty( $_string ) ) {

                  $options[$option_id][$key][$ckey] = $_string;

                }

              }

            }

          // All other acceptable option types
          } else if ( $option_id == $setting['id'] && in_array( $setting['type'], apply_filters( 'ot_wpml_option_types', array( 'text', 'textarea', 'textarea-simple' ) ) ) ) {

            $_string = icl_t( 'Theme Options', $option_id, $options[$option_id] );

            if ( ! empty( $_string ) ) {

              $options[$option_id] = $_string;

            }

          }

        }

      }

    }

    return $options[$option_id];

  }

}

global $meta_boxes;

$prefix = 'cb_';
$cb_name = ( wp_get_theme()->Name );

if ( ( $cb_name != 'Valenti' ) && ( $cb_name != 'Ciola' ) ) {
    $cb_name = 'Cubell Themes:';
}
$cb_cpt_list = ot_get_option( 'cb_cpt', NULL );
$cb_cpt_output = array('post');

if ( $cb_cpt_list != NULL ) {
    $cb_cpt = explode(',', str_replace(' ', '', $cb_cpt_list ) );

    foreach ( $cb_cpt as $cb_cpt_single ) {
        $cb_cpt_output[] = $cb_cpt_single;
    }
}

$meta_boxes = array();

// Post Options Format
$meta_boxes[] = array(
    'id' => 'cb_style',
    'title' => $cb_name. ' Background Options',
    'pages' => $cb_cpt_output,
    'context' => 'normal',
    'priority' => 'high',

    'fields' => array(
        array(
            'name' => 'Background Color',
            'id'   => $prefix . 'bg_color_post',
            'type' => 'color',
            'desc' => 'Overrides Global + Category Background Color'
        ),
        array(
            'name' => 'Background Image',
            'id'   => $prefix . 'bg_image_post',
            'type' => 'image_advanced',
            'desc' => 'Overrides Global + Category Background Image'
        ),
        array(
            'name'     => 'Background Image Settings',
            'id'       => $prefix . 'bg_image_post_setting',
            'desc' => "How do you want the background image to look?",
            'type'     => 'select',
            'std'   => '1',
            'options'  => array(
                '1' => 'Full-Width Stretch',
                '2' => 'Repeat',
                '3' => 'No-Repeat',
            ),
            'multiple' => false,
        ),
    )
);

// Page Options
$meta_boxes[] = array(
    'id' => 'cb_style',
    'title' => $cb_name. ' Page Options',
    'pages' => array( 'page' ),
    'context' => 'normal',
    'priority' => 'high',

    'fields' => array(
        array(
            'name' => 'Global Page Color',
            'id'   => $prefix . 'overall_color_post',
            'type' => 'color',
            'desc' => 'For the navigation menu'
        ),
        array(
            'name' => 'Background Color',
            'id'   => $prefix . 'bg_color_post',
            'type' => 'color',
            'desc' => 'Overrides Background Color'
        ),
        array(
            'name' => 'Background Image',
            'id'   => $prefix . 'bg_image_post',
            'type' => 'image_advanced',
            'desc' => 'Overrides Global + Category Background Image'
        ),
        array(
            'name'     => 'Background Image Settings',
            'id'       => $prefix . 'bg_image_post_setting',
            'desc' => "How do you want the background image to look?",
            'type'     => 'select',
            'std'   => '1',
            'options'  => array(
                '1' => 'Full-Width Stretch',
                '2' => 'Repeat',
                '3' => 'No-Repeat',
            ),
            'multiple' => false,
        ),
        array(
            'name'     => 'Custom Sidebar',
            'id'       => $prefix . 'page_custom_sidebar',
            'desc' => "",
            'type'     => 'select',
            'std'   => '1',
            'options'  => array(
                '1' => 'Off',
                '2' => 'On',
            ),
            'multiple' => false,
        ),
        array(
            'name'     => 'Featured Image Style',
            'id'       => $prefix . 'page_featured_style',
            'desc' => "",
            'type'     => 'select',
            'std'   => '1',
            'options'  => array(
                '1' => 'Standard',
                '2' => 'Full-width',
                '4' => 'Parallax',
                '5' => 'Full-Background',
                '3' => 'Off',
            ),
            'multiple' => false,
        ),
        array(
            'name'     => 'Comments',
            'id'       => $prefix . 'page_comments',
            'desc' => "If you enable comments, you may also need to click on 'screen options' on the top right and check the 'discussion' box and make sure 'Allow Comments' is also enabled.",
            'type'     => 'select',
            'std'   => '1',
            'options'  => array(
                'off' => 'Off',
                'on' => 'On',
            ),
            'multiple' => false,
        ),
    )
);


// Post Review Options
$meta_boxes[] = array(
    'id' => 'cb_review',
    'title' => $cb_name.' Review System',
    'pages' => $cb_cpt_output,
    'context' => 'normal',
    'priority' => 'high',

    'fields' => array(
        // Enable Review
        array(
            'name' => 'Include Review Box',
            'id' => $prefix . 'review_checkbox',
            'type' => 'checkbox',
            'desc' => 'Enable Review On This Post',
            'std'  => 0,
        ),
        // Type of review
        array(
            'name'     => 'Type',
            'id'       => $prefix . 'score_display_type',
            'type'     => 'select',
            // Array of 'value' => 'Label' pairs for select box
            'options'  => array(
                'percentage' => 'Percentage',
                'stars' => 'Stars',
                'points' => 'Points',
            ),
            // Select multiple values, optional. Default is false.
            'multiple' => false,
        ),
        // Location of review
        array(
            'name'     => 'Location',
            'id'       => $prefix . 'placement',
            'type'     => 'select',
            // Array of 'value' => 'Label' pairs for select box
            'options'  => array(
                'top' => 'Top',
                'top-half' => 'Top Half-Width',
                'bottom' => 'Bottom',
            ),
            // Select multiple values, optional. Default is false.
            'multiple' => false,
            'std'   => 'Select a location',
        ),
        // Type of Review
        array(
            'name'     => 'Type of review',
            'id'       => $prefix . 'user_score',
            'type'     => 'select',
            // Array of 'value' => 'Label' pairs for select box
            'options'  => array(
                'cb-both' => 'Editor Review + User Ratings',
                'cb-editor' => 'Editor Review Only',
                'cb-readers' => 'User Ratings Only',
            ),
            // Select multiple values, optional. Default is false.
            'multiple' => false,
            'std'   => 'Select a location',
        ),
       // Sub-title
        array(
            'name'  => 'Score Sub-Title Outside Post',
            'id'    => $prefix . 'rating_short_summary',
            'type'  => 'text',
            'desc' => 'Appears in modules/blog styles/widgets/etc',

        ),
        // Sub-title Inside Post
        array(
            'name'  => 'Score Sub-Title Inside Post',
            'id'    => $prefix . 'rating_short_summary_in',
            'type'  => 'text',
            'desc' => 'Default: "Overall Score"',
        ),
        // Criteria 1 Text & Score
        array(
            'name'  => 'Criteria 1 Title',
            'id'    => $prefix . 'ct1',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 1 Score', 'rwmb' ),
            'id' => $prefix . 'cs1',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Criteria 2 Text & Score
        array(
            'name'  => 'Criteria 2 Title',
            'id'    => $prefix . 'ct2',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 2 Score', 'rwmb' ),
            'id' => $prefix . 'cs2',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Criteria 3 Text & Score
        array(
            'name'  => 'Criteria 3 Title',
            'id'    => $prefix . 'ct3',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 3 Score', 'rwmb' ),
            'id' => $prefix . 'cs3',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Criteria 4 Text & Score
        array(
            'name'  => 'Criteria 4 Title',
            'id'    => $prefix . 'ct4',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 4 Score', 'rwmb' ),
            'id' => $prefix . 'cs4',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Criteria 5 Text & Score
        array(
            'name'  => 'Criteria 5 Title',
            'id'    => $prefix . 'ct5',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 5 Score', 'rwmb' ),
            'id' => $prefix . 'cs5',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Criteria 6 Text & Score
        array(
            'name'  => 'Criteria 6 Title',
            'id'    => $prefix . 'ct6',
            'type'  => 'text',
        ),
        array(
            'name' => __( 'Criteria 6 Score', 'rwmb' ),
            'id' => $prefix . 'cs6',
            'type' => 'slider',
            'js_options' => array(
                'min'   => 0,
                'max'   => 100,
                'step'  => 1,
            ),
        ),
        // Summary
        array(
            'name' => __( 'Summary', 'rwmb' ),
            'id'   => $prefix . 'summary',
            'type' => 'textarea',
            'cols' => 20,
            'rows' => 3,
        ),
        // Pros Title
        array(
            'name'  => 'Positives Title',
            'id'    => $prefix . 'pros_title',
            'type'  => 'text',
        ),
        // Pro 1
        array(
            'name'  => 'Positive 1',
            'id'    => $prefix . 'pro_1',
            'type'  => 'text',
        ),
        // Pro 2
        array(
            'name'  => 'Positive 2',
            'id'    => $prefix . 'pro_2',
            'type'  => 'text',
        ),
        // Pro 3
        array(
            'name'  => 'Positive 3',
            'id'    => $prefix . 'pro_3',
            'type'  => 'text',
        ),
        // Cons Title
        array(
            'name'  => 'Negatives Title',
            'id'    => $prefix . 'cons_title',
            'type'  => 'text',
        ),
         // Con 1
        array(
            'name'  => 'Negative 1',
            'id'    => $prefix . 'con_1',
            'type'  => 'text',
        ),
         // Con 2
        array(
            'name'  => 'Negative 2',
            'id'    => $prefix . 'con_2',
            'type'  => 'text',
        ),
         // Con 3
        array(
            'name'  => 'Negative 3',
            'id'    => $prefix . 'con_3',
            'type'  => 'text',
        ),

        // Final average
        array(
            'name'  => 'Final Average Score',
            'id'    => $prefix . 'final_score',
            'type'  => 'text',
        ),
        // Final average override
        array(
            'name'  => 'Final Score Override',
            'id'    => $prefix . 'final_score_override',
            'type'  => 'text',
        ),

    )
);
// Post Options Format
$meta_boxes[] = array(
    'id' => 'cb_format_options',
    'title' => $cb_name.' Post Format Options',
    'pages' => array( 'post' ),
    'context' => 'normal',
    'priority' => 'high',

    'fields' => array(
        array(
            'name' => '',
            'desc' => '',
            'id' => $prefix . 'gallery_content',
            'type' => 'image_advanced',
            'std' => ''
        )
    )
);

/********************* META BOX REGISTERING ***********************/

/**
 * Register meta boxes
 *
 * @return void
 */
if ( ! function_exists( 'cb_register_meta_boxes' ) ) {
    function cb_register_meta_boxes() {
    	// Make sure there's no errors when the plugin is deactivated or during upgrade
    	if ( !class_exists( 'RW_Meta_Box' ) )
    		return;

    	global $meta_boxes;
    	foreach ( $meta_boxes as $meta_box )
    	{
    		new RW_Meta_Box( $meta_box );
    	}
    }

add_action( 'admin_init', 'cb_register_meta_boxes' );
}