<?php
require_once __DIR__ . '/../Models/User.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();

    $result = $user->register($full_name, $email, $password);

    if ($result){
        $message = "Account created successfully";
    }
    else{
        $message = "Problem with registration";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-container">
    <div class="card">
        <h2 class="text-center mb-4">Register</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>
            <button type="submit">Register</button>
        </form>

        <p class="text-center mt-4">
            Already have an account? <a href="Login.php">Login</a>
        </p>
    </div>
</div>

</body>
</html>
