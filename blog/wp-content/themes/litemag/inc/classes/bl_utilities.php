<?php
class bl_utilities {


    // reads a theme option from wp
    static function get_option($optionName, $default = false) {
        return of_get_option($optionName);
    }

    // updates a theme option
    static function update_option($optionName, $newValue) {
        $theme_options = get_option(BL_THEME_OPTIONS);
        $theme_options[$optionName] = $newValue;
        update_option(BL_THEME_OPTIONS, $theme_options);
    }
}