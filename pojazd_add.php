<?php include 'header.php'; ?>
<div class="col-md-9">
    <h2>Dodaj Pojazd</h2>
    <form action="pojazd_add.php" method="POST">
        <div class="mb-3">
            <label for="klient_id" class="form-label">Klient</label>
            <select class="form-select" id="klient_id" name="klient_id">
                <!-- Załaduj klientów z bazy -->
                <?php
                    include 'config.php';
                    $sql = "SELECT ID_KLIENTA, IMIE, NAZWISKO FROM KLIENCI";
                    $stid = oci_parse($conn, $sql);
                    oci_execute($stid);
                    while (($row = oci_fetch_assoc($stid)) != false) {
                        echo "<option value='" . $row['ID_KLIENTA'] . "'>" . $row['IMIE'] . " " . $row['NAZWISKO'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="marka" class="form-label">Marka</label>
            <input type="text" class="form-control" id="marka" name="marka" required>
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="form-control" id="model" name="model" required>
        </div>
        <div class="mb-3">
            <label for="rok_produkcji" class="form-label">Rok Produkcji</label>
            <input type="number" class="form-control" id="rok_produkcji" name="rok_produkcji" required>
        </div>
        <div class="mb-3">
            <label for="numer_rejestracyjny" class="form-label">Numer Rejestracyjny</label>
            <input type="text" class="form-control" id="numer_rejestracyjny" name="numer_rejestracyjny" required>
        </div>
        <button type="submit" class="btn btn-primary">Dodaj Pojazd</button>
    </form>

    <h2 class="mt-4">Lista Pojazdów</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Klient</th>
                <th>Marka</th>
                <th>Model</th>
                <th>Rok</th>
                <th>Numer Rejestracyjny</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sql = "SELECT P.ID_POJAZDU, K.IMIE, K.NAZWISKO, P.MARKA, P.MODEL, P.ROK_PRODUKCJI, P.NUMER_REJESTRACYJNY 
                        FROM POJAZDY P 
                        JOIN KLIENCI K ON P.ID_KLIENTA = K.ID_KLIENTA";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                while (($row = oci_fetch_assoc($stid)) != false) {
                    echo "<tr>
                            <td>{$row['ID_POJAZDU']}</td>
                            <td>{$row['IMIE']} {$row['NAZWISKO']}</td>
                            <td>{$row['MARKA']}</td>
                            <td>{$row['MODEL']}</td>
                            <td>{$row['ROK_PRODUKCJI']}</td>
                            <td>{$row['NUMER_REJESTRACYJNY']}</td>
                            <td>
                                <a href='pojazd_delete.php?id={$row['ID_POJAZDU']}' class='btn btn-danger btn-sm'>Usuń</a>
                            </td>
                          </tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $klient_id = $_POST['klient_id'];
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $rok_produkcji = $_POST['rok_produkcji'];
    $numer_rejestracyjny = $_POST['numer_rejestracyjny'];

    include 'config.php'; 
    $sql = "INSERT INTO POJAZDY (ID_KLIENTA, MARKA, MODEL, ROK_PRODUKCJI, NUMER_REJESTRACYJNY) 
            VALUES (:klient_id, :marka, :model, :rok_produkcji, :numer_rejestracyjny)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':klient_id', $klient_id);
    oci_bind_by_name($stmt, ':marka', $marka);
    oci_bind_by_name($stmt, ':model', $model);
    oci_bind_by_name($stmt, ':rok_produkcji', $rok_produkcji);
    oci_bind_by_name($stmt, ':numer_rejestracyjny', $numer_rejestracyjny);

    if (oci_execute($stmt)) {
        echo "<script>alert('Pojazd został dodany!'); window.location.href='pojazd_add.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas dodawania pojazdu!'); window.location.href='pojazd_add.php';</script>";
    }
}
?>
<?php include 'footer.php'; ?>
