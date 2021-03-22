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

class ValidateFile {
    private static $instance = null;

    public static function getInstance() {
		
		if (ValidateFile::$instance == null) {
            ValidateFile::$instance = new ValidateFile;
			return ValidateFile::$instance;
		}
		return ValidateFile::$instance;
    }


    /**
	 * Checks whether given file is in supported format.
	 * @param  string $filename - file name
	 * @return string
	 */
    public function validate_file_format($filename){
        $supported_file = array('csv' , 'xml' , 'zip' , 'txt');
        $extension = explode(".", $filename);
        $file_extension = end($extension);
        if(empty($file_extension)){
			$file_extension = 'xml';
		}
        if(!in_array($file_extension , $supported_file)){       
            $message = "Unsupported File Format";    
        }else{
            $message = "yes";
        }
        return $message;
    }


    /**
	 * Validates the uploaded file for certain requirements.
	 * @param  string $filepath - path to file
     * @param  string $file_extension - extension of file
	 * @return string
	 */
    public function file_validation($file_path , $file_extension){
       
            $get_file = file_get_contents($file_path);   
            $get_memory_limit = ini_get('memory_limit');
            if (preg_match('/^(\d+)(.)$/', $get_memory_limit, $matches)) {
                if ($matches[2] == 'M') {
                    $get_memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                } else if ($matches[2] == 'K') {
                    $get_memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
                }
            }
            
            $uploaded_filesize = filesize($file_path);      

            if(function_exists('mb_check_encoding')) {
                $utf_format = mb_check_encoding($get_file, 'UTF-8');

                if(!$utf_format){    
                    $message = "Your file is not in UTF-8 format";
                    return $message; 
                }
                
            }
                $get_post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
                if($uploaded_filesize > $get_post_max_size){
                    $message = "File size exceeded post maximum size";
                    return $message;
                }

                $get_php_size = $this->get_config_bytes(ini_get('upload_max_filesize'));  
                if($uploaded_filesize > $get_php_size){
                    $message = "File size exceeded PHP maximum size";
                    return $message;
                }
                else{
            
                    if($file_extension == 'csv' || $file_extension == 'txt'){
                        $delimiter = $this->getFileDelimiter($file_path, 5);
                        $validate_csv = $this->validateCSV($file_path , $delimiter);
                            $message = "yes";
                    }else{
                        $message = "yes";
                    }
                    return $message;
                } 
    }


    /**
	 * Converts filesize to bytes.
	 * @param  string $val - path to file
	 * @return int
	 */
    public function get_config_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
                case 'g':
                        $val *= 1024;
                case 'm':
                        $val *= 1024;
                case 'k':
                        $val *= 1024;
        }
        return $val;
    }

    /**
	 * Validates csv and txt files.
	 * @param  string $file_path 
     * @param  string $delimiter 
	 * @return string
	 */
    public function validateCSV($file_path='', $delimiter=','){
        
        if(!file_exists($file_path) || !is_readable($file_path))
            return FALSE;
            $header = array();
            $data = array();
            if (($handle = fopen($file_path, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
                {
                    
                    if(!$header)
                        $header = $row;
                    else{    
                        $data[] = array_combine($header, $row);
                        break;
                    }      
                }   
                $handle = fopen($file_path, 'r');   
                if(array_key_exists(null,$data[0])){
                    $valid = 'No';
                }else{
                    $valid = 'Yes';
                }
                if(empty($data[0])){
                    $valid = 'No';
                }
            fclose($handle);    
        }
        return $valid;
    }


    /**
	 * Possible delimiters.
	 * @param  string $file 
     * @param  int $checkLines
	 * @return string
	 */
    public function getFileDelimiter($file, $checkLines = 2){
		$file = new \SplFileObject($file);
		$delimiters = array(
		  ',',
		  '\t',
		  ';',
		  '|',
		  ':',
		);
		$results = array();
		$i = 0;
		 while($file->valid() && $i <= 1){
		    $line = $file->fgets();
		    foreach ($delimiters as $delimiter){
			$regExp = '/['.$delimiter.']/';
			$fields = preg_split($regExp, $line);
			if(count($fields) > 1){
			    if(!empty($results[$delimiter])){
				$results[$delimiter]++;
			    } else {
				$results[$delimiter] = 1;
			    }
			}
		    }
		   $i++;
		}
		$results = array_keys($results, max($results));
		return $results[0];
    }


    /**
	 * Converts bytes to KB, MB, GB.
	 * @param  int $bytes 
	 * @return string
	 */
    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}


    /**
	 * Gets post types, taxonomies, selected post type.
	 * @param string $hashkey 
     * @param string $filename
	 * @return string
	 */
    public function import_record_function($hashkey , $filename){

        $result = array();
        $extension_instance = new ExtensionHandler;
        $get_post = $extension_instance->get_import_post_types();  
        $import_record_post = array_keys($get_post);
    
        $get_taxo = get_taxonomies();
        $other_taxo = array('nav_menu','action-group','product_type','product_visibility','product_shipping_class','wpsc-variation','wpsc_log_type','post_format','link_category');
    
        if(function_exists('wc_get_attribute_taxonomies')){
            $get_pro_attr = wc_get_attribute_taxonomies();
            foreach($get_pro_attr as $value){
                $attr_name = $value->attribute_name;
                $attribute = 'pa_'.$attr_name;
                if(in_array($attribute , $get_taxo));
                {
                    unset($get_taxo[$attribute]);
                }
            }
        }else{
           
        }
    
        foreach($other_taxo as $value){
            if(in_array($value , $get_taxo)){
                unset($get_taxo[$value]);
            }
        }
        $import_record_taxonomy = array_keys($get_taxo);
        $get_type = $extension_instance->set_post_types($hashkey , $filename);
        
        $result['Post Type'] = $import_record_post;
        $result['Taxonomy'] = $import_record_taxonomy;
        $result['selected type'] = $get_type;
    
        return $result;
    }

}