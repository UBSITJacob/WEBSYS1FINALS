<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }

$db_available = false;
try {
    include "pdo_functions.php";
    $pdoC = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$page_title = 'Applicants';
$breadcrumb = [
    ['title' => 'Applicants', 'active' => true]
];
?>
<?php include "includes/header.php"; ?>
<div class="app-layout">
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <main class="main-content">
            <div class="page-header">
                <div class="page-header-row">
                    <div>
                        <h1 class="page-header-title">Applicants</h1>
                        <p class="page-header-subtitle">Review and manage student applications</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Application List</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search by name or LRN..." id="searchInput" oninput="searchInput(this.value)">
                        </div>
                        <select class="form-control filter-dropdown" id="statusFilter" onchange="filterStatus(this.value)">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    <div class="table-container">
                        <div id="list"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>

<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="confirmModal">
    <div class="modal-header">
        <h4 class="modal-title" id="m_title">Confirm Action</h4>
        <button class="modal-close" onclick="closeModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <p id="m_body"></p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeConfirmModal()">Cancel</button>
        <button class="btn btn-primary" id="confirmBtn" onclick="runConfirmAction()">Confirm</button>
    </div>
</div>

<div class="modal-backdrop" id="viewBackdrop"></div>
<div class="modal modal-lg" id="viewModal">
    <div class="modal-header">
        <h4 class="modal-title">Applicant Details</h4>
        <button class="modal-close" onclick="closeViewModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body" id="viewContent">
        <div class="text-center p-6">
            <div class="text-muted">Loading...</div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeViewModal()">Close</button>
    </div>
</div>

<script>
    const demoMode = <?php echo $db_available ? 'false' : 'true'; ?>;
    let page = 1, limit = 10, q = "", sort = 'created_at', dir = 'DESC', status = '', confirmCb = null;
    
    function getDemoApplicantsHtml() {
        const demoApplicants = [
            { id: 1, lrn: '123456789001', name: 'Ana Marie Santos', grade: 'Grade 7', strand: '', status: 'pending', date: '2024-11-28' },
            { id: 2, lrn: '123456789002', name: 'Juan Carlos Cruz', grade: 'Grade 8', strand: '', status: 'pending', date: '2024-11-27' },
            { id: 3, lrn: '123456789003', name: 'Maria Clara Reyes', grade: 'Grade 11', strand: 'STEM', status: 'approved', date: '2024-11-25' },
            { id: 4, lrn: '123456789004', name: 'Jose Rizal Jr.', grade: 'Grade 11', strand: 'HUMSS', status: 'pending', date: '2024-11-24' },
            { id: 5, lrn: '123456789005', name: 'Gabriela Silang', grade: 'Grade 9', strand: '', status: 'declined', date: '2024-11-23' }
        ];
        
        let html = '<div class="demo-notice" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 16px; border-radius: 8px;"><strong style="color: #92400e;">Demo Mode</strong><span style="color: #92400e;"> - Showing sample applicant data</span></div>';
        html += '<table class="table"><thead><tr><th>LRN</th><th>Name</th><th>Grade Level</th><th>Strand</th><th>Status</th><th>Applied</th><th>Actions</th></tr></thead><tbody>';
        
        demoApplicants.forEach(a => {
            const statusClass = a.status === 'pending' ? 'warning' : (a.status === 'approved' ? 'success' : 'danger');
            html += `<tr>
                <td><span class="font-mono">${a.lrn}</span></td>
                <td><strong>${a.name}</strong></td>
                <td>${a.grade}</td>
                <td>${a.strand || '-'}</td>
                <td><span class="badge badge-${statusClass}">${a.status.charAt(0).toUpperCase() + a.status.slice(1)}</span></td>
                <td>${a.date}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline" style="min-width:90px;" onclick="viewApplicant(${a.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle></svg>
                            View
                        </button>
                        ${a.status === 'pending' ? `
                        <button class="btn btn-sm btn-success" style="min-width:90px;" onclick="approveApplicant(${a.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Approve
                        </button>
                        <button class="btn btn-sm btn-danger" style="min-width:90px;" onclick="declineApplicant(${a.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Decline
                        </button>` : ''}
                    </div>
                </td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        html += '<div class="pagination"><span class="text-muted">Showing 5 demo records</span></div>';
        return html;
    }
    
    function loadApplicants() {
        if(demoMode) {
            document.getElementById('list').innerHTML = getDemoApplicantsHtml();
            return;
        }
        let url = "getApplicants.php?q=" + encodeURIComponent(q) + "&page=" + page + "&limit=" + limit + "&sort=" + encodeURIComponent(sort) + "&dir=" + encodeURIComponent(dir);
        if(status) url += "&status=" + encodeURIComponent(status);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; })
            .catch(() => { document.getElementById('list').innerHTML = getDemoApplicantsHtml(); });
    }
    
    function searchInput(v) { q = v.trim(); page = 1; loadApplicants(); }
    function filterStatus(v) { status = v; page = 1; loadApplicants(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadApplicants(); }
    
    function openConfirmModal(title, body, cb, btnClass) {
        document.getElementById('m_title').innerText = title;
        document.getElementById('m_body').innerText = body;
        confirmCb = cb;
        const btn = document.getElementById('confirmBtn');
        btn.className = 'btn ' + (btnClass || 'btn-primary');
        document.getElementById('modalBackdrop').classList.add('active');
        document.getElementById('confirmModal').classList.add('active');
    }
    
    function closeConfirmModal() {
        document.getElementById('modalBackdrop').classList.remove('active');
        document.getElementById('confirmModal').classList.remove('active');
        confirmCb = null;
    }
    
    function runConfirmAction() { if(confirmCb) confirmCb(); }
    
    function approve(id) {
        openConfirmModal('Approve Applicant', 'Are you sure you want to approve this applicant? This will create a student record.', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Approving...';
            fetch('crud/applicant.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=approve&id=' + id })
                .then(async r => { const ct = r.headers.get('content-type') || ''; if(ct.includes('application/json')) return r.json(); const t = await r.text(); throw new Error(t || 'Invalid response'); })
                .then(j => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                    closeConfirmModal(); 
                    if(j.success) { 
                        showNotification('Applicant approved successfully', 'success');
                        loadApplicants(); 
                    } else { 
                        showNotification(j.error || j.message || 'Failed to approve', 'danger'); 
                    } 
                })
                .catch(e => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                    closeConfirmModal(); 
                    showNotification((e && e.message) ? e.message : 'Invalid response', 'danger'); 
                });
        }, 'btn-success');
    }
    
    function decline(id) {
        openConfirmModal('Decline Applicant', 'Are you sure you want to decline this applicant?', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Declining...';
            fetch('crud/applicant.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=decline&id=' + id })
                .then(async r => { const ct = r.headers.get('content-type') || ''; if(ct.includes('application/json')) return r.json(); const t = await r.text(); throw new Error(t || 'Invalid response'); })
                .then(j => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                    closeConfirmModal(); 
                    if(j.success) { 
                        showNotification('Applicant declined successfully', 'success');
                        loadApplicants(); 
                    } else { 
                        showNotification(j.error || j.message || 'Failed to decline', 'danger'); 
                    } 
                })
                .catch(e => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm';
                    closeConfirmModal(); 
                    showNotification((e && e.message) ? e.message : 'Invalid response', 'danger'); 
                });
        }, 'btn-danger');
    }
    
    function viewApplicant(id) {
        document.getElementById('viewBackdrop').classList.add('active');
        document.getElementById('viewModal').classList.add('active');
        document.getElementById('viewContent').innerHTML = '<div class="text-center p-6"><div class="text-muted">Loading...</div></div>';
        
        fetch('applicant_view.php?id=' + id + '&ajax=1')
            .then(r => r.text())
            .then(html => {
                document.getElementById('viewContent').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('viewContent').innerHTML = '<div class="text-center p-6 text-danger">Error loading details</div>';
            });
    }
    
    function closeViewModal() {
        document.getElementById('viewBackdrop').classList.remove('active');
        document.getElementById('viewModal').classList.remove('active');
    }
    
    document.getElementById('modalBackdrop').addEventListener('click', closeConfirmModal);
    document.getElementById('viewBackdrop').addEventListener('click', closeViewModal);
    
    window.onload = loadApplicants;
</script>
<?php include "includes/footer.php"; ?>
