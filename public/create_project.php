<?php
session_start();
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project = new Project();
    $result = $project->create(
        $_POST['project_name'],
        $_POST['description'],
        $_POST['start_date'],
        $_POST['end_date']
    );

    if ($result) {
        $message = "Project created successfully";
        
        $notifModel = new Notification();
        $notifMsg = "You created a new project: " . $_POST['project_name'];
        $notifModel->create($_SESSION['user_id'], $notifMsg);

    } else {
        $message = "Problem creating project";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2>Create Project</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Project Name</label>
                <input type="text" name="project_name" placeholder="Project name" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description" placeholder="Description" rows="4"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label>Start Date</label>
                    <input type="date" name="start_date">
                </div>
                <div>
                    <label>End Date</label>
                    <input type="date" name="end_date">
                </div>
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit">Create Project</button>
                <a href="Dashboard.php" class="btn btn--secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
