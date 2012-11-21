<?php
/**
 * Groupon_Client
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Groupon/Client.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-11-10
 * 
 **/

/* $Id$ */

class Groupon_Client {

    const GET   = "GET";
    const POST  = "POST";

    const SERVICE_URI = "http://api.groupon.com/v2/";

    protected $_clientId = "";

    /* {{{ public function __construct($clientId) */
    public function __construct($clientId) {
        $this->_clientId = $clientId;
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
            CURLOPT_USERAGENT       => "Mozilla/4.0 (Agent8_Client/1.0; PHP/".PHP_VERSION.")",
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
            throw new Groupon_Exception("unrecognized method");
        }

        Easy_Log::debug("Groupon send", array(
            "url" => $strUrl,
            "method" => $method,
            "data" => $strData,
        ));

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        Easy_Log::debug("Groupon recv", array(
            "errno" => $errno,
            "code" => $code,
            "size" => strlen($raw),
            "raw" => $raw,
            "elapse" => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        ));

        if ($errno != CURLE_OK) {
            throw new Groupon_Exception("Connection error [CURLE{$errno}]");
        }

        if ($code != 200) {
            throw new Groupon_Exception("HTTP Error: [HTTP{$code}]");
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

    /* {{{ function protected _getUrl($suffix))  */
    protected function _getUrl($suffix) {
        $strSuffix = $suffix . ( strpos($suffix, "?") ? "&" : "?" ) . "client_id=" . urlencode($this->_clientId);
        return self::SERVICE_URI.ltrim($strSuffix, "/");
    }
    /* }}} */

    /* {{{ public function getDeals($lat="", $lng="", $radius=100)  */
    public function getDeals($lat="", $lng="", $radius=100) {

        $arrData = array();
        $lat && $arrData["lat"] = $lat;
        $lng && $arrData["lng"] = $lng;
        $lat && $lng && $arrData["radius"] = $radius;

        $strRes = $this->_get($this->_getUrl("/deals.json"), $arrData);

        return json_decode($strRes, TRUE);

    }
    /* }}} */

}
