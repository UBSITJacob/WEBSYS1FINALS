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
        <button class="modal-close" onclick="closeConfirmModal()">
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
        <button class="btn btn-danger" id="confirmBtn" onclick="runConfirmAction()">Delete</button>
    </div>
</div>

<div class="modal-backdrop" id="addBackdrop"></div>
<div class="modal" id="addModal">
    <div class="modal-header">
        <h4 class="modal-title">Add Teacher</h4>
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
                <input type="text" class="form-control" id="t_fid">
            </div>
            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" class="form-control" id="t_name">
            </div>
        </div>
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Username</label>
                <input type="text" class="form-control" id="t_user">
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
                <input type="email" class="form-control" id="t_email">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addTeacher()">Add Teacher</button>
    </div>
</div>

<div class="modal-backdrop" id="adviserBackdrop"></div>
<div class="modal" id="adviserModal">
    <div class="modal-header">
        <h4 class="modal-title">Assign Advisory Section</h4>
        <button class="modal-close" onclick="closeAdviserModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="adv_teacher_id">
        <div class="form-group">
            <label class="form-label">Section</label>
            <select id="adv_section" class="form-control"></select>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAdviserModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveAdviserAssignment()">Assign</button>
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
                        <button class="btn btn-sm btn-accent" style="min-width:120px;" onclick="openAdviserModal(${t.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
                            Assign Advisory
                        </button>
                        <button class="btn btn-sm btn-primary" style="min-width:90px;" onclick="editTeacher(this)" data-id="${t.id}" data-name="${t.name}" data-user="demo.user" data-email="${t.email}" data-sex="${t.sex}" data-active="${t.active ? 1 : 0}">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" style="min-width:90px;" onclick="deleteTeacher(${t.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m5 6v6m4-6v6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            Delete
                        </button>
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
        if(!fid || !name || !user || !email) { showNotification('Please complete all required fields', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#addModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Adding...';
        
        fetch('crud/add.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'type=teacher&faculty_id=' + encodeURIComponent(fid) + '&full_name=' + encodeURIComponent(name) + '&username=' + encodeURIComponent(user) + '&sex=' + encodeURIComponent(sex) + '&email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(j => { 
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Add Teacher';
            
            if(j.success) {
                closeAddModal();
                showNotification('Teacher added successfully', 'success');
                loadTeachers();
            } else { 
                showNotification(j.error || j.message || 'Failed to add teacher', 'danger'); 
            } 
        })
        .catch(e => {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Add Teacher';
            showNotification(e.message, 'danger');
        });
    }
    
    function editTeacher(arg) {
        if (typeof arg === 'object' && arg.dataset) {
            document.getElementById('edit_id').value = arg.dataset.id;
            document.getElementById('edit_name').value = arg.dataset.name || '';
            document.getElementById('edit_user').value = arg.dataset.user || '';
            document.getElementById('edit_email').value = arg.dataset.email || '';
            document.getElementById('edit_sex').value = arg.dataset.sex || 'Male';
            document.getElementById('edit_active').value = arg.dataset.active || '1';
            document.getElementById('editBackdrop').classList.add('active');
            document.getElementById('editModal').classList.add('active');
            return;
        }
        const id = arg;
        fetch('getTeacher.php?id=' + id)
            .then(r => r.json())
            .then(j => {
                if(j.error) { showNotification(j.error, 'danger'); return; }
                document.getElementById('edit_id').value = j.id;
                document.getElementById('edit_name').value = j.full_name;
                document.getElementById('edit_user').value = j.username;
                document.getElementById('edit_email').value = j.email;
                document.getElementById('edit_sex').value = j.sex;
                document.getElementById('edit_active').value = j.active;
                document.getElementById('editBackdrop').classList.add('active');
                document.getElementById('editModal').classList.add('active');
            })
            .catch(() => { showNotification('Failed to load teacher details', 'danger'); });
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
        
        if(!name || !user || !email) { showNotification('Please complete all required fields', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#editModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        
        fetch('crud/update.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'type=teacher&id=' + id + '&full_name=' + encodeURIComponent(name) + '&username=' + encodeURIComponent(user) + '&email=' + encodeURIComponent(email) + '&sex=' + encodeURIComponent(sex) + '&active=' + encodeURIComponent(active)
        })
        .then(r => r.json())
        .then(j => { 
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Save Changes';
            
            if(j.success) { 
                closeEditModal();
                showNotification('Teacher updated successfully', 'success');
                loadTeachers(); 
            } else { 
                showNotification(j.error || 'Failed to update teacher', 'danger'); 
            } 
        })
        .catch(e => {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Save Changes';
            showNotification(e.message, 'danger');
        });
    }
    
    function openConfirmModal(title, body, cb, btnClass, btnText) {
        document.getElementById('m_title').innerText = title;
        document.getElementById('m_body').innerText = body;
        confirmCb = cb;
        const btn = document.getElementById('confirmBtn');
        btn.className = 'btn ' + (btnClass || 'btn-primary');
        btn.innerText = btnText || 'Confirm';
        btn.disabled = false;
        document.getElementById('modalBackdrop').classList.add('active');
        document.getElementById('confirmModal').classList.add('active');
    }
    
    function closeConfirmModal() {
        document.getElementById('modalBackdrop').classList.remove('active');
        document.getElementById('confirmModal').classList.remove('active');
        confirmCb = null;
    }
    
    function runConfirmAction() { if(confirmCb) confirmCb(); }
    
    function deleteTeacher(id) {
        openConfirmModal('Delete Teacher', 'Are you sure you want to delete this teacher? This action cannot be undone.', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
            fetch('crud/delete.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'type=teacher&id=' + id })
                .then(r => r.json())
                .then(j => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete';
                    closeConfirmModal(); 
                    if(j.success) { 
                        showNotification('Teacher deleted successfully', 'success');
                        loadTeachers(); 
                    } else { 
                        showNotification(j.error || 'Failed to delete teacher', 'danger'); 
                    } 
                })
                .catch(e => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete';
                    closeConfirmModal(); 
                    showNotification(e.message, 'danger'); 
                });
        }, 'btn-danger', 'Delete');
    }
    
    document.getElementById('modalBackdrop').addEventListener('click', closeConfirmModal);
    document.getElementById('addBackdrop').addEventListener('click', closeAddModal);
    document.getElementById('editBackdrop').addEventListener('click', closeEditModal);
    document.getElementById('adviserBackdrop').addEventListener('click', closeAdviserModal);
    
    function openAdviserModal(id){
        document.getElementById('adv_teacher_id').value = id;
        document.getElementById('adv_section').innerHTML = '<option>Loading...</option>';
        document.getElementById('adviserBackdrop').classList.add('active');
        document.getElementById('adviserModal').classList.add('active');
        fetch('listSections.php')
            .then(r=>r.json())
            .then(j=>{
                const sel = document.getElementById('adv_section');
                sel.innerHTML = '';
                j.forEach(s=>{ const opt=document.createElement('option'); opt.value=s.id; opt.textContent=s.name; sel.appendChild(opt); });
            })
            .catch(()=>{ document.getElementById('adv_section').innerHTML = '<option>Error</option>'; });
    }
    function closeAdviserModal(){
        document.getElementById('adviserBackdrop').classList.remove('active');
        document.getElementById('adviserModal').classList.remove('active');
        document.getElementById('adv_teacher_id').value='';
    }
    function saveAdviserAssignment(){
        const tid = document.getElementById('adv_teacher_id').value;
        const sec = document.getElementById('adv_section').value;
        if(!tid || !sec){ showNotification('Select a section', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#adviserModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Assigning...';
        
        fetch('crud/adviser.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'teacher_id='+tid+'&section_id='+sec
        })
            .then(r=>r.json())
            .then(j=>{ 
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Assign';
                
                if(j.success){ 
                    closeAdviserModal(); 
                    showNotification('Teacher assigned as adviser successfully', 'success');
                    loadTeachers(); 
                } else { 
                    showNotification(j.error || 'Failed to assign', 'danger'); 
                } 
            })
            .catch(e => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Assign';
                showNotification(e.message, 'danger');
            });
    }
    
    window.onload = loadTeachers;
</script>
<?php include "includes/footer.php"; ?>
