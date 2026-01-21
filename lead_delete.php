<?php 
require_once 'auth.php';
require_permission('crm.delete');
include 'config.php';
$lead_id = $_GET['id'] ?? null;
if ($lead_id) {
    $sql = "DELETE FROM LEADS WHERE ID_LEAD = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $lead_id);
    if (oci_execute($stmt)) { oci_commit($conn); }
}
header('Location: leads_list.php');
exit;
?>
