<!DOCTYPE html>
<html>
<head>
    <title>Add Contact</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Contact</h2>

    <?php
    include 'includes/db.php';
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
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM contacts WHERE email = ?');
            $stmt->execute([$email]);
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
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM contacts WHERE phone_number = ?');
            $stmt->execute([$phone]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $phoneError = 'Phone number already exists';
            }
        // If all fields are valid, insert the contact into the database
        if (empty($nameError) && empty($emailError) && empty($phoneError)) {
            $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone_number) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $phone]);

            header('Location: index.php');
            exit;
        }
      }
    }
    ?>

    <form method="POST">
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
        <button type="submit" class="btn btn-primary">Add Contact</button>
    </form>
</div>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>