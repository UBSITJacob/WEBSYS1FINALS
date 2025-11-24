<?php

class TeacherDB {

    private $conn;

    public function __construct() {
        // CHANGE THESE IF NEEDED
        $host = "localhost";
        $db   = "evelio_db";    // your database
        $user = "root";         // your username
        $pass = "";             // your password

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
        $stmt->bindParam(":stud", $student_id, PDO::PARAM_INT);
        $stmt->bindParam(":sec", $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":tid", $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(":gp", $grading_period, PDO::PARAM_STR);
        $stmt->bindParam(":gv", $grade_value, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /* -----------------------------------------------------------
       BULK SAVE GRADES FROM CSV FILE (Wrapper logic)
    ----------------------------------------------------------- */
    public function saveGradesFromCsv($file_path, $section_id, $subject_id, $teacher_id, $grading_period) {
        $uploaded_count = 0;
        $error_messages = [];
        $file_handle = fopen($file_path, 'r');
        
        if ($file_handle === FALSE) {
            return [0, ["Failed to open the uploaded file."]];
        }
        
        while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
            if (count($data) < 2) continue;
            
            $student_id = (int) trim($data[0]);
            $grade_value = trim($data[1]);
            
            if ($student_id > 0 && is_numeric($grade_value) && (float)$grade_value >= 60 && (float)$grade_value <= 100) {
                $saved = $this->saveGrade(
                    $student_id, (int)$section_id, (int)$subject_id, (int)$teacher_id, $grading_period, (float)$grade_value
                );
                if ($saved) {
                    $uploaded_count++;
                } else {
                    $error_messages[] = "Database error saving grade for student {$student_id}.";
                }
            } else if ($student_id > 0) {
                 $error_messages[] = "Skipped grade for student {$student_id}: Grade value '{$data[1]}' is invalid (must be 60-100).";
            }
        }
        
        fclose($file_handle);
        return [$uploaded_count, $error_messages];
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

    /* -----------------------------------------------------------
       GET EXISTING GRADES FOR SECTION + SUBJECT + PERIOD
       (Used for pre-filling the input field for the active period)
    ----------------------------------------------------------- */
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

    /* -----------------------------------------------------------
       GET ALL GRADES (5 Periods) for a Section + Subject
       (Used to populate all columns in the grading sheet)
    ----------------------------------------------------------- */
    public function getAllGradesForClass($section_id, $subject_id) {
        $sql = "SELECT student_id, grading_period, grade_value
                FROM grade
                WHERE section_id = :sec
                  AND subject_id = :subj";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grades = [];
        foreach ($rows as $row) {
            // Structure: [student_id] => [grading_period] => grade_value
            $grades[$row['student_id']][$row['grading_period']] = $row['grade_value'];
        }
        return $grades;
    }


    /* -----------------------------------------------------------
       GET CLASS INFO (FOR GRADING SHEET HEADER)
    ----------------------------------------------------------- */
    public function getClassInfo($section_id, $subject_id, $teacher_id) {
        $sql = "SELECT s.section_name, s.grade_level, s.section_letter, 
                       subj.subject_name
                FROM section_assignment sa
                JOIN section s ON sa.section_id = s.id
                JOIN subject subj ON sa.subject_id = subj.id
                WHERE sa.section_id = :sec AND sa.subject_id = :subj AND sa.teacher_id = :tid
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":tid",  $teacher_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: [
            'section_name' => 'N/A', 
            'subject_name' => 'Invalid Load', 
            'grade_level' => 'N/A', 
            'section_letter' => ''
        ];
    }
    
    /* -----------------------------------------------------------
       MISC HELPER METHODS
    ----------------------------------------------------------- */
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

        $ok = $this->enrollStudent($student_id, $section_id, $academic_year);

        if ($ok) {
            return [true, "Student {$school_id} enrolled/updated successfully."];
        } else {
            return [false, "Error enrolling student {$school_id}. Please try again."];
        }
    }

    /* -----------------------------------------------------------
       GET EXISTING ATTENDANCE FOR SECTION + SUBJECT + MONTH
    ----------------------------------------------------------- */
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


    /* -----------------------------------------------------------
   SAVE DAILY ATTENDANCE (SF2 daily marks)
----------------------------------------------------------- */
    public function saveDailyAttendance($student_id, $section_id, $subject_id, $teacher_id, $date, $status) {

        $sql = "INSERT INTO daily_attendance 
                    (student_id, section_id, subject_id, teacher_id, attendance_date, status)
                VALUES 
                    (:stud, :sec, :subj, :tid, :dt, :st)
                ON DUPLICATE KEY UPDATE
                    status = VALUES(status)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":stud", $student_id, PDO::PARAM_INT);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":tid",  $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(":dt",   $date);
        $stmt->bindParam(":st",   $status);

        return $stmt->execute();
    }

        /* -----------------------------------------------------------
    GET DAILY ATTENDANCE FOR SF2 GRID (full daily history)
    ----------------------------------------------------------- */
    public function getDailyAttendance($section_id, $subject_id, $year, $month) {

        $sql = "SELECT student_id, attendance_date, status
                FROM daily_attendance
                WHERE section_id = :sec
                AND subject_id = :subj
                AND YEAR(attendance_date) = :yr
                AND MONTH(attendance_date) = :mn";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":yr",   $year, PDO::PARAM_INT);
        $stmt->bindParam(":mn",   $month, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        /* -----------------------------------------------------------
    SAVE MONTHLY SUMMARY (auto-computed from daily attendance)
    ----------------------------------------------------------- */
    public function saveMonthlySummary($student_id, $section_id, $subject_id, $teacher_id, $year, $month, $presentCount) {

        $sql = "INSERT INTO monthly_attendance_summary
                    (student_id, section_id, subject_id, teacher_id, attendance_year, attendance_month, days_present_count)
                VALUES 
                    (:stud, :sec, :subj, :tid, :yr, :mn, :pc)
                ON DUPLICATE KEY UPDATE
                    days_present_count = VALUES(days_present_count)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":stud", $student_id, PDO::PARAM_INT);
        $stmt->bindParam(":sec",  $section_id, PDO::PARAM_INT);
        $stmt->bindParam(":subj", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":tid",  $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(":yr",   $year, PDO::PARAM_INT);
        $stmt->bindParam(":mn",   $month, PDO::PARAM_INT);
        $stmt->bindParam(":pc",   $presentCount, PDO::PARAM_INT);

        return $stmt->execute();
    }


}