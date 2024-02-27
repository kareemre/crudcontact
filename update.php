<?php
session_start();

include 'includes/db.php';
include 'includes/functions.php';
include 'includes/security.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
$stmt->execute([$id]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    header('Location: index.php');
    exit();
}

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
    } elseif (!isEmailUnique($pdo, $email, $id)) {
        $errors[] = 'Email already exists.';
    }

    if (empty($phone)) {
        $errors[] = 'Phone is required.';
    } elseif (!validatePhone($phone)) {
        $errors[] = 'Invalid phone number format.';
    } elseif (!isPhoneUnique($pdo, $phone, $id)) {
        $errors[] = 'Phone number already exists.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone_number = ? WHERE id = ?');
        $stmt->execute([$name, $email, $phone, $id]);

        $_SESSION['success_message'] = 'Contact updated successfully.';
        header('Location: index.php');
        exit();
    }
} else {
    $name = $contact['name'];
    $email = $contact['email'];
    $phone = $contact['phone_number'];
    $errors = [];
}

$csrfToken = generateCSRFToken();
?>

<?php include 'templates/header.php'; ?>

<h2>Edit Contact</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>

<?php include 'templates/footer.php'; ?>