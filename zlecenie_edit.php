<?php include 'header.php'; ?>
<?php
include 'config.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Brak ID zlecenia!</div>";
    exit;
}

$id = $_GET['id'];
$sql = "SELECT Z.ID_ZLECENIA, P.MARKA, P.MODEL, Z.OPIS_PROBLEMU, Z.STATUS 
        FROM ZLECENIA Z 
        JOIN POJAZDY P ON Z.ID_POJAZDU = P.ID_POJAZDU 
        WHERE Z.ID_ZLECENIA = :id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':id', $id);
oci_execute($stid);
$row = oci_fetch_assoc($stid);

if (!$row) {
    echo "<div class='alert alert-danger'>Zlecenie nie istnieje!</div>";
    exit;
}

$opis_problemu = $row['OPIS_PROBLEMU'];
if ($opis_problemu instanceof OCILob) {
    $opis_problemu = $opis_problemu->load();
}

?>

<div class="col-md-9">
    <h2>Edytuj Zlecenie</h2>
    <form action="zlecenie_edit.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-3">
            <label class="form-label">Pojazd</label>
            <input type="text" class="form-control" value="<?php echo $row['MARKA'] . ' ' . $row['MODEL']; ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Opis Problemu</label>
            <textarea class="form-control" rows="4" disabled><?php echo $opis_problemu; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Oczekuje" <?php echo ($row['STATUS'] == 'Oczekuje') ? 'selected' : ''; ?>>Oczekuje</option>
                <option value="W trakcie" <?php echo ($row['STATUS'] == 'W trakcie') ? 'selected' : ''; ?>>W trakcie</option>
                <option value="Zakończone" <?php echo ($row['STATUS'] == 'Zakończone') ? 'selected' : ''; ?>>Zakończone</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Zapisz zmiany</button>
        <a href="zlecenie_add.php" class="btn btn-secondary">Anuluj</a>
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nowy_status = $_POST['status'];
    
    $sql = "UPDATE ZLECENIA SET STATUS = :status WHERE ID_ZLECENIA = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':status', $nowy_status);
    oci_bind_by_name($stmt, ':id', $id);

    if (oci_execute($stmt)) {
        echo "<script>alert('Zlecenie zaktualizowane!'); window.location.href='zlecenie_add.php';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Błąd podczas aktualizacji!</div>";
    }
}
?>

<?php include 'footer.php'; ?>
