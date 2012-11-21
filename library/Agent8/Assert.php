<?php
/**
 * Agent8_Assert
 * 
 * @package easilydo
 * @author Jay Zhang <jay@easilydo.com>
 * @file Agent8/Assert.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-10-22
 * 
 **/

/* $Id$ */

class Agent8_Assert extends Agent8_Abstract {


    public function get($path, $data) {
        return $this->_get($this->_getUrl($path), $data);
    }

    public function post($path, $data) {
        return $this->_post($this->_getUrl($path), $data);
    }

}
