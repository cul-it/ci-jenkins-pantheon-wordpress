<?php
/**
 * Created by PhpStorm.
 * User: sujin
 * Date: 06/03/17
 * Time: 5:34 PM
 */

global $wpdb;

$wpdb->query("alter table wp_ultimate_csv_importer_mappingtemplate add column mapping_type varchar(30) after templateused;");
