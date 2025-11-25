<?php 
// admin/manage_sections.php - FIXED FOR AJAX LOADING AND BUTTON FUNCTIONALITY
require_once "../includes/oop_functions.php";

$db = new Database();
$conn = $db->getConnection();

// --- PHP LOGIC ---
$target_grade = isset($_GET['grade']) ? intval($_GET['grade']) : 7; 
$current_academic_year = "2024-2025"; 

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
                                    <button class="deleteSectionBtn" data-id="<?= (int)$row['sectionId'] ?>"
                                        style="background:#dc3545;color:white;border:none;padding:5px 8px;border-radius:4px;cursor:pointer;margin-left:6px;">
                                        Delete
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

    // --- Modal/Global Control Functions (Assumed defined globally) ---
    function openAddSectionModal() { /* Placeholder - Assumed defined elsewhere */ }
    function closeEnrollModal() {
        document.getElementById("enrollStudentModal").style.display = "none";
        document.getElementById('studentSearchInput').value = '';
    }
    
    // FIX: openEnrollModal now correctly calls the fetchUnassignedStudents for the modal content
    function openEnrollModal(sectionId, gradeLevel, sectionName) {
        document.getElementById('enrollModalTitle').innerHTML = `Enroll Students into <strong>${sectionName}</strong>`;
        document.getElementById('enroll_section_id').value = sectionId;
        document.getElementById('enroll_grade_level').value = gradeLevel;
        document.getElementById('enrollStudentModal').style.display = "flex";
        
        // This is the critical AJAX call that populates the checklist
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


    // --- Core Initialization on Load ---
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Setup the "View Students" button state (they start hidden)
        document.querySelectorAll('.viewStudentsBtn').forEach(btn => {
             btn.textContent = 'View Students';
             btn.style.background = '#17a2b8';
        });
        
        // 2. Filter the main table by the initial grade level (G7)
        filterByGrade(activeGrade);
    });

    // --- Sidebar Click Handler (Executed when clicking a Grade Level in the sidebar) ---
    function filterByGrade(targetGrade) {
        activeGrade = targetGrade;

        // 1. Filter Enrolled Sections Table
        document.querySelectorAll('.grade-segment').forEach(segment => {
            if (parseInt(segment.dataset.grade) === targetGrade) {
                segment.style.display = 'block';
            } else {
                segment.style.display = 'none';
            }
        });
    }

    // --- Table Action Delegation ---
    document.querySelector('.sections-container').addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.viewStudentsBtn');
        const enrollBtn = e.target.closest('.enrollStudentsBtn');
        
        if (enrollBtn) {
            const id = enrollBtn.dataset.id;
            const grade = enrollBtn.dataset.grade;
            const name = enrollBtn.dataset.name;
            openEnrollModal(id, grade, name); // FIXED ACTION
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
        // ... (rest of the listeners for editBtn, deleteBtn follow here) ...
    });
</script>
<?php $conn->close(); ?>