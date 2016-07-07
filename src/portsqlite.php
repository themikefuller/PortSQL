<?php

class PortSQLite {

    private $sqlizer;

    public function __construct($sqlizerlite) {
        $this->sqlizer = $sqlizerlite;
        }

    public function Import($input_file) {
        $data = json_decode(file_get_contents($input_file),true);
        if (!empty($data['tables'])) {
            foreach ($data['tables'] as $table) {
                $name = $table['name'];
                $this->sqlizer->RunSQL($table['schema']);
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
        $sql['statement'] = "select sql,name from sqlite_master where type='table' and name != 'sqlite_sequence'";
        $tables = [];
        $tables = $this->sqlizer->RunSQL($sql);
        $export = null;
        if (!empty($tables)) {
            foreach ($tables as $table) { 
                $name = $table['name'];
                $sql['statement'] = "select * from $name";
                $a['name'] = $name;
                $a['schema'] = str_replace("CREATE TABLE","CREATE TABLE IF NOT EXISTS",$table['sql']);
                $a['rows'] = $this->sqlizer->RunSQL($sql);
                $export['tables'][] = $a;
            }
        }
        file_put_contents($output_file,json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

}
