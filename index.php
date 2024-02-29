<?php
session_start();
require_once 'c:/xampp/htdocs/Contacts/core/Database/MySqlConnection.php';
require_once 'c:/xampp/htdocs/Contacts/core/Database/MySqlQueryBuilder.php';
require_once 'c:/xampp/htdocs/Contacts/core/Validation/Validation.php';

$dbConnection = new MySqlConnection;
$db = new MySqlQueryBuilder($dbConnection);
$validator = new Validation($db);

// $search = '';
// $contacts = [];

// if (isset($_GET['search'])) {
//     $search = $_GET['search'];

//     // Fetch contacts matching the search query
//     $stmt = $pdo->prepare('SELECT * FROM contacts WHERE name LIKE :search');
//     $stmt->execute(['search' => '%' . $search . '%']);
//     $contacts = $stmt->fetchAll();
// } 
    // Fetch all contacts
    $contacts = $db->getAll('contacts');


?>

<?php include "components/header.php"; ?>

<h2>Contacts</h2>

<form class="mb-3" method="GET">
    <div class="form-inline">
        <input type="text" class="form-control mr-2" name="search" placeholder="Search by name" value="">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<!-- <?php if ($search !== '') : ?>
    <div class="alert alert-info">
        Searching for: <strong><?php echo htmlspecialchars($search); ?></strong>
    </div>
<?php endif; ?> -->

<a href="create.php" class="btn btn-primary mb-3">Add Contact</a>

<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($contacts as $contact) : ?>
        <tr>
            <td><?php echo $contact->name; ?></td>
            <td><?php echo $contact->email; ?></td>
            <td><?php echo $contact->phone_number; ?></td>
            <td>
                <a href="update.php?id=<?php echo $contact->id; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="delete.php?id=<?php echo $contact->id; ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this contact?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include "components/footer.php"; ?>