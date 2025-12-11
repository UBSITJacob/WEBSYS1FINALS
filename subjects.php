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

$page_title = 'Subjects';
$breadcrumb = [ ['title' => 'Subjects', 'active' => true] ];
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
                        <h1 class="page-header-title">Subjects</h1>
                        <p class="page-header-subtitle">Manage the school subject catalog</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Subject
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subject List</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar" style="gap: 12px;">
                        <div class="search-input-wrapper" style="flex: 1; min-width: 220px;">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search subject name or code" id="searchInput" oninput="searchInput(this.value)">
                        </div>
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
<div class="modal modal-lg" id="addModal">
    <div class="modal-header">
        <h4 class="modal-title">Add Subject</h4>
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
                <label class="form-label">Code</label>
                <input type="text" class="form-control" id="s_code" placeholder="e.g., MATH7">
            </div>
            <div class="form-group">
                <label class="form-label required">Name</label>
                <input type="text" class="form-control" id="s_name" placeholder="e.g., Mathematics 7">
            </div>
        </div>
        <div class="form-row form-row-3">
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
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select id="s_sem" class="form-control">
                    <option value="">None</option>
                    <option>First</option>
                    <option>Second</option>
                </select>
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label">Strand (SHS)</label>
                <select id="s_strand" class="form-control">
                    <option value="">None</option>
                    <option>TVL</option>
                    <option>HUMSS</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="s_desc" class="form-control" rows="3" placeholder="Optional description"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addSubject()">Add Subject</button>
    </div>
</div>

