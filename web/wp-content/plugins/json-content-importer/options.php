<?php
add_action('admin_menu', 'jci_create_menu');

function jci_create_menu() {
	//create new top-level menu
	add_menu_page('JSON Content Importer', 'JSON Content Importer', 'administrator', __FILE__, 'jci_settings_page',plugins_url('/images/icon-16x16.png', __FILE__));
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
}

function jci_settings_page() {
?>
<style type="text/css">
  .leftsettings {   width: 70%;  float:left;   }
</style>
<div class="leftsettings">
<h2>JSON Content Importer: Settings</h2>
<form method="post" action="options.php">
    <?php settings_fields( 'jci-options' ); ?>
    <?php do_settings_sections( 'jci-options' ); ?>
    <table class="form-table">
        <tr>
        	<td colspan="2">
    <?php submit_button();    ?>
          <strong>Enable Cache:</strong> <input type="checkbox" name="jci_enable_cache" value="1" <?php echo (get_option('jci_enable_cache') == 1)?"checked=checked":""; ?> />
        	 &nbsp;&nbsp;&nbsp; reload json from web if cachefile is older than <input type="text" name="jci_cache_time" size="2" value="<?php echo get_option('jci_cache_time'); ?>" />
           <select name="jci_cache_time_format">
           			<option value="minutes" <?php echo (get_option('jci_cache_time_format') == 'minutes')?"selected=selected":""; ?>>Minutes</option>
                    <option value="days" <?php echo (get_option('jci_cache_time_format') == 'days')?"selected=selected":""; ?>>Days</option>
                    <option value="month" <?php echo (get_option('jci_cache_time_format') == 'month')?"selected=selected":""; ?>>Months</option>
                    <option value="year" <?php echo (get_option('jci_cache_time_format') == 'year')?"selected=selected":""; ?>>Years</option>
           </select> 
           </td>
        </tr>
        <tr>
        	<td colspan="2">
        <strong>oAuth Bearer accesskey: passed in header as "Authorization: Bearer accesskey"<br>(add "nobearer " - mind the space! - if you want to pass "Authorization:accesskey"):</strong>
        <br>
           <input type="text" name="jci_oauth_bearer_access_key" value="<?php echo get_option('jci_oauth_bearer_access_key'); ?>" size="60"/>
           </td>
        </tr>
        <tr>
        	<td colspan="2">
          <strong>Send default Useragent (some APIs need that):</strong> <input type="checkbox" name="jci_http_header_default_useragent" value="1" <?php echo (get_option('jci_http_header_default_useragent') == 1)?"checked=checked":""; ?> />
            </td>
        </tr>
        <tr>
        	<td colspan="2">
          <strong>Switch off Gutenberg features (maybe a site builder needs that):</strong> <input type="checkbox" name="jci_gutenberg_off" value="1" <?php echo (get_option('jci_gutenberg_off') == 1)?"checked=checked":""; ?> />
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22" target="_blank">Don't forget: Feel free to donate! Easy via PayPal!</a> |
            <a href="https://json-content-importer.com/" target="_blank">Plugin Website json-content-importer.com: Documentation and examples</a>
            <hr>
            <strong>Available Syntax for Wordpress-Pages and -Blogentries:</strong>
          <p>
          <strong>[jsoncontentimporter</strong>
         <ul>
         <li>&nbsp;&nbsp;url="http://...json"</li>
         <li>&nbsp;&nbsp;urlgettimeout="number: who many seconds for loading url till timeout?"</li>
         <li>&nbsp;&nbsp;numberofdisplayeditems="number: how many items of level 1 should be displayed? display all: leave empty"</li>
         <li>&nbsp;&nbsp;basenode="starting point of datasets, tha base-node in the JSON-Feed where the data is?"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustbein="default empty, if not empty keywords spearated by ','. At least one of these keywords must be in the created text (here: text=code without html-tags)"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustbeindepth="default: 1, number:where in the JSON-tree oneofthesewordsmustbein must be?"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustnotbein="default empty, if not empty keywords spearated by ','. If one of these keywords is in the created text, this textblock is igonred (here: text=code without html-tags)"</li>
         <li>&nbsp;&nbsp;oneofthesewordsmustnotbeindepth="default: 1, number:where in the JSON-tree oneofthesewordsmustnotbein must be?"</li>
         <li>&nbsp;&nbsp;<strong>]</strong></li>
         <li>Any HTML-Code plus "basenode"-datafields wrapped in "{}"</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;{subloop:"basenode_subloop":"number of subloop-datasets to be displayed"}</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;Any HTML-Code plus "basenode_subloop"-datafields wrapped in "{}"</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{subloop-array:"basenode_subloop_array":"number of subloop-array-datasets to be displayed"}</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Any HTML-Code plus "basenode_subloop_array"-datafields wrapped in "{}"</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/subloop-array:"basenode_subloop_array"}</li>
         <li>&nbsp;&nbsp;&nbsp;&nbsp;{/subloop:"basenode_subloop"}</li>
         </ul>
          <strong>[/jsoncontentimporter]</strong>

         <hr>
         If the subloop is not an object but an array, use {subloop-array} e.g.:
         <br>
         {subloop-array:type:5}{1:ifNotEmptyAddRight:aa&lt;br&gt;bb}{2:ifNotEmptyAddLeft:AA}{3:ifNotEmptyAddRight:BB}{/subloop-array}
         <br>shows the first, second and third entry of that array, modified by ifNotEmptyAddLeft and ifNotEmptyAddRight.

         <hr>
          <strong>There are some special add-ons for datafields:</strong>
          <ul>
          <li>"{street:html}": Default-display of a datafield is NOT HTML: "&lt;" etc. are converted to "&amp,lt;". Add "html" to display the HTML-Code as Code.</li>
          <li>"{street:htmlAndLinefeed2htmlLinefeed}": Same as "{street:html}" plus Text-Linefeeds are converted to HTML-Linebreaks &lt;br&gt;.</li>
          <li>"{street:ifNotEmptyAddRight:extratext}": If datafield "street" is not empty, add "," right of datafield-value. allowed chars are: "a-zA-Z0-9,;_-:&lt;&gt;/ "</li>
          <li>"{street:html,ifNotEmptyAddRight:extratext}": you can combine "html" and "ifNotEmptyAdd..." like this</li>
          <li>"{street:ifNotEmptyAdd:extratext}": some as "ifNotEmptyAddRight"</li>
          <li>"{street:ifNotEmptyAddLeft:extratext}": If datafield "street" is not empty, add "," left of datafield-value. allowed chars are: "a-zA-Z0-9,;_-:&lt;&gt;/ "</li>
          <li>"{locationname:urlencode}": Insert the php-urlencoded value of the datafield "locationname". Needed when building URLs. "html" does not work here.</li>
          </ul>
         <hr>
         <strong>How do I find the proper template for my JSON?</strong>
         <br><a href="https://wordpress.org/support/plugin/json-content-importer" target="_blank">If you're lost: open ticket here</a>
         <p>
         Some Examples to illustrate syntax:<br>
          <strong>Example 1:</strong><br>
          <i>
         <?php
            $example = "[jsoncontentimporter ";
            $example .= "url=\"https://www.kux.de/extra/json/digimuc/location.php\" numberofdisplayeditems=\"30\" basenode=\"location\"]\n";
            $example .= "<ul><li>{locationid}\n";
            $example .= "{street:ifNotEmptyAdd:,} {zipcode} {cityname}\n";
            $example .= "<a href=\"https://duckduckgo.com/?q={locationname:urlencode}\">search duckduckgo</a>\n";
            $example .= "list of events at this location:\n";
            $example .= "{subloop:event:5}<a href=\"{eventlink}\">{eventname:ifNotEmptyAdd::} {eventstart}</a><br>{/subloop}<hr></li></ul>\n[/jsoncontentimporter]\n";
            $example = htmlentities($example);
            echo $example;
          ?> 
          </i>
          <hr>
          <strong>Example 2:</strong><br>
          <i>
          <?php
           $ex2 = "{subloop-array:aspects:10}{text:ifNotEmptyAdd:: }{subloop:aspects.image:10}  {retina} {id}<br>{/subloop:aspects.image}{/subloop-array:aspects}<br>";
           echo htmlentities($ex2);
          ?>
          </i>
          <hr>
          </td>
        </tr>
       <tr valign="top">
        <td colspan="2">
          <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22" target="_blank">Do you like that plugin? Is it helpful? I'm looking forward for a Donation - easy via PayPal!</a>
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
<a href="https://json-content-importer.com/?sc=wp" target="_blank"><img src="<?php echo plugins_url( 'images/icon-256x256.png', __FILE__ ); ?>" border="0" title="Learn more about JSON Content Importer PRO"></a>
				<h3>Upgrade to PRO Version!</h3>
				<p>When the free version comes to it's limits: Check out the PRO-Version!</p>
        <h3>PRO version only:</h3>
        <ul>
        <li>widget: display JSON-livedata in the sidebar</li>
        <li>template-manager: no hassle with linebreaks, and: one-place to store, use on many pages</li>
        <li>debug-mode</li>
        <li>format date and time</li>
        <li>application building: pass GET/POST-parameter from Wordpress to JSON-feed</li>
        <li>shortcode inside template</li>
        <li>twig template-engine: add logic (if...) to your template</li>
        <li>and much more...</li>
        </ul></p>
				<a href="https://json-content-importer.com/compare/?sc=wp" target="_blank" title="Learn more about JSON Content Importer PRO">Learn more about JSON Content Importer PRO</a>
			</div>
		<?php
	}
?>