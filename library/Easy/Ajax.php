<?php
/**
 * Easy_Ajax lib
 * 
 * @package common
 * @author Jay Zhang <i@zh-ang.com>
 * @file Easy/Ajax.php
 * @copyright Copyright 2013 All right reserved.
 * @version 1.0
 * @since 2012-08-05
 * 
 **/

/* $Id$ */

class Easy_Ajax {

    public $status=0;
    public $result=NULL;
    public $error=array();

    /* {{{ protected function __construct ($mixRet=NULL, $intStatus=0)  */
    protected function __construct ($mixRet=NULL, $intStatus=0) {

        $this->result = $mixRet;
        $this->status = $intStatus;

    }
    /* }}} */

    /* {{{ public static function ajax($mixRet=NULL, $intStatus=0)  */
    public static function ajax($mixRet=NULL, $intStatus=0) {
        return new self($mixRet, $intStatus);
    }
    /* }}} */

    /* {{{ public function status($intStatus)  */
    public function status($intStatus) {
        $this->status = $intStatus;
        return $this;
    }
    /* }}} */

    /* {{{ public function result($mixRet)  */

    public function result($mixRet) {
        $this->result = $mixRet;
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
        return json_encode($this->toArray());
    }
    /* }}} */

    /* {{{ public function toArray()  */
    public function toArray() {
        $arrRet = (array)$this;
        if (empty($arrRet["error"])) {
            unset($arrRet["error"]);
        }
        if ($arrRet["result"] === NULL) {
            unset($arrRet["result"]);
        }
        return $arrRet;
    }
    /* }}} */

    /* {{{ public function __toString()  */
    public function __toString() {
        return $this->toJson();
    }
    /* }}} */

}
