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
$rows = $pdo->getStudents($q,$page,$limit,$sort,$dir);
$total = $pdo->countStudents($q);
$totalPages = max(1, ceil($total/$limit));
?>
<table class="table">
    <thead>
        <tr>
            <th onclick="setSort('lrn')" class="cursor-pointer">LRN</th>
            <th onclick="setSort('family_name')" class="cursor-pointer">Name</th>
            <th onclick="setSort('department')" class="cursor-pointer">Dept</th>
            <th onclick="setSort('grade_level')" class="cursor-pointer">Grade Level</th>
            <th onclick="setSort('strand')" class="cursor-pointer">Strand</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!$rows): ?>
        <tr>
            <td colspan="6" class="text-center text-muted p-6">No students found</td>
        </tr>
        <?php else: ?>
        <?php foreach($rows as $r): ?>
        <tr>
            <td class="font-medium"><?php echo htmlspecialchars($r['lrn'],ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <a href="javascript:void(0)" onclick="viewStudent(<?php echo (int)$r['id']; ?>)" class="font-medium text-accent">
                    <?php echo htmlspecialchars($r['family_name'].', '.$r['first_name'],ENT_QUOTES,'UTF-8'); ?>
                </a>
            </td>
            <td><span class="badge badge-info"><?php echo htmlspecialchars($r['department'],ENT_QUOTES,'UTF-8'); ?></span></td>
            <td><?php echo htmlspecialchars($r['grade_level'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['strand'] ?? '-',ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <div class="table-actions">
                    <button onclick="viewStudent(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-secondary" title="View">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                    <button onclick="updateStudent(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-accent" title="Edit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteStudent(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-danger" title="Delete">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                    <button onclick="createAccount(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-success" title="Create Account">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </button>
                    <button onclick="editAccount(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-warning" title="Edit Account">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php if($total > 0): ?>
<div class="d-flex justify-between align-center p-4 border-top">
    <div class="text-sm text-muted">
        Showing <?php echo (($page-1)*$limit)+1; ?> to <?php echo min($page*$limit, $total); ?> of <?php echo $total; ?> entries
    </div>
    <div class="pagination">
        <button class="pagination-item <?php echo $page <= 1 ? 'disabled' : ''; ?>" onclick="<?php echo $page > 1 ? 'page--; loadStudents();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <span class="pagination-item active"><?php echo $page; ?></span>
        <span class="text-muted px-2">of <?php echo $totalPages; ?></span>
        <button class="pagination-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" onclick="<?php echo $page < $totalPages ? 'page++; loadStudents();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>
