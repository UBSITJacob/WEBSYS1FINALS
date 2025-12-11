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

$page_title = 'Students';
$breadcrumb = [
    ['title' => 'Students', 'active' => true]
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
                        <h1 class="page-header-title">Students</h1>
                        <p class="page-header-subtitle">Manage student records and accounts</p>
                    </div>
                    <div class="page-header-actions">
                        <a href="students_add.php" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Student
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student List</h3>
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
                        <select class="form-control filter-dropdown" id="gradeFilter" onchange="filterGrade(this.value)">
                            <option value="">All Grades</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                        <select class="form-control filter-dropdown" id="deptFilter" onchange="filterDept(this.value)">
                            <option value="">All Departments</option>
                            <option value="JHS">Junior High School</option>
                            <option value="SHS">Senior High School</option>
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
        <button class="btn btn-primary" id="confirmBtn" onclick="runConfirmAction()">Confirm</button>
    </div>
</div>

<div class="modal-backdrop" id="accountBackdrop"></div>
<div class="modal" id="accountModal">
    <div class="modal-header">
        <h4 class="modal-title" id="accountTitle">Edit Account</h4>
        <button class="modal-close" onclick="closeAccountModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" id="accountUsername" placeholder="Enter username">
        </div>
        <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" id="accountPassword" placeholder="Enter new password">
        </div>
        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="accountPasswordConfirm" placeholder="Confirm new password">
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAccountModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveAccount()">Save</button>
    </div>
</div>

<script>
    const demoMode = <?php echo $db_available ? 'false' : 'true'; ?>;
    let page = 1, limit = 10, q = "", sort = 'grade_level', dir = 'ASC', grade = '', dept = '', pendingId = 0, confirmCb = null;
    
    function getDemoStudentsHtml() {
        const demoStudents = [
            { id: 1, lrn: '123456789001', name: 'Juan Dela Cruz', grade: 'Grade 7', section: 'Einstein', dept: 'JHS', hasAccount: true },
            { id: 2, lrn: '123456789002', name: 'Maria Santos', grade: 'Grade 8', section: 'Newton', dept: 'JHS', hasAccount: true },
            { id: 3, lrn: '123456789003', name: 'Pedro Reyes', grade: 'Grade 9', section: 'Galileo', dept: 'JHS', hasAccount: false },
            { id: 4, lrn: '123456789004', name: 'Ana Garcia', grade: 'Grade 11', section: 'Einstein', dept: 'SHS', hasAccount: true },
            { id: 5, lrn: '123456789005', name: 'Jose Rizal Jr.', grade: 'Grade 11', section: 'Newton', dept: 'SHS', hasAccount: false },
            { id: 6, lrn: '123456789006', name: 'Gabriela Silang', grade: 'Grade 12', section: 'Galileo', dept: 'SHS', hasAccount: true }
        ];
        
        let html = '<div class="demo-notice" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 16px; border-radius: 8px;"><strong style="color: #92400e;">Demo Mode</strong><span style="color: #92400e;"> - Showing sample student data</span></div>';
        html += '<table class="table"><thead><tr><th>LRN</th><th>Name</th><th>Grade Level</th><th>Section</th><th>Department</th><th>Account</th><th>Actions</th></tr></thead><tbody>';
        
        demoStudents.forEach(s => {
            html += `<tr>
                <td><span class="font-mono">${s.lrn}</span></td>
                <td><strong>${s.name}</strong></td>
                <td>${s.grade}</td>
                <td>${s.section}</td>
                <td><span class="badge badge-${s.dept === 'JHS' ? 'primary' : 'info'}">${s.dept}</span></td>
                <td>${s.hasAccount ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">None</span>'}</td>
                <td>
                    <div class="btn-group" style="flex-wrap: wrap; gap: 4px;">
                        <button class="btn btn-sm btn-outline" style="min-width:90px;" onclick="viewStudent(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle></svg>
                            View
                        </button>
                        <button class="btn btn-sm btn-primary" style="min-width:90px;" onclick="updateStudent(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
                            Update
                        </button>
                        <button class="btn btn-sm btn-danger" style="min-width:90px;" onclick="deleteStudent(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m5 6v6m4-6v6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            Delete
                        </button>
                        ${!s.hasAccount ? `<button class="btn btn-sm btn-success" style="min-width:120px;" onclick="createAccount(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Create Account
                        </button>` : `<button class="btn btn-sm btn-accent" style="min-width:120px;" onclick="editAccount(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
                            Edit Account
                        </button>`}
                    </div>
                </td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        html += '<div class="pagination"><span class="text-muted">Showing 6 demo records</span></div>';
        return html;
    }
    
    function loadStudents() {
        if(demoMode) {
            document.getElementById('list').innerHTML = getDemoStudentsHtml();
            return;
        }
        let url = 'getStudents.php?q=' + encodeURIComponent(q) + '&page=' + page + '&limit=' + limit + '&sort=' + encodeURIComponent(sort) + '&dir=' + encodeURIComponent(dir);
        if(grade) url += '&grade=' + encodeURIComponent(grade);
        if(dept) url += '&dept=' + encodeURIComponent(dept);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; })
            .catch(() => { document.getElementById('list').innerHTML = getDemoStudentsHtml(); });
    }
    
    function searchInput(v) { q = v.trim(); page = 1; loadStudents(); }
    function filterGrade(v) { grade = v; page = 1; loadStudents(); }
    function filterDept(v) { dept = v; page = 1; loadStudents(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadStudents(); }
    
    function viewStudent(id) { window.location.href = 'student_view.php?id=' + id; }
    function updateStudent(id) { window.location.href = 'student_update.php?id=' + id; }
    
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
    
    function deleteStudent(id) {
        openConfirmModal('Delete Student', 'Are you sure you want to delete this student? This action cannot be undone.', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
            fetch('crud/delete.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'type=student&id=' + id })
                .then(r => r.json())
                .then(j => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete';
                    closeConfirmModal(); 
                    if(j.success) { 
                        showNotification('Student deleted successfully', 'success');
                        loadStudents(); 
                    } else { 
                        showNotification(j.error || 'Failed to delete student', 'danger'); 
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
    
    function createAccount(id) {
        openConfirmModal('Create Account', 'Create a school account for this student? They will receive login credentials.', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Creating...';
            
            fetch('crud/student.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=create_account&id=' + id })
                .then(r => r.json())
                .then(j => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Create';
                    closeConfirmModal();
                    
                    if(j.success) { 
                        showNotification('Account created successfully', 'success');
                        loadStudents();
                    } else { 
                        showNotification(j.error || 'Failed to create account', 'danger'); 
                    } 
                })
                .catch(e => { 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Create';
                    closeConfirmModal();
                    showNotification(e.message, 'danger'); 
                });
        }, 'btn-success', 'Create');
    }
    
    function editAccount(id) {
        pendingId = id;
        document.getElementById('accountUsername').value = '';
        document.getElementById('accountBackdrop').classList.add('active');
        document.getElementById('accountModal').classList.add('active');
    }
    
    function closeAccountModal() {
        document.getElementById('accountBackdrop').classList.remove('active');
        document.getElementById('accountModal').classList.remove('active');
        pendingId = 0;
    }
    
    function saveAccount() {
        const username = document.getElementById('accountUsername').value.trim();
        const pw = document.getElementById('accountPassword').value;
        const pc = document.getElementById('accountPasswordConfirm').value;
        if(pw || pc){
            if(pw.length < 8){ showNotification('Password must be at least 8 characters', 'warning'); return; }
            if(pw !== pc){ showNotification('Passwords do not match', 'warning'); return; }
        }
        
        const confirmBtn = document.querySelector('#accountModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        
        const body = 'action=edit_account&id=' + pendingId + '&username=' + encodeURIComponent(username) + '&new_password=' + encodeURIComponent(pw) + '&confirm_password=' + encodeURIComponent(pc);
        fetch('crud/student.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(r => r.json())
            .then(j => { 
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Save';
                closeAccountModal();
                if(j.success){
                    showNotification('Account updated successfully', 'success');
                    loadStudents();
                } else {
                    showNotification(j.error || j.message || 'Failed to update account', 'danger');
                }
            })
            .catch(e => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Save';
                showNotification(e.message, 'danger');
            });
    }
    
    document.getElementById('modalBackdrop').addEventListener('click', closeConfirmModal);
    document.getElementById('accountBackdrop').addEventListener('click', closeAccountModal);
    
    window.onload = loadStudents;
</script>
<?php include "includes/footer.php"; ?>
