<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM POJAZDY WHERE ID_POJAZDU = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);

    if (oci_execute($stmt)) {
        echo "<script>alert('Pojazd został usunięty!'); window.location.href='pojazd_add.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas usuwania pojazdu!'); window.location.href='pojazd_add.php';</script>";
    }
}
?>
