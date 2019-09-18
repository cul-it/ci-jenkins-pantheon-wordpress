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

class XmlHandler {
    private static $xml_instance = null;

    private function __construct(){
		add_action('wp_ajax_get_parse_xml',array($this,'parse_xml'));
    }

    public static function getInstance() {
		
		if (XmlHandler::$xml_instance == null) {
			XmlHandler::$xml_instance = new XmlHandler;
			return XmlHandler::$xml_instance;
		}
		return XmlHandler::$xml_instance;
    }


    public function parse_xml(){
        
        $row_count = $_POST['row'];
        $hash_key = $_POST['HashKey'];

        $smack_csv_instance = SmackCSV::getInstance();
        $upload_dir = $smack_csv_instance->create_upload_dir();

        $upload_dir_path = $upload_dir. $hash_key;
        if (!is_dir($upload_dir_path)) {
            wp_mkdir_p( $upload_dir_path);
        }
        chmod($upload_dir_path, 0777);   
        $path = $upload_dir . $hash_key . '/' . $hash_key;    

        $response = [];
        $xml = simplexml_load_file($path);
       // $str_xml = simplexml_load_string($xml);
        $xml_arr = json_decode( json_encode($xml) , 1);
        
        //$parent_name = $xml->getName();

        foreach($xml->children() as $child){   
            $child_name = $child->getName();    
        }
      
        $total_xml_count = $this->get_xml_count($path , $child_name);

        $row = $row_count - 1;
        $headers = array_keys($xml_arr[$child_name][$row]); 
        $values = array_values($xml_arr[$child_name][$row]);
       
        foreach($values as $key => $value){  
            if(empty($value)){
                $values[$key] = '';
            }
        }
        
        $response['success'] = true;
        $response['total_rows'] = $total_xml_count;
        $response['Headers'] = $headers;
        $response['Values'] = $values;
               
        echo wp_json_encode($response);
        wp_die();
    }

    /**
	 * Parse xml file.
	 */
    public function parsing_xmls(){
       
        $hash_key = $_POST['HashKey'];
        $treetype = $_POST['treetype'];	

        $smack_csv_instance = SmackCSV::getInstance();
        $upload_dir = $smack_csv_instance->create_upload_dir();

        $upload_dir_path = $upload_dir. $hash_key;
        if (!is_dir($upload_dir_path)) {
            wp_mkdir_p( $upload_dir_path);
        }
        chmod($upload_dir_path, 0777);
        
        $file = $upload_dir . $hash_key . '/' . $hash_key;    
        $id = "item";
		
		$namespace = explode(":", $id);
		
		if(isset($namespace[1]))
		$n = $namespace[1];
		else
		$n = $id;
		
		
		$doc = new \DOMDocument();
		$doc->load($file);

		  $nodes=$doc->getElementsByTagName($n);
		  
		if($nodes->length < $_POST['pag'])
         //die('<div style="color:red;padding:20px">Maximum Limit Exceed!<div>');
        $response['message'] = "Maximum Limit Exceed!";
        //wp_die();

		if(isset($_POST['pag']))
		  $i = $_POST['pag'] - 1;
		else
		  $i = 0;
		if($i < 0)
         // die('<div style="color:red;padding:20px">Node not available!<div>');
          $response['message'] = "Node not available!";
          //wp_die();
         
        
		while (is_object($finance = $doc->getElementsByTagName($n)->item($i))) {
			
			if($treetype == 'table'){
               $result = $this->tableNode($finance);
                  
            }
			else{
                $result = $this->treeNode($finance);
            }
			   
		   // die();
		    $i++;
		}
        //die();
    }


    /**
	 * Display fields in table format .
	 * @param  string $node
	 * @return string
	 */
    public function tableNode($node){
        $response = [];
        $table_response = [];
        $resultant = [];
           
		if($node->nodeName != '#text'){ 
			if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
				    $newVal = str_replace('/', '_', $node->getNodePath());
				    $newVal = str_replace('[', '', $newVal);
                    $newVal = str_replace(']', '', $newVal);
                    //echo $node->nodeName . "\n";
				  	
			}  
			if ($node->hasChildNodes()) {
				foreach ($node->childNodes as $child){
				    $this->tableNode($child);
				}
                //get all attributes
                
                if($node->hasAttributes()){
                    
                    for ($i = 0; $i <= $node->attributes->length; ++$i) {    
                        $attr_nodes = $node->attributes->item($i);
                        if($attr_nodes->nodeName && $attr_nodes->nodeValue) 
                            $attrs[$node->nodeName][$attr_nodes->nodeName] = $attr_nodes->nodeValue;
                    }
                }
                   
			    //get all attributes
				if($node->nodeValue || $node->nodeValue == 0){ 
				    if($node->childNodes->length == 1){
                        $header = $node->nodeName; 
                        
                        if(strlen($node->nodeValue) > 150) {   
                            $value = substr($node->nodeValue, 0, 150);
                            $values = strip_tags($value);
                                
                        }else{
                            $values = strip_tags($node->nodeValue);    
                        }      
                    }   
                } 
			}  
			if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
                 //echo '&lt;/'.$node->nodeName.'&gt;'; 
            }   
            
        }   
        
        if(!empty($header) && !empty($values)){
            $header_values = preg_replace('/\s+/', '', $values); 
            
            $resultant['Headers'] = $header;
            $resultant['Values'] = $header_values;
            //echo wp_json_encode($resultant);   

           //wp_die();
        }
	}

    /**
	 * Display fields in tree format.
	 * @param  string $node
	 * @return string
	 */
	public function treeNode($node)
		{  
			if($node->nodeName != '#text'){ 
                if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
                  $newVal = str_replace('/', '_', $node->getNodePath());
                  $newVal = str_replace('[', '', $newVal);
                  $newVal = str_replace(']', '', $newVal);
                ?>
              
               <b><?php echo $node->nodeName ?></b>
              <?php }  
                   if ($node->hasChildNodes()) {
                  foreach ($node->childNodes as $child){
                      $this->treeNode($child);
                  }
                  //get all attributes
                  if($node->hasAttributes()){
                    for ($i = 0; $i <= $node->attributes->length; ++$i) {
                      $attr_nodes = $node->attributes->item($i);
                      if($attr_nodes->nodeName && $attr_nodes->nodeValue) 
                      $attrs[$node->nodeName][$attr_nodes->nodeName] = $attr_nodes->nodeValue;
                    }
                  }    
                  //get all attributes
                  if($node->nodeValue || $node->nodeValue == 0){ 
                    if($node->childNodes->length == 1){?>
             
              <<?php echo $node->nodeName ?>><?php echo $node->nodeValue ?><<?php echo '/'.$node->nodeName ?>><?php
              
               }
                  }
                 }  
                if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ ?>
              <b><?php echo '/'.$node->nodeName ?></b>
             
              <?php }
                 }
				
		}
	

    /**
	 * Get xml rows count.
	 * @param  string $eventFile - path to file
	 * @return int
	 */
    public function get_xml_count($eventFile , $tagname){
        //$tagname = "item";
        $doc = new \DOMDocument();
        $doc->load($eventFile);
        $nodes=$doc->getElementsByTagName($tagname);
        $total_row_count = $nodes->length;
        return $total_row_count;	
    }
}
