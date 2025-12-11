<?php
/**
 * API: Teacher Advisory Class Assignment
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

    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);

    if (!$teacher_id || !$section_id) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid teacher or section ID', 400)));
    }

    $pdo = new pdoCRUD();
    $result = null;

    try {
        $result = $pdo->setTeacherAdviser($teacher_id, $section_id);
        $message = $result ? 'Teacher assigned as adviser successfully' : 'Failed to assign teacher';

        if ($result) {
            http_response_code(200);
            die(json_encode(ApiResponse::success(['teacher_id' => $teacher_id, 'section_id' => $section_id], $message)));
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
