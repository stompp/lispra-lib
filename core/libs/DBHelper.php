<?php

include_once 'utilities.php';

class DBHelper {

    public $dbHost;
    public $dbName;
    public $dbUser;
    public $dbPass;
    // mysql connection
    private $c;
    private $connected;

    function __construct() {
        $this->c = NULL;
        $this->connected = FALSE;
    }

    function __destruct() {
        $this->close();
    }

    public function setDBCredentials($db_name, $user, $pass, $host = "localhost") {
        $this->dbName = $db_name;
        $this->dbUser = $user;
        $this->dbPass = $pass;
        $this->dbHost = $host;
    }

    public function close() {
        if ($this->connected) {
            try {
                if ($this->c != NULL) {
                    mysqli_close($this->c);
                }
            } catch (Exception $exc) {
                // echo $exc->getTraceAsString();
            }
            $this->c = NULL;
            $this->connected = FALSE;
        }
    }

    public function connect() {
        if ($this->connected == false) {
            // if($this->c == NULL){
            $this->c = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                $this->c = NULL;
                $this->connected = false;
            }
            $this->connected = true;
        }
        return $this->c;
    }

    public function getLink() {
        return new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
    }

    public function multiQuery($queryStr, $closeAfter = true) {
//        br("multiQuery");
        if (strlen($queryStr) == 0) {
            return NULL;
        }
        if (endsWith($queryStr, ";") == false) {
            $queryStr.=";";
        }
//        $link = $this->getLink();
        $link = $this->connect();

        $ok = $link->multi_query($queryStr);
        try {
            $this->close();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        $link = NULL;
        return $ok;
    }

    public function query($queryStr, $closeAfter = false) {

        if (is_array($queryStr)) {
            $out = array();
            foreach ($queryStr as $s) {
                array_push($out, $this->query($s, false));
            }
            if ($closeAfter) {
                $this->close();
            }
            return $out;
        }

        if (strlen($queryStr) == 0) {
            return NULL;
        }
        if (endsWith($queryStr, ";") == false) {
            $queryStr.=";";
        }
        if (substr_count($queryStr, ";") > 1) {
            return $this->multiQuery($queryStr);
        }

        $this->connect();
        if ($this->connected) {
            $r = mysqli_query($this->c, $queryStr);
            if ($closeAfter) {
                $this->close();
            }
            return $r;
        }
        return NULL;
    }

    public function queryResultRows($queryStr, $closeAfter = FALSE) {
        return mysqli_fetch_rows($this->query($queryStr, $closeAfter));
    }

    public function queryResultAssocs($queryStr, $closeAfter = true) {
        return mysqli_fetch_assocs($this->query($queryStr, $closeAfter));
    }

    public function select($tableName, $columns = "*", $where = "1") {
        return $this->queryResultAssocs(createSelectStatement($tableName, $columns, $where));
    }

    public function selectToTableRowsAssoc($tableName, $columns = "*", $where = "1") {
        $r = array();
        $r['table'] = $tableName;
        $r['SQLITECreateTableStatement'] = $this->getSQLITECreateTableStatement($tableName);
        $r['rows'] = $this->queryResultAssocs(createSelectStatement($tableName, $columns, $where));
        return $r;
    }

    public function selectRowWhere($tableName, $where) {
        $r = $this->select($tableName, "*", $where);
        $o = (is_array($r) && (count($r))) ? $r[0] : array();
        return $o;
    }

    public function selectRowWhereKeyEquals($tableName, $key, $value) {
//        br("sql_key_equals_value_string : " . sql_key_equals_value_string($key, $value));
        return $this->selectRowWhere($tableName, sql_key_equals_value_string($key, $value));
    }

    public function selectValue($tableName, $column, $where = "1") {
        $r = $this->select($tableName, $column, $where);

        if (is_array($r) && (count($r))) {
            $values = array_values($r[0]);
            return $values[0];
        }
        return "";
    }

    /*
     * Returns the number number of ocurrences of  $value
     */

    public function findInColumn($tableName, $column, $value) {
//        $s = "SELECT COUNT('$column') AS 'N' FROM `$tableName` WHERE `$column` LIKE '$value'";
        $s = "SELECT COUNT('$column') AS 'N' FROM `$tableName` WHERE `$column` = '$value'";
        $r = $this->queryResultAssocs($s);
        return (int) $r[0]['N'];
    }

//    SELECT COUNT('email') FROM `lispra_users` WHERE `email` LIKE 'admin@riggitt.org'

    public function rowCount($tableName) {
        $r = $this->queryResultAssocs("SELECT COUNT(*) FROM $tableName");
        return (int) $r[0]['COUNT(*)'];
    }

    public function delete($tableName, $where = "1") {
        return $this->query("DELETE FROM $tableName WHERE $where");
    }

    public function printQuery($queryStr, $glue = " ", $valuesBetween = "", $endline = "<br>") {
//         printDBTable($tableName,$this->dbName,$this->dbUser,$this->dbPass);
        // Check connection

        $rows = $this->queryResultAssocs($queryStr);
        foreach ($rows as $row) {
            $values = array_values($row);
            for ($n = 0; $n < count($values); $n++) {
                $values[$n] = $valuesBetween . $values[$n] . $valuesBetween;
            }
            echo implode($glue, $values) . $endline;
        }
    }

    public function printTable($tableName, $glue = " ", $endline = "<br>", $printHeader = true) {
//         printDBTable($tableName,$this->dbName,$this->dbUser,$this->dbPass);
        // Check connection
        $result = $this->query("SELECT * FROM " . $tableName, true);
        if ($result != false) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($printHeader) {
                    echo implode($glue, array_keys($row)) . $endline;
                    $printHeader = false;
                }
                $values = array_values($row);
                echo implode($glue, $values) . $endline;
                // br();
            }
        }
    }

    public function tableExists($tableName) {
        $result = $this->query(sprintf("SHOW TABLES LIKE '%s'", $tableName), true);
        return ($row = mysqli_fetch_array($result)) ? true : false;
    }

    public function truncateTable($tableName) {
        $result = $this->query("TRUNCATE TABLE " . $tableName, false);
        return ($result) ? true : false;
    }

    public function clearTable($tableName) {
//        clearDBTable($tableName,$this->dbName,$this->dbUser,$this->dbPass);
        $result = $this->query("DELETE FROM " . $tableName, false);
        return ($result) ? true : false;
    }

    public function dropTable($tableName) {
//        dropDBTable($tableName,$this->dbName,$this->dbUser,$this->dbPass);
        $result = $this->query("DROP TABLE " . $tableName, false);
        return ($result) ? true : false;
    }

    public function replaceInto($tableName, $columns, $values, $closeAfter = false) {
        $queryStr = createReplaceIntoStatement($tableName, $columns, $values);
        $this->connect();
        if ($this->connected) {
            mysqli_query($this->c, $queryStr);
            if ($closeAfter) {
                $this->close();
            }
        }
    }

    public function updateFromAssocArray($tableName, $array, $where, $closeAfter = false) {
//        $q = "UPDATE lispra_db.lispra_users SET `password` = 'ASASA', `display_name` = 'Riggitt AdDSADmin' WHERE user_id = 1;";
        $set = assocArrayToSQLSetString($array);
        $queryStr = "UPDATE $tableName SET $set WHERE $where";
//        br($queryStr);
        return $this->query($queryStr, $closeAfter);
    }

    public function insertArray($tableName, $valuesArray, $columnsArray = NULL, $closeAfter = false) {
        return $this->query(createInsertStatement($tableName, $valuesArray, $columnsArray), $closeAfter);
    }

    public function insertAssocArray($tableName, &$array, $closeAfter = false) {
        return $this->query(createInsertAssocStatement($tableName, $array), $closeAfter);
    }

    public function insertOnDuplicateKeyUpdateAssocArray($tableName, &$array, $keysToUpdate = null, $closeAfter = false) {
        return $this->query(createInsertOnDuplicateKeyUpdateAssocStatement($tableName, $array, $keysToUpdate), $closeAfter);
    }

    public function insertFromCSV($tableName, $content, $keaysAtFirstRow = true, $delimiter = ";", $endLine = "\r\n") {

        $rows = explode($endLine, $content);
        $n_rows = count($rows);
//        br("N Rows : $n_rows");
        $rowStart = 0;
        $keys = NULL;
        if ($keaysAtFirstRow) {
            $keys = explode($delimiter, $rows[0]);
            $rowStart = 1;
//            br("KEYS");
//            foreach ($keys as $value) {
//                print_r($value);
//                br();
//            }
        }
        $this->connect();
        for ($n = $rowStart; $n < $n_rows; $n++) {
            $values = arrayToSQLValues(explode($delimiter, $rows[$n]));
            $this->insertArray($tableName, $values, $keys);
//             br("VALUES ".$n);s
//             print_r($values);
//             br();
        }
        $this->close();
    }

    public function insertFromCSVFile($tableName, $fileName, $keaysAtFirstRow = true, $delimiter = ";", $endLine = "\r\n") {
        $this->insertFromCSV($tableName, file_get_contents($fileName), $keaysAtFirstRow, $delimiter, $endLine);
    }

    public function insertFromCSVTableFile($fileName, $keaysAtFirstRow = true, $delimiter = ";", $endLine = "\r\n") {
        $pathInfo = pathinfo($fileName);
        $tableName = $pathInfo['filename'];
        $this->insertFromCSV($tableName, file_get_contents($fileName), $keaysAtFirstRow, $delimiter, $endLine);
    }

    public function insertFromTableRowsAssoc($tableRowsAssoc, $closeAfter = true) {
        $tableName = $tableRowsAssoc["table"];
        foreach ($tableRowsAssoc["rows"] as $a) {
            $this->insertAssocArray($tableName, $a, false);
        }
        if ($closeAfter) {
            $this->close();
        }
    }

    public function insertfromJSONTableRowsObject($jsonString, $closeAfter = true) {
        $this->insertFromTableRowsAssoc(json_decode($jsonString, true), $closeAfter);
    }

    public function insertfromJSONTableRowsFile($fileName, $closeAfter = true) {
        $this->insertfromJSONTableRowsObject(file_get_contents($fileName), $closeAfter);
    }

    public function executeStatements($statements, $closeAfter = true) {
        if (is_array($statements)) {
            foreach ($statements as $s) {
                $this->query($s, false);
            }
            if ($closeAfter) {
                $this->close();
            }
        } else {
            if (is_string($statements)) {
                $this->query($statements);
            }
        }
//      
    }

    public function executeJSONStatementsArray($jsonString, $closeAfter = true) {
        $json = json_decode($jsonString, true);
        foreach ($json as $s) {
            $this->query($s, false);
        }
        if ($closeAfter) {
            $this->close();
        }
    }

    public function executeJSONStatementsArrayFile($fileName, $closeAfter = true) {
        $this->executeJSONStatementsArray(file_get_contents($fileName), $closeAfter);
    }

    public function executeJSONActions($data, $source = "text") {
        $json = json_decode($source == "file" ? file_get_contents($data) : $data, true);

        foreach ($json as $action) {
//            br("Executing action : ".$action['action']);
            switch ($action['action']) {
                case "echo":
                    echo $action['data'];
//                    br("Echo : ".$action['data']);
                    break;
                case "executeStatements":
                    if (isset($action['statements'])) {
                        $this->executeStatements($action['statements'], false);
                    }

                    break;
                case "executeJSONStatementsArrayFile":
                    if (strlen($action['file']))
                        $this->executeJSONStatementsArrayFile($action['file']);

                    if (is_array($action['files'])) {
                        foreach ($action['files'] as $file) {
                            $this->executeJSONStatementsArrayFile($file);
                        }
                    }

                    break;
                case "insertFromCSVTableFile":
                    if (strlen($action['file']))
                        $this->insertFromCSVTableFile($action['file']);
                    if (is_array($action['files'])) {
                        foreach ($action['files'] as $file) {
                            $this->insertFromCSVTableFile($file);
                        }
                    }

                    break;
                case "insertfromJSONTableRowsFile":
                    if (strlen($action['file']))
                        $this->insertfromJSONTableRowsFile($action['file']);
                    if (is_array($action['files'])) {
                        foreach ($action['files'] as $file) {
                            $this->insertfromJSONTableRowsFile($file);
                        }
                    }
                    break;
                case "csvTableFileToJSONTableRowsObjectFile":
                    if (strlen($action['file'])) {

                        csvTableFileToJSONTableRowsObjectFile($action['file']);
                    }
                    if (is_array($action['files'])) {

                        foreach ($action['files'] as $file) {
                            csvTableFileToJSONTableRowsObjectFile($file);
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

//        public function executeJSONActions($data, $source = "text") {
//        $json = json_decode($source == "file" ? file_get_contents($data) : $data, true);
//
//        foreach ($json as $action) {
//            br("Executing action : $action");
//            switch ($action['action']) {
//                case "echo":
////                    echo $action['data'];
//                    br("Echo : ".$action['data']);
//                    break;
//                case "executeStatements":
//                    if (isset($action['statements'])) {
//                        $this->executeStatements($action['statements'], false);
//                    }
//
//                    break;
//                case "executeJSONStatementsArrayFile":
//                    if (strlen($action['file']))
//                        $this->executeJSONStatementsArrayFile($action['file']);
//
//                    if (is_array($action['files'])) {
//                        foreach ($action['files'] as $file) {
//                            $this->executeJSONStatementsArrayFile($file);
//                        }
//                    }
//
//                    break;
//                case "insertFromCSVTableFile":
//                    if (strlen($action['file']))
//                        $this->insertFromCSVTableFile($action['file']);
//                    if (is_array($action['files'])) {
//                        foreach ($action['files'] as $file) {
//                            $this->insertFromCSVTableFile($file);
//                        }
//                    }
//
//                    break;
//                case "insertfromJSONTableRowsFile":
//                    if (strlen($action['file']))
//                        $this->insertfromJSONTableRowsFile($action['file']);
//                    if (is_array($action['files'])) {
//                        foreach ($action['files'] as $file) {
//                            $this->insertfromJSONTableRowsFile($file);
//                        }
//                    }
//                    break;
//                case "csvTableFileToJSONTableRowsObjectFile":
//                    if (strlen($action['file'])) {
//
//                        csvTableFileToJSONTableRowsObjectFile($action['file']);
//                    }
//                    if (is_array($action['files'])) {
//
//                        foreach ($action['files'] as $file) {
//                            csvTableFileToJSONTableRowsObjectFile($file);
//                        }
//                    }
//                    break;
//                default:
//                    break;
//            }
//        }
//    }
    public function tableToTableRowsAssoc($tableName) {
        $r = array();
        $r['table'] = $tableName;
        $r['SQLITECreateTableStatement'] = $this->getSQLITECreateTableStatement($tableName);
        $r['rows'] = $this->select($tableName);
        return $r;
    }

    public function tableToJSON($tableName, $format = true) {
//        $r = array();
//        $r['table'] = $tableName;
//        $r['SQLITECreateTableStatement'] = $this->getSQLITECreateTableStatement($tableName);
//        $r['rows'] = $this->select($tableName);
        $r = $this->tableToTableRowsAssoc($tableName);

        $json = json_encode($r);
        if ($format) {
            $json = format_json($json, $format);
        }
        return $json;
    }

    public function createTableRowsAssoc($tableName, $rows) {
        $r = array();
        $r['table'] = $tableName;
        $r['SQLITECreateTableStatement'] = $this->getSQLITECreateTableStatement($tableName);
        $r['rows'] = $rows;
        return $r;
    }

    public function createActionAssoc($action, $data) {
        $a = array();
        $a[ACTION] = $action;
        $a[DATA] = $data;
        return $a;
    }

    public function getSyncTableAction($tableName, $action = ACTION_SET_TABLE_DATA, $format = AS_ARRAY) {
        $action = $this->createActionAssoc($action, $this->tableToTableRowsAssoc($tableName));
        switch ($format) {
            case AS_JSON: return json_encode($action);
            default: return $action;
        }
    }

    public function getSyncTableActionAssoc($tableName, $action = ACTION_SET_TABLE_DATA) {
        return $this->createActionAssoc($action, $this->tableToTableRowsAssoc($tableName));
    }

    public function getSyncTableActionJSON($tableName, $format = true) {
        $json = json_encode($this->getSyncTableActionAssoc($tableName));
        return $format ? format_json($json) : $json;
    }

    public function tableToCSV($tableName, $delimiter = ";", $endLine = "\r\n", $dataBetween = '"') {
        $data = "";
        $r = $this->select($tableName);
        $nRows = count($r);
        if ($nRows) {
            $columns = array_keys($r[0]);
            foreach ($columns as $key => $value) {
                $columns[$key] = sprintf('"%s"', "" . $value);
            }
            $data .= implode($delimiter, $columns) . $endLine;
            $n = 0;
            for ($n; $n < $nRows; $n++) {
                foreach ($r[$n] as $key => $value) {
                    $r[$n][$key] = sprintf('"%s"', "" . $value);
                }
                $data .= implode($delimiter, $r[$n]) . (($n < $nRows - 1) ? $endLine : "");
            }
        }
        return $data;
    }

    public function tableTo($tableName, $format = "json") {
        switch ($format) {
            case "json":
                return $this->tableToJSON($tableName, true);
            case "csv":
                return $this->tableToCSV($tableName);

                break;

            default:
                break;
        }
    }

    public function tableToCSVFile($tableName) {
        file_put_contents(DIR_TABLES . $tableName . ".csv", $this->tableToCSV($tableName));
    }

    public function tableToJSONFile($tableName, $format = false) {
        file_put_contents(DIR_TABLES . $tableName . ".json", $this->tableToJSON($tableName, $format));
    }

//    public function executeJSONActionsFile($fileName,$closeAfter = true){
//        $this->executeJSONActions(file_get_contents($fileName),true);
//    }

    public function getCreateTableStatement($tableName) {

        $r = $this->queryResultAssocs("SHOW CREATE TABLE $tableName");
//        var_dump($r);
        if (is_null($r) == false) {
            if (isset($r[0]['Create Table'])) {
                return $r[0]['Create Table'];
            }
        }


        return "";
    }

    public function getTableNames() {
        $result = $this->queryResultRows("SHOW TABLES;");
        $names = array();
        foreach ($result as $r) {
//            $names[] = $r[0];
            array_push($names, $r[0]);
        }
        return $names;
    }

    public function getSQLITECreateTableStatement($tableName) {
        $s = "SHOW COLUMNS FROM $tableName";

        $result = $this->queryResultAssocs($s);

        if (is_null($result))
            return "";
        if (is_array($result)) {
            $lines = array();
            $primaries = array();
            foreach ($result as $r) {
//                $type = strstr($r['Type'],"(")
//                $line = sprintf("%s %s",$r['Field'],$r['Type']);
                $type = $r['Type'];

//                if(startsWith($type, "int")){ $type = strstr($r['Type'],"(",true);}
                if (strpos($type, "int") !== false) {
                    $type = "int";
                } else if (strpos($type, "varchar") !== false) {
                    $type = "text";
                }

                $line = sprintf("%s %s", $r['Field'], $type);
                if ($r['Extra'] == "auto_increment") {
                    $line .= " AUTO_INCREMENT";
                }
                if (strcmp($r['Key'], "PRI") == 0) {
                    array_push($primaries, $r['Field']);
                }
                array_push($lines, $line);
            }

            if (count($primaries)) {
                array_push($lines, sprintf("primary key(%s)", implode(",", $primaries)));
            }

            if (count($lines)) {
                return sprintf("create table if not exists $tableName (%s);", implode(",", $lines));
            }
        }

        return "";
    }

    public function getCreateTablesStatements($input = NULL) {

        if ($input == NULL) {
            $tables = $this->getTableNames();
            $out = array();
            foreach ($tables as $tableName) {
                array_push($out, $this->getSQLITECreateTableStatement($tableName));
            }

            return $out;
        } else if (is_string($input)) {
            return $this->getSQLITECreateTableStatement($input);
        }

        return NULL;
//        return $out;
    }

    public function getCreateTablesJSONArray($includeTableName = false) {
        $tables = $this->getTableNames();
        $out = array();
        foreach ($tables as $tableName) {
            $r = array();
            if ($includeTableName) {
                $r['table'] = $tableName;
            }
            $r['createTableStatement'] = $this->getShortCreateTableStatement($tableName);
            if (strlen($r['createTableStatement'])) {
                array_push($out, $r);
            }
//            $out.= sprintf("table %s\r\n%s\r\n",$tableName,$this->getShortCreateTableStatement($tableName));
        }

        return format_json(json_encode($out, true));
//        return $out;
    }

    public function getCreateTablesStatementsJSON() {
        $statements = $this->getCreateTablesStatements();
        return format_json(json_encode($statements, true));
    }

    public function getTableUpdateTime($tableName) {
        return $this->selectValue("information_schema.tables", "UPDATE_TIME", "TABLE_NAME = '$tableName'");
    }

    public function getTableNameUpdateTimeArray($tables = null, $lastSync = 0) {
        $names = "";
        if (is_null($tables)) {
            return $this->getTableNameUpdateTimeArray($this->getTableNames());
        } else if (is_array($tables)) {
            $names = join(",", arrayToSQLValues($tables));
        } else if (is_string($tables)) {
            $names = join(",", arrayToSQLValues(explode(",", $tables)));
        }
//        return $this->select("information_schema.tables", "TABLE_NAME,UPDATE_TIME", "TABLE_NAME in($names)");

        $ls = $lastSync;
        if (is_int($lastSync)) {
            $ls = sqlTimeStamp($lastSync);
        }
        return $this->select("information_schema.tables", "TABLE_NAME,UPDATE_TIME", "TABLE_NAME in($names) AND UPDATE_TIME>'$ls'");
    }

    public function getTableNameUpdateTimeArrayAfter($tables = null, $lastSync = 0) {
        $names = "";
        if (is_null($tables)) {
            return $this->getTableNameUpdateTimeArray($this->getTableNames());
        } else if (is_array($tables)) {
            $names = join(",", arrayToSQLValues($tables));
        } else if (is_string($tables)) {
            $names = join(",", arrayToSQLValues(explode(",", $tables)));
        }



        $ls = $lastSync;
        if (is_int($lastSync)) {
            $ls = sqlTimeStamp($lastSync);
        }
//        br("ls is $ls");
        return $this->select("information_schema.tables", "TABLE_NAME,UPDATE_TIME", "TABLE_NAME in($names) AND UPDATE_TIME >'$ls'");
    }

    /**
     * 
     * @param type $tables
     * @param type $lastSync
     * @return array. Array containing the name of all those tables updated after $lastSync
     */
    public function getTablesUpdatedAfter($tables = null, $lastSync = 0) {
        return matrix_column_to_array($this->getTableNameUpdateTimeArrayAfter($tables, $lastSync), TABLE_NAME);
    }

    public function getAllTablesUpdateTime() {
        return $this->getTableNameUpdateTimeArray($this->getTableNames());
    }

    public function getMySQLDataTypesForColumns($tableName, $columns) {
        $s = "SHOW COLUMNS FROM $tableName where Field='%s'";
        $types = array();
        for ($n = 0; $n < count($columns); $n++) {
            $r = $this->queryResultAssocs(sprintf($s, $columns[$n]));
            if ((isNullorEmpty($r[0]['Type']) == false) && ($r[0]['Type'] != 'null')) {
                $types[] = $r[0]['Type'];
            } else {
                $types[] = "varchar";
            }
        }
        return $types;
    }

    public function getLastInsertID() {
        $r = mysqli_fetch_assoc($this->query("SELECT LAST_INSERT_ID() AS last_id", false));
        return intval(getIfSet($r['last_id'], "-1"));
    }

}

?>