<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly


class SmackUCIFtpHandler {

	public static function init() {
		$ftp_records = array();
		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$post_values = isset($_POST['postdata']) ? $_POST['postdata'] : array();
		#print_r($post_values);
		parse_str($post_values, $ftp_records);
		$ftp_file_name = basename($ftp_records['host_path']);
		$port = isset($ftp_records['host_port']) ? $ftp_records['host_port'] : 21;
		$server = isset($ftp_records['host_name']) ? $ftp_records['host_name'] : '';
		$file_path = isset($ftp_records['host_path']) ? $ftp_records['host_path'] : '';
		$username = isset($ftp_records['host_username']) ? $ftp_records['host_username'] : '';
		$password = isset($ftp_records['host_password']) ? $ftp_records['host_password'] : '';
		$result = self::ftpfile_handling($server,$port,$username,$password,$ftp_file_name,$file_path);
		echo json_encode($result);die;

	}
	public static function ftpfile_handling($server, $port, $username, $password, $ftp_file_name, $file_path) {

		global $uci_admin;
		global $wpdb;
		$ftp = new SmackUCISFTP($server, $username, $password, $port);
		try {
			// connect to FTP server
			if($ftp->connect()) {
				$path = explode($ftp_file_name, $file_path);
			} else {
				throw new Exception("Connection failed: " . $ftp->error);
			}
			$path = isset($path[0]) ? $path[0] : '';

			$file_extension = pathinfo($ftp_file_name, PATHINFO_EXTENSION);
			$file_extn = '.' . $file_extension;
			$get_local_filename = explode($file_extn, $ftp_file_name);
			
			$all_file_names = $wpdb->get_results($wpdb->prepare("select id from smackuci_events where original_file_name = %s order by id desc limit 1", $ftp_file_name));
			if($all_file_names){
				if (is_array($all_file_names) && isset($all_file_names[0]->id)) {
					$last_version_id = $all_file_names[0]->revision;
					$version = $last_version_id + 1;
					$local_file_name = $get_local_filename[0] . '-' . $version . $file_extn;
				}
			} else {
				$local_file_name = $get_local_filename[0] . '-1' . $file_extn;
				$version = '1';
			}

			//create event_key
			$event_key = $uci_admin->convert_string2hash_key($local_file_name);
			$local_dir = SM_UCI_IMPORT_DIR . '/' . $event_key;

			if(!is_dir($local_dir)) {
				wp_mkdir_p($local_dir);
				chmod($local_dir, 0777);
			}
			$local_file = SM_UCI_IMPORT_DIR . '/' . $event_key . '/' . $event_key;

			chmod($local_file, 0777);

			//file extension check
			if ( ! preg_match('%\W(txt|csv|xml|zip)$%i', trim($ftp_file_name))) {
				throw new Exception('Unsupported file format');
			}

			// download a file from FTP server
			// will download file "somefile.php" and
			// save locally as "localfile.php"

			if($ftp->get($ftp_file_name, $local_file)) {
				
				$filesize = filesize($local_file);
				if ($filesize > 1024 && $filesize < (1024 * 1024)) {
					$fileSize = round(($filesize / 1024), 2) . ' kb';
				} else {
					if ($filesize > (1024 * 1024)) {
						$fileSize = round(($filesize / (1024 * 1024)), 2) . ' mb';
					} else {
						$fileSize = $filesize . ' byte';
					}
				}

				//mime type check
				$mime = mime_content_type($local_file);
				$filemimes = array('text/csv','text/plain');
				// if(!in_array($mime,$filemimes)){
				// 	throw new Exception('Unsupported file format');
				// }
				//mime type check
				//file size check with upload_max_size
                                $upload_max_size = $uci_admin->get_config_bytes(ini_get('upload_max_filesize'));
                                if ($upload_max_size && ($filesize > $upload_max_size)) {
                                        throw new Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini');
                                }
                                //file size check with upload_max_size
				//csv validation
                                if($file_extension == 'csv' || $file_extension == 'txt'){
                                      $valid_csv = $uci_admin->CheckCSV($local_file);
                                      $returnData['isutf8'] = $valid_csv['isutf8'];
                                      $returnData['isvalid'] = $valid_csv['isvalid'];
                                      if($valid_csv['isvalid'] == 'No'){
                                        throw new Exception('Your csv file columns and values are mismatch');
                                      }
                                }
                                //csv validation
				$returnData['filename'] = $ftp_file_name;
				$returnData['uploaded_name'] = $local_file_name;
				$returnData['version'] = $version;
				$returnData['filesize'] = $fileSize;
				$returnData['eventkey'] = $event_key;
				$returnData['Success'] = 'Success!';
			} else {				
				throw new Exception("Download failed: " . $ftp->error);
			}

		} catch (Exception $e) {
			$returnData["Failure"] = $e->getMessage();
		}
		return $returnData;
	}

}
if(isset($_POST['postdata'])) {
	SmackUCIFtpHandler::init();
}

