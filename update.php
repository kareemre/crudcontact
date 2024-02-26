<!DOCTYPE html>
<html>
<head>
    <title>Edit Contact</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Contact</h2>

    <?php
    include 'includes/db.php';

    $id = $_GET['id'] ?? '';
    $name = $email = $phone = '';
    $nameError = $emailError = $phoneError = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);

        // Validate name
        if (empty($name)) {
            $nameError = 'Name is required';
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $nameError = 'Name can only contain letters and spaces';
        }

        // Validate email
        if (empty($email)) {
            $emailError = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = 'Invalid email format';
        } else {
            // Check if email is unique
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM contacts WHERE email = ? AND id != ?');
            $stmt->execute([$email, $id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $emailError = 'Email already exists';
            }
        }

        // Validate phone
        if (empty($phone)) {
            $phoneError = 'Phone is required';
        } elseif (!preg_match('/^\d{11}$/', $phone)) {
            $phoneError = 'Invalid phone number format (11 digits only)';
        } else {
            // Check if phone number is unique
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM contacts WHERE phone_number = ? AND id != ?');
            $stmt->execute([$phone, $id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $phoneError = 'Phone number already exists';
            }
        }

        // If all fields are valid, update the contact in the database
        if (empty($nameError) && empty($emailError) && empty($phoneError)) {
            // Generate a CSRF token and store it in the session
            session_start();
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;

            $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone_number = ? WHERE id = ?');
            $stmt->execute([$name, $email, $phone, $id]);

            header('Location: index.php');
            exit;
        }
    } else {
        // Fetch the contact details from the database
        $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([$id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contact) {
            header('Location: index.php');
            exit;
        }

        $name = $contact['name'];
        $email = $contact['email'];
        $phone = $contact['phone_number'];
    }
    ?>
    

    <form method="POST">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            <span class="error"><?php echo $nameError; ?></span>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            <span class="error"><?php echo $emailError; ?></span>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
            <span class="error"><?php echo $phoneError; ?></span>
        </div>
        <button type="submit" class="btn btn-primary">Update Contact</button>
    </form>
</div>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>