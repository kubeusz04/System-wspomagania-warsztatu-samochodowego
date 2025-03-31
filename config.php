<?php
$conn = oci_connect('SYSTEM', '2004', 'localhost/XEPDB1');
if (!$conn) {
    $error = oci_error();
    echo "Błąd połączenia: " . $error['message'];
} else {
    echo "";
}
?>
