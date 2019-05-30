<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
require_once "SmackCSVObserver.php";

class SmackCSVLogger
{

    /**
     * SmackCSVLogger constructor.
     * @param null $log_file
     * @throws Exception
     */
    function __construct($log_file = null)
    {
        if (!$log_file)
        {
            $log_file = SM_UCI_DEBUG_LOG;
        }
	$myDir = SM_UCI_DEFAULT_UPLOADS_DIR;
        if(is_dir($myDir)) {
        if (!file_exists($log_file))
        {
            touch($log_file);
        }

        if (!(is_writable($log_file) || $this->win_is_writable($log_file)))
        {
            //Cant write to file,
            #throw new Exception("LOGGER ERROR: Can't write to log", 1);
            #TODO: Handle the log file exists or not.
        }
	}
    }

    /**
     * logD - Log Debug
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     **/
    public function logD($tag, $message)
    {
        $this->writeToLog("DEBUG", $tag, $message);
    }

    /**
     * logE - Log Error
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     **/
    public function logE($tag, $message)
    {
        $this->writeToLog("ERROR", $tag, $message);
    }

    /**
     * logW - Log Warning
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     **/
    public function logW($tag, $message)
    {
        $this->writeToLog("WARNING", $tag, $message);
    }

    /**
     * logI - Log Info
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     **/
    public function logI($tag, $message)
    {
        $this->writeToLog("INFO", $tag, $message);
    }

    /**
     * logA - Log All
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     **/
    public function logA($tag, $message)
    {
        $this->writeToLog("ALL", $tag, $message);
    }

    /**
     * writeToLog - writes out timestamped message to the log file as
     * defined by the $log_file class variable.
     *
     * @param String status - "INFO"/"DEBUG"/"ERROR"/"WARNING"/"ALL"/"NONE"
     * @param String tag - "Small tag to help find log entries"
     * @param String message - The message you want to output.
     * @return void
     **/
    private function writeToLog($status, $tag, $message)
    {
        if ($this->log_status == "ALL" || $this->log_status == $status)
        {
            $date = date('[Y-m-d H:i:s]');
            $msg = "$date: [$tag][$status] - $message" . PHP_EOL;
            file_put_contents($this->log_file, $msg, FILE_APPEND);
        }
    }

    /**
     * win_is_writable function lifted from WordPress
     * @param $path
     * @return bool
     */
    private function win_is_writable($path)
    {
        if ($path[ strlen($path) - 1 ] == '/')
            return win_is_writable($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return win_is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');

        $should_delete_tmp_file = !file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;

        fclose($f);

        if ($should_delete_tmp_file)
            unlink($path);

        return true;
    }
}
