<?php 
require_once 'header.php';
require_permission('crm.read');
include 'config.php';

// Paginacja
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filtrowanie
$where_conditions = [];
$bind_vars = [];

if (!empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $where_conditions[] = "(UPPER(FIRST_NAME) LIKE UPPER(:search1) OR UPPER(LAST_NAME) LIKE UPPER(:search2) OR UPPER(EMAIL) LIKE UPPER(:search3))";
    $bind_vars[':search1'] = $search;
    $bind_vars[':search2'] = $search;
    $bind_vars[':search3'] = $search;
}

if (!empty($_GET['owner'])) {
    $where_conditions[] = "OWNER_ID = :owner";
    $bind_vars[':owner'] = $_GET['owner'];
}

// Sortowanie
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'CREATED_AT';
$order = isset($_GET['order']) && $_GET['order'] === 'ASC' ? 'ASC' : 'DESC';
$allowed_sort = ['LAST_NAME', 'FIRST_NAME', 'EMAIL', 'CREATED_AT'];
if (!in_array($sort, $allowed_sort)) {
    $sort = 'CREATED_AT';
}

$where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Liczba wszystkich rekordów
$count_sql = "SELECT COUNT(*) AS TOTAL FROM CONTACTS $where_sql";
$count_stmt = oci_parse($conn, $count_sql);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($count_stmt, $key, $bind_vars[$key]);
}
oci_execute($count_stmt);
$total_records = oci_fetch_assoc($count_stmt)['TOTAL'];
$total_pages = ceil($total_records / $per_page);

// Pobieranie kontaktów
$sql = "SELECT c.*, u.FIRST_NAME || ' ' || u.LAST_NAME AS OWNER_NAME, a.ACCOUNT_NAME
        FROM CONTACTS c
        LEFT JOIN USERS u ON c.OWNER_ID = u.ID_USER
        LEFT JOIN ACCOUNTS a ON c.ACCOUNT_ID = a.ID_ACCOUNT
        $where_sql
        ORDER BY $sort $order
        OFFSET :offset ROWS FETCH NEXT :per_page ROWS ONLY";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':offset', $offset);
oci_bind_by_name($stmt, ':per_page', $per_page);
foreach ($bind_vars as $key => $value) {
    oci_bind_by_name($stmt, $key, $bind_vars[$key]);
}
oci_execute($stmt);

// Pobierz użytkowników dla filtra
$users_sql = "SELECT ID_USER, FIRST_NAME || ' ' || LAST_NAME AS FULL_NAME FROM USERS ORDER BY FIRST_NAME";
$users_stmt = oci_parse($conn, $users_sql);
oci_execute($users_stmt);
?>

<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Kontakty</h2>
        <?php if (has_permission('crm.write')): ?>
        <a href="contact_add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj Kontakt
        </a>
        <?php endif; ?>
    </div>

    <!-- Filtry -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="contacts_list.php" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Szukaj..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="owner" class="form-control">
                        <option value="">Wszyscy właściciele</option>
                        <?php while ($user = oci_fetch_assoc($users_stmt)): ?>
                            <option value="<?php echo $user['ID_USER']; ?>" <?php echo (isset($_GET['owner']) && $_GET['owner'] == $user['ID_USER']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['FULL_NAME']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary mr-2 mb-2"><i class="fas fa-filter"></i> Filtruj</button>
                <a href="contacts_list.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-times"></i> Wyczyść</a>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'LAST_NAME', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])); ?>">
                                Nazwisko <i class="fas fa-sort"></i>
                            </a></th>
                            <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'FIRST_NAME', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])); ?>">
                                Imię <i class="fas fa-sort"></i>
                            </a></th>
                            <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'EMAIL', 'order' => $order === 'ASC' ? 'DESC' : 'ASC'])); ?>">
                                Email <i class="fas fa-sort"></i>
                            </a></th>
                            <th>Telefon</th>
                            <th>Stanowisko</th>
                            <th>Firma</th>
                            <th>Właściciel</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stmt)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['LAST_NAME']); ?></td>
                            <td><?php echo htmlspecialchars($row['FIRST_NAME']); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($row['EMAIL']); ?>"><?php echo htmlspecialchars($row['EMAIL']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['PHONE'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['TITLE'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['ACCOUNT_NAME'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['OWNER_NAME'] ?? '-'); ?></td>
                            <td>
                                <?php if (has_permission('crm.write')): ?>
                                <a href="contact_edit.php?id=<?php echo $row['ID_CONTACT']; ?>" class="btn btn-sm btn-warning" title="Edytuj">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (has_permission('crm.delete')): ?>
                                <a href="contact_delete.php?id=<?php echo $row['ID_CONTACT']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Czy na pewno chcesz usunąć ten kontakt?')" title="Usuń">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($total_records == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center">Brak kontaktów do wyświetlenia</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginacja -->
            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Poprzednia</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Następna</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <p class="text-center text-muted">Strona <?php echo $page; ?> z <?php echo $total_pages; ?> (<?php echo $total_records; ?> rekordów)</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
