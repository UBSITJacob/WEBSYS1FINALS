<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q']);
$db = new Database();
$conn = $db->conn;

try {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT ad.admin_id AS AdminID, COALESCE(u.fullname,'') AS Fullname, COALESCE(u.email,'') AS Email, COALESCE(u.username,'') AS Username
        FROM admin_details ad
        LEFT JOIN users u ON u.id = ad.user_id
        WHERE u.fullname LIKE ? OR ad.admin_id LIKE ? OR u.email LIKE ? OR u.username LIKE ?
        ORDER BY u.fullname ASC
        LIMIT 8
    ");
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();

    $suggestions = [];
    while ($row = $res->fetch_assoc()) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode([]);
} finally {
    $conn->close();
}
?>
