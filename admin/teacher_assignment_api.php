<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$db = new Database();
$conn = $db->getConnection();

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_all_assignments':
            // Fetch all sections and subjects (for the dropdown list)
            $sql = "
                SELECT 
                    s.id AS section_id, s.grade_level, s.section_letter, s.section_name,
                    sub.id AS subject_id, sub.subject_code, sub.subject_name
                FROM section s
                CROSS JOIN subject sub
                ORDER BY s.grade_level, s.section_name, sub.subject_name;
            ";
            $result = $conn->query($sql);
            $assignments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            echo json_encode(['status' => 'success', 'assignments' => $assignments]);
            break;

        case 'get_teacher_loads':
            // Fetch loads for a specific teacher
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

        case 'add_assignment':
            // Add a new assignment (Create)
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
                // Check for duplicate key error (Teacher already assigned this specific load)
                if ($conn->errno == 1062) {
                    throw new Exception("This teacher is already assigned to this class and subject.");
                }
                throw new Exception("Database error inserting assignment.");
            }
            $stmt->close();
            break;

        case 'delete_assignment':
            // Delete an assignment (Delete)
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
?>