<?php

/** Custom walker for wp_dropdown_categories for media grid view filter */
class filebird_walker_category_mediagridfilter extends Walker_CategoryDropdown
{

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {

        //if(is_array($category) && !$category['invalid_taxonomy']){
        $pad = str_repeat('&nbsp;', $depth * 3);

        $cat_name = apply_filters('list_cats', $category->name, $category);

        // {"term_id":"1","term_name":"no category"}
        $output .= ',{"term_id":"' . $category->term_id . '",';

        $output .= '"term_name":"' . $pad . esc_attr($cat_name);
        if ($args['show_count']) {
            // $output .= '&nbsp;&nbsp;('. $category->count .')';
            $output .= '&nbsp;&nbsp;';
        }
        $output .= '"}';
        // }

    }

}
