<?php include 'header.php'; ?>
<div class="col-md-9">
    <h2>Dodaj Zlecenie</h2>
    <form action="zlecenie_add.php" method="POST">
        <div class="mb-3">
            <label for="pojazd_id" class="form-label">Pojazd</label>
            <select class="form-select" id="pojazd_id" name="pojazd_id">
                <?php
                    include 'config.php';
                    $sql = "SELECT ID_POJAZDU, MARKA, MODEL FROM POJAZDY";
                    $stid = oci_parse($conn, $sql);
                    oci_execute($stid);
                    while (($row = oci_fetch_assoc($stid)) != false) {
                        echo "<option value='" . $row['ID_POJAZDU'] . "'>" . $row['MARKA'] . " " . $row['MODEL'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="opis" class="form-label">Opis Problemu</label>
            <textarea class="form-control" id="opis" name="opis" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Oczekuje">Oczekuje</option>
                <option value="W trakcie">W trakcie</option>
                <option value="Zakończone">Zakończone</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Dodaj Zlecenie</button>
    </form>

    <h2 class="mt-4">Lista Zleceń</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pojazd</th>
                <th>Opis Problemu</th>
                <th>Status</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Pobranie listy zleceń
                $sql = "SELECT Z.ID_ZLECENIA, P.MARKA, P.MODEL, Z.OPIS_PROBLEMU, Z.STATUS 
                        FROM ZLECENIA Z 
                        JOIN POJAZDY P ON Z.ID_POJAZDU = P.ID_POJAZDU";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                while (($row = oci_fetch_assoc($stid)) != false) {
                    // Obsługa CLOB
                    $opis_problemu = $row['OPIS_PROBLEMU'];
                    if ($opis_problemu instanceof OCILob) {
                        $opis_problemu = $opis_problemu->load();
                    }

                    echo "<tr>
                            <td>{$row['ID_ZLECENIA']}</td>
                            <td>{$row['MARKA']} {$row['MODEL']}</td>
                            <td>{$opis_problemu}</td>
                            <td>{$row['STATUS']}</td>
                            <td>
                                <a href='zlecenie_edit.php?id={$row['ID_ZLECENIA']}' class='btn btn-warning btn-sm'>Edytuj</a>
                                <a href='zlecenie_delete.php?id={$row['ID_ZLECENIA']}' class='btn btn-danger btn-sm'>Usuń</a>
                            </td>
                          </tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pojazd_id = $_POST['pojazd_id'];
    $opis = $_POST['opis'];
    $status = $_POST['status'];

    // Dodajemy zlecenie do bazy
    $sql = "INSERT INTO ZLECENIA (ID_POJAZDU, OPIS_PROBLEMU, STATUS) VALUES (:pojazd_id, EMPTY_CLOB(), :status) RETURNING OPIS_PROBLEMU INTO :opis";
    $stmt = oci_parse($conn, $sql);

    // Obsługa CLOB
    $clob = oci_new_descriptor($conn, OCI_D_LOB);
    oci_bind_by_name($stmt, ':pojazd_id', $pojazd_id);
    oci_bind_by_name($stmt, ':status', $status);
    oci_bind_by_name($stmt, ':opis', $clob, -1, OCI_B_CLOB);

    if (oci_execute($stmt, OCI_DEFAULT)) {
        $clob->save($opis);
        oci_commit($conn);
        echo "<script>alert('Zlecenie zostało dodane!'); window.location.href='zlecenie_add.php';</script>";
    } else {
        echo "<script>alert('Wystąpił błąd podczas dodawania zlecenia!'); window.location.href='zlecenie_add.php';</script>";
    }

    $clob->free();
}
?>
<?php include 'footer.php'; ?>
