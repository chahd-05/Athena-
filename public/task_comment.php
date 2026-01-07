<?php
session_start();

require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

if (!isset($_GET['task_id'])) {
    die("Task not found");
}

$task_id = $_GET['task_id'];
$user_id = $_SESSION['user_id'];

$taskModel = new Task();
$commentModel = new Comment();

$task = $taskModel->getById($task_id);
$comments = $commentModel->getByTask($task_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    if (!empty($content)) {
        $commentModel->create($task_id, $user_id, $content);
        if ($task['assigned_to'] && $task['assigned_to'] != $user_id) {
            $notifModel = new Notification();
            $notifMsg = "New comment on task '" . $task['task_title'] . "'";
            $notifModel->create($task['assigned_to'], $notifMsg);
        }

        header("Location: task_comment.php?task_id=" . $task_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Comments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="Dashboard.php" class="btn btn--secondary">⬅ Back to Dashboard</a>
    </div>

    <div class="card">
        <h2 style="color: var(--primary); margin-bottom: 10px;"><?= htmlspecialchars($task['task_title']) ?></h2>
        <p style="color: var(--muted); line-height: 1.6;"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
    </div>

    <h3 style="margin-bottom: 16px;">Comments</h3>

    <?php if (empty($comments)): ?>
        <p class="text-center" style="color: var(--muted); font-style: italic;">No comments yet. Be the first to comment!</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="card" style="padding: 16px; margin-bottom: 16px;">
                <p style="margin: 0 0 8px;">
                    <strong style="color: var(--primary);"><?= htmlspecialchars($comment['full_name']) ?></strong>
                </p>
                <p style="margin: 0; color: var(--text);"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="card" style="margin-top: 24px;">
        <h3>Add a Comment</h3>
        <form method="POST">
            <textarea name="content" required placeholder="Type your comment here..." rows="3"></textarea>
            <div class="text-center">
                <button type="submit" style="width: 100%;">Send Comment</button>
            </div>
        </form>
    </div>

</div>

</body>
</html>
