<?php
# version 20160226

class CheckCacheFolder {
  /* internal */
  private $cacheFolder = "";
  private $cacheBaseFolder = "";
  private $errormsg = "";

  public function __construct($cacheBaseFolder, $cacheFolder){
    $this->cacheFolder = $cacheFolder;
    $this->cacheBaseFolder = $cacheBaseFolder;
    $this->checkCacheFolder();
	}

  /* decodeFeedData: convert raw-json-data into array */
	private function checkCacheFolder() {
        # wp version 4.4.2 and later: "/cache" is not created at install, so the plugin has to check and create...
        if (!is_dir($this->cacheBaseFolder)) {
          $mkdirError = @mkdir($this->cacheBaseFolder);
          if (!$mkdirError) {
            # mkdir failed, usually due to missing write-permissions
            $this->errormsg .= "<hr><b>caching not working, plugin aborted:</b><br>";
            $this->errormsg .= "plugin / wordpress / webserver can't create<br><i>".$this->cacheBaseFolder."</i><br>";
            $this->errormsg .= "therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)<hr>";
            # abort: no caching possible
            #exit;
            return $this->errormsg;
          }
        }

        if (!is_dir($this->cacheFolder)) {
          # $this->cacheFolder is no dir: not existing
          # try to create $this->cacheFolder
          $mkdirError = @mkdir($this->cacheFolder);
          if (!$mkdirError) {
            # mkdir failed, usually due to missing write-permissions
            $this->errormsg .= "<hr><b>caching not working, plugin aborted:</b><br>";
            $this->errormsg .= "plugin / wordpress / webserver can't create<br><i>".$this->cacheFolder."</i><br>";
            $this->errormsg .= "therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)<hr>";
            # abort: no caching possible
            #exit;
            return $this->errormsg;
          }
        }
        # $this->cacheFolder writeable?
        if (!is_writeable($this->cacheFolder)) {
          $this->errormsg .= "please check cacheFolder:<br>".$this->cacheFolder."<br>is not writable. Please change permissions.";
          #exit;
          return $this->errormsg;
        }
	}
}



class JSONdecode {
  /* internal */
  private $jsondata = "";
  private $feedData = "";
  private $isAllOk = TRUE;
  private $errormsg = "";

  public function __construct($feedData){
    $this->feedData = $feedData;
    $this->jsondata = "";
    #$this->decodeFeedData();
    $this->isAllOk = $this->decodeFeedData();
	}

  /* decodeFeedData: convert raw-json-data into array */
	public function decodeFeedData() {
	 if(empty($this->feedData)) {
       return FALSE;
   } else {
	    $this->jsondata =  json_decode($this->feedData);
      if (is_null($this->jsondata)) {
      # utf8_encode JSON-datastring, then try json_decode again
    	$this->jsondata =  json_decode(utf8_encode($this->feedData));
      if (is_null($this->jsondata)) {
        #echo "JSON-Decoding failed. Check structure and encoding if JSON-data.";
         return FALSE;
      }
      return TRUE;
     }
     return TRUE;
	 }
   return FALSE;
  }
  /* get */
	public function getJsondata() {
    return $this->jsondata;
  }
  /* get */
	public function getIsAllOk() {
    return $this->isAllOk;
  }
}

class FileLoadWithCache {
  /* internal */
  private $feedData = "";
  private $urlgettimeout = 5; # 5 seconds default timeout for get of JSON-URL
  private $cacheEnable = "";
  private $cacheFile = "";
  private $debugmode = 0;
  private $feedUrl = "";
  private $cacheExpireTime = 0;
  private $cacheWritesuccess = FALSE;
  private $oauth_bearer_access_key = "";
  private $http_header_default_useragent_flag = 0;
  private $allok = TRUE;
  private $errormsg = "";
  private $debugmessage = "";

  public function __construct($feedUrl, $urlgettimeout, $cacheEnable, $cacheFile, $cacheExpireTime, $oauth_bearer_access_key, $http_header_default_useragent_flag, $debugmode){
    $this->debugmode = $debugmode;
    $this->cacheEnable = $cacheEnable;
    if (is_numeric($urlgettimeout) && $urlgettimeout>=0) {
      $this->urlgettimeout = $urlgettimeout;
    }
    $this->cacheFile = $cacheFile;
    $this->feedUrl = $feedUrl;
    $this->cacheExpireTime = $cacheExpireTime;
    $this->oauth_bearer_access_key = $oauth_bearer_access_key;
    $this->http_header_default_useragent_flag = $http_header_default_useragent_flag;
	}

  /* get */
	public function getFeeddata() {
    return $this->feedData;
  }

  /* get errorlevel */
	public function getAllok() {
    return $this->allok;
  }

		public function getdebugmessage(){
      return $this->debugmessage;
    }
    
		private function showdebugmessage($message){
      if ($this->debugmode!=10) {
        return "";
      }
      $this->debugmessage .=  "DEBUG: $message<br>";
      #echo "DEBUG: $message<br>";
    }

