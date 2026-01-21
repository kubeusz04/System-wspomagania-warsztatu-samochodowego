<?php 
require_once 'header.php';
require_permission('crm.write');
include 'config.php';

$lead_id = $_GET['id'] ?? null;
if (!$lead_id) { header('Location: leads_list.php'); exit; }

$sql = "SELECT * FROM LEADS WHERE ID_LEAD = :id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':id', $lead_id);
oci_execute($stmt);
$lead = oci_fetch_assoc($stmt);
if (!$lead) { header('Location: leads_list.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_sql = "UPDATE LEADS SET FIRST_NAME = :first_name, LAST_NAME = :last_name, COMPANY = :company, 
                   EMAIL = :email, PHONE = :phone, SOURCE_CODE = :source_code, STATUS_CODE = :status_code, 
                   RATING = :rating, UPDATED_AT = SYSTIMESTAMP WHERE ID_LEAD = :id";
    $update_stmt = oci_parse($conn, $update_sql);
    oci_bind_by_name($update_stmt, ':first_name', $_POST['first_name']);
    oci_bind_by_name($update_stmt, ':last_name', $_POST['last_name']);
    oci_bind_by_name($update_stmt, ':company', $_POST['company']);
    oci_bind_by_name($update_stmt, ':email', $_POST['email']);
    oci_bind_by_name($update_stmt, ':phone', $_POST['phone']);
    oci_bind_by_name($update_stmt, ':source_code', $_POST['source_code']);
    oci_bind_by_name($update_stmt, ':status_code', $_POST['status_code']);
    oci_bind_by_name($update_stmt, ':rating', $_POST['rating']);
    oci_bind_by_name($update_stmt, ':id', $lead_id);
    if (oci_execute($update_stmt)) {
        oci_commit($conn);
        header('Location: leads_list.php?message=Zaktualizowano');
        exit;
    }
}
?>

<div class="col-md-12">
    <h2><i class="fas fa-edit"></i> Edytuj Lead</h2>
    <div class="card"><div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Imię</label><input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($lead['FIRST_NAME']); ?>" required></div></div>
                <div class="col-md-6"><div class="form-group"><label>Nazwisko</label><input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($lead['LAST_NAME']); ?>" required></div></div>
            </div>
            <div class="form-group"><label>Firma</label><input type="text" class="form-control" name="company" value="<?php echo htmlspecialchars($lead['COMPANY'] ?? ''); ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($lead['EMAIL'] ?? ''); ?>"></div>
            <div class="form-group"><label>Telefon</label><input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($lead['PHONE'] ?? ''); ?>"></div>
            <div class="form-group"><label>Źródło</label><select class="form-control" name="source_code"><?php $sources = oci_parse($conn, "SELECT CODE, NAME FROM DICT_LEAD_SOURCE"); oci_execute($sources); while ($s = oci_fetch_assoc($sources)): ?><option value="<?php echo $s['CODE']; ?>" <?php echo $lead['SOURCE_CODE'] == $s['CODE'] ? 'selected' : ''; ?>><?php echo $s['NAME']; ?></option><?php endwhile; ?></select></div>
            <div class="form-group"><label>Status</label><select class="form-control" name="status_code"><?php $statuses = oci_parse($conn, "SELECT CODE, NAME FROM DICT_LEAD_STATUS"); oci_execute($statuses); while ($st = oci_fetch_assoc($statuses)): ?><option value="<?php echo $st['CODE']; ?>" <?php echo $lead['STATUS_CODE'] == $st['CODE'] ? 'selected' : ''; ?>><?php echo $st['NAME']; ?></option><?php endwhile; ?></select></div>
            <div class="form-group"><label>Rating</label><select class="form-control" name="rating"><option value="">--</option><option <?php echo $lead['RATING']=='Hot'?'selected':''; ?>>Hot</option><option <?php echo $lead['RATING']=='Warm'?'selected':''; ?>>Warm</option><option <?php echo $lead['RATING']=='Cold'?'selected':''; ?>>Cold</option></select></div>
            <button type="submit" class="btn btn-success">Zapisz</button>
            <a href="leads_list.php" class="btn btn-secondary">Anuluj</a>
        </form>
    </div></div>
</div>

<?php include 'footer.php'; ?>
