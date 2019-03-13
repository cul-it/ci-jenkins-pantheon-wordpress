<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class SmackUCIInstall {
	/**
	 * Install UCI Pro
	 */

	/** @var array DB updates that need to be run */
	private static $db_updates = array(
		'4.0.0' => 'updates/sm-uci-update-5.0.php',
		'4.1.0' => 'updates/sm-uci-update-5.0.php',
		'4.4.0' => 'updates/sm-uci-update-5.0.php',
		'4.5' => 'updates/sm-uci-update-5.0.php',
		'5.0' => 'updates/sm-uci-update-5.2.php',
		'5.1' => 'updates/sm-uci-update-5.2.php',
		'5.2' => 'updates/sm-uci-update-5.3.php',
		'5.3' => 'updates/sm-uci-update-5.3.php'
	);

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
	}

	/**
	 * Check WPUltimateCSVImporterPro version.
	 */
	public static function check_version() {
		if ( get_option( 'ULTIMATE_CSV_IMP_VERSION' ) != SmackUCI()->version )  {
			self::install();
			do_action( 'sm_uci_pro_updated' );
		}
	}

	/**
	 * Install actions when a update button is clicked.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_sm_uci_pro'] ) ) {
			self::update();
		}
	}

	/**
	 * Show notice stating update was successful.
	 */
	public static function updated_notice() {
		?>
		<div class='notice updated uci-message wc-connect is-dismissible'>
			<p><?php esc_html__( 'Ultimate CSV Importer PRO data update complete. Thank you for updating to the latest version!', 'wp-ultimate-csv-importer-pro' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Install WUCI.
	 */
	public static function install() {

		// Ensure needed classes are loaded
		//include_once( '../admin/class-wuci-admin-notices.php' );

		// Queue upgrades/setup wizard
		$current_uci_version    = get_option( 'ULTIMATE_CSV_IMP_VERSION', null );

		//self::init();
		// No versions? This is a new install :)
		if ( is_null( $current_uci_version ) && apply_filters( 'sm_uci_enable_setup_wizard', true ) ) {
			self::create_options();         // Create option data on the initial stage
			self::create_tables();          // Create tables on the fresh install
			self::create_files();           // Create needed files on the fresh installation
		} else {
			foreach ( self::$db_updates as $version => $updater ) {
				if ( version_compare( $version, $current_uci_version, '>=' ) ) {
					include_once ( SM_UCI_PRO_DIR . '/' . $updater );
					#self::update_db_version( $version );
				}
			}
			#self::update();
		}

		self::update_uci_version();

		// Flush rules after install
		flush_rewrite_rules();

		// Trigger action
		do_action( 'sm_uci_installed' );
	}

	/**
	 * Update UCI version to current.
	 */
	private static function update_uci_version() {
		delete_option( 'ULTIMATE_CSV_IMP_VERSION' );
		add_option( 'ULTIMATE_CSV_IMP_VERSION', SmackUCI()->version );
	}

	/**
	 * @param null $version
	 * Update DB version to current.
	 */
	private static function update_db_version( $version = null ) {
		delete_option( 'sm_uci_db_version' );
		add_option( 'sm_uci_db_version', is_null( $version ) ? SmackUCI()->version : $version );
	}

	/**
	 * Handle updates.
	 */
	private static function update() {
		$current_db_version = get_option( 'ULTIMATE_CSV_IMP_VERSION' );
		foreach ( self::$db_updates as $version => $updater ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				include_once ( $updater );
				self::update_db_version( $version );
			}
		}

		self::update_db_version();
	}

	/**
	 * Add more cron schedules.
	 * @param  array $schedules
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {
		return array(
			'wp_ultimate_csv_importer_scheduled_csv_data' => array(
				'interval' => 5, // seconds
				'display' => __('Check scheduled events on every second', SM_UCI_SLUG)
			),
			'wp_ultimate_csv_importer_scheduled_export_data' => array(
				'interval' => 5, // seconds
				'display' => __('Check scheduled events on every second', SM_UCI_SLUG)
			),
			'wp_ultimate_csv_importer_scheduled_images' => array(
				'interval' => 10, // seconds
				'display' => __('Schedule images on every second', SM_UCI_SLUG)
			),
			'wp_ultimate_csv_importer_scheduled_emails' => array(
				'interval' => 5, // seconds
				'display' => __('Schedule emails on every second', SM_UCI_SLUG)
			),
			'wp_ultimate_csv_importer_replace_inline_images' => array(
				'interval' => 5, // seconds
				'display' => __('Replace all inline images from post content', SM_UCI_SLUG)
			)
		);
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	public static function create_options() {

		// We assign the default option data for the fresh instalization
		$settings = array('debug_mode' => 'off',
		                  'send_log_email' => 'on',
		                  'drop_table' => 'off',
		                  'author_editor_access' => 'off',
		                  'woocomattr' => 'off'
		);

		add_option('sm_uci_pro_settings', $settings);

	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *              smack_csv_dashboard             - Table for storing pie chart information's
	 *              smack_dashboard_manager - Table for storing all line chart information's
	 *              smackcsv_status_log             - Table for storing all log statuses ( Inserted, Skipped & Updated )
	 *              smack_field_types               - Table for storing all registered custom fields
	 *              wp_ultimate_csv_importer_filemanager  - Table for storing all imported file details
	 *              wp_ultimate_csv_importer_multisite_details - Table for storing multi-site relational information's to the imported data
	 *              wp_ultimate_csv_importer_manageshortcodes       - Table for storing all short-code information's to the specific event
	 *              wp_ultimate_csv_importer_shortcodes_statusrel   - Table for storing all short-code relational status to the specific event
	 *              wp_ultimate_csv_importer_log_values      - Table for storing all log values to the specific events
	 *              wp_ultimate_csv_importer_eventkey_manager  - Table for storing all the event details
	 *              wp_ultimate_csv_importer_exclusion_lists  - Table for storing all exclusion for export feature
	 *              wp_ultimate_csv_importer_acf_fields             - Table for storing ACF custom fields information on the registration through our importer
	 *              wp_ultimate_csv_importer_mappingtemplate        - Table for storing all mapping template information's
	 *              wp_ultimate_csv_importer_scheduled_import       - Table for storing all scheduled events information's
	 *              wp_ultimate_csv_importer_ftp_schedules          - Table for storing all scheduled events through the FTP connection.
	 *              wp_ultimate_csv_importer_external_file_schedules        - Table for storing all scheduled events through the external files.
	 *              wp_ultimate_csv_importer_uploaded_file_schedules        - Table for storing all scheduled events based on the existing files.
	 *
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		foreach(self::get_schema() as $table) {
			dbDelta($table);
		}
		/** Insert all custom field controls of ACF,PODS and TYPES */
		self::CustomField_controls();
	}

	/**
	 * Set Custom Field Controls
	 */
	private static function CustomField_controls() {
		$acf_controls = array(
			'Basic' => array('Text','Text Area','Number','Email','Url','Password'),
			'Content' => array('Wysiwyg Editor','oEmbed','Image','File','Gallery'),
			'Choice' => array('Select','Checkbox','Radio Button','True/False'),
			'Relational' => array('Post Object','Page Link','Relationship','Taxonomy','User'),
			'jQuery' => array('Google Map','Date Picker','Color picker'),
			'Layout' => array('Message','Tab','Repeater','Flexible Content')
		);
		$pods_controls = array(
			'Text' => array('Plain Text','Website','Phone','Email','Password'),
			'Paragraph' => array('Plain Paragraph Text','WYSIWYG (Visual Editor)','Code (Syntax Highlighting)'),
			'Date/Time' => array('Date/Time','Date','Time'),
			'Number' => array('Plain Number','Currency'),
			'Relationships/Media' => array('File/Image/Video','Relationship'),
			'Other' => array('Yes/No','Color Picker')
		);
		$types_controls = array(
			'Text'=> array('Textfield','Textarea','Numeric','Phone','Email','Url'),
			'Content' => array('Wysiwyg','Embed','Image','File','Video','Skype'),
			'Choice' => array('Select','Checkbox','Checkboxes','Radio'),
			'jQuery' => array('Colorpicker','Date')
		);
		self::insert_CF_controls($acf_controls,'acf-field-type');
		self::insert_CF_controls($pods_controls,'pods-field-type');
		self::insert_CF_controls($types_controls,'types-field-type');
	}

	/**
	 * Insert Custom Field Controls
	 *
	 * @param $cf_controls
	 * @param $cf_type
	 */
	private static function insert_CF_controls($cf_controls,$cf_type) {
		global $wpdb;
		foreach($cf_controls as $cf_group => $cf_fields) {
			$cf_fields = serialize($cf_fields);
			$cf_insert = "insert into smack_field_types(choices,fieldType,groupType)select * from (select '$cf_fields','$cf_group','$cf_type')as tmp where not exists(select groupType from smack_field_types where groupType = '$cf_type' and fieldType = '$cf_group')";
			$wpdb->query($cf_insert);
		}
	}

	/**
	 * Table schema for Dashboards, Import Events, Scheduling Events, Logs, File Manager, Short-code & Exclusions.
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$tables = array(
			"CREATE TABLE IF NOT EXISTS `smack_field_types` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `choices` varchar(160) NOT NULL,
                        `fieldType` varchar(100) NOT NULL,
                        `groupType` varchar(100) NOT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_manageshortcodes` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `pID` int(20) DEFAULT NULL,
                        `shortcode` varchar(110) DEFAULT NULL,
                        `eventkey` varchar(60) DEFAULT NULL,
                        `mode_of_code` varchar(20) DEFAULT NULL,
                        `module` varchar(20) DEFAULT NULL,
                        `populate_status` int(5) DEFAULT '1',
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_shortcodes_statusrel` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `eventkey` varchar(60) DEFAULT NULL,
                        `shortcodes_count` int(20) DEFAULT NULL,
                        `shortcode_mode` varchar(20) DEFAULT NULL,
                        `current_status` varchar(20) DEFAULT 'Pending',
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS  `wp_ultimate_csv_importer_log_values` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `eventKey` varchar(50) NOT NULL,
                        `recordId` int(10) NOT NULL,
                        `module` varchar(50) NOT NULL,
                        `method_of_import` varchar(50) NOT NULL,
                        `log_message` blob NOT NULL,
                        `imported_time` varchar(100) NOT NULL,
                        `mode_of_import` varchar(100) NOT NULL,
                        `sequence` varchar(100) NOT NULL,
                        `status` varchar(100) NOT NULL,
                        `assigned_user_id` int(10) NOT NULL,
                        `imported_by` int(100) NOT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_acf_fields` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `groupId` varchar(100) NOT NULL,
                        `fieldId` varchar(100) NOT NULL,
                        `fieldLabel` varchar(100) NOT NULL,
                        `fieldName` varchar(100) NOT NULL,
                        `fieldType` varchar(60) NOT NULL,
                        `fdOption` varchar(100) DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_mappingtemplate` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `templatename` varchar(100) NOT NULL,
                        `mapping` blob NOT NULL,
                        `createdtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `deleted` int(1) DEFAULT '0',
                        `templateused` int(10) DEFAULT '0',
                        `mapping_type` varchar(30),
                        `module` varchar(50) DEFAULT NULL,
                        `csvname` varchar(50) DEFAULT NULL,
                        `eventKey` varchar(60) DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_scheduled_import` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `templateid` int(10) NOT NULL,
                        `importid` int(10) NOT NULL,
                        `createdtime` datetime NOT NULL,
                        `updatedtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        `isrun` int(1) DEFAULT '0',
                        `scheduledtimetorun` varchar(10) NOT NULL,
                        `scheduleddate` date NOT NULL,
                        `module` varchar(100) NOT NULL,
                        `file_type` varchar(10) NOT NULL,
                        `response` blob,
                        `version` varchar(10) DEFAULT NULL,
                        `event_key` varchar(100) DEFAULT NULL,
                        `importbymethod` varchar(60) DEFAULT NULL,
                        `import_limit` int(11) DEFAULT '1',
                        `import_row_ids` blob default NULL,
                        `frequency` int(5) DEFAULT '0',
                        `start_limit` int(11) DEFAULT '0',
                        `end_limit` int(11) DEFAULT '0',
                        `lastrun` datetime DEFAULT '0000-00-00 00:00:00',
                        `nexrun` datetime DEFAULT '0000-00-00 00:00:00',
                        `scheduled_by_user` varchar(10) DEFAULT '1',
                        `cron_status` varchar(15) DEFAULT NULL,
                        `import_mode` varchar(100) NOT NULL,
                        `duplicate_headers` blob DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_scheduled_export` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `module` varchar(100) NOT NULL,
                        `export_mode` varchar(20) NOT NULL,
                        `optionalType` varchar(100) NOT NULL,
                        `conditions` blob,
                        `exclusions` blob,
                        `file_name` varchar(100),
                        `isrun` int(1) DEFAULT '0',
                        `scheduleddate` date NOT NULL,
                        `frequency` int(5) DEFAULT '0',
                        `scheduledtimetorun` varchar(10) NOT NULL,
                        `host_name` varchar(120),
                        `host_port` int(5),
                        `host_username` varchar(160),
                        `host_password` varchar(160),
                        `host_path` varchar(300),
                        `file_type` varchar(10) NOT NULL,
                        `start_limit` int(11) DEFAULT '0',
                        `end_limit` int(11) DEFAULT '1000',
                        `lastrun` datetime DEFAULT '0000-00-00 00:00:00',
                        `nexrun` datetime DEFAULT '0000-00-00 00:00:00',
                        `scheduled_by_user` varchar(10) DEFAULT '1',
                        `cron_status` varchar(15) DEFAULT 'pending',
                        `custom_options` blob DEFAULT NULL,
                        `createdtime` datetime NOT NULL,
                        `updatedtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_ftp_schedules` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `schedule_id` int(10) NOT NULL,
                        `hostname` varchar(110) DEFAULT NULL,
                        `username` varchar(110) DEFAULT NULL,
                        `password` varchar(110) DEFAULT NULL,
                        `initial_path` varchar(225) DEFAULT NULL,
                        `filename` varchar(110) DEFAULT NULL,
                        `port_no` int(5) DEFAULT NULL,
                        `hosttype` varchar(110) DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_external_file_schedules` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `schedule_id` int(10) NOT NULL,
                        `file_url` varchar(255) DEFAULT NULL,
                        `filename` varchar(255) DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `wp_ultimate_csv_importer_uploaded_file_schedules` (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `schedule_id` int(10) NOT NULL,
                        `file_path` varchar(120) DEFAULT NULL,
                        PRIMARY KEY (`id`)
	                ) $collate;",
			"CREATE TABLE IF NOT EXISTS `smackuci_events` (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`revision` bigint(20) NOT NULL default 0,
						`name` varchar(255),
						`original_file_name` varchar(255),
						`friendly_name` varchar(255),
						`import_type` varchar(32),
						`filetype` text,
						`filepath` text,
						`eventKey` varchar(32),
						`registered_on` datetime NOT NULL default '0000-00-00 00:00:00',
						`parent_node` varchar(255),
						`processing` tinyint(1) NOT NULL default 0,
						`executing` tinyint(1) NOT NULL default 0,
						`triggered` tinyint(1) NOT NULL default 0,
						`event_started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`count` bigint(20) NOT NULL default 0,
						`processed` bigint(20) NOT NULL default 0,
						`created` bigint(20) NOT NULL default 0,
						`updated` bigint(20) NOT NULL default 0,
						`skipped` bigint(20) NOT NULL default 0,
						`deleted` bigint(20) NOT NULL default 0,
						`is_terminated` tinyint(1) NOT NULL default 0,
						`terminated_on` datetime NOT NULL default '0000-00-00 00:00:00',
						`last_activity` datetime NOT NULL default '0000-00-00 00:00:00',
						`siteid` int(11) NOT NULL DEFAULT 1,
						`month` varchar(60) DEFAULT NULL,
			                        `year` varchar(60) DEFAULT NULL,
						PRIMARY KEY ( id )
					) $collate;",
			"CREATE TABLE IF NOT EXISTS `smackuci_history` (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`event_id` bigint(20) NOT NULL,
						`time_taken` text,
						`date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`summary` text,
						PRIMARY KEY (id)
					) $collate;",
		);

		return $tables;
	}

	/**
	 * Todo: add PHP docs
	 */
	public static function remove_options() {
		delete_option('ULTIMATE_CSV_IMP_VERSION');
		delete_option('ULTIMATE_CSV_IMPORTER_UPGRADE_VERSION');
	}

	/**
	 * Create files/directories.
	 */
	private static function create_files() {
		// Install files and folders for uploading files and prevent hot linking

		$files = array(
			array(
				'base'          => SM_UCI_FILE_MANAGING_DIR,
				'file'          => '.htaccess',
				'content'       => 'deny from all'
			),
			array(
				'base'          => SM_UCI_FILE_MANAGING_DIR,
				'file'          => 'index.html',
				'content'       => ''
			),
			array(
				'base'          => SM_UCI_LOG_DIR,
				'file'          => '.htaccess',
				'content'       => 'deny from all'
			),
			array(
				'base'          => SM_UCI_LOG_DIR,
				'file'          => 'index.html',
				'content'       => ''
			)
			// array(
			// 	'base'          => SM_UCI_EXPORT_DIR,
			// 	'file'          => '.htaccess',
			// 	'content'       => 'deny from all'
			// ),
			// array(
			// 	'base'          => SM_UCI_EXPORT_DIR,
			// 	'file'          => 'index.html',
			// 	'content'       => ''
			// )
		);
		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				@chmod($file['base'], 0777);
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * @param $links
	 *
	 * @return array
	 */
	public function smack_uci_action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=sm-uci-settings') ) .'">Settings</a>';
		$links[] = '<a href="http://wp-buddy.com" target="_blank">More plugins by WP-Buddy</a>';
		return $links;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param       mixed $links Plugin Row Meta
	 * @param       mixed $file  Plugin Base file
	 * @return      array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == SM_UCI_PLUGIN_BASENAME ) {
			$row_meta = array(
				'settings' => '<a href="' . esc_url( apply_filters( 'sm_uci_settings_url', admin_url() . 'admin.php?page=sm-uci-settings' ) ) . '" title="' . esc_attr( __( 'Visit Plugin Settings', 'wp-ultimate-csv-importer-pro' ) ) . '" target="_blank">' . __( 'Settings', 'wp-ultimate-csv-importer-pro' ) . '</a>',
				'docs'     => '<a href="' . esc_url( apply_filters( 'sm_uci_docs_url', 'https://goo.gl/hyU5G1' ) ) . '" title="' . esc_attr( __( 'View WP Ultimate CSV Importer Pro Documentation', 'wp-ultimate-csv-importer-pro' ) ) . '" target="_blank">' . __( 'Docs', 'wp-ultimate-csv-importer-pro' ) . '</a>',
				'videos'   => '<a href="' . esc_url( apply_filters( 'sm_uci_videos_url', 'https://goo.gl/OgW9PJ' ) ) . '" title="' . esc_attr( __( 'View Videos for WP Ultimate CSV Importer Pro', 'wp-ultimate-csv-importer-pro' ) ) . '" target="_blank">' . __( 'Videos', 'wp-ultimate-csv-importer-pro' ) . '</a>',
				'support'  => '<a href="' . esc_url( apply_filters( 'sm_uci_support_url', admin_url() . 'admin.php?page=sm-uci-support' ) ) . '" title="' . esc_attr( __( 'Contact Support', 'wp-ultimate-csv-importer-pro' ) ) . '" target="_blank">' . __( 'Support', 'wp-ultimate-csv-importer-pro' ) . '</a>',
			);

			return array_merge( $row_meta, $links );
		}
	}

	public static function after_plugin_row_meta() {
		$response = wp_safe_remote_get('https://www.smackcoders.com/wp-versions/wp-ultimate-csv-importer.json');
		if ( is_wp_error( $response ) ) {
                           return false;
                }
		$response = json_decode($response['body']);
		$current_plugin_version = $GLOBALS['wp_ultimate_csv_importer_pro']->version;
		if($current_plugin_version != $response->version[0]) {
			echo '<tr class="active"><td colspan="3">';
			echo '<div class="update-message notice inline notice-warning notice-alt"><p>There is a new version of WP Ultimate CSV Importer Pro <b>[ version '. $response->version[0] .' ]</b> available. <a href="https://smackcoders.com/my-account.html" class="update-link" aria-label="Upgrade WP Ultimate CSV Importer Pro now"> Upgrade now</a>.</p></div>';
			echo '</td></tr>';
		}
	}

	public static function important_cron_notice() {
		$get_notice = get_option('smack_uci_cron_notice');
		if($get_notice != 'off' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'sm-uci-import') {
			?>
			<div class="notice notice-error wc-connect is-dismissible" onclick="dismiss_notices('cron_notice');" >
			<p style="margin-top: 10px">
			<strong><?php echo esc_html__( 'Notice:', 'wp-ultimate-csv-importer-pro' ); ?> </strong> <?php echo esc_html__( 'To populate Featured images, Please make sure that CRON is enabled in your server. ', 'wp-ultimate-csv-importer-pro' ); ?></p>
			</div>
			<?php
			if(function_exists( 'curl_version' ) == null || function_exists( 'curl_version' ) == '' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'sm-uci-import') { ?>
				<div class="notice notice-error">
					<p style="margin-top: 10px;">
						<strong><?php echo esc_html__( 'Notice:', 'wp-ultimate-csv-importer-pro' ); ?> </strong> <?php echo esc_html__( 'Please install CURL & enable it in your server. ', 'wp-ultimate-csv-importer-pro' ); ?>
					</p>
				</div>
			<?php }
		}
	}

	public static function wp_ultimate_csv_importer_notice() {
		$get_notice = get_option('smack_uci_upgrade_notice');
		$smack_uci_pages = array('sm-uci-import', 'sm-uci-dashboard', 'sm-uci-managers', 'sm-uci-export', 'sm-uci-settings', 'sm-uci-support');
		if($get_notice != 'off' && isset($_REQUEST['page']) && in_array($_REQUEST['page'], $smack_uci_pages)) {
			?>
			<div class='notice updated uci-message wc-connect is-dismissible' onclick="dismiss_notices('upgrade_notice');">
				<?php
				if ( get_option( 'ULTIMATE_CSV_IMP_VERSION' ) == 5.0 ) {
					?>
					<p><?php echo esc_html__( 'Ultimate CSV Importer PRO data update complete. Thank you for updating to the latest version!', 'wp_ultimate_csv_importer_pro' ); ?></p>
				<?php } ?>
				<p><?php echo esc_html__("If you love WP Ultimate CSV Importer show us you care with a 5-star review on","wp-ultimate-csv-importer-pro");?> <a href='https://wordpress.org/support/plugin/wp-ultimate-csv-importer/reviews/?rate=5#new-post' target='_blank'><?php echo esc_html__("wordpress.org!","wp-ultimate-csv-importer-pro");?></a>
				</p></div>
			<?php
		}
	}
}
