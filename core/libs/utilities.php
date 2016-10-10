<?php

function pow2($order) {
    if ($order == 0) {
        return 1;
    }
    return 2 * pow2($order - 1);
}

function reg2array($reg, $size = 0) {
    $str = strrev(decbin($reg));
    if ($size === 0) {
        $size = strlen($str);
    }
    $a = array_fill(0, $size, 0);
    for ($n = 0; $n < strlen($str); $n++) {
        $a[$n] = (int) $str[$n];
    }
    return $a;
}

function array2reg($a) {
    $str = "";
    for ($n = 0; $n < count($a); $n++) {
        $str .= $a[$n];
    }
    return bindec(strrev($str));
}

function isNullorEmpty($d) {
    if (is_null($d)) {
        return true;
    }
    if (is_string($d) && (strlen($d) < 1)) {
        return true;
    } elseif (is_array($d) && (count($d) < 1)) {
        return true;
    }
    return false;
}

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function br($str = "") {
    if (isNullorEmpty($str)) {
        echo "<br>";
    } else if (is_array($str)) {
        print_r($str);
        br();
    } else if (is_object($str)) {
        print_r($str);
        br();
    } else {
        echo $str;
        br();
    }
}

function println() {
    echo "\r\n";
}

function getIfSet(&$value, $default = null) {
    return isset($value) ? $value : $default;
}

function getRequestParameterIfSet($param, $default = null) {
    return getIfSet($_REQUEST[$param], $default);
}

function getRequestBody() {
    return file_get_contents('php://input');
}

function getRequestBodyLines() {
    return explode("\r\n", file_get_contents('php://input'));
}

function sqlTimeStamp($unixTime = 'time()') {
//    return date("Y-m-d H:i:s", $unixTime === 'time()' ? time():$unixTime);
    return date("Y-m-d H:i:s", $unixTime === 'time()' ?
                    time() :
                    (is_string($unixTime) ? strtotime($unixTime) : $unixTime))
    ;
}

function sprintf_assoc($string = '', $replacement_vars = array(), $prefix_character = '%') {
    if (!$string)
        return '';
    if (is_array($replacement_vars) && count($replacement_vars) > 0) {
        foreach ($replacement_vars as $key => $value) {
            $string = str_replace($prefix_character . $key, $value, $string);
        }
    }
    return $string;
}

function printf_assoc($string = '', $replacement_vars = array(), $prefix_character = '%') {
    echo sprintf_assoc($string, $replacement_vars, $prefix_character);
}

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

function mysqli_fetch_rows($result) {
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_row($result)) {
//            array_push($rows, array_map('utf8_encode', $row));
            $rows[] = array_map('utf8_encode', $row);
//            array_push($rows,$row);
        }
    }
    return $rows;
}

function mysqli_fetch_assocs($result) {
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = array_map('utf8_encode', $row);
//            array_push($rows,  array_map('utf8_encode', $row));
//            array_push($rows,$row);
        }
    }
    return $rows;
}

function array_string_keys(&$array) {
    $keys = array_keys($array);
    $out = array();
    foreach ($keys as $key) {
        if (is_string($key)) {
            array_push($out, $key);
        }
    }
    return($out);
}

function array_extract_str_assoc($array) {
    $keys = array_string_keys($array);
    $out = array();
    foreach ($keys as $key) {
        $out[$key] = $array[$key];
    }
    return($out);
}

function array_extract_keys($data, $valid_keys) {
    $d = array();
    foreach ($valid_keys as $key) {
        if (array_key_exists($key, $data)) {
            $d[$key] = $data[$key];
        }
    }
    return $d;
}

function p_matrix_column_to_array(&$rows, $key) {
    $out = array();
    foreach ($rows as $r) {
        $out[] = $r[$key];
    }
    return $out;
}

function matrix_column_to_array($rows, $key) {
    return p_matrix_column_to_array($rows, $key);
}

function format_json($json, $html = false) {
    $tabcount = 0;
    $result = '';
    $inquote = false;
    $ignorenext = false;

    if ($html) {
        $tab = "&nbsp;&nbsp;&nbsp;";
        $newline = "<br/>";
    } else {
        $tab = "\t";
        $newline = "\n";
    }

    for ($i = 0; $i < strlen($json); $i++) {
        $char = $json[$i];

        if ($ignorenext) {
            $result .= $char;
            $ignorenext = false;
        } else {
            switch ($char) {
                case '{':
                    $tabcount++;
                    $result .= $char . $newline . str_repeat($tab, $tabcount);
                    break;
                case '}':
                    $tabcount--;
                    $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                    break;
                case ',':
                    if ($inquote)
                        $result .=$char;
                    else
                        $result .= $char . $newline . str_repeat($tab, $tabcount);
                    break;
                case '"':
                    $inquote = !$inquote;
                    $result .= $char;
                    break;
                case '\\':
                    if ($inquote)
                        $ignorenext = true;
                    $result .= $char;
                    break;
                default:
                    $result .= $char;
            }
        }
    }

    return $result;
}

