<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
       exit; // Exit if accessed directly
?>
<div class="wp_ultimate_csv_importer_pro">
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            <div class="col-md-6"><a href="https://goo.gl/jdPMW8" target="_blank"><?php echo esc_html__('Documentation','wp-ultimate-csv-importer-pro');?></a></div>
            <div class="col-md-6"><a href="https://goo.gl/fKvDxH" target="_blank"><?php echo esc_html__('Sample CSV','wp-ultimate-csv-importer-pro');?></a></div>
         </div>
<div class="box-one">
    <div class="top-right-box">
        <h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(SM_UCI_PRO_URL . '/assets/images/chart_bar.png');?>" /></span><?php echo __('Importers Activity','wp-ultimate-csv-importer-pro'); ?></h3>
        <div class="top-right-content">
            <div id='dispLabel'></div>
            <canvas id="uci-line-chart"></canvas>
            <!-- <div class='lineStats' id='lineStats' style='height: 250px;width:100%;margin-top:15px; margin-bottom:15px;'></div> -->
        </div>
    </div>
    <div class="top-right-box">
        <h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(SM_UCI_PRO_URL . '/assets/images/stat_icon.png');?>"></span><?php echo __('Import Statistics','wp-ultimate-csv-importer-pro'); ?></h3>
        <div class="top-left-content">
            <div id='dispLabel'></div>
            <!-- <div id="canvas-holder" style="width:50%; float: left;">
                <canvas id="uci-pie-chart"></canvas>
            </div> -->
            <div id="canvas-holder" style="width:100%;">
                <canvas id="uci-bar-stacked-chart"></canvas>
            </div>
            <!-- <div class='pieStats' id='pieStats' style='float:left;height:250px;width:100%;margin-top:15px;margin-bottom:15px;'></div> -->
        </div>
    </div>

    <div style="width:75%;">

    </div>
    
</div>
</div>
<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=pro_csv_importer" target="blank">Smackcoders</a>.</div>
