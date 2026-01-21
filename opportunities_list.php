<?php 
require_once 'header.php';
require_permission('crm.read');
include 'config.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$bind_vars = [];

if (!empty($_GET['stage'])) {
    $where_conditions[] = "STAGE_CODE = :stage";
    $bind_vars[':stage'] = $_GET['stage'];
}

$where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$count_sql = "SELECT COUNT(*) AS TOTAL FROM OPPORTUNITIES $where_sql";
$count_stmt = oci_parse($conn, $count_sql);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($count_stmt, $key, $bind_vars[$key]);
}
oci_execute($count_stmt);
$total_records = oci_fetch_assoc($count_stmt)['TOTAL'];
$total_pages = ceil($total_records / $per_page);

$sql = "SELECT o.*, s.NAME AS STAGE_NAME, s.PROBABILITY AS STAGE_PROBABILITY, 
               u.FIRST_NAME || ' ' || u.LAST_NAME AS OWNER_NAME,
               c.FIRST_NAME || ' ' || c.LAST_NAME AS CONTACT_NAME
        FROM OPPORTUNITIES o
        LEFT JOIN DICT_OPPORTUNITY_STAGE s ON o.STAGE_CODE = s.CODE
        LEFT JOIN USERS u ON o.OWNER_ID = u.ID_USER
        LEFT JOIN CONTACTS c ON o.CONTACT_ID = c.ID_CONTACT
        $where_sql
        ORDER BY EXPECTED_CLOSE_DATE ASC, CREATED_AT DESC
        OFFSET :offset ROWS FETCH NEXT :per_page ROWS ONLY";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':offset', $offset);
oci_bind_by_name($stmt, ':per_page', $per_page);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($stmt, $key, $bind_vars[$key]);
}
oci_execute($stmt);

$stages_sql = "SELECT CODE, NAME FROM DICT_OPPORTUNITY_STAGE ORDER BY DISPLAY_ORDER";
$stages_stmt = oci_parse($conn, $stages_sql);
oci_execute($stages_stmt);
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line"></i> Szanse Sprzedaży</h2>
        <?php if (has_permission('crm.write')): ?>
        <a href="opportunity_add.php" class="btn btn-warning"><i class="fas fa-plus"></i> Dodaj Szansę</a>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="form-inline">
                <select name="stage" class="form-control mr-2 mb-2">
                    <option value="">Wszystkie etapy</option>
                    <?php while ($stage = oci_fetch_assoc($stages_stmt)): ?>
                        <option value="<?php echo $stage['CODE']; ?>" <?php echo (isset($_GET['stage']) && $_GET['stage'] == $stage['CODE']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($stage['NAME']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-secondary mr-2 mb-2"><i class="fas fa-filter"></i> Filtruj</button>
                <a href="opportunities_list.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-times"></i> Wyczyść</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nazwa Szansy</th>
                            <th>Kontakt</th>
                            <th>Etap</th>
                            <th>Kwota</th>
                            <th>Prawdopodobieństwo</th>
                            <th>Termin Zamknięcia</th>
                            <th>Właściciel</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stmt)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['OPPORTUNITY_NAME']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['CONTACT_NAME'] ?? '-'); ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($row['STAGE_NAME']); ?></span></td>
                            <td><?php echo $row['AMOUNT'] ? number_format($row['AMOUNT'], 2) . ' PLN' : '-'; ?></td>
                            <td><?php echo $row['PROBABILITY'] ?? $row['STAGE_PROBABILITY']; ?>%</td>
                            <td><?php echo $row['EXPECTED_CLOSE_DATE'] ? date('Y-m-d', strtotime($row['EXPECTED_CLOSE_DATE'])) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row['OWNER_NAME'] ?? '-'); ?></td>
                            <td>
                                <?php if (has_permission('crm.write')): ?>
                                <a href="opportunity_edit.php?id=<?php echo $row['ID_OPPORTUNITY']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (has_permission('crm.delete')): ?>
                                <a href="opportunity_delete.php?id=<?php echo $row['ID_OPPORTUNITY']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Czy na pewno chcesz usunąć tę szansę?')"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
