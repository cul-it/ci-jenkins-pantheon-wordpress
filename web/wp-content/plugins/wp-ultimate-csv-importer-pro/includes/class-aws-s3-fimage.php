<?php

if ( ! defined( 'ABSPATH' ) ) exit; 


class UCI_S3_Feat_Image{

	private $ext_img 	= '_uci_s3_img';
	private $img_alt 	= '_uci_s3_alt';

	public function __construct(){
		if ( is_admin() ){
			add_action('add_meta_boxes', array($this, 'uci_s3_add_metaboxes'));
			add_action('save_post', array($this, 'uci_s3_save_data'));
		}
		else{
			add_action('init', array($this, 'uci_s3_dummy_featured_image'));
			add_filter('post_thumbnail_html', array($this, 'uci_s3replace_thumbnail'), 10, 5 );
		}
	}

	public function uci_s3replace_thumbnail($html, $post_id, $post_image_id, $size, $attr){

		$data = $this->uci_s3_get_meta( $post_id );
		if (!$data['imgurl']) {
			return $html;
		}else{
			$img 		= $data['img'];
			$alt 		= ( $data['alt'] ) ? 'alt="'.$data['alt'].'"' : '';
			$classes 	= 'external-img wp-post-image ';
			$classes   .= ( isset($attr['class']) ) ? $attr['class'] : '';
			$style 		= ( isset($attr['style']) ) ? 'style="'.$attr['style'].'"' : '';
			$html = sprintf('<img src="%s" %s class="%s" %s />', $img, $alt, $classes, $style);
			return $html;
		}
	}

	// Add Metaboxes 
	public function uci_s3_add_metaboxes(){
		$title			= __('Amazon S3 External URL', 'wp-ultimate-csv-importer-pro');
		$post_types 	= $this->uci_s3_get_post_types();
		add_meta_box( 'uci_aws_s3_feat_image',$title, [$this, 'uci_s3_metabox'],$post_types,'side','low');
	}

	public function uci_s3_metabox( $post ){

		$data 	 = $this->uci_s3_get_meta( $post->ID );
		$img 	 = $data['img'];
		$alt 	 = $data['alt'];
		$imgurl = $data['imgurl'];
		include SM_UCI_PRO_DIR.'admin/views/form-aws-s3-metabox.php';	
	}
		
	public function uci_s3_save_data( $post_id ){
		
		$url = isset($_POST['ext_url'])?esc_url($_POST['ext_url']):null;
		$alt = isset($_POST['ext_alt'])?wp_strip_all_tags($_POST['ext_alt']):null;

		if ( $url ){
			update_post_meta($post_id, $this->ext_img, $url);
			if ( $alt )	update_post_meta($post_id, $this->img_alt, $alt);	
		}
		else{
			delete_post_meta($post_id, $this->ext_img);
			delete_post_meta($post_id, $this->img_alt);
		}
	}
	
	public function uci_s3_dummy_featured_image(){

		foreach ( $this->uci_s3_get_post_types() as $post_type ) {
			add_filter( "get_${post_type}_metadata", array($this, 'uci_s3_thumbnail'), 10, 3 );
		}

	}

	public function uci_s3_thumbnail( $null, $obj_id, $meta_key ){

		if ( $meta_key == '_thumbnail_id' ){
			if ( $this->uci_s3_data( $obj_id ) )
				return true;
		}
		return null;
	}
	private function uci_s3_get_post_types(){
		
		$unwanted_types	= array('attachment', 'revision', 'nav_menu_item');
		$post_types 	= array_diff( get_post_types( array('public'   => true), 'names' ), $unwanted_types );
		return $post_types;
	}


	private function uci_s3_get_meta( $id ){
		
		$img = get_post_meta($id, $this->ext_img , true); 
		$alt = get_post_meta($id, $this->img_alt , true); 
		$data['img'] 	 = $img;
		$data['alt'] 	 = $alt;
		$data['imgurl'] = isset($img) && ! empty($img); 
		return $data;
	}

	private function uci_s3_data( $id ){
		return  get_post_meta($id, $this->ext_img , true);

	}
}
