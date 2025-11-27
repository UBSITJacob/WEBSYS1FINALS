<?php
require_once "../includes/oop_functions.php";

$db = new Database();
$conn = $db->getConnection();

// JOIN users + teacher_details, alias fields to match frontend expectations
$sql = "
    SELECT 
        td.user_id AS id,
        td.faculty_id AS facultyId,
        td.gender,
        td.status,
        COALESCE(u.fullname, '') AS fullname,
        COALESCE(u.email, '') AS email,
        COALESCE(u.username, '') AS username
    FROM teacher_details td
    LEFT JOIN users u ON u.id = td.user_id
    ORDER BY td.user_id DESC
";

$result = $conn->query($sql);
?>

<h2>Manage Teachers</h2>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <div style="position: relative; width: 100%; max-width: 420px;">
        <input type="text" id="searchInput" placeholder="Search by name, Faculty ID, username, or email..." autocomplete="off"
               style="padding:10px;width:100%;border:1px solid #ccc;border-radius:8px;outline:none;font-size:15px;">
        <div id="suggestions"></div>
    </div>
</div>

<table id="teachersTable" border="1" cellspacing="0" cellpadding="8"
       style="width:100%; background:#fff; border-radius:8px;">
    <thead style="background:#007bff; color:white;">
        <tr>
            <th>Faculty ID</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Username</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="teacherTbody">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr id="teacherRow_<?= (int)$row['id'] ?>" style="text-align:center;">
                    <td><?= htmlspecialchars($row['facultyId'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['gender'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td>
                        <button class="assignBtn" 
                                data-id="<?= (int)$row['id'] ?>" 
                                data-name="<?= htmlspecialchars($row['fullname']) ?>"
                                style="background:#20c997;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-right:6px;">
                            Assign Subjects
                        </button>
                        <button class="assignAdviserBtn" data-id="<?= (int)$row['id'] ?>" data-name="<?= htmlspecialchars($row['fullname']) ?>"
                            style="background:#17a2b8;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-right:6px;">
                            Assign Adviser
                        </button>
                        <button class="editBtn" data-id="<?= (int)$row['id'] ?>"
                            style="background:#007bff;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;">
                            Edit
                        </button>
                        <button class="deleteBtn" data-id="<?= (int)$row['id'] ?>"
                            style="background:#dc3545;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-left:6px;">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">No teachers found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="assignmentSection" class="card" style="display:none; margin-top: 30px;">
    <h3 id="assignmentHeader">Assign Subjects to [Teacher Name]</h3>
    <p>Use the form below to add a new subject load or remove an existing assignment.</p>
    
    <input type="hidden" id="assignment_teacher_id">

    <div style="display: flex; gap: 20px; margin-bottom: 20px; align-items: flex-end;">
        <div style="flex: 1;">
            <label for="assignment_select">Select Section and Subject to Assign:</label>
            <select id="assignment_select" style="width: 100%; padding: 10px; border-radius: 5px;">
                <option value="">-- Loading Assignments... --</option>
            </select>
        </div>
        <button id="addAssignmentBtn" style="width: 150px; padding: 10px;">
            + Add Assignment
        </button>
    </div>

    <h4>Current Assignments</h4>
    <table id="currentAssignmentsTable" border="1" cellspacing="0" cellpadding="8" style="width:100%; font-size: 0.9em;">
        <thead style="background:#e9ecef;">
            <tr>
                <th>Section</th>
                <th>Subject</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="assignmentsTbody">
            <tr><td colspan="3" style="text-align:center;">Select a teacher to load assignments.</td></tr>
        </tbody>
    </table>
    
    <div style="margin-top: 15px; text-align: right;">
        <button onclick="hideAssignmentSection()" style="background:#6c757d;">Close Assignment</button>
    </div>
</div>
<div id="editTeacherModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3>Edit Teacher Information</h3>
    <form id="editTeacherForm">
      <input type="hidden" name="id" id="edit_id">

      <label>Faculty ID</label>
      <input type="text" name="facultyId" id="edit_facultyId" required>

      <label>Full Name</label>
      <input type="text" name="fullname" id="edit_fullname" required>

      <label>Username</label>
      <input type="text" name="username" id="edit_username" required>

      <label>Email</label>
      <input type="email" name="email" id="edit_email" required>

      <label>Gender</label>
      <select name="gender" id="edit_gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>

      <label>Status</label>
      <select name="status" id="edit_status" required>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
        <option value="Leave">Leave</option>
      </select>

      <div style="margin-top:15px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" id="resetPasswordBtn" style="background:#ffc107;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">
          Reset Password
        </button>
        <div>
          <button type="submit" style="background:#28a745;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">Save</button>
          <button type="button" onclick="closeEditModal()" style="background:#6c757d;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;margin-left:5px;">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Adviser modal -->
<div id="adviserModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3>Assign Adviser</h3>
        <form id="adviserForm">
            <input type="hidden" name="teacher_id" id="adviser_teacher_id">

            <label for="adviser_select">Select Section</label>
            <select id="adviser_select" name="section_id" style="width:100%; padding:8px; margin-bottom:12px;">
                <option value="">-- Loading sections... --</option>
            </select>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <button type="button" id="removeAdviserBtn" style="background:#dc3545;color:white;border:none;padding:8px 12px;border-radius:5px;cursor:pointer;">Remove Adviser</button>
                <div>
                    <button type="submit" style="background:#28a745;color:white;border:none;padding:8px 12px;border-radius:5px;cursor:pointer;">Save</button>
                    <button type="button" onclick="closeAdviserModal()" style="background:#6c757d;color:white;border:none;padding:8px 12px;border-radius:5px;cursor:pointer;margin-left:6px;">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Global object to store data for the assignment module
const AssignmentModule = {
    teacherId: null,
    allLoads: [],
    currentLoads: [] 
};

function hideAssignmentSection() {
    document.getElementById('assignmentSection').style.display = 'none';
}

function closeEditModal() {
    const modal = document.getElementById("editTeacherModal");
    if (modal) modal.style.display = "none";
}

// =========================================================
// ASSIGNMENT MODULE FUNCTIONS (Copied from previous step)
// =========================================================

function renderAssignmentSelect(selectList, assignments) {
    selectList.innerHTML = '<option value="">-- Select Section and Subject --</option>';
    if (!assignments || assignments.length === 0) return;

    assignments.forEach(a => {
        const value = a.section_id + '|' + a.subject_id;
        const text = `G${a.grade_level} ${a.section_name} (${a.section_letter}) - ${a.subject_name}`;
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        selectList.appendChild(option);
    });
}

function renderCurrentAssignmentsTable(tbody, assignments) {
    tbody.innerHTML = '';
    if (!assignments || assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:#888;">No subjects currently assigned.</td></tr>';
        return;
    }

    // Update the AssignmentModule.currentLoads list for client-side duplicate checking
    AssignmentModule.currentLoads = assignments; 
    
    assignments.forEach(a => {
        const row = tbody.insertRow();
        row.id = `load_row_${a.load_id}`;
        
        row.innerHTML = `
            <td>G${a.grade_level} ${a.section_name} (${a.section_letter})</td>
            <td>${a.subject_name} (${a.subject_code})</td>
            <td>
                <button class="delete-assignment-btn" data-load-id="${a.load_id}"
                    style="background:#dc3545; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;">
                    Remove
                </button>
            </td>
        `;
    });
}

async function loadAssignmentModule(teacherId, teacherName) {
    const section = document.getElementById('assignmentSection');
    const header = document.getElementById('assignmentHeader');
    const selectList = document.getElementById('assignment_select');
    const tbody = document.getElementById('assignmentsTbody');

    AssignmentModule.teacherId = teacherId;
    header.innerHTML = `Assign Subjects to <strong>${teacherName}</strong>`;
    section.style.display = 'block';

    // 1. Fetch ALL possible assignments (only once)
    if (AssignmentModule.allLoads.length === 0) {
        try {
            const res = await fetch("teacher_assignment_api.php?action=get_all_assignments");
            const data = await res.json();
            if (data.status === 'success') {
                AssignmentModule.allLoads = data.assignments;
                renderAssignmentSelect(selectList, AssignmentModule.allLoads);
            } else {
                selectList.innerHTML = '<option value="">-- Error loading data --</option>';
            }
        } catch (e) {
            Swal.fire("Error", "Network error loading assignment options.", "error");
        }
    }
    
    // 2. Fetch CURRENT assignments for this teacher
    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; color:#007bff;">Loading current loads...</td></tr>';
    try {
        const res = await fetch(`teacher_assignment_api.php?action=get_teacher_loads&id=${teacherId}`);
        const data = await res.json();
        if (data.status === 'success') {
            renderCurrentAssignmentsTable(tbody, data.loads); // Will update AssignmentModule.currentLoads inside
        } else {
            Swal.fire("Error", "Failed to load current assignments.", "error");
        }
    } catch (e) {
        Swal.fire("Error", "Network error loading current assignments.", "error");
    }
}

// Event listener for adding an assignment (Kept for completeness)
document.getElementById('addAssignmentBtn').addEventListener('click', async () => {
    const select = document.getElementById('assignment_select');
    const selectedValue = select.value;
    const teacherId = AssignmentModule.teacherId;

    if (!selectedValue || !teacherId) {
        return Swal.fire("Warning", "Please select a section and subject.", "warning");
    }

    const [sectionId, subjectId] = selectedValue.split('|');
    
    const isDuplicate = AssignmentModule.currentLoads.some(load => 
        load.section_id == sectionId && load.subject_id == subjectId
    );

    if (isDuplicate) {
        return Swal.fire("Warning", "This teacher is already assigned to this specific load.", "warning");
    }

    try {
        const formData = new FormData();
        formData.append('action', 'add_assignment');
        formData.append('teacher_id', teacherId);
        formData.append('section_id', sectionId);
        formData.append('subject_id', subjectId);
        formData.append('assignment_type', 'Subject Teacher'); 

        const res = await fetch("teacher_assignment_api.php", { method: 'POST', body: formData });
        const data = await res.json();

        if (data.status === 'success') {
            Swal.fire("Success", data.message, "success");
            loadAssignmentModule(teacherId, document.getElementById('assignmentHeader').textContent.replace('Assign Subjects to ', '').trim());
            select.value = ""; 
        } else {
            Swal.fire("Error", data.message || "Failed to add assignment.", "error");
        }
    } catch (e) {
        Swal.fire("Error", "Network error adding assignment.", "error");
    }
});

// Event listener for deleting an assignment (Delegation)
document.getElementById('currentAssignmentsTable').addEventListener('click', async (e) => {
    const deleteBtn = e.target.closest('.delete-assignment-btn');
    if (deleteBtn) {
        const loadId = deleteBtn.dataset.loadId;
        const teacherId = AssignmentModule.teacherId;

        Swal.fire({
            title: "Remove Assignment?",
            text: "Are you sure you want to remove this subject load?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, Remove it"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete_assignment');
                    formData.append('load_id', loadId);

                    const res = await fetch("teacher_assignment_api.php", { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire("Removed!", data.message, "success");
                        // Efficiently remove row from DOM
                        document.getElementById(`load_row_${loadId}`).remove();
                        // Reload the current assignments array (to update client-side list)
                        loadAssignmentModule(teacherId, document.getElementById('assignmentHeader').textContent.replace('Assign Subjects to ', '').trim());
                    } else {
                        Swal.fire("Error", data.message || "Failed to remove assignment.", "error");
                    }
                } catch (e) {
                    Swal.fire("Error", "Network error removing assignment.", "error");
                }
            }
        });
    }
});

// =========================================================
// TEACHER CRUD ACTIONS (FIXED EVENT LISTENER DELEGATION)
// =========================================================

function initManageTeachers() {
    const tbody = document.getElementById("teacherTbody");
    if (!tbody) return;

    // Delegate ALL actions to the tbody listener
    tbody.addEventListener("click", function(e) {
        const assignBtn = e.target.closest(".assignBtn");
        const assignAdviserBtn = e.target.closest(".assignAdviserBtn");
        const editBtn = e.target.closest(".editBtn");
        const deleteBtn = e.target.closest(".deleteBtn");

        // 1. ASSIGN SUBJECTS
        if (assignBtn) {
            const id = assignBtn.dataset.id;
            const name = assignBtn.dataset.name;
            loadAssignmentModule(id, name);
            return; 
        }

        // 2. ASSIGN ADVISER
        if (assignAdviserBtn) {
            const id = assignAdviserBtn.dataset.id;
            const name = assignAdviserBtn.dataset.name;
            loadAdviserModule(id, name);
            return;
        }

        // 3. EDIT BUTTON
        if (editBtn) {
            const id = editBtn.dataset.id;
            if (!id) return;
            
            // Fetch teacher data to fill the modal
            fetch("teacher_fetch_single.php?id=" + encodeURIComponent(id))
                .then(r => r.json())
                .then(data => {
                    if (data.status !== "success") {
                        Swal.fire("Error", data.message || "Failed to load teacher.", "error");
                        return;
                    }
                    const t = data.teacher;
                    document.getElementById("edit_id").value = t.id;
                    document.getElementById("edit_facultyId").value = t.facultyId;
                    document.getElementById("edit_fullname").value = t.fullname ?? '';
                    document.getElementById("edit_username").value = t.username ?? '';
                    document.getElementById("edit_email").value = t.email ?? '';
                    document.getElementById("edit_gender").value = t.gender ?? '';
                    document.getElementById("edit_status").value = t.status ?? 'Active';
                    
                    // Note: originalFormData must be defined in the scope where editForm is handled
                    // For now, setting it to null to avoid breaking the update handler
                    originalFormData = null; 
                    document.getElementById("editTeacherModal").style.display = "flex";
                })
                .catch(() => Swal.fire("Error", "Could not load teacher details.", "error"));
            return;
        }

        // 4. DELETE BUTTON
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            if (!id) return;
            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete the teacher record.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch("teacher_delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        Swal.fire({ icon: "success", title: "Deleted!", text: res.message, timer: 1400, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.message || "Delete failed", "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Could not reach server.", "error"));
            });
            return;
        }
    });

    // Handle Edit Form Submission (must be outside the click listener)
    const editForm = document.getElementById("editTeacherForm");
    if (editForm) {
        editForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            // Simplified change check since originalFormData is tricky in this scope
            
            closeEditModal();

            fetch("teacher_update.php", { method: "POST", body: formData })
                .then(r => r.json())
                .then(res => {
                    Swal.fire({
                        icon: res.status === "success" ? "success" : "error",
                        title: res.status === "success" ? "Updated!" : "Error",
                        text: res.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(() => Swal.fire("Error", "Could not update teacher.", "error"));
        });
    }
}

/* =========================================================
   ADVISER MODULE
   - loadAdviserModule(teacherId, teacherName)
   - closeAdviserModal()
   - adviser form submit / remove
   ========================================================= */

function closeAdviserModal() {
    const m = document.getElementById('adviserModal');
    if (m) m.style.display = 'none';
}

async function loadAdviserModule(teacherId, teacherName) {
    const modal = document.getElementById('adviserModal');
    const select = document.getElementById('adviser_select');
    const hidden = document.getElementById('adviser_teacher_id');

    hidden.value = teacherId;
    modal.style.display = 'block';
    select.innerHTML = '<option value="">-- Loading sections... --</option>';

    // 1. load all sections
    try {
        const res = await fetch('teacher_assignment_api.php?action=get_all_sections');
        const data = await res.json();
        if (data.status === 'success') {
            select.innerHTML = '<option value="">-- Unassigned / Select Section --</option>';
            data.sections.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.section_id;
                opt.textContent = `G${s.grade_level} ${s.section_name} (${s.section_letter})`;
                select.appendChild(opt);
            });
        } else {
            select.innerHTML = '<option value="">-- Error loading sections --</option>';
        }
    } catch (e) {
        select.innerHTML = '<option value="">-- Network error --</option>';
    }

    // 2. load current advisory for this teacher and pre-select
    try {
        const res2 = await fetch(`teacher_assignment_api.php?action=get_teacher_advisory&id=${teacherId}`);
        const d2 = await res2.json();
        if (d2.status === 'success' && d2.section) {
            select.value = d2.section.section_id || '';
        } else {
            select.value = '';
        }
    } catch (e) {
        // ignore
    }
}

