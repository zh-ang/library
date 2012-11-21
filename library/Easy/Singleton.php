<?php
/**
 * Easy_Singleton
 * 
 * @package ranktool
 * @author Jay Zhang <jay@easilydo.com>
 * @file Easy/Singleton.php
 * @copyright Copyright 2012 Easilydo Inc. 
 * @version 1.0
 * @since 2012-07-31
 * 
 **/

/* $Id$ */

abstract class Easy_Singleton {

    protected static $_instance=array();

    /* {{{ public static function getInstance() */
    public static function getInstance() {

        $class = get_called_class();

        if (isset(self::$_instance[$class])) {
            if (self::$_instance[$class] instanceof $class) {
                return self::$_instance[$class];
            }
        }

        $obj = new $class;
        self::$_instance[$class] = $obj;
        return $obj;

    }
    /* }}} */

    /* {{{ protected function __construct() */
    protected function __construct() {
    }
    /* }}} */

}
