<?php require_once 'header.php'; require_permission('crm.write'); include 'config.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: activities_list.php'); exit; }
echo '<div class="col-md-12"><h2>Edytuj Aktywność</h2><p><a href="activities_list.php" class="btn btn-secondary">Powrót</a></p></div>';
include 'footer.php';
?>