// Adviser form submit
const adviserForm = document.getElementById('adviserForm');
if (adviserForm) {
    adviserForm.addEventListener('submit', async (ev) => {
        ev.preventDefault();
        const teacherId = document.getElementById('adviser_teacher_id').value;
        const sectionId = document.getElementById('adviser_select').value;
        if (!teacherId || !sectionId) return Swal.fire('Warning', 'Please select a section to assign.', 'warning');

        try {
            const fd = new FormData();
            fd.append('action', 'set_adviser');
            fd.append('teacher_id', teacherId);
            fd.append('section_id', sectionId);

            const res = await fetch('teacher_assignment_api.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'success') {
                Swal.fire('Success', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to assign adviser', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Network error assigning adviser.', 'error');
        }
    });
}

// Remove adviser button
const removeAdviserBtn = document.getElementById('removeAdviserBtn');
if (removeAdviserBtn) {
    removeAdviserBtn.addEventListener('click', async () => {
        const teacherId = document.getElementById('adviser_teacher_id').value;
        if (!teacherId) return;

        Swal.fire({
            title: 'Remove Adviser?',
            text: 'This will unassign the adviser from their current section.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove'
        }).then(async (r) => {
            if (!r.isConfirmed) return;
            try {
                const fd = new FormData();
                fd.append('action', 'remove_adviser');
                fd.append('teacher_id', teacherId);
                const res = await fetch('teacher_assignment_api.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.status === 'success') {
                    Swal.fire('Removed!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to remove adviser', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Network error removing adviser.', 'error');
            }
        });
    });
}

// 🔑 FINAL FIX: Ensure the initialization is called
document.addEventListener("DOMContentLoaded", initManageTeachers);
setTimeout(initManageTeachers, 500); // Fallback initialization
</script>

<?php $conn->close(); ?>