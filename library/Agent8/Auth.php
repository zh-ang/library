<?php
/**
 * Agent8_Auth
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Agent8/Auth.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-10-22
 * 
 **/

/* $Id$ */

class Agent8_Auth extends Agent8_Abstract {

    /* {{{ protected function _getUrl($suffix)  */
    protected function _getUrl($suffix) {
        return parent::_getUrl("/auth/".ltrim($suffix, "/"));
    }
    /* }}} */

    /* {{{ public function authUser($username, $password)  */
    public function authUser($username, $password) {

        $arrRet = array(
            "code" => 0,
            "token" => "",
        );

        $arrData = array(
            "username" => $username,
            "password" => $password,
        );
        
        $strBody = $this->_post($this->_getUrl(__FUNCTION__."?deviceId=doBuilder"), $arrData);

        $arrTemp = explode(",", $strBody);
        $arrTemp && ($arrRet["code"] = array_shift($arrTemp));
        $arrTemp && ($arrRet["token"] = array_shift($arrTemp));

        return $arrRet;
        
    }
    /* }}} */

    /* {{{ public function setUserAuth($username, $password, $email, ... */
    /**
     * @return codes:
     * * ret codes:
     * 100 - Ok username and email address
     * 101 - Ok username and no email address
     * 200 - Bad username
     * 201 - Username already exist
     * 202 - Bad password
     * 203 - Bad username and password
     * 204 - General server Error
     * 205 - Pending activation
     */
    public function setUserAuth($username, $password, $email,
                $pushtoken="", $mobile="", $accessToken="", $fbEnabled="",
                $firstName="", $lastName="", $fbAccessToken="", $fbExpirationDate="", $fbId="",
                $birthday="", $verifyEmail="") {

        $arrData = array(
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "deviceid" => "doBuilder",
        );

        $pushtoken && $arrData["pushtoken"] = $pushtoken;
        $mobile && $arrData["mobile"] = $mobile;
        $accessToken && $arrData["accessToken"] = $accessToken;
        $fbEnabled && $arrData["fbEnabled"] = $fbEnabled;
        $firstName && $arrData["firstName"] = $firstName;
        $lastName && $arrData["lastName"] = $lastName;
        $fbAccessToken && $arrData["fbAccessToken"] = $fbAccessToken;
        $fbExpirationDate && $arrData["fbExpirationDate"] = $fbExpirationDate;
        $fbId && $arrData["fbId"] = $fbId;
        $birthday && $arrData["birthday"] = $birthday;
        $verifyEmail && $arrData["verifyEmail"] = $verifyEmail;
        
        return $this->_post($this->_getUrl(__FUNCTION__), $arrData);

    }
    /* }}} */

    /* {{{ public function logout($eat)  */
    public function logout($eat) {

        $arrData = array(
            "eat" => $eat,
        );
        
        $strBody = $this->_post($this->_getUrl(__FUNCTION__."?deviceId=doBuilder"), $arrData);

        return TRUE;

    }
    /* }}} */

}
