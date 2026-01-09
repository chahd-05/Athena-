<?php
session_start();

require_once __DIR__ . '/../Models/sprint.php';
require_once __DIR__ . '/../Models/task.php';
require_once __DIR__ . '/../Models/user.php';
require_once __DIR__ . '/../Models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$message = '';

$sprintModel = new Sprint();
$sprints = $sprintModel->getAll();

$users = [];
if (in_array($role, ['scrum', 'admin'])) {
    $userModel = new User();
    $users = $userModel->getAll();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = $_POST['title'];
    $description = $_POST['description'];
    $sprint_id   = $_POST['sprint_id'];
    $priority    = $_POST['priority'] ?? 'medium';

    $task = new Task();

    $assigned_to = ($role === 'member') ? $user_id : ($_POST['assigned_to'] ?? $user_id);

    $result = $task->create($title, $description, $sprint_id, $assigned_to, $priority);

    if ($result) {
        $message = "Task created successfully";
        if ($assigned_to) {
            $notifModel = new Notification();
            $notifMsg = "You have been assigned a new task: '$title'";
            $notifModel->create($assigned_to, $notifMsg);
        }

    } else {
        $message = "Problem creating task";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2>Create Task</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Title</label>
                <input type="text" name="title" placeholder="Task title" required>
            </div>
            
            <div>
                <label>Description</label>
                <textarea name="description" placeholder="Description" rows="4"></textarea>
            </div>

            <div>
                <label>Sprint</label>
                <select name="sprint_id" required>
                    <option value="">Pick a sprint</option>
                    <?php foreach ($sprints as $sprint): ?>
                        <option value="<?= $sprint['id'] ?>"><?= htmlspecialchars($sprint['sprint_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (in_array($role, ['scrum', 'admin'])): ?>
                <div>
                    <label>Assign to User</label>
                    <select name="assigned_to" required>
                        <option value="">Pick a user</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label>Priority</label>
                <select name="priority" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div style="margin-top: 10px;">
                <button type="submit">Create Task</button>
                <a href="Dashboard.php" class="btn btn--secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
