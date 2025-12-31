<?php

require_once __DIR__ . '/../public/project.php';
require_once __DIR__ . '/../Models/sprint.php';

if (!isset($_SESSION['user'])){
    header('location: login.php');
    exit();
}

$message = '';

$projects = new Sprint();
$projects->getAll();

if ($_SERVER['REQUEST_METHOD'] = $_POST){
    $name = $_POST['sprint_name'];
    $project_id = $_POST['project_id'];
    $sprint = new Sprint();
    $result = $sprint->create($name, $project_id);

    if ($result){
        $message = "sprint created succesfully";
    }
    else{
        $message = "There was a problem creating the sprint";
    }
}
?>

<h2>Create new sprint</h2>

<form method="POST">
    <input type="text" name="sprint_name" placeholder="sprint name" required>
    <select name="project_id">
        <option value="">pick a project</option>
        <?php foreach ($projects as $project): ?>
            <option value="<?= $project['id'] ?>"><?= $project['project_name'] ?></option>
            <?php endforeach; ?>
    </select>
    <button type="submit">Create sprint</button>
</form>

<p><? $message ?></p>
<a href="Dashboard.php">Return to Control Panel</a>