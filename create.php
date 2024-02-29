<?php
session_start();

require_once 'c:/xampp/htdocs/Contacts/core/Database/MySqlConnection.php';
require_once 'c:/xampp/htdocs/Contacts/core/Database/MySqlQueryBuilder.php';
require_once 'c:/xampp/htdocs/Contacts/core/Validation/Validation.php';
require_once 'c:/xampp/htdocs/Contacts/core/security.php';

$dbConnection = new MySqlConnection;
$db = new MySqlQueryBuilder($dbConnection);
$validator = new Validation($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);

    $validator->required('name');

    $validator->required('email')->email('email')->unique('email', ['contacts', 'email']);

    $validator->required('phone')->unique('phone', ['contacts', 'phone_number']);


    if ($validator->passes()) {
        $db->data([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
        ])->insert('contacts');

        $_SESSION['success'] = 'Contact added successfully.';
        header('Location: index.php');
        exit();
    }
} 
$csrfToken = generateCSRFToken();
?>

<?php include 'components/header.php'; ?>

<h2>Add Contact</h2>

<?php if ($validator->fails()) : $errors = $validator->getMessages();?>
    
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<form method="POST" action="">
    
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="">
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="">
    </div>

    <button type="submit" class="btn btn-primary">Add</button>
</form>

<?php include 'components/footer.php'; ?>