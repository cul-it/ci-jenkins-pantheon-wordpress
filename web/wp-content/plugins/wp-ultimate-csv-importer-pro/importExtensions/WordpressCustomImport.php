<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

namespace Smackcoders\WCSV;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WordpressCustomImport {
    private static $wordpress_custom_instance = null;

    public static function getInstance() {		
		if (WordpressCustomImport::$wordpress_custom_instance == null) {
			WordpressCustomImport::$wordpress_custom_instance = new WordpressCustomImport;
			return WordpressCustomImport::$wordpress_custom_instance;
		}
		return WordpressCustomImport::$wordpress_custom_instance;
    }
    
    function set_wordpress_custom_values($header_array ,$value_array , $map, $post_id , $type, $group_name){	
        $post_values = [];
        $helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
      
        if($group_name == 'CORECUSTFIELDS'){

			$this->wordpress_custom_user_import_function($post_values, $post_id ,$type , 'off',$header_array,$value_array);	
		}
		else{
			$this->wordpress_custom_import_function($map,$post_values, $post_id ,$type , 'off',$header_array,$value_array);
		}

    }

    public function wordpress_custom_user_import_function ($data_array, $pID, $importType , $core_serialize_info) {
  
		global $wpdb;
		$createdFields = array();
		if(!empty($data_array)) {
            foreach ($data_array as $custom_key => $custom_value) {
                $createdFields[] = $custom_key;
                if( $importType != 'Users'){
                    
                    if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){
                        $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}postmeta where post_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                       
                        if( !empty($get_meta_info)){
                            $wpdb->update($wpdb->prefix.'postmeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'post_id' => $pID ));
                        }else{
                            $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'post_id' => $pID ));
                        }
                    }
                    elseif (strpos($custom_value) != false) {
                            $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'post_id' => $pID ));
                    }elseif($custom_key == '_opening_time'){
                        $time = $data_array['_opening_time'];
                        $multiple_time = explode( '->', $time );
                        $opening_array = [];
                        foreach($multiple_time as $mul_time){
                            $exploded_time = explode( ',', $mul_time );
                            $opening_array[$exploded_time[0]] = $exploded_time[1];
                        }
                        update_post_meta($pID, '_opening_time',$opening_array);
                     }elseif($custom_key == 'image_gallery_ids'){
                        $image_url = $data_array['image_gallery_ids'];
                        $urls = explode( ',', $image_url );
                        $results = [];
                        foreach($urls as $images){
                            $results[] = $wpdb->get_var("select ID from {$wpdb->prefix}posts where guid = '$images'"); 
                            if(empty($results=='')){
                               $media_instance = MediaHandling::getInstance();
                               $results[]= $media_instance->media_handling($images, $pID,$data_array,'','','','','');
                            }
                        }
                     $imgs=implode(",",$results);
                     update_post_meta($pID, 'image_gallery_ids',$imgs);
                     }
                    else{	
                        update_post_meta($pID, $custom_key, $custom_value);
                    }
                }else{
                    if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){						
                        $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                        if( !empty($get_meta_info)){
                            $wpdb->update($wpdb->prefix.'usermeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'user_id' => $pID ));
                        }else{
                            $wpdb->insert($wpdb->prefix.'usermeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'user_id' => $pID ));
                        }
                    }else{
                        update_user_meta($pID, $custom_key, $custom_value);
                    }
                }
            }
        }
       
		return $createdFields;

    }
    
    public function wordpress_custom_import_function ($map,$data_array, $pID, $importType , $core_serialize_info,$header_array,$value_array) {
        global $wpdb;
        $helpers_instance = ImportHelpers::getInstance();
        $post_values = [];
		if (is_array($map)) {
		foreach($map as $key => $value){	
			$csv_value= trim($value['value']);
			if(!empty($csv_value)){
				$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
				if(preg_match_all($pattern, $csv_value, $matches, PREG_PATTERN_ORDER)){		
                    $csv_element = $csv_value;
                 	foreach($matches[2] as $value){

						$get_key = array_search($value , $header_array);
						if(isset($value_array[$get_key])){
							$csv_value_element = $value_array[$get_key];	
							$value = '{'.$value.'}';
							$csv_element = str_replace($value, $csv_value_element, $csv_element);
						}
					}

					$math = 'MATH';
						if (strpos($csv_element, $math) !== false) {
									
							$equation = str_replace('MATH', '', $csv_element);
							$csv_element = $helpers_instance->evalmath($equation);
						}
					$wp_element= trim($value['name']);
					if(!empty($csv_element) && !empty($wp_element)){
						$post_values[$wp_element] = $csv_element;
					}	
				}

				elseif(!in_array($csv_value , $header_array)){
					$wp_element= trim($value['name']);
					$post_values[$wp_element] = $csv_value;
				}

				else{
					$get_key = array_search($csv_value , $header_array);
					if(!empty($value_array[$get_key])){
						$csv_element = $value_array[$get_key];		
                        $wp_element = trim($value['name']);
                        if( $importType != 'Users'){
                            if( isset($core_serialize_info[$wp_element]) && $core_serialize_info[$wp_element] == 'on'){
                              $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}postmeta where post_id=%d and meta_key=%s" , $pID , $wp_element ), ARRAY_A);
                                if( !empty($get_meta_info)){
                                    $wpdb->update($wpdb->prefix.'postmeta' , array('meta_value' => $csv_element ) , array('meta_key' => $wp_element , 'post_id' => $pID ));
                                }else{
                                    $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $wp_element , 'meta_value' => $csv_element , 'post_id' => $pID ));
                                }
                            }else{	
                              $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $wp_element , 'meta_value' => $csv_element , 'post_id' => $pID ));  
                            }
                        }else{
                            if( isset($core_serialize_info[$wp_element]) && $core_serialize_info[$wp_element] == 'on'){
                                $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                                if( !empty($get_meta_info)){
                                    $wpdb->update($wpdb->prefix.'usermeta' , array('meta_value' => $csv_element ) , array('meta_key' => $wp_element , 'user_id' => $pID ));
                                }else{
                                    $wpdb->insert($wpdb->prefix.'usermeta' , array('meta_key'=> $wp_element , 'meta_value' => $csv_element , 'user_id' => $pID ));
                                }
                            }else{
                                update_user_meta($pID, $wp_element, $csv_element);
                            }
                        }
					}
				}
			}
		}
	}
}
}
