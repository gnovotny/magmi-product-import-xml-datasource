<?php
require_once ("magmi_mixin.php");
require_once ("magmi_utils.php");

class Magmi_XMLException extends Exception
{
}

class Magmi_XMLReader extends Magmi_Mixin
{
    protected $_filename;
    protected $_xml;
    protected $_cols;
    protected $_elementparent;
    protected $_elementname;

    public function initialize()
    {
        $this->_filename = $this->getParam("XML:filename");
        $this->_elementparent = $this->getParam("XML:elementparent", ",");
        $this->_elementname = $this->getParam("XML:elementname", ",");

        $this->_ignored = explode(",", $this->getParam("XML:ignore"));
    }

    public function getLinesCount()
    {
        $count = 0;
        $xml = new SimpleXMLElement($this->_filename, null, true);
        if ($xml != false)
        {
            $count = $xml->{$this->_elementparent}->{$this->_elementname}->count();
        }
        else
        {
            $this->log("Could not read $this->_filename , check permissions", "error");
        }

        return $count;
    }

    public function checkXML()
    {
        if (!isset($this->_filename))
        {
            throw new Magmi_XMLException("No xml file set");
        }
        if (!file_exists($this->_filename))
        {
            throw new Magmi_XMLException("{$this->_filename} not found");
        }
        $this->log("Importing XML : $this->_filename using parent element [ $this->_elementparent ] element name [ $this->_elementname ]",
            "startup");
    }

    public function openXML()
    {
        // open xml file
        $this->_xml = new SimpleXMLIterator($this->_filename, null, true);

        $this->_xml->rewind();
        if($this->_xml->valid()) {
            while(true) {
                $this->_xml->next();
                if($this->_xml->key() == $this->_elementparent) {
                    $this->_xml = $this->_xml->getChildren();
                    $this->_xml->rewind();
                    break;
                }
            }

            //var_dump($this->_xml); exit;
        } else {
            $this->log("$this->_filename contains invalid XML", "error");
        }
    }

    public function getColumnNames()
    {
        $xml = new SimpleXMLElement($this->_filename, null, true);
        $this->_cols = [];

        foreach($xml->{$this->_elementparent}->{$this->_elementname}->children() as $col) {
            $this->_cols[] = $col->getName();
        }

        return $this->_cols;
    }

    public function getNextRecord()
    {
        //return false;
        $this->_xml->next();
        $record = $this->xml2array($this->_xml->getChildren());
        return $record ?: false;
    }

    public function xml2array($xml)
    {
        $arr = array();

        foreach ($xml as $element)
        {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e))
            {
                $arr[$tag] = $element instanceof SimpleXMLElement ? $this->xml2array($element) : $e;
            }
            else
            {
                $arr[$tag] = trim($element);
            }
        }

        return $arr;
    }
}