<?php
include 'config.php';

// Pobieranie statystyk z bazy danych
$klienci_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM KLIENCI");
oci_execute($klienci_query);
$klienci = oci_fetch_assoc($klienci_query)['LICZBA'];

$pojazdy_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM POJAZDY");
oci_execute($pojazdy_query);
$pojazdy = oci_fetch_assoc($pojazdy_query)['LICZBA'];

$zlecenia_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM ZLECENIA");
oci_execute($zlecenia_query);
$zlecenia = oci_fetch_assoc($zlecenia_query)['LICZBA'];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warsztat Samochodowy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            margin-top: 50px;
            text-align: center;
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="display-4">Warsztat Samochodowy</h1>
    <p class="lead">Zarządzaj klientami, pojazdami i zleceniami w jednym miejscu.</p>

    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Dodaj Klienta</h5>
                    <p class="card-text">Szybko dodaj nowego klienta do bazy.</p>
                    <a href="klient_add.php" class="btn btn-light">Dodaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Dodaj Pojazd</h5>
                    <p class="card-text">Zarejestruj pojazd przypisany do klienta.</p>
                    <a href="pojazd_add.php" class="btn btn-light">Dodaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Dodaj Zlecenie</h5>
                    <p class="card-text">Utwórz nowe zlecenie naprawy.</p>
                    <a href="zlecenie_add.php" class="btn btn-light">Dodaj</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-4">Statystyki:</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h5 class="card-title">Liczba klientów</h5>
                    <p class="display-4"><?php echo $klienci; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h5 class="card-title">Liczba pojazdów</h5>
                    <p class="display-4"><?php echo $pojazdy; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h5 class="card-title">Liczba zleceń</h5>
                    <p class="display-4"><?php echo $zlecenia; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>Autor projektu:</p> 
	<p>Zawadzki Kuba (nr albumu - 12500)</p>
</footer>

</body>
</html>
