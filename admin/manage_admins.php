<?php
require_once "../includes/oop_functions.php";
$db = new Database();
$conn = $db->conn; // use conn property

$sql = "
    SELECT 
        ad.user_id AS id,
        ad.admin_id AS adminId,
        ad.gender,
        ad.status,
        COALESCE(u.fullname,'') AS fullname,
        COALESCE(u.email,'') AS email,
        COALESCE(u.username,'') AS username,
        COALESCE(u.user_type,'') AS user_type
    FROM admin_details ad
    LEFT JOIN users u ON u.id = ad.user_id
    ORDER BY ad.user_id DESC
";
$result = $conn->query($sql);
?>
<h2>Manage Admins</h2>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
  <div style="position: relative; width: 100%; max-width: 420px;">
    <input type="text" id="searchInput" placeholder="Search by name, Admin ID, username, or email..." autocomplete="off"
           style="padding:10px;width:100%;border:1px solid #ccc;border-radius:8px;outline:none;font-size:15px;">
    <div id="suggestions"></div>
  </div>
</div>

<table id="adminsTable" border="1" cellspacing="0" cellpadding="8" style="width:100%; background:#fff; border-radius:8px;">
  <thead style="background:#007bff; color:white;">
    <tr>
      <th>Admin ID</th>
      <th>Full Name</th>
      <th>Gender</th>
      <th>Email</th>
      <th>Username</th>
      <th>Status</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody id="adminTbody">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr id="adminRow_<?= (int)$row['id'] ?>" style="text-align:center;">
          <td><?= htmlspecialchars($row['adminId'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['gender'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['user_type'] ?? '') ?></td>
          <td>
            <button class="editBtn" data-id="<?= (int)$row['id'] ?>" style="background:#007bff;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;">Edit</button>
            <button class="deleteBtn" data-id="<?= (int)$row['id'] ?>" style="background:#dc3545;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-left:6px;">Delete</button>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8" style="text-align:center;">No admins found</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- EDIT MODAL -->
<div id="editAdminModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3>Edit Admin</h3>
    <form id="editAdminForm">
      <input type="hidden" name="id" id="edit_id">

      <label>Admin ID</label>
      <input type="text" name="admin_id" id="edit_adminId" required>

      <label>Full Name</label>
      <input type="text" name="fullname" id="edit_fullname" required>

      <label>Username</label>
      <input type="text" name="username" id="edit_username" required>

      <label>Email</label>
      <input type="email" name="email" id="edit_email">

      <label>Gender</label>
      <select name="gender" id="edit_gender">
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>

      <label>Status</label>
      <select name="status" id="edit_status" required>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>

      <label>Role</label>
      <select name="user_type" id="edit_user_type" required>
        <option value="Admin">Admin</option>
      </select>

      <div style="margin-top:15px; display:flex; justify-content:space-between; align-items:center;">
        <button type="button" id="resetPasswordBtn" style="background:#ffc107;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">Reset Password</button>

        <div>
          <button type="submit" style="background:#28a745;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;">Save</button>
          <button type="button" onclick="closeEditModal()" style="background:#6c757d;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;margin-left:5px;">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function initManageAdmins() {
  const input = document.getElementById("searchInput");
  const suggestionsBox = document.getElementById("suggestions");
  const tbody = document.getElementById("adminTbody");
  if (!input || !tbody) return;

  function restoreTable() { Array.from(tbody.querySelectorAll("tr")).forEach(row => row.style.display = ""); }

  function showOnlyByQuery(q) {
    const query = (q + '').trim().toLowerCase();
    if (!query) return restoreTable();
    const rows = Array.from(tbody.querySelectorAll("tr"));
    let found = false;
    rows.forEach(row => {
      const adminId = row.cells[0]?.textContent.trim().toLowerCase() || "";
      const fullname = row.cells[1]?.textContent.trim().toLowerCase() || "";
      const gender = row.cells[2]?.textContent.trim().toLowerCase() || "";
      const email = row.cells[3]?.textContent.trim().toLowerCase() || "";
      const username = row.cells[4]?.textContent.trim().toLowerCase() || "";
      const status = row.cells[5]?.textContent.trim().toLowerCase() || "";
      if (adminId.includes(query) || fullname.includes(query) || username.includes(query) || email.includes(query) || gender.includes(query) || status.includes(query)) {
        row.style.display = ""; found = true;
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
      div.innerHTML = `
        <div style="display:flex;gap:10px;align-items:center;">
          <div style="width:36px;height:36px;border-radius:50%;background:#007bff;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">
            ${initial}
          </div>
          <div style="flex:1;">
            <div style="font-weight:600;color:#222;">${escapeHtml(item.Fullname)}</div>
            <div style="font-size:13px;color:#666;">${escapeHtml(item.AdminID)} • ${escapeHtml(item.Email)} • ${escapeHtml(item.Username)}</div>
          </div>
        </div>
      `;
      div.addEventListener("click", () => {
        input.value = item.Fullname;
        suggestionsBox.style.display = "none";
        showOnlyByQuery(item.AdminID);
      });
      suggestionsBox.appendChild(div);
    });
    suggestionsBox.style.display = "block";
  }

  input.addEventListener('input', function() {
    const q = this.value.trim();
    if (q.length < 1) { suggestionsBox.style.display = "none"; restoreTable(); return; }
    fetch('admin_search_suggest.php?q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(data => { renderSuggestions(data); showOnlyByQuery(q); })
      .catch(() => { suggestionsBox.style.display = "none"; });
  });

  document.addEventListener('click', (e) => { if (!e.target.closest('#searchInput') && !e.target.closest('#suggestions')) suggestionsBox.style.display = 'none'; });

  // Edit / Delete / Reset flows
  let originalFormData = null;
  const modal = document.getElementById("editAdminModal");

  tbody.addEventListener("click", function(e) {
    const editBtn = e.target.closest(".editBtn");
    const deleteBtn = e.target.closest(".deleteBtn");

    if (editBtn) {
      const id = editBtn.dataset.id;
      if (!id) return;
      fetch('admin_fetch_single.php?id=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(data => {
          if (!data || data.status === 'error') {
            Swal.fire("Error", data?.message || "Failed to load admin.", "error");
            return;
          }
          const t = data.admin;
          document.getElementById("edit_id").value = t.id;
          document.getElementById("edit_adminId").value = t.admin_id ?? '';
          document.getElementById("edit_fullname").value = t.fullname ?? '';
          document.getElementById("edit_username").value = t.username ?? '';
          document.getElementById("edit_email").value = t.email ?? '';
          document.getElementById("edit_gender").value = t.gender ?? '';
          document.getElementById("edit_status").value = t.status ?? 'Active';
          document.getElementById("edit_user_type").value = t.user_type ?? 'Admin';
          originalFormData = new FormData(document.getElementById("editAdminForm"));
          modal.style.display = "flex";
        })
        .catch(() => Swal.fire("Error", "Could not load admin details.", "error"));
    }

    if (deleteBtn) {
      const id = deleteBtn.dataset.id;
      if (!id) return;
      Swal.fire({
        title: "Are you sure?",
        text: "This will permanently delete the admin record.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (!result.isConfirmed) return;
        fetch("admin_delete.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id=" + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(res => {
          if (res.status === "success") {
            Swal.fire({ icon: "success", title: "Deleted!", text: res.message, timer: 1200, showConfirmButton: false })
              .then(() => location.reload());
          } else {
            Swal.fire("Error", res.message || "Delete failed", "error");
          }
        })
        .catch(() => Swal.fire("Error", "Could not reach server.", "error"));
      });
    }
  });

  // Save edit
  const editForm = document.getElementById("editAdminForm");
  if (editForm) {
    editForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      let changed = false;
      for (let [key, value] of formData.entries()) {
        if (!originalFormData || originalFormData.get(key) !== value) { changed = true; break; }
      }

      closeEditModal();

      if (!changed) {
        Swal.fire({ icon: "info", title: "No changes made" });
        return;
      }

      fetch("admin_update.php", { method: "POST", body: formData })
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
        .catch(() => Swal.fire("Error", "Could not update admin.", "error"));
    });
  }

  // Reset password
  const resetBtn = document.getElementById("resetPasswordBtn");
  if (resetBtn) {
    resetBtn.addEventListener("click", function() {
      const adminId = document.getElementById("edit_id").value.trim();
      if (!adminId) return Swal.fire("Error", "Missing admin ID.", "error");
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
        fetch("admin_reset_password.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id=" + encodeURIComponent(adminId)
        })
        .then(r => r.json())
        .then(res => {
          Swal.fire({
            icon: res.status === "success" ? "success" : "error",
            title: res.status === "success" ? "Password Reset!" : "Error",
            text: res.message,
            timer: 1400,
            showConfirmButton: false
          }).then(() => location.reload());
        })
        .catch(() => Swal.fire("Error", "Could not reset password.", "error"));
      });
    });
  }
}

document.addEventListener("DOMContentLoaded", initManageAdmins);
setTimeout(initManageAdmins, 500);

function closeEditModal() {
  const modal = document.getElementById("editAdminModal");
  if (modal) modal.style.display = "none";
}
</script>

<style>
h2 { margin-bottom: 12px; color:#333; }
#adminsTable { border-collapse: collapse; width:100%; }
#adminsTable th, #adminsTable td { padding:8px; border:1px solid #e6e6e6; }
#adminsTable tbody tr:nth-child(even) { background:#fafafa; }
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
