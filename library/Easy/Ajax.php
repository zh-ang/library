<?php
/**
 * Easy_Ajax lib
 * 
 * @package ranktool
 * @author Jay Zhang <jay@easilydo.com>
 * @file Easy/Ajax.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-08-05
 * 
 **/

/* $Id$ */

class Easy_Ajax {

    public $status=0;
    public $return=NULL;
    public $error=array();

    /* {{{ protected function __construct ($mixRet=NULL, $intStatus=0)  */
    protected function __construct ($mixRet=NULL, $intStatus=0) {

        $this->return = $mixRet;
        $this->status = $intStatus;

    }
    /* }}} */

    /* {{{ public static function ajax($mixRet=NULL, $intStatus=0)  */
    public static function ajax($mixRet=NULL, $intStatus=0) {
        return new self($mixRet, $intStatus);
    }
    /* }}} */

    /* {{{ public static function prepare()  */
    public static function prepare() {

        $objReq = Yaf_Application::app()->getDispatcher()->getRequest();
        if (YAF_ENVIRON != "dev" && !$objReq->isXmlHttpRequest()) {
            if (headers_sent()) {
                throw new Easy_Exception("Unacceptable ajax request");
            } else {
                header("Location: ".self::webroot());
                exit;
            }
        }

        Yaf_Application::app()->getDispatcher()->disableView();

    }
    /* }}} */

    /* {{{ public function status($intStatus)  */
    public function status($intStatus) {
        $this->status = $intStatus;
        return $this;
    }
    /* }}} */

    /* {{{ public function data($mixRet)  */
    public function data($mixRet) {
        $this->return = $mixRet;
        return $this;
    }
    /* }}} */

    /* {{{ public function error($intCode, $strMsg="", $mixData=NULL)  */
    public function error($intCode, $strMsg="", $mixData=NULL) {
        $this->error[] = array(
            "code" => $intCode,
            "message" => "$strMsg",
            "data" => $mixData,
        );
        return $this;
    }
    /* }}} */

    /* {{{ public function toJson()  */
    public function toJson() {
        $arrRet = (array)$this;
        if (empty($arrRet["error"])) {
            unset($arrRet["error"]);
        }
        if (empty($arrRet["return"])) {
            unset($arrRet["return"]);
        }
        return json_encode($arrRet);
    }
    /* }}} */

    /* {{{ public function __toString()  */
    public function __toString() {
        return $this->toJson();
    }
    /* }}} */

}
