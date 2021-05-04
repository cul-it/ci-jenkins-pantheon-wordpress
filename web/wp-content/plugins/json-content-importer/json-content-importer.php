<?php
/*
Plugin Name: JSON Content Importer
Plugin URI: https://json-content-importer.com/
Description: Plugin to import, cache and display a JSON-Feed. Display is done with wordpress-shortcode or gutenberg-block.
Version: 1.3.12
Author: Bernhard Kux
Author URI: https://json-content-importer.com/
Text Domain: json-content-importer
Domain Path: /languages/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/* block direct requests */
if ( !function_exists( 'add_action' ) ) {
	_e('Hello, this is a plugin: You must not call me directly.', 'json-content-importer');
	exit;
}
defined('ABSPATH') OR exit;

#$requri = $_SERVER["REQUEST_URI"];
#if (preg_match("/wp\-json/", $requri)) {
#  return "";
#}

function jci_i18n_init() {
	$pd = dirname(
		plugin_basename(__FILE__)
	).'/languages/';
	load_plugin_textdomain('json-content-importer', false, $pd);
}
add_action('plugins_loaded', 'jci_i18n_init');

class jciGutenberg {
	private $gutenbergIsActive = FALSE;
	private $gutenbergPluginIsActive = FALSE;
	private $itIsWP5 = FALSE;
	private $gutenbergMessage = ""; 

	function __construct()
    {
		$this->buildGutenbergMessage("#f00", __("Gutenberg not available", 'json-content-importer'));
		$this->checkGutenbergIsActive();
    }	
	
	private function checkGutenbergIsActive()
	{
		@$jci_gutenberg_off_option_value = @get_option('jci_gutenberg_off');
		if (1==$jci_gutenberg_off_option_value) {
			#$this->gutenbergMessage = "Gutenberg-Mode of Plugin switched of in Options";
			$this->buildGutenbergMessage("#f00", __("Gutenberg-Mode of Plugin switched off in Options", 'json-content-importer'));
			#return TRUE;
		} else {
			# previous to 5.0 the constant GUTENBERG_VERSION indicates, that the Gutenberg-Plugin is active
			$this->gutenbergPluginIsActive = (true === defined('GUTENBERG_VERSION'));
			if ($this->gutenbergPluginIsActive) {
				$this->gutenbergIsActive = TRUE;
				$this->buildGutenbergMessage("#3db634", __('Gutenberg-Plugin-Mode', 'json-content-importer'));
			}
			# things change from 5.0 on
			$this->itIsWP5 = version_compare(get_bloginfo('version'),'5.','>='); # ????? 5. // 5.0
			if ($this->itIsWP5) {
				# maybe the classic editor plugin is active in wp 5.0
				
				
				#if ( ! function_exists( 'is_plugin_active' ) ) {
				#	include_once ABSPATH . 'wp-admin/includes/plugin.php';
				#}
				if ( class_exists( 'Classic_Editor' ) ) {
				#if (is_plugin_active( 'classic-editor/classic-editor.php' )) {
					$this->buildGutenbergMessage("#f00", __('No Gutenberg: Classic Editor Plugin active', 'json-content-importer'));
				} else {
					$this->gutenbergIsActive = TRUE;
					$this->buildGutenbergMessage("#3db634", __('Gutenberg-WP5-Mode', 'json-content-importer'));
				}
			}
		}
		define( 'JCI_GUTENBERG_PLUGIN_MESSAGE', $this->gutenbergMessage );
		
	}

	public function getGutenbergIsActive()
	{
		return $this->gutenbergIsActive;
	}

	private function buildGutenbergMessage($color, $message)
	{
		$this->gutenbergMessage = '<a style="color:'.$color.'; font-weight: bold;" href="https://wordpress.org/gutenberg/" target="_blank">'.$message.'</a>';
	}
}

if (!isset($jciGB)) {
	$jciGB = new jciGutenberg();
}


if ( $jciGB->getGutenbergIsActive() ) {
	define( 'JCI_FREE_BLOCK_VERSION', '0.1' );
	if ( ! defined( 'JCI_FREE_BLOCK_NAME' ) ) {
		define( 'JCI_FREE_BLOCK_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
	}
	if ( ! defined( 'JCI_FREE_BLOCK_DIR' ) ) {
		define( 'JCI_FREE_BLOCK_DIR', WP_PLUGIN_DIR . '/' . JCI_FREE_BLOCK_NAME );
	}
	if ( ! defined( 'JCI_FREE_BLOCK_URL' ) ) {
		define( 'JCI_FREE_BLOCK_URL', WP_PLUGIN_URL . '/' . JCI_FREE_BLOCK_NAME );
	}
	require_once( JCI_FREE_BLOCK_DIR . '/block/index.php' );
 
}


// add Quicktag to Text Editor
function jcifree_add_quicktags() {
	if ( wp_script_is( 'quicktags' ) ) { 
		$jsonexample = plugin_dir_url( __FILE__ )."json/gutenbergblockexample1.json";
		$template = "{start}<br>{subloop-array:level2:-1}{level2.key}<br>{subloop:level2.data:-1}id: {level2.data.id}<br>{/subloop:level2.data}{/subloop-array:level2}";
		?>
		<script type="text/javascript">
			QTags.addButton( 'jcifreequicktag', 'JSON Content Importer', '[jsoncontentimporter url=<?php echo $jsonexample; ?> debugmode=10 basenode=level1]<?php echo $template; ?>[/jsoncontentimporter]', '', '', '', 1 );
		</script>
	<?php }

}
add_action( 'admin_print_footer_scripts', 'jcifree_add_quicktags' );

if (!function_exists('jci_addlinks')) {
	function jci_addlinks($links, $file) {
		if ( strpos( $file, 'json-content-importer.php' ) !== false ) {
			$gbmsg = "";
			if ( defined( 'JCI_GUTENBERG_PLUGIN_MESSAGE' ) ) {
				$gbmsg = JCI_GUTENBERG_PLUGIN_MESSAGE;
			}
			$link2pro = array(
				$gbmsg,
				'<a style="color:#3db634; font-weight: bold;" href="https://json-content-importer.com/welcome-to-the-home-of-the-json-content-importer-plugin/" target="_blank">'.__('Upgrade to PRO-Version', 'json-content-importer').'</a>',
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22" title="'.__('Support the development', 'json-content-importer').'" target="_blank">'.__('Donate', 'json-content-importer').'</a>'
			);
			return array_merge( $links, $link2pro);
		}
		return $links;
	}
	add_filter( 'plugin_row_meta', 'jci_addlinks', 10, 2 );
}

if(!class_exists('JsonContentImporter')){
	require_once plugin_dir_path( __FILE__ ) . '/class-json-content-importer.php';
}
require_once plugin_dir_path( __FILE__ ) . '/options.php';
$JsonContentImporter = new JsonContentImporter();


/* extension hook BEGIN */
do_action('json_content_importer_extension');
/* extension hook END */
?>