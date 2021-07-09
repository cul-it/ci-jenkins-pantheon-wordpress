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

class YoastSeoImport {
    private static $yoast_instance = null;

    public static function getInstance() {
		
			if (YoastSeoImport::$yoast_instance == null) {
				YoastSeoImport::$yoast_instance = new YoastSeoImport;
				return YoastSeoImport::$yoast_instance;
			}
			return YoastSeoImport::$yoast_instance;
		}
		
    function set_yoast_values($header_array ,$value_array , $map, $post_id , $type){	
			$post_values = [];
			$helpers_instance = ImportHelpers::getInstance();
			$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);

			$this->yoast_import_function($post_values,$type, $post_id, $header_array , $value_array);
    }

    function yoast_import_function($data_array, $importas, $pID, $header_array , $value_array) {
		
			$createdFields = $yoastData = array();
			$media_instance = MediaHandling::getInstance();

			foreach ($data_array as $dkey => $dvalue) {
				$createdFields[] = $dkey;
			}
			// Import WP Yoast SEO information for Terms & Taxonomies
			foreach (get_taxonomies() as $item => $taxonomy_name) {
				if($taxonomy_name == $importas) {
					if(isset($data_array['title'])) {
						$yoastData['wpseo_title']= $data_array['title'];
					}
					if(isset($data_array['meta_desc'])) {
						$yoastData['wpseo_desc'] = $data_array['meta_desc'];
					}
					if(isset($data_array['meta-robots-noindex'])) {
						if($data_array['meta-robots-noindex'] == 1){
							$yoastData['wpseo_noindex'] = 'noindex';
						}
						if($data_array['meta-robots-noindex'] == 2){
							$yoastData['wpseo_noindex'] = 'index';
						}
					}
					if(isset($data_array['meta-robots-nofollow'])) {
						$yoastData['wpseo_nofollow'] = $data_array['meta-robots-nofollow'];
					}
					if(isset($data_array['meta-robots-adv'])) {
						$yoastData['wpseo_adv'] = $data_array['meta-robots-adv'];
					}
					if(isset($data_array['bctitle'])) {
						$yoastData['wpseo_bctitle'] = $data_array['bctitle'];
					}
					if(isset($data_array['sitemap-include'])) {
						$yoastData['wpseo_sitemap_include'] = $data_array['sitemap-include'];
					}
					if(isset($data_array['sitemap-prio'])) {
						$yoastData['wpseo_sitemap_prio'] = $data_array['sitemap-prio'];
					}
					if(isset($data_array['canonical'])) {
						$yoastData['wpseo_canonical'] = $data_array['canonical'];
					}
					if(isset($data_array['redirect'])) {
						$yoastData['wpseo_redirect'] = $data_array['redirect'];
					}
					if(isset($data_array['opengraph-title'])) {
						$yoastData['wpseo_opengraph-title'] = $data_array['opengraph-title'];
					}
					if(isset($data_array['opengraph-description'])) {
						$yoastData['wpseo_opengraph-description'] = $data_array['opengraph-description'];
					}
					if(isset($data_array['opengraph-image'])) {
						$yoastData['wpseo_opengraph-image'] = $data_array['opengraph-image'];

						$image_id = $media_instance->media_handling($data_array['opengraph-image'] , $pID,$data_array,'','');
						$yoastData['wpseo_opengraph-image-id'] = $image_id;
					}
					if(isset($data_array['twitter-title'])) {
						$yoastData['wpseo_twitter-title'] = $data_array['twitter-title'];
					}
					if(isset($data_array['twitter-description'])) {
						$yoastData['wpseo_twitter-description'] = $data_array['twitter-description'];
					}
					if(isset($data_array['twitter-image'])) {
						$yoastData['wpseo_twitter-image'] = $data_array['twitter-image'];

						$imageid = $media_instance->media_handling($data_array['twitter-image'] , $pID);
						$yoastData['wpseo_twitter-image-id'] = $imageid;
					}
					if(isset($data_array['google-plus-title'])) {
						$yoastData['wpseo_google-plus-title'] = $data_array['google-plus-title'];
					}
					if(isset($data_array['google-plus-description'])) {
						$yoastData['wpseo_google-plus-description'] = $data_array['google-plus-description'];
					}
					if(isset($data_array['google-plus-image'])) {
						$yoastData['wpseo_google-plus-image'] = $data_array['google-plus-image'];
					}
					if(isset($data_array['focus_keyword'])) {
						$yoastData['wpseo_focuskw'] = $data_array['focus_keyword'];
					}
					$seo_yoast_cat = get_option('wpseo_taxonomy_meta');	
					$seo_yoast_cat[$importas][$pID] = $yoastData;
					update_option('wpseo_taxonomy_meta', $seo_yoast_cat);
					break;
				}
			}
			// Import WP Yoast SEO information for Post types
			if (isset($data_array['focus_keyword'])) {
				$custom_array['_yoast_wpseo_focuskw'] = $data_array['focus_keyword'];
				$custom_array['_yoast_wpseo_focuskw_text_input'] = $data_array['focus_keyword']; //yoast seo pro works
			}
			if (isset($data_array['title'])) {
				$custom_array['_yoast_wpseo_title'] = $data_array['title'];
			}
			if (isset($data_array['meta_desc'])) {
				$custom_array['_yoast_wpseo_metadesc'] = $data_array['meta_desc'];
			}
			if (isset($data_array['meta_keywords'])) {
				$custom_array['_yoast_wpseo_metakeywords'] = $data_array['meta_keywords'];
			}
			if (isset($data_array['meta-robots-noindex'])) {
				$custom_array['_yoast_wpseo_meta-robots-noindex'] = $data_array['meta-robots-noindex'];
			}
			if (isset($data_array['meta-robots-nofollow'])) {
				$custom_array['_yoast_wpseo_meta-robots-nofollow'] = $data_array['meta-robots-nofollow'];
			}
			if (isset($data_array['meta-robots-adv'])) {
				$custom_array['_yoast_wpseo_meta-robots-adv'] = $data_array['meta-robots-adv'];
			}
			if (isset($data_array['bctitle'])) {
				$custom_array['_yoast_wpseo_bctitle'] = $data_array['bctitle'];
			}
			if (isset($data_array['sitemap-include'])) {
				$custom_array['_yoast_wpseo_sitemap-include'] = $data_array['sitemap-include'];
			}
			if (isset($data_array['sitemap-prio'])) {
				$custom_array['_yoast_wpseo_sitemap-prio'] = $data_array['sitemap-prio'];
			}
			if (isset($data_array['canonical'])) {
				$custom_array['_yoast_wpseo_canonical'] = $data_array['canonical'];
			}
			if (isset($data_array['redirect'])) {
				$custom_array['_yoast_wpseo_redirect'] = $data_array['redirect'];
			}
			if (isset($data_array['opengraph-title'])) {
				$custom_array['_yoast_wpseo_opengraph-title'] = $data_array['opengraph-title'];
			}
			if (isset($data_array['opengraph-description'])) {
				$custom_array['_yoast_wpseo_opengraph-description'] = $data_array['opengraph-description'];
			}
			if (isset($data_array['opengraph-image'])) {
				$custom_array['_yoast_wpseo_opengraph-image'] = $data_array['opengraph-image'];

				$image_id = $media_instance->media_handling($data_array['opengraph-image'] , $pID);
				$custom_array['_yoast_wpseo_opengraph-image-id'] = $image_id;
			}
			if (isset($data_array['twitter-title'])) {
				$custom_array['_yoast_wpseo_twitter-title'] = $data_array['twitter-title'];
			}
			if (isset($data_array['twitter-description'])) {
				$custom_array['_yoast_wpseo_twitter-description'] = $data_array['twitter-description'];
			}
			if (isset($data_array['twitter-image'])) {
				$custom_array['_yoast_wpseo_twitter-image'] = $data_array['twitter-image'];

				$imageid = $media_instance->media_handling($data_array['twitter-image'] , $pID);
				$custom_array['_yoast_wpseo_twitter-image-id'] = $imageid;	
			}
			if (isset($data_array['google-plus-title'])) {
				$custom_array['_yoast_wpseo_google-plus-title'] = $data_array['google-plus-title'];
			}
			if (isset($data_array['google-plus-description'])) {
				$custom_array['_yoast_wpseo_google-plus-description'] = $data_array['google-plus-description'];
			}
			if (isset($data_array['google-plus-image'])) {
				$custom_array['_yoast_wpseo_google-plus-image'] = $data_array['google-plus-image'];
			}
			if(isset($data_array['cornerstone-content'])) {
				$custom_array['_yoast_wpseo_is_cornerstone'] = $data_array['cornerstone-content'];
			}
			if (!empty ($custom_array)) {
				foreach ($custom_array as $custom_key => $custom_value) {
					update_post_meta($pID, $custom_key, $custom_value);
				}
			}
			return $createdFields;
		}

}