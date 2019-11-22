<?php

	add_action( 'add_meta_boxes', 'wprss_kf_add_meta_boxes');
	/**
	 * Adds the meta boxes to the wprss feed source screen.
	 * 
	 * @since 1.2
	 */
	function wprss_kf_add_meta_boxes(){
		add_meta_box(
            'keyword_filtering_meta_box', 
            __( 'Keyword Filtering', WPRSS_TEXT_DOMAIN ),
            'wprss_kf_meta_box_callback', 
            'wprss_feed', 
            'normal', 
            'core'
        );
	}


	/**
	 * The callback that renders the keyword filtering metabox.
	 * 
	 * @since 1.2
	 */
	function wprss_kf_meta_box_callback() {
		global $post;
		$keywords = get_post_meta( $post->ID, 'wprss_keywords', true );
		$keywords_any = get_post_meta( $post->ID, 'wprss_keywords_any', true );
		$keywords_not = get_post_meta( $post->ID, 'wprss_keywords_not', true );
		$keywords_tags = get_post_meta( $post->ID, 'wprss_keywords_tags', true );
		$keywords_not_tags = get_post_meta( $post->ID, 'wprss_keywords_not_tags', true );

		$filter_title = get_post_meta( $post->ID, 'wprss_filter_title', true );
		$filter_title = ( $filter_title == '' )? 'true' : $filter_title;
		$filter_title_checked = ( $filter_title == 'true' )? 'checked="checked"' : '';

		$filter_content = get_post_meta( $post->ID, 'wprss_filter_content', true );
		$filter_content = ( $filter_content == '' )? 'true' : $filter_content;
		$filter_content_checked = ( $filter_content == 'true' )? 'checked="checked"' : '';

		wp_enqueue_style('wprss-kf-options-css');
		?>

        <p class="wprss-kf-help">
            <span class="dashicons dashicons-editor-help"></span>
            <?php _e('Separate your keywords and tags by commas. For example:', 'wprss'); ?>
            <span class="wprss-kf-help-example">sports, news, red velvet, cupcakes</span>
        </p>

		<h3><?php _e('Filter by keywords', 'wprss'); ?></h3>
		<div class="wprss_kf_metabox_section">

            <div class="wprss_kf_metabox_filter_checkboxes">
                <span>
                    <?php _e('Filter the:', 'wprss'); ?>
                </span>

                <label>
                    <input type="hidden" name="wprss_filter_title" value="false" />
                    <input type="checkbox" id="wprss_filter_title" name="wprss_filter_title" value="true" <?php echo $filter_title_checked; ?> />
                    <?php _e('Title', 'wprss'); ?>
                </label>

                <label>
                    <input type="hidden" name="wprss_filter_content" value="false" />
                    <input type="checkbox" id="wprss_filter_content" name="wprss_filter_content" value="true" <?php echo $filter_content_checked; ?> />
                    <?php _e('Content', 'wprss'); ?>
                </label>
            </div>

			<div>
				<label>
                    <?php _e('Only import items that have <b>any</b> of these keywords:', 'wprss'); ?>
                    <br>

                    <input type="text"
                           id="wprss_keywords_any"
                           name="wprss_keywords_any"
                           value="<?php echo esc_attr($keywords_any); ?>"
                           size="60" />
                </label>
			</div>
			<div>
				<label>
                    <?php _e('Only import items that have <b>all</b> of these keywords:', 'wprss'); ?>
                    <br>

                    <input type="text"
                           id="wprss_keywords"
                           name="wprss_keywords"
                           value="<?php echo esc_attr($keywords); ?>"
                           size="60" />
                </label>
			</div>

			<div>
				<label>
                    <?php _e('Do <b>not</b> import items that have <b>any</b> of these keywords:', 'wprss'); ?>
                    <br>

				    <input type="text"
                           id="wprss_keywords_not"
                           name="wprss_keywords_not"
                           value="<?php echo esc_attr($keywords_not); ?>"
                           size="60" />
                </label>
			</div>
		</div>

		<h3><?php _e('Filter by tags', 'wprss'); ?></h3>
		<div class="wprss_kf_metabox_section">
			<div>
				<label>
                    <?php _e('Only import items that have <b>any</b> of these tags:', 'wprss'); ?>
                    <br/>

                    <input type="text"
                           id="wprss_keywords_tags"
                           name="wprss_keywords_tags"
                           value="<?php echo esc_attr($keywords_tags); ?>"
                           size="60" />
                </label>
			</div>
			<div>
				<label for="wprss_keywords_not_tags">
                    <?php _e('Do <b>not</b> import items that have <b>any</b> of these tags:', 'wprss'); ?>
                    <br/>

                    <input type="text"
                           id="wprss_keywords_not_tags"
                           name="wprss_keywords_not_tags"
                           value="<?php echo esc_attr($keywords_not_tags); ?>"
                           size="60" />
                </label>
			</div>
		</div>

		<?php
	}



	add_action( 'save_post', 'wprss_kf_save_post', 8, 2 );
	/**
	 * Saves the post meta, when a post is saved or created.
	 * 
	 * @since 1.2
	 */
	function wprss_kf_save_post( $post_id, $post ) {
		if ( isset($_POST['wprss_keywords']) ) {
			update_post_meta( $post_id, 'wprss_keywords', $_POST['wprss_keywords'] );
		}
		if ( isset($_POST['wprss_keywords_any']) ) {
			update_post_meta( $post_id, 'wprss_keywords_any', $_POST['wprss_keywords_any'] );
		}
		if ( isset($_POST['wprss_keywords_not']) ) {
			update_post_meta( $post_id, 'wprss_keywords_not', $_POST['wprss_keywords_not'] );
		}
		if ( isset($_POST['wprss_keywords_tags']) ) {
			update_post_meta( $post_id, 'wprss_keywords_tags', $_POST['wprss_keywords_tags'] );
		}
		if ( isset($_POST['wprss_keywords_not_tags']) ) {
			update_post_meta( $post_id, 'wprss_keywords_not_tags', $_POST['wprss_keywords_not_tags'] );
		}
		if ( isset($_POST['wprss_filter_title']) ) {
			update_post_meta( $post_id, 'wprss_filter_title', $_POST['wprss_filter_title'] );
		}
		if ( isset($_POST['wprss_filter_content']) ) {
			update_post_meta( $post_id, 'wprss_filter_content', $_POST['wprss_filter_content'] );
		}
	}
