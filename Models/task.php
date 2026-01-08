<?php
require_once __DIR__ . '/../core/Database.php';


class Task {
    private $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }
    public function existsInSprint($title, $sprint_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE sprint_id = :sprint_id AND task_title = :title");
        $stmt->execute([':sprint_id' => $sprint_id, ':title' => $title]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create($title, $description, $sprint_id, $assigned_to, $priority = 'medium') {
        try {
            if ($this->existsInSprint($title, $sprint_id)) {
                return false;
            }
            $sql = "INSERT INTO tasks (task_title, description, sprint_id, assigned_to, priority)
                    VALUES (:title, :description, :sprint_id, :assigned_to, :priority)";
            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':sprint_id' => $sprint_id,
                ':assigned_to' => $assigned_to,
                ':priority' => $priority
            ]);

            return $result;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT t.*, 
                   s.sprint_name, 
                   u.full_name AS assigned_name
            FROM tasks t
            LEFT JOIN sprints s ON t.sprint_id = s.id
            LEFT JOIN user u ON t.assigned_to = u.id
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function search($filters = [], $limit = 10, $offset = 0) {
        $where = [];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = "t.task_title LIKE :q";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "t.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = "t.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        if (!empty($filters['assigned_to'])) {
            $where[] = "t.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "
            SELECT t.*, 
                   s.sprint_name, 
                   u.full_name AS assigned_name
            FROM tasks t
            LEFT JOIN sprints s ON t.sprint_id = s.id
            LEFT JOIN user u ON t.assigned_to = u.id
            $whereSql
            ORDER BY t.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function count($filters = []) {
        $where = [];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = "task_title LIKE :q";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = "priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        if (!empty($filters['assigned_to'])) {
            $where[] = "assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT COUNT(*) FROM tasks $whereSql";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    public function getByUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   s.sprint_name
            FROM tasks t
            LEFT JOIN sprints s ON t.sprint_id = s.id
            WHERE t.assigned_to = :user_id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($task_id) {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   s.sprint_name, 
                   u.full_name AS assigned_name
            FROM tasks t
            LEFT JOIN sprints s ON t.sprint_id = s.id
            LEFT JOIN user u ON t.assigned_to = u.id
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => $task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateStatus($task_id, $status) {
        $stmt = $this->db->prepare("
            UPDATE tasks 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':status' => $status,
            ':id' => $task_id
        ]);
    }
    public function isOwner($task_id, $user_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE id = :id AND assigned_to = :user_id");
        $stmt->execute([':id' => $task_id, ':user_id' => $user_id]);
        return (int)$stmt->fetchColumn() > 0;
    }
    public function update($id, $title, $description, $priority, $user_id, $role) {
        if ($role === 'member' && !$this->isOwner($id, $user_id)) {
            return false;
        }
        $stmt = $this->db->prepare("
            UPDATE tasks
            SET task_title = :title,
                description = :description,
                priority = :priority
            WHERE id = :id
        ");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority,
            ':id' => $id
        ]);
    }
    public function delete($id, $user_id, $role) {
        if ($role === 'member' && !$this->isOwner($id, $user_id)) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
