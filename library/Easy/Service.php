<?php
/**
 * Easy_Service
 * 
 * @package common
 * @author Jay Zhang <i@zh-ang.com>
 * @file Easy/Client.php
 * @copyright Copyright 2013 All right reserved.
 * @version 1.0
 * @since 2012-09-12
 * 
 **/

/* $Id$ */

class Easy_Service {

    protected $_service;

    /* {{{ public function __construct($objService=NULL)  */
    public function __construct($objService=NULL) {

        $this->_service = null;

        if (!isset($_SERVER["REQUEST_METHOD"])) {
            throw new Easy_Exception("Service works only over HTTP");
        }

        if (is_null($objService)) {
            return;
        }

        if (is_object($objService)) {
            $this->_service = $objService;
        } else {
            throw new Easy_Exception("unexpected param, need an object");
        }

    }
    /* }}} */

    /* {{{ public function setService($objService)  */
    public function setService($objService) {
        if (is_object($objService)) {
            $this->_service = $objService;
        } else {
            throw new Easy_Exception("unexpected param, need an object");
        }
        return $this;
    }
    /* }}} */

    /* {{{ public function export()  */
    public function export() {

        $objRef = new ReflectionObject($this->_service);
        $strClass = $objRef->getName();
        $strVersion = "1.0.0";
    
        echo <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="copyright" content="JsonRPC Service {$strVersion}" />
    <title>{$strClass} - JsonRPC Service</title>
    <style>
body { margin:0; background-color:#F8F8F8; }
h1 { margin:0; font:bold 28px Verdana,Arial; background:#99c; padding:12px 10px; border-bottom:4px solid #669; box-shadow:0 1px 4px #bbb; text-shadow:3px 3px 7px #cce;}
a { text-decoration:none; color:#333; outline:none; -moz-outline:none }
.f { margin:16px; border-bottom:1px solid #ddd; }
.f * { -webkit-transition:all 0.3s ease-in; -moz-transition:all 0.3s ease-in; -o-transition:all 0.3s ease-in; -ms-transition:all 0.3s ease-in; transition:all 0.3s ease-in; }
.f a { font:normal 20px/22px Georgia, Times, "Times New Roman", serif; padding:5px 4px 8px 5px; display:block; white-space:nowrap; overflow:hidden; }
.f a:hover { padding:5px 4px 8px 18px; white-space:normal; }
.f pre { margin:0 0 0 30px; padding:0; height:0; overflow:auto; white-space:pre-line; _height:auto; *height:auto; }
.f:target pre { height:150px; margin:0 0 10px 30px; }
.f:target a { font-weight:bold; padding:5px 4px 8px 18px; }
h6 { position:fixed; left:0; right:0; bottom:0; margin:0; padding:0 20px; background:#99c; border-top:4px solid #669; box-shadow:0 -1px 4px #bbb; height:20px; line-height:20px; text-align:right;}
    </style>
  </head>
  <body>
    <h1 id="yar-header"><a href="#">{$strClass}</a></h1>
HTML;

    foreach ($objRef->getMethods(ReflectionMethod::IS_PUBLIC) as $objMethod) {
        $strMethod = $objMethod->getName();
        $strDoc = $objMethod->getDocComment();
        $arrParam = $objMethod->getParameters();
        $arrTemp = array();
        foreach ($arrParam as $objParam) {
            $arrTemp[] = "$".$objParam->getName().($objParam->isDefaultValueAvailable()?" = ".var_export($objParam->getDefaultValue(), true):"");
        }
        $objDeclaringClass = $objMethod->getDeclaringClass();
        $strDeclaringClass = $objDeclaringClass->getName();
        $strDescribe = htmlspecialchars("{$strDeclaringClass}::{$strMethod} ( ".join(", ", $arrTemp)." )");
        $strMark = strtolower($strMethod);
        echo <<<HTML
    <div id="{$strMark}" class="f">
      <a href="#{$strMark}">{$strDescribe}</a>
      <pre>
{$strDoc}
      </pre>
    </div>
HTML;
    }

    echo <<<HTML
    <h6 id="yar-footer">
      <i>Powered by <a href="http://pecl.php.net/yar">JsonRPC Service {$strVersion}</a>.</i>
    </div>
  </body>
</html>
HTML;
    }
    /* }}} */

    /* {{{ public function service()  */
    public function service() {

        $strRequest = file_get_contents("php://input");

        $strTag = "";
        if ($strVerbose = Yaf_Application::app()->getConfig()->get("rpc.verbose")) {
            if (is_dir($strVerbose)) {
                $strTag = date("Ymd_His_", $_SERVER["REQUEST_TIME"]).uniqid();
                $strFile = rtrim($strVerbose, "/")."/".$strTag.".request";
                $fp = fopen($strFile, "w");
                foreach (apache_request_headers() as $key => $value) {
                    fprintf($fp, "%s: %s\r\n", $key, $value);
                }
                fwrite($fp, "\r\n");
                fwrite($fp, $strRequest);
                fclose($fp);
            }
        }

        Easy_Log::debug("API Request", $strRequest);
        $strTimestampA = microtime();

        try {

            $objService= new Jsonrpc5_Service($this->_service);
            $strResponse = $objService->dispatch($strRequest);

        } catch (Exception $e) {
            Easy_Log::fatal("Jsonrpc service exception", $e->getMessage());
            throw new Exception("Internal Error"); // GOTO HTTP500 page
        }

        $strTimestampB = microtime();
        Easy_Log::debug("API Response", $strResponse);
        list($strAM, $strAS) = explode(" ", $strTimestampA);
        list($strBM, $strBS) = explode(" ", $strTimestampB);
        $floatElapsed = ($strBS - $strAS) + ($strBM - $strAM);
        Easy_Log::trace("API Time Elapsed (seconds)", $floatElapsed);

        header("Content-Type: application/json; charset=utf-8");
        echo $strResponse;

        if ($strVerbose) {
            $strFile = rtrim($strVerbose, "/")."/".$strTag.".response";
            $fp = fopen($strFile, "w");
            foreach (apache_response_headers() as $key => $value) {
                fprintf($fp, "%s: %s\r\n", $key, $value);
            }
            fwrite($fp, "\r\n");
            fwrite($fp, $strResponse);
            fclose($fp);
        }


        exit;

    }
    /* }}} */

    /* {{{ public function handle()  */
    public function handle() {

        if (headers_sent()) {
            throw new Easy_Exception("headers has sent");
        }

        if (!is_object($this->_service)) {
            throw new Easy_Exception("no service is set");
        }

        switch (strtoupper(trim($_SERVER["REQUEST_METHOD"]))) {
            case "GET": $this->export(); break;
            case "POST": $this->service(); break;
            default: throw new Easy_Exception("unrecognised http method");
        }

        exit;

    }
    /* }}} */

}
