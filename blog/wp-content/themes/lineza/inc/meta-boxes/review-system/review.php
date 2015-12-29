<?php
// Add the Meta Box
function add_review_box() {
    add_meta_box(
		'review_box', // $id
		'Review Box', // $title 
		'show_review_box', // $callback
		'post', // $page
		'normal', // $context
		'core'); // $priority
}
add_action('add_meta_boxes', 'add_review_box');

// Field Array
$prefix = 'review_';
$custom_meta_fields = array(
	array(
		'label'=> 'Enable Review?',
		'desc'	=> 'Check this option to enable review system for this post.',
		'id'	=> $prefix.'enable',
		'type'	=> 'checkbox'
	),
	array(
		'label'=> 'Rating Type',
		'desc'	=> 'Select the type of rating.',
		'id'	=> $prefix.'type',
		'type'	=> 'select',
		'options' => array (
			'star' => array (
				'label' => 'Stars',
				'value'	=> 'star'
			),
			'percent' => array (
				'label' => 'Percentage',
				'value'	=> 'percent'
			)
		)
	),
	array(
		'label'=> 'Review Color',
		'desc'	=> 'Choose a color for review stars or percentage bar.',
		'id'	=> $prefix.'color',
		'type'	=> 'colorpicker'
	),
);
$i=1;
$reviewfields = 6;
while ($i <= ($reviewfields)) {	
	$reviewcriteria = array(
					'label' => __('Review Criteria ', 'bloompixel').$i,
					'desc' => __('Enter your review criteria.', 'bloompixel'),
					'id' => $prefix . 'criteria'.$i,
					'type' => 'text',
					'std' => ''
					);
	$reviewrating = array(
					'label' => __('Criteria Rating ', 'bloompixel').$i,
					'desc' => __('Enter your first criteria rating.', 'bloompixel'),
					'id' => $prefix . 'rating'.$i,
					'type' => 'textrating',
					'std' => '0'
					);
					
	array_push($custom_meta_fields, $reviewcriteria);
	array_push($custom_meta_fields, $reviewrating);
	$i++;
}
array_push($custom_meta_fields,
	array(
		'label'=> 'Summary',
		'desc'	=> 'A description for the field.',
		'id'	=> $prefix.'summary',
		'type'	=> 'textarea'
	)
);

function bpxl_admin_scripts() {
	wp_enqueue_script('wp-color-picker');
	wp_enqueue_style( 'wp-color-picker' );
	wp_register_script( 'admin-scripts', get_template_directory_uri() . '/js/admin-scripts.js', array( 'jquery' ), '1.4.8', true );
	wp_enqueue_script( 'admin-scripts' );
}
add_action( 'admin_enqueue_scripts', 'bpxl_admin_scripts' );
?>
<?php

// The Callback
function show_review_box() {
global $custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	
	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ($custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with
				switch($field['type']) {
					// color picker
					case 'colorpicker':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-default-color="#ffffff" />
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					// text
					case 'text':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					// textrating
					case 'textrating':
						echo '<tr>
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					// textarea
					case 'textarea':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					// checkbox
					case 'checkbox':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
							<label for="'.$field['id'].'">'.$field['desc'].'</label>';
					break;
					
					// select
					case 'select':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
						foreach ($field['options'] as $option) {
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						echo '</select><br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					// radio
					case 'radio':
						echo '<tr style="border-top:1px solid #DFDFDF;">
								<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
								<td>';
						foreach ( $field['options'] as $option ) {
							echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
					break;  
				} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

// Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;
	
	// verify nonce
	//if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
	if (!isset($_POST[ 'custom_meta_box_nonce' ] ) || !wp_verify_nonce( $_POST[ 'custom_meta_box_nonce' ], basename( __FILE__ ) ) )
		return $post_id;
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}
	
	// loop through fields and save the data
	foreach ($custom_meta_fields as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	} // end foreach
}
add_action('save_post', 'save_custom_meta'); 
?>