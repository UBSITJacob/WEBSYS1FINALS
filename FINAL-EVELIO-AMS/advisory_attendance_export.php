<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$teacher_id = (int)$acc['person_id'];
$load_id = (int)($_GET['load_id'] ?? 0);
$date = trim($_GET['date'] ?? '');
if($load_id<1 || $date===''){ exit; }
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_'.$load_id.'_'.$date.'.csv"');
$pdo = new PDO('mysql:host=localhost;dbname=evelio_ams_db;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$sql = "SELECT st.lrn, CONCAT(st.family_name, ', ', st.first_name) AS student_name,
               s.code AS subject_code, s.name AS subject_name, sec.name AS section_name,
               COALESCE(a.status,'') AS status
        FROM enrollments e
        JOIN students st ON e.student_id=st.id
        JOIN subject_loads sl ON e.subject_load_id=sl.id
        JOIN subjects s ON sl.subject_id=s.id
        JOIN sections sec ON sl.section_id=sec.id
        LEFT JOIN attendance a ON a.student_id=st.id AND a.subject_load_id=sl.id AND a.date=:d
        WHERE e.subject_load_id=:lid";
$st = $pdo->prepare($sql);
$st->execute([':lid'=>$load_id, ':d'=>$date]);
$out = fopen('php://output','w');
fputcsv($out, ['Subject Code','Subject','Section','Date','LRN','Student','Status']);
while($row = $st->fetch()){
    fputcsv($out, [$row['subject_code'],$row['subject_name'],$row['section_name'],$date,$row['lrn'],$row['student_name'],$row['status']]);
}
fclose($out);
?>

