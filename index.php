<!DOCTYPE html>
<html>
<head>
    <title>Contact Management</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Contact Management System</h2>
    <a href="create.php" class="btn btn-primary">Add New Contact</a>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        include 'includes/db.php';

        $stmt = $pdo->prepare('SELECT * FROM contacts ORDER BY id DESC');
        $stmt->execute();
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($contacts as $contact) {
            echo '<tr>';
            echo '<td>' . $contact['name'] . '</td>';
            echo '<td>' . $contact['email'] . '</td>';
            echo '<td>' . $contact['phone_number'] . '</td>';
            echo '<td>
                        <a href="update.php?id=' . $contact['id'] . '" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete.php?id=' . $contact['id'] . '" class="btn btn-sm btn-danger">Delete</a>
                  </td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>