<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// The Database class (Database) is expected to be defined in oop_functions.php
$db = new Database();
// NOTE: $conn uses mysqli for fetch_all, as shown by your code.
$conn = $db->getConnection(); 

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        
        // =========================================================
        // READ ALL POSSIBLE ASSIGNMENTS (Filtered by Grade Level)
        // =========================================================
        case 'get_all_assignments':
            // Enforces s.grade_level = sub.grade_level to list only valid pairings.
            $sql = "
                SELECT 
                    s.id AS section_id, s.grade_level, s.section_letter, s.section_name,
                    sub.id AS subject_id, sub.subject_code, sub.subject_name
                FROM section s
                JOIN subject sub ON s.grade_level = sub.grade_level
                ORDER BY s.grade_level, s.section_name, sub.subject_name
            ";
            
            // Execute using query for simplicity since no user input is involved
            $result = $conn->query($sql);
            $assignments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            echo json_encode(['status' => 'success', 'assignments' => $assignments]);
            break;

        // =========================================================
        // READ CURRENT TEACHER LOADS
        // =========================================================
        case 'get_teacher_loads':
            $teacher_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($teacher_id <= 0) throw new Exception("Missing teacher ID.");

            $sql = "
                SELECT 
                    sa.id AS load_id, sa.section_id, sa.subject_id,
                    s.grade_level, s.section_name, s.section_letter,
                    sub.subject_name, sub.subject_code
                FROM section_assignment sa
                JOIN section s ON sa.section_id = s.id
                JOIN subject sub ON sa.subject_id = sub.id
                WHERE sa.teacher_id = ?
                ORDER BY s.grade_level, sub.subject_name
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $teacher_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $loads = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
            echo json_encode(['status' => 'success', 'loads' => $loads]);
            break;

        // =========================================================
        // CREATE ASSIGNMENT
        // =========================================================
        case 'add_assignment':
            $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;
            $section_id = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;
            $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : 0;
            $assignment_type = isset($_POST['assignment_type']) ? trim($_POST['assignment_type']) : 'Subject Teacher';
            
            if ($teacher_id <= 0 || $section_id <= 0 || $subject_id <= 0) throw new Exception("Missing IDs for assignment.");

            $sql = "
                INSERT INTO section_assignment (section_id, subject_id, teacher_id, assignment_type)
                VALUES (?, ?, ?, ?)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $section_id, $subject_id, $teacher_id, $assignment_type);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Assignment added successfully.']);
            } else {
                // Check for duplicate key error (MySQL error code 1062)
                if ($conn->errno == 1062) {
                    throw new Exception("This teacher is already assigned to this class and subject.");
                }
                throw new Exception("Database error inserting assignment.");
            }
            $stmt->close();
            break;

        // =========================================================
        // DELETE ASSIGNMENT
        // =========================================================
        case 'delete_assignment':
            $load_id = isset($_POST['load_id']) ? intval($_POST['load_id']) : 0;
            if ($load_id <= 0) throw new Exception("Missing load ID.");
            
            $sql = "DELETE FROM section_assignment WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $load_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Assignment removed successfully.']);
            } else {
                throw new Exception("Database error removing assignment.");
            }
            $stmt->close();
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}