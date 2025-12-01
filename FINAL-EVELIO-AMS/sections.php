<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }

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
                    <div class="search-filter-bar">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search sections..." id="searchInput" oninput="searchInput(this.value)">
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
    let page = 1, limit = 10, q = "", sort = 'grade_level', dir = 'ASC', grade = '', dept = '', confirmCb = null;
    
    function loadSections() {
        let url = 'getSections.php?q=' + encodeURIComponent(q) + '&page=' + page + '&limit=' + limit + '&sort=' + encodeURIComponent(sort) + '&dir=' + encodeURIComponent(dir);
        if(grade) url += '&grade=' + encodeURIComponent(grade);
        if(dept) url += '&dept=' + encodeURIComponent(dept);
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('list').innerHTML = html; });
    }
    
    function searchInput(v) { q = v.trim(); page = 1; loadSections(); }
    function filterGrade(v) { grade = v; page = 1; loadSections(); }
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
        if(!name) { alert('Please enter a section name'); return; }
        fetch('addSection.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'name=' + encodeURIComponent(name) + '&department=' + encodeURIComponent(dept) + '&grade_level=' + encodeURIComponent(grade) + '&strand=' + encodeURIComponent(strand) + '&capacity=' + encodeURIComponent(cap) 
        })
        .then(r => r.json())
        .then(j => { 
            if(j.success) {
                closeAddModal();
                loadSections();
            } else { 
                alert(j.message || 'Failed to add section'); 
            } 
        });
    }
    
    function editSection(id) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = '';
        document.getElementById('edit_capacity').value = '';
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
        const cap = document.getElementById('edit_capacity').value;
        if(!name) { alert('Please enter a section name'); return; }
        fetch('updateSection.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: 'id=' + id + '&name=' + encodeURIComponent(name) + '&capacity=' + encodeURIComponent(cap) 
        })
        .then(r => r.json())
        .then(j => { 
            if(j.success) { 
                closeEditModal();
                loadSections(); 
            } else { 
                alert('Failed to update section'); 
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
    
    function deleteSection(id) {
        openModal('Delete Section', 'Are you sure you want to delete this section? This action cannot be undone.', function() {
            fetch('deleteSection.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'id=' + id })
                .then(r => r.json())
                .then(j => { 
                    closeModal(); 
                    if(j.success) { loadSections(); } else { alert('Failed to delete section'); } 
                });
        });
    }
    
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);
    document.getElementById('addBackdrop').addEventListener('click', closeAddModal);
    document.getElementById('editBackdrop').addEventListener('click', closeEditModal);
    
    window.onload = loadSections;
</script>
<?php include "includes/footer.php"; ?>
