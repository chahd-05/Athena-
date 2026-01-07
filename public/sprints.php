<?php
session_start();
require_once __DIR__ . '/../Models/sprint.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'scrum'])) {
    die("🚫 Access denied!");
}

$sprintModel = new Sprint();
$sprints = $sprintModel->getAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sprints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2>Manage Sprints</h2>
        <div>
            <a href="create_sprint.php" class="btn">New Sprint</a>
            <a href="Dashboard.php" class="btn btn--secondary">Back to Dashboard</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sprint Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sprints as $sprint): ?>
            <tr>
                <td><?= $sprint['id'] ?></td>
                <td><?= htmlspecialchars($sprint['sprint_name']) ?></td>
                <td><?= htmlspecialchars($sprint['start_date']) ?></td>
                <td><?= htmlspecialchars($sprint['end_date']) ?></td>
                <td>
                    <span class="status status--<?= $sprint['status'] ?? 'default' ?>">
                        <?= htmlspecialchars(ucfirst($sprint['status'] ?? '')) ?>
                    </span>
                </td>
                <td>
                    <a href="update_sprint.php?id=<?= $sprint['id'] ?>" class="btn btn--secondary" style="padding: 6px 12px; font-size: 14px;">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
