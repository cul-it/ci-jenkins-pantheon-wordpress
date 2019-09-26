<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackUCIMediaScheduler {

	public $admin_user_id = null;

	public static function get_admin_user_id() {

	}

	public static function populateFeatureImages() {
		global $wpdb;

		if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true)
			return false;

		$result = $wpdb->get_results($wpdb->prepare("select option_name, option_value from $wpdb->options where option_name like %s", '%smack_featured_%'));
		require_once SM_UCI_PRO_DIR.'/libs/aws/aws-upload.php';
		include_once SM_UCI_PRO_DIR.'/libs/aws/aws-autoloader.php';
                $s3 = new uci_aws_s3_helper();
                $uciEventLogger = new SmackUCIEventLogging();
		if(!empty($result)) {
			foreach($result as $val) {
				$thumbnailId = null;
				$post_id = explode('smack_featured_', $val->option_name);
				$postID = $post_id[1];
				$get_image_info = maybe_unserialize($val->option_value);
				$url = $get_image_info['value'];
				preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches );
				$renameImage = basename( $matches[0] );
				 //s3 upload
				$ucisettings = get_option('sm_uci_pro_settings');
                                if($ucisettings['enable_s3'] == 'on'){
                                        $fimg_path = $url;
                                        $fimg_name = basename($fimg_path);
					if(@getimagesize($fimg_path)){
					$s3imgurl = $s3->aws_image_upload($postID,$fimg_path,$fimg_name,$uciEventLogger);
                                        update_post_meta($postID, '_uci_s3_img', $s3imgurl);
					$eventLog = '';
					$eventLogFile = SM_UCI_DEBUG_LOG;
					fopen($eventLogFile , 'w+');
					$uciEventLogger->lfile($eventLogFile);
					$uciEventLogger->lwrite('PostId :' .$postID .' External Image Url : '.$s3imgurl);
					}
					//s3 upload
				}else{
					if(in_array('nextgen-gallery/nggallery.php', self::active_plugins())) {
						if(!empty($get_image_info['nextgen_gallery'])) {
							$thumbnailId = self::NextGenGallery( $postID, $get_image_info );
						} else {
							$thumbnailId = self::convert_local_imageURL( $url, $postID, $renameImage, $get_image_info['media_settings'] );
						}
					} else {
						$thumbnailId = self::convert_local_imageURL( $url, $postID, $renameImage, $get_image_info['media_settings'] );
					}
					if($thumbnailId != null) {
						set_post_thumbnail( $postID, $thumbnailId );
						delete_option( $val->option_name );
					}
				}
			}
		}
	}

	public static function NextGenGallery($post_id, $imageInfo) {
		global $wpdb;
		require_once(ABSPATH . "wp-includes/pluggable.php");
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$imageURL = $imageInfo['value'];
		$get_ngg_options = get_option('ngg_options');
		$get_gallery_path = explode('/', $get_ngg_options['gallerypath']);

		// Get NextGEN Gallery Image
		$gallery_table = $wpdb->prefix . 'ngg_gallery';
		if(is_numeric($imageInfo['nextgen_gallery']['directory'])) {
			$gallery_id = $imageInfo['nextgen_gallery']['directory'];
			$is_gallery_dir_available = $wpdb->get_results($wpdb->prepare("select *from $gallery_table where gid = %d", $imageInfo['nextgen_gallery']['directory']));
			if(empty($is_gallery_dir_available)) { // Create a gallery directory if not exist
				$gallery_title = 'Smack UCI Gallery';
				$gallery_name = preg_replace('/\s/', '-', strtolower(trim($gallery_title)));
				// Check the directory if already exist or not
				$get_gallery_id = $wpdb->get_col($wpdb->prepare("select gid from $gallery_table where name = %s", $gallery_name));
				if(!empty($get_gallery_id)) {
					$gallery_id = $get_gallery_id[0];
				} else {
					$wpdb->insert( $wpdb->prefix . 'ngg_gallery', array(
						'name'  => strtolower( $gallery_name ),
						'slug'  => strtolower( $gallery_name ),
						'path'  => $get_ngg_options['gallerypath'] . $gallery_name,
						'title' => $gallery_title,
						'author'=> 1,
					), array( '%s', '%s', '%s', '%s', '%d' ) );
					$gallery_id  = $wpdb->insert_id;
					$gallery_dir = WP_CONTENT_DIR . '/' . $get_gallery_path[1] . '/' . $gallery_name;
					wp_mkdir_p( $gallery_dir );
				}
			} else {
				$get_gallery_name = $wpdb->get_col($wpdb->prepare("select name from $gallery_table where gid = %d", $imageInfo['nextgen_gallery']['directory']));
				$gallery_name = $get_gallery_name[0];
			}
		} else {
			$gallery_title = $imageInfo['nextgen_gallery']['directory'];
			$gallery_name = preg_replace('/\s/', '-', strtolower(trim($gallery_title)));
			$is_gallery_dir_available = $wpdb->get_results($wpdb->prepare("select *from $gallery_table where name = %s", $gallery_name));
			if(empty($is_gallery_dir_available)) {
				$wpdb->insert( $wpdb->prefix . 'ngg_gallery', array(
					'name'  => strtolower( $gallery_name ),
					'slug'  => strtolower( $gallery_name ),
					'path'  => $get_ngg_options['gallerypath'] . $gallery_name,
					'title' => $gallery_title,
					'author'=> 1,
				), array( '%s', '%s', '%s', '%s', '%d' ) );
				$gallery_id = $wpdb->insert_id;
				$gallery_dir = WP_CONTENT_DIR . '/' . $get_gallery_path[1] . '/' . $gallery_name;
				wp_mkdir_p( $gallery_dir );
			} else {
				$get_gallery_id = $wpdb->get_col($wpdb->prepare("select gid from $gallery_table where name = %s", $gallery_name));
				$gallery_id = $get_gallery_id[0];
			}
		}

		// Populating the gallery images using NextGEN API by Fredrick Marks
		$gallery_image_path = WP_CONTENT_DIR . '/' . $get_gallery_path[1] . '/' . $gallery_name . '/';
		$fImg_name = @basename($imageURL);
		$fImg_name = preg_replace("/[^a-zA-Z0-9.\s_-]/", "", $fImg_name);
		$fImg_name = preg_replace('/\s/', '-', $fImg_name);
		$fImg_name = urlencode($fImg_name);

		$path_parts = pathinfo($imageURL);
		if (!isset($path_parts['extension'])) {
			$fImg_name = $fImg_name . '.jpg';
			$img_extension = 'jpg';
		}
		if (isset($path_parts['extension'])) {
			$img_extension = $path_parts['extension'];
		}
		$real_fImg_name = $path_parts['filename'];
		$real_fImg_name = preg_replace("/[^a-zA-Z0-9.\s_-]/", "", $real_fImg_name);
		$real_fImg_name = preg_replace('/\s/', '-', $real_fImg_name);
		$real_fImg_name = urlencode($real_fImg_name);

		//duplicate check for media
		$file = $existing_attachment = array();
		$query = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where post_type = %s", 'attachment'));
		foreach($query as $key){
			$existing_attachment[] = $key->post_title;
		}

		if(!in_array($real_fImg_name, $existing_attachment)){
			$fImg_name = wp_unique_filename($gallery_image_path, $fImg_name, $img_extension);
			self::get_img_from_URL($imageURL, $gallery_image_path, $fImg_name);
		} else{
			$fImg_name = $real_fImg_name . '.' . $img_extension;
		}
		$gallery_image_url = WP_CONTENT_URL . '/' . $get_gallery_path[1] . '/' . $gallery_name . '/' . $fImg_name;
		if (@getimagesize($gallery_image_url)) {
			$file ['guid'] = $gallery_image_url;
			$file ['post_title'] = $real_fImg_name;
			$file ['post_content'] = '';
			$file ['post_status'] = 'attachment';
		} else {
			$file = array();
		}
		$attachment_id = null;
		if (!empty ($file)) {
			$attachment = array(
				'guid'           => $gallery_image_url,
				'post_mime_type' => 'image/jpeg',
				'post_title'     => preg_replace( '/\.[^.]+$/', '', @basename( $gallery_image_url ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$img_import_date = date('Y-m-d H:i:s');
			$wpdb->insert( $wpdb->prefix .'ngg_pictures', array(
				'image_slug' => $real_fImg_name,
				'galleryid'  => $gallery_id,
				'filename'   => $fImg_name,
				'alttext'    => $real_fImg_name,
				'imagedate'  => $img_import_date,
			),
				array( '%s', '%d', '%s', '%s', '%s' )
			);
			$image_id = $wpdb->insert_id;
			$storage  = C_Gallery_Storage::get_instance();
			$params = array('watermark' => false, 'reflection' => false);
			$result = $storage->generate_thumbnail($image_id, $params);
			$post_args = array('post_id' => $post_id);

			$copy_image = TRUE;

			$upload_dir = wp_upload_dir();
			$basedir = $upload_dir['basedir'];
			$thumbs_dir = implode(DIRECTORY_SEPARATOR, array($basedir, 'ngg_featured'));
			$gallery_abspath = $storage->get_gallery_abspath($gallery_id);
			$image_abspath = $storage->get_full_abspath($image_id);
			$url = $storage->get_full_url($image_id);
			$target_basename = M_I18n::mb_basename($image_abspath);

			$image = $storage->_image_mapper->find($image_id);

			if (strpos($image_abspath, $gallery_abspath) === 0) {
				$target_relpath = substr($image_abspath, strlen($gallery_abspath));
			} else {
				if ($gallery_id) {
					$target_relpath = path_join(strval($gallery_id), $target_basename);
				} else {
					$target_relpath = $target_basename;
				}
			}
			$target_relpath = trim($target_relpath, '\\/');
			$target_path = path_join($thumbs_dir, $target_relpath);
			$max_count = 100;
			$count = 0;
			while (@file_exists($target_path) && $count <= $max_count) {
				$count++;
				$pathinfo = M_I18n::mb_pathinfo($target_path);
				$dirname = $pathinfo['dirname'];
				$filename = $pathinfo['filename'];
				$extension = $pathinfo['extension'];
				$rand = mt_rand(1, 9999);
				$basename = $filename . '_' . sprintf('%04d', $rand) . '.' . $extension;
				$target_path = path_join($dirname, $basename);
			}
			$target_dir = dirname($target_path);
			wp_mkdir_p($target_dir);
			if ($copy_image) {
				@copy($image_abspath, $target_path);
				if (!$attachment_id) {
					$size = @getimagesize($target_path);
					$image_type = $size ? $size['mime'] : 'image/jpeg';
					$title = sanitize_file_name($image->alttext);
					$caption = sanitize_file_name($image->description);
					$attachment = array('post_title' => $title, 'post_content' => $caption, 'post_status' => 'attachment', 'post_parent' => 0, 'post_mime_type' => $image_type, 'guid' => $url);
					$attachment_id = wp_insert_attachment($attachment, $target_path);
				}
				update_post_meta($attachment_id, '_ngg_image_id', $image_id);
				wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $target_path));
			}
		}

		return $attachment_id;
	}

	public static function convert_local_imageURL ($imageURL, $post_id, $renameimage = NULL, $media_settings = array()) {

		require_once(ABSPATH . "wp-includes/pluggable.php");
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$dir = wp_upload_dir();
		$existing_attachment = $existing_attachment_url = $file = array();
		$get_media_settings = get_option('uploads_use_yearmonth_folders');
		$img_extension = 'jpg';
		$mime_type = 'image/jpeg';
		$attach_id = null;
		$realImageURL = $imageURL;
		global $wpdb;
		if ($get_media_settings == 1) {
			$dirname = date('Y') . '/' . date('m');
			$full_path = $dir ['basedir'] . '/' . $dirname;
			$baseurl = $dir ['baseurl'] . '/' . $dirname;
		} else {
			$full_path = $dir ['basedir'];
			$baseurl = $dir ['baseurl'];
		}
		$fimg_path = $full_path;
		$image_name = basename($imageURL);
		$path_parts = pathinfo($imageURL);
		if (!empty($path_parts['extension'])) {
			$img_extension = $path_parts['extension'];
		}
		$fimg_type = wp_check_filetype( $image_name, null );

		if (!empty($fimg_type['type'])) {
			$mime_type = $fimg_type['type'];
		} elseif (!empty($fimg_type['ext'])) {
			$mime_type = $fimg_type['ext'];
		}

		$real_image_name = $path_parts['filename'];
		$real_image_name = preg_replace("/[^a-zA-Z0-9.\s_-]/", "", $real_image_name);
		$real_image_name = preg_replace('/\s/', '-',$real_image_name);
		$real_image_name = urlencode($real_image_name);
		
		if(isset($media_settings['thumbnail'])) {
			$thumbnail_width = get_option('thumbnail_size_w');
			$thumbnail_height = get_option('thumbnail_size_h');
			add_image_size( 'thumbnail', $thumbnail_width, $thumbnail_height, true );
		}
		if(isset($media_settings['medium'])) {
			$medium_width = get_option('medium_size_w');
			$medium_height = get_option('medium_size_h');
			add_image_size( 'medium', $medium_width, $medium_height, true );
		}
		if(isset($media_settings['medium_large'])) {
			$medium_large_width = get_option('medium_large_size_w');
			$medium_large_height = get_option('medium_size_h');
			add_image_size( 'medium_large', $medium_large_width, $medium_large_height, true );
		}
		if(isset($media_settings['large'])) {
			$large_width = get_option('large_size_w');
			$large_height = get_option('large_size_h');
			add_image_size( 'large', $large_width, $large_height, true );
		}
	    if(isset($media_settings['custom'])) {
            $i = 0;
            foreach($media_settings as $k=>$v) {
			    if(preg_match("/custom_slug_/", $k)) {
			    	$pattern = str_replace('custom_slug_', '', $k);
			        $matches[$i]['slug'] = $media_settings['custom_slug_'.$pattern];
			        $matches[$i]['width'] = $media_settings['custom_width_'.$pattern];
			        $matches[$i]['height'] = $media_settings['custom_height_'.$pattern];
			        $i++;
			    } 
			}

			foreach ($matches as $v1 => $settings) {
				add_image_size( $settings['slug'], $settings['width'], $settings['height'], true );
			}
        }
	
		//duplicate media
		$query = $wpdb->get_results($wpdb->prepare("select post_title, guid from $wpdb->posts where post_type = %s", 'attachment'));
		foreach($query as $key){
			$existing_attachment[] = $key->post_title;
			$existing_attachment_url[$key->post_title][] = $key->guid;
		}

		$rename_file_name = null;
		if(isset($media_settings['imageName']) && $media_settings['imageName'] != null && $media_settings['imageName'] != '--Select--') {
			$rename_file_name = preg_replace('/[^ \w]+/', "", $media_settings['imageName']);
			$rename_file_name = preg_replace('/\s/', '-', $rename_file_name);
			//$real_image_name = $media_settings['imageName'];
		}

		//duplicate media
		$is_exist = false;
		if(isset($media_settings['media_process'])) {
			if($media_settings['media_process'] == 'use_existing_images' || $media_settings['media_process'] == 'overwrite_existing_images') {
				if ( in_array( $real_image_name, $existing_attachment ) ) {
					$imageURL = $existing_attachment_url[ $real_image_name ][0];
					$get_attachment_id = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where guid = %s", $imageURL));
					$attach_id = $get_attachment_id[0];
					$is_exist = true;
				}
			}
			if($media_settings['media_process'] == 'use_existing_images' && $is_exist == true) {
				return $attach_id; #TODO: Need to un comment this line.
			} elseif($media_settings['media_process'] == 'overwrite_existing_images' && $is_exist == true) {
				self::get_img_from_URL($imageURL, $fimg_path, $real_image_name);
			}
		}

		// if($rename_file_name != null) {
		// 	if(!in_array($rename_file_name, $existing_attachment)){
		// 		$imageURL = $existing_attachment_url[ $real_image_name ][0];
		// 		$fimg_name = wp_unique_filename($fimg_path, $rename_file_name, $img_extension);
		// 		self::get_img_from_URL($imageURL, $fimg_path, $fimg_name);
		// 	} else{
		// 		$fimg_name = $rename_file_name . '.' . $img_extension;
		// 	}
		// } else {
			if($is_exist == false) {
				$fimg_name = wp_unique_filename( $fimg_path, $path_parts['basename'], $img_extension );
				$extcheck = pathinfo($fimg_name); //without extension images fix Ex: http://verkkouutisetfi.s3.amazonaws.com/ca66d1c32d12a15a6644e9001dabd9d937439e3b4bdb82230a347afb6e70b5cc
		                if (empty($extcheck['extension'])) {	
					$fimg_name = $fimg_name. '.' . $img_extension;
				}
				self::get_img_from_URL($imageURL, $fimg_path, $fimg_name);
				$imageURL = $baseurl . "/" . $fimg_name;
			} else {
				$fimg_name = $path_parts['basename'];
			}
		//}

		$filepath = $fimg_path . "/" . $fimg_name;
		if(@getimagesize($filepath)) {
			$file ['guid'] = $imageURL;
			// Image title
			if(isset($media_settings['title'])) {
				$file ['post_title'] = $media_settings['title'];
			} else {
				$file ['post_title'] = preg_replace('/\.[^.]+$/', '', @basename($file ['guid']));
			}
			// Image description
			if(isset($media_settings['description'])) {
				$file ['post_content'] = $media_settings['description'];
			}
			// Image caption
			if(isset($media_settings['caption'])) {
				$file ['post_excerpt'] = $media_settings['caption'];
			}
			// Image alt text
			if(isset($media_settings['alttext'])) {
				$file ['_wp_attachment_image_alt'] = $media_settings['alttext'];
			}
			$file ['post_status'] = 'inherit';
			$file ['post_type'] = 'attachment';
			$file ['post_mime_type'] = $mime_type;
		} else {
			$parserObj = new SmackCSVParser();
			$parserObj->logW('Invalid Image URL: ', $realImageURL);
			delete_option('smack_featured_' . $post_id);
			unset($parserObj);
		}

		if (!empty ($file)) {
			$attachment = $file;
			if ($get_media_settings == 1) {
				$generate_attachment = $dirname . '/' . $fimg_name;
			} else {
				$generate_attachment = $fimg_name;
			}
			$uploadedImage = $dir['path'] . '/' . $fimg_name;

			//duplicate check for media
			if($rename_file_name != null) {
				if(!in_array($rename_file_name, $existing_attachment)){
					$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
				} else {
					$query2 = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s", $rename_file_name, 'attachment'));
					foreach($query2 as $key2){
						$attach_id = $key2->ID;
					}
				}
				$post = get_post($post_id);
				$old_file = get_attached_file($attach_id);
				$path = pathinfo($old_file);
				if(!isset($path['extension']))
					$path['extension'] = 'jpg';
				$new_file = $dir['path'] . '/' . $rename_file_name . "." . $path['extension'];
				rename($old_file, $new_file);
				update_attached_file( $attach_id, $new_file );
				$attachment['guid'] = $baseurl . "/" . $rename_file_name . "." . $path['extension'];
				$wpdb->update($wpdb->posts, array('guid' => $new_file), array('ID' => $attach_id));
				$attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
				wp_update_attachment_metadata($attach_id, $attach_data);
			} else {
				if(in_array($real_image_name, $existing_attachment)){
					$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
				} else {
					$query2 = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s", $real_image_name, 'attachment'));
					foreach($query2 as $key2){
						$attach_id = $key2->ID;
					}
					if($attach_id == null) {
						$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
					}
				}
				$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
				wp_update_attachment_metadata($attach_id, $attach_data);
			}
		}
		if($attach_id != null && isset($attachment['_wp_attachment_image_alt']))
			update_post_meta($attach_id, '_wp_attachment_image_alt', $attachment['_wp_attachment_image_alt']);

		return $attach_id;
	}

	public static function get_img_from_URL($f_img, $fimg_path, $fimg_name) {
		$f_img = str_replace(" ", "%20", $f_img);
		if ($fimg_path != "" && $fimg_path) {
			$fimg_path = $fimg_path . "/" . $fimg_name;
		}
		$response = wp_remote_get($f_img);
		$rawdata =  wp_remote_retrieve_body($response);
		$http_code = wp_remote_retrieve_response_code($response);
		if ( $http_code != 200 && strpos( $rawdata, 'Not Found' ) != 0 ) {
			$rawdata = false;
		}
		if ( $rawdata == false ) {
			return null;
		} else {
			if ( file_exists( $fimg_path ) ) {
				unlink( $fimg_path );
			}
			$fp = fopen( $fimg_path, 'x' );
			fwrite( $fp, $rawdata );
			fclose( $fp );
		}
	}

	public static function active_plugins() {
		return get_option('active_plugins');
	}
}
