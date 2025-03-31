<?php include 'header.php'; ?>
<div class="col-md-9">
    <h2>Dodaj Klienta</h2>
    <form action="klient_add.php" method="POST">
        <div class="mb-3">
            <label for="imie" class="form-label">Imię</label>
            <input type="text" class="form-control" id="imie" name="imie" required>
        </div>
        <div class="mb-3">
            <label for="nazwisko" class="form-label">Nazwisko</label>
            <input type="text" class="form-control" id="nazwisko" name="nazwisko" required>
        </div>
        <div class="mb-3">
            <label for="telefon" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="telefon" name="telefon" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Dodaj Klienta</button>
    </form>

    <h2 class="mt-4">Lista Klientów</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php
                include 'config.php';
                $sql = "SELECT ID_KLIENTA, IMIE, NAZWISKO, TELEFON, EMAIL FROM KLIENCI";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                while (($row = oci_fetch_assoc($stid)) != false) {
                    echo "<tr>
                            <td>{$row['ID_KLIENTA']}</td>
                            <td>{$row['IMIE']}</td>
                            <td>{$row['NAZWISKO']}</td>
                            <td>{$row['TELEFON']}</td>
                            <td>{$row['EMAIL']}</td>
                            <td>
                                <a href='klient_delete.php?id={$row['ID_KLIENTA']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Czy na pewno chcesz usunąć tego klienta?\")'>Usuń</a>
                            </td>
                          </tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];

    include 'config.php';

    $checkTable = "SELECT table_name FROM user_tables WHERE table_name = 'KLIENCI'";
    $tableStmt = oci_parse($conn, $checkTable);
    oci_execute($tableStmt);
    
    if (!oci_fetch_assoc($tableStmt)) {
        echo "<div class='alert alert-danger mt-3'>Błąd: Tabela 'KLIENCI' nie istnieje!</div>";
        exit();
    }

    $sql = "INSERT INTO KLIENCI (IMIE, NAZWISKO, TELEFON, EMAIL) VALUES (:imie, :nazwisko, :telefon, :email)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':imie', $imie);
    oci_bind_by_name($stmt, ':nazwisko', $nazwisko);
    oci_bind_by_name($stmt, ':telefon', $telefon);
    oci_bind_by_name($stmt, ':email', $email);

    if (oci_execute($stmt)) {
        echo "<script>alert('Klient został dodany!'); window.location.href='klient_add.php';</script>";
    } else {
        $error = oci_error($stmt);
        echo "<div class='alert alert-danger mt-3'>Błąd: " . $error['message'] . "</div>";
    }
}
?>

<?php include 'footer.php'; ?>