function json_format_encode($data, $html = false) {
    return format_json(json_encode($data));
}

function json_decode_file($filePath, $assoc = false) {
    return json_decode(file_get_contents($filePath), $assoc);
}

function array_str_replace($search, $replace, $strArray) {
    if (is_array($strArray)) {
        $keys = array_keys($strArray);
        foreach ($keys as $key) {
            $strArray[$key] = array_str_replace($search, $replace, $strArray[$key]);
        }
    } else if (is_string($strArray)) {
        $strArray = str_replace($search, $replace, $strArray);
    }
    return $strArray;
}

function csvToAssoc($content, $delimiter = ";", $endLine = "\r\n", $dataBetween = "\"") {

    $rows = explode($endLine, $content);
    $n_rows = count($rows);
    $out = array();
    $keys = explode($delimiter, $rows[0]);
    $n_keys = count($keys);
    for ($n = 1; $n < $n_rows; $n++) {
        $values = array_str_replace($dataBetween, "", explode($delimiter, $rows[$n], $n_keys));
        while (count($keys) > count($values)) {
            array_push($values, "");
        }
//        $out = array_combine($keys, $values);
        array_push($out, array_combine($keys, $values));
    }
    return $out;
}

function csvToJson($content, $delimiter = ";", $endLine = "\r\n", $dataBetween = "\"") {
    return json_encode(csvToAssoc($content, $delimiter, $endLine, $dataBetween));
}

function csvToJSONTableRowsObject($tableName, $content, $delimiter = ";", $endLine = "\r\n", $dataBetween = "\"") {

    $out = array();
    $out ["table"] = $tableName;
    $out ["rows"] = csvToAssoc($content, $delimiter, $endLine, $dataBetween);
    return json_encode($out);
}

function csvTableFileToJSONTableRowsObjectFile($csvFileName, $delimiter = ";", $endLine = "\r\n", $dataBetween = "\"") {

    $pathInfo = pathinfo($csvFileName);
    $tableName = $pathInfo['filename'];
    $outFileName = str_replace(".csv", ".json", $csvFileName);

    $json = csvToJSONTableRowsObject($tableName, file_get_contents($csvFileName), $delimiter, $endLine, $dataBetween);
    file_put_contents($outFileName, format_json($json));
}

function multi_array_select($multi, $needles, $outputs) {
    foreach ($multi as $row) {
        $found = true;
        foreach ($needles as $key => $value) {
            if (strcmp("" . $row[$key], "" . $value) != 0) {
                $found = false;
                break;
            }
        }

        if ($found) {

            if (is_array($outputs)) {
                $out = array();
                foreach ($outputs as $value) {
                    $out[$value] = $row[$value];
                }
                return $out;
            } else {
                return $row[$outputs];
            }
        }
    }
    return NULL;
}

function mySqlDataTypesToGChartsDataTypes($mTypes) {

    $types = array();
    for ($n = 0; $n < count($mTypes); $n++) {
//                $type = strstr($r['Type'],"(")
//                $line = sprintf("%s %s",$r['Field'],$r['Type']);
        $type = $mTypes[$n];

//                if(startsWith($type, "int")){ $type = strstr($r['Type'],"(",true);}
        if (strpos($type, "int") !== false) {
            $type = "number";
        } else if (strpos($type, "varchar") !== false) {
            $type = "string";
        } else if (strpos($type, "text") !== false) {
            $type = "string";
        } else if (strpos($type, "float") !== false) {
            $type = "number";
        } else if (strpos($type, "timestamp") !== false) {
            $type = "date";
        } else {
            $type = "string";
        }

        $types[$n] = $type;
    }

    return $types;
}

function getTableNameFromMySQLStatement($s) {
    $t = explode(" ", $s);
    $ok = false;
    for ($n = 0; $n < count($t); $n++) {
        if ($ok == true) {
            return $t[$n];
        } else if (strtolower($t[$n]) == 'from') {
            $ok = true;
        }
    }
    return "";
}

