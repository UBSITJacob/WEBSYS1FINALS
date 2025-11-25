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
            // Using PDO for connection
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

    // Suggested Fix for getProfile()
// oop_functions.php - Inside class Student
public function getProfile() {
    $stmt = $this->conn->prepare("
        SELECT 
            u.fullname, 
            u.email, 
            sd.*,
            s.section_name AS section,  /* Alias the section name to 'section' */
            s.grade_level                /* Get the grade level from the section table */
        FROM users u
        JOIN student_details sd ON u.id = sd.user_id 
        -- Join enrollment (using singular 'enrollment')
        LEFT JOIN enrollment e ON u.id = e.student_id 
        -- Join section to get the name (using singular 'section')
        LEFT JOIN section s ON e.section_id = s.id 
        WHERE u.id = ? 
        LIMIT 1
    ");
    $stmt->execute([$this->id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function getEnrollment() {
        // 1. Find the student's current section
        $stmtEnrollment = $this->conn->prepare("
            SELECT section_id
            FROM enrollment
            WHERE student_id = ?
            ORDER BY academic_year DESC
            LIMIT 1
        ");
        $stmtEnrollment->execute([$this->id]);
        $currentEnrollment = $stmtEnrollment->fetch(PDO::FETCH_ASSOC);

        if (!$currentEnrollment) return [];

        $sectionId = $currentEnrollment['section_id'];
        
        // 2. Fetch subjects assigned to that section
        $stmtSubjects = $this->conn->prepare("
            SELECT subj.subject_name, subj.subject_code
            FROM section_assignment sa
            JOIN subject subj ON sa.subject_id = subj.id
            WHERE sa.section_id = ?
            ORDER BY subj.subject_name
        ");
        $stmtSubjects->execute([$sectionId]);
        
        return $stmtSubjects->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGrades() {
        $stmt = $this->conn->prepare("SELECT subject, grade FROM grades WHERE student_id = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalCourses() {
        // Uses the same logic as getEnrollment to find the count of subjects
        $stmtEnrollment = $this->conn->prepare("
            SELECT COUNT(sa.subject_id) AS total
            FROM enrollment e
            LEFT JOIN section_assignment sa ON e.section_id = sa.section_id
            WHERE e.student_id = ?
            ORDER BY e.academic_year DESC
            LIMIT 1
        ");
        $stmtEnrollment->execute([$this->id]);
        $row = $stmtEnrollment->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
