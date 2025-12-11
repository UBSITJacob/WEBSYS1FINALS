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

$page_title = 'Admins';
$breadcrumb = [ ['title' => 'Admins', 'active' => true] ];
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
                        <h1 class="page-header-title">Admins</h1>
                        <p class="page-header-subtitle">Manage system administrators and access</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Admin
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin List</h3>
                </div>
                <div class="card-body">
                    <div class="search-filter-bar" style="gap:12px;">
                        <div class="search-input-wrapper" style="flex:1; min-width:220px;">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" class="form-control" placeholder="Search name, username, email" id="searchInput" oninput="searchInput(this.value)">
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
<div class="modal" id="addModal">
    <div class="modal-header">
        <h4 class="modal-title">Add Admin</h4>
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
                <input type="text" class="form-control" id="a_fid" placeholder="e.g., ADM-001">
            </div>
            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" class="form-control" id="a_name" placeholder="e.g., Juan Dela Cruz">
            </div>
        </div>
        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label required">Username</label>
                <input type="text" class="form-control" id="a_user" placeholder="e.g., juan.dcrz">
            </div>
            <div class="form-group">
                <label class="form-label required">Email</label>
                <input type="email" class="form-control" id="a_email" placeholder="e.g., admin@example.com">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label required">Sex</label>
            <select id="a_sex" class="form-control">
                <option>Male</option>
                <option>Female</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button class="btn btn-primary" onclick="addAdmin()">Add Admin</button>
    </div>
</div>

<div class="modal-backdrop" id="editBackdrop"></div>
<div class="modal" id="editModal">
    <div class="modal-header">
        <h4 class="modal-title">Edit Admin</h4>
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
        <div class="form-row form-row-2">
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
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
    </div>
</div>

<script>
    const demoMode = <?php echo $db_available ? 'false' : 'true'; ?>;
    let page=1, limit=10, q="", sort='full_name', dir='ASC', confirmCb=null;
    function loadAdmins(){
        if(demoMode){ document.getElementById('list').innerHTML = '<div class="demo-notice">Demo Mode - No database connection</div>'; return; }
        const url = 'getAdmins.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir);
        fetch(url).then(r=>r.text()).then(html=>{ document.getElementById('list').innerHTML = html; });
    }
    function searchInput(v){ q=v.trim(); page=1; loadAdmins(); }
    function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadAdmins(); }
    function openAddModal(){ document.getElementById('addBackdrop').classList.add('active'); document.getElementById('addModal').classList.add('active'); }
    function closeAddModal(){ document.getElementById('addBackdrop').classList.remove('active'); document.getElementById('addModal').classList.remove('active'); }
    function openConfirmModal(title, body, cb, btnClass, btnText){
        document.getElementById('m_title').innerText = title; document.getElementById('m_body').innerText = body; confirmCb = cb;
        const btn = document.getElementById('confirmBtn'); btn.className = 'btn ' + (btnClass || 'btn-primary'); btn.innerText = btnText || 'Confirm'; btn.disabled = false;
        document.getElementById('modalBackdrop').classList.add('active'); document.getElementById('confirmModal').classList.add('active');
    }
    function closeConfirmModal(){ document.getElementById('modalBackdrop').classList.remove('active'); document.getElementById('confirmModal').classList.remove('active'); confirmCb=null; }
    function runConfirmAction(){ if(confirmCb) confirmCb(); }

    function addAdmin(){
        const fid = document.getElementById('a_fid').value.trim();
        const name = document.getElementById('a_name').value.trim();
        const user = document.getElementById('a_user').value.trim();
        const email = document.getElementById('a_email').value.trim();
        const sex = document.getElementById('a_sex').value;
        if(!fid || !name || !user || !email){ showNotification('Please complete all required fields','warning'); return; }
        const btn = document.querySelector('#addModal .modal-footer .btn-primary'); btn.disabled = true; btn.textContent = 'Adding...';
        const body = 'type=admin&faculty_id='+encodeURIComponent(fid)+'&full_name='+encodeURIComponent(name)+'&username='+encodeURIComponent(user)+'&email='+encodeURIComponent(email)+'&sex='+encodeURIComponent(sex);
        fetch('crud/add.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body})
            .then(r=>r.json()).then(j=>{ btn.disabled=false; btn.textContent='Add Admin'; if(j.success){ closeAddModal(); showNotification('Admin added successfully','success'); loadAdmins(); } else { showNotification(j.error || j.message || 'Failed to add admin','danger'); } })
            .catch(e=>{ btn.disabled=false; btn.textContent='Add Admin'; showNotification(e.message,'danger'); });
    }

    function editAdmin(el){
        const d = el.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('edit_name').value = d.name || '';
        document.getElementById('edit_user').value = d.user || '';
        document.getElementById('edit_email').value = d.email || '';
        document.getElementById('edit_sex').value = d.sex || 'Male';
        document.getElementById('editBackdrop').classList.add('active'); document.getElementById('editModal').classList.add('active');
    }
    function closeEditModal(){ document.getElementById('editBackdrop').classList.remove('active'); document.getElementById('editModal').classList.remove('active'); }
    function saveEdit(){
        const id = document.getElementById('edit_id').value;
        const name = document.getElementById('edit_name').value.trim();
        const user = document.getElementById('edit_user').value.trim();
        const email = document.getElementById('edit_email').value.trim();
        const sex = document.getElementById('edit_sex').value;
        if(!name || !user || !email){ showNotification('Please complete all required fields','warning'); return; }
        const btn = document.querySelector('#editModal .modal-footer .btn-primary'); btn.disabled = true; btn.textContent = 'Saving...';
        const body = 'type=admin&id='+id+'&full_name='+encodeURIComponent(name)+'&username='+encodeURIComponent(user)+'&email='+encodeURIComponent(email)+'&sex='+encodeURIComponent(sex);
        fetch('crud/update.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body})
            .then(r=>r.json()).then(j=>{ btn.disabled=false; btn.textContent='Save Changes'; if(j.success){ closeEditModal(); showNotification('Admin updated successfully','success'); loadAdmins(); } else { showNotification(j.error || j.message || 'Failed to update admin','danger'); } })
            .catch(e=>{ btn.disabled=false; btn.textContent='Save Changes'; showNotification(e.message,'danger'); });
    }

    function deleteAdmin(id){
        openConfirmModal('Delete Admin','Are you sure you want to delete this admin?', function(){
            const btn = document.getElementById('confirmBtn'); btn.disabled = true; btn.textContent = 'Deleting...';
            fetch('crud/delete.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'type=admin&id='+id})
                .then(r=>r.json()).then(j=>{ btn.disabled=false; btn.textContent='Delete'; closeConfirmModal(); if(j.success){ showNotification('Admin deleted successfully','success'); loadAdmins(); } else { showNotification(j.error || j.message || 'Failed to delete admin','danger'); } })
                .catch(e=>{ btn.disabled=false; btn.textContent='Delete'; closeConfirmModal(); showNotification(e.message,'danger'); });
        }, 'btn-danger', 'Delete');
    }

    document.getElementById('modalBackdrop').addEventListener('click', closeConfirmModal);
    document.getElementById('addBackdrop').addEventListener('click', closeAddModal);
    document.getElementById('editBackdrop').addEventListener('click', closeEditModal);

    window.onload = loadAdmins;
</script>
<?php include "includes/footer.php"; ?>

