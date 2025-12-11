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
<table class="table">
    <thead>
    <tr>
        <th onclick="setSort('teacher_name')">Teacher</th>
        <th onclick="setSort('subject_code')">Subject</th>
        <th onclick="setSort('section_name')">Section</th>
        <th onclick="setSort('school_year')">School Year</th>
        <th onclick="setSort('semester')">Semester</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if(!$rows){ ?>
        <tr><td colspan="6" class="text-center text-muted">No records found</td></tr>
    <?php } else { foreach($rows as $r){ ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r['teacher_name'],ENT_QUOTES,'UTF-8'); ?></strong></td>
            <td><?php echo htmlspecialchars($r['subject_code'].' - '.$r['subject_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['section_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['school_year'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['semester'] ?? '',ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <div class="table-actions">
                    <button onclick="openEditModalFrom(this)"
                            class="btn btn-sm btn-accent" title="Edit Load"
                            data-id="<?php echo (int)$r['id']; ?>"
                            data-teacher_id="<?php echo (int)$r['teacher_id']; ?>"
                            data-subject_id="<?php echo (int)$r['subject_id']; ?>"
                            data-section_id="<?php echo (int)$r['section_id']; ?>"
                            data-school_year="<?php echo htmlspecialchars($r['school_year'],ENT_QUOTES,'UTF-8'); ?>"
                            data-semester="<?php echo htmlspecialchars($r['semester'] ?? '',ENT_QUOTES,'UTF-8'); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteLoad(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-danger" title="Delete Load">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    <?php }} ?>
    </tbody>
</table>
<?php $totalPages = max(1, ceil($total/$limit)); ?>
<?php if($total > 0): ?>
<div class="d-flex justify-between align-center p-4 border-top">
    <div class="text-sm text-muted">
        Showing <?php echo (($page-1)*$limit)+1; ?> to <?php echo min($page*$limit, $total); ?> of <?php echo $total; ?> entries
    </div>
    <div class="pagination">
        <button class="pagination-item <?php echo $page <= 1 ? 'disabled' : ''; ?>" onclick="<?php echo $page > 1 ? 'page--; loadLoads();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <span class="pagination-item active"><?php echo $page; ?></span>
        <span class="text-muted px-2">of <?php echo $totalPages; ?></span>
        <button class="pagination-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" onclick="<?php echo $page < $totalPages ? 'page++; loadLoads();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>
