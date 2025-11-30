<?php 
// admin/manage_sections.php - Listing ALL sections and enabling Grade-level filtering via JS/Sidebar
require_once "../includes/oop_functions.php";

$db = new Database();
$conn = $db->getConnection();

// --- PHP LOGIC ---
// Query ALL sections (for filtering by grade level in JS)
$sql = "
    SELECT 
        s.id AS sectionId, s.section_name AS sectionName, s.grade_level AS gradeLevel,
        s.section_letter AS sectionLetter, COALESCE(u.fullname, 'N/A') AS adviserName,
        COALESCE(st.strand_code, 'N/A') AS strandCode
    FROM section s
    LEFT JOIN teacher_details td ON s.adviser_id = td.user_id 
    LEFT JOIN users u ON td.user_id = u.id
    LEFT JOIN strand st ON s.strand_id = st.id
    ORDER BY s.grade_level ASC, s.section_name ASC
";
$result = $conn->query($sql);

$sections_by_grade = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $grade = $row['gradeLevel'];
        if (!isset($sections_by_grade[$grade])) {
            $sections_by_grade[$grade] = [];
        }
        $sections_by_grade[$grade][] = $row;
    }
    $result->free(); 
}

$current_academic_year = "2024-2025";
$initial_target_grade = isset($_GET['grade']) ? intval($_GET['grade']) : 7;
?>

