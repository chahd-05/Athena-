<?php

require_once __DIR__ . '/../core/Database.php';

class Sprint {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }

    public function create($sprint_name, $project_id, $start_date = null, $end_date = null) {
        try {
            if ($start_date && $end_date && $this->hasDateConflict($project_id, $start_date, $end_date)) {
                return false;
            }
            $sql = "INSERT INTO sprints (sprint_name, project_id, start_date, end_date)
                    VALUES (:sprint_name, :project_id, :start_date, :end_date)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':sprint_name' => $sprint_name,
                ':project_id'  => $project_id,
                ':start_date'  => $start_date,
                ':end_date'    => $end_date
            ]);

        } catch (PDOException $e) {
            echo "Error creating sprint: " . $e->getMessage();
            return false;
        }
    }

    public function hasDateConflict($project_id, $start_date, $end_date) {
        $sql = "
            SELECT COUNT(*) FROM sprints 
            WHERE project_id = :project_id
              AND (
                    (:start BETWEEN start_date AND end_date)
                 OR (:end BETWEEN start_date AND end_date)
                 OR (start_date BETWEEN :start AND :end)
                 OR (end_date BETWEEN :start AND :end)
              )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':project_id' => $project_id,
            ':start' => $start_date,
            ':end' => $end_date
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM sprints");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error fetching sprints: " . $e->getMessage();
            return [];
        }
    }

    public function getByProject($project_id) {
        try {
            $sql = "SELECT * FROM sprints WHERE project_id = :project_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':project_id' => $project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error fetching sprints for project: " . $e->getMessage();
            return;
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM sprints WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching sprint: " . $e->getMessage();
            return false;
        }
    }

    public function update($id, $sprint_name, $start_date, $end_date, $status) {
        try {
            $sql = "UPDATE sprints 
                    SET sprint_name = :sprint_name, 
                        start_date = :start_date, 
                        end_date = :end_date,
                        status = :status
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':sprint_name' => $sprint_name,
                ':start_date'  => $start_date,
                ':end_date'    => $end_date,
                ':status'      => $status,
                ':id'          => $id
            ]);

        } catch (PDOException $e) {
            echo "Error updating sprint: " . $e->getMessage();
            return false;
        }
    }
}
