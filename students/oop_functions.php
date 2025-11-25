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

    /* -----------------------------------------------------------------
       GET PROFILE
       (Unchanged - Confirmed working in prior steps)
    ----------------------------------------------------------------- */
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

    /* -----------------------------------------------------------------
       GET ENROLLMENT (Unchanged - Confirmed working in prior steps)
    ----------------------------------------------------------------- */
    public function getEnrollment() {
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

    /* -----------------------------------------------------------------
       GET GRADES (FIXED)
       Corrected table name from 'grades' to 'grade' and added joins/columns.
    ----------------------------------------------------------------- */
    public function getGrades() {
        $stmt = $this->conn->prepare("
            SELECT 
                g.grade_value, 
                g.grading_period,
                subj.subject_name
            FROM grade g /* <--- FIXED TABLE NAME: 'grade' */
            JOIN subject subj ON g.subject_id = subj.id
            WHERE g.student_id = ?
            ORDER BY subj.subject_name, g.grading_period
        ");
        $stmt->execute([$this->id]);
        
        // --- Added Pivoting Logic for Columnar Display ---
        $gradeRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $gradesPivot = [];
        foreach ($gradeRows as $row) {
            $subject = $row['subject_name'];
            $period = $row['grading_period'];
            $value = $row['grade_value'];

            // Initialize the subject array if it doesn't exist
            if (!isset($gradesPivot[$subject])) {
                $gradesPivot[$subject] = [
                    '1st Qtr' => null,
                    '2nd Qtr' => null,
                    '3rd Qtr' => null,
                    '4th Qtr' => null,
                    'Final'   => null,
                    'Subject' => $subject // Keep the name handy
                ];
            }

            // Store the grade under the correct period key
            $gradesPivot[$subject][$period] = $value;
        }
        
        return array_values($gradesPivot); 
    }

    /* -----------------------------------------------------------------
       TOTAL COURSES (Unchanged - Confirmed working in prior steps)
    ----------------------------------------------------------------- */
    public function totalCourses() {
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