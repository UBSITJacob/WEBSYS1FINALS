<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ exit; }
$pdo = new pdoCRUD();
$q = $_GET['q'] ?? '';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = max(1,min(50,(int)($_GET['limit'] ?? 10)));
$sort = $_GET['sort'] ?? 'grade_level';
$dir = $_GET['dir'] ?? 'ASC';
$rows = $pdo->getSections($q,$page,$limit,$sort,$dir);
$total = $pdo->countSections($q);
?>
<table>
    <tr>
        <th onclick="setSort('name')">Name</th>
        <th onclick="setSort('department')">Department</th>
        <th onclick="setSort('grade_level')">Grade</th>
        <th onclick="setSort('strand')">Strand</th>
        <th onclick="setSort('capacity')">Capacity</th>
        <th>Actions</th>
    </tr>
    <?php if(!$rows){ ?>
        <tr><td colspan="6" align="center">No Records Found...</td></tr>
    <?php } else { foreach($rows as $r){ ?>
        <tr>
            <td><?php echo htmlspecialchars($r['name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['department'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['grade_level'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['strand'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo (int)$r['capacity']; ?></td>
            <td>
                <button onclick="editSection(<?php echo (int)$r['id']; ?>)">Edit</button>
                <button onclick="deleteSection(<?php echo (int)$r['id']; ?>)">Delete</button>
            </td>
        </tr>
    <?php }} ?>
</table>
<div class="pager"><button onclick="page>1&&(--page,loadSections())">Prev</button><span>Page <?php echo (int)$page; ?> of <?php echo max(1, ceil($total/$limit)); ?></span><button onclick="(++page,loadSections())">Next</button></div>
