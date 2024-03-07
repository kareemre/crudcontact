<?php
session_start();
require_once 'classes/Database/MySqlConnection.php';
require_once 'classes/Database/QueryBuilder.php';
require_once 'classes/Validation/Validation.php';

$dbConnection = new MySqlConnection;
$db = new QueryBuilder($dbConnection);
    // Fetch all contacts
    $contacts = $db->getAll('contacts');

?>

<?php include "components/header.php"; ?>

<h2>Contacts</h2>

<a href="create.php" class="btn btn-primary mb-3">Add Contact</a>

<?php include 'components/list.php'; ?>

<?php include "components/footer.php"; ?>