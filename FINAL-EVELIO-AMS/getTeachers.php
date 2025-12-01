<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ exit; }
$pdo = new pdoCRUD();
$q = $_GET['q'] ?? '';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = max(1,min(50,(int)($_GET['limit'] ?? 10)));
$sort = $_GET['sort'] ?? 'full_name';
$dir = $_GET['dir'] ?? 'ASC';
$rows = $pdo->getTeachers($q,$page,$limit,$sort,$dir);
$total = $pdo->countTeachers($q);
?>
<table>
    <tr>
        <th onclick="setSort('faculty_id')">Faculty ID</th>
        <th onclick="setSort('full_name')">Name</th>
        <th onclick="setSort('username')">Username</th>
        <th onclick="setSort('sex')">Sex</th>
        <th onclick="setSort('email')">Email</th>
        <th onclick="setSort('active')">Status</th>
        <th>Actions</th>
    </tr>
    <?php if(!$rows){ ?>
        <tr><td colspan="7" align="center">No Records Found...</td></tr>
    <?php } else { foreach($rows as $r){ ?>
        <tr>
            <td><?php echo htmlspecialchars($r['faculty_id'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['full_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['username'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['sex'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['email'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo $r['active']? 'Active':'Inactive'; ?></td>
            <td>
                <button onclick="editTeacher(<?php echo (int)$r['id']; ?>)">Edit</button>
                <button onclick="deleteTeacher(<?php echo (int)$r['id']; ?>)">Delete</button>
            </td>
        </tr>
    <?php }} ?>
</table>
<div class="pager"><button onclick="page>1&&(--page,loadTeachers())">Prev</button><span>Page <?php echo (int)$page; ?> of <?php echo max(1, ceil($total/$limit)); ?></span><button onclick="(++page,loadTeachers())">Next</button></div>
