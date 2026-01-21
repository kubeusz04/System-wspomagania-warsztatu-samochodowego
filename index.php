<?php
require_once 'header.php';
include 'config.php';

// Pobieranie statystyk CRM
$stats = [];

// Statystyki CRM
if (has_permission('crm.read')) {
    $contacts_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM CONTACTS");
    oci_execute($contacts_query);
    $stats['contacts'] = oci_fetch_assoc($contacts_query)['LICZBA'];

    $leads_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM LEADS WHERE STATUS_CODE != 'CONVERTED'");
    oci_execute($leads_query);
    $stats['leads'] = oci_fetch_assoc($leads_query)['LICZBA'];

    $opportunities_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM OPPORTUNITIES WHERE STAGE_CODE NOT IN ('CLOSED_WON', 'CLOSED_LOST')");
    oci_execute($opportunities_query);
    $stats['opportunities'] = oci_fetch_assoc($opportunities_query)['LICZBA'];

    $activities_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM ACTIVITIES WHERE STATUS_CODE != 'COMPLETED'");
    oci_execute($activities_query);
    $stats['activities'] = oci_fetch_assoc($activities_query)['LICZBA'];
}

// Statystyki Serwisu
if (has_permission('service.read') || has_role('ADMIN')) {
    $klienci_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM KLIENCI");
    oci_execute($klienci_query);
    $stats['klienci'] = oci_fetch_assoc($klienci_query)['LICZBA'];

    $pojazdy_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM POJAZDY");
    oci_execute($pojazdy_query);
    $stats['pojazdy'] = oci_fetch_assoc($pojazdy_query)['LICZBA'];

    $zlecenia_query = oci_parse($conn, "SELECT COUNT(*) AS LICZBA FROM ZLECENIA");
    oci_execute($zlecenia_query);
    $stats['zlecenia'] = oci_fetch_assoc($zlecenia_query)['LICZBA'];
}
?>

<div class="container">
    <h1 class="display-4 text-center mb-4">
        <i class="fas fa-tachometer-alt"></i> Dashboard CRM
    </h1>
    <p class="lead text-center mb-5">
        Witaj, <strong><?php echo htmlspecialchars(get_current_user_full_name()); ?></strong>! 
        Zarządzaj swoim biznesem w jednym miejscu.
    </p>

    <?php if (has_permission('crm.read')): ?>
    <h2 class="mb-3"><i class="fas fa-chart-pie"></i> Statystyki CRM</h2>
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-2"></i>
                    <h5 class="card-title">Kontakty</h5>
                    <p class="display-4"><?php echo $stats['contacts'] ?? 0; ?></p>
                    <a href="contacts_list.php" class="btn btn-light btn-sm">Zobacz wszystkie</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-3x mb-2"></i>
                    <h5 class="card-title">Leady Aktywne</h5>
                    <p class="display-4"><?php echo $stats['leads'] ?? 0; ?></p>
                    <a href="leads_list.php" class="btn btn-light btn-sm">Zobacz wszystkie</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                    <h5 class="card-title">Szanse Otwarte</h5>
                    <p class="display-4"><?php echo $stats['opportunities'] ?? 0; ?></p>
                    <a href="opportunities_list.php" class="btn btn-light btn-sm">Zobacz wszystkie</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-3x mb-2"></i>
                    <h5 class="card-title">Aktywności Otwarte</h5>
                    <p class="display-4"><?php echo $stats['activities'] ?? 0; ?></p>
                    <a href="activities_list.php" class="btn btn-light btn-sm">Zobacz wszystkie</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card border-primary mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">Dodaj Kontakt</h5>
                    <p class="card-text">Dodaj nowy kontakt do bazy</p>
                    <a href="contact_add.php" class="btn btn-primary">Dodaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-funnel-dollar fa-2x text-success mb-2"></i>
                    <h5 class="card-title">Dodaj Lead</h5>
                    <p class="card-text">Zarejestruj nowego leada</p>
                    <a href="lead_add.php" class="btn btn-success">Dodaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">Dodaj Szansę</h5>
                    <p class="card-text">Nowa szansa sprzedaży</p>
                    <a href="opportunity_add.php" class="btn btn-warning">Dodaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-plus fa-2x text-info mb-2"></i>
                    <h5 class="card-title">Dodaj Aktywność</h5>
                    <p class="card-text">Zaplanuj zadanie lub spotkanie</p>
                    <a href="activity_add.php" class="btn btn-info">Dodaj</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (has_permission('service.read') || has_role('ADMIN')): ?>
    <h2 class="mb-3"><i class="fas fa-tools"></i> Statystyki Serwisu</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-2x mb-2 text-secondary"></i>
                    <h5 class="card-title">Klienci Serwisu</h5>
                    <p class="display-4"><?php echo $stats['klienci'] ?? 0; ?></p>
                    <a href="klient_add.php" class="btn btn-secondary btn-sm">Zarządzaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-car fa-2x mb-2 text-secondary"></i>
                    <h5 class="card-title">Pojazdy</h5>
                    <p class="display-4"><?php echo $stats['pojazdy'] ?? 0; ?></p>
                    <a href="pojazd_add.php" class="btn btn-secondary btn-sm">Zarządzaj</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-wrench fa-2x mb-2 text-secondary"></i>
                    <h5 class="card-title">Zlecenia</h5>
                    <p class="display-4"><?php echo $stats['zlecenia'] ?? 0; ?></p>
                    <a href="zlecenie_add.php" class="btn btn-secondary btn-sm">Zarządzaj</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
