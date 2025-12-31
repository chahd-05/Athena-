<?php

require_once __DIR__ . '/project.php';
if(!isset($_SESSION['user'])){
    header('location: login.php');
    exit();
}


$message = '';

if($_SERVER['REQUEST_METHOD'] = $_POST){
    $name = $_POST['project_name'];
    $desc = $_POST['description'];
    $project = new Project();
    $result = $project->create($name, $desc, $_SESSION['user']);

    if($result){
        $message = "Project created successfully";
    }
    else{
        $message = "A problem occurred in the project's creation";
    }
}
?>

<h2>Create a new project</h2>
<form method="post">
    <input type="text" name="project_name" placeholder="project name" required>
    <textarea name="description" placeholder="description"></textarea>
    <button type="submit">Create</button>
</form>
<p><? $messageK?></p>
<a href="Dashboard.php">Return to Control Panel</a>