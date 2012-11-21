<?php
/**
 * Jsonrpc5_Client
 * 
 * @package jsonrpc5
 * @author Jay Zhang <i@zh-ang.com>
 * @file Client.php
 * @version 1.0
 * @since 2012-09-20
 * 
 **/

/* $Id$ */

class Jsonrpc5_Client {

    protected $_url     = "";
    protected $_id      = 0;
    protected $_class   = NULL;
    protected $_timeout = NULL;

    protected static $_connection=array();

    public function __construct($url=NULL, $timeout=5) {

        if ($url) $this->_url = "$url";
        $this->_timeout = doubleval($timeout);
        $this->_id = 0;
        if (is_null($this->_class)) {
            if (get_class($this) != __CLASS__) {
                $this->_class = get_class($this);
            } else {
                $this->_class = "";
            }
        }

    }

    public function __call($method, $params) {

		$request = array(
            "jsonrpc" => "2.0",
            "method" => ($this->_class?$this->_class.".":"").$method,
            "params" => $params,
            "id" => ($this->_id = mt_rand()),
        );

		$opt = array (
            CURLOPT_URL             => $this->_url,
            CURLOPT_RETURNTRANSFER  => TRUE,         // return web page
            CURLOPT_HEADER          => FALSE,        // don't return headers
            CURLOPT_FOLLOWLOCATION  => FALSE,        // follow redirects
            CURLOPT_ENCODING        => "",           // handle all encodings
            CURLOPT_USERAGENT       => "Jsonrpc5 (https://github.com/zh-ang/jsonrpc5)",
            CURLOPT_AUTOREFERER     => TRUE,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT  => 3,            // timeout on connect
            CURLOPT_TIMEOUT         => $this->_timeout,// timeout on response
            CURLOPT_POST            => 1,            // i am sending post data
            CURLOPT_POSTFIELDS      => json_encode($request),
            CURLOPT_SSL_VERIFYHOST  => 0,            // don't verify ssl
            CURLOPT_SSL_VERIFYPEER  => FALSE,        //
            CURLOPT_VERBOSE         => FALSE,        // 
        );

        $parse = parse_url($this->_url);
        $protocol = isset($parse["scheme"]) ? $parse["scheme"] : NULL;
        if ($protocol == "http" || $protocol == "https") {
            $ip = isset($parse["host"]) ? gethostbyname($parse["host"]) : "";
            $port = isset($parse["port"]) ? intval($parse["port"]) : 0;
            if ($port == 0) {
                $port = ($protocol == "https") ? 443 : 80;
            }
            $tag = "{$ip}:{$port}";
            if (isset(self::$_connection[$tag])) {
                $ch = self::$_connection[$tag];
            } else {
                $ch = (self::$_connection[$tag] = curl_init());
            }
        } else {
            throw new Jsonrpc5_Exception("Unrecognized protocol: {$protocol}");
        }

        curl_setopt_array($ch, $opt);
        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        
        if ($errno != CURLE_OK) {
            throw new Jsonrpc5_Exception("Connection error [{$errno}]");
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            throw new Jsonrpc5_Exception("HTTP Error: [HTTP{$code}]");
        }

        if (empty($raw)) {
            throw new Jsonrpc5_Exception("Empty response");
        }

        $response = json_decode($raw, TRUE);

        if (isset($response["error"])) {
            throw new Jsonrpc5_Exception("Request error: ".json_encode($response["error"]));
        }
        if (!isset($response["id"])) {
            throw new Jsonrpc5_Exception("Unrecognised package");
        }
        if ($response["id"] != $this->_id) {
            throw new Jsonrpc5_Exception("Incorrect id: req={$this->_id}, res={$response["id"]}");
        }

        return $response["result"];
    }

}
