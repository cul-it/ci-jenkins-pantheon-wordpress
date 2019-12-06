<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

global $wpdb;
$wpdb->hide_errors();
$wpdb->query("alter table {$wpdb->prefix}ultimate_csv_importer_scheduled_import add column time_zone varchar(100) after duplicate_headers;");
$wpdb->query("alter table {$wpdb->prefix}ultimate_csv_importer_scheduled_export add column time_zone varchar(100) after updatedtime;");


