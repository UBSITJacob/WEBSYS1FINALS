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
$status = $_GET['status'] ?? '';
$rows = $pdo->getApplicants($q,$page,$limit,$sort,$dir,$status);
$total = $pdo->countApplicants($q,$status);
$totalPages = max(1, ceil($total/$limit));
// housekeeping purge for retention
try{ $pdo->purgeDecidedApplicants(); }catch(Exception $e){}
?>
<table class="table">
    <thead>
        <tr>
            <th onclick="setSort('lrn')" class="cursor-pointer">LRN</th>
            <th onclick="setSort('family_name')" class="cursor-pointer">Name</th>
            <th onclick="setSort('department')" class="cursor-pointer">Department</th>
            <th onclick="setSort('grade_level')" class="cursor-pointer">Grade Level</th>
            <th onclick="setSort('strand')" class="cursor-pointer">Strand</th>
            <th onclick="setSort('created_at')" class="cursor-pointer">Submitted</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!$rows): ?>
        <tr>
            <td colspan="7" class="text-center text-muted p-6">No applicants found</td>
        </tr>
        <?php else: ?>
        <?php foreach($rows as $row): ?>
        <tr>
            <td class="font-medium"><?php echo htmlspecialchars($row['lrn'],ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <a href="javascript:void(0)" onclick="viewApplicant(<?php echo (int)$row['id']; ?>)" class="font-medium text-accent">
                    <?php echo htmlspecialchars($row['family_name'].', '.$row['first_name'],ENT_QUOTES,'UTF-8'); ?>
                </a>
            </td>
            <td><span class="badge badge-info"><?php echo htmlspecialchars($row['department'],ENT_QUOTES,'UTF-8'); ?></span></td>
            <td><?php echo htmlspecialchars($row['grade_level'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['strand'] ?? '-',ENT_QUOTES,'UTF-8'); ?></td>
            <td class="text-muted text-sm"><?php echo htmlspecialchars($row['created_at'],ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <?php $st = $row['status']; $cls = $st==='approved'?'success':($st==='declined'?'danger':'warning'); ?>
                <span class="badge badge-<?php echo $cls; ?>"><?php echo htmlspecialchars($st,ENT_QUOTES,'UTF-8'); ?></span>
            </td>
            <td>
                <div class="table-actions">
                    <button onclick="viewApplicant(<?php echo (int)$row['id']; ?>)" class="btn btn-sm btn-secondary" title="View Details">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                    <?php if(($row['status'] ?? 'pending') === 'pending'): ?>
                    <button onclick="approve(<?php echo (int)$row['id']; ?>)" class="btn btn-sm btn-success" title="Approve">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                    <button onclick="decline(<?php echo (int)$row['id']; ?>)" class="btn btn-sm btn-danger" title="Decline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <?php endif; ?>
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
        <button class="pagination-item <?php echo $page <= 1 ? 'disabled' : ''; ?>" onclick="<?php echo $page > 1 ? 'page--; loadApplicants();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <span class="pagination-item active"><?php echo $page; ?></span>
        <span class="text-muted px-2">of <?php echo $totalPages; ?></span>
        <button class="pagination-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" onclick="<?php echo $page < $totalPages ? 'page++; loadApplicants();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>
