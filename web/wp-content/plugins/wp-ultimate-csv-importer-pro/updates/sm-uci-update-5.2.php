<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
global $wpdb;

$wpdb->query("alter table {$wpdb->prefix}ultimate_csv_importer_mappingtemplate add column mapping_type varchar(30) after templateused;");