class SmackUCISFTP {
	/**
	 * FTP host
	 *
	 * @var string $_host
	 */
	private $_host;

	/**
	 * FTP port
	 *
	 * @var int $_port
	 */
	private $_port = 21;

	/**
	 * FTP password
	 *
	 * @var string $_pwd
	 */
	private $_pwd;

	/**
	 * FTP stream
	 *
	 * @var resource $_id
	 */
	private $_stream;

	/**
	 * FTP timeout
	 *
	 * @var int $_timeout
	 */
	private $_timeout = 90;

	/**
	 * FTP user
	 *
	 * @var string $_user
	 */
	private $_user;

	/**
	 * Last error
	 *
	 * @var string $error
	 */
	public $error;

	/**
	 * FTP passive mode flag
	 *
	 * @var bool $passive
	 */
	public $passive = false;

	/**
	 * SSL-FTP connection flag
	 *
	 * @var bool $ssl
	 */
	public $ssl = false;

	/**
	 * System type of FTP server
	 *
	 * @var string $system_type
	 */
	public $system_type;

	/**
	 * Initialize connection params
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param int $port
	 * @param int $timeout (seconds)
	 */
	public function  __construct($host = null, $user = null, $password = null, $port = 21, $timeout = 90) {
		$this->_host = $host;
		$this->_user = $user;
		$this->_pwd = $password;
		$this->_port = (int)$port;
		$this->_timeout = (int)$timeout;
	}

	/**
	 * Auto close connection
	 */
	public function  __destruct() {
		$this->close();
	}

	/**
	 * Close FTP connection
	 */
	public function close() {
		// check for valid FTP stream
		if($this->_stream) {
			// close FTP connection
			ftp_close($this->_stream);

			// reset stream
			$this->_stream = false;
		}
	}

	/**
	 * Connect to FTP server
	 *
	 * @return bool
	 */
	public function connect() {
		// check if non-SSL connection
		if(!$this->ssl) {
			// attempt connection
			if(!$this->_stream = ftp_connect($this->_host, $this->_port, $this->_timeout)) {
				// set last error
				$this->error = "Failed to connect to {$this->_host}";
				return false;
			}
			// SSL connection
		} elseif(function_exists("ftp_ssl_connect")) {
			// attempt SSL connection
			if(!$this->_stream = ftp_ssl_connect($this->_host, $this->_port, $this->_timeout)) {
				// set last error
				$this->error = "Failed to connect to {$this->_host} (SSL connection)";
				return false;
			}
			// invalid connection type
		} else {
			$this->error = "Failed to connect to {$this->_host} (invalid connection type)";
			return false;
		}

		// attempt login
		if(ftp_login($this->_stream, $this->_user, $this->_pwd)) {
			// set passive mode
			ftp_pasv($this->_stream, (bool)$this->passive);

			// set system type
			$this->system_type = ftp_systype($this->_stream);

			// connection successful
			return true;
			// login failed
		} else {
			$this->error = "Failed to connect to {$this->_host} (login failed)";
			return false;
		}
	}

	/**
	 * Download file from server
	 *
	 * @param string $remote_file
	 * @param string $local_file
	 * @param int $mode
	 * @return bool
	 */
	public function get($remote_file = null, $local_file = null, $mode = FTP_ASCII) {
		// ASCII
		// Mari changed ascii to ftp_ascii
		// attempt download
		if(ftp_get($this->_stream, $local_file, $remote_file, $mode)) {
			// success
			return true;
			// download failed
		} else {
			$this->error = "Failed to download file \"{$remote_file}\"";
			return false;
		}
	}

	/**
	 * Download file from server
	 *
	 * @param string $remote_file
	 * @param string $local_file
	 * @param int $mode
	 * @return bool
	 */
	public function put($remote_file = null, $local_file = null, $mode = FTP_ASCII) {
		// attempt download
		if(ftp_put($this->_stream, $remote_file, $local_file, $mode)) {
			// success
			return true;
			// download failed
		} else {
			$this->error = "Failed to download file \"{$remote_file}\"";
			return false;
		}
	}

	/**
	 * Get list of files/directories in directory
	 *
	 * @param string $directory
	 * @return array
	 */
	public function ls($directory = null) {
		$list = array();

		// attempt to get list
		if($list = ftp_nlist($this->_stream, $directory)) {
			// success
			return $list;
			// fail
		} else {
			$this->error = "Failed to get directory list";
			return array();
		}
	}

}
