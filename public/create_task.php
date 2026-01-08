<?php

require_once __DIR__ . '/../Models/project.php';
require_once __DIR__ . '/../Models/sprint.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Notification.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$message = '';

$projectModel = new Project();
$projects = $projectModel->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sprint_name = $_POST['sprint_name'];
    $project_id  = $_POST['project_id'];
    $start_date  = $_POST['start_date'] ?? null;
    $end_date    = $_POST['end_date'] ?? null;

    $sprint = new Sprint();
    $result = $sprint->create($sprint_name, $project_id, $start_date, $end_date);

    if ($result) {
        $message = "Sprint created successfully";

        $notifModel = new Notification();
        $notifMsg = "You created a new sprint: " . $sprint_name;
        $notifModel->create($_SESSION['user_id'], $notifMsg);

    } else {
        $message = "There was a problem creating the sprint";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Sprint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2>Create New Sprint</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Sprint Name</label>
                <input type="text" name="sprint_name" placeholder="Sprint name" required>
            </div>

            <div>
                <label>Project</label>
                <select name="project_id" required>
                    <option value="">Pick a project</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['project_name']) ?></option>
                    <?php endforeach; ?>
                </select>
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
                <button type="submit">Create Sprint</button>
                <a href="Dashboard.php" class="btn btn--secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
