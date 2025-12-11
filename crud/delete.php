<?php
/**
 * API: Standardized DELETE endpoint
 * Handles deletion of students, teachers, sections, and subject loads
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

    // Get and validate parameters
    $type = trim($_POST['type'] ?? '');
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid ID provided', 400)));
    }

    if (!in_array($type, ['student', 'teacher', 'section', 'load', 'subject', 'admin'])) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid type provided', 400)));
    }

    $pdo = new pdoCRUD();
    $result = null;
    $message = '';

    try {

        switch ($type) {
            case 'student':
                $result = $pdo->deleteStudent($id);
                $message = $result ? 'Student deleted successfully' : 'Failed to delete student';
                break;

            case 'teacher':
                $result = $pdo->deleteTeacher($id);
                $message = $result ? 'Teacher deleted successfully' : 'Failed to delete teacher';
                break;

            case 'section':
                $result = $pdo->deleteSection($id);
                $message = $result ? 'Section deleted successfully' : 'Failed to delete section';
                break;

            case 'load':
                $result = $pdo->deleteSubjectLoad($id);
                $message = $result ? 'Subject load deleted successfully' : 'Failed to delete subject load';
                break;

            case 'subject':
                $result = $pdo->deleteSubject($id);
                $message = $result ? 'Subject deleted successfully' : 'Failed to delete subject';
                break;

            case 'admin':
                $result = $pdo->deleteAdmin($id);
                $message = $result ? 'Admin deleted successfully' : 'Failed to delete admin';
                break;
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
        die(json_encode(ApiResponse::error('Database error: ' . $e->getMessage(), 500)));
    }

} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(ApiResponse::error('System error: ' . $e->getMessage(), 500)));
}
?>
