<?php
session_start();

include 'includes/db.php';
include 'includes/functions.php';
include 'includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken($_POST['csrf_token']);

    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Invalid email format.';
    } elseif (!isEmailUnique($pdo, $email)) {
        $errors[] = 'Email already exists.';
    }

    if (empty($phone)) {
        $errors[] = 'Phone is required.';
    } elseif (!validatePhone($phone)) {
        $errors[] = 'Invalid phone format.';
    } elseif (!isPhoneUnique($pdo, $phone)) {
        $errors[] = 'Phone number already exists.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone_number) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $phone]);

        $_SESSION['success'] = 'Contact added successfully.';
        header('Location: index.php');
        exit();
    }
} else {
    $name = '';
    $email = '';
    $phone = '';
}

$csrfToken = generateCSRFToken();
?>

<?php include 'templates/header.php'; ?>

<h2>Add Contact</h2>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>">
    </div>

    <button type="submit" class="btn btn-primary">Add</button>
</form>

<?php include 'templates/footer.php'; ?>