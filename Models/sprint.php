<?php
require_once __DIR__ . '/../core/Database.php';

class Sprint {
    private $db;

    public function __construct(){
        $this->db = Database::getconnection();
    }

    public function create($sprint_name, $project_id, $start_date=null, $end_date=null){
        try{
            $sql = "insert into sprints(sprint_name, project_id, start_date, end_date) values(:sprint_name, :project_id, :start_date, :end_date)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['sprint_name'=>$sprint_name, 'project_id'=>$project_id, 'start_date'=>$start_date, 'end_date'=>$end_date]);
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    
    public function getAll(){
        $stmt = $this->db->query("select * from sprints");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
