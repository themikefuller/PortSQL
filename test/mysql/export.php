<?php

require_once '../../../sqlizer/src/sqlizer.php';
$sqlizer = new SQLizer('localhost','3306','test','password','test');

require_once '../../src/portmysql.php';

$port = new PortMySQL($sqlizer);
$port->Export('backup.json');
