<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
ini_set('auto_detect_line_endings', true);
require_once "SmackImpUtilClasses.php";

#Thanks to http://gaarf.info/2009/08/13/xml-string-to-php-array/

class SmackXMLParser extends SmackImpUtilClasses
{

    /**
     * xml_to_array - XML file array
     * @param null $file
     * @return array|bool|string
     */
    function xml_to_array($file = null)
    {
        $this->logI("XMLParser", "xml_to_array function called");

        $this->getXMLString($file);

        if (!$this->xmlstring)
        {
            $this->logE("XMLParser", "Invalid XML String / File");

            return false;
        }

        $doc = new DOMDocument();
        $doc->loadXML($this->xmlstring);
        $root = $doc->documentElement;
        $output = $this->domnode_to_array($root);
        $output['@root'] = $root->tagName;

        return $output;
    }

    /**
     * domnode_to_array - DOM to array
     * @param $node
     * @return array|string
     */
    function domnode_to_array($node)
    {
        $this->logI("XMLParser", "domnode_to_array function called");

        $output = array();
        switch ($node->nodeType)
        {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i ++)
                {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if (isset($child->tagName))
                    {
                        $t = $child->tagName;
                        if (!isset($output[ $t ]))
                        {
                            $output[ $t ] = array();
                        }
                        $output[ $t ][] = $v;
                    } elseif ($v || $v === '0')
                    {
                        $output = (string) $v;
                    }
                }
                if ($node->attributes->length && !is_array($output))
                {
                    $output = array('@content' => $output);
                }
                if (is_array($output))
                {
                    if ($node->attributes->length)
                    {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode)
                        {
                            $a[ $attrName ] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v)
                    {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes')
                        {
                            $output[ $t ] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }

}