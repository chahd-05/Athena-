<?php
session_start();
require_once __DIR__ . "/../core/Database.php";

class Project {
    private PDO $db;

    public function __constuct(){
        $this->db = Database::getconnection();
    }

    public function create ($project_name, $description, $created_by, $start_date=null, $end_date=null){
        try {
            $sql = "insert into projects(project_name, description, created_by, start_date, end_date) values(:project_name, :description, :created_by, :start_date, :end_date)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['project_name'=>$project_name, 'description'=>$description, 'created_by'=>$created_by, 'start_date'=>$start_date, 'end_date'=>$end_date]);
        }
        catch(PDOException $e){
            echo $e->getMessage();    
            return false;    
        }
    }
    public function getAll(){
        $stmt = $this->db->query("select * from projects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}