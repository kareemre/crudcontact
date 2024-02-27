<?php
session_start();

include 'includes/db.php';
include 'includes/functions.php';
include 'includes/security.php';



$searchName = $_GET['name'] ?? '';

$query = 'SELECT * FROM contacts WHERE 1';

if ($searchName) {
    $query .= ' AND name LIKE :name';
    $params['name'] = '%' . $searchName . '%';
}


$stmt = $pdo->query('SELECT * FROM contacts');
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.php'; ?>

<h2>Contacts</h2>

<form method="get" class="mb-3">
    <div class="form-row">
        <div class="col-md-4">
            <input type="text" class="form-control" name="name" placeholder="Search by Name" value="<?php echo $searchName; ?>">
        </div>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

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
            <td><?php echo $contact['name']; ?></td>
            <td><?php echo $contact['email']; ?></td>
            <td><?php echo $contact['phone_number']; ?></td>
            <td>
                <a href="update.php?id=<?php echo $contact['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="delete.php?id=<?php echo $contact['id']; ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this contact?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include 'templates/footer.php'; ?>