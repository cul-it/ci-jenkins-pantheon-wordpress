<h4 class="sp-text-xl sp-mt-4 sp-mb-1"><?php esc_html_e( 'System Information', 'wpforms-lite' ); ?></h4>
    <textarea readonly="readonly" style="width: 100%; height: 500px"><?php echo seedprod_lite_get_system_info(); ?></textarea>

<?php 
if(!empty($_GET['sp-reset-cs']) && $_GET['sp-reset-cs'] == 1){
    update_option('seedprod_coming_soon_page_id', false);
}
if(!empty($_GET['sp-reset-mm']) && $_GET['sp-reset-mm'] == 1){
    update_option('seedprod_maintenance_mode_page_id', false);
}
if(!empty($_GET['sp-reset-p404']) && $_GET['sp-reset-p404'] == 1){
    update_option('seedprod_404_page_id', false);
}
?>