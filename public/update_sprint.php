<?php
session_start();
require_once __DIR__ . '/../Models/sprint.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'scrum'])) {
    die("Access denied!");
}

$sprintModel = new Sprint();
$message = '';
$sprint = null;

if (isset($_GET['id'])) {
    $sprint = $sprintModel->getById($_GET['id']);
}

if (!$sprint) {
    die("Sprint not found!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sprint_name = $_POST['sprint_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    
    $updated = $sprintModel->update($sprint['id'], $sprint_name, $start_date, $end_date, $status);
    
    if ($updated) {
        $message = "Sprint updated successfully!";
        $sprint = $sprintModel->getById($sprint['id']);

        $notifModel = new Notification();
        $notifMsg = "You updated sprint: " . $sprint_name;
        $notifModel->create($_SESSION['user_id'], $notifMsg);

    } else {
        $message = "Error updating sprint.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Sprint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <h2>Update Sprint</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Sprint Name</label>
                <input type="text" name="sprint_name" value="<?= htmlspecialchars($sprint['sprint_name']) ?>" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($sprint['start_date']) ?>">
                </div>
                <div>
                    <label>End Date</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($sprint['end_date']) ?>">
                </div>
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?= $sprint['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $sprint['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="finished" <?= $sprint['status'] == 'finished' ? 'selected' : '' ?>>Finished</option>
                </select>
            </div>

            <div style="margin-top: 10px;">
                <button type="submit">Update Sprint</button>
                <a href="sprints.php" class="btn btn--secondary" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
