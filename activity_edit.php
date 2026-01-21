<?php 
require_once 'header.php';
require_permission('crm.write');
include 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: activities_list.php'); exit; }

$sql = "SELECT * FROM ACTIVITIES WHERE ID_ACTIVITY = :id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':id', $id);
oci_execute($stmt);
$activity = oci_fetch_assoc($stmt);

if (!$activity) { header('Location: activities_list.php'); exit; }

if ($activity['DESCRIPTION'] instanceof OCILob) {
    $activity['DESCRIPTION'] = $activity['DESCRIPTION']->load();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_sql = "UPDATE ACTIVITIES SET 
                    SUBJECT = :subject,
                    TYPE_CODE = :type_code,
                    STATUS_CODE = :status_code,
                    PRIORITY_CODE = :priority_code,
                    DUE_DATE = TO_TIMESTAMP(:due_date, 'YYYY-MM-DD\"T\"HH24:MI'),
                    ASSIGNED_TO_ID = :assigned_to_id,
                    UPDATED_AT = SYSTIMESTAMP
                   WHERE ID_ACTIVITY = :id";
    
    $update_stmt = oci_parse($conn, $update_sql);
    oci_bind_by_name($update_stmt, ':subject', $_POST['subject']);
    oci_bind_by_name($update_stmt, ':type_code', $_POST['type_code']);
    oci_bind_by_name($update_stmt, ':status_code', $_POST['status_code']);
    oci_bind_by_name($update_stmt, ':priority_code', $_POST['priority_code']);
    oci_bind_by_name($update_stmt, ':due_date', $_POST['due_date']);
    oci_bind_by_name($update_stmt, ':assigned_to_id', $_POST['assigned_to_id']);
    oci_bind_by_name($update_stmt, ':id', $id);
    
    if (oci_execute($update_stmt)) {
        oci_commit($conn);
        header('Location: activities_list.php?message=Aktywność zaktualizowana');
        exit;
    } else {
        $e = oci_error($update_stmt);
        $error = 'Błąd: ' . $e['message'];
    }
}
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit"></i> Edytuj Aktywność</h2>
        <a href="activities_list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Powrót do listy
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Temat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="subject" value="<?php echo htmlspecialchars($activity['SUBJECT']); ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Typ</label>
                            <select class="form-control" name="type_code">
                                <?php 
                                $types = oci_parse($conn, "SELECT CODE, NAME FROM DICT_ACTIVITY_TYPE");
                                oci_execute($types);
                                while($t = oci_fetch_assoc($types)):
                                ?>
                                <option value="<?php echo $t['CODE']; ?>" <?php echo $activity['TYPE_CODE'] == $t['CODE'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['NAME']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status_code">
                                <?php 
                                $statuses = oci_parse($conn, "SELECT CODE, NAME FROM DICT_ACTIVITY_STATUS");
                                oci_execute($statuses);
                                while($s = oci_fetch_assoc($statuses)):
                                ?>
                                <option value="<?php echo $s['CODE']; ?>" <?php echo $activity['STATUS_CODE'] == $s['CODE'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['NAME']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Priorytet</label>
                            <select class="form-control" name="priority_code">
                                <?php 
                                $priorities = oci_parse($conn, "SELECT CODE, NAME FROM DICT_PRIORITY ORDER BY LEVEL");
                                oci_execute($priorities);
                                while($p = oci_fetch_assoc($priorities)):
                                ?>
                                <option value="<?php echo $p['CODE']; ?>" <?php echo $activity['PRIORITY_CODE'] == $p['CODE'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['NAME']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Termin</label>
                            <input type="datetime-local" class="form-control" name="due_date" 
                                   value="<?php echo $activity['DUE_DATE'] ? date('Y-m-d\TH:i', strtotime($activity['DUE_DATE'])) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Przypisz do</label>
                    <select class="form-control" name="assigned_to_id">
                        <?php 
                        $users = oci_parse($conn, "SELECT ID_USER, FIRST_NAME || ' ' || LAST_NAME AS NAME FROM USERS");
                        oci_execute($users);
                        while($u = oci_fetch_assoc($users)):
                        ?>
                        <option value="<?php echo $u['ID_USER']; ?>" <?php echo $activity['ASSIGNED_TO_ID'] == $u['ID_USER'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($u['NAME']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Zapisz</button>
                <a href="activities_list.php" class="btn btn-secondary"><i class="fas fa-times"></i> Anuluj</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
