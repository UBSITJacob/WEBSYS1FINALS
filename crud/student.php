<?php
/**
 * API: Student Account Operations (Create/Edit Account)
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

    if (!$id) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid student ID', 400)));
    }

    if (!in_array($action, ['create_account', 'edit_account'])) {
        http_response_code(400);
        die(json_encode(ApiResponse::error('Invalid action', 400)));
    }

    $pdo = new pdoCRUD();
    $result = null;
    $message = '';

    try {
        if ($action === 'create_account') {
            $result = $pdo->createStudentAccount($id);
            $message = $result ? 'Account created successfully' : 'Failed to create account';
        } else {
            $username = Validator::sanitizeString($_POST['username'] ?? '');
            $password = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($password) {
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters');
                }
                if ($password !== $confirmPassword) {
                    throw new Exception('Passwords do not match');
                }
            }

            $updated = true;
            if ($username !== '') {
                $updated = $pdo->updateStudentAccount($id, $username);
            }
            if ($updated && $password) {
                $updated = $pdo->setStudentPassword($id, $password);
            }

            $result = $updated;
            $message = $result ? 'Account updated successfully' : 'Failed to update account';
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
