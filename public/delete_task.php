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

$deleted = $taskModel->delete($task_id, $user_id, $role);
header("Location: Dashboard.php");
exit;
