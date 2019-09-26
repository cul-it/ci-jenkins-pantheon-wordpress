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
    
class DragandDropExtension {
    private static $drag_drop_instance = null;

    private function __construct(){
        add_action('wp_ajax_displayCSV',array($this,'display_csv_values'));
        add_action('wp_ajax_preview',array($this,'preview_record'));
    }
    
    public static function getInstance() {
            
        if (DragandDropExtension::$drag_drop_instance == null) {
            DragandDropExtension::$drag_drop_instance = new DragandDropExtension;
            return DragandDropExtension::$drag_drop_instance;
        }
        return DragandDropExtension::$drag_drop_instance;
    }

    public function display_csv_values(){
        $hashkey = $_POST['HashKey'];
        $templatename = $_POST['templatename'];
        $get_row = $_POST['row'];
        
        global $wpdb;
        $file_table_name = $wpdb->prefix ."smackcsv_file_events";
        $template_table_name = $wpdb->prefix . "ultimate_csv_importer_mappingtemplate";
        $row = $get_row - 1;

        if(empty($hashkey)){	
			$get_detail   = $wpdb->get_results( "SELECT eventKey FROM $template_table_name WHERE templatename = '$templatename' " );
			$hashkey = $get_detail[0]->eventKey;
        }

        $smackcsv_instance = SmackCSV::getInstance();
		$upload_dir = $smackcsv_instance->create_upload_dir();

        ini_set("auto_detect_line_endings", true);
		$info = [];
		if (($h = fopen($upload_dir.$hashkey.'/'.$hashkey, "r")) !== FALSE) 
		{

        $line_number = 0;
        $Headers = [];
        $Values = [];
        $response = [];
		// Convert each line into the local $data variable	
		while (($data = fgetcsv($h, 0, ",")) !== FALSE) 
		{		
			// Read the data from a single line
			array_push($info , $data);
			if($line_number == 0){
                $Headers = $info[$line_number];
            }else{
                $values = $info[$line_number];
                array_push($Values , $values);		
			}
			$line_number ++;		
		}	
		// Close the file
		fclose($h);
		}
             
        $get_total_row = $wpdb->get_results("SELECT total_rows FROM $file_table_name WHERE hash_key = '$hashkey' ");
        $total_row = $get_total_row[0]->total_rows;

        $response['success'] = true;
        $response['total_rows'] = $total_row;
        $response['Headers'] = $Headers;
        $response['Values'] = $Values[$row];
        
        echo wp_json_encode($response);
        wp_die();
    }

    /**
	 * @param $xml
	 * @param $query
	 * @param $row
	 * @return string
	 */
	public function parse_element($xml,$value,$row,$parent_name,$child_name){
        $xpath = new \DOMXPath($xml);
        //$query = '/data/item['.$row.']/'.$value;
        $query = '/'.$parent_name.'/'.$child_name.'['.$row.']/'.$value;
        $entries = $xpath->query($query);
       
		$content = $entries->item(0)->textContent;
		return $content;
	}

    public function preview_record(){
        $row = $_POST['row'];
        $hashkey = $_POST['hashkey'];
       // $result = '';
        $response = [];
        $helpers_instance = ImportHelpers::getInstance(); 

        $get_row = $row - 1;
        $smackcsv_instance = SmackCSV::getInstance();
        $upload_dir = $smackcsv_instance->create_upload_dir();

        $file = $upload_dir.$hashkey.'/'.$hashkey;

			if($_POST['xml'] == 'true'){

                $xml = simplexml_load_file($file);
                $xml_arr = json_decode( json_encode($xml) , 1);
                $parent_name = $xml->getName();
                foreach($xml->children() as $child){   
                    $child_name = $child->getName();    
                }

				$mapping = array('title' => $_POST['title'], 'content' => $_POST['content'], 'excerpt' => $_POST['excerpt'], 'image' => $_POST['image'], 'slug' => $_POST['slug'], 'date' => $_POST['date'], 'status' => $_POST['status']);
				$doc = new \DOMDocument();
				$doc->load($file);

				foreach ($mapping as $key => $val) {
					if($val!=""){
						$val = str_replace('{', '', $val);
                        $val = str_replace('}', '', $val);
                        $val = str_replace('<p>', '', $val);
                        $val = str_replace('</p>', '', $val);
                        $val = preg_replace("(".$child_name."[+[0-9]+])", $child_name."[".$_POST['row']."]", $val);

                        $modified_result[$key] = $this->parse_element($doc, $val, $_POST['row'], $parent_name, $child_name);
					}
				}	
            }
            else{

                ini_set("auto_detect_line_endings", true);
                $info = [];
                if (($h = fopen($upload_dir.$hashkey.'/'.$hashkey, "r")) !== FALSE) 
                {
                    $line_number = 0;
                    $Headers = [];
                    $Values = [];
                    $response = [];
                            
                    while (($data = fgetcsv($h, 0, ",")) !== FALSE) 
                    {		
                        array_push($info , $data);
                        if($line_number == 0){
                            $Headers = $info[$line_number];
                        }else{
                            $values = $info[$line_number];
                            array_push($Values , $values);		
                        }
                        $line_number ++;		
                    }	
                    fclose($h);
                }    
                $csv_values = $Values[$get_row];
                $data = array();
                $data = array_combine($Headers , $csv_values);

                $mapping = array('title' => $_POST['title'], 'content' => $_POST['content'], 'excerpt' => $_POST['excerpt'], 'image' => $_POST['image'], 'slug' => $_POST['slug'], 'date' => $_POST['date'], 'status' => $_POST['status']);		
                foreach($mapping as $key => $val) {		
                    $pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
                    preg_match_all($pattern, $val, $results, PREG_PATTERN_ORDER);
                    for($i=0; $i<count($results[2]); $i++) {
                        $oldWord = $results[0][$i];
                                    
                        $get_val = $results[2][$i];
                        //TODO xml
                        if(isset($data[$get_val])) {
                            $newWord = $data[$get_val];
                        } else {
                            $newWord = $get_val;
                        }
                        $val = str_replace($oldWord, ' ' . $newWord, $val);	
                    }
                    $modified_result[$key] = $val;
                }
            }
            
            $response['success'] = true;                
            $response['Title'] = $modified_result['title'];
            $response['Content'] = $modified_result['content'];
            $response['Image'] = $modified_result['image'];
            $response['Excerpt'] = $modified_result['excerpt'];
            $response['Slug'] = $modified_result['slug'];
            $response['Date'] = $modified_result['date'];
            $response['Status'] = $modified_result['status'];
            
            // $result .= '<table class="table table-striped">';
			// $result .= '<tr>';
			// #$result .= '<td><label>Post Title</label></td>';
			// $result .= '<td><p><b>' . $modified_result['title'] . '</b></p></td>';
			// $result .= '</tr>';
			// $result .= '<tr>';
			// #$result .= '<td><label>Post Content</label></td>';
			// $result .= '<td><p>' . $modified_result['content'] . '</p></td>';
			// $result .= '</tr>';
			// $result .= '<tr>';
			// $result .= '<tr>';
			// #$result .= '<td><label>Featured Image</label></td>';
			// $result .= '<td><p><img src="' . $modified_result['image'] . '" width="50" height="50" /></p></td>';
			// $result .= '</tr>';
			// #$result .= '<td><label>Short Description</label></td>';
			// $result .= '<td><p>' . $modified_result['excerpt'] . '</p></td>';
			// $result .= '</tr>';
			// $result .= '</table>';
            // print $result;
            echo wp_json_encode($response);    
        wp_die();
    }
}