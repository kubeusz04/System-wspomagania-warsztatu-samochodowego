<?php require_once 'header.php'; require_permission('crm.read'); include 'config.php';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20; $offset = ($page - 1) * $per_page;
$where = []; $bind = [];
if (!empty($_GET['type'])) { $where[] = "TYPE_CODE = :type"; $bind[':type'] = $_GET['type']; }
if (!empty($_GET['status'])) { $where[] = "STATUS_CODE = :status"; $bind[':status'] = $_GET['status']; }
$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT a.*, t.NAME AS TYPE_NAME, s.NAME AS STATUS_NAME, p.NAME AS PRIORITY_NAME, u.FIRST_NAME || ' ' || u.LAST_NAME AS OWNER_NAME
        FROM ACTIVITIES a
        LEFT JOIN DICT_ACTIVITY_TYPE t ON a.TYPE_CODE = t.CODE
        LEFT JOIN DICT_ACTIVITY_STATUS s ON a.STATUS_CODE = s.CODE
        LEFT JOIN DICT_PRIORITY p ON a.PRIORITY_CODE = p.CODE
        LEFT JOIN USERS u ON a.OWNER_ID = u.ID_USER
        $where_sql ORDER BY DUE_DATE ASC NULLS LAST, CREATED_AT DESC
        OFFSET :offset ROWS FETCH NEXT :per_page ROWS ONLY";
$stmt = oci_parse($conn, $sql); oci_bind_by_name($stmt, ':offset', $offset); oci_bind_by_name($stmt, ':per_page', $per_page);
foreach ($bind as $k => $v) { oci_bind_by_name($stmt, $k, $bind[$k]); }
oci_execute($stmt);
?>
<div class="col-md-12"><div class="d-flex justify-content-between align-items-center mb-4">
<h2><i class="fas fa-tasks"></i> Aktywności</h2>
<?php if (has_permission('crm.write')): ?><a href="activity_add.php" class="btn btn-info"><i class="fas fa-plus"></i> Dodaj Aktywność</a><?php endif; ?>
</div>
<div class="card mb-4"><div class="card-body"><form method="GET" class="form-inline">
<select name="type" class="form-control mr-2 mb-2"><option value="">Wszystkie typy</option><?php $types=oci_parse($conn,"SELECT CODE,NAME FROM DICT_ACTIVITY_TYPE"); oci_execute($types); while($t=oci_fetch_assoc($types)):?><option value="<?php echo $t['CODE'];?>" <?php echo (isset($_GET['type'])&&$_GET['type']==$t['CODE'])?'selected':'';?>><?php echo $t['NAME'];?></option><?php endwhile;?></select>
<select name="status" class="form-control mr-2 mb-2"><option value="">Wszystkie statusy</option><?php $statuses=oci_parse($conn,"SELECT CODE,NAME FROM DICT_ACTIVITY_STATUS"); oci_execute($statuses); while($s=oci_fetch_assoc($statuses)):?><option value="<?php echo $s['CODE'];?>" <?php echo (isset($_GET['status'])&&$_GET['status']==$s['CODE'])?'selected':'';?>><?php echo $s['NAME'];?></option><?php endwhile;?></select>
<button type="submit" class="btn btn-secondary mr-2 mb-2"><i class="fas fa-filter"></i> Filtruj</button>
<a href="activities_list.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-times"></i> Wyczyść</a>
</form></div></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Temat</th><th>Typ</th><th>Status</th><th>Priorytet</th><th>Termin</th><th>Właściciel</th><th>Akcje</th></tr></thead><tbody>
<?php while($row=oci_fetch_assoc($stmt)):?><tr><td><?php echo htmlspecialchars($row['SUBJECT']);?></td><td><span class="badge badge-secondary"><?php echo $row['TYPE_NAME'];?></span></td><td><span class="badge badge-info"><?php echo $row['STATUS_NAME'];?></span></td><td><?php echo $row['PRIORITY_NAME'];?></td><td><?php echo $row['DUE_DATE']?date('Y-m-d H:i',strtotime($row['DUE_DATE'])):'-';?></td><td><?php echo htmlspecialchars($row['OWNER_NAME']??'-');?></td><td>
<?php if(has_permission('crm.write')):?><a href="activity_edit.php?id=<?php echo $row['ID_ACTIVITY'];?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a><?php endif;?>
<?php if(has_permission('crm.delete')):?><a href="activity_delete.php?id=<?php echo $row['ID_ACTIVITY'];?>" class="btn btn-sm btn-danger" onclick="return confirm('Usuń?')"><i class="fas fa-trash"></i></a><?php endif;?>
</td></tr><?php endwhile;?></tbody></table></div></div></div>
</div>
<?php include 'footer.php'; ?>
