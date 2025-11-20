<?php

class TeacherDB {

    private $conn;

    public function __construct() {
        // CHANGE THESE IF NEEDED
        $host = "localhost";
        $db   = "evelio_db";   // your database
        $user = "root";           // your username
        $pass = "";               // your password

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";

        try {
            $this->conn = new PDO($dsn, $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("DATABASE CONNECTION FAILED: " . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------
        LOAD TEACHER INFO
    ----------------------------------------------------------- */
    public function getTeacherInfo($user_id) {
        $sql = "SELECT u.id, u.fullname, u.email,
                       td.faculty_id, td.status
                FROM users u
                JOIN teacher_details td 
                    ON u.id = td.user_id
                WHERE u.id = :uid";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* -----------------------------------------------------------
        ADVISORY SECTION
    ----------------------------------------------------------- */
    public function getAdvisorySection($teacher_id) {
        $sql = "SELECT *
                FROM section
                WHERE adviser_id = :tid";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":tid", $teacher_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* -----------------------------------------------------------
        TEACHER LOAD (sections + subjects)
    ----------------------------------------------------------- */
    public function getTeacherLoads($teacher_id) {
        $sql = "SELECT sa.id AS load_id,
                       s.id AS section_id,
                       s.section_name,
                       s.grade_level,
                       s.section_letter,
                       subj.id AS subject_id,
                       subj.subject_name,
                       subj.subject_code,
                       sa.assignment_type
                FROM section_assignment sa
                JOIN section s ON sa.section_id = s.id
                JOIN subject subj ON sa.subject_id = subj.id
                WHERE sa.teacher_id = :tid
                ORDER BY s.grade_level, s.section_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":tid", $teacher_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -----------------------------------------------------------
        STUDENTS IN SECTION
    ----------------------------------------------------------- */
    public function getSectionStudents($section_id, $academic_year) {
        $sql = "SELECT e.student_id,
                       u.fullname,
                       sd.school_id
                FROM enrollment e
                JOIN student_details sd 
                    ON e.student_id = sd.user_id
                JOIN users u 
                    ON sd.user_id = u.id
                WHERE e.section_id = :sec 
                  AND e.academic_year = :ay
                ORDER BY u.fullname";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec", $section_id);
        $stmt->bindParam(":ay", $academic_year);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -----------------------------------------------------------
        ENROLL OR MOVE STUDENT
    ----------------------------------------------------------- */
    public function enrollStudent($student_id, $section_id, $academic_year) {
        $sql = "INSERT INTO enrollment (student_id, section_id, academic_year)
                VALUES (:sid, :secid, :ay)
                ON DUPLICATE KEY UPDATE section_id = VALUES(section_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sid", $student_id);
        $stmt->bindParam(":secid", $section_id);
        $stmt->bindParam(":ay", $academic_year);

        return $stmt->execute();
    }

    /* -----------------------------------------------------------
        SAVE GRADES
    ----------------------------------------------------------- */
    public function saveGrade($student_id, $section_id, $subject_id, $teacher_id, $grading_period, $grade_value) {
        $sql = "INSERT INTO grade
                    (student_id, section_id, subject_id, teacher_id, grading_period, grade_value)
                VALUES
                    (:stud, :sec, :subj, :tid, :gp, :gv)
                ON DUPLICATE KEY UPDATE
                    grade_value = VALUES(grade_value),
                    date_recorded = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":stud", $student_id);
        $stmt->bindParam(":sec", $section_id);
        $stmt->bindParam(":subj", $subject_id);
        $stmt->bindParam(":tid", $teacher_id);
        $stmt->bindParam(":gp", $grading_period);
        $stmt->bindParam(":gv", $grade_value);

        return $stmt->execute();
    }

    /* -----------------------------------------------------------
        ATTENDANCE
    ----------------------------------------------------------- */
    public function saveAttendance($student_id, $section_id, $subject_id, $teacher_id, $year, $month, $days_present) {
        $sql = "INSERT INTO monthly_attendance_summary
                    (student_id, section_id, subject_id, teacher_id, attendance_year, attendance_month, days_present_count)
                VALUES
                    (:stud, :sec, :subj, :tid, :yr, :mn, :dp)
                ON DUPLICATE KEY UPDATE
                    days_present_count = VALUES(days_present_count)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":stud", $student_id);
        $stmt->bindParam(":sec", $section_id);
        $stmt->bindParam(":subj", $subject_id);
        $stmt->bindParam(":tid", $teacher_id);
        $stmt->bindParam(":yr", $year);
        $stmt->bindParam(":mn", $month);
        $stmt->bindParam(":dp", $days_present);

        return $stmt->execute();
    }

        // -----------------------------------------------------------
    // GET EXISTING GRADES FOR SECTION + SUBJECT + PERIOD
    // returns: [student_id => grade_value]
    // -----------------------------------------------------------
    public function getExistingGrades($section_id, $subject_id, $grading_period) {
        $sql = "SELECT student_id, grade_value
                FROM grade
                WHERE section_id = :sec
                  AND subject_id = :subj
                  AND grading_period = :gp";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":gp",   $grading_period, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grades = [];
        foreach ($rows as $row) {
            $grades[$row['student_id']] = $row['grade_value'];
        }
        return $grades;
    }

    // -----------------------------------------------------------
    // GET EXISTING ATTENDANCE FOR SECTION + SUBJECT + MONTH
    // returns: [student_id => days_present_count]
    // -----------------------------------------------------------
    public function getExistingAttendance($section_id, $subject_id, $year, $month) {
        $sql = "SELECT student_id, days_present_count
                FROM monthly_attendance_summary
                WHERE section_id = :sec
                  AND subject_id = :subj
                  AND attendance_year = :yr
                  AND attendance_month = :mn";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":yr",   $year, PDO::PARAM_INT);
        $stmt->bindParam(":mn",   $month, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $att = [];
        foreach ($rows as $row) {
            $att[$row['student_id']] = $row['days_present_count'];
        }
        return $att;
    }

    // Find internal student_id (user_id) by School ID / LRN
public function getStudentIdBySchoolId($school_id) {
    $sql = "SELECT user_id 
            FROM student_details 
            WHERE school_id = :sid
            LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":sid", $school_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['user_id'] : null;
}

// Wrapper: enroll a student using School ID instead of numeric user_id
public function enrollStudentBySchoolId($school_id, $section_id, $academic_year) {
    $student_id = $this->getStudentIdBySchoolId($school_id);
    if (!$student_id) {
        return [false, "Student with School ID {$school_id} not found."];
    }

    // Assuming you already have enrollStudent($student_id, $section_id, $academic_year)
    $ok = $this->enrollStudent($student_id, $section_id, $academic_year);

    if ($ok) {
        return [true, "Student {$school_id} enrolled/updated successfully."];
    } else {
        return [false, "Error enrolling student {$school_id}. Please try again."];
    }
}


}
?>
