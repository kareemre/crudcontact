<?php
require_once 'c:/xampp/htdocs/Contacts/classes/Database/MySqlConnection.php';
require_once 'c:/xampp/htdocs/Contacts/classes/Database/MySqlQueryBuilder.php';

$dbConnection = new MySqlConnection;
$db = new MySqlQueryBuilder($dbConnection);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $db->where('id = ?', $id)->delete('contacts');
    header('Location: index.php');
    exit;
} else {
    exit('Contact ID not specified.');
}
