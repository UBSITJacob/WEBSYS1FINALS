<?php
// This file is meant to be loaded dynamically inside dashboard.php
?>
<div class="add-teacher-form">
  <h2>Add New Teacher</h2>
  <form id="addTeacherForm" method="POST" enctype="multipart/form-data" autocomplete="off">
    
    <label for="facultyId">Faculty ID</label>
    <input type="text" name="facultyId" id="facultyId" required placeholder="Enter Faculty ID">

    <label for="fullname">Full Name</label>
    <input type="text" name="fullname" id="fullname" required placeholder="Enter full name">

    <label for="username">Username</label>
    <input type="text" name="username" id="username" required placeholder="Enter username">

    <label for="gender">Gender</label>
    <select name="gender" id="gender" required>
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" required placeholder="Enter email (e.g. teacher@evelio.edu)">

    <label>Default Password</label>
    <input type="text" value="1" readonly style="background:#f1f1f1;">
    <small style="color:#555;">Note: The default password is <b>1</b>. The teacher will be asked to change it upon first login.</small>

    <button type="submit" id="submitBtn">Add Teacher</button>
  </form>
</div>

<script>
(() => {
  const form = document.getElementById('addTeacherForm');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing...";

    const formData = new FormData(form);

    try {
      const res = await fetch('add_teacher.php', {
        method: 'POST',
        body: formData,
        cache: 'no-store' // prevents caching problems
      });

      const data = await res.json();

      Swal.fire({
        icon: data.status,
        title: data.status === 'success' ? 'Teacher Added!' : 'Error',
        text: data.message
      });

      if (data.status === 'success') {
        form.reset();
        if (typeof loadInterface === 'function') {
          setTimeout(() => loadInterface('manage_teachers.php'), 1000);
        }
      }

    } catch (error) {
      Swal.fire('Error', 'Could not process request.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Add Teacher";
    }
  });
})();
</script>

<style>
.add-teacher-form {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.add-teacher-form h2 {
  color: #333;
  font-weight: 600;
}
.add-teacher-form label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
}
.add-teacher-form input,
.add-teacher-form select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.add-teacher-form input:focus,
.add-teacher-form select:focus {
  border-color: #007bff;
  outline: none;
}
.add-teacher-form input[readonly] {
  background: #f9f9f9;
}
.add-teacher-form small {
  display: block;
  margin-top: 4px;
  font-size: 0.9em;
  color: #666;
}
.add-teacher-form button {
  margin-top: 15px;
  background: #007bff;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.2s;
}
.add-teacher-form button:hover:not(:disabled) {
  background: #0056b3;
}
.add-teacher-form button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
