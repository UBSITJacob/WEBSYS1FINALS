<?php 
require_once "../includes/oop_functions.php";

$db = new Database();
$conn = $db->getConnection();

// SELECT includes lrn and contact_no and grade_level (no birthdate)
$sql = "
    SELECT 
        sd.user_id AS id,
        sd.lrn AS lrn,
        sd.school_id AS schoolId,
        sd.grade_level AS gradeLevel,
        sd.gender,
        sd.contact_no AS contactNo,
        sd.status,
        COALESCE(u.fullname, '') AS fullname,
        COALESCE(u.email, '') AS email
    FROM student_details sd
    LEFT JOIN users u ON u.id = sd.user_id
    ORDER BY sd.user_id DESC
";

$result = $conn->query($sql);
?>

<h2>Manage Students</h2>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <div style="position: relative; width: 100%; max-width: 480px;">
        <input type="text" id="searchInput" placeholder="Search by name, School ID, LRN, or email..." autocomplete="off"
            style="padding:10px;width:100%;border:1px solid #ccc;border-radius:8px;outline:none;font-size:15px;">
        <div id="suggestions"></div>
    </div>
</div>

<table id="studentsTable" border="1" cellspacing="0" cellpadding="8"
    style="width:100%; background:#fff; border-radius:8px;">
    <thead style="background:#007bff; color:white;">
        <tr>
            <th>LRN</th>
            <th>School ID</th>
            <th>Full Name</th>
            <th>Grade Level</th>
            <th>Gender</th>
            <th>Contact No.</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="studentTbody">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr id="studentRow_<?= (int)$row['id'] ?>" style="text-align:center;">
                    <td><?= htmlspecialchars($row['lrn'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['schoolId'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['gradeLevel'] ?? '') ?></td> 
                    <td><?= htmlspecialchars($row['gender'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['contactNo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td>
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
            <tr><td colspan="9" style="text-align:center;">No students found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Edit Modal -->
<div id="editStudentModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3>Edit Student Information</h3>
    <form id="editStudentForm">
      <input type="hidden" name="id" id="edit_id">

      <label>LRN</label>
      <input type="text" name="lrn" id="edit_lrn" required>

      <label>School ID</label>
      <input type="text" name="schoolId" id="edit_schoolId" required>

      <label>Full Name</label>
      <input type="text" name="fullname" id="edit_fullname" required>

      <label>Username</label>
      <input type="text" name="username" id="edit_username" required>

      
      <label>Grade Level</label>
      <select name="gradeLevel" id="edit_gradeLevel">
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
      </select>
      
      <label>Email</label>
      <input type="email" name="email" id="edit_email">

      <label>Gender</label>
      <select name="gender" id="edit_gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>

      <label>Contact No.</label>
      <input type="text" name="contactNo" id="edit_contactNo" required>

      <label>Status</label>
      <select name="status" id="edit_status" required>
        <option value="Active">Active</option>
        <option value="Alumni">Alumni</option>
        <option value="Transferred">Transferred</option>
        <option value="Dropped">Dropped</option>
      </select>

      <div style="margin-top:15px; display:flex; justify-content:space-between;">
        <button type="button" id="resetPasswordBtn"
          style="background:#ffc107;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">
          Reset Password
        </button>
        <div>
          <button type="submit"
            style="background:#28a745;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">
            Save
          </button>
          <button type="button" onclick="closeEditModal()"
            style="background:#6c757d;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;margin-left:5px;">
            Cancel
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function initManageStudents() {
    const input = document.getElementById("searchInput");
    const suggestionsBox = document.getElementById("suggestions");
    const tbody = document.getElementById("studentTbody");
    if (!input || !suggestionsBox || !tbody) return;

    function restoreTable() {
        Array.from(tbody.querySelectorAll("tr")).forEach(row => row.style.display = "");
    }

    function showOnlyByQuery(q) {
        const query = (q + '').trim().toLowerCase();
        if (!query) { restoreTable(); return; }
        const rows = Array.from(tbody.querySelectorAll("tr"));
        let found = false;
        rows.forEach(row => {
            const lrn = (row.cells[0]?.textContent || "").trim().toLowerCase();
            const schoolId = (row.cells[1]?.textContent || "").trim().toLowerCase();
            const fullname = (row.cells[2]?.textContent || "").trim().toLowerCase();
            const grade = (row.cells[3]?.textContent || "").trim().toLowerCase();
            const gender = (row.cells[4]?.textContent || "").trim().toLowerCase();
            const contactNo = (row.cells[5]?.textContent || "").trim().toLowerCase();
            const email = (row.cells[6]?.textContent || "").trim().toLowerCase();
            const status = (row.cells[7]?.textContent || "").trim().toLowerCase();

            if (lrn.includes(query) || schoolId.includes(query) || fullname.includes(query) || email.includes(query) ||
                grade.includes(query) || gender.includes(query) || contactNo.includes(query) || status.includes(query)) {
                row.style.display = "";
                found = true;
            } else {
                row.style.display = "none";
            }
        });
        if (!found) rows.forEach(r => r.style.display = "none");
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"'`=\/]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'})[s]);
    }

    function renderSuggestions(list) {
        suggestionsBox.innerHTML = "";
        if (!list || list.length === 0) {
            suggestionsBox.innerHTML = "<div class='no-suggestion'>No results found</div>";
            suggestionsBox.style.display = "block";
            return;
        }
        list.forEach(item => {
            const div = document.createElement("div");
            div.classList.add("suggestion-item");

            const initial = (item.Fullname && item.Fullname.length > 0) ? item.Fullname.charAt(0).toUpperCase() : "?";
            const html = `
                <div style="display:flex;gap:10px;align-items:center;">
                  <div style="width:36px;height:36px;border-radius:50%;background:#007bff;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">
                    ${escapeHtml(initial)}
                  </div>
                  <div style="flex:1;">
                    <div style="font-weight:600;color:#222;">${escapeHtml(item.Fullname)}</div>
                    <div style="font-size:13px;color:#666;">${escapeHtml(item.SchoolID)} • ${escapeHtml(item.LRN || '')} • ${escapeHtml(item.Email || '')}</div>
                  </div>
                </div>
            `;
            div.innerHTML = html;

            div.addEventListener("click", () => {
                input.value = item.Fullname;
                suggestionsBox.style.display = "none";
                // filter table by school id (preferred) or fallback to LRN
                if (item.SchoolID && item.SchoolID.trim() !== "") {
                    showOnlyByQuery(item.SchoolID);
                } else if (item.LRN && item.LRN.trim() !== "") {
                    showOnlyByQuery(item.LRN);
                } else {
                    showOnlyByQuery(item.Fullname);
                }
            });

            suggestionsBox.appendChild(div);
        });
        suggestionsBox.style.display = "block";
    }

    input.addEventListener("input", function() {
        const q = this.value.trim();
        if (q.length < 1) { suggestionsBox.style.display = "none"; restoreTable(); return; }
        fetch("student_search_suggest.php?q=" + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                renderSuggestions(data);
                showOnlyByQuery(q);
            })
            .catch(() => suggestionsBox.style.display = "none");
    });

    document.addEventListener("click", e => {
        if (!e.target.closest("#searchInput") && !e.target.closest("#suggestions")) {
            suggestionsBox.style.display = "none";
        }
    });

    let originalFormData = null;
    const modal = document.getElementById("editStudentModal");

    tbody.addEventListener("click", function(e) {
        const editBtn = e.target.closest(".editBtn");
        const deleteBtn = e.target.closest(".deleteBtn");

        if (editBtn) {
            const id = editBtn.dataset.id;
            if (!id) return;
            fetch("student_fetch_single.php?id=" + encodeURIComponent(id))
                .then(r => r.json())
                .then(data => {
                    if (data.status !== "success") {
                        Swal.fire("Error", data.message || "Failed to load student.", "error");
                        return;
                    }
                    const s = data.student;
                    document.getElementById("edit_id").value = s.id;
                    document.getElementById("edit_lrn").value = s.lrn || '';
                    document.getElementById("edit_schoolId").value = s.schoolId || '';
                    document.getElementById("edit_fullname").value = s.fullname || '';
                    document.getElementById("edit_email").value = s.email || '';
                    document.getElementById("edit_username").value = s.username || '';
                    document.getElementById("edit_gender").value = s.gender || '';
                    document.getElementById("edit_contactNo").value = s.contactNo || '';
                    document.getElementById("edit_status").value = s.status || 'Active';
                    document.getElementById("edit_gradeLevel").value = s.gradeLevel !== null ? s.gradeLevel : '';
                    originalFormData = new FormData(document.getElementById("editStudentForm"));
                    modal.style.display = "flex";
                })
                .catch(() => Swal.fire("Error", "Could not load student details.", "error"));
        }

        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            if (!id) return;
            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete the student record.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch("student_delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        // success alert auto-close then reload
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Swal.fire("Error", res.message || "Delete failed", "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Could not reach server.", "error"));
            });
        }
    });

    const editForm = document.getElementById("editStudentForm");
    if (editForm) {
        editForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            let changed = false;
            for (let [key, value] of formData.entries()) {
                if (!originalFormData || originalFormData.get(key) !== value) { changed = true; break; }
            }
            // close modal immediately
            closeEditModal();
            if (!changed) { Swal.fire({ icon: "info", title: "No changes made", timer: 1200, showConfirmButton: false }); return; }

            fetch("student_update.php", { method: "POST", body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Swal.fire("Error", res.message || "Update failed", "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Could not update student.", "error"));
        });
    }

    const resetBtn = document.getElementById("resetPasswordBtn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            const studentId = document.getElementById("edit_id").value.trim();
            if (!studentId) return Swal.fire("Error", "Missing student ID.", "error");
            // close modal immediately
            closeEditModal();
            Swal.fire({
                title: "Reset Password?",
                text: "This will reset the password to default (1).",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ffc107",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, reset it!"
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch("student_reset_password.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(studentId)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Password Reset!",
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Swal.fire("Error", res.message || "Password reset failed", "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Could not reset password.", "error"));
            });
        });
    }
}

