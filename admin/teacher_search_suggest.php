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

$stmt = $conn->prepare("
    SELECT facultyId AS FacultyID, fullname AS Fullname, email AS Email, username AS Username
    FROM teacher
    WHERE fullname LIKE ? OR facultyId LIKE ? OR email LIKE ? OR username LIKE ?
    ORDER BY fullname ASC
    LIMIT 8
");
$search = "%{$q}%";
$stmt->bind_param("ssss", $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}

echo json_encode($suggestions);

$stmt->close();
$conn->close();
?>