    /* retrieveJsonData: get json-data and build json-array */
		public function retrieveJsonData(){
      # check cache: is there a not expired file?
			if ($this->cacheEnable) {
        # use cache
        if ($this->isCacheFileExpired()) {
          # get json-data from cache
          #$this->retrieveFeedFromCache();
          $this->retrieveFeedFromCache();
        } else {
          $this->retrieveFeedFromWeb();
        }
      } else {
        # no use of cache OR cachefile expired: retrieve json-url
        $this->retrieveFeedFromWeb();
      }

  		if(empty($this->feedData)) {
        $this->errormsg .= "error: get of json-data failed - plugin aborted: check url of json-feed";
        $this->showdebugmessage("no data received: check url of json-feed");
        $this->allok = FALSE;
        return "";
      }
		}

      /* isCacheFileExpired: check if cache enabled, if so: */
		public function isCacheFileExpired(){
			# get age of cachefile, if there is one...
      if (file_exists($this->cacheFile)) {
        $ageOfCachefile = filemtime($this->cacheFile);  # time of last change of cached file
      } else {
        # there is no cache file yet
        return FALSE;
      }

      # if $ageOfCachefile is < $cacheExpireTime use the cachefile:  isCacheFileExpired = FALSE
      if ($ageOfCachefile < $this->cacheExpireTime) {
        return FALSE;
      } else {
        return TRUE;
      }
		}

    /* storeFeedInCache: store retrieved data in cache */
		private function storeFeedInCache(){
		  if (!$this->cacheEnable) {
        # no use of cache if cache is not enabled or not working
        return NULL;
      }
      $handle = fopen($this->cacheFile, 'w');
			if(isset($handle) && !empty($handle)){
				$this->cacheWritesuccess = fwrite($handle, $this->feedData); # false if failed
				fclose($handle);
        if (!$this->cacheWritesuccess) {
          $this->debugmessage .= "cache-error:<br>".$this->cacheFile."<br>can't be stored - plugin aborted";
          $this->showdebugmessage("cache-error:<br>JSON-data can't be stored to ".$this->cacheFile);
          $this->allok = FALSE;
          return "";
        } else {
          $this->showdebugmessage("saving JSON-data to cache successful");
          return $this->cacheWritesuccess; # no of written bytes
        }
			} else {
        $this->debugmessage .= "cache-error:<br>".$this->cacheFile."<br>is either empty or unwriteable - plugin aborted";
        $this->showdebugmessage("cache-error:<br>".$this->cacheFile."<br>is either empty or unwriteable - plugin aborted");
        $this->allok = FALSE;
        return "";
      }
		}

		public function retrieveFeedFromWeb(){
      # wordpress unicodes http://openstates.org/api/v1/bills/?state=dc&q=taxi&apikey=4680b1234b1b4c04a77cdff59c91cfe7;
      # to  http://openstates.org/api/v1/bills/?state=dc&#038;q=taxi&#038;apikey=4680b1234b1b4c04a77cdff59c91cfe7
      # and the param-values are corrupted
      # un_unicode ampersand:
      $this->feedUrl = preg_replace("/&#038;/", "&", $this->feedUrl);
      $this->feedUrl = str_replace('&amp;', '&', $this->feedUrl);
      #$useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0"; # in case of simulating a browser
      if (empty($this->oauth_bearer_access_key)) {
        $args = array(
          'timeout'     => $this->urlgettimeout,
          #'httpversion' => '1.0',
          # 'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
          #'blocking'    => true,
          #'headers'     => array(),
          #'cookies'     => array(),
          #'body'        => null,
          #'compress'    => false,
          #'decompress'  => true,
          #'sslverify'   => true,
          #'stream'      => false,
          #'filename'    => null
        );
      } else {
        $acckey = $this->oauth_bearer_access_key;
        if (preg_match("/^nobearer /i", $acckey)) {
          $acckey = preg_replace("/^nobearer /i", "", $acckey);
          $header = $acckey;
        } else {
          $header = 'Bearer '.$acckey;
        }
        $args = array(
            'timeout'     => $this->urlgettimeout,
            'headers'     => array('Authorization' => $header),
      			'sslverify' => false
          );
      }
      if ($this->http_header_default_useragent_flag==1) {
        $args{'user-agent'} = 'JCI WordPress-Plugin - free Version';
      }
      $this->showdebugmessage("wp_remote_get to ".$this->feedUrl);
      $this->showdebugmessage("arguments: ".print_r($args, TRUE));

      $response = wp_remote_get($this->feedUrl, $args);
      if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        $this->errormsg .= "Something went wrong fetching URL with JSON-data: $error_message";
        $this->showdebugmessage("error getting URL: ". $error_message);
        $this->allok = FALSE;
      } else if(isset($response['body']) && !empty($response['body'])){
				$this->feedData = $response['body'];
        $this->storeFeedInCache();
        $this->showdebugmessage("success getting URL");
			}
		}

    /* retrieveFeedFromCache: get cached filedata  */
		public function retrieveFeedFromCache(){
			if(file_exists($this->cacheFile)) {
        $this->feedData = file_get_contents($this->cacheFile);
      } else {
        # get from cache failed, try from web
        $this->retrieveFeedFromWeb();
      }
		}
}
?>