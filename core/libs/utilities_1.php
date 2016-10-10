<?php

//class MySQLHelper{
//    
//}
function arrayToSQLValues(&$array) {
    $keys = array_keys($array);
    $out = array();
    foreach ($keys as $key) {
        if (is_array($array[$key])) {
            $out[$key] = assocArrayToSQLValues($array[$key]);
        } else if (is_string($array[$key])) {
            if (startsWith($array[$key], "'") && endsWith($array[$key], "'")) {
                $out[$key] = $array[$key];
//            }else if (startsWith($array[$key], '"') && endsWith($array[$key], '"')) {
//                $out[$key] = "'".$array[$key]."'";
            } else {
                $out[$key] = "'" . $array[$key] . "'";
            }
        } else {
            $out[$key] = $array[$key];
        }
    }
    return $out;
}

function assocArrayToSQLValues(&$array) {
    $keys = array_keys($array);
    $out = array();
    foreach ($keys as $key) {
        if (is_array($array[$key])) {
            $out[$key] = assocArrayToSQLValues($array[$key]);
        } else if (is_string($array[$key])) {
            if (startsWith($array[$key], "'") && endsWith($array[$key], "'")) {
                $out[$key] = $array[$key];
            } else {
                $out[$key] = "'" . $array[$key] . "'";
            }
        } else {
            $out[$key] = $array[$key];
        }
    }
    return $out;
}

function assocArrayToSQLSetString(&$array, $prepend_set = false) {
    $keys = array_keys($array);
    $a = assocArrayToSQLValues($array);
    foreach ($a as $key => $value) {
        $se[] = "$key = $value";
    }
    $out = $prepend_set ? "SET " : "";
    $out = $out . implode(",", $se);

    return $out;
}

function createInsertStatement($tableName, $valuesArray, $columnsArray = NULL) {
    $values = (is_array($valuesArray)) ? implode(",", $valuesArray) : $valuesArray;
    if (is_null($columnsArray) == false) {
        $columns = (is_array($columnsArray)) ? implode(",", $columnsArray) : $columnsArray;
        return sprintf("INSERT INTO %s (%s) VALUES(%s)", $tableName, $columns, $values);
    }
    return sprintf("INSERT INTO %s VALUES(%s)", $tableName, $values);
}

function createInsertAssocStatement($tableName, &$array) {
    $str = sprintf("INSERT INTO %s (%s) VALUES (%s)", $tableName, implode(",", array_keys($array)), implode(",", assocArrayToSQLValues(array_values($array)))
    );

//  br($str);
    return $str;
}

function createInsertOnDuplicateKeyUpdateAssocStatement($tableName, &$array, $keysToUpdate = null) {

    $dupKeys = is_null($keysToUpdate) ? array_keys($array) : (is_string($keysToUpdate) ? array($keysToUpdate) : $keysToUpdate);
    $dupKeyValues = array();
    foreach ($dupKeys as $key) {
        $dupKeyValues[] = "$key=VALUES($key)";
    }
    $str = sprintf("INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s", $tableName, implode(",", array_keys($array)), implode(",", assocArrayToSQLValues(array_values($array))), implode(",", $dupKeyValues)
    );

//  br($str);
    return $str;
}

function createSelectStatement($tableName, $columns = "*", $where = "1") {

//    $keys = is_array($columns) ? $columns : array($columns);
    return sprintf("SELECT %s FROM %s WHERE %s", implode(",", is_array($columns) ? $columns : array($columns)), $tableName, $where);
}

function createReplaceIntoStatement($tableName, $columns, $values) {
    $v = is_array($values) ? $values : array($values);
    return sprintf("REPLACE INTO %s (%s) VALUES (%s)", $tableName, implode(",", is_array($columns) ? $columns : array($columns)), implode(",", arrayToSQLValues($v))
    );
}

?>