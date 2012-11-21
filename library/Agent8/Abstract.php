<?php
/**
 * Agent8_Abstract
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Agent8/Abstract.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-10-22
 * 
 **/

/* $Id$ */

abstract class Agent8_Abstract {

    const GET   = "GET";
    const POST  = "POST";

    protected static $_serviceUri = "";

    protected static $_instance = NULL;

    /* {{{ public static function getInstance() */
    public static function getInstance() {

        if (isset(self::$_instance)) {
            if (self::$_instance instanceof self) {
                return self::$_instance;
            }
        }

        $obj = new self;
        self::$_instance = $obj;
        return $obj;

    }
    /* }}} */

    /* {{{ public static function setServiceUri($strPrefix)  */
    public static function setServiceUri($strPrefix) {
        return self::$_serviceUri = $strPrefix;
    }
    /* }}} */

    /* {{{ public static function getServiceUri()  */
    public static function getServiceUri() {
        return self::$_serviceUri;
    }
    /* }}} */

    /* {{{ protected function __construct()  */
    protected function __construct() {

    }
    /* }}} */

    /* {{{ protected function _exec($method, $url, array $data)  */
    protected function _exec($method, $url, $data) {

        $ch = curl_init();

        $opt = array (
            CURLOPT_RETURNTRANSFER  => TRUE,         // return web page
            CURLOPT_HEADER          => FALSE,        // don't return headers
            CURLOPT_FOLLOWLOCATION  => FALSE,        // follow redirects
            CURLOPT_ENCODING        => "",           // handle all encodings
            CURLOPT_USERAGENT       => "Mozilla/4.0 (Agent8_Client/1.0; PHP/".PHP_VERSION."; Dobuilder)",
            CURLOPT_AUTOREFERER     => FALSE,        // set no referer on redirect
            CURLOPT_CONNECTTIMEOUT  => 3,            // timeout on connect
            CURLOPT_TIMEOUT         => 10,           // timeout on response
            // CURLOPT_POST            => 1,            // i am sending post data
            // CURLOPT_POSTFIELDS      => json_encode($request),
            CURLOPT_VERBOSE         => FALSE,        // 
            // CURLOPT_SSL_VERIFYHOST  => 0,            // don't verify ssl
            // CURLOPT_SSL_VERIFYPEER  => FALSE,        //
            // CURLOPT_URL             => $this->_url,
        );

        curl_setopt_array($ch, $opt);

        if (is_string($data)) {
            $strData = $data;
        } else {
            $arrParam = array();
            foreach ($data as $key => $value) {
                $arrParam[] = urlencode($key)."=".urlencode($value);
            }
            $strData = join("&", $arrParam);
        }

        if (strcasecmp($method, self::GET) == 0) {

            if ($strData) {
                $strUrl = $url . ( strpos($url, "?") ? "&" : "?" ) . $strData;
            } else {
                $strUrl = $url;
            }

            curl_setopt_array($ch, array(
                CURLOPT_HTTPGET => TRUE,
                CURLOPT_URL     => $strUrl,
            ));

        } elseif (strcasecmp($method, self::POST) == 0) {
            $strUrl = $url;
            curl_setopt_array($ch, array(
                CURLOPT_URL     => $url,
                CURLOPT_POST    => TRUE,
                CURLOPT_POSTFIELDS => $strData,
            ));
        } else {
            throw new Agent8_Exception("unrecognized method");
        }

        Easy_Log::debug("Agent8 send", array(
            "url" => $strUrl,
            "method" => $method,
            "data" => $strData,
        ));

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        Easy_Log::debug("Agent8 recv", array(
            "errno" => $errno,
            "code" => $code,
            "size" => strlen($raw),
            "raw" => $raw,
            "elapse" => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        ));

        if ($errno != CURLE_OK) {
            throw new Agent8_Exception("Connection error [CURLE{$errno}]");
        }

        if ($code != 200) {
            throw new Agent8_Exception("HTTP Error: [HTTP{$code}]");
        }

        return $raw;

    }
    /* }}} */

    /* {{{ protected function _get($url, $data = array())  */
    protected function _get($url, $data = array()) {
        return $this->_exec(self::GET, $url, $data);
    }
    /* }}} */

    /* {{{ protected function _post($url, $data = array())  */
    protected function _post($url, $data = array()) {
        return $this->_exec(self::POST, $url, $data);
    }
    /* }}} */

    /* {{{ protected function _getUrl($suffix)  */
    protected function _getUrl($suffix) {
        if (empty(self::$_serviceUri)) {
            throw new Agent8_Exception("\$_serviceUri is undefined. ( @RD call Agent8_Abstract::setServiceUri(\$uri) to fix this problem )");
        }
        return rtrim(self::$_serviceUri, "/")."/".ltrim($suffix, "/");
    }
    /* }}} */

}
