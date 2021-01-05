<?php
/*
CLASS JsonContentParser
Description: Basic template engine Class: building code with JSON-data and template markups 
Version: 20200719
Author: Bernhard Kux
Author URI: https://www.kux.de/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


class JsonContentParser123 {

    /* shortcode-params */		
		private $jsondata = "";
    private $datastructure = "";
    private $basenode = ""; 
    private $numberofdisplayeditems = -1; # -1: show all
    private $oneOfTheseWordsMustBeIn = "";
    private $oneOfTheseWordsMustBeInDepth = 1;
    private $oneOfTheseWordsMustNotBeIn = "";
    private $oneOfTheseWordsMustNotBeInDepth = 1;
    private $errormsg = "";

    /* internal */
    private $showDebugMessages = FALSE; # set TRUE in constructor for debugging
    private $triggerUnique = NULL;
    private $subLoopParamArr = NULL;
    #private $regExpPatternDetect = "([a-zA-Z0-9,;\_\-\:\,\<\>\/ ]*)"; prior to vers 1.2.7
    private $regExpPatternDetect = "([a-zA-Z0-9\=\",;\_\-:!\*\,\<\>\/ ]+)";
    private $addToResult = TRUE;

		public function __construct($jsonData, $datastructure, $basenode, $numberofdisplayeditems,
          $oneOfTheseWordsMustBeIn, $oneOfTheseWordsMustBeInDepth,
          $oneOfTheseWordsMustNotBeIn, $oneOfTheseWordsMustNotBeInDepth
          ){
      #$this->showDebugMessages = TRUE; # sometimes helpful
      if (is_numeric($numberofdisplayeditems)) {     
        $this->numberofdisplayeditems = $numberofdisplayeditems;
      }
      $this->oneOfTheseWordsMustBeIn = $oneOfTheseWordsMustBeIn;
      if (is_numeric($oneOfTheseWordsMustBeInDepth)) {
        $this->oneOfTheseWordsMustBeInDepth = $oneOfTheseWordsMustBeInDepth;
      }
      $this->oneOfTheseWordsMustNotBeIn = $oneOfTheseWordsMustNotBeIn;
      if (is_numeric($oneOfTheseWordsMustNotBeInDepth)) {
        $this->oneOfTheseWordsMustNotBeInDepth = $oneOfTheseWordsMustNotBeInDepth;
      }
      $this->jsondata = $jsonData;
      $this->datastructure = $datastructure;
      $this->datastructure = preg_replace("/\n/", "", $this->datastructure); # remove linefeeds from template
      $this->basenode = $basenode;
      $this->output = "";
		}
    
    /* retrieveDataAndBuildAllHtmlItems: get json-data, build html*/
		public function retrieveDataAndBuildAllHtmlItems(){
      $jsonTree = $this->jsondata;
      $baseN = $this->basenode;
      $this->debugEcho("<hr>basenode: $baseN<br>");
      if ($baseN!="") {
        $baseNArr = explode(".", $baseN);  # path of basenode: separator is "."
        foreach($baseNArr as $key => $valin) {
          $val = $valin;
          if (is_object($jsonTree)) {
            @$jsonTree = $jsonTree->$val;
          } else if (is_array($jsonTree)){
           foreach($jsonTree as $jsonTreekey => $jsonTreeval) {
              if (is_object($jsonTreeval)) {
                $test = $jsonTree[$jsonTreekey]->$val;
                if (!is_null($test)) {
                  $jsonTree1 = $jsonTree[$jsonTreekey];
                }
              } else {
                # not implemented yet: uncool, but possible - why not another array
                $this->debugEcho("<hr>".__('double-array at root? not implemented yet', 'json-content-importer')."<hr>", "wordpressticket");
              }
            }
          } else {
            # neither object nor array? not implemented yet: should never happen
            $this->debugEcho("<hr>".__('neither object nor array? not implemented yet', 'json-content-importer')."<hr>", "wordpressticket");
          }
        }
      }
      
      $this->debugEcho(__('basic entry with', 'json-content-importer').": <i>".gettype($jsonTree)."</i><br>");
      
      # $jsonTree has to be object or array
      if (!is_object($jsonTree) && !is_array($jsonTree)) {
        $this->debugEcho("<b>".__('We have a Problem with JSON here', 'json-content-importer').":</b><br>".__('Either we got no JSON from the API. Or the basenode-parameter is not ok.', 'json-content-importer')."<br>".__('Switch on the Debugmode of the Plugin!', 'json-content-importer'), "wordpressticket");
        return $this->errormsg;
        #exit;
      }

      # start parsing
      $startdepth = 0;
      $resultArr = $this->checkType($jsonTree, gettype($jsonTree), $this->datastructure, "", $startdepth, "", $this->numberofdisplayeditems);
      $finalText = $this->clearUnusedArrayDatafields($resultArr[1]);
      return trim($finalText);
		}


     private function checkType($jsonIn, $type, $template, $node2check, $depth, $keyIn, $noofDisplayedItems=-1) {
        $result = "";
        $depth++;
        $counter = 0;
        $loopcounter = 0;

        @$keypass .= $keyIn.".".$node2check;
        $keypass = preg_replace("/^\./", "", $keypass);
        $keypass = preg_replace("/\.$/", "", $keypass);
        $keypass = preg_replace("/\.\./", ".", $keypass);

        $this->debugEcho( "<hr><font color=blue>".__('ENTER function checkType', 'json-content-importer')." // depth: <i>$depth</i> // type: <i>$type</i> // keyIn: <i>$keypass</i> // node2check: <i>$node2check</i> // noofDisplayedItems: <i>$noofDisplayedItems</i> // template: <i>".htmlentities($template)."</i>");
        $this->debugEcho("<br> // json-in: ", "showdump", $jsonIn);
        $this->debugEcho( "</font><br><font color=green>".__('start loop', 'json-content-importer')."</font><br>");

        foreach($jsonIn as $key => $val) {
          $loopcounter++;
          if (is_object($val)) {
            $this->debugEcho( __('object found', 'json-content-importer').": depth: <i>$depth</i> // loop: <i>$loopcounter</i> // key:  <i>$key</i> // type: <i>$type</i> // template: <i>".htmlentities($template)."</i> // node2check: <i>$node2check</i> // ");
	    
            $this->debugEcho( __('json in loop', 'json-content-importer').": ", "showdump", $val);
            if (is_numeric($noofDisplayedItems) && ($noofDisplayedItems>0) && is_numeric($key)) {
              $counter++;
              if ($counter > $noofDisplayedItems) {
                continue;
              }
            }
            if ($type=="array") {
              list($returnHTMLinsideProc, $resultOfProcessedObjects, $noofItems) = $this->checkType($val, "object", $template, "", $depth, $keypass, $noofDisplayedItems);
              @$noofFoundItems++;
              $result .= $resultOfProcessedObjects;
            } else if (is_numeric($key)) {
              $this->debugEcho("num key:  <i>$key</i> // val: <i>".gettype($val)."</i><br>");
              if (is_object($val)) {
                list($returnHTMLinsideProc, $resultOfProcessedObjects, $noofItems) = $this->checkType($val, "object", $template, "", $depth, $keypass, $noofDisplayedItems);
                $result .= $resultOfProcessedObjects; ## concat needed for locations-json
              }
            } else {
              list($subloopNodeObj, $subLoopNumberObj, $subloopTemplate, $keypassreturn) = $this->process_subloop($template, $key, $keypass, $noofDisplayedItems);
              if ($subloopTemplate=="") {
                # no subloop: use template
                list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $template, $subloopNodeObj, $depth, $keypass, $subLoopNumberObj);
                $template = $resultFromSubloopprocessing;

              } else {
                if ($key==$subloopNodeObj || is_numeric($key)
                ) {
                  list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $subloopTemplate, $subloopNodeObj, $depth, $keypass, $subLoopNumberObj);
                  @$returnHTMLinsideProc = $this->replace_subloop($resultFromSubloopprocessing, $subloopNodeObj, $subLoopNumberObj, $subloopHTMLObj, $template, $keypass);
                  $template = $returnHTMLinsideProc;
                  $result = $template;
                } else {
                  $this->debugEcho(__('no match', 'json-content-importer')."<hr>");
                }
              }
            }
          } else if (is_array($val)) {
            $this->debugEcho("array ".__('found', 'json-content-importer').": key: <i>$key</i> // template: <i>".htmlentities($template)."</i> <br>// ");
            $this->debugEcho("jsininarray: ", "showdump", $val);
            list($subloopNode, $subLoopNumber, $subloopTemplate) = $this->process_subloop_array($template, $key, $keypass); # check on {subloop-array}
            if ($subloopTemplate=="") {
              $this->debugEcho("no {subloop-array}: ".__('loop array one by one', 'json-content-importer')."<br>");
              foreach($val as $keynosubloop => $valnosubloop) {
                list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($valnosubloop, gettype($valnosubloop), $template, "", $depth, $keypass, $subLoopNumber);
                $result = $resultFromSubloopprocessing;
              }
            } else if ($key==$subloopNode) {
              $this->debugEcho("subloopNode: <i>".htmlentities($subloopNode)."</i> // ".__('no', 'json-content-importer').": <i>".htmlentities($subLoopNumber)."</i> // html: <i>".htmlentities($subloopTemplate)."</i><br>");
              list($returnHTMLinsideProc, $resultFromSubloopArray, $noofItems) = $this->checkType($val, "array", $subloopTemplate, $subloopNode, $depth, $keypass, $subLoopNumber);
              if (preg_match("/{/", $resultFromSubloopArray)) {
			
                $resultFromSubloopArray = preg_replace("/{(.*?)}/i", "", $resultFromSubloopArray);
			
              }
              $template = $this->replace_subloop_array($resultFromSubloopArray, $subloopNode, $subLoopNumber, $subloopTemplate, $template, $keypass);
              $result = $template;
            }
          } else if (is_string($val) || is_numeric($val)) {
            if (
              ($type=="array") && is_numeric($key) && ($key >= $noofDisplayedItems)
              ){
              continue;
            }
            $valout = $val;
	          if (!isset($valout)) {
              $valout = "";
            }

            $template = $this->replacePattern($template, $key, $valout, $keypass);
            $result = $template;
          } else if (is_bool($val)) {
            if ($val) {
              $valout = "true";
            } else {
              $valout = "false";
            }
            $template = $this->replacePattern($template, $key, $valout, $keypass);
            $result = $template;
          } else if (is_null($val)) {
				$template = $this->replacePattern($template, $key, "", $keypass);
				$result = $template;
          }
        }
        $this->debugEcho( "<hr><font color=red>".__('LEAVE function checkType', 'json-content-importer').": // ".__('depth').": ".@$depth." // ".__('result')." :<i>".htmlentities($result)."</i><br>// ".__('noofItems').": <i>".@$noofItems."</i><br>// ".__('returnHTMLinsideProc').": <i>".@$returnHTMLinsideProc."</i><br></font>");
        if (
        $depth==$this->oneOfTheseWordsMustBeInDepth
        ||
        $depth==$this->oneOfTheseWordsMustNotBeInDepth
        ) {
          $result = $this->checkIfAddToResult($result);
        }
        return array (@$returnHTMLinsideProc, @$result, @$noofItems);
   }

    private function replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, $nameofsubloop) {
      if (is_numeric($subLoopNumber)) {
        $subLoopNumberPattern = $subLoopNumber;
      } else {
        $subLoopNumberPattern = 777;#"([0-9])"; ## to be fixed
      }
      if ($keypass=="") {
        $re = $subloopNode;
      } else {
        $re = $keypass.".".$subloopNode;
      }
      if ($nameofsubloop!="") {
        $nameofsubloopTmp = "-".$nameofsubloop;
      }
      @$sli = '/{subloop'.$nameofsubloopTmp.':'.$re.':'.$subLoopNumberPattern.'}(.*){\/subloop'.$nameofsubloopTmp.':'.$re.'}/i';
      $resulttmp = $this->preg_escape_dollar_slash($result);
      $ret = preg_replace($sli , $resulttmp , $datastructure);
      @$sli = '/{subloop'.$nameofsubloopTmp.':'.$re.':'.$subLoopNumberPattern.'}(.*){\/subloop'.$nameofsubloopTmp.'}/i';
      $ret = preg_replace($sli , $result , $ret);
      return $ret;
    }

    private function preg_escape_dollar_slash($string) {
      // handle $: switch $ to $ and \ to \\
      return preg_replace('/(\$|\\\\)/', '\\\\\1', $string);
    }

    private function replace_subloop_array($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass) {
      return $this->replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, "array");
    }
    private function replace_subloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass) {
      return $this->replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, "");
    }


    /* replacePattern: replace markup with data and do the specials like urlencode etc.*/
    private function replacePattern($datastructure, $pattern, $value, $keyIn) {
      $tmp = $this->replacePatternWithKeyin($datastructure, $pattern, $value, $keyIn);
      $tmp = $this->replacePatternWithKeyin($tmp, $pattern, $value, "");
      return $tmp;
    }

    private function value2html($valueIn) {
      ## reverse htmlentities($keyIn, ENT_QUOTES, "UTF-8", FALSE) from replacePatternWithKeyin
      $ret = $valueIn;
      $nbspReplacer = "ANDnbspSEMICOL";
      $ret = str_replace("&nbsp;", $nbspReplacer, $ret);
      $ret = html_entity_decode($ret, ENT_NOQUOTES, "UTF-8");
      $ret = str_replace($nbspReplacer, "&nbsp;", $ret);
      return $ret;
    }

    private function value2htmlAndLineFeed2LineFeed($valueIn) { # proversion
      $ret = preg_replace("/\n/", "<br>", $valueIn);
      $ret = $this->value2html($ret);
      return $ret;
    }

    private function replacePatternWithKeyin($datastructure, $pattern, $value, $keyIn) {
      # JSON data like { "$a": "$content", }
      $valueConv2Html = $value;
      if (function_exists('mb_check_encoding') && mb_check_encoding($valueConv2Html, 'UTF-8')) {
        $valueConv2Html = htmlentities($valueConv2Html, ENT_QUOTES, "UTF-8", FALSE); # convert to HTML
      }
      $valueConv2Html = preg_quote($valueConv2Html);  // put backslash pre of char in regex
      $value = preg_quote($value);  // put backslash pre of char in regex

      if (function_exists('mb_check_encoding') && mb_check_encoding($keyIn, 'UTF-8')) {
        $keyIn = htmlentities($keyIn, ENT_QUOTES, "UTF-8", FALSE); # convert to HTML
      }
      $pattern = preg_quote($pattern); // put backslash pre of char in regex

      if ($keyIn!="") {
         $pattern = $keyIn.".".$pattern;
      }
      if (is_numeric($pattern)) {  # preg_replace: trouble with pattern {0}
        $datastructure = str_replace("{".$pattern."}" , $valueConv2Html , $datastructure);
        $datastructure = str_replace("{".$pattern.":htmlAndLinefeed2htmlLinefeed}" , $this->value2htmlAndLineFeed2LineFeed($valueConv2Html) , $datastructure);    # proversion
        $datastructure = str_replace("{".$pattern.":html}" , $this->value2html($valueConv2Html) , $datastructure);
        $datastructure = str_replace("{".$pattern.":purejsondata}" , $value, $datastructure);
        $datastructure = str_replace("{".$pattern.":urlencode}" , urlencode(html_entity_decode($valueConv2Html)) , $datastructure);
      } else {
        $pattern = preg_replace("/\//", "\/", $pattern); # change "aa/aa" to "aa\/aa"
        $datastructure = preg_replace("/{".$pattern."}/i" , $valueConv2Html , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":htmlAndLinefeed2htmlLinefeed}/i" , $this->value2htmlAndLineFeed2LineFeed($valueConv2Html) , $datastructure);    # proversion
        $datastructure = preg_replace("/{".$pattern.":html}/i" , $this->value2html($valueConv2Html) , $datastructure);
        $datastructure = preg_replace("/\{".$pattern.":purejsondata\}/i" , $value, $datastructure);
        $datastructure = preg_replace("/{".$pattern.":urlencode}/i" , urlencode(html_entity_decode($valueConv2Html)) , $datastructure);
      }
      if (trim($valueConv2Html)=="") {
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddLeftRight:(.*?)##(.*?)##}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeftRight:(.*?)##(.*?)##}/i" , '' , $datastructure); #v1-2-15
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , '' , $datastructure); #v1-2-15
      } else {
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , $valueConv2Html.'${1}' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , $this->value2html($valueConv2Html.'${1}') , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , $valueConv2Html.'${1}' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , $this->value2html($valueConv2Html.'${1}') , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , '${1}'.$valueConv2Html , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , $this->value2html('${1}'.$valueConv2Html) , $datastructure);

        #v1-2-15 begin
        if (defined("PLUGINJCIID") && PLUGINJCIID=="jcinyt"
        ) {
          ## ifNotEmptyAddLeftRight: {} as placeholders allowed, therefore: "##" is separator and marks end of tag with "##}"
          $pat1 = "{".$pattern.":ifNotEmptyAddLeftRight:(.*?)##(.*?)##}";
          $pat1 = addcslashes($pat1, "/^$\_");
          $datastructure = preg_replace("/".$pat1."/i" , '${1}'.$valueConv2Html.'${2}' , $datastructure);

          $pat1 = "{".$pattern.":html,ifNotEmptyAddLeftRight:(.*?)##(.*?)##}";
          $pat1 = addcslashes($pat1, "/^$\_");
          $datastructure = preg_replace("/".$pat1."/i" , '${1}'.$this->value2html($valueConv2Html).'${2}' , $datastructure);

          if (preg_match("/\{".$pattern.":datetime,/i", $datastructure)) {
            $noofmatches = preg_match_all("/\{".$pattern.":datetime,(.*?),([-\d]*?)\}/i", $datastructure, $match);
            global $wp_version;
	          for ($i=0; $i<$noofmatches; $i++) {
              $timezoneoffset = $match[2][$i];
	            if (is_numeric($valueConv2Html)) {
        	     $inTs = $valueConv2Html + $timezoneoffset; # input is numeric, hence assume unixtimestamp
          	  } else {
	             $valuestripslashes = stripslashes($valueConv2Html);
        	     $inTs = strtotime($valuestripslashes) + 60*60*$timezoneoffset; # strtotime gives unixtimestamp
		          }
              if (isset($wp_version)) {
            	 $outTs = date_i18n($match[1][$i], $inTs); # wordpress-funtion, does not work outside wordpress, check first if $wp_version is existing
              } else {
	             $outTs = date($match[1][$i], $inTs);
              }
              $outTs = preg_replace("/\&\#8220\;/", "", $outTs);
              $outTs = preg_replace("/\"/", "", $outTs);
              $match[1][$i] = preg_replace('/\//', '\\/', $match[1][$i]);
      		    $datastructure = preg_replace("/\{".$pattern.":datetime,".$match[1][$i].",".$match[2][$i]."\}/i" , $outTs , $datastructure);
	         }
          }
        }
        #v1-2-15 end
      }

      # a markup can be defined as unique: display only the FIRST data, ignore all following...
      $uniqueParam = '{'.$pattern.':unique}';
      if (preg_match("/$uniqueParam/", $datastructure)) {
    	   # there is a markup defined as unique
         $datastructure = str_replace("{".$pattern.":unique}" , $valueConv2Html , $datastructure);
         $this->triggerUnique[$valueConv2Html]++;
         if ($this->triggerUnique[$valueConv2Html]>1) {
            return "";
         }
      }
      $datastructure = stripslashes($datastructure); # remove backslashes
      $datastructure = preg_replace("/".urlencode(html_entity_decode("\\"))."/", "", $datastructure); # remove urlencoded-backslashes
      return $datastructure; # return template filled with data
    }

      private function process_subloop_array($datastructure, $callingKey, $keypass) {
		  
		  

      $rege = "([a-zA-Z0-9\_\-\|]*)";
      $regereturn = "";
      $this->debugEcho("process_subloop_array: $callingKey || $keypass<br>");
      if (is_string($callingKey)) {
        $rege = $callingKey;
        if ($keypass!="") {
          $rege = $keypass.".".$callingKey;
        }
        preg_match('/{subloop-array:'.$rege.':([\-0-9]*)}/', $datastructure, $subloopNodeArr);
        $subloopNode = $callingKey; # name of subloop-datanode
        $regereturn = $rege;
        @$subLoopNumber = $subloopNodeArr[1];
        $this->debugEcho( "pattern-array: <i>".'/{subloop-array:'.htmlentities($rege).':'.htmlentities($subLoopNumber).'}(.*){\/subloop-array:'.htmlentities($rege).'}/'."</i><br>");
        preg_match('/{subloop-array:'.$rege.':'.$subLoopNumber.'}(.*){\/subloop-array:'.$rege.'}/', $datastructure, $subloopStructureArr);
        @$subloopStructure = $subloopStructureArr[1];
      } else {
        preg_match('/{subloop-array:'.$rege.':([\-0-9]*)}/', $datastructure, $subloopNodeArr);
        $subloopNode = @$subloopNodeArr[1]; # name of subloop-datanode
        $subLoopNumber = @$subloopNodeArr[2];
        preg_match('/{subloop-array:'.$subloopNode.':'.$subLoopNumber.'}(.*){\/subloop-array:'.$subloopNode.'}/', $datastructure, $subloopStructureArr);
        $subloopStructure = @$subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        #  subloop not found, e.g. in closing-tag no subloopNode?
        preg_match('/{subloop-array:'.$subloopNode.':'.$subLoopNumber.'}(.*){\/subloop-array}/', $datastructure, $subloopStructureArr);
        @$subloopStructure = $subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        $subloopHTML = $datastructure;
      } else {
        $subloopHTML = $subloopStructure;
      }

      $this->debugEcho("subloop-array end: <i>".htmlentities($datastructure)."</i> // node: <i>".htmlentities($subloopNode)."</i> // subLoopNumber: <i>".htmlentities($subLoopNumber)."</i> html: <i>".htmlentities($subloopHTML)."</i><br>");
      return array ($subloopNode, $subLoopNumber, $subloopHTML);
    }

     private function process_subloop($datastructure, $callingKey, $keypass) {
      $rege = "([a-zA-Z0-9\_\-]*)";
      $regereturn = "";
      $this->debugEcho("process_subloop: $callingKey || $keypass<br>");
      if (is_string($callingKey)) {
        $rege = $callingKey;
        if ($keypass!="") {
          $rege = $keypass.".".$callingKey;
        }
        preg_match('/{subloop:'.$rege.':([\-0-9]*)}/', $datastructure, $subloopNodeArr);
        $subloopNode = $callingKey; # name of subloop-datanode
        $regereturn = $rege;
        @$subLoopNumber = $subloopNodeArr[1];
        $this->debugEcho( "pattern: <i>".'/{subloop:'.htmlentities($rege).':'.htmlentities($subLoopNumber).'}(.*){\/subloop:'.htmlentities($rege).'}/'."</i><br>");
        preg_match('/{subloop:'.$rege.':'.$subLoopNumber.'}(.*){\/subloop:'.$rege.'}/', $datastructure, $subloopStructureArr);
        @$subloopStructure = $subloopStructureArr[1];
      } else {
        preg_match('/{subloop:'.$rege.':([\-0-9]*)}/', $datastructure, $subloopNodeArr);
        $subloopNode = $subloopNodeArr[1]; # name of subloop-datanode
        $subLoopNumber = $subloopNodeArr[2];
        preg_match('/{subloop:'.$subloopNode.':'.$subLoopNumber.'}(.*){\/subloop:'.$subloopNode.'}/', $datastructure, $subloopStructureArr);
        $subloopStructure = $subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        #  subloop not found, e.g. in closing-tag no subloopNode?
        preg_match('/{subloop:'.$subloopNode.':'.$subLoopNumber.'}(.*){\/subloop}/', $datastructure, $subloopStructureArr);
        #$subloopStructure = $subloopStructureArr[0][0];
        @$subloopStructure = $subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        $subloopHTML = $datastructure;
      } else {
        $subloopHTML = $subloopStructure;
      }
      $this->debugEcho( "subloop end: <i>".htmlentities($datastructure)."</i> //  node: <i>".htmlentities($subloopNode)."</i> // regereturn: <i>".htmlentities($regereturn)."</i> // number: <i>".htmlentities($subLoopNumber)."</i> //  html: <i>".htmlentities($subloopHTML)."</i><br>");
      return array ($subloopNode, $subLoopNumber, $subloopHTML, $regereturn);
    }


    /* checkIfAddToResult: the code created by the template and the JSON-data is checked on
    - needed or forbidden text
    */
    private function checkIfAddToResult($resultCode) {
      # is at least one keywords in the text? if not ignore this text
      if ($this->oneOfTheseWordsMustBeIn!="") {
        $isIn = $this->checkKeywordArray($this->oneOfTheseWordsMustBeIn, $resultCode);
        if (!$isIn) {   return "";    } # none of the keywords was found: ignore this
      }
      # if one of the keywords is in the text, ignore it
      if ($this->oneOfTheseWordsMustNotBeIn!="") {
        $isKeywordThere = $this->checkKeywordArray($this->oneOfTheseWordsMustNotBeIn, $resultCode);
        if ($isKeywordThere) {   return "";    } # one of the keywords was found: ignore this
      }
      if ($this->addToResult) {
        return $resultCode; # ok, add this code
      }
      return "";
    }

    /* is one of the keywords in the text? */
    private function checkKeywordArray($kwArrList, $resultCode) {
      $kwArr = explode(",", trim($kwArrList));
      $isIn = FALSE;
      foreach($kwArr as $keyword) {
          if (trim($keyword)=="") { continue; }
          $kw = $this->createUtf8Keyword($keyword);
          $isIn = $this->checkKeyword($kw, $resultCode);
          if ($isIn) {
            return TRUE;
          }
      }
      return $isIn;
    }

    /* is keyword in the text? */
    private function checkKeyword($kw, $resultCode) {
       if (preg_match("/".$kw."/i", strip_tags($resultCode))) {
          return TRUE;
       }
       return FALSE;
    }

    /* shortcode-text might be encoded or not */
    private function createUtf8Keyword($kw) {
      $kw = htmlentities(trim($kw), ENT_COMPAT, 'UTF-8', FALSE);
      if ($kw=="") {
        # if input was not utf8
        $kw = htmlentities(utf8_encode(trim($kw)), ENT_COMPAT, 'UTF-8', FALSE);
      }
      return $kw;
    }

    /* debugEcho: display debugMessages or not */
    public function getErrorDebugMsg() {
      return $this->errormsg;
    }
    
    private function debugEcho($txt, $paramIn="", $object=NULL) {
      if ( defined( 'JCI_FREE_BLOCK_VERSION' ) ) {
          $this->errormsg = $txt;#.'<b>Check the Basenode in the Block-Settings, please:</b><br>This must match to the JSON - as this is the JSON-entrypoint.<br>For the example "level1" is the matching basenode.';
          return null;
      }
      if ($paramIn=="wordpressticket") {
        $this->errormsg .= $txt.'<br><b>'.__('Switch to gutenberg-Blocks!', 'json-content-importer').'</b><br>'.__('There the JSON Content Importer Block gives an easier way to use the JSON-APIs.', 'json-content-importer').'<br>'.__('Or', 'json-content-importer').': <b>'.__('Switch on the Debugmode', 'json-content-importer').'</b> '.__('by adding "debugmode=10" in the Shortcode.', 'json-content-importer').'<p>'.__('If all is without success: Open ticket at', 'json-content-importer').' <a href="https://wordpress.org/support/plugin/json-content-importer" target="_blank">wordpress.org</a> '.__('please', 'json-content-importer').'<hr>';
      }
      if ($this->showDebugMessages) {
        if ($paramIn=="showdump") {
          $this->errormsg .= "$txt<br><i>";
          print_r($object);
          $this->errormsg .= "</i><br>";
        } else if ($paramIn=="") {
          $this->errormsg .= $txt;
        }
      }
    }

    /* clearUnusedArrayDatafields: remove unfilled markups: we loop the JSON-data, not the markups. If there is no JSON, the markup might stay markup... */
    private function clearUnusedArrayDatafields($datastructure) {
      $regExpPatt = "([a-zA-Z0-9\{\}\=\"\#,;\_\.\-:!\*\,\$\<\>\/ ]+?)";  # (.*?) not ok when result should be JSON itself
      $regExpPattWithDotAndKomma = $this->regExpPatternDetect; # "([a-z0-9\.\,]*)"; prior to vers. 1.2.7
      $datastructure = preg_replace("/{".$regExpPatt."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":urlencode}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":purejsondata}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":unique}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":datetime,,(.*?),([-\d]*?)}/i", "", $datastructure);
      $datastructure = preg_replace('/\{'.$regExpPatt.':html\}/i', '', $datastructure);
      $datastructure = preg_replace("/{subloop:".$regExpPattWithDotAndKomma.":".$regExpPattWithDotAndKomma."}/i", "", $datastructure);
      $datastructure = preg_replace("/{\/subloop:".$regExpPattWithDotAndKomma."}/i", "", $datastructure);

      $datastructure = preg_replace("/#CBO#/", '{' , $datastructure);
      $datastructure = preg_replace("/#CBC#/", '}', $datastructure);
      $datastructure = preg_replace("/#SBO#/", '{' , $datastructure);
      $datastructure = preg_replace("/#SBC#/", '}', $datastructure);
      return $datastructure;
    }

	}
?>