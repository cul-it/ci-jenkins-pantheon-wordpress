<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

require_once "SmackCSVLogger.php";

class SmackImpUtilClasses extends SmackCSVObserver
{

    /**
     * getXMLString - Get XML string from file
     * @param null $file
     * @return bool|string
     */
    function getXMLString($file = null)
    {
        global $wp_session;

        $this->logI("SMUtils", "getXMLFileContents function called");

        if (!file_exists($file))
        {
            $this->logE("SMUtils", "XML file doesn't exist");
            $wp_session['smimp']['error'] = "XML file doesn't exist";

            return false;
        } else
        {
            $this->xmlstring = file_get_contents($file);
            if (!$this->xmlstring)
            {
                $this->logE("XMLParser", "XMLString is empty");
                $wp_session['smimp']['error'] = "XMLString is empty";

                return false;
            }

            return $this->xmlstring;
        }
    }
}