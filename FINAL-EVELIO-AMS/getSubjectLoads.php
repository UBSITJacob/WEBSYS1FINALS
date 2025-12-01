<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ exit; }
$pdo = new pdoCRUD();
$q = $_GET['q'] ?? '';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = max(1,min(50,(int)($_GET['limit'] ?? 10)));
$sort = $_GET['sort'] ?? 'school_year';
$dir = $_GET['dir'] ?? 'DESC';
$rows = $pdo->getSubjectLoads($q,$page,$limit,$sort,$dir);
$total = $pdo->countSubjectLoads($q);
?>
<table>
    <tr>
        <th onclick="setSort('teacher_name')">Teacher</th>
        <th onclick="setSort('subject_code')">Subject</th>
        <th onclick="setSort('section_name')">Section</th>
        <th onclick="setSort('school_year')">School Year</th>
        <th onclick="setSort('semester')">Semester</th>
        <th>Actions</th>
    </tr>
    <?php if(!$rows){ ?>
        <tr><td colspan="6" align="center">No Records Found...</td></tr>
    <?php } else { foreach($rows as $r){ ?>
        <tr>
            <td><?php echo htmlspecialchars($r['teacher_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['subject_code'].' - '.$r['subject_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['section_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['school_year'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['semester'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><button onclick="deleteLoad(<?php echo (int)$r['id']; ?>)">Delete</button></td>
        </tr>
    <?php }} ?>
</table>
<div class="pager"><button onclick="page>1&&(--page,loadLoads())">Prev</button><span>Page <?php echo (int)$page; ?> of <?php echo max(1, ceil($total/$limit)); ?></span><button onclick="(++page,loadLoads())">Next</button></div>
