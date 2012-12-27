<?php
/**
 * Agent8_Persistence
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Agent8/Persistence.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-10-22
 * 
 **/

/* $Id$ */

class Agent8_Persistence extends Agent8_Abstract {

    /* {{{ protected function _getUrl($suffix)  */
    protected function _getUrl($suffix) {
        return parent::_getUrl("/persistence/".ltrim($suffix, "/"));
    }
    /* }}} */

    /* {{{ public function processProposedDos($requestId, $doType, array $doResponse)  */
    /* Sample call:
       Agent8_Persistence::getInstance()->processProposedDos(uniqid(), 2016, array(
           array (
               "userName" => UserModel::getInstance()->getUsername(),
               "streamId" => 0,
               "uniqueId" => uniqid(),
               "variables" => array(
                   "songId" => 123,
                   "genre"  => "pop",
               ),
           ),
       ));
     */
    public function processProposedDos($requestId, $doType, array $doResponse) {

        $arrData = array(
            "requestId"     => $requestId,
            "doType"        => $doType,
            "doResponse"    => $doResponse,
        );
        
        $strBody = $this->_post($this->_getUrl(__FUNCTION__."?deviceId=doBuilder"), json_encode($arrData));

        return TRUE;
        
    }
    /* }}} */

    /* {{{ public function processProposedDo($requestId, $doType, $userName, $streamId, array $variables)  */
    /* Sample call:
       Agent8_Persistence::getInstance()->processProposedDos(uniqid(), 2016,
                UserModel::getInstance()->getUsername(), 0, array(
                   "songId" => 123,
                   "genre"  => "pop",
                ),
            );
     */
    public function processProposedDo($requestId, $doType, $userName, $streamId, array $variables) {

        $doResponse = array(
            array(
                "userName" => $userName,
                "streamId" => $streamId,
                "uniqueId" => uniqid(),
                "variables" => $variables,
            ),
        );

        return $this->processProposedDos($requestId, $doType, $doResponse);
        
    }
    /* }}} */

    /* {{{ public function getData($token)  */
    public function getData($token) {

        $arrData = array( "userName"  => $token, );
        
        $strBody = $this->_get($this->_getUrl(__FUNCTION__), $arrData);

        return json_decode($strBody, TRUE);
        
    }
    /* }}} */

}

