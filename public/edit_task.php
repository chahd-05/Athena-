<?php
session_start();

require_once __DIR__ . '/../Models/Task.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$role    = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$taskModel = new Task();
$message = '';

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($task_id <= 0) {
    die("Task not found");
}

$task = $taskModel->getById($task_id);
if (!$task) {
    die("Task not found");
}

if ($role === 'member' && (($task['assigned_to'] ?? null) != $user_id)) {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';

    if ($title === '') {
        $message = "Title is required";
    } else {
        $updated = $taskModel->update($task_id, $title, $description, $priority, $user_id, $role);
        if ($updated) {
            header("Location: Dashboard.php");
            exit;
        }
        $message = "Error updating task";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 600px;">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
            <h2 style="margin:0;">Edit Task</h2>
            <a href="Dashboard.php" class="btn btn--secondary">Back</a>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($task['task_title']) ?>" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Priority</label>
                <select name="priority" required>
                    <option value="low" <?= ($task['priority'] ?? '')==='low'?'selected':'' ?>>Low</option>
                    <option value="medium" <?= ($task['priority'] ?? '')==='medium'?'selected':'' ?>>Medium</option>
                    <option value="high" <?= ($task['priority'] ?? '')==='high'?'selected':'' ?>>High</option>
                </select>
            </div>
            <div style="margin-top: 10px;">
                <button type="submit">Save Changes</button>
                <a href="Dashboard.php" class="btn btn--secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
    <div class="card" style="margin-top: 16px;">
        <h3 style="margin:0 0 8px;">Task Info</h3>
        <p style="margin:0;"><strong>Sprint:</strong> <?= htmlspecialchars($task['sprint_name'] ?? '-') ?></p>
        <p style="margin:0;"><strong>Assigned to:</strong> <?= htmlspecialchars($task['assigned_name'] ?? '-') ?></p>
    </div>
    </div>
</div>
</body>
</html>
