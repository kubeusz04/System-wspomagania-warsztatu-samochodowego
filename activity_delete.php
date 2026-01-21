<?php require_once 'auth.php'; require_permission('crm.delete'); include 'config.php';
$id = $_GET['id'] ?? null;
if ($id) { $sql = "DELETE FROM ACTIVITIES WHERE ID_ACTIVITY = :id"; $stmt = oci_parse($conn, $sql); oci_bind_by_name($stmt, ':id', $id); if (oci_execute($stmt)) { oci_commit($conn); } }
header('Location: activities_list.php'); exit;
?>
