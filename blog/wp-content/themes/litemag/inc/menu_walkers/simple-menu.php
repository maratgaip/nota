<?php

/**
 * Simple Menu Walker
 * Author: Bluthemes
 * URL: http://www.bluthemes.com
 */
class Bluth_Simple_Menu extends Walker_Nav_Menu {
        
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "\n<ul class=\"child-items\">\n";
    }
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "</ul>\n";
    }
    
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        
        $item_output =  '';

        // prepare anchor properties
        $attributes  = !empty($item->attr_title) ? ' title="'.esc_attr($item->attr_title).'"' : '';
        $attributes .= !empty($item->target) ? ' target="'.esc_attr($item->target).'"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="'.esc_attr($item->xfn).'"' : '';
        $attributes .= !empty($item->url) ? ' href="'.esc_attr($item->url).'"' : '';

        // if it's the top menu and it has children
        if($item->hasChildren){
             array_push($item->classes, 'normal-menu');
        }


        #
        #   ADD CLASSES
        #

        $class_names = '';

        $classes = empty($item->classes) ? array() : (array)$item->classes;

        $class_names = esc_attr(join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item)));


        #
        #   OPEN <li><a>
        #
        
        $output      .= '<li id="menu-item-'. $item->ID . '" class="' . $class_names . '">';
       
        $item_output .= '<a'. $attributes .'>'.$args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a>';

            // if it's the top menu and it has children
            if($item->hasChildren and $depth == 0){ 
                $item_output .= '<div class="child-menu-wrap">';
            }


        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    
    function end_el( &$output, $item, $depth = 0, $args = array() ) {

        if($item->hasChildren and $depth == 0){ 
            $output .= "</div>\n";
        }

        $output .= "</li>\n";
    }

    function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        // check whether this item has children, and set $item->hasChildren accordingly
        $element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]);
        
        // if the element has children then push information on what kind of children it is
        if($element->hasChildren){
            $element->getChildren = array();
            foreach($children_elements[$element->ID] as $child_element){
                array_push($element->getChildren, $child_element->object);

            }
        }

        // continue with normal behavior
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }  

}