document.addEventListener("DOMContentLoaded", initManageStudents);
setTimeout(initManageStudents, 500);

function closeEditModal() {
    const modal = document.getElementById("editStudentModal");
    if (modal) modal.style.display = "none";
}
</script>

<style>
/* Styles matched to admin design */
h2 { margin-bottom: 12px; color:#333; }
#studentsTable { border-collapse: collapse; width:100%; }
#studentsTable th, #studentsTable td { padding:8px; border:1px solid #e6e6e6; }
#studentsTable tbody tr:nth-child(even) { background:#fafafa; }
#searchInput:focus { border-color:#007bff; box-shadow:0 0 5px #007bff55; }

#suggestions { position:absolute; top:44px; width:100%; background:white; border:1px solid #ccc; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); display:none; z-index:2000; overflow-y:auto; max-height:260px; }
.suggestion-item { padding:10px; border-bottom:1px solid #eee; transition:background 0.15s, transform 0.06s; }
.suggestion-item:hover { background:#f0f7ff; cursor:pointer; transform:translateY(-1px); }
.no-suggestion { padding:10px; text-align:center; color:#888; }

.modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 9998; }
.modal-content { background: #fff; padding: 20px; border-radius: 10px; width: 520px; max-width: 95%; box-shadow: 0 3px 10px rgba(0,0,0,0.2); z-index: 9999; }
.modal-content h3 { margin-top:0; margin-bottom:10px; text-align:center; }
.modal-content label { display:block; margin-top:10px; font-weight:500; }
.modal-content input, .modal-content select { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
</style>

<?php $conn->close(); ?>
