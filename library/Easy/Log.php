<?php
/**
 * Easy_Log
 * 
 * @package common
 * @author Jay Zhang <i@zh-ang.com>
 * @file Easy/Log.php
 * @copyright Copyright 2013 All right reserved.
 * @version 1.0
 * @since 2012-07-31
 * 
 **/

/* $Id$ */


class Easy_Log extends Easy_Singleton {

    const LOG_LEVEL_NONE    = 0x00;   /** 日志级别NONE    */
    const LOG_LEVEL_FATAL   = 0x01;   /** 日志级别FATAL   */
    const LOG_LEVEL_WARNING = 0x02;   /** 日志级别WARNING */
    const LOG_LEVEL_NOTICE  = 0x04;   /** 日志级别NOTICE  */
    const LOG_LEVEL_TRACE   = 0x08;   /** 日志级别TRACE   */
    const LOG_LEVEL_DEBUG   = 0x10;   /** 日志级别DEBUG   */
    const LOG_LEVEL_ALL     = 0xFF;   /** 日志级别ALL     */

    public static $arrLogLevels = array(
        self::LOG_LEVEL_NONE    => 'NONE',
        self::LOG_LEVEL_FATAL   => 'FATAL',
        self::LOG_LEVEL_WARNING => 'WARNING',
        self::LOG_LEVEL_NOTICE  => 'NOTICE',
        self::LOG_LEVEL_TRACE   => 'TRACE',
        self::LOG_LEVEL_DEBUG   => 'DEBUG',
        self::LOG_LEVEL_ALL     => 'ALL',
    );

    protected static $_intToken=0;

    protected $_strFile;
    protected $_intMask;
    protected $_intFilesize;
    protected $_intMaxline;
    protected $_bolWf;

    /* {{{ protected function __construct() */
    protected function __construct() {
        $objConfig          = Yaf_Application::app()->getConfig();
        $this->_intMask     = $objConfig->get("log.mask");
        $this->_intFilesize = $objConfig->get("log.filesize");
        $this->_intMaxline  = $objConfig->get("log.maxline");
        $this->_bolWf       = $objConfig->get("log.sep_wf");
        $strDir             = $objConfig->get("log.directory");
        $strFile            = //date("Ymd").".log";
                              "app.log";
        $this->_strFile     = rtrim($strDir, "/") . "/" . ltrim($strFile, "/");
    }
    /* }}} */

    /* {{{ public static function token($intToken = NULL) */
    public static function token($intToken = NULL) {
        if (is_int($intToken)) {
            self::$_intToken = $intToken;
        }
        return self::$_intToken;
    }
    /* }}} */

    /* {{{ protected function _writeLine($strLine, $strFile) */
    protected function _writeLine($strLine, $strFile) {
        if (file_exists($strFile)) {
            if($this->_intFilesize > 0) {
                clearstatcache();
                $arrStat = stat($strFile);
                if(is_array($arrStat)) {
                    $intSize = intval($arrStat['size']);
                    if ($intSize > $this->_intFilesize ) {
                        rename($strFile, $strFile.".bak");
                    }
                }
            }
        } else {
            touch($strFile);
        }

        $intLen = strlen($strLine);
        $maxLen = $this->_intMaxline;
        if ($intLen > $maxLen) {
            $strTail = " ... [${intLen}b]";
            $strLine = substr($strLine, 0, $maxLen-strlen($strTail)) . $strTail;
        }

        $strLine .= "\n";

        return file_put_contents($strFile, $strLine, FILE_APPEND);
    }
    /* }}} */

    /* {{{ public function writeLog($intLevel, $strMsg, $mixArg = NULL, $intDepth = 0 ) */
    public function writeLog($intLevel, $strMsg, $mixArg = NULL, $intDepth = 0 ) {
        if (!isset(self::$arrLogLevels[$intLevel])) {
            return FALSE;
        }
        
        if (is_string($intLevel)) {
            $strLevel = $intLevel;
            $intLevel = 0;
        } else {
            $intLevel = intval($intLevel);
            if ($intLevel & $this->_intMask == 0) {
                return TRUE;
            }
            $strLevel = self::$arrLogLevels[$intLevel];
        }


        $strMsg = strval($strMsg);

        $arrTrace = debug_backtrace();

        $arrStack = $arrTrace[min($intDepth, count($arrTrace) - 1)];
        $strFile = basename($arrStack['file']);
        $strLine = $arrStack['line'];

        $arrStack = $arrTrace[min($intDepth+1, count($arrTrace) - 1)];
        $strClass = isset($arrStack['class']) ? $arrStack['class'] : NULL;

        $strCont = preg_replace("/ ?([^\\w]) ?/", "\\1",
                    preg_replace("/\\s+/", " ", var_export($mixArg, TRUE)));

        $strLine =
            $strLevel . ": " .
            date('m-d H:i:s ') .
            "[" . ($strClass ? $strClass . "#" : "") . $strFile . ":" . $strLine . "] " .
            "ip[" . Easy_Util::getClientIP() . "] " .
            (self::$_intToken ? "token[".self::$_intToken."] " : "") .
            $strMsg .
            ($mixArg === NULL ? "" : " { " . $strCont . " }");

        $mixRet = $this->_writeLine($strLine, $this->_strFile);
        if ($mixRet === FALSE) return FALSE;

        if ($this->_bolWf) {
            if ($intLevel & (self::LOG_LEVEL_WARNING | self::LOG_LEVEL_FATAL) ) {
                $mixRet = $this->_writeLine($strLine, $this->_strFile.".wf");
                if ($mixRet === FALSE) return FALSE;
            }
        }

        return TRUE;
    }
    /* }}} */

    /* {{{ public static function debug($strMsg, $mixArg=NULL, $intDepth=0) */
    public static function debug($strMsg, $mixArg=NULL, $intDepth=0) {
        return self::getInstance()->writeLog(self::LOG_LEVEL_DEBUG, $strMsg, $mixArg, $intDepth+1);
    }
    /* }}} */

    /* {{{ public static function warning($strMsg, $mixArg=NULL, $intDepth=0) */
    public static function warning($strMsg, $mixArg=NULL, $intDepth=0) {
        return self::getInstance()->writeLog(self::LOG_LEVEL_WARNING, $strMsg, $mixArg, $intDepth+1);
    }
    /* }}} */

    /* {{{ public static function notice($strMsg, $mixArg=NULL, $intDepth=0) */
    public static function notice($strMsg, $mixArg=NULL, $intDepth=0) {
        return self::getInstance()->writeLog(self::LOG_LEVEL_NOTICE, $strMsg, $mixArg, $intDepth+1);
    }
    /* }}} */

    /* {{{ public static function trace($strMsg, $mixArg=NULL, $intDepth=0) */
    public static function trace($strMsg, $mixArg=NULL, $intDepth=0) {
        return self::getInstance()->writeLog(self::LOG_LEVEL_TRACE, $strMsg, $mixArg, $intDepth+1);
    }
    /* }}} */

    /* {{{ public static function fatal($strMsg, $mixArg=NULL, $intDepth=0) */
    public static function fatal($strMsg, $mixArg=NULL, $intDepth=0) {
        return self::getInstance()->writeLog(self::LOG_LEVEL_FATAL, $strMsg, $mixArg, $intDepth+1);
    }
    /* }}} */

}
