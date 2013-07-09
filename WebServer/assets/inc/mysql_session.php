<?php
# mysql_session.php - MySQL-based session storage module
require_once "DB.php";
require_once 'db_config.php';

$dsn = $DB_dbType . "://"       // Build a DSN string (Data Source Name)
        . $DB_user . ":"        // Required by DB::connect()
        . $DB_pass . "@" 
        . $DB_host . "/" 
        . $DB_dbName;

$db = DB::connect($dsn, TRUE);  // Creates a database connection object in $db
                                // or, a database error object if it went wrong.
                                // The boolean specifies this is a persistent
                                // connection like mysql_pconnect(), it
                                // defaults to FALSE.

if (DB::isError($db)) {         // Check whether the object is a connection or an error.
    $error = ($db->getMessage());     // store error if it's an error object.
}

// Fuction to clean to clean variable for database
//   taken from http://ditio.net/2008/06/29/clean-input-variable-php/
function cleanData($data) {
	$data = trim($data);
	$data = htmlentities($data);
	$data = mysql_real_escape_string($data);
	return $data;
}

?>
