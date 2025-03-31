<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM KLIENCI WHERE ID_KLIENTA = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);

    if (oci_execute($stmt)) {
        echo "<script>alert('Klient został usunięty!'); window.location.href='klient_add.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas usuwania klienta!'); window.location.href='klient_add.php';</script>";
    }
}
?>
