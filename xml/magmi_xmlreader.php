<?php
require_once("magmi_mixin.php");
require_once("magmi_utils.php");

class Magmi_XMLException extends Exception {
}

class Magmi_XMLReader extends Magmi_Mixin {
    protected $_filename;
    protected $_xml;
    protected $_cols;
    protected $_elementparent;
    protected $_elementname;
    protected $_xpath;

    public function initialize() {
        $this->_filename = $this->getParam("XML:filename");
        $this->_elementparent = $this->getParam("XML:elementparent", ",");
        $this->_elementname = $this->getParam("XML:elementname", ",");
        $this->_xpath = $this->getParam("XML:xpath", ",");

        $this->_ignored = explode(",", $this->getParam("XML:ignore"));
    }

    public function getLinesCount() {
        $count = 0;
        $xml = simplexml_load_file($this->_filename);
        if ($xml != false) {
            $count = sizeof($xml->xpath($this->_xpath));
            //$count = $xml->{$this->_elementparent}->{$this->_elementname}->count();
        } else {
            $this->log("Could not read $this->_filename , check permissions", "error");
        }

        return $count;
    }

    public function checkXML() {
        if (!isset($this->_filename)) {
            throw new Magmi_XMLException("No xml file set");
        }
        if (!file_exists($this->_filename)) {
            throw new Magmi_XMLException("{$this->_filename} not found");
        }
        $this->log("Importing XML : $this->_filename using xpath element [ $this->_xpath ]",
            "startup");
    }

    public function openXML() {
        // open xml file
        $this->_xml = simplexml_load_file($this->_filename);

        //$this->_xml->rewind();
        if ($this->_xml = new ArrayIterator($this->_xml->xpath($this->_xpath))) {
            $this->_xml->rewind();
        } else {
            $this->log("$this->_filename contains invalid XML", "error");
        }
    }

    public function getColumnNames() {
        $xml = simplexml_load_file($this->_filename);
        $this->_cols = [];

        foreach ($xml->xpath($this->_xpath)[0]->children() as $col) {
            $this->_cols[] = $col->getName();
        }

        return $this->_cols;
    }

    public function getNextRecord() {
        //return false;
        $this->_xml->next();
        if (is_object($this->_xml->current())) {
            $record = $this->xml2array($this->_xml->current()->children());
        } else {
            return false;
        }
        return $record ?: false;
    }

    public function xml2array($xml) {
        $arr = array();

        foreach ($xml as $element) {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e)) {
                $arr[$tag] = $element instanceof SimpleXMLElement ? $this->xml2array($element) : $e;
            } else {
                $arr[$tag] = trim($element);
            }
        }

        return $arr;
    }
}