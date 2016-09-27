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
    } else if (is_array($d)) {
        if (count($d) < 1) {
            return true;
        }
    } else if (is_string($d)) {
        if (strlen($d) < 1) {
            return true;
        }
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

function getRequestParameterIfSet($param,$default = null) {
    return getIfSet($_REQUEST[$param],$default);
    
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

//function sqlValue($value, $type) {
//  $value = get_magic_quotes_gpc() ? stripslashes($value) : $value;
//  //$value = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($value) : mysqli_escape_string($value);
//  switch ($type) {
//    case "text":
//      $value = ($value != "") ? "'" . $value . "'" : "NULL";
//      break;
//    case "int":
//      $value = ($value != "") ? intval($value) : "NULL";
//      break;
//    case "double":
//      $value = ($value != "") ? "'" . doubleval($value) . "'" : "NULL";
//      break;
//    case "date":
//      $value = ($value != "") ? "'" . $value . "'" : "NULL";
//      break;
//  }
//  return $value;
//}
//function arrayElementsToSqlValues($values, $types){
//	//foreach($values as $s) $s = sqlValue($s,$type);
//	for($n = 0 ; $n < count($values) ; $n++){
//		$values[$n] = sqlValue($values[$n],$type[$n]);
//	}
//	return $values;
//}
//function arrayElementsToSqlValue($values, $type){
//	//foreach($values as $s) $s = sqlValue($s,$type);
//	for($n = 0 ; $n < count($values) ; $n++){
//		$values[$n] = sqlValue($values[$n],$type);
//	}
//	return $values;
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

//function executeInsert($con,$tableName,$valuesArray,$columnsArray = NULL){
//	$s = createInsertStatement($tableName,$valuesArray,$columnsArray);
//	mysqli_query($con,$s);
//}

/*
  function checkIfDBTableExists($tableName,$dbname,$username,$password, $host = "localhost"){

  $con = mysqli_connect($host,$username,$password,$dbname);
  $tableExists  = false;
  // Check connection
  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
  $result = mysqli_query($con,"SHOW TABLES LIKE '".$tableName."'");
  if($row = mysqli_fetch_array($result))$tableExists = true;
  }
  return $tableExists;
  }
  function printDBTable($tableName,$dbname,$username,$password, $host = "localhost"){

  $con = mysqli_connect($host,$username,$password,$dbname);
  // Check connection
  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
  $result = mysqli_query($con,"SELECT * FROM ".$tableName);
  if($result != false){
  while($row = mysqli_fetch_array($result)) {
  //echo "Row Count : ". count($row);br();
  for($n = 0 ; $n < count($row) ; $n++){
  if(isset($row[$n])) {echo $row[$n]." ";}
  }
  //$str =  implode ( " " , $row);
  //var_dump($row);
  br();
  }
  }
  mysqli_close($con);
  }

  }

  function truncateDBTable($tableName,$dbname,$username,$password, $host = "localhost"){

  $con = mysqli_connect($host,$username,$password,$dbname);
  $ok = false;
  // Check connection
  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
  $result = mysqli_query($con,"TRUNCATE TABLE ".$tableName);
  if($result) $ok = true;
  mysqli_close($con);
  }

  return $ok;

  }

  function clearDBTable($tableName,$dbname,$username,$password, $host = "localhost"){

  $con = mysqli_connect($host,$username,$password,$dbname);
  $ok = false;
  // Check connection
  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
  $result = mysqli_query($con,"DELETE FROM ".$tableName);
  if($result) $ok = true;
  mysqli_close($con);
  }

  return $ok;

  }

  function dropDBTable($tableName,$dbname,$username,$password, $host = "localhost"){

  $con = mysqli_connect($host,$username,$password,$dbname);
  $ok = false;
  // Check connection
  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
  $result = mysqli_query($con,"DROP TABLE ".$tableName);
  if($result) $ok = true;
  mysqli_close($con);
  }

  return $ok;

  }
 */
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

//function copyFromAssocToObject(&$o,$data){
//        $object_keys = array_keys(get_object_vars($o));
//        foreach ($object_keys as $key) {
//            if(array_key_exists($key, $data)){
//                $o->{$key} = $data[$key];
//            }
//            
//        }
//}

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
    $a = ((isNullorEmpty($default_attrs)) 
            ?(((isNullorEmpty($attrs)) ? array(): $attrs))
            :(((isNullorEmpty($attrs))? $default_attrs : array_merge($default_attrs, $attrs)))
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


?>