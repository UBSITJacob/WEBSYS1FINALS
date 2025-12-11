<?php
/**
 * API: Applicant Actions (Approve/Decline)
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

    $action = trim($_POST['action'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    $adminId = (int)($_SESSION['account_id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid applicant ID', 400)));
    }

    if (!in_array($action, ['approve', 'decline'])) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid action', 400)));
    }

    $pdo = new pdoCRUD();
    $result = null;
    $message = '';

    try {
        if ($action === 'approve') {
            $result = $pdo->approveApplicant($id, $adminId);
            $message = $result ? 'Applicant approved successfully' : 'Failed to approve applicant';
        } else {
            $result = $pdo->declineApplicant($id, $adminId);
            $message = $result ? 'Applicant declined successfully' : 'Failed to decline applicant';
        }

        if ($result) {
            http_response_code(200);
            die(json_encode(ApiResponse::success(['id' => $id, 'action' => $action], $message)));
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
