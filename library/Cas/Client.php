<?php
/**
 * Cas_Client
 *
 * @package dobuilder
 * @author Jay Zhang <jay@zh-ang.com>
 * @file Client.php
 * @copyright Copyleft 2013
 * @version 1.0
 * @since 2012-10-12
 *
 **/

/* $Id$ */

class Cas_Client {

    const DEFAULT_COOKIE_NAME   = "USERSTAT";
    const DEFAULT_TICKET_PERIOD = 300;      // 5 min
    const ENCRYPT_METHOD        = "aes-128-cbc"; // there is a list in openssl_get_cipher_methods()

    protected static $_instance = NULL;

    protected $_info = null; /* = array(
        "renew" => time(),
        "ip" => "127.0.0.1",
        "user" => array(
            "userId" => 1,
            "displayName" => "Jay Zhang",
            "primaryEmail" => "jay@easilydo.com",
            "locale" => "en_US",
            "timezone" => "-08:00",
            "state" => 0,
            "addtime" => "2013-01-01 12:34:56",
            "modtime" => "2013-01-01 12:34:56",
        ),
    ) */

    protected static $_config = null; /* = array(
        "appkey"*   => "builder",
        "secret"*   => "secret00",
        "server"*   => "login.easilydo.com/user",
        "domain"    => "builder.easilydo.com",
        "cookie"    => "USERSTAT",
        "period"    => 300,
    ) */

    /* {{{ public static function setConfig ($mixKey, $mixVal = NULL)  */
    public static function setConfig ($mixKey, $mixVal = NULL) {
        if (is_array($mixKey)) {
            self::$_config = $mixKey;
        }
        if (is_string($mixKey)) {
            if ($mixKey === NULL) {
                unset(self::$_config[$mixKey]);
            } else {
                self::$_config[$mixKey]= $mixVal;
            }
        }
    }
    /* }}} */

    /* {{{ protected function __construct()  */
    protected function __construct() {

        if (!function_exists("openssl_encrypt") || !function_exists("openssl_decrypt")) {
            error_log("Agent8_Cas depends on openssl extension");
        }

        if (!function_exists("gzcompress") || !function_exists("gzuncompress")) {
            error_log("Agent8_Cas depends on zlib extension");
        }

        if (!isset(self::$_config["appkey"])) {
            throw new Agent8_Exception("appkey is not set");
        }

        if (!isset(self::$_config["secret"])) {
            throw new Agent8_Exception("secret is not set");
        }

        if (!isset(self::$_config["domain"])) {
            self::$_config["domain"] = $_SERVER["SERVER_NAME"];
        }

        if (!isset(self::$_config["cookie"])) {
            self::$_config["cookie"] = self::DEFAULT_COOKIE_NAME;
        }

        if (!isset(self::$_config["server"])) {
            throw new Agent8_Exception("server is not set");
        }

        if (!isset(self::$_config["period"])) {
            self::$_config["period"] = self::DEFAULT_TICKET_PERIOD;
        }

        $this->_info = $this->_loadCookie();

    }
    /* }}} */

