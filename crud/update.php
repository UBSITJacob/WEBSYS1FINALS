<?php
/**
 * API: Standardized UPDATE endpoint
 * Handles updates of teachers, sections, and subject loads
 */

header('Content-Type: application/json');
include '../pdo_functions.php';
include '../session.php';
include '../utils/response.php';
include '../utils/auth.php';
include '../utils/validator.php';

try {
    // Verify admin access
    if (!AuthHelper::requireAdmin()) {
        http_response_code(403);
        die(json_encode(ApiResponse::error('Unauthorized access', 403)));
    }

    // Require POST method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die(json_encode(ApiResponse::error('Method not allowed', 405)));
    }

    $type = trim($_POST['type'] ?? '');
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid ID provided', 400)));
    }

    // Assuming pdoCRUD is correctly defined in pdo_functions.php
    $pdo = new pdoCRUD();
    $result = null;
    $message = '';

    try {

        switch ($type) {
            case 'teacher':
                $full_name = Validator::sanitizeString($_POST['full_name'] ?? '');
                $username = Validator::sanitizeString($_POST['username'] ?? '');
                $sex = Validator::sanitizeString($_POST['sex'] ?? '');
                $email = Validator::sanitizeEmail($_POST['email'] ?? '');
                $active = Validator::sanitizeInt($_POST['active'] ?? 1);

                if (!$full_name || !$username || !$email || !$sex) {
                    throw new Exception('Full name, username, sex, and email are required');
                }

                $result = $pdo->updateTeacher($id, $full_name, $username, $sex, $email, $active);
                $message = $result ? 'Teacher updated successfully' : 'Failed to update teacher';
                break;

            case 'section':
                $name = Validator::sanitizeString($_POST['name'] ?? '');
                $department = Validator::sanitizeString($_POST['department'] ?? '');
                $gradelevel = Validator::sanitizeString($_POST['gradelevel'] ?? '');
                $strand = Validator::sanitizeString($_POST['strand'] ?? '');
                $capacity = Validator::sanitizeInt($_POST['capacity'] ?? 0);

                if (!$name || $capacity < 1) {
                    throw new Exception('Section name and capacity are required');
                }

                if ($capacity > 60) {
                    throw new Exception('Capacity must not exceed 60');
                }

                if (!$department || !$gradelevel) {
                    if (method_exists($pdo, 'getSectionMeta')) {
                        $curr = $pdo->getSectionMeta($id);
                        if (!$department) $department = $curr['department'] ?? '';
                        if (!$gradelevel) $gradelevel = $curr['grade_level'] ?? '';
                        if (!$strand) $strand = $curr['strand'] ?? '';
                    }
                }

                if (!$department || !$gradelevel) {
                    throw new Exception('Department and grade level are required');
                }

                $result = $pdo->updateSection($id, $name, $department, $gradelevel, $strand, $capacity);
                $message = $result ? 'Section updated successfully' : 'Failed to update section';
                break;

            case 'subject':
                $code = Validator::sanitizeString($_POST['code'] ?? '');
                $name = Validator::sanitizeString($_POST['name'] ?? '');
                $department = Validator::sanitizeString($_POST['department'] ?? '');
                $gradelevel = Validator::sanitizeString($_POST['gradelevel'] ?? '');
                $strand = Validator::sanitizeString($_POST['strand'] ?? '');
                $semester = Validator::sanitizeString($_POST['semester'] ?? '');
                $description = Validator::sanitizeString($_POST['description'] ?? '');

                if (!$name || !$department || !$gradelevel) {
                    throw new Exception('Subject name, department and grade level are required');
                }

                $result = $pdo->updateSubject($id, $code, $name, $description, $department, $gradelevel, $strand, $semester);
                $message = $result ? 'Subject updated successfully' : 'Failed to update subject';
                break;
            
            // --- MISSING 'load' CASE ADDED HERE ---
            case 'load':
                // Data sent from the saveEdit() JavaScript function
                $teacher_id = Validator::sanitizeInt($_POST['teacher_id'] ?? 0);
                $subject_id = Validator::sanitizeInt($_POST['subject_id'] ?? 0);
                $section_id = Validator::sanitizeInt($_POST['section_id'] ?? 0);
                $school_year = Validator::sanitizeString($_POST['school_year'] ?? '');
                $semester = Validator::sanitizeString($_POST['semester'] ?? ''); // Optional, can be empty

                // Validation
                if (!$teacher_id || !$subject_id || !$section_id || !$school_year) {
                    throw new Exception('Teacher, Subject, Section, and School Year are required fields for a load');
                }

                // Assuming pdoCRUD has a method named updateSubjectLoad
                // This is the function that runs the SQL UPDATE query for the subject loads table.
                $result = $pdo->updateSubjectLoad($id, $teacher_id, $subject_id, $section_id, $school_year, $semester);
                $message = $result ? 'Subject load updated successfully' : 'Failed to update subject load or no changes were made';
                break;
            // ------------------------------------

            case 'admin':
                $full_name = Validator::sanitizeString($_POST['full_name'] ?? '');
                $username = Validator::sanitizeString($_POST['username'] ?? '');
                $sex = Validator::sanitizeString($_POST['sex'] ?? '');
                $email = Validator::sanitizeEmail($_POST['email'] ?? '');

                if (!$full_name || !$username || !$email || !$sex) {
                    throw new Exception('Full name, username, sex, and email are required');
                }

                $result = $pdo->updateAdmin($id, $full_name, $username, $sex, $email);
                $message = $result ? 'Admin updated successfully' : 'Failed to update admin';
                break;

            default:
                throw new Exception('Invalid type provided');
        }

        if ($result) {
            http_response_code(200);
            die(json_encode(ApiResponse::success(['id' => $id, 'type' => $type], $message)));
        } else {
            http_response_code(400);
            die(json_encode(ApiResponse::error($message, 400)));
        }
    } catch (Exception $e) {
        http_response_code(500);
        die(json_encode(ApiResponse::error('Error: ' . $e->getMessage(), 500)));
    }

} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(ApiResponse::error('System error: ' . $e->getMessage(), 500)));
}
?>