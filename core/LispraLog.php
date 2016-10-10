<?php

/**
 * Description of LispraLog
 *
 * @author josem
 */
class LispraLog {

    public static $FILE_NAME = LISPRA_BETA_LOG_FILE;

//    public static function getFileName(){return LISPRA_BETA_LOG_FILE;}
    public static function getTimeStamp() {
        return date("Y-m-d H:i:s", time());
    }

    public static function log($msg) {
        $s = sprintf("[%s] %s\r\n", self::getTimeStamp(), $msg);
        return file_put_contents(self::$FILE_NAME, $s, FILE_APPEND | LOCK_EX);
    }

    public static function info($msg) {
        return self::log("Info:  $msg");
    }

    public static function error($msg) {
        return self::log("Error: $msg");
    }

    public static function classLog($class, $method, $msg) {
        return self::log("$class :: $method $msg");
    }

    public static function classError($class, $method, $msg) {
        return self::classLog($class, $method, "Error : $msg");
    }

    public static function getLogContent() {
        return file_get_contents(self::$FILE_NAME);
    }



}

?>