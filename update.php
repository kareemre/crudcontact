<?php
session_start();

require_once 'classes/Database/MySqlConnection.php';
require_once 'classes/Database/QueryBuilder.php';
require_once 'classes/Validation/Validation.php';
require_once 'classes/helpers.php';

$dbConnection = new MySqlConnection;
$db = new QueryBuilder($dbConnection);
$validator = new Validation($db);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}
$contact = $db->where('id = ?', $id)->get('contacts');
// $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
// $stmt->execute([$id]);
// $contact = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$contact) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCSRFToken($_POST['csrf_token']);
    $name =  sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);

    $validator->required('name');

    $validator->required('email')->email('email')->unique('email', ['contacts', 'email']);

    $validator->required('phone')->unique('phone', ['contacts', 'phone_number']);

    if ($validator->passes()) {
        $db->data([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone
        ])->where('id = ?', $id)->update('contacts');

        $_SESSION['success'] = 'Contact added successfully.';
        header('Location: index.php');
        exit();
    }
} 


$csrfToken = generateCSRFToken();
?>

<?php include 'components/header.php'; ?>

<h2>Edit Contact</h2>

<?php if ($validator->fails()) : $errors = $validator->getMessages();?>
    
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php include 'components/update_form.php'; ?>

<?php include 'components/footer.php'; ?>