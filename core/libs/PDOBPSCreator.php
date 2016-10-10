<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDOBPSCreator
 *
 * @author josem
 */
class PDOBPSCreator {
//INSERT INTO subs
//  (subs_name, subs_email, subs_birthday)
//VALUES
//  (?, ?, ?)
//ON DUPLICATE KEY UPDATE
//  subs_name     = VALUES(subs_name),
//  subs_birthday = VALUES(subs_birthday)
    public static function insertValues($table, $columns) {

        $params = array();
        foreach ($columns as $c) {
            $params[] = ":" . $c;
        }
        return sprintf("INSERT IGNORE INTO %s (%s) VALUES (%s)", $table, implode(",", $columns), implode(",", $params));
//        return sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, implode(",", $columns), implode(",", $params));
    }

    public static function selectRowWhereColumnsEquals($table, $columns) {

        $params = array();
        foreach ($columns as $c) {
            $params[] = "$c=:$c";
        }

        return sprintf("SELECT * FROM %s WHERE %s", $table, implode(" AND ", $params));
    }

    public static function toBindedParameterKey($key) {
        try {
            return ($key[0] == ':') ? $key : (":$key");
        } catch (Exception $exc) {
            return "";
        }

        return "";
    }

    public static function assocExtractParams($array, $keys) {
        $o = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $o[":$key"] = $array[$key];
            }
        }



        return $o;
    }

    public static function assoc2Params($array, $excluded = null) {
        $o = array();
        $a = array();
        if (!is_null($excluded) && is_array($excluded)) {

            foreach ($excluded as $e) {
                if (array_key_exists($e, $array)) {
                    unset($array[$e]);
                }
            }
        }
        foreach ($array as $key => $value) {

            $o[":" . $key] = $value;
        }

        return $o;
    }

}


?>
