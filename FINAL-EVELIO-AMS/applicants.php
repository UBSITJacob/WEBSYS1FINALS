<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }

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
        <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button class="btn btn-primary" id="confirmBtn" onclick="confirmModal()">Confirm</button>
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
    let page = 1, limit = 10, q = "", sort = 'created_at', dir = 'DESC', status = '', confirmCb = null;
    
    function loadApplicants() {
        let url = "getApplicants.php?q=" + encodeURIComponent(q) + "&page=" + page + "&limit=" + limit + "&sort=" + encodeURIComponent(sort) + "&dir=" + encodeURIComponent(dir);
        if(status) url += "&status=" + encodeURIComponent(status);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; })
            .catch(() => { document.getElementById('list').innerHTML = '<div class="text-center p-6 text-muted">Error loading data</div>'; });
    }
    
    function searchInput(v) { q = v.trim(); page = 1; loadApplicants(); }
    function filterStatus(v) { status = v; page = 1; loadApplicants(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadApplicants(); }
    
    function openModal(title, body, cb, btnClass) {
        document.getElementById('m_title').innerText = title;
        document.getElementById('m_body').innerText = body;
        confirmCb = cb;
        const btn = document.getElementById('confirmBtn');
        btn.className = 'btn ' + (btnClass || 'btn-primary');
        document.getElementById('modalBackdrop').classList.add('active');
        document.getElementById('confirmModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('modalBackdrop').classList.remove('active');
        document.getElementById('confirmModal').classList.remove('active');
        confirmCb = null;
    }
    
    function confirmModal() { if(confirmCb) confirmCb(); }
    
    function approve(id) {
        openModal('Approve Applicant', 'Are you sure you want to approve this applicant? This will create a student record.', function() {
            fetch('applicant_approve.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'id=' + encodeURIComponent(id) })
                .then(r => r.json())
                .then(j => { 
                    closeModal(); 
                    if(j.success) { loadApplicants(); } else { alert(j.message || 'Failed to approve'); } 
                });
        }, 'btn-success');
    }
    
    function decline(id) {
        openModal('Decline Applicant', 'Are you sure you want to decline this applicant?', function() {
            fetch('applicant_decline.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'id=' + encodeURIComponent(id) })
                .then(r => r.json())
                .then(j => { 
                    closeModal(); 
                    if(j.success) { loadApplicants(); } else { alert(j.message || 'Failed to decline'); } 
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
    
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);
    document.getElementById('viewBackdrop').addEventListener('click', closeViewModal);
    
    window.onload = loadApplicants;
</script>
<?php include "includes/footer.php"; ?>
