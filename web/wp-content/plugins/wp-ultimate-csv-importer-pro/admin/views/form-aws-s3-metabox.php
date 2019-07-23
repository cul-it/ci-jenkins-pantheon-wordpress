<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="uci_container" style="display:none;">
	<input id="ext_url" type="url" name="ext_url" placeholder="URL" style="width:50%;" />
	<a id="uci_preview" class="button" style="text-align:center;width:46%;display:inline-block;"><?php _e('Preview', 'wp-ultimate-csv-importer-pro') ?></a>
	<!--<input id="ext_alt" type="text" name="ext_alt" placeholder="Alt text" style="width:100%">-->
	<div style="width:100%;border:1px dotted #d1d1d1;min-height:20px;margin-top:8px;text-align:center;color:#d1d1d1;">
		<span id="ext_noimg"><?php _e('No image', 'wp-ultimate-csv-importer-pro'); ?></span>
		<img id="ext_img" style="max-width:100%;height:auto;" />
	</div>
	<a id="uci_remove" class="button" style="margin-top:4px;"><?php _e('Remove Image', 'wp-ultimate-csv-importer-pro') ?></a>
</div>

<script>

jQuery(document).ready(function($){

		$('#uci_container').show();
		<?php if ( ! $imgurl ): ?>
			$('#ext_img').attr('src','');
			$('#ext_noimg').show();
			//$('#ext_alt').hide().val('');
			$('#uci_remove').hide();
			$('#ext_url').show().val('');
			$('#uci_preview').show();
		<?php else: ?>
			$('#ext_img').attr('src',"<?php echo $img; ?>");
	    	$('#ext_noimg').hide();
	    //	$('#ext_alt').show().val("<?php echo $alt; ?>");
	    	$('#uci_remove').show();
	    	$('#ext_url').hide().val("<?php echo $img ?>");
	    	$('#uci_preview').hide();
		<?php endif; ?>

		// Preview
		$('#uci_preview').click(function(e){
			e.preventDefault();
			imgUrl = $('#ext_url').val();
			if ( imgUrl != '' ){
				$("<img>", { // Url validation
					    src: imgUrl,
					    error: function() {alert('<?php _e('Error URL Image', 'wp-ultimate-csv-importer-pro') ?>')},
					    load: function() {
						$('#ext_img').attr('src',imgUrl);
						$('#ext_noimg').hide();
						//$('#ext_alt').show();
						$('#uci_remove').show();
						$('#ext_url').hide();
						$('#uci_preview').hide();
					    }
				});
			} 
		}); 

		// Remove
		$('#uci_remove').click(function(e){
			e.preventDefault();
			$('#ext_img').attr('src','');
			$('#ext_noimg').show();
		    	//$('#ext_alt').hide().val('');
		    	$('#uci_remove').hide();
	    		$('#ext_url').show().val('');
		    	$('#uci_preview').show();
		}); 
});
</script>
