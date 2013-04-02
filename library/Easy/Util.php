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
        return sprintf("[%s] %s", gettype($e), $e->getMessage());
    }
    /* }}} */

    /* {{{ public static function date($strFormat, $strZone="+00:00", $mixTime="now")  */
    public static function date($strFormat, $strZone="+00:00", $mixTime="now") {

        if (preg_match("/^[+-]\\d\\d:\\d\\d$/", $strZone)) {
            $objTemp = new DateTime($strZone);
            $intOffset = $objTemp->getOffset();
        } else {
            Easy_Log::notice("Bad timezone", $strZone);
            $strZone = "+00:00";
            $intOffset = 0;
        }

        $intTime = is_string($mixTime) ? strtotime($mixTime) : intval($mixTime);
        $intTime += $intOffset;
        $strTime = date("Y-m-d\TH:i:s", $intTime) . $strZone;
        $objTime = new DateTime($strTime);
        return $objTime->format($strFormat);

    }
    /* }}} */

    /* {{{ public static function ip2zone($strIp)  */
    public static function ip2zone($strIp) {
        if (!function_exists("geoip_record_by_name")) {
            return NULL;
        }
        $arrRegion      = geoip_record_by_name($strIp);
        if (empty($arrRegion)) {
            return NULL;
        }
        $strTimezone    = geoip_time_zone_by_country_and_region($arrRegion['country_code'], $arrRegion['region']);
        if (empty($strTimezone)) {
            return NULL;
        }
        try {
            $objTimezone    = new DateTimeZone($strTimezone);
            $objDatetime    = new DateTime("now", $objTimezone);
            $intOffset      = $objTimezone->getOffset($objDatetime);
        } catch (Exception $e) {
            Easy_Log::notice("An exception on Date/Time/Zone", Easy_Util::explain($e));
            return NULL;
        }
        $intHour        = $intOffset/3600;
        $intMinute      = abs($intOffset/60)%60;
        return sprintf("%s%02d:%02d", $intHour < 0 ? "-" : "+", floor(abs($intHour)), $intMinute);
    }
    /* }}} */

}
