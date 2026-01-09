<?php
session_start();
require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/../Models/Notification.php';
require_once __DIR__ . '/../Models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
$role    = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$notifModel = new Notification();
$notifications = $notifModel->getByUser($user_id);

$taskModel = new Task();
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$filters = [
    'q' => $_GET['q'] ?? null,
    'status' => $_GET['status'] ?? null,
    'priority' => $_GET['priority'] ?? null,
    'assigned_to' => null
];
if ($role === 'member') {
    $filters['assigned_to'] = $user_id;
} else {
    if (!empty($_GET['assigned_to'])) {
        $filters['assigned_to'] = (int)$_GET['assigned_to'];
    }
}
$tasks = $taskModel->search($filters, $limit, $offset);
$totalTasks = $taskModel->count($filters);
$totalPages = max(1, (int)ceil($totalTasks / $limit));
$users = [];
if ($role === 'admin' || $role === 'scrum') {
    $userModel = new User();
    $users = $userModel->getAll();
}

function statusClass($status) {
    if ($status === 'to_do') return 'status--to_do';
    if ($status === 'in_progress') return 'status--in_progress';
    if ($status === 'finished') return 'status--finished';
    return 'status--default';
}
function statusLabel($status) {
    return ucfirst(str_replace('_', ' ', $status));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <style>
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
        }
        .task-card {
            background: var(--card);
            border-radius: 12px;
            padding: 16px;
            box-shadow: var(--shadow);
        }
        .task-title { 
            margin: 0 0 8px; 
            color: var(--primary); 
            font-size: 18px; 
            line-height: 1.3;
        }
        .task-meta { margin-bottom: 10px; }
        .task-info { 
            list-style: none; 
            margin: 0 0 12px; 
            padding: 0; 
            display: grid; 
            gap: 6px; 
        }
        .task-info li { font-size: 14px; }
        .task-actions { display: flex; gap: 10px; }
        .link { font-weight: 600; font-size: 14px; }
    </style>
</head>

<body>

<header class="topbar">
    <div class="topbar__title">Dashboard</div>
    <div class="topbar__actions">
        <span class="role"><?= htmlspecialchars($role) ?></span>
        <a href="logout.php" class="btn btn--danger">Logout</a>
    </div>
</header>

<main class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h3 class="section-title" style="margin:0">Tasks</h3>
        <?php if ($role === 'scrum' || $role === 'admin' || $role === 'member'): ?>
            <a href="create_task.php" class="btn">New Task</a>
        <?php endif; ?>
    </div>
    <form method="GET" style="margin-bottom: 16px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
        <input type="text" name="q" placeholder="Search by title" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <select name="status">
            <option value="">All statuses</option>
            <option value="to_do" <?= (($_GET['status'] ?? '')==='to_do')?'selected':'' ?>>To Do</option>
            <option value="in_progress" <?= (($_GET['status'] ?? '')==='in_progress')?'selected':'' ?>>In Progress</option>
            <option value="finished" <?= (($_GET['status'] ?? '')==='finished')?'selected':'' ?>>Finished</option>
        </select>
        <select name="priority">
            <option value="">All priorities</option>
            <option value="low" <?= (($_GET['priority'] ?? '')==='low')?'selected':'' ?>>Low</option>
            <option value="medium" <?= (($_GET['priority'] ?? '')==='medium')?'selected':'' ?>>Medium</option>
            <option value="high" <?= (($_GET['priority'] ?? '')==='high')?'selected':'' ?>>High</option>
        </select>
        <?php if ($role === 'admin' || $role === 'scrum'): ?>
        <select name="assigned_to">
            <option value="">All users</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ((isset($_GET['assigned_to']) && (int)$_GET['assigned_to']==$u['id']))?'selected':'' ?>><?= htmlspecialchars($u['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div>
            <button type="submit" class="btn">Filter</button>
        </div>
    </form>

    <?php if ($role === 'admin' || $role === 'scrum'): ?>
        <div style="margin-bottom: 20px; display:flex; gap: 10px;">
            <a href="create_sprint.php" class="btn btn--secondary">New Sprint</a>
            <a href="sprints.php" class="btn btn--secondary">Manage Sprints</a>
            <?php if ($role === 'admin'): ?>
                <a href="create_project.php" class="btn btn--secondary">New Project</a>
                <a href="project_managment.php" class="btn btn--secondary">Projects</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 30px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0;">Recent Notifications</h3>
        <?php if (empty($notifications)): ?>
            <p style="color: #666;">No notifications yet.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($notifications as $notif): ?>
                    <li style="padding: 10px; border-bottom: 1px solid #eee; <?= ($notif['is_read'] ?? 0) ? 'opacity: 0.6;' : 'font-weight: bold;' ?>">
                        <?= htmlspecialchars($notif['message']) ?>
                        <br>
                        <small style="color: #888;"><?= $notif['created_at'] ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php if (empty($tasks)): ?>
        <p class="no-tasks">There are no tasks yet.</p>
    <?php else: ?>
        <div class="tasks-grid">
            <?php foreach ($tasks as $task): ?>
                <article class="task-card">
                    <h4 class="task-title"><?= htmlspecialchars($task['task_title']) ?></h4>
                    <div class="task-meta">
                        <span class="status <?= statusClass($task['status']) ?>"><?= statusLabel($task['status']) ?></span>
                    </div>
                    <ul class="task-info">
                        <li><strong>Priority:</strong> <?= ucfirst($task['priority']) ?></li>
                        <li><strong>Sprint:</strong> <?= htmlspecialchars($task['sprint_name'] ?? '-') ?></li>
                        <li><strong>Assigned to:</strong> <?= htmlspecialchars($task['assigned_name'] ?? '-') ?></li>
                    </ul>
                    <div class="task-actions">
                        <a class="link" href="task_comment.php?task_id=<?= $task['id'] ?>">Comments</a>
                        <?php if ($role === 'member' && ($task['assigned_to'] ?? null) == $user_id): ?>
                            <a class="link" href="status_task.php?id=<?= $task['id'] ?>">Update Status</a>
                            <a class="link" href="edit_task.php?id=<?= $task['id'] ?>">Edit</a>
                            <a class="link" href="delete_task.php?id=<?= $task['id'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:16px;">
    <?php if ($page > 1): ?>
        <a class="btn btn--secondary" href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>">Prev</a>
    <?php endif; ?>
    <span>Page <?= $page ?> / <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
        <a class="btn btn--secondary" href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>">Next</a>
    <?php endif; ?>
    <a href="profile.php" class="btn btn--secondary">My Profile</a>
 </div>

</body>
</html>