function array_replace_values($array, $old, $new) {
    foreach ($array as $key => $value) {
        if ($value == $old) {
            $array[$key] = $new;
        }
    }

//    for ($n = 0; $n < count($array); $n++) {
//        if ($array[$n] == $old) {
//            $array[$n] = $new;
//        }
//    }
    return $array;
}

function isJson($string) {
    if (!is_string($string))
        return false;
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function isEmail($string) {
    return filter_var($string, FILTER_VALIDATE_EMAIL);
}

/**
 * Will return the string for the html table created from assoc array
 * Input must be an array of assoc arrays.Keys will be taken from the first row
 * and then each key will be searched on each row.
 * @param assocs array $array_of_assoc 
 */
function arrayOfAssocsToHeadersRowsAssoc($array_of_assoc) {
    $o = array(
        'headers' => array(),
        'rows' => array()
    );
    if (isNullorEmpty($array_of_assoc))
        return $o;

    if (is_array($array_of_assoc[0])) {
        if (!isNullorEmpty($array_of_assoc[0])) {
// get columns from first line
            $o['headers'] = array_keys($array_of_assoc[0]);

            foreach ($array_of_assoc as $row) {
                $o['rows'][] = array_values($row);
            }
        }
    }

    return $o;
}

function createHtmlTableFromHeadersRows($headers_rows, $add_index = false) {
    $hr = $headers_rows;

    if (isJson($headers_rows))
        $hr = json_decode($headers_rows);

    $html_text = '<table class="table">'
            . '<thead>'
            . '%s'
            . '</thead>'
            . '<tbody>'
            . '%s'
            . '</tbody>'
            . '</table>';

//    br("HTML TEXT : $html_text");

    $rows = array();
    $i = 0;
    foreach ($hr['rows'] as $row) {
        $rows[] = (($add_index) ? '<th scope ="row">' . $i++ . '</th><td>' : "<td>") . implode("</td><td>", $row) . "</td>";
    }

    if (isNullorEmpty($rows))
        return "<span><a>No data to plot</a></span>";
    $thead = (($add_index) ? "<tr><th>#</th>" : "<tr>") . "<th>" . implode("</th><th>", $hr['headers']) . "</th></tr>";
//    br("headers : $thead");
    $trows = "<tr>" . implode("</tr><tr>", $rows) . "</tr>";
//    br("rows : $trows");
    $output = sprintf($html_text, $thead, $trows);

    return $output;
}

function sqlize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sqlize($data[$key]);
        }
    } else if (is_string($data)) {
        if (!(startsWith($data, "'") && endsWith($data, "'"))) {
            return "'" . $data . "'";
        }
    }

    return $data;
}

function createHtmlTableFromAssocsArray($assocs, $add_index = false) {
    return createHtmlTableFromHeadersRows(arrayOfAssocsToHeadersRowsAssoc($assocs), $add_index);
}

function sql_key_equals_value_string($key, $value) {
    return "$key = " . sqlize($value);
}

function array_to_attrs_string($attrs, $default_attrs = null) {

    $attrs_str = "";
    $attrs_chunks = array();
    $a = ((isNullorEmpty($default_attrs)) ? (((isNullorEmpty($attrs)) ? array() : $attrs)) : (((isNullorEmpty($attrs)) ? $default_attrs : array_merge($default_attrs, $attrs)))
            );


    foreach ($a as $key => $value) {
        if (is_int($key)) {
            $attrs_chunks[] = $value;
        } else {
            $attrs_chunks[] = "$key=" . '"' . $value . '" ';
        }
    }


//avoid repeated
    $o = array();
    foreach ($attrs_chunks as $chunk) {

        $new = true;
        foreach ($o as $v) {
            if ($v == $chunk) {
                $new = false;
                break;
            }
        }
        if ($new) {
            $o[] = $chunk;
        }
    }
    $attrs_str = implode(" ", $o);
    return $attrs_str;
}

function sprintf_on_values_and_implode($sprintf_str, $glue, $array) {

    if (isNullorEmpty($array)) {
        return "";
    }

    foreach ($array as &$a) {
        $a = str_replace("%s", $a, $sprintf_str);
    }
    return implode($glue, $array);
}

class JsonObjectHelper {

    private $json_data_string = '{}';

    function __construct($meta_data) {
        $this->setData($meta_data);
    }

    public function setData($meta_data) {
        if (is_string($meta_data)) {
            if (isJson($meta_data))
                $this->json_data_string = $meta_data;
        }else if (is_array($meta_data)) {
            $this->json_data_string = json_encode($meta_data);
        }
    }

