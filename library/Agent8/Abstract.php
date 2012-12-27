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

abstract class Agent8_Abstract extends Easy_Singleton {

    const GET   = "GET";
    const POST  = "POST";

    const VERSION   = "1.0";

    protected static $_serviceUri = "";
    protected static $_userAgent = NULL;
    protected static $_userAgentStr = NULL;

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

    /* {{{ protected static function initUserAgent()  */
    protected static function initUserAgent() {
        self::$_userAgent = array(
            "Agent8_Client" => self::VERSION,
            "PHP" => PHP_VERSION,
            "cURL" => ( ($arrCurlVer = curl_version()) ?  $arrCurlVer["version"] : "" ),
        );
    }
    /* }}} */

    /* {{{ public static function setUserAgent($key, $val="")  */
    public static function setUserAgent($key, $val="") {
        if (self::$_userAgent === NULL) {
            self::initUserAgent();
        }
        if ($val === NULL) {
            unset(self::$_userAgent[$key]);
        } else {
            self::$_userAgent[$key] = $val;
        }
        self::$_userAgentStr = NULL;
    }
    /* }}} */

    /* {{{ public static function getUserAgent($key=NULL)  */
    public static function getUserAgent($key=NULL) {
        if (self::$_userAgent === NULL) {
            self::initUserAgent();
        } else {
            if ($key === NULL) {
                return self::$_userAgent;
            } else {
                return isset(self::$_userAgent[$key]) ? self::$_userAgent[$key] : NULL;
            }
        }
    }
    /* }}} */

    /* {{{ protected static function getUserAgentStr()  */
    protected static function getUserAgentStr() {
        if (self::$_userAgent === NULL) {
            self::initUserAgent();
        }
        if (self::$_userAgentStr) {
            return self::$_userAgentStr;
        }
        $arrTemp = array();
        foreach (self::$_userAgent as $key => $val) {
            if ($val) {
                $arrTemp[] = "{$key}/{$val}";
            } else {
                $arrTemp[] = "{$key}";
            }
        }
        $strRet = "Mozilla/4.0";
        if ($arrTemp) {
            $strRet.= " (".join("; ", $arrTemp).")";
        }
        return (self::$_userAgentStr = $strRet);
    }
    /* }}} */

    /* {{{ protected function __construct()  */
    protected function __construct() {
        parent::__construct();
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
            CURLOPT_USERAGENT       => self::getUserAgentStr(),
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