<style>
    /* CSS styles remain here (removed unassigned tags CSS since the card is gone) */
    .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 9998; }
    .modal-content { background: #fff; padding: 20px; border-radius: 10px; width: 600px; max-width: 95%; box-shadow: 0 3px 10px rgba(0,0,0,0.2); z-index: 9999; }
    .sectionsTable { border-collapse: collapse; width:100%; }
    .sectionsTable th, .sectionsTable td { padding:10px; border:1px solid #e6e6e6; }
    .sectionsTable tbody tr:nth-child(even) { background:#fafafa; }
    .inner-student-table { width: 95%; margin: 10px auto; border: 1px solid #ddd; border-collapse: collapse; }
    .inner-student-table th, .inner-student-table td { padding: 8px; border: 1px solid #eee; }
</style>

<h2>Manage Sections</h2>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <div style="position: relative; width: 100%; max-width: 400px;">
        <input type="text" id="searchInput" placeholder="Search sections..." autocomplete="off"
            style="padding:10px;width:100%;border:1px solid #ccc;border-radius:8px;outline:none;font-size:15px;">
        <div id="suggestions"></div> 
    </div>
    <button onclick="openAddSectionModal()" style="background:#28a745;color:white;border:none;padding:10px 15px;border-radius:5px;cursor:pointer;margin-left:15px;">
        + Add New Section
    </button>
</div>


<div class="sections-container" id="sectionsContainer">
    <?php if (!empty($sections_by_grade)): ?>
        <?php foreach ($sections_by_grade as $grade_level => $sections): ?>
            <div class="grade-segment" data-grade="<?= (int)$grade_level ?>" style="margin-bottom: 25px; <?php echo ($grade_level != $initial_target_grade) ? 'display:none;' : ''; ?>">
                <h3 style="background:#f0f0f0; padding: 10px; border-radius: 6px; border-left: 5px solid #007bff; color: #333; margin-top: 0;">
                    Sections for Grade <?php echo htmlspecialchars($grade_level); ?> (AY: <?php echo htmlspecialchars($current_academic_year); ?>)
                </h3>

                <table class="sectionsTable" border="1" cellspacing="0" cellpadding="8" style="width:100%; background:#fff; border-radius:8px;">
                    <thead style="background:#007bff; color:white;">
                        <tr>
                            <th>ID</th>
                            <th>Section Name</th>
                            <th>Adviser</th>
                            <th>Strand</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sectionTbody_<?= (int)$grade_level ?>">
                        <?php foreach ($sections as $row): ?>
                            <tr id="sectionRow_<?= (int)$row['sectionId'] ?>" style="text-align:center;">
                                <td><?= htmlspecialchars($row['sectionId'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['sectionName'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['adviserName'] ?? 'Unassigned') ?></td>
                                <td><?= htmlspecialchars($row['strandCode'] ?? 'N/A') ?></td>
                                <td data-section-id="<?= (int)$row['sectionId'] ?>">
                                    <button class="enrollStudentsBtn" 
                                            data-id="<?= (int)$row['sectionId'] ?>"
                                            data-grade="<?= (int)$row['gradeLevel'] ?>"
                                            data-name="<?= htmlspecialchars($row['sectionName']) ?>"
                                            style="background:#20c997;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer; margin-right: 5px;">
                                        Enroll
                                    </button>
                                    
                                    <button class="viewStudentsBtn" 
                                            data-id="<?= (int)$row['sectionId'] ?>"
                                            style="background:#17a2b8;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;">
                                        View Students
                                    </button>
                                    
                                    <button class="editSectionBtn" data-id="<?= (int)$row['sectionId'] ?>"
                                        style="background:#007bff;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-left:6px;">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="student-details-row" 
                                id="students_<?= (int)$row['sectionId'] ?>" 
                                style="display:none;">
                                <td colspan="5" style="padding: 0;">
                                    <div class="student-list-container" style="padding: 15px; background: #f9f9f9; text-align: left;">
                                        Loading student data...
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; padding: 20px; background: #fff; border-radius: 8px;">No sections found in the database. Please add new sections.</p>
    <?php endif; ?>
</div>

<div id="addSectionModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3>Add New Section</h3>
        <p>Form content goes here...</p>
        <button onclick="closeAddSectionModal()">Close</button>
    </div>
</div>

<div id="enrollStudentModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="width: 600px; max-height: 90vh; overflow-y: auto;">
        <h3 id="enrollModalTitle">Enroll Students into [Section Name]</h3>
        <p>Academic Year: <strong><?php echo htmlspecialchars($current_academic_year); ?></strong></p>
        
        <input type="hidden" id="enroll_section_id">
        <input type="hidden" id="enroll_grade_level">

        <p class="text-info" style="color: #007bff; background: #f0f7ff; padding: 8px; border-radius: 5px;">
            Showing unassigned students matching the Grade Level of this section.
        </p>

        <div class="form-row">
            <input type="text" id="studentSearchInput" placeholder="Search unassigned student by name or ID..." style="width: 100%;">
        </div>

        <form id="enrollmentForm">
            <div id="unassignedStudentList" style="min-height: 100px; max-height: 300px; overflow-y: auto; border: 1px solid #ccc; border-radius: 5px; padding: 10px;">
                Please select a section to load students.
            </div>

            <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
                <button type="submit" id="finalEnrollBtn" style="background:#28a745; margin-right: 10px;" disabled>
                    Enroll Selected Students
                </button>
                <button type="button" onclick="closeEnrollModal()" style="background:#6c757d;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const CURRENT_ACADEMIC_YEAR = '<?php echo $current_academic_year; ?>';
    let activeGrade = <?php echo $initial_target_grade; ?>;

    function openAddSectionModal() {
        document.getElementById("addSectionModal").style.display = "flex";
    }
    function closeAddSectionModal() {
        document.getElementById("addSectionModal").style.display = "none";
    }
    
    function closeEnrollModal() {
        document.getElementById("enrollStudentModal").style.display = "none";
        document.getElementById('studentSearchInput').value = '';
    }

    function openEnrollModal(sectionId, gradeLevel, sectionName) {
        document.getElementById('enrollModalTitle').innerHTML = `Enroll Students into <strong>${sectionName}</strong>`;
        document.getElementById('enroll_section_id').value = sectionId;
        document.getElementById('enroll_grade_level').value = gradeLevel;
        document.getElementById('enrollStudentModal').style.display = "flex";
        
        // Load unassigned students matching the grade level
        fetchUnassignedStudents(gradeLevel); 
    }

    // --- Student List Renderer (Enrolled) ---
    function renderStudentList(students, containerElement) {
        if (!students || students.length === 0) {
            containerElement.innerHTML = '<p style="margin: 0; color: #666;">No students enrolled in this section for ' + CURRENT_ACADEMIC_YEAR + '.</p>';
            return;
        }

        let html = '<h4>Enrolled Students (' + students.length + ')</h4>';
        html += '<table class="inner-student-table">';
        html += '<thead style="background:#e0e0e0; color:#333;"><tr><th>School ID</th><th>Full Name</th></tr></thead><tbody>';
        
        students.forEach(student => {
            html += `<tr>
                        <td style="width: 30%;">${student.school_id}</td>
                        <td style="width: 70%;">${student.fullname}</td>
                    </tr>`;
        });

        html += '</tbody></table>';
        containerElement.innerHTML = html;
    }

    async function loadStudentsForSection(sectionId, container) {
        container.innerHTML = '<p style="margin: 0; color: #007bff;">Loading Students...</p>';
        
        try {
            const url = 'get_students_for_section.php?id=' + sectionId + '&ay=' + CURRENT_ACADEMIC_YEAR;
            const res = await fetch(url);
            const data = await res.json();
            
            if (data.status === 'success') {
                renderStudentList(data.students, container);
            } else {
                container.innerHTML = '<p style="margin: 0; color: #dc3545;">Error fetching students: ' + (data.message || 'Server error.') + '</p>';
            }

        } catch (error) {
            container.innerHTML = '<p style="margin: 0; color: #dc3545;">Network error: Unable to load data.</p>';
        }
    }

    // --- Unassigned Students AJAX (For Enrollment Modal) ---

    async function fetchUnassignedStudents(gradeLevel, query = '') {
        const studentListContainer = document.getElementById('unassignedStudentList');
        studentListContainer.innerHTML = '<p style="margin: 0; color: #007bff;">Loading unassigned students...</p>';
        document.getElementById('finalEnrollBtn').disabled = true;

        try {
            const url = `fetch_unassigned_students.php?grade=${gradeLevel}&q=${encodeURIComponent(query)}`;
            const res = await fetch(url);
            const data = await res.json();

            if (data.status === 'success') {
                renderUnassignedStudentCheckboxes(data.students, studentListContainer);
            } else {
                studentListContainer.innerHTML = `<p style="margin: 0; color: #dc3545;">Error: ${data.message || 'Server error.'}</p>`;
            }
        } catch (error) {
            studentListContainer.innerHTML = '<p style="margin: 0; color: #dc3545;">Network error fetching student data.</p>';
        }
    }
    
    function renderUnassignedStudentCheckboxes(students, containerElement) {
        if (!students || students.length === 0) {
            containerElement.innerHTML = '<p style="margin: 0; color: #666;">No unassigned students found for this grade level or matching your search.</p>';
            document.getElementById('finalEnrollBtn').disabled = true;
            return;
        }

        let html = '';
        students.forEach(student => {
            html += `
                <div style="display: flex; align-items: center; padding: 5px; border-bottom: 1px solid #eee;">
                    <input type="checkbox" name="student_ids[]" value="${student.id}" id="stu_${student.id}" style="margin-right: 10px; width: auto;" />
                    <label for="stu_${student.id}" style="margin: 0; flex-grow: 1; font-weight: normal; cursor: pointer;">
                        <strong>${student.fullname}</strong> (${student.school_id})
                    </label>
                </div>
            `;
        });
        containerElement.innerHTML = html;
        document.getElementById('finalEnrollBtn').disabled = false;
    }

    /** Submits the selected students for enrollment into the target section. */
    document.getElementById('enrollmentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('finalEnrollBtn');
        btn.disabled = true;
        btn.textContent = 'Enrolling...';

        const sectionId = document.getElementById('enroll_section_id').value;
        const academicYear = CURRENT_ACADEMIC_YEAR;
        const form = new FormData(document.getElementById('enrollmentForm'));

        // Check if any checkboxes are selected
        const selectedStudents = form.getAll('student_ids[]');
        if (selectedStudents.length === 0) {
            Swal.fire('Error', 'Please select at least one student to enroll.', 'warning');
            btn.disabled = false;
            btn.textContent = 'Enroll Selected Students';
            return;
        }

        // Add hidden fields to form data
        form.append('section_id', sectionId);
        form.append('academic_year', academicYear);

        try {
            const res = await fetch('process_enrollment.php', { 
                method: 'POST',
                body: form
            });
            const data = await res.json();

            closeEnrollModal();
            Swal.fire({
                icon: data.status === 'success' ? 'success' : 'error',
                title: data.status === 'success' ? 'Enrollment Complete!' : 'Enrollment Failed',
                text: data.message,
            }).then(() => {
                // FIX 1: If successful, force reload of the enrolled list for the section immediately.
                if (data.status === 'success') {
                    const detailsRow = document.getElementById('students_' + sectionId);
                    const container = detailsRow.querySelector('.student-list-container');
                    const viewBtn = document.querySelector(`button.viewStudentsBtn[data-id="${sectionId}"]`);
                    
                    // Ensure row is visible and reload content
                    detailsRow.style.display = 'table-row';
                    if (viewBtn) {
                        viewBtn.textContent = 'Hide Students';
                        viewBtn.style.background = '#ffc107';
                    }
                    loadStudentsForSection(sectionId, container);
                }
                
                // FIX 2: Reload the entire page to update the unassigned list when modal is opened next time.
                loadPage('manage_sections.php?grade=' + activeGrade);
            });

        } catch (error) {
            closeEnrollModal();
            Swal.fire('Error', 'Network error during enrollment.', 'error');
        }
    });

    // Student search filter inside the modal
    document.getElementById('studentSearchInput').addEventListener('input', (e) => {
        const gradeLevel = document.getElementById('enroll_grade_level').value;
        const query = e.target.value;
        fetchUnassignedStudents(gradeLevel, query);
    });
    
    // --- Existing Functions for Sections Table (View Students) ---
    
    function renderStudentList(students, containerElement) {
        if (!students || students.length === 0) {
            containerElement.innerHTML = '<p style="margin: 0; color: #666;">No students enrolled in this section for ' + CURRENT_ACADEMIC_YEAR + '.</p>';
            return;
        }

        let html = '<h4>Enrolled Students (' + students.length + ')</h4>';
        html += '<table class="inner-student-table">';
        html += '<thead style="background:#e0e0e0; color:#333;"><tr><th>School ID</th><th>Full Name</th></tr></thead><tbody>';
        
        students.forEach(student => {
            html += `<tr>
                        <td style="width: 30%;">${student.school_id}</td>
                        <td style="width: 70%;">${student.fullname}</td>
                    </tr>`;
        });

        html += '</tbody></table>';
        containerElement.innerHTML = html;
    }

    async function loadStudentsForSection(sectionId, container) {
        container.innerHTML = '<p style="margin: 0; color: #007bff;">Loading Students...</p>';
        
        try {
            const url = 'get_students_for_section.php?id=' + sectionId + '&ay=' + CURRENT_ACADEMIC_YEAR;
            const res = await fetch(url);
            const data = await res.json();
            
            if (data.status === 'success') {
                renderStudentList(data.students, container);
            } else {
                container.innerHTML = '<p style="margin: 0; color: #dc3545;">Error fetching students: ' + (data.message || 'Server error.') + '</p>';
            }

        } catch (error) {
            container.innerHTML = '<p style="margin: 0; color: #dc3545;">Network error: Unable to load data.</p>';
        }
    }

    document.querySelectorAll('.sectionsTable').forEach(table => {
        table.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.viewStudentsBtn');
            const enrollBtn = e.target.closest('.enrollStudentsBtn');
            const editBtn = e.target.closest('.editSectionBtn');
            const deleteBtn = e.target.closest('.deleteSectionBtn');
            
            if (enrollBtn) {
                const id = enrollBtn.dataset.id;
                const grade = enrollBtn.dataset.grade;
                const name = enrollBtn.dataset.name;
                openEnrollModal(id, grade, name);
            }

            if (viewBtn) {
                const id = viewBtn.dataset.id;
                const detailsRow = document.getElementById('students_' + id);
                const container = detailsRow.querySelector('.student-list-container');
                
                // Toggle visibility
                const isHidden = detailsRow.style.display === 'none';

                if (isHidden) {
                    loadStudentsForSection(id, container);
                    detailsRow.style.display = 'table-row';
                    viewBtn.textContent = 'Hide Students';
                    viewBtn.style.background = '#ffc107'; 
                } else {
                    detailsRow.style.display = 'none';
                    viewBtn.textContent = 'View Students';
                    viewBtn.style.background = '#17a2b8';
                }
            }
            
            if (editBtn) {
                const id = editBtn.dataset.id;
                Swal.fire('Edit Function', 'Ready to edit Section ID: ' + id, 'info'); 
            }

            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                 Swal.fire({
                    title: "Delete Section?",
                    text: "Are you sure you want to delete Section ID " + id + "?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire('Deleting...', 'Deleting Section ' + id, 'info');
                    }
                });
            }
        });
    });

</script>
<?php $conn->close(); ?>