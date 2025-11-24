<?php
// This file must remain inside /admin/
?>
<div class="add-admin-form">
  <h2>Add New Admin</h2>

  <form id="addAdminForm" method="POST" autocomplete="off">

    <label for="admin_id">Admin ID</label>
    <input type="text" name="admin_id" id="admin_id" required placeholder="Enter Admin ID">

    <label for="fullname">Full Name</label>
    <input type="text" name="fullname" id="fullname" required placeholder="Enter full name">

    <label for="username">Username</label>
    <input type="text" name="username" id="username" required placeholder="Enter username">

    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Enter email">

    <label for="gender">Gender</label>
    <select name="gender" id="gender" required>
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <label for="user_type">Role</label>
    <select name="user_type" id="user_type" required>
      <option value="Admin">Admin</option>
    </select>

    <label>Default Password</label>
    <input type="text" value="1" readonly style="background:#f1f1f1;">
    <small style="color:#555;">Default password is <b>1</b>. Admin must change it on first login.</small>

    <button type="submit" id="submitBtn">Add Admin</button>
  </form>
</div>

<script>
(() => {
  const form = document.getElementById('addAdminForm');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing...";

    const formData = new FormData(form);
    formData.append("status", "Active"); // new admin ALWAYS active

    try {
      const res = await fetch('add_admin.php', {
        method: 'POST',
        body: formData,
        cache: 'no-store'
      });

      const data = await res.json();

      Swal.fire({
        icon: data.status,
        title: data.status === 'success' ? 'Admin Added!' : 'Error',
        text: data.message
      });

      if (data.status === 'success') {
        form.reset();

        if (typeof loadInterface === 'function') {
          setTimeout(() => loadInterface('manage_admins.php'), 1000);
        }
      }

    } catch (error) {
      Swal.fire('Error', 'Could not process request.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Add Admin";
    }
  });
})();
</script>

<style>
.add-admin-form {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.add-admin-form h2 {
  color: #333;
  font-weight: 600;
}
.add-admin-form label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
}
.add-admin-form input,
.add-admin-form select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.add-admin-form input:focus,
.add-admin-form select:focus {
  border-color: #007bff;
  outline: none;
}
.add-admin-form input[readonly] {
  background: #f9f9f9;
}
.add-admin-form small {
  display: block;
  margin-top: 4px;
  font-size: 0.9em;
  color: #666;
}
.add-admin-form button {
  margin-top: 15px;
  background: #007bff;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.2s;
}
.add-admin-form button:hover:not(:disabled) {
  background: #0056b3;
}
.add-admin-form button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
