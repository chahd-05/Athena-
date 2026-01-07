<?php

require_once __DIR__ . '/../core/Database.php';

class Project {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }

    public function create($project_name, $description, $start_date = null, $end_date = null) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in");
        }

        $created_by = $_SESSION['user_id'];

        $sql = "INSERT INTO projects 
                (project_name, description, created_by, start_date, end_date)
                VALUES 
                (:project_name, :description, :created_by, :start_date, :end_date)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'project_name' => $project_name,
            'description'  => $description,
            'created_by'   => $created_by,
            'start_date'   => $start_date,
            'end_date'     => $end_date
        ]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM projects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStatus($id, $status) {
        $sql = "UPDATE projects SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