    public function toAssoc() {
        return json_decode($this->json_data_string, true);
    }

    public function fromAssoc($meta_data) {
        $this->setData($meta_data);
    }

    public function toJson() {
        return $this->json_data_string;
    }

    public function fromJson($meta_data) {
        $this->setData($meta_data);
    }

    public function getValue($key) {
        $d = $this->toAssoc();
        return (array_key_exists($key, $d)) ?
                $d[$key] : NULL;
    }

    public function setValue($key, $value) {
        $d = $this->toAssoc();
        $d[$key] = $value;
        $this->fromAssoc($d);
    }

    public function set($data) {
        $d = $this->toAssoc();

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $d[$key] = $value;
            }
            $this->fromAssoc($d);
        }
    }

    public function get($key_or_keys) {
        $d = $this->toAssoc();
        if (is_array($key_or_keys)) {
            $o = array();
            foreach ($key_or_keys as $key) {
                if (array_key_exists($key, $d)) {
                    $o[$key] = $d[$key];
                }
            }
            return $o;
        }


        return (array_key_exists($key_or_keys, $d)) ?
                $d[$key_or_keys] : NULL;
    }

    public function toMySqlFormattedJSON() {
        return mysql_real_escape_string($this->toJson());
    }

    public function delete($key_or_keys) {
        $d = $this->toAssoc();
        if (is_array($key_or_keys)) {
            foreach ($key_or_keys as $key) {
                if (array_key_exists($key, $d)) {
                    unset($d[$key]);
                }
            }
        } else if (array_key_exists($key_or_keys, $d)) {
            unset($d[$key_or_keys]);
        }
        $this->fromAssoc($d);
    }

}

class ClassConverter {

    function __construct() {
        
    }

    public function getKey($key) {
        $object_keys = array_keys(get_object_vars($this));
        if (in_array($key, $object_keys)) {
            return $this->{$key};
        }
    }

    public function fromAssoc($data) {
//        br("fromAssoc START ");
        $object_keys = array_keys(get_object_vars($this));
        foreach ($object_keys as $key) {
//             br("Checking key $key ");
            if (array_key_exists($key, $data)) {
//                  br("$key exists ");
                $this->{$key} = $data[$key];
//                 br("$key set ");
            }
//            else  br("$key doesn't exists ");
        }
//        br("fromAssoc END ");
    }

    public function toAssoc() {
        return get_object_vars($this);
    }

    public function fromJSON($json) {
        $this->fromAssoc(json_decode($json));
    }

    public function toJSON() {
        return json_encode($this->toAssoc());
    }

}

class TagsUtils {

    /**
     * Parse string Will parse tags string or array to a valid tags string
     * @param type $tags_str_or_array String or array containing tags
     * @return string Formatted tags string such as #tag1 #tag2 #tag3... #tagN
     */
    public static function parseToString($tags_str_or_array) {
        $tags = tags_array($tags_str_or_array);
        if (count($tags) > 0) {
            return "#" . implode(" #", $tags);
        }
        return "";
    }

    /**
     * Parse string Will parse tags string or array to a valid tags array
     * @param type $tags_str_or_array String or array containing tags
     * @return array Array of tags with all values validated. No # or spaces inside each tag 
     */
    public static function parseToArray($tags_str_or_array) {
        $out = array();
        if (is_string($tags_str_or_array)) {
            $out = array_values(array_filter(explode(' ', str_replace("#", " ", $tags_str_or_array)), 'strlen'));
        } elseif (is_array($tags_str_or_array)) {
            $out = str_replace(array("#", " "), "", $tags_str_or_array);
            $out = array_values(array_filter($out, 'strlen'));
        }

        return $out;
    }

}

function tags_string($tags) {
    $out = "";
    $pieces = tags_array($tags);
    if (count($pieces)) {
        $out = "#" . implode(" #", $pieces);
    }
    return $out;
}

function tags_array($tags) {
    $out = array();
    if (is_string($tags)) {
        $out = array_values(array_filter(explode(' ', str_replace("#", " ", $tags)), 'strlen'));
    } elseif (is_array($tags)) {
        $out = str_replace(array("#", " "), "", $tags);
        $out = array_values(array_filter($out, 'strlen'));
    }

    return $out;
}

class ArrayUtils {

//    public static function isNullOrEmpty($array){
//        if(is_null($var))
//    }

    public static function defaults($array, $defaults) {
        foreach ($defaults as $key => $value) {
            if(!array_key_exists($key, $array)){
                $array[$key] = $value;
            }
            
        }
        return $array;
    }

