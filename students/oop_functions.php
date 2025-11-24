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
       Fetches user details and current enrollment section (most recent AY).
       NOTE: This fetches the student's current enrollment based on the 
       LATEST academic_year recorded in the enrollment table.
    ----------------------------------------------------------------- */
    public function getProfile() {
        $stmt = $this->conn->prepare("
            SELECT 
                u.fullname, 
                u.username,
                u.email, 
                sd.grade_level,
                sd.birthdate AS birthday,
                sd.gender,
                sd.contact_no AS mobile,
                s.section_name AS section
            FROM users u
            JOIN student_details sd ON u.id = sd.user_id 
            -- We find the current enrollment by ordering by Academic Year
            LEFT JOIN enrollment e ON u.id = e.student_id 
            LEFT JOIN section s ON e.section_id = s.id 
            WHERE u.id = ? 
            ORDER BY e.academic_year DESC 
            LIMIT 1
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* -----------------------------------------------------------------
       GET ENROLLMENT (SUBJECTS)
       Fetches all subjects assigned to the student's current section.
    ----------------------------------------------------------------- */
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
    
    /* -----------------------------------------------------------------
       GET GRADES
       Fetches the grade history from the 'grade' table.
    ----------------------------------------------------------------- */
public function getGrades() {
    $stmt = $this->conn->prepare("
        SELECT 
            g.grade_value, 
            g.grading_period,
            subj.subject_name
        FROM grade g
        JOIN subject subj ON g.subject_id = subj.id
        WHERE g.student_id = ?
        ORDER BY subj.subject_name, g.grading_period
    ");
    $stmt->execute([$this->id]);
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
    
    // Convert back to an indexed array for easier HTML looping
    return array_values($gradesPivot); 
}
    /* -----------------------------------------------------------------
       TOTAL COURSES
       Counts the number of unique subjects the student is currently enrolled in.
    ----------------------------------------------------------------- */
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