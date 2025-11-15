<?php
// This file is meant to be loaded dynamically inside dashboard.php
?>
<div class="add-student-form">
  <h2>Add New Student</h2>
  <form id="addStudentForm" autocomplete="off">

    <label for="schoolId">School ID</label>
    <input type="text" name="schoolId" id="schoolId" required placeholder="e.g. 20258393">

    <label for="fullname">Full Name</label>
    <input type="text" name="fullname" id="fullname" required placeholder="Enter full name">

    <label for="gender">Gender</label>
    <select name="gender" id="gender" required>
      <option value="">Select Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <label for="birthdate">Date of Birth</label>
    <input type="date" name="birthdate" id="birthdate" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" required placeholder="Enter  email (e.g. student@evelio.edu)">

    <label>Default Password</label>
    <input type="text" value="1" readonly style="background:#f1f1f1;">
    <small style="color:#555;">Note: The default password is <b>1</b>. The student will be asked to change it upon first login.</small>

    <button type="submit" id="submitBtn">Add Student</button>
  </form>
</div>

<script>
(() => {
  const form = document.getElementById('addStudentForm');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing...";

    const formData = new FormData(form);

    try {
      const res = await fetch('add_student.php', {
        method: 'POST',
        body: formData,
        cache: 'no-store'
      });

      const data = await res.json();

      Swal.fire({
        icon: data.status,
        title: data.status === 'success' ? 'Student Added!' : 'Error',
        text: data.message
      });

      if (data.status === 'success') {
        form.reset();
        if (typeof loadInterface === 'function') {
          setTimeout(() => loadInterface('manage_students.php'), 1000);
        }
      }
    } catch (error) {
      Swal.fire('Error', 'Could not process request.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Add Student";
    }
  });
})();
</script>

<style>
.add-student-form {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.add-student-form h2 {
  color: #333;
  font-weight: 600;
}
.add-student-form label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
}
.add-student-form input,
.add-student-form select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.add-student-form input:focus,
.add-student-form select:focus {
  border-color: #007bff;
  outline: none;
}
.add-student-form input[readonly] {
  background: #f9f9f9;
}
.add-student-form small {
  display: block;
  margin-top: 4px;
  font-size: 0.9em;
  color: #666;
}
.add-student-form button {
  margin-top: 15px;
  background: #007bff;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.2s;
}
.add-student-form button:hover:not(:disabled) {
  background: #0056b3;
}
.add-student-form button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
