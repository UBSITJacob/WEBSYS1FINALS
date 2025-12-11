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
$active = isset($_GET['active']) ? $_GET['active'] : null;
$rows = $pdo->getTeachers($q,$page,$limit,$sort,$dir,$active);
$total = $pdo->countTeachers($q,$active);
$totalPages = max(1, ceil($total/$limit));
?>
<table class="table">
    <thead>
        <tr>
            <th onclick="setSort('faculty_id')" class="cursor-pointer">Faculty ID</th>
            <th onclick="setSort('full_name')" class="cursor-pointer">Name</th>
            <th onclick="setSort('username')" class="cursor-pointer">Username</th>
            <th onclick="setSort('sex')" class="cursor-pointer">Sex</th>
            <th onclick="setSort('email')" class="cursor-pointer">Email</th>
            <th>Advisory Section</th>
            <th onclick="setSort('active')" class="cursor-pointer">Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!$rows): ?>
        <tr>
            <td colspan="8" class="text-center text-muted p-6">No teachers found</td>
        </tr>
        <?php else: ?>
        <?php foreach($rows as $r): ?>
        <tr>
            <td class="font-medium"><?php echo htmlspecialchars($r['faculty_id'],ENT_QUOTES,'UTF-8'); ?></td>
            <td class="font-medium"><?php echo htmlspecialchars($r['full_name'],ENT_QUOTES,'UTF-8'); ?></td>
            <td class="text-muted"><?php echo htmlspecialchars($r['username'],ENT_QUOTES,'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['sex'],ENT_QUOTES,'UTF-8'); ?></td>
            <td class="text-muted"><?php echo htmlspecialchars($r['email'],ENT_QUOTES,'UTF-8'); ?></td>
            <td>
                <?php if(!empty($r['advisory_section_name'])): ?>
                <span class="badge badge-info"><?php echo htmlspecialchars($r['advisory_section_name'],ENT_QUOTES,'UTF-8'); ?> (<?php echo htmlspecialchars($r['advisory_grade_level'] ?? '',ENT_QUOTES,'UTF-8'); ?>)</span>
                <?php else: ?>
                <span class="text-muted">None</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if($r['active']): ?>
                <span class="badge badge-success">Active</span>
                <?php else: ?>
                <span class="badge badge-danger">Inactive</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="table-actions">
                    <button onclick="openAdviserModal(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-primary" title="Assign Advisory Class">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 12H8"></path>
                            <path d="M12 16V8"></path>
                        </svg>
                    </button>
                    <button onclick="editTeacher(this)" class="btn btn-sm btn-accent" title="Edit Teacher"
                            data-id="<?php echo (int)$r['id']; ?>"
                            data-name="<?php echo htmlspecialchars($r['full_name'],ENT_QUOTES,'UTF-8'); ?>"
                            data-user="<?php echo htmlspecialchars($r['username'],ENT_QUOTES,'UTF-8'); ?>"
                            data-email="<?php echo htmlspecialchars($r['email'],ENT_QUOTES,'UTF-8'); ?>"
                            data-sex="<?php echo htmlspecialchars($r['sex'],ENT_QUOTES,'UTF-8'); ?>"
                            data-active="<?php echo (int)$r['active']; ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteTeacher(<?php echo (int)$r['id']; ?>)" class="btn btn-sm btn-danger" title="Delete Teacher">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
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
        <button class="pagination-item <?php echo $page <= 1 ? 'disabled' : ''; ?>" onclick="<?php echo $page > 1 ? 'page--; loadTeachers();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <span class="pagination-item active"><?php echo $page; ?></span>
        <span class="text-muted px-2">of <?php echo $totalPages; ?></span>
        <button class="pagination-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" onclick="<?php echo $page < $totalPages ? 'page++; loadTeachers();' : ''; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>
<?php endif; ?>
