<?php
$user = "sys";
$pwid = "asad2045";
$dbname = "localhost:1522/xe";
$session_mode = OCI_SYSDBA;

//$conn = oci_connect($user, $pwid, $dbname, $session_mode );
$conn = oci_connect($user, $pwid , $dbname, '',$session_mode );

if (!$conn) {
   $m = oci_error();
   echo $m['message'], "\n";
   exit;
}
?>