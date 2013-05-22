<?php

class Easy_Geography {

    const EARTH_RADIUS = 6378.137; // in KM
    // const EARTH_RADIUS = 3963.1906; // in Mile

    /* {{{ protected static function _rad($d)  */
    protected static function _rad($d) {
        return pi() * $d / 180.0;
    }
    /* }}} */

    /* {{{ public static function getDistance($lat1, $lng1, $lat2, $lng2)  */
    public static function getDistance($lat1, $lng1, $lat2, $lng2) {
        $radLat1 = self::_rad($lat1);
        $radLat2 = self::_rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = self::_rad($lng1) - self::_rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
        return $s * self::EARTH_RADIUS;
    }
    /* }}} */

}
