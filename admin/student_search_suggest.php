<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q']);
$db = new Database();
$conn = $db->getConnection();

try {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT sd.school_id AS SchoolID, sd.lrn AS LRN, COALESCE(u.fullname,'') AS Fullname, COALESCE(u.email,'') AS Email
        FROM student_details sd
        LEFT JOIN users u ON u.id = sd.user_id
        WHERE u.fullname LIKE ? OR sd.school_id LIKE ? OR u.email LIKE ? OR sd.lrn LIKE ?
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
} catch (Exception $e) {
    echo json_encode([]);
} finally {
    $conn->close();
}
