<?php require_once 'header.php'; require_permission('crm.write'); include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO OPPORTUNITIES (OPPORTUNITY_NAME, CONTACT_ID, STAGE_CODE, AMOUNT, PROBABILITY, EXPECTED_CLOSE_DATE, OWNER_ID)
            VALUES (:name, :contact_id, :stage_code, :amount, :probability, TO_DATE(:expected_date, 'YYYY-MM-DD'), :owner_id)";
    $stmt = oci_parse($conn, $sql);
    $owner_id = get_current_user_id();
    oci_bind_by_name($stmt, ':name', $_POST['opportunity_name']);
    oci_bind_by_name($stmt, ':contact_id', $_POST['contact_id']);
    oci_bind_by_name($stmt, ':stage_code', $_POST['stage_code']);
    oci_bind_by_name($stmt, ':amount', $_POST['amount']);
    oci_bind_by_name($stmt, ':probability', $_POST['probability']);
    oci_bind_by_name($stmt, ':expected_date', $_POST['expected_close_date']);
    oci_bind_by_name($stmt, ':owner_id', $owner_id);
    if (oci_execute($stmt)) {
        oci_commit($conn);
        header('Location: opportunities_list.php?success=1');
        exit;
    }
}
$contacts = oci_parse($conn, "SELECT ID_CONTACT, FIRST_NAME || ' ' || LAST_NAME AS NAME FROM CONTACTS ORDER BY LAST_NAME");
oci_execute($contacts);
$stages = oci_parse($conn, "SELECT CODE, NAME, PROBABILITY FROM DICT_OPPORTUNITY_STAGE ORDER BY DISPLAY_ORDER");
oci_execute($stages);
?>
<div class="col-md-12"><h2><i class="fas fa-handshake"></i> Dodaj Szansę</h2>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-group"><label>Nazwa Szansy <span class="text-danger">*</span></label><input type="text" class="form-control" name="opportunity_name" required></div>
<div class="form-group"><label>Kontakt</label><select class="form-control" name="contact_id"><option value="">--</option><?php while($c=oci_fetch_assoc($contacts)):?><option value="<?php echo $c['ID_CONTACT'];?>"><?php echo htmlspecialchars($c['NAME']);?></option><?php endwhile;?></select></div>
<div class="form-group"><label>Etap <span class="text-danger">*</span></label><select class="form-control" name="stage_code" required><?php while($s=oci_fetch_assoc($stages)):?><option value="<?php echo $s['CODE'];?>" data-probability="<?php echo $s['PROBABILITY'];?>"><?php echo htmlspecialchars($s['NAME']);?> (<?php echo $s['PROBABILITY'];?>%)</option><?php endwhile;?></select></div>
<div class="row"><div class="col-md-6"><div class="form-group"><label>Kwota (PLN)</label><input type="number" step="0.01" class="form-control" name="amount"></div></div>
<div class="col-md-6"><div class="form-group"><label>Prawdopodobieństwo (%)</label><input type="number" step="0.01" class="form-control" name="probability" value="50"></div></div></div>
<div class="form-group"><label>Przewidywana Data Zamknięcia</label><input type="date" class="form-control" name="expected_close_date"></div>
<button type="submit" class="btn btn-warning">Zapisz</button>
<a href="opportunities_list.php" class="btn btn-secondary">Anuluj</a>
</form></div></div></div>
<?php include 'footer.php'; ?>
