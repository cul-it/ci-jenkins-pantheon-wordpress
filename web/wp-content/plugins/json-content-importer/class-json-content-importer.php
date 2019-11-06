<?php
/*
CLASS JsonContentImporter
Description: Class for WP-plugin "JSON Content Importer"
Version: 1.2.19
Author: Bernhard Kux
Author URI: https://www.kux.de/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


class JsonContentImporter {

    /* shortcode-params */		
    private $numberofdisplayeditems = -1; # -1: show all
    private $feedUrl = ""; # url of JSON-Feed
    private $urlgettimeout = 5; # 5 sec default timeout for http-url
    private $basenode = ""; # where in the JSON-Feed is the data? 
    private $debugmode = 0; # 10: show ebug-messages
    private $oneofthesewordsmustbein = ""; # optional: one of these ","-separated words have to be in the created html-code
    private $oneofthesewordsmustbeindepth = 1; # optional: one of these ","-separated words have to be in the created html-code
    private $oneofthesewordsmustnotbeIn = ""; # optional: one of these ","-separated words must NOT in the created html-code
    private $oneofthesewordsmustnotbeindepth = 1; # optional: one of these ","-separated words must NOT to in the created html-code

    /* plugin settings */
    private $isCacheEnable = FALSE;
 
    /* internal */
		private $cacheFile = "";
		private $jsondata;
		private $feedData  = "";
 		private $cacheFolder;
    private $datastructure = "";
    private $triggerUnique = NULL;
    private $cacheExpireTime = 0;
    private $oauth_bearer_access_key = "";
    private $http_header_default_useragent_flag = 0;
    private $debugmessage = "";


		public function __construct(){  
			 add_shortcode('jsoncontentimporter' , array(&$this , 'shortcodeExecute')); # hook shortcode
		}
    

		private function showdebugmessage($message, $showDEBUG=TRUE){
      if ($this->debugmode!=10) {
        return "";
      }
      if ($showDEBUG) {
        $this->debugmessage .= "DEBUG: ";
      }
      $this->debugmessage .= "$message<br>";
    }
    
    /* shortcodeExecute: read shortcode-params and check cache */
		public function shortcodeExecute($atts , $content = ""){
			
      extract(shortcode_atts(array(
        'url' => '',
        'urlgettimeout' => '',
        'numberofdisplayeditems' => '',
        'oneofthesewordsmustbein' => '',
        'oneofthesewordsmustbeindepth' => '',
        'oneofthesewordsmustnotbein' => '',
        'oneofthesewordsmustnotbeindepth' => '',
        'basenode' => '',
        'debugmode' => '',
      ), $atts));

      if ($debugmode==10) {
        $this->debugmode = $debugmode;
      }
      
      $this->feedUrl = $this->removeInvalidQuotes($url);
      $this->oneofthesewordsmustbein = $this->removeInvalidQuotes($oneofthesewordsmustbein);
      $this->oneofthesewordsmustbeindepth = $this->removeInvalidQuotes($oneofthesewordsmustbeindepth);
      $this->oneofthesewordsmustnotbein = $this->removeInvalidQuotes($oneofthesewordsmustnotbein);
      $this->oneofthesewordsmustnotbeindepth = $this->removeInvalidQuotes($oneofthesewordsmustnotbeindepth);
      /* caching or not? */
      if (
          (!class_exists('FileLoadWithCache'))
          || (!class_exists('JSONdecode'))
      ) {
        require_once plugin_dir_path( __FILE__ ) . '/class-fileload-cache.php';
      }
			if (get_option('jci_enable_cache')==1) {
        # 1 = checkbox "enable cache" activ
        $this->cacheEnable = TRUE;
        # check cacheFolder
        $this->cacheFolder = WP_CONTENT_DIR.'/cache/jsoncontentimporter/';
        $checkCacheFolderObj = new CheckCacheFolder(WP_CONTENT_DIR.'/cache/', $this->cacheFolder);

        # cachefolder ok: set cachefile
  			$this->cacheFile = $this->cacheFolder . sanitize_file_name(md5($this->feedUrl)) . ".cgi";  # cache json-feed
      } else {
        # if not=1: no caching
        $this->cacheEnable = FALSE;
      }

      /* set other parameter */      
      if ($numberofdisplayeditems>=0) {
        $this->numberofdisplayeditems = $this->removeInvalidQuotes($numberofdisplayeditems);
      }
      if (is_numeric($urlgettimeout) && ($urlgettimeout>=0)) {
        $this->urlgettimeout = $this->removeInvalidQuotes($urlgettimeout);
      }

      /* cache */
      $this->cacheEnable = FALSE;
      if (get_option('jci_enable_cache')==1) {
        $this->cacheEnable = TRUE;
        $this->showdebugmessage("Cache is active");
      } else {
        $this->showdebugmessage("Cache is NOT active");
      }
      $cacheTime = get_option('jci_cache_time');  # max age of cachefile: if younger use cache, if not retrieve from web
			$format = get_option('jci_cache_time_format');
      $cacheExpireTime = strtotime(date('Y-m-d H:i:s'  , strtotime(" -".$cacheTime." " . $format )));
      $this->cacheExpireTime = $cacheExpireTime;
      if ($this->cacheEnable) {
        $this->showdebugmessage("CacheExpireTime: ".$cacheTime." $format");
      }

      $this->oauth_bearer_access_key = get_option('jci_oauth_bearer_access_key');
      $this->http_header_default_useragent_flag = get_option('jci_http_header_default_useragent');

      if (""==$this->feedUrl) {
        $errormsg = "No URL defined: Check the shortcode - one typical error: is there a blank after url= ?";
        $rdamtmp = $this->debugmessage.$errormsg;
  			return apply_filters("json_content_importer_result_root", $rdamtmp);
      } else {
        $this->showdebugmessage("try to retieve this url: ".$this->feedUrl);
      }

      $fileLoadWithCacheObj = new FileLoadWithCache($this->feedUrl, $this->urlgettimeout, $this->cacheEnable, $this->cacheFile,
      $this->cacheExpireTime, $this->oauth_bearer_access_key, $this->http_header_default_useragent_flag, $this->debugmode);
      
      $fileLoadWithCacheObj->retrieveJsonData();
      $this->feedData = $fileLoadWithCacheObj->getFeeddata();
      $this->showdebugmessage($fileLoadWithCacheObj->getdebugmessage(), FALSE);
      if (""==$this->feedData) {
        $errormsg = "EMPTY api-answer: No JSON received - is the API down? Check the URL you use in the shortcode!";
        $rdamtmp = $this->debugmessage.$errormsg;
  			return apply_filters("json_content_importer_result_root", $rdamtmp);
      } else {
        $inspurl = "https://jsoneditoronline.org";
        $this->buildDebugTextarea("api-answer:<br>Inspect JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to <a href=\"".$inspurl."\" target=_blank>https://jsoneditoronline.org</a>):", $this->feedData);
      }
			# build json-array
      $jsonDecodeObj = new JSONdecode($this->feedData);
      $this->jsondata = $jsonDecodeObj->getJsondata();


      $this->basenode = $this->removeInvalidQuotes($basenode);
      $this->showdebugmessage("basenode: ".$basenode);
      
      $this->datastructure = preg_replace("/\n/", "", $content);
      $outdata = htmlentities($this->datastructure);
      $this->buildDebugTextarea("template:", $outdata);
      
      require_once plugin_dir_path( __FILE__ ) . '/class-json-parser.php';
      $JsonContentParser = new JsonContentParser123($this->jsondata, $this->datastructure, $this->basenode, $this->numberofdisplayeditems,
            $this->oneofthesewordsmustbein, $this->oneofthesewordsmustbeindepth,
            $this->oneofthesewordsmustnotbein, $this->oneofthesewordsmustnotbeindepth);
      $rdam = $JsonContentParser->retrieveDataAndBuildAllHtmlItems();
      $outdata = htmlentities($rdam);
      $parseMsg = $JsonContentParser->getErrorDebugMsg();
      $this->showdebugmessage($parseMsg);
      $this->buildDebugTextarea("result:", $outdata);
      $rdamtmp = $this->debugmessage.$rdam;
			return apply_filters("json_content_importer_result_root", $rdamtmp);
		}

    private function buildDebugTextarea($message, $txt, $addline=FALSE) {
        $norowsmax = 20;
        $norows = $norowsmax; 
        $strlentmp = round(strlen($txt)/90);
        if ($strlentmp<20) {
          $norows = $strlentmp;
        }
        $nooflines = substr_count($txt, "\n");
        if ($nooflines > $norows) {
          $norows = $nooflines;
        }
        if ($norows > $norowsmax) {
          $norows = $norowsmax;
        }
        $norows = $norows + 2;
        $out = $message."<br><textarea rows=".$norows." cols=90>".$txt."</textarea>";
        if ($addline) {
          $out .= "<hr>";
        }
        $this->showdebugmessage($out);
    }

    private function removeInvalidQuotes($txtin) {
      $invalid1 = urldecode("%E2%80%9D");
      $invalid2 = urldecode("%E2%80%B3");
      $txtin = preg_replace("/^[".$invalid1."|".$invalid2."]*/i", "", $txtin);
      $txtin = preg_replace("/[".$invalid1."|".$invalid2."]*$/i", "", $txtin);
      return $txtin;
    }

}
?>