    /* {{{ public static function getInstance() */
    public static function getInstance() {

        if (! self::$_instance instanceof self) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
    /* }}} */

    /* {{{ protected function _saveCookie($arrInfo)
        @return bool true on success, false on failure */
    protected function _saveCookie($arrInfo) {

        $strData = gzcompress(json_encode($arrInfo));
        $strCookie = @openssl_encrypt($strData, self::ENCRYPT_METHOD, self::$_config["secret"]);
        // $arrTemp = parse_url(Easy_Util::webroot());
        // $strDomain = $arrTemp["host"];
        // $strPath = isset($arrTemp["path"]) ? $arrTemp["path"] : "/";
        $strPath = "/";
        // $intExpire = $bolPersist ? time() + self::COOKIE_EXPIRES : 0;
        $intExpire = 0;
        return setcookie(self::$_config["cookie"], $strCookie, $intExpire, $strPath);

    }
    /* }}} */

    /* {{{ protected function _loadCookie()  */
    protected function _loadCookie() {

        $strCookie = NULL;
        if (isset($_COOKIE[self::$_config["cookie"]])) {
            $strCookie = $_COOKIE[self::$_config["cookie"]];
        }

        if ($strCookie) {
            $strData = @openssl_decrypt($strCookie, self::ENCRYPT_METHOD, self::$_config["secret"]);
            if ($strData) {
                $arrInfo = json_decode(gzuncompress($strData), TRUE);
                if ($arrInfo) {
                    Easy_Log::debug("Load from cookies", $arrInfo);
                    return $arrInfo;
                }
            }
            // $this->_removeCookie();
        }

        return NULL;

    }
    /* }}} */

    /* {{{ protected function _removeCookie()
        @return bool true on success, false on failure */
    const COOKIE_DEL_TIME   = 595314600;
    protected function _removeCookie() {

        // $arrTemp = parse_url(Easy_Util::webroot());
        // $strDomain = $arrTemp["host"];
        // $strPath = isset($arrTemp["path"]) ? $arrTemp["path"] : "/";
        $strPath = "/";
        return setcookie(self::$_config["cookie"], "", self::COOKIE_DEL_TIME, $strPath);

    }
    /* }}} */

    /* {{{ protected function _getIp()  */
    protected function _getIp() {
        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
        { $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
        elseif( isset($_SERVER['HTTP_CLIENTIP']) )
        { $ip = $_SERVER['HTTP_CLIENTIP']; }
        elseif( isset($_SERVER['REMOTE_ADDR']) )
        { $ip = $_SERVER['REMOTE_ADDR']; }
        elseif( getenv('HTTP_X_FORWARDED_FOR') )
        { $ip = getenv('HTTP_X_FORWARDED_FOR'); }
        elseif( getenv('HTTP_CLIENTIP') )
        { $ip = getenv('HTTP_CLIENTIP'); }
        elseif( getenv('REMOTE_ADDR') )
        { $ip = getenv('REMOTE_ADDR'); }
        else
        { $ip = '127.0.0.1'; }

        if( ($pos = strpos($ip, ',')) > 0 )
        { $ip = substr($ip, 0, $pos); }

        return trim($ip);
    }
    /* }}} */

    /* {{{ protected function _signRequest(array $arrParam)  */
    protected function _signRequest(array $arrParam) {
        $strData = sprintf("%s:%s:%s:%s", self::$_config["appkey"], self::$_config["secret"], $arrParam["time"], $arrParam["refer"]);
        return md5($strData);
    }
    /* }}} */

    /* {{{ public function getUrl($strFunc, array $arrParam = array())  */
    public function getUrl($strFunc, array $arrParam = array()) {
        $strProt    = (@$_SERVER["HTTPS"] == "on") ? "https" : "http";
        $strHere    = $strProt . "://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $arrTemp["appkey"]  = self::$_config["appkey"];
        $arrTemp["refer"]   = $strHere;
        $arrTemp["time"]    = $_SERVER["REQUEST_TIME"];
        $arrParam   = array_merge($arrTemp, $arrParam);
        $arrParam["sign"]    = $this->_signRequest($arrParam);
        $strUrl     = $strProt . "://" . self::$_config["server"] . "/" . ltrim($strFunc, "/");
        $strFull    = $strUrl . ($arrParam ? "?" . http_build_query($arrParam) : "");
        return $strFull;
    }
    /* }}} */

    /* {{{ public function verify($bolRedir = TRUE)  */
    public function verify($bolRedir = TRUE) {

        if (is_array($this->_info)) {
            $arrUser = NULL;

            //I think here should be a signature comes from server side to verify.
            if (isset($_GET["_ticket"])) {
                $strTicket = trim($_GET["_ticket"]);
                $strData = @openssl_decrypt($strTicket, self::ENCRYPT_METHOD, self::$_config["secret"]);
                if ($strData) {
                    $arrInfo = json_decode(gzuncompress($strData), TRUE);
                    Easy_Log::debug("data", $strData);
                    if (is_array($arrInfo) && $this->_info["renew"] == $arrInfo["time"]) {
                        $arrUser = $arrInfo["user"];    // Don't optimize this line.
                        $this->_info["user"] = $arrUser;
                        $this->_info["renew"] = 0;
                    }
                }
            }

            if (empty($arrUser) && isset($this->_info["user"]) && is_array($this->_info["user"])) {
                // Don't delete empty($arrUser)
                if ($this->_info["ip"] == $this->_getIp()) {
                    $arrUser = $this->_info["user"];
                }
            }
            Easy_Log::debug("user", $arrUser);

            if ($arrUser) {
                if ($_SERVER["REQUEST_TIME"] - $this->_info["renew"] > self::$_config["period"]) {
                    $this->_info["renew"] = $_SERVER["REQUEST_TIME"];
                    $this->_saveCookie($this->_info);
                    // Add sync verify if needs
                }

                return $arrUser;
            }

        }

        $this->_info = array(
            "renew" => $_SERVER["REQUEST_TIME"],
            "ip" => $this->_getIp(),
        );

        $this->_saveCookie($this->_info);

        if ($bolRedir) {
            if ($this->redirect("login")) {
                exit;
            }
        }

        return FALSE;

    }
    /* }}} */

    /* {{{ public function redirect($strFunc, array $arrParam = array())  */
    public function redirect($strFunc, array $arrParam = array()) {
        if (headers_sent()) {
            return FALSE;
        }
        $strUrl = $this->getUrl($strFunc, $arrParam);
        header("Location: {$strUrl}");
        return TRUE;
    }
    /* }}} */

    /* {{{ public function logout()  */
    public function logout() {

        if (empty($this->_info)) {
            return FALSE;
        }

        $strToken = $this->_info["token"];
        $arrParam = array(
            "userid"    => $this->_info["user"]["userId"],
        );
        $this->_removeCookie();
        $this->redirect("logout", $arrParam);

    }
    /* }}} */

}
