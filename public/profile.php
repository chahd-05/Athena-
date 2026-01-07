<?php
session_start();
require_once __DIR__ . '/../Models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$userModel = new User();
$user = $userModel->getById($_SESSION['user_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $password  = !empty($_POST['password']) ? $_POST['password'] : null;
    $updated = $userModel->updateProfile($_SESSION['user_id'], $full_name, $password);
    if ($updated) {
        $message = "Profile updated successfully";
        $user = $userModel->getById($_SESSION['user_id']);
    } else {
        $message = "Problem updating profile";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div style="margin-bottom: 20px;">
        <a href="Dashboard.php" class="btn btn--secondary">Back to Dashboard</a>
    </div>
    <div class="card">
        <h2>My Profile</h2>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>
            <div>
                <label>New Password (optional)</label>
                <input type="password" name="password" placeholder="New password">
            </div>
            <div style="margin-top: 10px;">
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

