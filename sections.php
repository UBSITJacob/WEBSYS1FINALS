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

$page_title = 'Sections';
$breadcrumb = [
    ['title' => 'Sections', 'active' => true]
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
                        <h1 class="page-header-title">Sections</h1>
                        <p class="page-header-subtitle">Manage class sections and capacity</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Section
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Section List</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar" style="flex-wrap: wrap; gap: 12px;">
                        <div class="search-input-wrapper" style="flex: 1; min-width: 200px;">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search sections..." id="searchInput" oninput="searchInput(this.value)">
                        </div>
                        <select class="form-control filter-dropdown" id="gradeFilter" onchange="filterGrade(this.value)" style="min-width: 150px; flex: 0 0 auto;">
                            <option value="">All Grades</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                        <select class="form-control filter-dropdown" id="deptFilter" onchange="filterDept(this.value)" style="min-width: 150px; flex: 0 0 auto;">
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
        <button class="btn btn-danger" id="confirmBtn" onclick="runConfirmAction()">Delete</button>
    </div>
</div>

<div class="modal-backdrop" id="addBackdrop"></div>
<div class="modal" id="addModal">
    <div class="modal-header">
        <h4 class="modal-title">Add New Section</h4>
        <button class="modal-close" onclick="closeAddModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="form-label required">Section Name</label>
            <input type="text" class="form-control" id="s_name" placeholder="e.g., Section A, Einstein, Newton">
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">Department</label>
                <select id="s_dept" class="form-control">
                    <option value="JHS">Junior High School</option>
                    <option value="SHS">Senior High School</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Grade Level</label>
                <select id="s_grade" class="form-control">
                    <option>Grade 7</option>
                    <option>Grade 8</option>
                    <option>Grade 9</option>
                    <option>Grade 10</option>
                    <option>Grade 11</option>
                    <option>Grade 12</option>
                </select>
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label">Strand (SHS only)</label>
                <select id="s_strand" class="form-control">
                    <option value="">None</option>
                    <option>HUMSS</option>
                    <option>TVL</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Capacity</label>
                <input type="number" class="form-control" id="s_capacity" value="40" min="1">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addSection()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Section
        </button>
    </div>
</div>

<div class="modal-backdrop" id="editBackdrop"></div>
<div class="modal" id="editModal">
    <div class="modal-header">
        <h4 class="modal-title">Edit Section</h4>
        <button class="modal-close" onclick="closeEditModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="edit_id">
        <div class="form-group">
            <label class="form-label required">Section Name</label>
            <input type="text" class="form-control" id="edit_name">
        </div>
        <div class="form-group">
            <label class="form-label required">Capacity</label>
            <input type="number" class="form-control" id="edit_capacity" min="1">
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
    </div>
</div>

<script>
    const demoMode = <?php echo $db_available ? 'false' : 'true'; ?>;
    let page = 1, limit = 10, q = "", sort = 'grade_level', dir = 'ASC', grade = '', dept = '', confirmCb = null;
    
    function getDemoSectionsHtml() {
        const demoSections = [
            { id: 1, name: 'Einstein', grade: 'Grade 7', dept: 'JHS', strand: '', capacity: 40, enrolled: 35, adviser: 'Maria Santos' },
            { id: 2, name: 'Newton', grade: 'Grade 8', dept: 'JHS', strand: '', capacity: 40, enrolled: 38, adviser: 'Jose Reyes' },
            { id: 3, name: 'Galileo', grade: 'Grade 9', dept: 'JHS', strand: '', capacity: 35, enrolled: 32, adviser: 'Ana Garcia' },
            { id: 4, name: 'Darwin', grade: 'Grade 10', dept: 'JHS', strand: '', capacity: 40, enrolled: 28, adviser: '' },
            { id: 5, name: 'Einstein', grade: 'Grade 11', dept: 'SHS', strand: 'STEM', capacity: 45, enrolled: 42, adviser: 'Lucia Mendoza' },
            { id: 6, name: 'Newton', grade: 'Grade 11', dept: 'SHS', strand: 'HUMSS', capacity: 40, enrolled: 36, adviser: '' },
            { id: 7, name: 'Galileo', grade: 'Grade 12', dept: 'SHS', strand: 'STEM', capacity: 40, enrolled: 30, adviser: 'Pedro Cruz' }
        ];
        
        let html = '<div class="demo-notice" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 16px; border-radius: 8px;"><strong style="color: #92400e;">Demo Mode</strong><span style="color: #92400e;"> - Showing sample section data</span></div>';
        html += '<table class="table"><thead><tr><th>Section Name</th><th>Grade Level</th><th>Department</th><th>Strand</th><th>Capacity</th><th>Enrolled</th><th>Adviser</th><th>Actions</th></tr></thead><tbody>';
        
        demoSections.forEach(s => {
            const usagePercent = Math.round((s.enrolled / s.capacity) * 100);
            const usageColor = usagePercent >= 90 ? 'danger' : (usagePercent >= 70 ? 'warning' : 'success');
            html += `<tr>
                <td><strong>${s.name}</strong></td>
                <td>${s.grade}</td>
                <td><span class="badge badge-${s.dept === 'JHS' ? 'primary' : 'info'}">${s.dept}</span></td>
                <td>${s.strand || '-'}</td>
                <td>${s.capacity}</td>
                <td><span class="badge badge-${usageColor}">${s.enrolled}</span></td>
                <td>${s.adviser || '<span class="text-muted">None</span>'}</td>
                <td>
                    <div class="btn-group" style="flex-wrap: wrap; gap: 4px;">
                        <button class="btn btn-sm btn-primary" style="min-width:90px;" onclick="editSection(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path></svg>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" style="min-width:90px;" onclick="deleteSection(${s.id})">
                            <svg width="16" height="16" style="margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m5 6v6m4-6v6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        html += '</tbody></table>';
        return html;
    }
    function filterDept(v) { dept = v; page = 1; loadSections(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadSections(); }
    
    function openAddModal() {
        document.getElementById('s_name').value = '';
        document.getElementById('s_dept').value = 'JHS';
        document.getElementById('s_grade').value = 'Grade 7';
        document.getElementById('s_strand').value = '';
        document.getElementById('s_capacity').value = '40';
        document.getElementById('addBackdrop').classList.add('active');
        document.getElementById('addModal').classList.add('active');
    }

    function loadSections() {
        if (demoMode) {
            document.getElementById('list').innerHTML = getDemoSectionsHtml();
            return;
        }
        let url = 'getSections.php?q=' + encodeURIComponent(q) + '&page=' + page + '&limit=' + limit + '&sort=' + encodeURIComponent(sort) + '&dir=' + encodeURIComponent(dir);
        if (grade !== '') url += '&grade=' + encodeURIComponent(grade);
        if (dept !== '') url += '&dept=' + encodeURIComponent(dept);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; })
            .catch(() => { document.getElementById('list').innerHTML = getDemoSectionsHtml(); });
    }

    function searchInput(v) { q = v.trim(); page = 1; loadSections(); }
    function filterGrade(v) { grade = v; page = 1; loadSections(); }
    function filterDept(v) { dept = v; page = 1; loadSections(); }
    function setSort(s) { dir = (sort === s && dir === 'ASC') ? 'DESC' : 'ASC'; sort = s; loadSections(); }

    function closeAddModal() {
        document.getElementById('addBackdrop').classList.remove('active');
        document.getElementById('addModal').classList.remove('active');
    }
    
    function addSection() {
        const name = document.getElementById('s_name').value.trim();
        const dept = document.getElementById('s_dept').value;
        const grade = document.getElementById('s_grade').value;
        const strand = document.getElementById('s_strand').value;
        const cap = document.getElementById('s_capacity').value;
        if(!name) { showNotification('Please enter a section name', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#addModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Adding...';
        
        fetch('crud/add.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'type=section&name=' + encodeURIComponent(name) + '&department=' + encodeURIComponent(dept) + '&gradelevel=' + encodeURIComponent(grade) + '&strand=' + encodeURIComponent(strand) + '&capacity=' + encodeURIComponent(cap) 
        })
        .then(r => r.json())
        .then(j => { 
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Add Section';
            
            if(j.success) {
                closeAddModal();
                showNotification('Section added successfully', 'success');
                loadSections();
            } else { 
                showNotification(j.error || j.message || 'Failed to add section', 'danger'); 
            } 
        })
        .catch(e => {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Add Section';
            showNotification(e.message, 'danger');
        });
    }
    
    function editSection(arg) {
        if (typeof arg === 'object' && arg.dataset) {
            document.getElementById('edit_id').value = arg.dataset.id;
            document.getElementById('edit_name').value = arg.dataset.name || '';
            document.getElementById('edit_capacity').value = arg.dataset.capacity || '40';
            document.getElementById('editBackdrop').classList.add('active');
            document.getElementById('editModal').classList.add('active');
            return;
        }
        const id = arg;
        fetch('getSection.php?id=' + id)
            .then(r => r.json())
            .then(j => {
                if(j.error) { showNotification(j.error, 'danger'); return; }
                document.getElementById('edit_id').value = j.id;
                document.getElementById('edit_name').value = j.name;
                document.getElementById('edit_capacity').value = j.capacity;
                document.getElementById('editBackdrop').classList.add('active');
                document.getElementById('editModal').classList.add('active');
            })
            .catch(() => { showNotification('Failed to load section details', 'danger'); });
    }
    
    function closeEditModal() {
        document.getElementById('editBackdrop').classList.remove('active');
        document.getElementById('editModal').classList.remove('active');
    }
    
    function saveEdit() {
        const id = document.getElementById('edit_id').value;
        const name = document.getElementById('edit_name').value.trim();
        const cap = document.getElementById('edit_capacity').value;
        
        if(!name) { showNotification('Please enter a section name', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#editModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        
        fetch('crud/update.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'type=section&id=' + id + '&name=' + encodeURIComponent(name) + '&capacity=' + encodeURIComponent(cap) 
        })
        .then(r => r.json())
        .then(j => { 
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Save Changes';
            
            if(j.success) { 
                closeEditModal();
                showNotification('Section updated successfully', 'success');
                loadSections(); 
            } else { 
                showNotification(j.error || 'Failed to update section', 'danger'); 
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
    
    function deleteSection(id) {
        openConfirmModal('Delete Section', 'Are you sure you want to delete this section? This action cannot be undone.', function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
            fetch('crud/delete.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
                body: 'type=section&id=' + id 
            })
            .then(r => r.json())
            .then(j => { 
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Delete';
                closeConfirmModal();
                
                if(j.success) { 
                    showNotification('Section deleted successfully', 'success');
                    loadSections(); 
                } else { 
                    showNotification(j.error || 'Failed to delete section', 'danger'); 
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
    
    window.onload = loadSections;
</script>
<?php include "includes/footer.php"; ?>
