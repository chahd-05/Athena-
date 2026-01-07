<?php
session_start();
require_once __DIR__ . '/../Models/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $logUser = $user->login($email, $password);

    if ($logUser) {
        $_SESSION['user_id'] = $logUser['id'];
        $_SESSION['user'] = $logUser['full_name'];
        $_SESSION['role'] = $logUser['role'];

        header('Location: Dashboard.php');
        exit();
    } else {
        $error = "Wrong email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-container">
    <div class="card">
        <h2 class="text-center mb-4">Login</h2>
        
        <?php if ($error): ?>
            <div class="message message--error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
        <p class="text-center mt-4">
            Don't have an account? <a href="register.php">Register</a>
        </p>
    </div>
</div>

</body>
</html>
