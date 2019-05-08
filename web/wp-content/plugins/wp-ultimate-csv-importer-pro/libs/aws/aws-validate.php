<?php
require_once SM_UCI_PRO_DIR.'libs/aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
$s3Client = new S3Client([
    'region' => $region,
    'version' => 'latest',
     'credentials' => [
        'key'         => $key,
        'secret'      => $secretkey
    ]
]);

try {
	$buckets = $s3Client->listBuckets();
	foreach ($buckets['Buckets'] as $bucket){
		 $allbuckets[] = $bucket['Name'];
	}
	print_r(json_encode($allbuckets));
	update_option('show_bucket' , 'block');
	update_option('get_buckets' , $allbuckets);
	$response = 'success';
}
catch (Exception $e){
	  print_r('failure');
	  update_option('show_bucket' , 'none');
	  $response = 'failure';
}
?>
