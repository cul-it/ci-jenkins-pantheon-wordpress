<?php

class FileBird_Notification
{
    public function __construct($plugin_name, $version)
    {
        add_action('wp_ajax_njt_FileBird_review_save', array($this, 'njt_FileBird_review_save'));
        
        $this->notice_first_use();
        $option = get_option('njt_FileBird_review');

        if ($option){ 
            if( (is_string($option) && $option == 'show') || (is_numeric($option) && time() >= $option) ){
                add_action('admin_notices', array($this, 'give_review'));
            }
        }
    }

    public function checkNonce($nonce)
    {
        if (!wp_verify_nonce($nonce, "ajax-nonce")) {
            wp_send_json_error(array('status' => 'Wrong nonce validate!'));
            exit();
        }
    }

    public function hasField($field, $request)
    {
        return isset($request[$field]) ? sanitize_text_field($request[$field]) : null;
    }

    public function njt_FileBird_review_save()
    {
        global $wpdb;
        if (count($_REQUEST)) {
            $nonce = $this->hasField('nonce', $_REQUEST);
            $field = $this->hasField('field', $_REQUEST);

            $this->checkNonce($nonce);

            if ($field == 'later'){
                update_option('njt_FileBird_review', time() + 3*60*60*24); //After 3 days show
            } else if ($field == 'alreadyDid'){
                update_option('njt_FileBird_review', 'hide');
            }
            wp_send_json_success();
        }
        wp_send_json_error(array('message' => "Update fail!"));
    }

    public function give_review()
    {
        if (function_exists('get_current_screen')) {
            if (get_current_screen()->id == 'upload' || get_current_screen()->id == 'plugins') {
                ?>
                <div class="notice notice-success is-dismissible" id="njt-FileBird-review">
                    <h3><?php _e('Give FileBird a review', NJT_FILEBIRD_TEXT_DOMAIN)?></h3>
                    <p>
                        <?php _e('Thank you for choosing FileBird. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', NJT_FILEBIRD_TEXT_DOMAIN)?>
                    </p>
                    <p>
                        <?php _e('We will be forever grateful. Thank you in advance ;)', NJT_FILEBIRD_TEXT_DOMAIN)?>
                    </p>
                    <p>
                        <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', NJT_FILEBIRD_TEXT_DOMAIN)?></a>
                        <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', NJT_FILEBIRD_TEXT_DOMAIN)?></a>
                        <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', NJT_FILEBIRD_TEXT_DOMAIN)?></a>
                    </p>
                </div>
                <script>
                    jQuery(document).ready(function(){jQuery("#njt-FileBird-review a").on("click",function(){var e=jQuery(this).attr("data"),n=!1;"rateNow"==e?("ratePro"==(e="11"===window.njtFBV?"ratePro":"rateFree")&&window.open("https://codecanyon.net/item/media-folders-manager-for-wordpress/reviews/21715379","_blank"),"rateFree"==e&&window.open("https://wordpress.org/support/plugin/filebird/reviews/#new-post","_blank")):n=!0,jQuery.ajax({url:window.ajaxurl,type:"post",data:{action:"njt_FileBird_review_save",field:e,nonce:window.njt_fb_nonce}}).done(function(e){e.success?1==n&&jQuery("#njt-FileBird-review").hide("slow"):(console.log("Error",e.message),1==n&&jQuery("#njt-FileBird-review").hide("slow"))}).fail(function(e){console.log(e.responseText),1==n&&jQuery("#njt-FileBird-review").hide("slow")}),jQuery.ajax({url:"https://preview.ninjateam.org/filebird/wp-json/filebird/v2/addReview",contentType:"application/json",type:"POST",dataType:"json",data:JSON.stringify({field:e})}).done(function(e){e.success||console.log("Error",e.message)}).fail(function(e){console.log(e.responseText)})})});
                </script>
                <?php
            }
        }
    }

    public function notice_first_use()
    {
        global $wpdb;
        $query = 'SELECT count(*) as "count" from ' . $wpdb->prefix . "term_taxonomy" . ' WHERE taxonomy="' . NJT_FILEBIRD_FOLDER . '"';
        $result = $wpdb->get_results($query);
        if (intval($result[0]->count) > 0) {
            return;
        }
        add_action('admin_notices', function () {
            ?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php _e('Create your first folder for media library now.', NJT_FILEBIRD_TEXT_DOMAIN)?>
					<a href="<?php echo esc_url(admin_url('/upload.php')) ?>">
						<strong><?php _e('Get Started', NJT_FILEBIRD_TEXT_DOMAIN)?></strong>
					</a>
				</p>
			</div>
			<?php
        });
    }
}
