<?php
/*
 * : JCI FREE 20201225
 */
 
add_action( 'init', 'jsoncontentimporterGutenbergBlock' );

function checkCacheFolder($cacheBaseFolder, $cacheFolder) {
	# wp version 4.4.2 and later: "/cache" is not created at install, so the plugin has to check and create...
	 if (!is_dir($cacheBaseFolder)) {
	   $mkdirError = @mkdir($cacheBaseFolder);
	   if (!$mkdirError) {
		 # mkdir failed, usually due to missing write-permissions
		 $errormsg .= "<hr><b>".__('caching not working, plugin aborted', 'json-content-importer').":</b><br>";
		 $errormsg .= __("plugin / wordpress / webserver can't create", 'json-content-importer')."<br><i>".$cacheBaseFolder."</i><br>";
		 $errormsg .= __('therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)', 'json-content-importer')."<hr>";
		 # abort: no caching possible
		 return $errormsg;
	   }
	 }

	 if (!is_dir($cacheFolder)) {
	   # $this->cacheFolder is no dir: not existing
	   # try to create $this->cacheFolder
	   $mkdirError = @mkdir($cacheFolder);
	   if (!$mkdirError) {
		 # mkdir failed, usually due to missing write-permissions
		 $errormsg .= "<hr><b>".__('caching not working, plugin aborted', 'json-content-importer').":</b><br>";
		 $errormsg .= __("plugin / wordpress / webserver can't create", 'json-content-importer')."<br><i>".$cacheFolder."</i><br>";
		 $errormsg .= __('therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)', 'json-content-importer')."<hr>";
		 # abort: no caching possible
		 return $errormsg;
	   }
	 }
	 # $this->cacheFolder writeable?
	 if (!is_writeable($cacheFolder)) {
	   $errormsg .= __('please check cacheFolder', 'json-content-importer').":<br>".$cacheFolder."<br>".__('is not writable. Please change permissions.', 'json-content-importer');
	   #exit;
	   return $errormsg;
	 }
 }

	 function addShortcodeParam($key, $value) {
		 if (trim($value)=="") {
			 return "";
		 }
		 $asc = $key.'='.$value;
		 $asc .= " ";
		 return $asc;
	 }


 function jci_free_render( $attributes, $content ) {
	 $out = "";
	 #$out .= "att: ".$attributes{'apiURL'}."<br>";
	 #return $out;
	 
	 $debugmode = 0;
	 $debugmsg = '';
	 $example_out_text = '';
	 $example_url = '/json-content-importer/json/gutenbergblockexample1.json';
	 if (""==$attributes['template']) {
		 $exampleTemplate = 'start: {start}<br>
			 {subloop-array:level2:-1}
			 level2: {level2.key}
			 <br>{subloop:level2.data:-1}
			 id: {level2.data.id}, type: {level2.data.type}<br>
			 {/subloop:level2.data}
			 {/subloop-array:level2}
		 ';
		 $exampleTemplate = str_replace ( "{" , "&#123;" ,$exampleTemplate);
		 $exampleTemplate = str_replace ( "}" , "&#125;" ,$exampleTemplate);
		 $exampleTemplate = str_replace ( "<" , "&lt;" ,$exampleTemplate);
		 $exampleTemplate = str_replace ( ">" , "&gt;" ,$exampleTemplate);
		 $attributes['template'] = '<b>'.__('Empty template: Add some text!', 'json-content-importer').'</b><br>
			 '.__('For the example copypaste this to the right Template box', 'json-content-importer').':<br>'.$exampleTemplate;			
	 }
	 if ('e1'==trim($attributes['apiURL'])) {
		 $attributes['apiURL'] = $example_url;
	 }
	 if ($example_url==trim($attributes['apiURL'])) {
		 $attributes['apiURL'] = WP_PLUGIN_URL.$example_url;
		 $example_out_text = __('Welcome!', 'json-content-importer').'<br><b>'.__('Click on this block', 'json-content-importer').'</b> '.__('and you see on the right side  the Gutenberg-Block settings of the "JSON Content Importer Gutenberg Block". Yet there is an example to show how it works. The Example-URL is', 'json-content-importer').'<br><a href="'.$attributes['apiURL'].'" target="_blank">'.$attributes['apiURL'].'</a><br> 
	 '.__('Some settings show you how the JSON-parser and display works.
	 The example-template is (try to change it on the right)', 'json-content-importer').':<br><code>'.htmlentities($attributes['template']).'</code><hr>'.__('The result of combining JSON and this template gives us the output. Use this example to experiment', 'json-content-importer').': <b>'.__('Type "level1" in the right basenode-field, please. This will change the output as now the JSON and the template fit together (without not...)', 'json-content-importer').'</b>
	 <br>'.__('You may also open the lower right "<b>JCI Advanced</b>"-section. Insert at "One of these words must be displayed:" the word "bb". And at "JSON-depth of the above displayed Words:" the number 3. Do you see the difference at once?', 'json-content-importer').'
	 <hr>';
	 } 
	 
	 if (1!=trim($attributes['toggleswitchexample'])) {
		 $example_out_text = '';
	 }
	 
	 @$oneofthesewordsmustbeindepth = checkIntAttrib($attributes['oneofthesewordsmustbeindepth'], "");
	 @$oneofthesewordsmustnotbeindepth = checkIntAttrib($attributes['oneofthesewordsmustnotbeindepth'], "");
	 @$oneofthesewordsmustbein = $attributes['oneofthesewordsmustbein'];
	 @$oneofthesewordsmustnotbein = $attributes['oneofthesewordsmustnotbein'];		
	 @$basenode = $attributes['basenode'];

	 @$debugmode = 0;
	 if (1==$attributes['toggleswitch']) {
		 $debugmode = 10;
	 }
	 
	 @$urlgettimeout = checkIntAttrib($attributes['urlgettimeout'], 5);
	 @$numberofdisplayeditems = checkIntAttrib($attributes['numberofdisplayeditems'], -1);

	 ###############################################################
	 ###############################################################
	 ###############################################################
	 ### the magic begins
	 
	 # get
	 $feedUrl = $attributes['apiURL'];

	 ## plugin-option BEGIN
	 $cacheEnable = FALSE;
	 $cacheFolder = WP_CONTENT_DIR.'/cache/jsoncontentimporter/';
	 $cacheFile = $cacheFolder . sanitize_file_name(md5($feedUrl)) . ".cgi";  # cache json-feed
	 $pluginOption_cacheStatus = get_option('jci_enable_cache');
	 $cacheExpireTime = 0;
	 $out_pluginSettings = "<b>".__('Plugin-Settings (see Plugin options)', 'json-content-importer').":</b><br>";

	 if (1==$pluginOption_cacheStatus) {
		 # 1 = checkbox "enable cache" activ
		 $cacheEnable = TRUE;
		 # check cacheFolder
		 checkCacheFolder(WP_CONTENT_DIR.'/cache/', $cacheFolder);

		 $pluginOption_cacheTime = get_option('jci_cache_time'); 
		 $pluginOption_cacheTimeFormat = get_option('jci_cache_time_format');
		 $cacheExpireTime = strtotime(date('Y-m-d H:i:s'  , strtotime(" -".$pluginOption_cacheTime." " . $pluginOption_cacheTimeFormat )));
		 $out_pluginSettings .= "Cache: ".__('active', 'json-content-importer')."<br>";
		 $out_pluginSettings .= __('Cachetime', 'json-content-importer').": $pluginOption_cacheTime $pluginOption_cacheTimeFormat<br>";
		 $out_pluginSettings .= __('Cachefolder', 'json-content-importer').": $cacheFolder<br>";
	 } else {
		 $out_pluginSettings .= "Cache: ".__('disabled', 'json-content-importer')."<br>";
	 }
	 

   
	 $pluginOption_oauthBearerAccessKey = get_option('jci_oauth_bearer_access_key');
	 $out_pluginSettings .= __('oauth Bearer Accesskey', 'json-content-importer').": $pluginOption_oauthBearerAccessKey<br>";
	 $pluginOption_httpHeaderDefaultUseragentFlag = get_option('jci_http_header_default_useragent');
	 $out_pluginSettings .= __('http-Header default Useragent', 'json-content-importer').": $pluginOption_httpHeaderDefaultUseragentFlag<br>";
	 ## plugin-option END

	 #$out = "att: ".$attributes['apiURL']."<br>";
	 $out .= $example_out_text;
	 
	 if(!class_exists('FileLoadWithCache')){
		 require_once plugin_dir_path( __FILE__ ) . '../class-fileload-cache.php';
	 }
	 
	 $pluginOption_jci_api_errorhandling = get_option('jci_api_errorhandling');
		 if (empty($pluginOption_jci_api_errorhandling)) {
			 update_option('jci_api_errorhandling', 0);
		 }

	 $fileLoadWithCacheObj = new FileLoadWithCache($feedUrl, $urlgettimeout, $cacheEnable, $cacheFile,
		 $cacheExpireTime, $pluginOption_oauthBearerAccessKey, $pluginOption_httpHeaderDefaultUseragentFlag, $debugmode, $pluginOption_jci_api_errorhandling);
	 $fileLoadWithCacheObj->retrieveJsonData();
	 $feedData = $fileLoadWithCacheObj->getFeeddata();
	 $debugmsg .= $fileLoadWithCacheObj->getdebugmessage();

	 if (""==$feedData) {
		 $errormsg = __('EMPTY api-answer: No JSON received - is the API down?', 'json-content-importer').'<br><b>'.__('Check the API-URL in the Block-Settings, please.', 'json-content-importer').'</b><br>
		 Use "/json-content-importer/json/gutenbergblockexample1.json" '.__('as example. You might switch on the debugmode on the right side.', 'json-content-importer');
		 
		 $rdamtmp = $debugmsg;
		   return $debugmsg.$errormsg;
	 }
	 
	 # template
	 $datastructure = $attributes['template'];
	 
	 ## shortcode builder BEGIN
	 $shortcodeData = '[jsoncontentimporter url='.$feedUrl.' ';
	 $shortcodeData .= addShortcodeParam('numberofdisplayeditems', $numberofdisplayeditems);
	 $shortcodeData .= addShortcodeParam('basenode', $basenode);
	 $shortcodeData .= addShortcodeParam('urlgettimeout', $urlgettimeout);
	 $shortcodeData .= addShortcodeParam('debugmode', $debugmode);
	 $shortcodeData .= addShortcodeParam('oneofthesewordsmustbein', $oneofthesewordsmustbein);
	 $shortcodeData .= addShortcodeParam('oneofthesewordsmustbeindepth', $oneofthesewordsmustbeindepth);
	 $shortcodeData .= addShortcodeParam('oneofthesewordsmustnotbein', $oneofthesewordsmustnotbein);
	 $shortcodeData .= addShortcodeParam('oneofthesewordsmustnotbeindepth', $oneofthesewordsmustnotbeindepth);
	 $shortcodeData .= ']'.$datastructure.'[/jsoncontentimporter]';
	 $debugmsg .= add2Debug($debugmode, 
		 buildDebugTextarea(
			 __('If you use Wordpress without Gutenberg, you can use this Shortcode (copy with Strg+A, paste to TEXT-editor!)', 'json-content-importer').":", 
			 $shortcodeData));
	 ## shortcode builder END
	 
	 $inspurl = "https://jsoneditoronline.org";
	 $debugmsg .= add2Debug($debugmode, 
		 buildDebugTextarea(__('api-answer', 'json-content-importer').":<br>".__('Inspect JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to', 'json-content-importer')." <a href=\"".$inspurl."\" target=_blank>https://jsoneditoronline.org</a>):", $feedData));

	 $jsonDecodeObj = new JSONdecode($feedData);
	 $jsonObj = $jsonDecodeObj->getJsondata();

	 # debug info template
	 $debugmsg .= add2Debug($debugmode, buildDebugTextarea(__('template', 'json-content-importer').":", $datastructure));

	 # parse
	 if(!class_exists('JsonContentParser123gb')){
		 require_once plugin_dir_path( __FILE__ ) . '../class-json-parser.php';
	 }	
	 $JsonContentParser = new JsonContentParser123($jsonObj, $datastructure, $basenode, $numberofdisplayeditems,
		 $oneofthesewordsmustbein, $oneofthesewordsmustbeindepth,
		 $oneofthesewordsmustnotbein, $oneofthesewordsmustnotbeindepth);
 
	 $rdam = $JsonContentParser->retrieveDataAndBuildAllHtmlItems();
	 
	 $outdata = htmlentities($rdam);
	 $debugmsg .= add2Debug($debugmode, buildDebugTextarea(__('result', 'json-content-importer').":", $outdata));
	 
	 #$parseMsg = $JsonContentParser->getErrorDebugMsg();
	 #$debugmsg .= add2Debug($debugmode, $parseMsg);
	 if (""==$rdam) {
		 $debugmsg .= "\n".add2Debug($debugmode, __('result of parsing is empty: no data to be displayed.', 'json-content-importer')."<br>".__('Check JSON and template, please.', 'json-content-importer')); # the starting linefeed is needed, otherwise the gutenberg-block has problems with empty blocks...
		 $out .= $debugmsg;
		 return $out;
	 }
	 $debugmsg .= add2Debug($debugmode, $out_pluginSettings."<hr><b>".__('the real result', 'json-content-importer').":</b><br>");
	 $out .= $debugmsg."\n".$rdam;

	 ## the magic ends ;-)
	 ###############################################################
	 ###############################################################
	 ###############################################################

	 return $out;
 }

