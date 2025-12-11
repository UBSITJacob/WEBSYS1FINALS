<?php
header('Content-Type: application/json');
include '../pdo_functions.php';
include '../session.php';
include '../utils/response.php';
include '../utils/auth.php';
include '../utils/validator.php';

try {
    if (!AuthHelper::requireAdmin()) {
        http_response_code(403);
        die(json_encode(ApiResponse::error('Unauthorized access', 403)));
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die(json_encode(ApiResponse::error('Method not allowed', 405)));
    }

    $type = trim($_POST['type'] ?? '');
    $pdo = new pdoCRUD();
    $result = null;
    $message = '';

    try {
        switch ($type) {
            case 'teacher':
                $faculty_id = Validator::sanitizeString($_POST['faculty_id'] ?? '');
                $full_name = Validator::sanitizeString($_POST['full_name'] ?? '');
                $username = Validator::sanitizeString($_POST['username'] ?? '');
                $sex = Validator::sanitizeString($_POST['sex'] ?? '');
                $email = Validator::sanitizeEmail($_POST['email'] ?? '');

                if (!$faculty_id || !$full_name || !$username || !$email || !$sex) {
                    throw new Exception('All teacher fields are required');
                }

                $new_id = $pdo->addTeacher($faculty_id, $full_name, $username, $sex, $email);
                $result = (int)$new_id > 0;
                $message = $result ? 'Teacher added successfully' : 'Failed to add teacher';
                break;

            case 'section':
                $name = Validator::sanitizeString($_POST['name'] ?? '');
                $department = Validator::sanitizeString($_POST['department'] ?? '');
                $gradelevel = Validator::sanitizeString($_POST['gradelevel'] ?? '');
                $strand = Validator::sanitizeString($_POST['strand'] ?? '');
                $capacity = Validator::sanitizeInt($_POST['capacity'] ?? 0);

                if (!$name || !$department || !$gradelevel || $capacity < 1) {
                    throw new Exception('Section name, department, grade level and capacity are required');
                }

                if ($capacity > 60) {
                    throw new Exception('Capacity must not exceed 60');
                }

                $new_id = $pdo->addSection($name, $department, $gradelevel, $strand, $capacity);
                $result = (int)$new_id > 0;
                $message = $result ? 'Section added successfully' : 'Failed to add section';
                break;

            case 'load':
                $teacher_id = Validator::sanitizeInt($_POST['teacher_id'] ?? 0);
                $subject_id = Validator::sanitizeInt($_POST['subject_id'] ?? 0);
                $section_id = Validator::sanitizeInt($_POST['section_id'] ?? 0);
                $school_year = Validator::sanitizeString($_POST['school_year'] ?? '');
                $semester = Validator::sanitizeString($_POST['semester'] ?? '');

                if (!$teacher_id || !$subject_id || !$section_id || !$school_year) {
                    throw new Exception('Teacher, subject, section and school year are required');
                }

                $new_id = $pdo->addSubjectLoad($teacher_id, $subject_id, $section_id, $school_year, $semester);
                $result = (int)$new_id > 0;
                $message = $result ? 'Subject load added successfully' : 'Failed to add subject load';
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
                    throw new Exception('Subject name, department, and grade level are required');
                }

                $new_id = $pdo->addSubject($code, $name, $description, $department, $gradelevel, $strand, $semester);
                $result = (int)$new_id > 0;
                $message = $result ? 'Subject added successfully' : 'Failed to add subject';
                break;

            case 'admin':
                $faculty_id = Validator::sanitizeString($_POST['faculty_id'] ?? '');
                $full_name = Validator::sanitizeString($_POST['full_name'] ?? '');
                $username = Validator::sanitizeString($_POST['username'] ?? '');
                $sex = Validator::sanitizeString($_POST['sex'] ?? '');
                $email = Validator::sanitizeEmail($_POST['email'] ?? '');

                if (!$faculty_id || !$full_name || !$username || !$email || !$sex) {
                    throw new Exception('All admin fields are required');
                }

                $new_id = $pdo->addAdmin($faculty_id, $full_name, $username, $sex, $email);
                $result = (int)$new_id > 0;
                $message = $result ? 'Admin added successfully' : 'Failed to add admin';
                break;

            default:
                throw new Exception('Invalid type provided');
        }

        if ($result) {
            http_response_code(200);
            die(json_encode(ApiResponse::success(['type' => $type, 'id' => $new_id], $message)));
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
