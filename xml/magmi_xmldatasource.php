<?php
require_once ("magmi_xmlreader.php");
require_once ("fshelper.php");

class Magmi_XMLDataSource extends Magmi_Datasource
{
    protected $_xmlreader;

    public function initialize($params)
    {
        $this->_xmlreader = new Magmi_XMLReader();
        $this->_xmlreader->bind($this);
        $this->_xmlreader->initialize();
    }

    public function getAbsPath($path)
    {
        return abspath($path, $this->getScanDir());
    }

    public function getScanDir($resolve = true)
    {
        $scandir = $this->getParam("XML:basedir", "var/import");
        if (!isabspath($scandir))
        {
            $scandir = abspath($scandir, Magmi_Config::getInstance()->getMagentoDir(), $resolve);
        }
        return $scandir;
    }

    public function getXMLList()
    {
        $scandir = $this->getScanDir();
        $files = glob("$scandir/*.xml");
        return $files;
    }

    public function getPluginParams($params)
    {
        $pp = array();
        foreach ($params as $k => $v)
        {
            if (preg_match("/^XML:.*$/", $k))
            {
                $pp[$k] = $v;
            }
        }
        return $pp;
    }

    public function getPluginInfo()
    {
        return array("name"=>"XML Datasource","author"=>"gnovotny","version"=>"0.1");
    }

    public function getRecordsCount()
    {
        return $this->_xmlreader->getLinesCount();
    }

    public function getAttributeList()
    {}

    public function getRemoteFile($url)
    {
        $fg = RemoteFileGetterFactory::getFGInstance();
        if ($this->getParam("XML:remoteauth", false) == true)
        {
            $user = $this->getParam("XML:remoteuser");
            $pass = $this->getParam("XML:remotepass");
            $fg->setCredentials($user, $pass);
        }
        $cookies = $this->getParam("XML:remotecookie");
        if ($cookies)
        {
            $fg->setCookie($cookies);
        }
        
        $this->log("Fetching XML: $url", "startup");
        // output filename (current dir+remote filename)
        $xmldldir = dirname(__FILE__) . "/downloads";
        if (!file_exists($xmldldir))
        {
            @mkdir($xmldldir);
            @chmod($xmldldir, Magmi_Config::getInstance()->getDirMask());
        }
        
        $outname = $xmldldir . "/" . basename($url);
        $ext = substr(strrchr($outname, '.'), 1);
        if ($ext != "txt" && $ext != "xml")
        {
            $outname = $outname . ".xml";
        }
        // open file for writing
        if (file_exists($outname))
        {
            if ($this->getParam("XML:forcedl", false) == true)
            {
                unlink($outname);
            }
            else
            {
                return $outname;
            }
        }
        $fg->copyRemoteFile($url, $outname);
        
        // return the xml filename
        return $outname;
    }

    public function beforeImport()
    {
        if ($this->getParam("XML:importmode", "local") == "remote")
        {
            $url = $this->getParam("XML:remoteurl", "");
            $outname = $this->getRemoteFile($url);
            $this->setParam("XML:filename", $outname);
            $this->_xmlreader->initialize();
        }
        return $this->_xmlreader->checkXML();
    }

    public function afterImport()
    {}

    public function startImport()
    {
        $this->_xmlreader->openXML();
    }

    public function getColumnNames($prescan = false)
    {
        return $this->_xmlreader->getColumnNames();
    }

    public function getNextRecord()
    {
        return $this->_xmlreader->getNextRecord();
    }
}