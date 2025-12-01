<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ exit; }
$pdo = new pdoCRUD();
$q = $_GET['q'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(1, min(50, (int)($_GET['limit'] ?? 10)));
$sort = $_GET['sort'] ?? 'created_at';
$dir = $_GET['dir'] ?? 'DESC';
$rows = $pdo->getApplicants($q,$page,$limit,$sort,$dir);
$total = $pdo->countApplicants($q);
?>
<table>
    <tr>
        <th onclick="setSort('lrn')">LRN</th>
        <th onclick="setSort('family_name')">Name</th>
        <th onclick="setSort('department')">Department</th>
        <th onclick="setSort('grade_level')">Grade</th>
        <th onclick="setSort('strand')">Strand</th>
        <th onclick="setSort('created_at')">Submitted</th>
        <th>Actions</th>
    </tr>
    <?php if(!$rows){ ?>
        <tr><td colspan="7" align="center">No Records Found...</td></tr>
    <?php } else { foreach($rows as $row){ ?>
        <tr>
            <td><?php echo htmlspecialchars($row['lrn'],ENT_QUOTES,'UTF-8'); ?></td>
            <td onclick="viewApplicant(<?php echo (int)$row['id']; ?>)"><?php echo htmlspecialchars($row['family_name'].', '.$row['first_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['department'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['grade_level'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['strand'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['created_at'],ENT_QUOTES,'UTF-8'); ?></td>
            <td class="actions">
                <button onclick="approve(<?php echo (int)$row['id']; ?>)">Approve</button>
                <button onclick="decline(<?php echo (int)$row['id']; ?>)">Decline</button>
            </td>
        </tr>
    <?php }} ?>
</table>
<div class="pager"><button onclick="page>1&&(--page,loadApplicants())">Prev</button><span>Page <?php echo (int)$page; ?> of <?php echo max(1, ceil($total/$limit)); ?></span><button onclick="(++page,loadApplicants())">Next</button></div>
