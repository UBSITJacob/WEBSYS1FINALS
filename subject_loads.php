<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$teachers = $pdo->query("SELECT id, full_name FROM teachers ORDER BY full_name")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT id, name FROM sections ORDER BY name")->fetchAll();
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
                        <h1 class="page-header-title">Subject Loads</h1>
                        <p class="page-header-subtitle">Manage teacher subject assignments</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Load
                        </button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loads</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search teacher, subject, or section" id="searchInput" oninput="searchInput(this.value)">
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
                        <h4 class="modal-title">Add Subject Load</h4>
                        <button class="modal-close" onclick="closeAddModal()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
    <div class="modal-body">
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Teacher</label>
                <select id="l_teacher" class="form-control">
                    <?php foreach($teachers as $t): ?>
                    <option value="<?php echo (int)$t['id']; ?>"><?php echo htmlspecialchars($t['full_name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Subject</label>
                <select id="l_subject" class="form-control">
                    <?php foreach($subjects as $s): ?>
                    <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Section</label>
                <select id="l_section" class="form-control">
                    <?php foreach($sections as $sec): ?>
                    <option value="<?php echo (int)$sec['id']; ?>"><?php echo htmlspecialchars($sec['name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">School Year</label>
                <input id="l_sy" type="text" class="form-control" placeholder="2025-2026">
            </div>
            <div class="form-group">
                <label class="form-label">Semester (SHS)</label>
                <select id="l_sem" class="form-control"><option value="">None</option><option>First</option><option>Second</option></select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addLoad()">Add Load</button>
    </div>
</div>

<div class="modal-backdrop" id="editBackdrop"></div>
<div class="modal modal-lg" id="editModal">
    <div class="modal-header">
        <h4 class="modal-title">Edit Subject Load</h4>
        <button class="modal-close" onclick="closeEditModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="edit_id">
        <div class="form-row form-row-3">
            <div class="form-group">
                <label class="form-label required">Teacher</label>
                <select id="e_teacher" class="form-control">
                    <?php foreach($teachers as $t): ?>
                    <option value="<?php echo (int)$t['id']; ?>"><?php echo htmlspecialchars($t['full_name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Subject</label>
                <select id="e_subject" class="form-control">
                    <?php foreach($subjects as $s): ?>
                    <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Section</label>
                <select id="e_section" class="form-control">
                    <?php foreach($sections as $sec): ?>
                    <option value="<?php echo (int)$sec['id']; ?>"><?php echo htmlspecialchars($sec['name'],ENT_QUOTES,'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">School Year</label>
                <input id="e_sy" type="text" class="form-control" placeholder="2025-2026">
            </div>
            <div class="form-group">
                <label class="form-label">Semester (SHS)</label>
                <select id="e_sem" class="form-control"><option value="">None</option><option>First</option><option>Second</option></select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
    </div>
</div>

<script>
    let page=1,limit=10,q="",sort='school_year',dir='DESC',confirmCb=null;
    function loadLoads(){
        fetch('getSubjectLoads.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir))
            .then(r=>r.text()).then(html=>{document.getElementById('list').innerHTML=html});
    }
    function searchInput(v){ q=v.trim(); page=1; loadLoads(); }
    function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadLoads(); }
    function openAddModal(){ document.getElementById('addBackdrop').classList.add('active'); document.getElementById('addModal').classList.add('active'); }
    function closeAddModal(){ document.getElementById('addBackdrop').classList.remove('active'); document.getElementById('addModal').classList.remove('active'); }
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
    
    function addLoad(){
        const teacher_id = document.getElementById('l_teacher').value;
        const subject_id = document.getElementById('l_subject').value;
        const section_id = document.getElementById('l_section').value;
        const sy = document.getElementById('l_sy').value.trim();
        const sem = document.getElementById('l_sem').value;
        if(!sy){ showNotification('Enter school year', 'warning'); return; }
        
        const confirmBtn = document.querySelector('#addModal .modal-footer .btn-primary');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Adding...';
        
        fetch('crud/add.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'type=load&teacher_id='+teacher_id+'&subject_id='+subject_id+'&section_id='+section_id+'&school_year='+encodeURIComponent(sy)+'&semester='+encodeURIComponent(sem)
        })
            .then(r=>r.json())
            .then(j=>{ 
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Add Load';
                
                if(j.success){ 
                    closeAddModal(); 
                    showNotification('Subject load added successfully', 'success');
                    loadLoads(); 
                } else { 
                    showNotification(j.error || 'Failed to add load', 'danger'); 
                } 
            })
            .catch(e=>{ 
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Add Load';
                showNotification(e.message, 'danger'); 
            });
    }
    
    function deleteLoad(id){
        openConfirmModal('Delete Load','Are you sure you want to delete this subject load?', function(){
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting...';
            fetch('crud/delete.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'type=load&id='+id})
                .then(r=>r.json()).then(j=>{ 
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete';
                    closeConfirmModal(); 
                    if(j.success){ 
                        showNotification('Subject load deleted successfully', 'success');
                        loadLoads(); 
                    } else { 
                        showNotification(j.error || 'Failed to delete', 'danger'); 
                    } 
                })
                .catch(e=>{ 
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
    
    function openEditModalFrom(el){
        const d = el.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('e_teacher').value = d.teacher_id;
        document.getElementById('e_subject').value = d.subject_id;
        document.getElementById('e_section').value = d.section_id;
        document.getElementById('e_sy').value = d.school_year || '';
        document.getElementById('e_sem').value = d.semester || '';
        document.getElementById('editBackdrop').classList.add('active');
        document.getElementById('editModal').classList.add('active');
    }
    function closeEditModal(){
        document.getElementById('editBackdrop').classList.remove('active');
        document.getElementById('editModal').classList.remove('active');
    }

    function saveEdit(){
        const id = document.getElementById('edit_id').value;
        const teacher_id = document.getElementById('e_teacher').value;
        const subject_id = document.getElementById('e_subject').value;
        const section_id = document.getElementById('e_section').value;
        const sy = document.getElementById('e_sy').value.trim();
        const sem = document.getElementById('e_sem').value;
        if(!sy){ showNotification('Enter school year', 'warning'); return; }
        const btn = document.querySelector('#editModal .modal-footer .btn-primary');
        btn.disabled = true; btn.textContent = 'Saving...';
        fetch('crud/update.php',{
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'type=load&id='+id+'&teacher_id='+teacher_id+'&subject_id='+subject_id+'&section_id='+section_id+'&school_year='+encodeURIComponent(sy)+'&semester='+encodeURIComponent(sem)
        }).then(r=>r.json()).then(j=>{
            btn.disabled = false; btn.textContent = 'Save Changes';
            if(j.success){ closeEditModal(); showNotification('Subject load updated successfully','success'); loadLoads(); }
            else{ showNotification(j.error || 'Failed to update load','danger'); }
        }).catch(e=>{ btn.disabled=false; btn.textContent='Save Changes'; showNotification(e.message,'danger'); });
    }

    window.onload = loadLoads;
</script>
<?php include "includes/footer.php"; ?>
