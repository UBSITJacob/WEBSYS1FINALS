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

    // Get profile details for the student
    public function getProfile() {
        $stmt = $this->conn->prepare("
            SELECT 
                u.fullname, 
                u.email, 
                sd.*,
                s.section_name AS section,  
                s.grade_level                
            FROM users u
            JOIN student_details sd ON u.id = sd.user_id 
            LEFT JOIN enrollment e ON u.id = e.student_id 
            LEFT JOIN section s ON e.section_id = s.id 
            WHERE u.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get the courses enrolled by the student
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

    // Get the grades for the student
    public function getGrades() {
        $stmt = $this->conn->prepare("SELECT subject, grade FROM grades WHERE student_id = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total number of courses the student is enrolled in
    public function totalCourses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM enrollment WHERE student_id = ?");
        $stmt->execute([$this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
    
        public function updatePassword($old_password, $new_password) {
            // Step 1: Verify if the old password is correct
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$this->id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Check if password matches
            if (password_verify($old_password, $user['password'])) {
                // Step 2: Update with the new password
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);  // Hash the new password
                $update_stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->execute([$new_password_hashed, $this->id]);
                return ['status' => 'success', 'message' => 'Password updated successfully!'];
            } else {
                return ['status' => 'error', 'message' => 'Old password is incorrect.'];
            }
        }
    }
    
?>
