<?php
	add_action( 'init', 'wp_rss_add_custom_taxonomy' );
	/**
	 * Adds the custom Category Taxonomy
	 * 
	 * @since 1.0
	 */
	function wp_rss_add_custom_taxonomy() {
		wprss_c_register_custom_taxonomy();

		if ( !term_exists( 'Uncategorized', 'wprss_category' ) ) {
			wp_insert_term(
				'Uncategorized',
				'wprss_category',
				array(
					'slug'			=> 'uncategorized',
					'description' 	=> ''
				)
			);
		}

		$terms = get_terms( 'wprss_category' );
		foreach ( $terms as $term ) {
			add_feed( $term->slug, 'wprss_c_add_category_feed' );
		}

		// Refreshing cache to reflect modifications
		global $wp_rewrite;
		/* @var $wp_rewrite WP_Rewrite */
		$wp_rewrite->flush_rules( false );
		
		wprss_c_check_existing_feeds();
	}


	/**
	 * Registers the custom Category Taxonomy
	 * 
	 * @since 1.2.10
	 */
	function wprss_c_register_custom_taxonomy() {
		register_taxonomy(
			'wprss_category',
			array( 'wprss_feed', 'wprss_feed_item' ),
			array(
				'hierarchical' => true,
				'labels' => array(
							'name' 				=> __( 'Feed Categories', WPRSS_TEXT_DOMAIN ),
							'singular_name' 	=> __( 'Category', WPRSS_TEXT_DOMAIN ),
							'search_items' 		=> __( 'Search Categories', WPRSS_TEXT_DOMAIN ),
							'all_items' 		=> __( 'All Categories', WPRSS_TEXT_DOMAIN ),
							'parent_item'		=> __( 'Parent Category', WPRSS_TEXT_DOMAIN ),
							'parent_item_colon' => __( 'Parent Category:', WPRSS_TEXT_DOMAIN ),
							'edit_item' 		=> __( 'Edit Category', WPRSS_TEXT_DOMAIN ),
							'update_item' 		=> __( 'Update Category', WPRSS_TEXT_DOMAIN ),
							'add_new_item' 		=> __( 'Add New Category', WPRSS_TEXT_DOMAIN ),
							'new_item_name' 	=> __( 'New Category Name', WPRSS_TEXT_DOMAIN ),
							'menu_name' 		=> __( 'Categories', WPRSS_TEXT_DOMAIN )
				),
				'rewrite' => array(
							'slug' 		   => 'wprss_feed_categories',
							'with_front'   => false,
							'hierarchical' => true
				),
				'public' => true,
			)
		);
	}


	add_filter( 'wprss_shortcode_args', 'wprss_c_shortcode_query' );
	/**
	 * Extends the feed query to include a taxonomy query condition.
	 *
	 * @since 1.0
	 */
	function wprss_c_shortcode_query( $args ) {
		
		if ( isset( $args['category'] ) ) {
			// Parse the category attribute string into an array
			$categories = array_map( 'trim', explode( ',', $args['category'] ) );
			// Prepare the Query arguments
			$query_args = array(
				'post_type'		=>	'wprss_feed',
				'tax_query'		=>	array(
					array(
						'taxonomy'	=>	'wprss_category',
						'field'		=>	'slug',
						'terms'		=>	$categories,
						'operator'	=>	'IN'
					)
				),
				'posts_per_page'	=>	-1,
			);

			// Create the Query
			$query = new WP_Query( $query_args );

			// Ceate an array to store the ID of each source
			$sources = array();
			// If the query returned posts, then ..
			if ( $query->have_posts() ) {

				// iterate through them and ...
				while ( $query->have_posts() ) {
					// Get each query result
					$qt = $query->next_post();
					// Add the ID to the sources array
					array_push ( $sources, $qt->ID );
				}
				// Set the args 'source' index the CSV version of the sources array
				$args['source'] = implode( ',', $sources );

			// IF no posts where found, set the 'source' index to -1 (impossible ID value)
			} else $args['source'] = '-1';

			// Unset the category index
			unset( $args['category'] );

		}
		return $args;
	}


	if ( version_compare(WPRSS_VERSION, '4.6.5', '>') ) {
		add_filter( 'wprss_set_feed_item_custom_columns', 'wprss_c_add_column' );
	}
	add_filter( 'wprss_set_feed_custom_columns', 'wprss_c_add_column' );
	/**
	 * Adds a new 'Categories' column to the 'All Feed Sources' table.
	 * 
	 * @since 1.0
	 * @return array The columns array with an extra 'Categories' column
	 */
	function wprss_c_add_column( $columns ) {
		$columns['category'] = __( 'Categories', WPRSS_TEXT_DOMAIN );
		return $columns;
	}


	add_action( 'manage_wprss_feed_item_posts_custom_column', 'wprss_c_show_category_column', 10, 2 );
	add_action( 'manage_wprss_feed_posts_custom_column', 'wprss_c_show_category_column', 10, 2 );
	/**
	 * Prints out the categories for each feed source in the 'All 
	 * Feed Sources' table.
	 * 
	 * @since 1.0
	 */
	function wprss_c_show_category_column( $column, $post_id ) {
		if ( $column === 'category' ) {
			// Get the terms for each feed, for the wprss_category custom taxonomy
			$terms = wp_get_post_terms( $post_id, 'wprss_category' );
			// Create a new array, that will store only the term names
			$term_names = array();
			// For each term, push its name into the new aray
			foreach ( $terms as $term ) {
				$term_link = 'edit-tags.php?action=edit&taxonomy=wprss_category&tag_ID=' . $term->term_id . '&post_type=wprss_feed';
				array_push( $term_names, '<a href="'.$term_link.'">' . $term->name . '</a>' );
			}
			// Echo out the implosion of the new array, as comma separated values
			echo implode( ', ', $term_names );
		}
	}


	add_action( 'save_post', 'wprss_c_set_post_default_category', 10, 2 );
	/**
	 * Adds the default 'Uncategorized' term for feed items that are saved without a category
	 *
	 * @since 1.0
	 */
	function wprss_c_set_post_default_category( $post_id, $post ) {
		if ( $post->post_status === 'publish' && $post->post_type === 'wprss_feed' ) {
			// Get post categories
			$terms = wp_get_post_terms( $post_id, 'wprss_category' );
			// If the post has no categories, give it the default category
			if ( $terms === null || empty( $terms ) || count( $terms ) === 0 ) {
				wp_set_object_terms( $post_id, 'uncategorized', 'wprss_category' );
			}
		}
		
	}
	

	add_action( 'wprss_items_create_post_meta', 'wprss_c_feed_item_terms_on_import', 10, 3 );
	/**
	 * Adds the categories of the feed source to the newly imported feed item.
	 *
	 * @since 1.2.8
	 */
	function wprss_c_feed_item_terms_on_import( $item_ID, $simplepie_item, $feed_ID ) {

		// Get the categories of the feed source
		$terms = get_the_terms( $feed_ID, 'wprss_category' );
		// check for errors
		if ( $terms && ! is_wp_error( $terms ) ) {
			// Get the category IDs only
			$term_ids = array();
			foreach ( $terms as $term_obj ) {
				$term_ids[] = $term_obj->term_id;
			}
			// Set the categories to the imported feed item
			wp_set_object_terms( $item_ID, $term_ids, 'wprss_category' );

		} else {
			// On error, log the message
			if ( is_wp_error( $terms ) ) {
				wprss_log_obj( "Error while copying categories from feed source #{$feed_ID} to feed item #{item_ID}: " . $terms->get_error_message(), NULL, WPRSS_LOG_LEVEL_ERROR );
			}
		}
	}


	/**
	 * Generate the feeds for categories
	 *
	 * @since 1.0
	 */
	function wprss_c_add_category_feed( $in ) {
		global $wp_query;
		$query = $wp_query->query;
		$category = ( isset( $query['feed'] ) )? $query['feed'] : '';

		// Prepare the post query
		// Get published wprss_feed posts with a wprss_category slug in the taxonomy
        $wprss_custom_feed_query = apply_filters(            
            'wprss_custom_feed_query',
            array(
	            'post_type'   => 'wprss_feed', 
	            'post_status' => 'publish',
	            'cache_results' => false,   // disable caching
	            'tax_query' => array(
					array(
						'taxonomy' => 'wprss_category',
						'field'    => 'slug',
						'terms'    => array( $category ),
						'operator' => 'IN'
					)
				)
	        )
        );

        // Submit the query to get latest feed items
        query_posts( $wprss_custom_feed_query );

        $sources = array();
        while ( have_posts() ) {
        	the_post();
        	$sources[] = get_the_ID();
        }


        // Create the query array
        $pre_query = array(
			'post_type'   	 => 'wprss_feed_item', 
            'post_status'	 => 'publish',
            'cache_results'  => false,   // disable caching
            'meta_query'     => array(
									array(
										'key'     => 'wprss_feed_id',
										'value'   => $sources,
										'compare' => 'IN'
									)
			)
		);

        // Get options
        $options = get_option( 'wprss_settings_general' );
        if ( $options !== FALSE ) {
        	// If options exist, get the limit
        	$limit = $options['custom_feed_limit'];
        	if ( $limit !== FALSE ) {
        		// if limit exists, set the query limit
        		$pre_query['posts_per_page'] = $limit;
        	}
        }

        // query the posts
        query_posts( $pre_query );

        // Send content header and start ATOM output
        header( 'Content-Type: text/xml' );
        // Disabling caching
        header( 'Cache-Control: no-cache, no-store, must-revalidate' ); // HTTP 1.1.
        header( 'Pragma: no-cache' ); // HTTP 1.0.
        header( 'Expires: 0' ); // Proxies.
        echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
        ?>
        <feed xmlns="https://www.w3.org/2005/Atom">
            <title type="text">Latest imported feed items on <?php bloginfo_rss( 'name' ) ?></title>
            <?php
            // Start the Loop
            while ( have_posts() ) : the_post();
            	
            ?>
            <entry>
                <title><![CDATA[<?php the_title_rss() ?>]]></title>
                <link href="<?php the_permalink_rss() ?>" />
                <published><?php echo get_post_time( 'Y-m-d\TH:i:s\Z' ) ?></published>
                <content type="html"><![CDATA[<?php the_content() ?>]]></content>
            </entry>
            <?php
            // End of the Loop
            endwhile;
            ?>
        </feed>
        <?php
	}