    public static function getFormattedValues($array, $format, $replacement_in_format) {

        $values = array_values($array);
        foreach ($values as &$value) {
            $value = str_replace($replacement_in_format, $value, $format);
        }

        return $values;
    }

    public static function getFormattedKeys($array, $format, $replacement_in_format) {


        $keys = array_keys($array);
        foreach ($keys as &$key) {
            $key = str_replace($replacement_in_format, $key, $format);
        }

        return $keys;
    }

    public static function formatKeys($array, $format, $replacement_in_format) {

        $f = array();
        foreach ($array as $key => $value) {
            $f[str_replace($replacement_in_format, $key, $format)] = $value;
        }
        return $f;
    }

    public static function formatValues($array, $format, $replacement_in_format) {

        $f = array();
        foreach ($array as $key => $value) {
            $f[$key] = str_replace($replacement_in_format, $value, $format);
        }
        return $f;
    }

    public static function addPrefixToKeys($assoc, $prefix) {
        return self::formatKeys($array, "$prefix?", "?");
    }

    public static function str_replace_keys($string, $array) {

        if (strlen($string) && count($array)) {
//            $values = array_values($array);
//            $keys = array_keys($array);
            return str_replace(array_keys($array), array_values($array), $string);
        }
        return $string;
    }

    public static function sprintf_assoc($string, $assoc, $key_prefix = '%') {

        if ((strlen($string)) && is_array($assoc) && (count($assoc) > 0)) {
            return str_replace(self::getFormattedKeys($assoc, "$key_prefix{?}", "{?}"), array_values($assoc), $string);
        }
//        if ((strlen($string)) && is_array($assoc) && (count($assoc) > 0)) {
//            $f = self::formatKeys($assoc, "$key_prefix?", "?");
//            return self::str_replace_keys($string, $f);
////            foreach ($assoc as $key => $value) {
////                $string = str_replace($key_prefix . $key, $value, $string);
////            }
//        }
        return $string;
    }

    public static function toArray($source, $delimiterIfString = ",") {
        $out = array();
        if (isNullorEmpty($source)) {
            return $out;
        }

        if (is_string($source)) {
            return explode($delimiterIfString, $source);
        } elseif (is_numeric($source)) {
            return array($source);
        } elseif (is_array($source)) {
            return $source;
        } elseif (is_object($out)) {
            return array_values((array) $source);
        }

        return $out;
    }

    public static function getNotSetKeys($array, $keys = null) {
        $out = array();
        if (is_null($keys)) {
            $keys = array_keys($array);
        }
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                $out[] = $key;
            }
        }
        return $out;
    }

    public static function getEmptyKeys($array, $keys = null) {

        $out = array();
        if (is_null($keys)) {
            $keys = array_keys($array);
        }
        foreach ($keys as $key) {
            if (isNullorEmpty($array[$key])) {
                $out[] = $key;
            }
        }
        return $out;
    }

    public static function areKeysSet($array, $keys) {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
        }
        return true;
    }

    public static function keysExist($array, $keys) {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }

    public static function areKeysNull($array, $keys) {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array) || is_null($array[$key])) {
                return true;
            }
        }
        return false;
    }

    public static function isKeyNull($array, $key) {
        return self::areKeysNull($array, array($key));
    }

    public static function areKeysEmpty($array, $keys) {
        foreach ($keys as $key) {
            if (isNullorEmpty($array[$key])) {
                return true;
            }
        }
        return false;
    }

    public static function isKeyEmpty($array, $key) {
        return self::areKeysEmpty($array, array($key));
    }

    public static function areKeysNullOrEmpty($array, $keys) {
        foreach ($keys as $key) {
            if (isNullorEmpty($array[$key])) {
                return true;
            }
        }
        return false;
    }

    public static function isKeyNullOrEmpty($array, $key) {
        return self::areKeysNullOrEmpty($array, array($key));
    }

    public static function checkKeys($array, $existingKeys, $notNullOrEmptyKeys) {

        if (!isNullorEmpty($array)) {
            return false;
        }
        if (!isNullorEmpty($existingKeys)) {
            $o = self::keysExist($array, $existingKeys);
            if ($o) {
                return $o;
            }
        }
        if (!isNullorEmpty($notNullOrEmptyKeys)) {
            $o = self::areKeysNullOrEmpty($array, $notNullOrEmptyKeys);
            if ($o) {
                return $o;
            }
        }
        return true;
    }

}

?>