<div id="seed_csp4_quicklinks">
    <div class="seed_csp4_quicklinks_menu_list">
        <div id="seed_csp4_q1" class="seed_csp4_quicklinks_menu_item_wrapper">
            <div class="seed_csp4_quicklinks_menu_item"><a href="https://wordpress.org/support/plugin/coming-soon" target="_blank">Support</a></div>
            <div class="seed_csp4_quicklinks_inner_item">
            <a href="https://wordpress.org/support/plugin/coming-soon" target="_blank"><i class="fas fa-life-ring fa-fw"></i></a>
            </div> 
        </div>   
        <div id="seed_csp4_q2" class="seed_csp4_quicklinks_menu_item_wrapper">
            <div class="seed_csp4_quicklinks_menu_item"><a href="https://www.facebook.com/wpbeginner/" target="_blank">Join our Community</a></div>
            <div class="seed_csp4_quicklinks_inner_item">
            <a href="https://www.facebook.com/wpbeginner/" target="_blank"><i class="fab fa-wpbeginner fa-fw"></i></a>
            </div> 
        </div>  
        <div id="seed_csp4_q3" class="seed_csp4_quicklinks_menu_item_wrapper">
            <div class="seed_csp4_quicklinks_menu_item"><a href="https://www.seedprod.com/suggest-a-feature/" target="_blank">Suggest a Feature</a></div>
            <div class="seed_csp4_quicklinks_inner_item">
            <a href="https://www.seedprod.com/suggest-a-feature/" target="_blank"><i class="fas fa-lightbulb fa-fw"></i></a>
            </div> 
        </div>  
        <div id="seed_csp4_q4" class="seed_csp4_quicklinks_menu_item_wrapper">
            <div class="seed_csp4_quicklinks_menu_item"><a href="https://www.seedprod.com/ultimate-coming-soon-page-vs-coming-soon-pro/?utm_source=WordPress&utm_medium=quicklinks&utm_campaign=liteplugin" target="_blank">Upgrade to Pro &raquo;</a> </div>
            <div class="seed_csp4_quicklinks_inner_item">
            <a href="https://www.seedprod.com/ultimate-coming-soon-page-vs-coming-soon-pro/?utm_source=WordPress&utm_medium=quicklinks&utm_campaign=liteplugin" target="_blank"><i class="fas fa-shopping-cart fa-fw"></i></a>
            </div> 
        </div>   
    </div>
    <div class="seed_csp4_quicklinks_base">
        <div class="seed_csp4_quicklinks_menu">See Quick Links</div>
        <div class="seed_csp4_quicklinks_inner">
        <img  src="<?php echo SEED_CSP4_PLUGIN_URL ?>public/images/SeedProd-logo 2.png" />
        </div>
    </div>
</div>
<script>
jQuery( document ).ready(function($) {
    $( ".seed_csp4_quicklinks_base" ).click(function() {
        $( "#seed_csp4_quicklinks" ).toggleClass( 'active' );
        
    });
    $( "#seed_csp4_q1" ).click(function(event) {
        event.preventDefault();
        window.open('https://wordpress.org/support/plugin/coming-soon', '_blank');
    });
    $( "#seed_csp4_q2" ).click(function(event) {
        event.preventDefault();
        window.open('https://www.facebook.com/wpbeginner/', '_blank');
    });
    $( "#seed_csp4_q3" ).click(function(event) {
        event.preventDefault();
        window.open('https://www.seedprod.com/suggest-a-feature/', '_blank');
    });
    $( "#seed_csp4_q4" ).click(function(event) {
        event.preventDefault();
        window.open('https://www.seedprod.com/ultimate-coming-soon-page-vs-coming-soon-pro/?utm_source=WordPress&utm_medium=quicklinks&utm_campaign=liteplugin', '_blank');
    });

});
</script>


