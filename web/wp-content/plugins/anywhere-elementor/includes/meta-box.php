<?php


function add_ae_meta_box(){
    add_meta_box('ae-shortcode-box','Anywhere Elementor Usage','ae_shortcode_box','ae_global_templates','side','high');
}
add_action("add_meta_boxes", "add_ae_meta_box");


function ae_shortcode_box($post){
    ?>
    <h4 style="margin-bottom:5px;">Shortcode</h4>
    <input type='text' class='widefat' value='[INSERT_ELEMENTOR id="<?php echo $post->ID; ?>"]' readonly="">

    <h4 style="margin-bottom:5px;">Php Code</h4>
    <input type='text' class='widefat' value="&lt;?php echo do_shortcode('[INSERT_ELEMENTOR id=&quot;<?php echo $post->ID; ?>&quot;]'); ?&gt;" readonly="">
    <?php
}