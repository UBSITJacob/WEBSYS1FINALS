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
    SELECT schoolId AS SchoolID, fullname AS Fullname, email AS Email
    FROM students
    WHERE fullname LIKE ? OR schoolId LIKE ? OR email LIKE ?
    ORDER BY fullname ASC
    LIMIT 8
");
$search = "%{$q}%";
$stmt->bind_param("sss", $search, $search, $search);
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
