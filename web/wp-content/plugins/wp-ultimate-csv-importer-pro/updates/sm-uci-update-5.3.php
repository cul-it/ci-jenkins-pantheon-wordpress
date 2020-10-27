<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
global $wpdb;

$wpdb->hide_errors();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$collate = '';
if ( $wpdb->has_cap( 'collation' ) ) {
	if ( ! empty( $wpdb->charset ) ) {
		$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$collate .= " COLLATE $wpdb->collate";
	}
}

$tables = array("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ultimate_csv_importer_scheduled_export` (
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
	) $collate;");

	dbDelta($tables);

	$wpdb->query("alter table {$wpdb->prefix}ultimate_csv_importer_scheduled_import modify column `cron_status` varchar(15) DEFAULT 'pending'");
