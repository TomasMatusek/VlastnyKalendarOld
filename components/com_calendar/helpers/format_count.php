<?php

class FormalCountHelper {

    private static $count = array(
        'o' => array(
            'cover'     => 1,
            'january'   => 3,
            'february'  => 2,
            'march'     => 2,
            'april'     => 2,
            'may'       => 2,
            'june'      => 3,
            'july'      => 2,
            'august'    => 2,
            'september' => 2,
            'october'   => 2,
            'november'  => 2,
            'december'  => 3
        ),
        'p' => array(
            'cover'     => 2,
            'january'   => 2,
            'february'  => 2,
            'march'     => 2,
            'april'     => 2,
            'may'       => 2,
            'june'      => 2,
            'july'      => 2,
            'august'    => 2,
            'september' => 2,
            'october'   => 2,
            'november'  => 2,
            'december'  => 2
        ),
        'q' => array(
            'cover'     => 1,
            'january'   => 1,
            'february'  => 1,
            'march'     => 1,
            'april'     => 1,
            'may'       => 1,
            'june'      => 1,
            'july'      => 1,
            'august'    => 1,
            'september' => 1,
            'october'   => 1,
            'november'  => 1,
            'december'  => 1
        ),
        'default' => array(
            'cover'     => 1,
            'january'   => 1,
            'february'  => 1,
            'march'     => 1,
            'april'     => 1,
            'may'       => 1,
            'june'      => 1,
            'july'      => 1,
            'august'    => 1,
            'september' => 1,
            'october'   => 1,
            'november'  => 1,
            'december'  => 1
        ),
    ); 
    
    public static function getFormatCount($type, $month) {
        $counts = FormalCountHelper::$count;
        // return array_key_exists($type, $counts) ? $counts[$type][$month] : $counts['default'][$month];
        return 1;
    }
}