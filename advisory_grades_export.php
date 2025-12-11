<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$teacher_id = (int)$acc['person_id'];
$load_id = (int)($_GET['load_id'] ?? 0);
if($load_id<1){ exit; }
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="grades_'.intval($load_id).'.csv"');
$pdo = new PDO('mysql:host=localhost;dbname=evelio_ams_db;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$sql = "SELECT st.lrn, CONCAT(st.family_name, ', ', st.first_name) AS student_name,
               s.code AS subject_code, s.name AS subject_name, sec.name AS section_name,
               COALESCE(g.grade,'') AS grade
        FROM enrollments e
        JOIN students st ON e.student_id=st.id
        JOIN subject_loads sl ON e.subject_load_id=sl.id
        JOIN subjects s ON sl.subject_id=s.id
        JOIN sections sec ON sl.section_id=sec.id
        LEFT JOIN grades g ON g.enrollment_id=e.id
        WHERE e.subject_load_id=:lid";
$st = $pdo->prepare($sql);
$st->execute([':lid'=>$load_id]);
$out = fopen('php://output','w');
fputcsv($out, ['Subject Code','Subject','Section','LRN','Student','Grade']);
while($row = $st->fetch()){
    fputcsv($out, [$row['subject_code'],$row['subject_name'],$row['section_name'],$row['lrn'],$row['student_name'],$row['grade']]);
}
fclose($out);
?>
