<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

ini_set('auto_detect_line_endings', true);
require_once "SmackImpUtilClasses.php";

class SmackCSVParser extends SmackImpUtilClasses
{

    # $fileObj - SplFileObject
    private $fileObj;

    /**
     * parseCSV - Parse the CSV and return as Array
     * @param null $file
     * @param null $offset
     * @param null $limit
     * @return array|bool
     */
    public function parseCSV($file = null, $offset = null, $limit = null)
    {
        global $wp_session;

        if ($file)
        {
            $this->file = $file;
        }

        if ($this->initializeFile($file))
        {
            $Hdata = $this->get_CSVheaders();
            $Cdata = $this->_parseCSV($offset, $limit);
            $this->total_row_cont = count($Cdata);
            if (!$Hdata)
            {
                $this->logE("CSVParser", "Empty header, throws exception in the function parseCSV");
                $wp_session['smimp']['error'] = "Empty header, throws exception in the function parseCSV";

                return false;
            } elseif (!$Cdata)
            {
                $this->logE("CSVParser", "Empty CSV data, throws exception in the function parseCSV");
                $wp_session['smimp']['error'] = "Empty CSV data, throws exception in the function parseCSV";

                return false;
            } else
            {
                return $this->_array_combine($Hdata, $Cdata);
            }
        } else
        {
            return false;
        }
    }

    /**
     * initializeFile - Initialize the CSV file for parsing
     * @param null $file
     * @return bool
     */
    function initializeFile($file = null)
    {
        global $wp_session;
        if (file_exists($file))
        {
            $this->file = $file;

            return true;
        } else
        {
            $this->logE("CSVParser", "CSV file not exist");
            $wp_session['smimp']['error'] = "CSV file not exist";

            return false;
        }
    }

    /**
     * getFileObj - The singleton method to get file Obj
     * @return bool|SplFileObject
     */
    private function getFileObj()
    {
        if (!isset(self::$fileObj))
        {
            if (file_exists($this->file) == false) return false;
            $fileObj = new SplFileObject($this->file, 'r');

            return $fileObj;
        }

        $this->logI("CSVParser", "SplFileObject instantiated ");

        return self::$fileObj;
    }

    /**
     * get_CSVheaders - Returns the CSV header
     * @return array
     */
    function get_CSVheaders()
    {
        $this->logI("CSVParser", "get_CSVheaders function called");

        return $this->_getCSVHeaderData();
    }

    /**
     * set_CSVheaders - Set the CSV header array globally
     */
    function set_CSVheaders()
    {
        $this->logI("CSVParser", "set_CSVheaders function called");
        $this->csvfile_header = $this->_getCSVHeaderData();
    }

    /**
     * getDelimiter - Get the delimiter of the CSV file
     * @return mixed
     */
    public function getDelimiter()
    {
        $this->logI("CSVParser", "getDelimiter function called");

        return $this->_autoDetectDelimiter();
    }

    /**
     * setDelimiter - Set the delimiter of the CSV file
     */
    public function setDelimiter()
    {
        $this->logI("CSVParser", "setDelimiter function called");
        $this->delimiter = $this->_autoDetectDelimiter();
    }

    /**
     * _array_combine - Combine CSV header & data
     * @param $headerArray
     * @param $dataArray
     * @return array
     */
    public function _array_combine($headerArray, $dataArray)
    {
        #$this->logI("CSVParser", "Combining CSV header and data");
        
        $result = array();

        $Hdata = $headerArray[0];

        foreach ($dataArray as $row => $Cdata)
        {
            $result[ $row ] = @array_combine($Hdata, $Cdata);
        }

        return $result;
    }

    /**
     * _getCSVHeaderData - Get the CSV header
     * @return array
     */
    public function _getCSVHeaderData()
    {
        $this->logI("CSVParser", "_getCSVHeaderData function called");

        $hData = $this->_parseCSV(0, 1);

        if (!$hData)
        {
            $this->logE("CSVParser", "Empty header value returned");
        }

        return $hData;
    }

    /**
     * _autoDetectDelimiter - Auto detect delimiter
     * @return mixed
     */
    public function _autoDetectDelimiter()
    {
        $this->logI("CSVParser", "_autoDetectDelimiter function called");
        $dObj = $this->getFileObj();
        $dObj->seek(0);

        $content = $dObj->current();

        foreach ($this->delimiters as $delimiter => &$count)
        {
            $count = count(str_getcsv($content, $delimiter));
        }

        return array_search(max($this->delimiters), $this->delimiters);
    }


    /**
     * _parseCSV - Core parsing function
     * @param null $offset
     * @param null $limit
     * @return array
     */
    public function _parseCSV($offset = null, $limit = null)
    {
        $this->logI("CSVParser", "_parseCSV function called");

        $data = array();
        if (!is_null($offset))
        {
            $this->offset = $offset;
        }
        if (!is_null($limit))
        {
            $this->limit = $limit;
        }

        $pObj = $this->getFileObj();
        $this->delimiter = $this->getDelimiter();
        $pObj->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        $pObj->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        foreach (new LimitIterator($pObj, $this->offset, $this->limit) as $num => $line)
        {
            $data[ $num ] = $line;
        }

        return $data;

    }
}
