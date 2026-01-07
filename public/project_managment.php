<?php
session_start();
require_once __DIR__ . '/../Models/Project.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

$projectModel = new Project();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id && $action === 'activate') {
        $projectModel->updateStatus($id, 'active');
        $message = 'Project activated';
    } elseif ($id && $action === 'deactivate') {
        $projectModel->updateStatus($id, 'inactive');
        $message = 'Project deactivated';
    } elseif ($id && $action === 'delete') {
        $projectModel->delete($id);
        $message = 'Project deleted';
    }
}
$projects = $projectModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2>Project Management</h2>
        <a href="Dashboard.php" class="btn btn--secondary">Back to Dashboard</a>
    </div>
    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= $project['id'] ?></td>
                <td><?= htmlspecialchars($project['project_name']) ?></td>
                <td><?= htmlspecialchars($project['description']) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                        <input type="hidden" name="action" value="activate">
                        <button type="submit" class="btn">Activate</button>
                    </form>
                    <form method="POST" style="display:inline; margin-left:6px;">
                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                        <input type="hidden" name="action" value="deactivate">
                        <button type="submit" class="btn btn--secondary">Deactivate</button>
                    </form>
                    <form method="POST" style="display:inline; margin-left:6px;">
                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn--danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
