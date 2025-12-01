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

$page_title = 'Teachers';
$breadcrumb = [
    ['title' => 'Teachers', 'active' => true]
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
                        <h1 class="page-header-title">Teachers</h1>
                        <p class="page-header-subtitle">Manage teacher records and assignments</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Teacher
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teacher List</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search teachers..." id="searchInput" oninput="searchInput(this.value)">
                        </div>
                        <select class="form-control filter-dropdown" id="statusFilter" onchange="filterStatus(this.value)">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
        <button class="btn btn-danger" id="confirmBtn" onclick="confirmModal()">Delete</button>
    </div>
</div>

<div class="modal-backdrop" id="addBackdrop"></div>
<div class="modal modal-lg" id="addModal">
    <div class="modal-header">
        <h4 class="modal-title">Add New Teacher</h4>
        <button class="modal-close" onclick="closeAddModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">Faculty ID</label>
                <input type="text" class="form-control" id="t_fid" placeholder="Enter faculty ID">
            </div>
            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" class="form-control" id="t_name" placeholder="Enter full name">
            </div>
        </div>
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Username</label>
                <input type="text" class="form-control" id="t_user" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label class="form-label required">Sex</label>
                <select id="t_sex" class="form-control">
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Email</label>
                <input type="email" class="form-control" id="t_email" placeholder="Enter email address">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addTeacher()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Teacher
        </button>
    </div>
</div>

<div class="modal-backdrop" id="editBackdrop"></div>
<div class="modal modal-lg" id="editModal">
    <div class="modal-header">
        <h4 class="modal-title">Edit Teacher</h4>
        <button class="modal-close" onclick="closeEditModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="edit_id">
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" class="form-control" id="edit_name">
            </div>
            <div class="form-group">
                <label class="form-label required">Username</label>
                <input type="text" class="form-control" id="edit_user">
            </div>
        </div>
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Email</label>
                <input type="email" class="form-control" id="edit_email">
            </div>
            <div class="form-group">
                <label class="form-label required">Sex</label>
                <select id="edit_sex" class="form-control">
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Status</label>
                <select id="edit_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
    </div>
</div>

<script>
    const demoMode = <?php echo $db_available ? 'false' : 'true'; ?>;
    let page = 1, limit = 10, q = "", sort = 'full_name', dir = 'ASC', status = '', confirmCb = null;
    
    function getDemoTeachersHtml() {
        const demoTeachers = [
            { id: 1, fid: 'FAC-2024-001', name: 'Maria Santos', email: 'msantos@school.edu', sex: 'Female', active: true, advisory: 'Einstein (Grade 7)' },
            { id: 2, fid: 'FAC-2024-002', name: 'Jose Reyes', email: 'jreyes@school.edu', sex: 'Male', active: true, advisory: 'Newton (Grade 8)' },
            { id: 3, fid: 'FAC-2024-003', name: 'Ana Garcia', email: 'agarcia@school.edu', sex: 'Female', active: true, advisory: '' },
            { id: 4, fid: 'FAC-2024-004', name: 'Pedro Cruz', email: 'pcruz@school.edu', sex: 'Male', active: false, advisory: '' },
            { id: 5, fid: 'FAC-2024-005', name: 'Lucia Mendoza', email: 'lmendoza@school.edu', sex: 'Female', active: true, advisory: 'Galileo (Grade 11)' }
        ];
        
        let html = '<div class="demo-notice" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 16px; border-radius: 8px;"><strong style="color: #92400e;">Demo Mode</strong><span style="color: #92400e;"> - Showing sample teacher data</span></div>';
        html += '<table class="table"><thead><tr><th>Faculty ID</th><th>Name</th><th>Email</th><th>Sex</th><th>Advisory</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        
        demoTeachers.forEach(t => {
            html += `<tr>
                <td><span class="font-mono">${t.fid}</span></td>
                <td><strong>${t.name}</strong></td>
                <td>${t.email}</td>
                <td>${t.sex}</td>
                <td>${t.advisory || '<span class="text-muted">None</span>'}</td>
                <td><span class="badge badge-${t.active ? 'success' : 'secondary'}">${t.active ? 'Active' : 'Inactive'}</span></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="alert('Demo mode - Edit disabled')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="alert('Demo mode - Delete disabled')">Delete</button>
                    </div>
                </td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        html += '<div class="pagination"><span class="text-muted">Showing 5 demo records</span></div>';
        return html;
    }
    
    function loadTeachers() {
        if(demoMode) {
            document.getElementById('list').innerHTML = getDemoTeachersHtml();
            return;
        }
        let url = 'getTeachers.php?q=' + encodeURIComponent(q) + '&page=' + page + '&limit=' + limit + '&sort=' + encodeURIComponent(sort) + '&dir=' + encodeURIComponent(dir);
        if(status !== '') url += '&active=' + encodeURIComponent(status);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; })
            .catch(() => { document.getElementById('list').innerHTML = getDemoTeachersHtml(); });
    }
    
    function searchInput(v) { q = v.trim(); page = 1; loadTeachers(); }
    function filterStatus(v) { status = v; page = 1; loadTeachers(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadTeachers(); }
    
    function openAddModal() {
        document.getElementById('t_fid').value = '';
        document.getElementById('t_name').value = '';
        document.getElementById('t_user').value = '';
        document.getElementById('t_sex').value = 'Male';
        document.getElementById('t_email').value = '';
        document.getElementById('addBackdrop').classList.add('active');
        document.getElementById('addModal').classList.add('active');
    }
    
    function closeAddModal() {
        document.getElementById('addBackdrop').classList.remove('active');
        document.getElementById('addModal').classList.remove('active');
    }
    
    function addTeacher() {
        const fid = document.getElementById('t_fid').value.trim();
        const name = document.getElementById('t_name').value.trim();
        const user = document.getElementById('t_user').value.trim();
        const sex = document.getElementById('t_sex').value;
        const email = document.getElementById('t_email').value.trim();
        if(!fid || !name || !user || !email) { alert('Please complete all required fields'); return; }
        fetch('addTeacher.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'faculty_id=' + encodeURIComponent(fid) + '&full_name=' + encodeURIComponent(name) + '&username=' + encodeURIComponent(user) + '&sex=' + encodeURIComponent(sex) + '&email=' + encodeURIComponent(email) 
        })
        .then(r => r.json())
        .then(j => { 
            if(j.success) {
                closeAddModal();
                loadTeachers();
            } else { 
                alert(j.message || 'Failed to add teacher'); 
            } 
        });
    }
    
    function editTeacher(id) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = '';
        document.getElementById('edit_user').value = '';
        document.getElementById('edit_email').value = '';
        document.getElementById('edit_sex').value = 'Male';
        document.getElementById('edit_active').value = '1';
        document.getElementById('editBackdrop').classList.add('active');
        document.getElementById('editModal').classList.add('active');
    }
    
    function closeEditModal() {
        document.getElementById('editBackdrop').classList.remove('active');
        document.getElementById('editModal').classList.remove('active');
    }
    
    function saveEdit() {
        const id = document.getElementById('edit_id').value;
        const name = document.getElementById('edit_name').value.trim();
        const user = document.getElementById('edit_user').value.trim();
        const email = document.getElementById('edit_email').value.trim();
        const sex = document.getElementById('edit_sex').value;
        const active = document.getElementById('edit_active').value;
        if(!name || !user || !email) { alert('Please complete all required fields'); return; }
        fetch('updateTeacher.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'id=' + id + '&full_name=' + encodeURIComponent(name) + '&username=' + encodeURIComponent(user) + '&email=' + encodeURIComponent(email) + '&sex=' + encodeURIComponent(sex) + '&active=' + active 
        })
        .then(r => r.json())
        .then(j => { 
            if(j.success) { 
                closeEditModal();
                loadTeachers(); 
            } else { 
                alert('Failed to update teacher'); 
            } 
        });
    }
    
    function openModal(title, body, cb) {
        document.getElementById('m_title').innerText = title;
        document.getElementById('m_body').innerText = body;
        confirmCb = cb;
        document.getElementById('modalBackdrop').classList.add('active');
        document.getElementById('confirmModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('modalBackdrop').classList.remove('active');
        document.getElementById('confirmModal').classList.remove('active');
        confirmCb = null;
    }
    
    function confirmModal() { if(confirmCb) confirmCb(); }
    
    function deleteTeacher(id) {
        openModal('Delete Teacher', 'Are you sure you want to delete this teacher? This action cannot be undone.', function() {
            fetch('deleteTeacher.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'id=' + id })
                .then(r => r.json())
                .then(j => { 
                    closeModal(); 
                    if(j.success) { loadTeachers(); } else { alert('Failed to delete teacher'); } 
                });
        });
    }
    
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);
    document.getElementById('addBackdrop').addEventListener('click', closeAddModal);
    document.getElementById('editBackdrop').addEventListener('click', closeEditModal);
    
    window.onload = loadTeachers;
</script>
<?php include "includes/footer.php"; ?>
