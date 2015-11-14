<?php
/* 
Plugin Name: BluthCodes
Plugin URI: http://www.bluthemes.com/
Description: A Shortcode plugin from Bluthemes
Version: 1.11
Author: Bluthemes
Author URI: http://www.bluthemes.com
*/

/* Updated: 26. june 2014 */
	function blu_tbl( $atts, $content = null ) {
		$type = (!empty($atts['type']) ? 'table-'.$atts['type'] : '');
		$hover = (!empty($atts['hover']) ? 'table-hover' : '');

		return "\t\n" . '<table class="table '.$type.' '.$hover.'" style="font-size:14px;">'. do_shortcode( $content ).'</table>';
	}
	function blu_tbl_head( $atts, $content = null ) {
		return "\t\n" . '<thead>'. do_shortcode( $content ).'</thead>';
	}
	function blu_tbl_th( $atts, $content = null ) {
		return "\t\n" . '<th>'. do_shortcode( $content ).'</th>';
	}
	function blu_tbl_body( $atts, $content = null ) {
		return "\t\n" . '<tbody>'. do_shortcode( $content ).'</tbody>';
	}
	function blu_tbl_row( $atts, $content = null ) {
		return "\t\n" . '<tr>'. do_shortcode( $content ).'</tr>';
	}
	function blu_tbl_clmn( $atts, $content = null ) {
		return "\t\n" . '<td>'. do_shortcode( $content ).'</td>';
	}

	function blu_icon( $atts, $content = null ) {
		$type = (!empty($atts['type']) ? $atts['type'] : 'fa-android');
		$size = (!empty($atts['size']) ? $atts['size'] : '14');
		$float = (!empty($atts['float']) ? $atts['float'] : 'none');
		$color = (!empty($atts['color']) ? 'color:'.$atts['color'].'; ' : '');

		return "\t\n" . '<i class="fa '.$type.'" style="font-size:'.$size.'px; float:'.$float.'; '.$color.' margin: 5px 5px 0 0;" ></i>';
	}

	function blu_code( $atts, $content = null ) {
		$block = !empty($atts['block']) && $atts['block'] == 'on' ? ' display:block; ' : '';
		$padding = !empty($atts['padding']) ? ' padding:'.$atts['padding'].'px; ' : '';
		return "\t\n" . '<pre style="'.$block.$padding.'">'. htmlspecialchars(do_shortcode( $content )) . '</pre>';
	}
	function blu_pre( $atts, $content = null ) {
		return "\t\n" . '<pre>'. htmlspecialchars(do_shortcode( $content )) . '</pre>';
	}

	function blu_mailchimp($atts, $content = null){

		extract(shortcode_atts(array('title' => '', 'text' => '', 'buttontext' => 'Subscribe!', 'list' => '0', 'api' => '0'),$atts));
		
		$output = 	'<div class="bl_newsletter shortcode">';
		$output .= 		'<h3>' . $title . '</h3>';
		$output .= 		'<p>' . $text . '</p>';
		$output .= 		'<div class="input-group">';
	    $output .= 			'<input class="bl_newsletter_email form-control" type="text" placeholder="' . __('Email address', 'bluth_admin') . '">';
	    $output .= 			'<span class="input-group-btn"><button data-shortcode="true" data-api="' . $api . '" data-list="' . $list . '" class="btn btn-success" type="button">' . $buttontext . '</button></span>';
		$output .= 		'</div>';
		$output .= 	'</div>';



		return $output;
	
	}
	function blu_pullquote($atts, $content = null){

		extract(shortcode_atts(array('align'=> 'left', 'background' => 'transparent', 'style' => 'normal', 'bordercolor' => '#444444', 'borderradius' => '0px'),$atts));
		return '<blockquote class="pullquote ' . $style . '  '.(($align == 'right') ? 'pull-right' : 'pull-left').'" style="' . 'background-color: ' . $background . '; border-color:' . $bordercolor . '; border-radius: ' . $borderradius . ';"><p class="pullquote-text">'.do_shortcode($content).'</p></blockquote>';
	}
	function blu_quote( $atts, $content = null ) {

		$text_size 				= ' font-size:20px; ';
		$text_thickness 		= ' font-weight:100; ';
		$authortext_size 		= ' font-size:13px; ';
		$authortext_thickness 	= ' font-weight:600; ';
		$line_height 			= ' line-height:25px; ';
		$textalign 				= ' text-align:left; ';


		$bgcolor 				= empty($atts['bgcolor']) ? ' background-color:rgba(0,0,0,0); ' : ' background-color:'.$atts['bgcolor'].'; ';
		$iconcolor 				= empty($atts['iconcolor']) ? '' : ' color:'.$atts['iconcolor'].'; ';
		$iconcolorclass			= empty($iconcolor) ? 'bl_color' : '';
		$paragraphcolor 		= empty($atts['paragraphcolor']) ? '' : ' color:'.$atts['paragraphcolor'].'; ';
		$paragraphcolorclass	= empty($paragraphcolor) ? 'bl_color' : '';
		$authorcolor 			= empty($atts['authorcolor']) ? ' color:#555555; ' : ' color:'.$atts['authorcolor'].'; ';

		$author 				= empty($atts['author']) ? '' : '<div class="bl_authortext" style="'.$authortext_size.$authortext_thickness.$authorcolor.$line_height.'">'.$atts['author'].'</div>';

		$html = '';
		$html .= '<div class="bl_quotebox" style="'.$bgcolor.'">';
		$html .= 	'<i class="fa fa-quote-left '.$iconcolorclass.'" style="'.$iconcolor.'"></i>';
		$html .= 	'<div class="bl_body" style="'.$textalign.'">';
		$html .= 		'<div class="bl_quotetext '.$paragraphcolorclass.'" style="'.$text_size.$text_thickness.$paragraphcolor.$line_height.'">'.do_shortcode( $content ).'</div>';
		$html .= 		$author;
		$html .= 	'</div>';
		$html .= '</div>';
		// echo $html;

		return "\t\n" . $html;
	}
	/**
	 * Dropcaps 
	 * @param  array $atts Array of attributes
	 * @return html returns a drop cap
	 */
	function blu_dropcap($atts, $content = null){

		extract(shortcode_atts(array('background'=> '', 'color' => '#333333', 'size' => ''),$atts));
		return '<span class="dropcap'.((empty($background) or $background == 'no') ? '' : ' dropcap-bg').'" style="' . ((empty($background) or $background == 'no') ? 'color:' . $color : 'background-color:' . $color ) . '; ' . (empty($size) ? '' : 'font-size:' . $size ) . ';">'.do_shortcode($content).'</span>';
	}

	/**
	 * Bullet List 
	 * @param  array $atts Array of attributes
	 * @return html returns a drop cap
	 */
	function blu_bulletlist($atts, $content = null){

		extract(shortcode_atts(array('title' => '', 'align'=> 'left', 'background' => 'off'),$atts));
		if($title){
			$title = '<h4>' . $title . '</h4>';
		}
		return '<div class="bulletlist ' . $align . '" style="float:' . $align . '; ' . (( $background == 'off' ) ? 'background-color: transparent; border: none;' : '' ) . '">'.$title.'<ul>' . do_shortcode($content) . '</ul></div>';
	}
	function blu_bulletlist_item($atts, $content = null){
		// extract(shortcode_atts(array('align'=> 'left', 'background' => 'off'),$atts));
		return '<li>' . do_shortcode($content) . '</li>';
		
	}

	/**
	 * Alert box 
	 * @param  array $atts Array of attributes
	 * @return html returns an alert box
	 */
	function blu_alert($atts, $content = null){

		extract(shortcode_atts(array(
	      'style' 	=> 'blue',
	      'close'	=> 'true'
	    ),$atts));

		$html  = '<div class="alert bluth ' . $style . '">';
		if($close == 'true'){
			$html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		}
		$html .= do_shortcode($content);
		$html .= '</div>';
		return $html;
	}

	/**
	 * Social
	 * @param  array $atts Aattributes
	 * @return returns a social link
	 */
	function blu_social($atts, $content = null){
		extract(shortcode_atts(array('media'=> '', 'url' => ''),$atts));
		$name = $media;
		switch($media){
			case 'twitter':
			case 'linkedin':
				$media .= '-1';
			break;
			case 'flickr':
			case 'pinterest':
				$media .= '-circled';
			break;
			case 'vimeo':
			case 'tumblr':
				$media .= '-rect';
			break;
			case 'instagram':
				$media .= '-filled';
			break;
		}
		return '<a href="'.$url.'" class="bl-social-icon tips" data-title="'.$name.'"><i class="icon-'.$media.'"></i></a>';
	}

	/**
	 * Label
	 * @param  array $atts Aattributes
	 * @return html label span
	 */
	function blu_label($atts, $content = null){

		extract(shortcode_atts(array('style'=> ''),$atts));
		return '<span class="label bluth ' . $style . '">' . do_shortcode($content) . '</span>';
	}

	/**
	 * Badge
	 * @param  array $atts Aattributes
	 * @return html badge span
	 */
	function blu_badge($atts, $content = null){

		extract(shortcode_atts(array('style'=> ''),$atts));
		return '<span class="badge bluth ' . $style . '">'.do_shortcode($content).'</span>';
	}

	/**
	 * Well
	 * @param  array $atts Aattributes
	 * @return html well inset effect
	 */
	function blu_well($atts, $content = null){
		$content = wpautop(trim($content));
		return '<div class="well">'.do_shortcode( wpautop( $content ) ).'</div>';
	}

	/**
	 * Button
	 * @param  array $atts Aattributes
	 * @return html style button link
	 */
	function blu_button($atts, $content = null){

		extract(shortcode_atts(array(
        'url'      => '#',
		'style'     => '',
        'size'    	=> '',
        'block'    	=> '',
		'target'    => '_self',
		'icon'		=> ''
    	), $atts));
		return '<a href="'.$url.'" class="btn bluth ' . $style . ' ' . 'btn-'.$size . ' ' . ( $block == 'true' ? 'btn-block' : '' ) . '" target="'.$target.'">'.(!empty($icon) ? '<i class="icon-'.$icon.'"></i> ' : '').do_shortcode($content).'</a>';
	}

	/**
	 * Blockquote
	 * @return html For quoting blocks of content
	 */
	function blu_blockquote( $atts, $content = null) {
		
		extract(shortcode_atts(array('source' => ''), $atts));

		return '<blockquote><p>'.do_shortcode($content).(!empty($source) ? '<small>'.$source.'</small>' : '').'</p></blockquote>';
	}


	/**
	 * syntax highlighting
	 * @return html convert content to html enteties
	 */
	function blu_syntax( $atts, $content = null) {
		
		extract(shortcode_atts(array('type' => 'html'), $atts));

		return '<pre class="'.$type.'">'.do_shortcode($content).'</pre>';
	}

	/**
	 * font size
	 * @return html convert content to html enteties
	 */
	function blu_font( $atts, $content = null) {
		
		extract(shortcode_atts(array('size' => '18', 'family' => 'inherit'), $atts));

		return '<span style="font-family: ' . $family . '; font-size:' . $size . 'px;">' . htmlentities(do_shortcode($content)) . '</span>';
	}

	/**
	 * progress bar
	 * @return html convert content to html enteties
	 */
	function blu_progress_bar( $atts, $content = null) {
		
		extract(shortcode_atts(array('length' => '50', 'color' => '#3bd2f8'), $atts));

		return '<div class="bl-progressbar progress"><h5>' . htmlentities(do_shortcode($content)) . '</h5><div class="bar" style="background-color: ' . $color . ' ; width: ' . $length . '%;"><h5 class="length">' . $length . '%</h5></div></div>';
	}

	/**
	 * Tooltip
	 * @param  array $atts Aattributes
	 * @return html Anchor with a tooltip
	 */
	function blu_tooltip( $atts, $content = null)
	{
		extract(shortcode_atts(array('text' => '', 'trigger' => 'hover', 'placement' => 'top'), $atts));

		return '<a href="javascript:void(0)" class="tips" data-toggle="tooltip"  data-trigger="' . $trigger . '" data-placement="' . $placement . '" title="' . $text . '">'. do_shortcode($content) . '</a>';
	}

	/**
	 * Popover
	 * @param  array $atts Aattributes
	 * @return html Anchor with a popover
	 */
	function blu_popover( $atts, $content = null)
	{
		extract(shortcode_atts(array('title' => '', 'trigger' => 'hover', 'placement' => 'top', 'text' => ''), $atts));

		return '<a href="javascript:void(0)" class="bl_popover" data-trigger="'.$trigger.'" data-placement="'.$placement.'" data-content="'.$text.'" title="'.$title.'">'. do_shortcode($content) . '</a>';
	}

	/**
	 * Tabs
	 * @param  array $atts Aattributes
	 * @return tabs with multiple components
	 */
	function blu_tabs_header($atts, $content = null){

		$html  = '<ul class="nav nav-tabs bluth">' . do_shortcode($content) . '</ul>';

		return $html;
    
	}
	function blu_tabs_header_group($atts, $content = null){
		extract(shortcode_atts(array('open'=> 'home', 'active' => ''),$atts));
		if(!empty($active)){ $active = "active"; }
		// if($background){ $background = 'background-color:' . $background . ';'; }

		$html = '<li class="' . $active . '"><a href="#' . $open . '" data-toggle="tab">' . do_shortcode($content) . '</a></li>';

		return $html;
	}
	function blu_tabs_content($atts, $content = null){
		$html = '<div class="tab-content">' . do_shortcode($content) . '</div>';

		return $html;
	}
	function blu_tabs_content_group($atts, $content = null){
		extract(shortcode_atts(array('id'=> 'home', 'active' => ''),$atts));
		if(!empty($active)){ $active = "active"; }
		
		$html = '<div class="tab-pane ' . $active . '" id="' . $id . '">' . do_shortcode(wpautop($content)) . '</div>';

		return $html;
	}
	/**
	 * Accordion
	 * @param  array $atts Aattributes
	 * @return html accordion with multiple collapsible components
	 */
	$blu_accordion = array('parent' => 0, 'id' => 0, 'almost_unique' => rand(0,999));
	function blu_accordion($atts, $content = null){
		global $blu_accordion;
		$blu_accordion['parent']++;

		return '<div class="panel-group" id="' . $blu_accordion['parent'] . '">'.do_shortcode($content).'</div>';
	}

	/**
	 * Accordion
	 * @param  array $atts Aattributes
	 * @return html accordion with multiple collapsible components
	 */
	function blu_accordion_group($atts, $content = null){

		global $blu_accordion;
		$blu_accordion['id']++;

		extract(shortcode_atts(array('title'=> 'Heading', 'style' => ''),$atts));

		$html = '<div class="panel panel-default">';
		$html .= '<div class="panel-heading bluth ' . $style . '">';
		$html .= '<div class="panel-title">';
		$html .= '<a class="accordion-toggle " data-toggle="collapse" data-parent="#' . $blu_accordion['parent'] . '" href="#'.$blu_accordion['almost_unique'].'_'.$blu_accordion['id'].'">';
		$html .= $title;
		$html .= '</a>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div id="'.$blu_accordion['almost_unique'].'_'.$blu_accordion['id'].'" class="accordion-body collapse'.(($blu_accordion['id'] == 1) ? ' in' : '') . '">';
		$html .= '<div class="panel-collapse">';
		$html .= '<div class="panel-body">';
		$html .= do_shortcode( wpautop( $content ) );
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Columns
	 * @return html returns the content in a column
	 */
	// [ ][ ]

	function blu_full_width( $atts, $content = null ) {		
		extract(shortcode_atts(array(
	      'color' 	=> 'transparent',
	      'borderless' 	=> 'false',
	    ),$atts));	
	    $noboxshadow = ($color == 'transparent') ? 'box-shadow:none;' : '';
	    $html =  '<div class="row full_width_row" style="background-color:'.$color.';'.$noboxshadow.'">';
	    if($borderless == 'false'){
	    	$html .= '<div class="col-lg-12 col-md-12 col-sm-12">';
	    	$html .= 	'<p>'. do_shortcode( $content ) . '</p>';
	    	$html .= '</div>';
	    }else{
	    	$html .= 	'<p>'. do_shortcode( $content ) . '</p>';
	    }
	    $html .= '</div>';	
		return $html;
	}

	function blu_two_first( $atts, $content = null ) {			return '<div class="row"><div class="col-md-6 col-sm-6">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_two_second( $atts, $content = null ) {			return '<div class="col-md-6 col-sm-6">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [   ][ ]
	function blu_two_one_first( $atts, $content = null ) {		return '<div class="row"><div class="col-md-8 col-sm-8">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_two_one_second( $atts, $content = null ) {		return '<div class="col-md-4 col-sm-4">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [ ][   ]
	function blu_one_two_first( $atts, $content = null ) {		return '<div class="row"><div class="col-md-4 col-sm-4">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_one_two_second( $atts, $content = null ) {		return '<div class="col-md-8 col-sm-8">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [ ][ ][ ]
	function blu_three_first( $atts, $content = null ) {		return '<div class="row"><div class="col-md-4 col-sm-4">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_three_second( $atts, $content = null ) {		return '<div class="col-md-4 col-sm-4">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_three_third( $atts, $content = null ) {		return '<div class="col-md-4 col-sm-4">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [ ][ ][ ][ ]
	function blu_four_first( $atts, $content = null ) {			return '<div class="row"><div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_four_second( $atts, $content = null ) {		return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_four_third( $atts, $content = null ) {			return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_four_fourth( $atts, $content = null ) {		return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [ ][ ][   ]
	function blu_one_one_two_first( $atts, $content = null ) {	return '<div class="row"><div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_one_one_two_second( $atts, $content = null ) {	return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_one_one_two_third( $atts, $content = null ) {	return '<div class="col-md-6 col-sm-6">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [   ][ ][ ]
	function blu_two_one_one_first( $atts, $content = null ) {	return '<div class="row"><div class="col-md-6 col-sm-6">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_two_one_one_second( $atts, $content = null ) {	return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_two_one_one_third( $atts, $content = null ) {	return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	// [ ][   ][ ]
	function blu_one_two_one_first( $atts, $content = null ) {	return '<div class="row"><div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_one_two_one_second( $atts, $content = null ) {	return '<div class="col-md-6 col-sm-6">'. do_shortcode( wpautop($content) ) . '</div>';	}
	function blu_one_two_one_third( $atts, $content = null ) {	return '<div class="col-md-3 col-sm-3">'. do_shortcode( wpautop($content) ) . '</div></div>';	}

	/**
	 * Divider 
	 * @param  array $atts Array of attributes
	 * @return html returns a row-fluid divider
	 */
	function blu_divider( $atts, $content = null ) {

		extract(shortcode_atts(array(
	      'type' 	=> 'white',
	      'color' 	=> 'rgba(0,0,0,0.1)',
	      'text' 	=> '',
	    ),$atts));


		$icon = !empty($atts['icon']) ? $atts['icon'] == 'off' ? '' : '<i class="'.$atts['icon'].'"></i>' : '<i class="fa fa-caret-square-o-up"></i>';

		$spacing = !empty($atts['spacing']) ? ' margin-top:'.$atts['spacing'].'px; margin-bottom:'.$atts['spacing'].'px; ' : ' margin-top:10px; margin-bottom:10px; ';

		$html = '<div class="row pad-xs-5 pad-sm-10 pad-md-20 pad-lg-20" style="min-height:0; padding-top:0; padding-bottom:0;">';
		switch($type){
			case 'white';
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12" style="min-height:0; '.$spacing.'"></div>';
			break;
			case 'thin':
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12" style="min-height:0; border-bottom:1px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'thick':
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12" style="min-height:0; border-bottom:2px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'short':
				$html .= '<div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-5 col-md-offset-5 col-sm-offset-5" style="min-height:0; border-bottom:2px solid '.$color.';'.$spacing.'"></div>';
			break;
			case 'dotted':
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12" style="min-height:0; border-bottom:1px dotted '.$color.';'.$spacing.'"></div>';
			break;
			case 'dashed':
				$html .= '<div class="col-lg-12 col-md-12 col-sm-12" style="min-height:0; border-bottom:1px dashed '.$color.';'.$spacing.'"></div>';
			break;
			case 'go_to_top':
				$bigspan = empty($text) ? 'col-md-10' : 'col-md-8';
				$smallspan = empty($text) ? 'col-md-2' : 'col-md-4';
				$html .= '<div class="'.$bigspan.'" style="border-bottom:2px solid '.$color.';'.$spacing.'"></div><a class="'.$smallspan.'" href="#top">'.$text. '  ' .$icon.'</a>';
			break;
		}
		$html .= '</div>';

		return $html;
	}


	/**
	 * Intro-Text
	 * @param  array $atts Aattributes
	 * @return Usually the first text in the content
	 */
	function blu_introtext($atts, $content = null){
		extract( shortcode_atts( array( 'size' => '25px' ), $atts ) );
		return '<div class="intro-text" style="font-size: ' . $size . '; ">' . do_shortcode( wpautop( $content ) ) . '</div>';
	}
	
	if(!function_exists('blu_process_shortcode')){
		function blu_process_shortcode($content) {
		    global $blu_shortcode_tags;
		 
		    $original_shortcode_tags = $blu_shortcode_tags;
		    
			add_shortcode( "table", "blu_tbl" ); 
			add_shortcode( "thead", "blu_tbl_head" ); 
			add_shortcode( "th", "blu_tbl_th" ); 
			add_shortcode( "tbody", "blu_tbl_body" ); 
			add_shortcode( "tr", "blu_tbl_row" ); 
			add_shortcode( "td", "blu_tbl_clmn" ); 

			add_shortcode( "code", "blu_code" ); 
			add_shortcode( "pre", "blu_pre" ); 

			add_shortcode('divider', 'blu_divider');
			add_shortcode('social', 'blu_social');
			add_shortcode('alert', 'blu_alert');
			add_shortcode('label', 'blu_label');
			add_shortcode('badge', 'blu_badge');
			add_shortcode('well', 'blu_well');
			add_shortcode('button', 'blu_button');
			add_shortcode('blockquote', 'blu_blockquote');
			add_shortcode('syntax', 'blu_syntax');
			add_shortcode('bluth_icon', 'blu_icon');
			add_shortcode('font', 'blu_font');
			add_shortcode('progress', 'blu_progress_bar');
			add_shortcode('tooltip', 'blu_tooltip');
			add_shortcode('popover', 'blu_popover');
			add_shortcode('tabs-header', 'blu_tabs_header');
			add_shortcode('tabs-header-group', 'blu_tabs_header_group');
			add_shortcode('tabs-content', 'blu_tabs_content');
			add_shortcode('tabs-content-group', 'blu_tabs_content_group');
			add_shortcode('accordion', 'blu_accordion');
			add_shortcode('accordion-group', 'blu_accordion_group');
			add_shortcode('dropcap', 'blu_dropcap');
			add_shortcode('blu_mailchimp', 'blu_mailchimp');
			add_shortcode('pullquote', 'blu_pullquote');
			add_shortcode('blu_quote', 'blu_quote' ); 
			add_shortcode('bulletlist', 'blu_bulletlist');
			add_shortcode('bulletlist_item', 'blu_bulletlist_item');
			add_shortcode('get-posts', 'blu_get_posts');
			add_shortcode('intro-text', 'blu_introtext');

			// COLUMNS
			add_shortcode( "full_width", "blu_full_width" ); 

			add_shortcode( "two_first", "blu_two_first" ); 
			add_shortcode( "two_second", "blu_two_second" ); 

			add_shortcode( "two_one_first", "blu_two_one_first" ); 
			add_shortcode( "two_one_second", "blu_two_one_second" );

			add_shortcode( "one_two_first", "blu_one_two_first" ); 
			add_shortcode( "one_two_second", "blu_one_two_second" ); 

			add_shortcode( "three_first", "blu_three_first" ); 
			add_shortcode( "three_second", "blu_three_second" ); 
			add_shortcode( "three_third", "blu_three_third" ); 

			add_shortcode( "four_first", "blu_four_first" ); 
			add_shortcode( "four_second", "blu_four_second" ); 
			add_shortcode( "four_third", "blu_four_third" ); 
			add_shortcode( "four_fourth", "blu_four_fourth" ); 

			add_shortcode( "one_one_two_first", "blu_one_one_two_first" ); 
			add_shortcode( "one_one_two_second", "blu_one_one_two_second" ); 
			add_shortcode( "one_one_two_third", "blu_one_one_two_third" ); 

			add_shortcode( "two_one_one_first", "blu_two_one_one_first" ); 
			add_shortcode( "two_one_one_second", "blu_two_one_one_second" ); 
			add_shortcode( "two_one_one_third", "blu_two_one_one_third" ); 

			add_shortcode( "one_two_one_first", "blu_one_two_one_first" ); 
			add_shortcode( "one_two_one_second", "blu_one_two_one_second" ); 
			add_shortcode( "one_two_one_third", "blu_one_two_one_third" ); 
		 
		 	// $returned = do_shortcode($content);
		    $blu_shortcode_tags = $original_shortcode_tags;
		    return $content;
		}
	}

	add_filter('the_content', 'blu_process_shortcode', 7);
	
	// Shortcodes in widget
	add_filter('widget_text', 'blu_process_shortcode', 7);
	add_filter('shortcode_filter', 'blu_process_shortcode', 7);
	add_action('admin_head', 'blu_add_tinymce');

	if(!function_exists('blu_add_tinymce')){
		function blu_add_tinymce() {  

		   if(current_user_can('edit_posts') && current_user_can('edit_pages'))  
		   {  
		     add_filter('mce_external_plugins', 'blu_add_tinymce_plugin');  
		     add_filter('mce_buttons', 'blu_add_tinymce_button');
		   }  
		}  
	}  

	if(!function_exists('blu_add_tinymce_plugin')){
		function blu_add_tinymce_plugin($plugin_array) {  
			// If it's a separate plugin and the theme isn't from bluthemes then load the javascript from the plugins directory
			// else load it from the theme directory
			if(!defined('BLUTHEMES')){
				$plugin_array['bluthcodes_location'] = plugins_url('bluthcodes') . '/tinymce/tinymce.js';
			}else{
				$plugin_array['bluthcodes_location'] = get_template_directory_uri() . '/assets/plugins/bluthcodes/tinymce/tinymce.js';
			}
		   
		   return $plugin_array;  
		}  
	}  

	// Define Position of TinyMCE Icons
	if(!function_exists('blu_add_tinymce_button')){
		function blu_add_tinymce_button($buttons) {  
			array_push($buttons, 'bluthcodes');  
			return $buttons;  
		} 
	} 

	if(!function_exists('bluthcodes_assets')){
		function bluthcodes_assets()  { 
			// check if it's a bluth theme, so we don't have to load things twice
			if(!defined('BLUTHEMES')){
				wp_enqueue_style( 'bluth-bootstrap', plugins_url('bluthcodes') . '/bootstrap/bootstrap.min.css' );
				wp_enqueue_script( 'bluth-bootstrap', plugins_url('bluthcodes') . '/bootstrap/bootstrap.min.js', array('jquery') );	
				wp_enqueue_style( 'bluthcodes-style', plugins_url('bluthcodes') . '/style.css' );
			}else{
				wp_enqueue_style( 'bluthcodes-style', get_template_directory_uri()  . '/assets/plugins/bluthcodes/style.css' );
			}
		}
	}
	add_action( 'wp_enqueue_scripts', 'bluthcodes_assets' );