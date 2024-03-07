<?php
require_once 'classes/Database/MySqlConnection.php';
require_once 'classes/Database/QueryBuilder.php';

$dbConnection = new MySqlConnection;
$db = new QueryBuilder($dbConnection);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $db->where('id = ?', $id)->delete('contacts');
    header('Location: index.php');
    exit;
} else {
    exit('Contact ID not specified.');
}
