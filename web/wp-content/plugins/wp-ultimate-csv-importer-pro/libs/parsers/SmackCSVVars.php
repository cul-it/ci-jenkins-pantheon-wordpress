<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
class SmackCSVVars extends SmackCSVLogger
{

    #Plugin information
    #var $version = '4.5';
    var $plugin_name = "Ultimate CSV Importer PRO";

    var $plugin_slug = "wp-ultimate-csv-importer-pro";

    # default delimiter (comma) and default enclosure (double quote)
    var $delimiter = ',';
    var $enclosure = '"';
    var $escape = "\\";

    # number of rows to ignore from beginning of data
    var $offset = 2;

    # limits the number of returned rows to specified amount
    var $limit = 2;

    # preferred delimiter characters
    var $delimiters = array(
        ';'  => 0,
        ','  => 0,
        "\t" => 0,
        "|"  => 0,
        ":"  => 0,
        "^"  => 0,
    );

    # current file
    var $file;

    # loaded file contents
    var $csvfile_data;

    # current CSV header data
    var $csvfile_header;

    #Logger configuration
    var $log_file = SM_UCI_DEBUG_LOG;

    #String status - "INFO"/"DEBUG"/"ERROR"/"WARNING"/"ALL"/"NONE"
    var $log_status = "ALL";

    #total row count
    var $total_row_cont;

    #XML string
    var $xmlstring;

    var $integrations = array();

    var $plugin_basename;

    var $plugin_location;

    var $plugin_logfile_location;

    var $uploaded_file_location;

    var $exported_file_location;

    var $zip_file_location;

    var $screenData = array();

    // @var string CSV upload directory name
    var $uploadDir = 'imports';

    // @var Export CSV directory name
    var $exportDir = 'exports';

    // @var string ZIP handle dir  directory name
    var $zipDir = 'zip_files';

    // @var string CSV Log directory name
    var $logDir = 'import_logs';

    // @var string event screen data storage location
    var $screenDataDir = 'screens_data';

    #Using WP session variable for development purposes
    #TODO:Remove this when implementing in WordPress
    var $wp_session;

    #var $smack_uci_globals = array();
}