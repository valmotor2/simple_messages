<?php
namespace app\helpers;

class Utils {

    static function debug($any, $stop = false) {
        echo '<pre>';
        print_r($any);
        echo '<hr />';
        var_dump($any);
        echo '</pre>';

        if($stop) die;
    }

    static function filter_destination($destination) {
        if(strlen($destination) === 10) {
            return '+4'.$destination;
        }

        return $destination;
    }

}