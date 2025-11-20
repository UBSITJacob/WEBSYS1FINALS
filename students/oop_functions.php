<?php
class Database {
    private $host = "localhost";
    private $db_name = "evelio_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            exit;
        }
        return $this->conn;
    }
}

class Student {
    private $conn;
    private $id;

    public function __construct($student_id) {
        $db = new Database();
        $this->conn = $db->connect();
        $this->id = $student_id;
    }

    public function getProfile() {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEnrollment() {
        $stmt = $this->conn->prepare("
            SELECT c.course_name, c.description, e.enrolled_on
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.student_id = ?
            ORDER BY e.enrolled_on DESC
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGrades() {
        $stmt = $this->conn->prepare("SELECT subject, grade FROM grades WHERE student_id = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalCourses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM enrollments WHERE student_id = ?");
        $stmt->execute([$this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
