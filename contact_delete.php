<?php 
require_once 'auth.php';
require_permission('crm.delete');
include 'config.php';

$contact_id = $_GET['id'] ?? null;

if (!$contact_id) {
    header('Location: contacts_list.php');
    exit;
}

$sql = "DELETE FROM CONTACTS WHERE ID_CONTACT = :id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':id', $contact_id);

if (oci_execute($stmt)) {
    oci_commit($conn);
    header('Location: contacts_list.php?message=Kontakt został usunięty');
} else {
    $error = oci_error($stmt);
    header('Location: contacts_list.php?error=' . urlencode($error['message']));
}
exit;
?>
