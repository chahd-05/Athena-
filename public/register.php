<?php
require_once __DIR__ . '/../Models/User.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] = $_POST){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();

    $result = $user->register($full_name, $email, $password);

    if ($result){
        $message = "account created successfully";
    }
    else{
        $message = "problem with registration";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="full_name" placeholder="full name" required>
        <input type="email" name="email" placeholder="email" required>
        <input type="password" name="password" placeholder="password" required>
        <button type="submit">register</button>
    </form>
    <p><?= $message ?></p>
</body>
</html>