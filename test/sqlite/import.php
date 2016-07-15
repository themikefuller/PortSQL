<?php

require_once '/path/to/sqlizerlite/src/sqlizerlite.php';
$sqlizer = new SQLizerLite('database.db');

require_once '../../src/portsqlite.php';

$port = new PortSQLite($sqlizer);
$port->Import('backup.json');