function checkIntAttrib($value, $defaultvalue) {
	 $ret = $defaultvalue;
	 if (""!=$value) {
		 $valuetmp = $value;
		 if (is_numeric($valuetmp)) {
			 $ret = round($valuetmp);
		 }
	 }
	 return $ret;
 }

 function add2Debug($debugmode, $message) {
	 if ($debugmode>0) {
		 return "<br>".$message;
	 }
	 return '';
 }


 function buildDebugTextarea($message, $txt, $addline=FALSE) {
	 $norowsmax = 20;
	 $norows = $norowsmax; 
	 $strlentmp = round(strlen($txt)/90);
	 if ($strlentmp<20) {
	   $norows = $strlentmp;
	 }
	 $nooflines = substr_count($txt, "\n");
	 if ($nooflines > $norows) {
	   $norows = $nooflines;
	 }
	 if ($norows > $norowsmax) {
	   $norows = $norowsmax;
	 }
	 $norows = $norows + 2;
	 $out = $message."<br><textarea rows=".$norows." cols=90>".$txt."</textarea>";
	 if ($addline) {
	   $out .= "<hr>";
	 }
	 return $out;
 }

function jsoncontentimporterGutenbergBlock() {

	wp_register_script(
		'jcifree-block-script', 
		plugins_url( 'jcifree-block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor', 'wp-components'),
		filemtime( plugin_dir_path(__FILE__).'jcifree-block.js')
	);
	if (is_admin()) {
		wp_enqueue_script('jcifree-block-script');
	}
	$langpath = plugin_dir_path( __FILE__ ) . '../languages/' ;
	wp_set_script_translations( 'jcifree-block-script', 'json-content-importer', $langpath );
	load_plugin_textdomain('json-content-importer', false, $langpath);
	

	register_block_type( 'jci/jcifree-block-script', 
		array(
			'render_callback' => 'jci_free_render',
			'attributes'	  => array(
				'apiURL'	 => array(
					'type' => 'string',
					'default' => '/json-content-importer/json/gutenbergblockexample1.json',
				),
				'template'	 => array(
					'type' => 'string',
					'default' => 'start: {start}<br>{subloop-array:level2:-1}level2: {level2.key}<br>{subloop:level2.data:-1}id: {level2.data.id}, type: {level2.data.type}<br>{/subloop:level2.data}{/subloop-array:level2}',
				),
				'basenode'	 => array(
					'type' => 'string',
					'default' => '',
				),
				//'noitems'	 => array(
				//	'type' => 'number',
				//),
				'toggleswitch'	 => array(
					'type' => 'boolean',
					'default' => false,
				),
				'toggleswitchexample'	 => array(
					'type' => 'boolean',
					'default' => true,
				),
				//'m1'	 => array(
				//	'type' => 'string',
				//),
				'urlgettimeout'	 => array(
					'type' => 'string',
					'default' => '5',
				),
				'numberofdisplayeditems'	 => array(
					'type' => 'string',
				),
				'oneofthesewordsmustbein'	 => array(
					'type' => 'string',
				),
				'oneofthesewordsmustbeindepth'	 => array(
					'type' => 'string',
				),
				'oneofthesewordsmustnotbein'	 => array(
					'type' => 'string',
				),
				'oneofthesewordsmustnotbeindepth'	 => array(
					'type' => 'string',
				),
			),
		)
	);
}

?>
