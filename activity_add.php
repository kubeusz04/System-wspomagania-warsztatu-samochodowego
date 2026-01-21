<?php require_once 'header.php'; require_permission('crm.write'); include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO ACTIVITIES (SUBJECT, TYPE_CODE, STATUS_CODE, PRIORITY_CODE, DUE_DATE, OWNER_ID, ASSIGNED_TO_ID)
            VALUES (:subject, :type_code, :status_code, :priority_code, TO_TIMESTAMP(:due_date, 'YYYY-MM-DD\"T\"HH24:MI'), :owner_id, :assigned_to_id)";
    $stmt = oci_parse($conn, $sql);
    $owner_id = get_current_user_id();
    oci_bind_by_name($stmt, ':subject', $_POST['subject']);
    oci_bind_by_name($stmt, ':type_code', $_POST['type_code']);
    oci_bind_by_name($stmt, ':status_code', $_POST['status_code']);
    oci_bind_by_name($stmt, ':priority_code', $_POST['priority_code']);
    oci_bind_by_name($stmt, ':due_date', $_POST['due_date']);
    oci_bind_by_name($stmt, ':owner_id', $owner_id);
    oci_bind_by_name($stmt, ':assigned_to_id', $_POST['assigned_to_id']);
    if (oci_execute($stmt)) { oci_commit($conn); echo '<script>alert("Aktywność dodana"); window.location.href="activities_list.php";</script>'; }
}
?>
<div class="col-md-12"><h2>Dodaj Aktywność</h2><div class="card"><div class="card-body"><form method="POST">
<div class="form-group"><label>Temat <span class="text-danger">*</span></label><input type="text" class="form-control" name="subject" required></div>
<div class="row"><div class="col-md-6"><div class="form-group"><label>Typ</label><select class="form-control" name="type_code"><?php $types=oci_parse($conn,"SELECT CODE,NAME FROM DICT_ACTIVITY_TYPE"); oci_execute($types); while($t=oci_fetch_assoc($types)):?><option value="<?php echo $t['CODE'];?>"><?php echo $t['NAME'];?></option><?php endwhile;?></select></div></div>
<div class="col-md-6"><div class="form-group"><label>Status</label><select class="form-control" name="status_code"><?php $statuses=oci_parse($conn,"SELECT CODE,NAME FROM DICT_ACTIVITY_STATUS"); oci_execute($statuses); while($s=oci_fetch_assoc($statuses)):?><option value="<?php echo $s['CODE'];?>"><?php echo $s['NAME'];?></option><?php endwhile;?></select></div></div></div>
<div class="row"><div class="col-md-6"><div class="form-group"><label>Priorytet</label><select class="form-control" name="priority_code"><?php $priorities=oci_parse($conn,"SELECT CODE,NAME FROM DICT_PRIORITY ORDER BY LEVEL"); oci_execute($priorities); while($p=oci_fetch_assoc($priorities)):?><option value="<?php echo $p['CODE'];?>"><?php echo $p['NAME'];?></option><?php endwhile;?></select></div></div>
<div class="col-md-6"><div class="form-group"><label>Termin</label><input type="datetime-local" class="form-control" name="due_date"></div></div></div>
<div class="form-group"><label>Przypisz do</label><select class="form-control" name="assigned_to_id"><?php $users=oci_parse($conn,"SELECT ID_USER, FIRST_NAME||' '||LAST_NAME AS NAME FROM USERS"); oci_execute($users); while($u=oci_fetch_assoc($users)):?><option value="<?php echo $u['ID_USER'];?>"><?php echo $u['NAME'];?></option><?php endwhile;?></select></div>
<button type="submit" class="btn btn-info">Zapisz</button><a href="activities_list.php" class="btn btn-secondary">Anuluj</a>
</form></div></div></div>
<?php include 'footer.php'; ?>
