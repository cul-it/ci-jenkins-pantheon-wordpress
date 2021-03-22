<?php

	add_action( 'restrict_manage_posts', 'restrict_feeds_by_category' );
	/**
	 * Sets up the category dropdown menu to filter feeds by category
	 *
	 * @since 1.0
	 */
	function restrict_feeds_by_category() {
		global $typenow;
		global $wp_query;
		if ( $typenow === 'wprss_feed' ) {
			$taxonomy = 'wprss_category';
			$category_taxonomy = get_taxonomy( $taxonomy );
			$selected_category = isset( $wp_query->query['wprss_category'] )? $wp_query->query['wprss_category'] : '';
			wp_dropdown_categories(
				array(
					'show_option_all' =>  sprintf(__( "Show All %s", WPRSS_TEXT_DOMAIN ), $category_taxonomy->label),
					'taxonomy'        =>  $taxonomy,
					'name'            =>  'wprss_category',
					'orderby'         =>  'name',
					'selected'        =>  $selected_category,
					'hierarchical'    =>  true,
					'depth'           =>  3,
					'show_count'      =>  true,
					'hide_empty'      =>  true,
				)
			);
		}
	}
	
	
	add_filter( 'parse_query', 'category_id_to_query_taxonomy_term' );
	/**
	 * Filters the WordPress query to convert the wprss category id into a taxonomy query term
	 *
	 * @since 1.0
	 */
	function category_id_to_query_taxonomy_term( $query ) {
		global $pagenow;
		// Get a pointer to the query variables
		$qv = &$query->query_vars;
		
		// Check if the page is edit.php and if the taxonomy wprss_category exists and is a number
		if ( $pagenow === 'edit.php' && isset( $qv['wprss_category'] ) && is_numeric( $qv['wprss_category'] ) ) {
		
			// Get the term and and re-set the query's term to the term's slug name
			$term = get_term_by( 'id', $qv['wprss_category'], 'wprss_category' );
			if ( $term !== null && $term !== false ) {
				$qv['wprss_category'] = $term->slug;
			}
		}
	}