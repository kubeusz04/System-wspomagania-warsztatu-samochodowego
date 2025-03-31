<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM ZLECENIA WHERE ID_ZLECENIA = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);

    if (oci_execute($stmt)) {
        echo "<script>alert('Zlecenie zostało usunięte!'); window.location.href='zlecenie_add.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas usuwania zlecenia!'); window.location.href='zlecenie_add.php';</script>";
    }
}
?>
