<?php 
require_once 'header.php';
require_permission('crm.write');
include 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $company = $_POST['company'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $source_code = $_POST['source_code'];
    $status_code = $_POST['status_code'];
    $rating = $_POST['rating'] ?? null;
    $owner_id = get_current_user_id();
    
    $sql = "INSERT INTO LEADS (FIRST_NAME, LAST_NAME, COMPANY, EMAIL, PHONE, SOURCE_CODE, STATUS_CODE, RATING, OWNER_ID)
            VALUES (:first_name, :last_name, :company, :email, :phone, :source_code, :status_code, :rating, :owner_id)";
    
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':first_name', $first_name);
    oci_bind_by_name($stmt, ':last_name', $last_name);
    oci_bind_by_name($stmt, ':company', $company);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':phone', $phone);
    oci_bind_by_name($stmt, ':source_code', $source_code);
    oci_bind_by_name($stmt, ':status_code', $status_code);
    oci_bind_by_name($stmt, ':rating', $rating);
    oci_bind_by_name($stmt, ':owner_id', $owner_id);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        $success = 'Lead został dodany pomyślnie!';
        $_POST = [];
    } else {
        $e = oci_error($stmt);
        $error = 'Błąd: ' . $e['message'];
    }
}

$sources_sql = "SELECT CODE, NAME FROM DICT_LEAD_SOURCE ORDER BY NAME";
$sources_stmt = oci_parse($conn, $sources_sql);
oci_execute($sources_stmt);

$statuses_sql = "SELECT CODE, NAME FROM DICT_LEAD_STATUS ORDER BY DISPLAY_ORDER";
$statuses_stmt = oci_parse($conn, $statuses_sql);
oci_execute($statuses_stmt);
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-funnel-dollar"></i> Dodaj Lead</h2>
        <a href="leads_list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Powrót do listy</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Imię <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nazwisko <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Firma</label>
                            <input type="text" class="form-control" name="company">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telefon</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Rating</label>
                            <select class="form-control" name="rating">
                                <option value="">-- Wybierz --</option>
                                <option value="Hot">Hot</option>
                                <option value="Warm">Warm</option>
                                <option value="Cold">Cold</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Źródło <span class="text-danger">*</span></label>
                            <select class="form-control" name="source_code" required>
                                <option value="">-- Wybierz --</option>
                                <?php while ($source = oci_fetch_assoc($sources_stmt)): ?>
                                    <option value="<?php echo $source['CODE']; ?>"><?php echo htmlspecialchars($source['NAME']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status_code" required>
                                <option value="">-- Wybierz --</option>
                                <?php while ($status = oci_fetch_assoc($statuses_stmt)): ?>
                                    <option value="<?php echo $status['CODE']; ?>"><?php echo htmlspecialchars($status['NAME']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Zapisz Lead</button>
                <a href="leads_list.php" class="btn btn-secondary"><i class="fas fa-times"></i> Anuluj</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
