<?php 
require_once 'header.php';
require_permission('crm.read');
include 'config.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$bind_vars = [];

if (!empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $where_conditions[] = "(UPPER(FIRST_NAME) LIKE UPPER(:search1) OR UPPER(LAST_NAME) LIKE UPPER(:search2) OR UPPER(COMPANY) LIKE UPPER(:search3))";
    $bind_vars[':search1'] = $search;
    $bind_vars[':search2'] = $search;
    $bind_vars[':search3'] = $search;
}

if (!empty($_GET['status'])) {
    $where_conditions[] = "STATUS_CODE = :status";
    $bind_vars[':status'] = $_GET['status'];
}

$where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$count_sql = "SELECT COUNT(*) AS TOTAL FROM LEADS $where_sql";
$count_stmt = oci_parse($conn, $count_sql);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($count_stmt, $key, $bind_vars[$key]);
}
oci_execute($count_stmt);
$total_records = oci_fetch_assoc($count_stmt)['TOTAL'];
$total_pages = ceil($total_records / $per_page);

$sql = "SELECT l.*, ls.NAME AS STATUS_NAME, lso.NAME AS SOURCE_NAME, u.FIRST_NAME || ' ' || u.LAST_NAME AS OWNER_NAME
        FROM LEADS l
        LEFT JOIN DICT_LEAD_STATUS ls ON l.STATUS_CODE = ls.CODE
        LEFT JOIN DICT_LEAD_SOURCE lso ON l.SOURCE_CODE = lso.CODE
        LEFT JOIN USERS u ON l.OWNER_ID = u.ID_USER
        $where_sql
        ORDER BY CREATED_AT DESC
        OFFSET :offset ROWS FETCH NEXT :per_page ROWS ONLY";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':offset', $offset);
oci_bind_by_name($stmt, ':per_page', $per_page);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($stmt, $key, $bind_vars[$key]);
}
oci_execute($stmt);

$statuses_sql = "SELECT CODE, NAME FROM DICT_LEAD_STATUS ORDER BY DISPLAY_ORDER";
$statuses_stmt = oci_parse($conn, $statuses_sql);
oci_execute($statuses_stmt);
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-plus"></i> Leady</h2>
        <?php if (has_permission('crm.write')): ?>
        <a href="lead_add.php" class="btn btn-success"><i class="fas fa-plus"></i> Dodaj Lead</a>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="form-inline">
                <input type="text" name="search" class="form-control mr-2 mb-2" placeholder="Szukaj..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <select name="status" class="form-control mr-2 mb-2">
                    <option value="">Wszystkie statusy</option>
                    <?php while ($status = oci_fetch_assoc($statuses_stmt)): ?>
                        <option value="<?php echo $status['CODE']; ?>" <?php echo (isset($_GET['status']) && $_GET['status'] == $status['CODE']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($status['NAME']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-secondary mr-2 mb-2"><i class="fas fa-filter"></i> Filtruj</button>
                <a href="leads_list.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-times"></i> Wyczyść</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nazwisko</th>
                            <th>Imię</th>
                            <th>Firma</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Źródło</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Właściciel</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stmt)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['LAST_NAME']); ?></td>
                            <td><?php echo htmlspecialchars($row['FIRST_NAME']); ?></td>
                            <td><?php echo htmlspecialchars($row['COMPANY'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['EMAIL'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['PHONE'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['SOURCE_NAME']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($row['STATUS_NAME']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['RATING'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['OWNER_NAME'] ?? '-'); ?></td>
                            <td>
                                <?php if (has_permission('crm.write')): ?>
                                <a href="lead_edit.php?id=<?php echo $row['ID_LEAD']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (has_permission('crm.delete')): ?>
                                <a href="lead_delete.php?id=<?php echo $row['ID_LEAD']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Czy na pewno chcesz usunąć ten lead?')"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