<div class="modal-backdrop" id="editBackdrop"></div>
<div class="modal modal-lg" id="editModal">
    <div class="modal-header">
        <h4 class="modal-title">Edit Subject</h4>
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
                <label class="form-label">Code</label>
                <input type="text" class="form-control" id="edit_code">
            </div>
            <div class="form-group">
                <label class="form-label required">Name</label>
                <input type="text" class="form-control" id="edit_name">
            </div>
        </div>
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Department</label>
                <select id="edit_dept" class="form-control">
                    <option value="JHS">Junior High School</option>
                    <option value="SHS">Senior High School</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Grade Level</label>
                <select id="edit_grade" class="form-control">
                    <option>Grade 7</option>
                    <option>Grade 8</option>
                    <option>Grade 9</option>
                    <option>Grade 10</option>
                    <option>Grade 11</option>
                    <option>Grade 12</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select id="edit_sem" class="form-control">
                    <option value="">None</option>
                    <option>First</option>
                    <option>Second</option>
                </select>
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label">Strand (SHS)</label>
                <select id="edit_strand" class="form-control">
                    <option value="">None</option>
                    <option>TVL</option>
                    <option>HUMSS</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="edit_desc" class="form-control" rows="3"></textarea>
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
    let page=1, limit=10, q="", sort='name', dir='ASC', confirmCb=null;
    function loadSubjects(){
        if(demoMode){
            document.getElementById('list').innerHTML = '<div class="demo-notice">Demo Mode - No database connection</div>';
            return;
        }
        const url = 'getSubjects.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir);
        fetch(url).then(r=>r.text()).then(html=>{ document.getElementById('list').innerHTML = html; });
    }
    function searchInput(v){ q=v.trim(); page=1; loadSubjects(); }
    function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadSubjects(); }
    function openAddModal(){ document.getElementById('addBackdrop').classList.add('active'); document.getElementById('addModal').classList.add('active'); }
    function closeAddModal(){ document.getElementById('addBackdrop').classList.remove('active'); document.getElementById('addModal').classList.remove('active'); }
    function openConfirmModal(title, body, cb, btnClass, btnText){
        document.getElementById('m_title').innerText = title; document.getElementById('m_body').innerText = body; confirmCb = cb;
        const btn = document.getElementById('confirmBtn'); btn.className = 'btn ' + (btnClass || 'btn-primary'); btn.innerText = btnText || 'Confirm'; btn.disabled = false;
        document.getElementById('modalBackdrop').classList.add('active'); document.getElementById('confirmModal').classList.add('active');
    }
    function closeConfirmModal(){ document.getElementById('modalBackdrop').classList.remove('active'); document.getElementById('confirmModal').classList.remove('active'); confirmCb=null; }
    function runConfirmAction(){ if(confirmCb) confirmCb(); }

    function addSubject(){
        const code = document.getElementById('s_code').value.trim();
        const name = document.getElementById('s_name').value.trim();
        const dept = document.getElementById('s_dept').value;
        const grade = document.getElementById('s_grade').value;
        const strand = document.getElementById('s_strand').value;
        const sem = document.getElementById('s_sem').value;
        const desc = document.getElementById('s_desc').value.trim();
        if(!name){ showNotification('Please enter a subject name','warning'); return; }
        const btn = document.querySelector('#addModal .modal-footer .btn-primary'); btn.disabled = true; btn.textContent = 'Adding...';
        const body = 'type=subject&code='+encodeURIComponent(code)+'&name='+encodeURIComponent(name)+'&department='+encodeURIComponent(dept)+'&gradelevel='+encodeURIComponent(grade)+'&strand='+encodeURIComponent(strand)+'&semester='+encodeURIComponent(sem)+'&description='+encodeURIComponent(desc);
        fetch('crud/add.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body})
            .then(r=>r.json()).then(j=>{
                btn.disabled=false; btn.textContent='Add Subject';
                if(j.success){ closeAddModal(); showNotification('Subject added successfully','success'); loadSubjects(); } else { showNotification(j.error || j.message || 'Failed to add subject','danger'); }
            }).catch(e=>{ btn.disabled=false; btn.textContent='Add Subject'; showNotification(e.message,'danger'); });
    }

    function editSubject(el){
        const d = el.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('edit_code').value = d.code || '';
        document.getElementById('edit_name').value = d.name || '';
        document.getElementById('edit_dept').value = d.dept || 'JHS';
        document.getElementById('edit_grade').value = d.grade || 'Grade 7';
        document.getElementById('edit_strand').value = d.strand || '';
        document.getElementById('edit_sem').value = d.semester || '';
        document.getElementById('edit_desc').value = d.description || '';
        document.getElementById('editBackdrop').classList.add('active'); document.getElementById('editModal').classList.add('active');
    }
    function closeEditModal(){ document.getElementById('editBackdrop').classList.remove('active'); document.getElementById('editModal').classList.remove('active'); }
    function saveEdit(){
        const id = document.getElementById('edit_id').value;
        const code = document.getElementById('edit_code').value.trim();
        const name = document.getElementById('edit_name').value.trim();
        const dept = document.getElementById('edit_dept').value;
        const grade = document.getElementById('edit_grade').value;
        const strand = document.getElementById('edit_strand').value;
        const sem = document.getElementById('edit_sem').value;
        const desc = document.getElementById('edit_desc').value.trim();
        if(!name){ showNotification('Please enter a subject name','warning'); return; }
        const btn = document.querySelector('#editModal .modal-footer .btn-primary'); btn.disabled = true; btn.textContent = 'Saving...';
        const body = 'type=subject&id='+id+'&code='+encodeURIComponent(code)+'&name='+encodeURIComponent(name)+'&department='+encodeURIComponent(dept)+'&gradelevel='+encodeURIComponent(grade)+'&strand='+encodeURIComponent(strand)+'&semester='+encodeURIComponent(sem)+'&description='+encodeURIComponent(desc);
        fetch('crud/update.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body})
            .then(r=>r.json()).then(j=>{
                btn.disabled=false; btn.textContent='Save Changes';
                if(j.success){ closeEditModal(); showNotification('Subject updated successfully','success'); loadSubjects(); } else { showNotification(j.error || j.message || 'Failed to update subject','danger'); }
            }).catch(e=>{ btn.disabled=false; btn.textContent='Save Changes'; showNotification(e.message,'danger'); });
    }

    function deleteSubject(id){
        openConfirmModal('Delete Subject','Are you sure you want to delete this subject?', function(){
            const btn = document.getElementById('confirmBtn'); btn.disabled = true; btn.textContent = 'Deleting...';
            fetch('crud/delete.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'type=subject&id='+id})
                .then(r=>r.json()).then(j=>{
                    btn.disabled=false; btn.textContent='Delete'; closeConfirmModal();
                    if(j.success){ showNotification('Subject deleted successfully','success'); loadSubjects(); } else { showNotification(j.error || j.message || 'Failed to delete subject','danger'); }
                }).catch(e=>{ btn.disabled=false; btn.textContent='Delete'; closeConfirmModal(); showNotification(e.message,'danger'); });
        }, 'btn-danger', 'Delete');
    }

    document.getElementById('modalBackdrop').addEventListener('click', closeConfirmModal);
    document.getElementById('addBackdrop').addEventListener('click', closeAddModal);
    document.getElementById('editBackdrop').addEventListener('click', closeEditModal);

    window.onload = loadSubjects;
</script>
<?php include "includes/footer.php"; ?>
