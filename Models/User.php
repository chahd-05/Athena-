<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }
    public function register($full_name, $email, $password) {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (full_name, email, password) VALUES (:full_name, :email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':password' => $hashed
        ]);
    }
    public function login($email, $password) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
    public function getByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $sql = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateProfile($id, $full_name, $password = null) {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET full_name = :full_name, password = :password WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':full_name' => $full_name,
                ':password' => $hashed,
                ':id' => $id
            ]);
        } else {
            $sql = "UPDATE user SET full_name = :full_name WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':full_name' => $full_name,
                ':id' => $id
            ]);
        }
    }
}
