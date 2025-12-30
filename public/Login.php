<?php 
session_start();
require_once __DIR__ . '/../Models/user.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] = $_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = new User();
    $logUser = $user->login($email, $password);

    if($logUser){
        $_SESSION['user'] = $logUser;
        header('location: Dashboard.php');
        exit;
    }
    else {
        $error = "wrong password or email!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="password" required>
    <button type="submit">Login</button>
</form>

<p style="color:red"><?= $error ?></p>