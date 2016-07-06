<?php

class PortMySQL {

    private $sqlizer;

    public function __construct($db_file) {
        require_once '/home/mike/framework/modules/sqlizer/src/sqlizer.php';
        $this->sqlizer = new Sqlizer('localhost','3306','test','password','test');
        }

    public function Import($input_file) {
        $data = json_decode(file_get_contents($input_file),true);
        foreach ($data['tables'] as $table) {
            $name = $table['name'];
            $this->sqlizer->RunSQL($table['schema']);
            if (isset($table['rows']) and !empty($table['rows'])) {
                foreach ($table['rows'] as $row) {
                    $statment = "replace into $name ";
                    $keys = "(";
                    $values = "(";
                    foreach ($row as $key=>$value) {
                        $keys .= $key . ',';
                        $values .= ":$key,";
                        $sql['values'][':' . $key] = $value;
                    }
                    $keys = rtrim($keys,',');
                    $values = rtrim($values,',');
                    $keys .= ")";
                    $values .= ")";
                    $sql['statement'] = "replace into $name $keys values $values";
                    $this->sqlizer->RunSQL($sql);
                    unset($sql);
                }
            }
        }
    }

    public function Export($output_file) {
        $sql['statement'] = "show tables";
        $tables = [];
        $tables = array_reverse($this->sqlizer->RunSQL($sql));
        $dbname = $this->sqlizer->database;
        foreach ($tables as $table) { 
            $name = $table['Tables_in_' . $dbname];
            $sql['statement'] = "select * from $name";
            $table['sql'] = $this->sqlizer->RunSQL("show create table " . $name)[0]['Create Table'];
            $a['name'] = $name;
            $a['schema'] = str_replace("CREATE TABLE","CREATE TABLE IF NOT EXISTS",$table['sql']);
            $a['rows'] = $this->sqlizer->RunSQL($sql);
            $export['tables'][] = $a;
        }
        file_put_contents($output_file,json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

}
