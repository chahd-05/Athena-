<?php 
require_once __DIR__ .'/../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }
    public function register($full_name, $email, $password, $role = 'member'){
        $stmt = $this->db->prepare("insert into user (full_name, email, password, role) values (:full_name, :email, :password, :role)");
        return $stmt->execute(['full_name'=>$full_name,
        'email'=>$email, 'password'=>$password, 'role'=>$role]);
    }
    public function login($email, $password) {
        $stmt = $this->db->prepare("select * from user where email = :email");
        $stmt->execute(['email'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && $password === $user['password']) {
            return $user;
        }
        return false;
    }
    public function getAll() {
        $stmt = $this->db->query("select id, full_name, email, role, created_at from user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>