<?php require_once 'header.php'; require_permission('crm.write'); include 'config.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: opportunities_list.php'); exit; }
$sql = "SELECT * FROM OPPORTUNITIES WHERE ID_OPPORTUNITY = :id";
$stmt = oci_parse($conn, $sql); oci_bind_by_name($stmt, ':id', $id); oci_execute($stmt);
$opp = oci_fetch_assoc($stmt); if (!$opp) { header('Location: opportunities_list.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update = "UPDATE OPPORTUNITIES SET OPPORTUNITY_NAME=:name, CONTACT_ID=:contact_id, STAGE_CODE=:stage_code, AMOUNT=:amount, PROBABILITY=:probability, EXPECTED_CLOSE_DATE=TO_DATE(:expected_date,'YYYY-MM-DD'), UPDATED_AT=SYSTIMESTAMP WHERE ID_OPPORTUNITY=:id";
    $upd_stmt = oci_parse($conn, $update);
    oci_bind_by_name($upd_stmt, ':name', $_POST['opportunity_name']);
    oci_bind_by_name($upd_stmt, ':contact_id', $_POST['contact_id']);
    oci_bind_by_name($upd_stmt, ':stage_code', $_POST['stage_code']);
    oci_bind_by_name($upd_stmt, ':amount', $_POST['amount']);
    oci_bind_by_name($upd_stmt, ':probability', $_POST['probability']);
    oci_bind_by_name($upd_stmt, ':expected_date', $_POST['expected_close_date']);
    oci_bind_by_name($upd_stmt, ':id', $id);
    if (oci_execute($upd_stmt)) { oci_commit($conn); echo '<script>alert("Zaktualizowano"); window.location.href="opportunities_list.php";</script>'; }
}
?>
<div class="col-md-12"><h2>Edytuj Szansę</h2><div class="card"><div class="card-body"><form method="POST">
<div class="form-group"><label>Nazwa</label><input type="text" class="form-control" name="opportunity_name" value="<?php echo htmlspecialchars($opp['OPPORTUNITY_NAME']);?>" required></div>
<div class="form-group"><label>Etap</label><select class="form-control" name="stage_code"><?php $stages=oci_parse($conn,"SELECT CODE,NAME FROM DICT_OPPORTUNITY_STAGE ORDER BY DISPLAY_ORDER"); oci_execute($stages); while($s=oci_fetch_assoc($stages)):?><option value="<?php echo $s['CODE'];?>" <?php echo $opp['STAGE_CODE']==$s['CODE']?'selected':'';?>><?php echo $s['NAME'];?></option><?php endwhile;?></select></div>
<div class="form-group"><label>Kontakt ID</label><input type="number" class="form-control" name="contact_id" value="<?php echo $opp['CONTACT_ID'];?>"></div>
<div class="form-group"><label>Kwota</label><input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $opp['AMOUNT'];?>"></div>
<div class="form-group"><label>Prawdopodobieństwo</label><input type="number" step="0.01" class="form-control" name="probability" value="<?php echo $opp['PROBABILITY'];?>"></div>
<div class="form-group"><label>Data Zamknięcia</label><input type="date" class="form-control" name="expected_close_date" value="<?php echo date('Y-m-d', strtotime($opp['EXPECTED_CLOSE_DATE']));?>"></div>
<button type="submit" class="btn btn-warning">Zapisz</button><a href="opportunities_list.php" class="btn btn-secondary">Anuluj</a>
</form></div></div></div>
<?php include 'footer.php'; ?>
