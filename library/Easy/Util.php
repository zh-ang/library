<?php
/**
 * Easy_Util
 * 
 * @package ranktool
 * @author Jay Zhang <jay@easilydo.com>
 * @file Util.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-07-31
 * 
 **/

/* $Id$ */


class Easy_Util {

    public static function getClientIP() /* {{{ */
    {
        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif( isset($_SERVER['HTTP_CLIENTIP']) )
        {
            $ip = $_SERVER['HTTP_CLIENTIP'];
        }
        elseif( isset($_SERVER['REMOTE_ADDR']) )
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        elseif( getenv('HTTP_X_FORWARDED_FOR') )
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif( getenv('HTTP_CLIENTIP') )
        {
            $ip = getenv('HTTP_CLIENTIP');
        }
        elseif( getenv('REMOTE_ADDR') )
        {
            $ip = getenv('REMOTE_ADDR');
        }
        else
        {
            $ip = '127.0.0.1';
        }

        $pos = strpos($ip, ',');
        if( $pos > 0 )
        {
            $ip = substr($ip, 0, $pos);
        }

        return trim($ip);
    }
    /* }}} */

    protected static $_webroot = NULL;
    public static function webroot($strUri = "") /* {{{ */
    {
        if (self::$_webroot === NULL) {
            self::$_webroot = Yaf_Application::app()->getConfig()->get("env.webroot");
            if (empty(self::$_webroot)) self::$_webroot = "http://".$_SERVER["HTTP_HOST"]."/";
        }
        return rtrim(self::$_webroot, "/") . "/" . ltrim($strUri, "/");
    }
    /* }}} */

    protected static $_pubroot = NULL;
    protected static $_version = NULL;
    public static function pubroot($strUri = "") /* {{{ */
    {
        if (self::$_pubroot === NULL) {
            self::$_pubroot = Yaf_Application::app()->getConfig()->get("env.pubroot");
            if (empty(self::$_pubroot)) {
                self::$_pubroot = rtrim(self::webroot(), "/")."/public";
            }
        }
        if (self::$_version === NULL) {
            self::$_version = Yaf_Application::app()->getConfig()->get("env.version");
            if (empty(self::$_version)) self::$_version = date("Ymd");
        }
        return rtrim(self::$_pubroot, "/") . "/" . ltrim($strUri, "/") . "?v=" . self::$_version;
    }
    /* }}} */

    /* {{{ public static function explain(Exception $e)  */
    public static function explain(Exception $e) {
        return $e->getMessage();
    }
    /* }}} */

}
