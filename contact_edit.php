<?php 
require_once 'header.php';
require_permission('crm.write');
include 'config.php';

$success = '';
$error = '';
$contact_id = $_GET['id'] ?? null;

if (!$contact_id) {
    header('Location: contacts_list.php');
    exit;
}

// Pobierz kontakt
$sql = "SELECT * FROM CONTACTS WHERE ID_CONTACT = :id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':id', $contact_id);
oci_execute($stmt);
$contact = oci_fetch_assoc($stmt);

if (!$contact) {
    header('Location: contacts_list.php');
    exit;
}

// Obsługa CLOB
if ($contact['DESCRIPTION'] instanceof OCILob) {
    $contact['DESCRIPTION'] = $contact['DESCRIPTION']->load();
}

// Pobierz accounts dla dropdown
$accounts_sql = "SELECT ID_ACCOUNT, ACCOUNT_NAME FROM ACCOUNTS ORDER BY ACCOUNT_NAME";
$accounts_stmt = oci_parse($conn, $accounts_sql);
oci_execute($accounts_stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? null;
    $mobile = $_POST['mobile'] ?? null;
    $title = $_POST['title'] ?? null;
    $department = $_POST['department'] ?? null;
    $address = $_POST['address'] ?? null;
    $account_id = !empty($_POST['account_id']) ? $_POST['account_id'] : null;
    $description = $_POST['description'] ?? '';

    $update_sql = "UPDATE CONTACTS SET 
                    ACCOUNT_ID = :account_id,
                    FIRST_NAME = :first_name,
                    LAST_NAME = :last_name,
                    EMAIL = :email,
                    PHONE = :phone,
                    MOBILE = :mobile,
                    TITLE = :title,
                    DEPARTMENT = :department,
                    ADDRESS = :address,
                    DESCRIPTION = EMPTY_CLOB(),
                    UPDATED_AT = SYSTIMESTAMP
                   WHERE ID_CONTACT = :id
                   RETURNING DESCRIPTION INTO :description";
    
    $update_stmt = oci_parse($conn, $update_sql);
    $clob = oci_new_descriptor($conn, OCI_D_LOB);
    
    oci_bind_by_name($update_stmt, ':account_id', $account_id);
    oci_bind_by_name($update_stmt, ':first_name', $first_name);
    oci_bind_by_name($update_stmt, ':last_name', $last_name);
    oci_bind_by_name($update_stmt, ':email', $email);
    oci_bind_by_name($update_stmt, ':phone', $phone);
    oci_bind_by_name($update_stmt, ':mobile', $mobile);
    oci_bind_by_name($update_stmt, ':title', $title);
    oci_bind_by_name($update_stmt, ':department', $department);
    oci_bind_by_name($update_stmt, ':address', $address);
    oci_bind_by_name($update_stmt, ':id', $contact_id);
    oci_bind_by_name($update_stmt, ':description', $clob, -1, OCI_B_CLOB);

    if (oci_execute($update_stmt, OCI_DEFAULT)) {
        $clob->save($description);
        oci_commit($conn);
        $success = 'Kontakt został zaktualizowany pomyślnie!';
        
        // Odśwież dane kontaktu
        oci_execute($stmt);
        $contact = oci_fetch_assoc($stmt);
        if ($contact['DESCRIPTION'] instanceof OCILob) {
            $contact['DESCRIPTION'] = $contact['DESCRIPTION']->load();
        }
        
        $clob->free();
    } else {
        $e = oci_error($update_stmt);
        $error = 'Błąd: ' . $e['message'];
        $clob->free();
    }
}
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-edit"></i> Edytuj Kontakt</h2>
        <a href="contacts_list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Powrót do listy
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">Imię <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($contact['FIRST_NAME']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Nazwisko <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($contact['LAST_NAME']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($contact['EMAIL']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_id">Firma</label>
                            <select class="form-control" id="account_id" name="account_id">
                                <option value="">-- Wybierz firmę (opcjonalnie) --</option>
                                <?php while ($account = oci_fetch_assoc($accounts_stmt)): ?>
                                    <option value="<?php echo $account['ID_ACCOUNT']; ?>"
                                            <?php echo ($contact['ACCOUNT_ID'] == $account['ID_ACCOUNT']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($account['ACCOUNT_NAME']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($contact['PHONE'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">Telefon komórkowy</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" 
                                   value="<?php echo htmlspecialchars($contact['MOBILE'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Stanowisko</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($contact['TITLE'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Dział</label>
                            <input type="text" class="form-control" id="department" name="department" 
                                   value="<?php echo htmlspecialchars($contact['DEPARTMENT'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Adres</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($contact['ADDRESS'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Opis / Notatki</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($contact['DESCRIPTION'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz Zmiany
                </button>
                <a href="contacts_list.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
