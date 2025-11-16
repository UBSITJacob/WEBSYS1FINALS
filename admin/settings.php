<?php
// admin/settings.php - dynamic settings interface loaded inside dashboard.php
session_start();
require_once "../includes/oop_functions.php";

// ensure admin session exists
if (!isset($_SESSION['admin'])) {
    echo "<div style='padding:20px;background:#fff;border-radius:8px;'>Unauthorized access.</div>";
    exit;
}

$admin = $_SESSION['admin'];
?>
<div class="settings-interface" style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 3px 12px rgba(0,0,0,0.08);">
  <h2>My Account Settings</h2>

  <form id="accountSettingsForm">
    <input type="hidden" name="update_account" value="1">

    <label style="display:block;margin-top:12px;font-weight:600;">Full Name</label>
    <input type="text" id="new_fullname" name="new_fullname" value="<?= htmlspecialchars($admin['fullname'] ?? '') ?>" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <label style="display:block;margin-top:12px;font-weight:600;">Username</label>
    <input type="text" id="new_username" name="new_username" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <label style="display:block;margin-top:12px;font-weight:600;">Email</label>
    <input type="email" id="new_email" name="new_email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <label style="display:block;margin-top:12px;font-weight:600;">Current Password</label>
    <input type="password" id="old_password" name="old_password" placeholder="Enter your current password" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <label style="display:block;margin-top:12px;font-weight:600;">New Password</label>
    <input type="password" id="new_password" name="new_password" placeholder="New password (min 8 characters)" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <label style="display:block;margin-top:12px;font-weight:600;">Confirm New Password</label>
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ccc;">

    <div style="margin-top:16px;display:flex;gap:10px;">
      <button type="submit" id="saveSettingsBtn" style="background:#007bff;color:#fff;padding:10px 14px;border-radius:6px;border:none;cursor:pointer;font-weight:700;">Save Changes</button>
      <button type="button" id="cancelSettingsBtn" style="background:#6c757d;color:#fff;padding:10px 14px;border-radius:6px;border:none;cursor:pointer;font-weight:700;">Back</button>
    </div>
  </form>
</div>

<script>
(function () {
  const form = document.getElementById('accountSettingsForm');
  const saveBtn = document.getElementById('saveSettingsBtn');
  const cancelBtn = document.getElementById('cancelSettingsBtn');

  if (!form) return;

  // Keep original values so we can check for changes
  const original = {
    fullname: document.getElementById('new_fullname').value.trim(),
    username: document.getElementById('new_username').value.trim(),
    email: document.getElementById('new_email').value.trim()
  };

  cancelBtn.addEventListener('click', () => {
    // go back to dashboard main view
    if (typeof loadPage === 'function') {
      loadPage('dashboard');
    } else {
      location.reload();
    }
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fullname = document.getElementById('new_fullname').value.trim();
    const username = document.getElementById('new_username').value.trim();
    const email = document.getElementById('new_email').value.trim();
    const oldPassword = document.getElementById('old_password').value.trim();
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (!fullname || !username || !email || !oldPassword) {
      return Swal.fire('Error', 'Please fill required fields.', 'error');
    }

    if (newPassword && newPassword.length < 8) {
      return Swal.fire('Error', 'New password must be at least 8 characters long.', 'error');
    }

    if (newPassword && newPassword !== confirmPassword) {
      return Swal.fire('Error', 'New passwords do not match.', 'error');
    }

    const hasChanges =
      fullname !== original.fullname ||
      username !== original.username ||
      email !== original.email ||
      newPassword.length > 0;

    if (!hasChanges) {
      return Swal.fire('Info', 'No changes detected.', 'info');
    }

    // disable button while processing
    saveBtn.disabled = true;
    saveBtn.textContent = 'Processing...';

    try {
      const formData = new FormData();
      formData.append('new_fullname', fullname);
      formData.append('new_username', username);
      formData.append('new_email', email);
      formData.append('old_password', oldPassword);
      formData.append('new_password', newPassword);
      formData.append('confirm_password', confirmPassword);
      formData.append('update_account', '1');

      const res = await fetch('update_account.php', {
        method: 'POST',
        body: formData,
        cache: 'no-store'
      });

      const data = await res.json();

      if (data.status === 'success') {
        Swal.fire('Success', data.message || 'Account updated!', 'success')
          .then(() => {
            // reload the whole dashboard to reflect session changes
            location.reload();
          });
      } else {
        Swal.fire('Error', data.message || 'Update failed', 'error');
      }
    } catch (err) {
      Swal.fire('Error', 'Unable to reach server.', 'error');
    } finally {
      saveBtn.disabled = false;
      saveBtn.textContent = 'Save Changes';
    }
  });
})();
</script>

<style>
/* Basic local styles to match admin UI */
.settings-interface h2 { margin: 0 0 12px 0; color:#333; }
.settings-interface label { font-weight:600; margin-top:8px; display:block; }
.settings-interface input { width:100%; padding:8px; margin-top:6px; border:1px solid #ddd; border-radius:6px; }
</style>
