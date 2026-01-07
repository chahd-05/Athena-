<?php
session_start();

require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$taskModel = new Task();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['status'])) {
    $task_id = $_POST['task_id'];
    $status  = $_POST['status'];

    if ($role === 'member') {
        $userTasks = $taskModel->getByUser($user_id);
        $task_ids = array_column($userTasks, 'id');
        if (!in_array($task_id, $task_ids)) {
            die("You cannot update this task!");
        }
    }

    $taskModel->updateStatus($task_id, $status);
    $notifModel = new Notification();
    $taskInfo = $taskModel->getById($task_id);
    $taskTitle = $taskInfo ? $taskInfo['task_title'] : 'Unknown Task';

    $notifMsg = "You updated task status: '$taskTitle' to '$status'";
    $notifModel->create($_SESSION['user_id'], $notifMsg);

    $message = "Status updated successfully!";
    header("Location: Dashboard.php");
    exit();
}

$tasks = ($role === 'member') ? $taskModel->getByUser($user_id) : $taskModel->getAll();

function statusLabel($status) {
    return ucfirst(str_replace('_', ' ', $status));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2>Update Task Status</h2>
        <a href="Dashboard.php" class="btn btn--secondary">Back to Dashboard</a>
    </div>

    <?php if(!empty($message)) echo "<div class='message'>{$message}</div>"; ?>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Sprint</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['task_title']) ?></td>
                <td><?= htmlspecialchars($task['sprint_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($task['assigned_name'] ?? '-') ?></td>
                <td>
                    <span class="status status--<?= $task['status'] ?>">
                        <?= statusLabel($task['status']) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" style="display:flex; gap:8px; align-items:center;">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <select name="status" style="padding: 6px; width:auto;">
                            <option value="to_do" <?= $task['status']=='to_do'?'selected':'' ?>>To Do</option>
                            <option value="in_progress" <?= $task['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                            <option value="finished" <?= $task['status']=='finished'?'selected':'' ?>>Finished</option>
                        </select>
                        <button type="submit" class="btn" style="padding: 6px 12px; font-size: 14px;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
