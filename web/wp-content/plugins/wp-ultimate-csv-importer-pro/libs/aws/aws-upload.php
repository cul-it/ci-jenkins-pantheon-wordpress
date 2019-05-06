<?php

class uci_aws_s3_helper{
	
	public function __construct(){


	}


	public function aws_image_upload($postID,$fimg_path,$fimg_name,$uciEventLogger){
		$key = get_option('aws_key');
		$secretkey = get_option('aws_secret_key');
		$region = get_option('aws_region');
		$bucket = get_option('aws_bucket_name');
		if (strpos($_SERVER['PHP_SELF'], 'admin-ajax.php') !== false) {
			$path = explode("wp-admin/admin-ajax.php",$_SERVER['PHP_SELF']);
		}else{
			$path = explode("wp-cron.php",$_SERVER['PHP_SELF']);
		}
		$path = ltrim($path[0], '/');
		$s3 = [
			'version'     => 'latest',
			'region'      => $region,
			'credentials' => [
			'key'         => $key,
			'secret'      => $secretkey
		    ]
		];
		$sdk = new Aws\Sdk($s3);
		$s3Client = $sdk->createS3();
		//use Aws\S3\MultipartUploader;
		//use Aws\Exception\MultipartUploadException;
		$year = date("Y");
		$month = date("m");
		$uploader = new Aws\S3\MultipartUploader($s3Client, $fimg_path, [
		    'bucket' => $bucket,
		    'ACL'          => 'public-read',
		    'key'    => $path.$year.'/'.$month.'/'.$postID.'/'. $fimg_name,
		]);
		try{
			$result = $uploader->upload();
		  /*$res = $s3Client->putObjectAcl([
				       'ACL' => 'public-read',
				       'Bucket' => $bucket,
				       'Key' => $year.'/'.$month.'/'.$fimg_name,
				   ]);*/
			$s3imgurl = $result['ObjectURL'];
			delete_option('smack_featured_' . $postID);
		} catch (Aws\Exception\MultipartUploadException $e) {
			 $eventLog = $e->getMessage() . "\n";
			 $eventLogFile = SM_UCI_DEBUG_LOG;
			 fopen($eventLogFile , 'w+');
			 $uciEventLogger->lfile("$eventLogFile");
			 $uciEventLogger->lwrite($eventLog);
		}
		return $s3imgurl;
	}
}
?>
