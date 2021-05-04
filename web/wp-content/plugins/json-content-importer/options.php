<?php
add_action('admin_menu', 'jci_create_menu');

function jci_create_menu() {
	//create new top-level menu
	add_menu_page(__('JSON Content Importer', 'json-content-importer'), __('JSON Content Importer', 'json-content-importer'), 'administrator', __FILE__, 'jci_settings_page',plugins_url('/images/icon-16x16.png', __FILE__));
	//call register settings function
	add_action( 'admin_init', 'register_jcisettings' );
}

function register_jcisettings() {
	//register our settings
	register_setting( 'jci-options', 'jci_json_url' );
	register_setting( 'jci-options', 'jci_enable_cache' );
	register_setting( 'jci-options', 'jci_cache_time' );
	register_setting( 'jci-options', 'jci_cache_time_format' );
	register_setting( 'jci-options', 'jci_oauth_bearer_access_key' );
	register_setting( 'jci-options', 'jci_http_header_default_useragent' );
	register_setting( 'jci-options', 'jci_gutenberg_off' );
	register_setting( 'jci-options', 'jci_api_errorhandling' );
}

function jci_settings_page() {
?>
<style type="text/css">
  .leftsettings {   width: 70%;  float:left;   }
</style>
<div class="leftsettings">
<h2>JSON Content Importer: <?php _e('Settings', 'json-content-importer'); ?></h2>
<form method="post" action="options.php">
    <?php settings_fields( 'jci-options' ); ?>
    <?php do_settings_sections( 'jci-options' ); ?>
	<table class="widefat striped">
<?php
			echo '<tr><td>';
			submit_button();
			if(!ini_get('allow_url_fopen') ) {
					echo "<a href=\"https://www.php.net/manual/en/features.remote-files.php\" target=\"_blank\">";
					_e('PHP allow_url_fopen check</a>:', 'json-content-importer');
					echo '<br><span style="color:#f00;"><b>';
					_e('NOT ok, allow_url_fopen NOT active: The security settings of your PHP / Webserver maybe prevent getting remote data via http-requests of URLs. You might get timeout-errors when using this plugun.</b>', 'json-content-importer');
					echo '<br>';
					_e('Ask your Serverhoster about setting "allow_url_fopen" TRUE</span>', 'json-content-importer');
			}
			echo '</td></tr>';
?>

        <tr>
        	<td>
			<h2><?php _e('Cacher: Saving API-JSON-data locally saves time by avoding http-Requests', 'json-content-importer'); ?></h2>
          <strong><?php _e('Enable Cache', 'json-content-importer') ?>:</strong> <input type="checkbox" name="jci_enable_cache" value="1" <?php echo (get_option('jci_enable_cache') == 1)?"checked=checked":""; ?> />
        	 &nbsp;&nbsp;&nbsp; <?php _e('reload json from web - if cachefile is older than', 'json-content-importer') ?> <input type="text" name="jci_cache_time" size="2" value="<?php echo get_option('jci_cache_time'); ?>" />
           <select name="jci_cache_time_format">
           			<option value="minutes" <?php echo (get_option('jci_cache_time_format') == 'minutes')?"selected=selected":""; ?>><?php _e('Minutes', 'json-content-importer') ?></option>
                    <option value="days" <?php echo (get_option('jci_cache_time_format') == 'days')?"selected=selected":""; ?>><?php _e('Days', 'json-content-importer') ?></option>
                    <option value="month" <?php echo (get_option('jci_cache_time_format') == 'month')?"selected=selected":""; ?>><?php _e('Months', 'json-content-importer') ?></option>
                    <option value="year" <?php echo (get_option('jci_cache_time_format') == 'year')?"selected=selected":""; ?>><?php _e('Years', 'json-content-importer') ?></option>
           </select> 

			<hr>
          <strong><?php _e('Handle unavailable  APIs', 'json-content-importer') ?>:</strong> 
		  <br>
		  <?php 
			$pluginOption_jci_api_errorhandling = get_option('jci_api_errorhandling');
			if (empty($pluginOption_jci_api_errorhandling)) {
				update_option('jci_api_errorhandling', 0);
				$pluginOption_jci_api_errorhandling = 0;
			}
		  ?>
		  
			<?php _e('If the request to an API to get JSON fails, the plugin can try to use a maybe cached JSON (fill the cache at least once with a successful API-request)', 'json-content-importer') ?>:<br>
		  <input type="radio" name="jci_api_errorhandling" value="0" <?php echo ($pluginOption_jci_api_errorhandling == 0)?"checked=checked":""; ?> />
		  <?php _e('do not try to use cached JSON', 'json-content-importer') ?><br>
		  <input type="radio" name="jci_api_errorhandling" value="1" <?php echo ($pluginOption_jci_api_errorhandling == 1)?"checked=checked":""; ?> />
		  <?php _e('If the API-http-answercode is not 200: try to use cached JSON', 'json-content-importer') ?><br>
		  <input type="radio" name="jci_api_errorhandling" value="2" <?php echo ($pluginOption_jci_api_errorhandling == 2)?"checked=checked":""; ?> />
		  <?php _e('If the API sends invalid JSON: try to use cached JSON', 'json-content-importer') ?><br>
		  <input type="radio" name="jci_api_errorhandling" value="3" <?php echo ($pluginOption_jci_api_errorhandling == 3)?"checked=checked":""; ?> />
		  <?php _e('Recommended (not switched on due to backwards-compatibility):<br>If the API-http-answercode is not 200 OR sends invalid JSON: try to use cached JSON', 'json-content-importer') ?><br>
            </td>
        </tr>
        <tr>
        	<td>
			<h2><?php _e('API-Request: If needed, send Authentication-Info or an Browser-Useragent', 'json-content-importer'); ?></h2>
        <strong><?php _e('oAuth Bearer accesskey: passed in header as "Authorization: Bearer accesskey"<br>(add "nobearer " - mind the space! - if you want to pass "Authorization:accesskey")', 'json-content-importer') ?>:</strong>
        <br>
           <input type="text" name="jci_oauth_bearer_access_key" value="<?php echo get_option('jci_oauth_bearer_access_key'); ?>" size="60"/>
			<hr>
          <strong><?php _e('Send default Useragent (some APIs need that)', 'json-content-importer') ?>:</strong> <input type="checkbox" name="jci_http_header_default_useragent" value="1" <?php echo (get_option('jci_http_header_default_useragent') == 1)?"checked=checked":""; ?> />
            </td>
        </tr>
        <tr>
        	<td>
			<h2><?php _e('Gutenberg?', 'json-content-importer'); ?></h2>
			<a href="https://www.youtube.com/watch?v=t3m0PmNyOHI" target="_blank">Video: <?php _e('Easy JSON Content Importer - Gutenberg-Block', 'json-content-importer') ?></a>
<br>
          <strong><?php _e('Switch off Gutenberg features (maybe a site builder needs that)', 'json-content-importer') ?>:</strong> <input type="checkbox" name="jci_gutenberg_off" value="1" <?php echo (get_option('jci_gutenberg_off') == 1)?"checked=checked":""; ?> />
            </td>
        </tr>
       <tr>
        <td>
			<h2><?php _e('Donate', 'json-content-importer'); ?></h2>
          <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22" target="_blank"><?php _e('Do you like that plugin? Is it helpful? I\'m looking forward for a Donation - easy via PayPal!', 'json-content-importer') ?></a>
        </td>
      </tr>
        <tr>
        	<td>
			<h2><?php _e('Build in Example', 'json-content-importer'); ?></h2>
		 	<?php		$exurl = plugin_dir_url(__FILE__)."json/gutenbergblockexample1.json";
		?>
          <strong><?php _e('Example with this URL:', 'json-content-importer') ?> <a href="<?php echo $exurl; ?>" target="_blank"><?php echo $exurl; ?></a></strong><br>
          <i>
         <?php
            $example = "[jsoncontentimporter ";
            $example .= "url=\"".$exurl."\" debugmode=\"10\" basenode=\"level1\"]\n";
            $example .= "{start}<br>{subloop-array:level2:-1}{level2.key}\n<br>\n{subloop:level2.data:-1}id: {level2.data.id}\n<br>\n{/subloop:level2.data}{/subloop-array:level2}\n";
            $example .= "\n[/jsoncontentimporter]\n";
            $example = htmlentities($example);
            echo "<code>".$example."</code>";
          ?> 
          </i>
            </td>
        </tr>
        <tr>
        	<td>

			<h2><?php _e('Available Syntax for Wordpress-Pages and -Blogentries', 'json-content-importer') ?></h2>

<a href="https://www.youtube.com/watch?v=IiMfE_CUPBo" target="_blank">Video: <?php _e('How to - First Shortcode with JSON Content Importer', 'json-content-importer') ?></a> / <a href="https://www.youtube.com/watch?v=GJGBPvaKZsk" target="_blank">Video: <?php _e('How to: Wikipedia API, JSON Content Importer and WordPress', 'json-content-importer') ?></a>
          <p>
          <strong>[jsoncontentimporter</strong>
         <ul>
         <li>&nbsp;&nbsp;url="http://...json"</li>
         <li>&nbsp;&nbsp;urlgettimeout="<?php _e('number: who many seconds for loading url till timeout?', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;debugmode="<?php _e('number: if 10 show backstage- and debug-info', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;numberofdisplayeditems="<?php _e('number: how many items of level 1 should be displayed? display all: leave empty', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;basenode="<?php _e('starting point of datasets, tha base-node in the JSON-Feed where the data is?', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustbein="<?php _e('default empty, if not empty keywords spearated by\',\'. At least one of these keywords must be in the created text (here: text=code without html-tags)', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustbeindepth="<?php _e('default: 1, number:where in the JSON-tree oneofthesewordsmustbein must be?', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustnotbein="<?php _e('default empty, if not empty keywords spearated by \',\'. If one of these keywords is in the created text, this textblock is igonred (here: text=code without html-tags)', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustnotbeindepth="<?php _e('default: 1, number:where in the JSON-tree oneofthesewordsmustnotbein must be?', 'json-content-importer') ?>"</li>
         <li>&nbsp;&nbsp;<strong>]</strong></li>
         <li><?php _e('Any HTML-Code plus "basenode"-datafields wrapped in "{}"', 'json-content-importer') ?></li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('{subloop:"basenode_subloop":"number of subloop-datasets to be displayed"}', 'json-content-importer') ?></li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Any HTML-Code plus "basenode_subloop"-datafields wrapped in "{}"', 'json-content-importer') ?></li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('{subloop-array:"basenode_subloop_array":"number of subloop-array-datasets to be displayed"}', 'json-content-importer') ?></li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Any HTML-Code plus "basenode_subloop_array"-datafields wrapped in "{}"', 'json-content-importer') ?></li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/subloop-array:"basenode_subloop_array"}</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;{/subloop:"basenode_subloop"}</li>
         </ul>
          <strong>[/jsoncontentimporter]</strong>

         <hr><strong>
         <?php _e('If the subloop is not an object but an array, use {subloop-array} e.g.', 'json-content-importer') ?>:</strong>
         <br>
         {subloop-array:type:5}{1:ifNotEmptyAddRight:aa&lt;br&gt;bb}{2:ifNotEmptyAddLeft:AA}{3:ifNotEmptyAddRight:BB}{/subloop-array}
         <br><?php _e('shows the first, second and third entry of that array, modified by ifNotEmptyAddLeft and ifNotEmptyAddRight.', 'json-content-importer') ?>

         <hr>
          <strong><?php _e('There are some special add-ons for datafields', 'json-content-importer') ?>:</strong>
          <ul>
          <li>"{street:html}": <?php _e('Default-display of a datafield is NOT HTML: "&lt;" etc. are converted to "&amp,lt;". Add "html" to display the HTML-Code as Code.', 'json-content-importer') ?></li>
          <li>"{street:htmlAndLinefeed2htmlLinefeed}":<?php _e(' Same as "{street:html}" plus Text-Linefeeds are converted to &lt;br&gt; HTML-Linebreaks', 'json-content-importer') ?></li>
          <li>"{street:ifNotEmptyAddRight:extratext}": <?php _e('If datafield "street" is not empty, add "," right of datafield-value. allowed chars are', 'json-content-importer') ?>: "a-zA-Z0-9,;_-:&lt;&gt;/ "</li>
          <li>"{street:html,ifNotEmptyAddRight:extratext}": <?php _e('you can combine "html" and "ifNotEmptyAdd..." like this', 'json-content-importer') ?></li>
          <li>"{street:ifNotEmptyAdd:extratext}": <?php _e('some as', 'json-content-importer') ?> "ifNotEmptyAddRight"</li>
          <li>"{street:ifNotEmptyAddLeft:extratext}": <?php _e('If datafield "street" is not empty, add "," left of datafield-value. allowed chars are', 'json-content-importer') ?>: "a-zA-Z0-9,;_-:&lt;&gt;/ "</li>
          <li>"{locationname:urlencode}": <?php _e('Insert the php-urlencoded value of the datafield "locationname". Needed when building URLs. "html" does not work here.', 'json-content-importer') ?></li>
          </ul>

          </td>
        </tr>
        <tr>
        	<td>
         <h2><?php _e('How do I find the proper template for my JSON?', 'json-content-importer') ?></h2>
        <a href="https://wordpress.org/support/plugin/json-content-importer" target="_blank"><?php _e('If you\'re lost: open ticket here', 'json-content-importer') ?></a>
          </td>
        </tr>
    </table>
    </form>
</div>
<?php
  option_page_right();
}

function option_page_right()	{
		$current_user = wp_get_current_user();
		?>
<style type="text/css">
  .rightsettings { margin:12px; margin-left: 70%; font-size: 14px; }
  .rightsettings p,li { font-size: 14px; }
  .rightsettings1 { background-color: #F7BB59; padding: 12px; border-radius: 5px; }
  .rightsettings2 { background-color: white; padding: 12px; border-radius: 5px; }
  .rightsettings h3 {
    color: black;
    font-family: arial;
    font-size: 18px;
    font-weight: bold !important;
    margin-bottom: 14px;
    border-bottom-color: black;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    font-size: 24px;
    margin-top: 30px;
    padding-bottom: 10px;
  }

  .rightsettings ul {
    list-style-image: none;
    list-style-position: inside;
    list-style-type: disc;
  }


</style>
		<div class="rightsettings">
    	<div class="rightsettings1">
<a href="https://json-content-importer.com/?sc=wp" target="_blank"><img src="<?php echo plugins_url( 'images/icon-256x256.png', __FILE__ ); ?>" border="0" title="<?php _e('Learn more about JSON Content Importer PRO', 'json-content-importer') ?>"></a>
				<h3><?php _e('Upgrade to PRO Version!', 'json-content-importer') ?></h3>
				<p><?php _e("When the free version comes to it's limits: Check out the PRO-Version!", 'json-content-importer') ?></p>
       <?php _e('Try it without risk: Full refund, if the PRO plugin can\'t solve your challenge', 'json-content-importer') ?>
        <h3><?php _e('PRO version only', 'json-content-importer') ?>:</h3>
        <ul>
        <li><?php _e('template-manager: no hassle with linebreaks, and: one-place to store, use on many pages', 'json-content-importer') ?></li>
        <li><?php _e('format date and time', 'json-content-importer') ?></li>
        <li><?php _e('application building: pass GET/POST-parameter from Wordpress to JSON-feed', 'json-content-importer') ?></li>
        <li><?php _e('shortcode inside template', 'json-content-importer') ?></li>
        <li><?php _e('twig template-engine: add logic (if...) to your template', 'json-content-importer') ?></li>
        <li><?php _e('create custom post pages out of JSON and custom post types', 'json-content-importer') ?>: <a href="https://www.youtube.com/watch?v=fQsiJj_Aozw" target="_blank">Video</a></li>
        <li><?php _e('and much more...', 'json-content-importer') ?></li>
        </ul></p>
				<a href="https://json-content-importer.com/compare/?sc=wp" target="_blank" title="<?php _e('Learn more about JSON Content Importer PRO', 'json-content-importer') ?>"><?php _e('Learn more about JSON Content Importer PRO', 'json-content-importer') ?></a>
			</div>
		<?php
	}
?>