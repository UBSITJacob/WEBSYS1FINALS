# Evelio AMS - Action Buttons Reference Guide

## Complete Button Implementation Status

All action buttons have been successfully implemented with standardized design, consistent behavior, and full mobile responsiveness.

---

## STUDENTS MANAGEMENT (`/students.php`)

### Button 1: View Student Details
- **Button Text**: "View"
- **Button Class**: `.btn-sm.btn-outline`
- **Color**: Gray (border)
- **Function**: `viewStudent(id)`
- **Action**: Navigates to `student_view.php?id={id}`
- **Mobile**: ✅ Responsive, stacks with other action buttons
- **Status**: ✅ **WORKING**

### Button 2: Update Student Details
- **Button Text**: "Update"
- **Button Class**: `.btn-sm.btn-primary`
- **Color**: Navy Blue
- **Function**: `updateStudent(id)`
- **Action**: Navigates to `student_update.php?id={id}`
- **Mobile**: ✅ Responsive, proper sizing on all devices
- **Status**: ✅ **WORKING**

### Button 3: Delete Student
- **Button Text**: "Delete"
- **Button Class**: `.btn-sm.btn-danger`
- **Color**: Red
- **Function**: `deleteStudent(id)`
- **Action**: API call to `api/delete.php` with `type=student&id={id}`
- **Confirmation**: Modal dialog with "Are you sure?" message
- **Loading State**: Button shows "Deleting..." while processing
- **Response**: Shows success or error message
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

### Button 4: Create Account
- **Button Text**: "Create Account"
- **Button Class**: `.btn-sm.btn-success`
- **Color**: Green
- **Function**: `createAccount(id)`
- **Action**: API call to `api/student.php` with `action=create_account&id={id}`
- **Confirmation**: Modal dialog confirming account creation
- **Loading State**: Button shows "Creating..." while processing
- **Response**: Confirmation of account creation, page refresh
- **Mobile**: ✅ Full-width on mobile, readable text
- **Status**: ✅ **WORKING**

### Button 5: Edit Account
- **Button Text**: "Edit Account"
- **Button Class**: `.btn-sm.btn-primary`
- **Color**: Navy Blue
- **Function**: `editAccount(id)`
- **Action**: Opens modal form with username and password fields
- **Validation**: 
  - Password must be 8+ characters if provided
  - Passwords must match
- **Submit Button Text**: "Save"
- **Loading State**: Shows "Saving..." while processing
- **Response**: Success/error message, page refresh
- **Mobile**: ✅ Full-width modal with stacked form fields
- **Status**: ✅ **WORKING**

---

## TEACHERS MANAGEMENT (`/teachers.php`)

### Button 1: Assign Advisory Class
- **Button Text**: "Assign"
- **Button Class**: `.btn-sm.btn-primary`
- **Color**: Navy Blue
- **Function**: `openAdviserModal(id)`
- **Action**: Opens modal to select section
- **Modal Content**: Dropdown to select section for advisory
- **Submit Button Text**: "Assign"
- **Submit Action**: API call to `api/adviser.php` with `teacher_id={id}&section_id={sectionId}`
- **Loading State**: Shows "Assigning..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Full-width modal, readable dropdowns
- **Status**: ✅ **WORKING**

### Button 2: Edit Teacher
- **Button Text**: "Edit"
- **Button Class**: `.btn-sm.btn-primary`
- **Color**: Navy Blue
- **Function**: `editTeacher(id)`
- **Action**: Opens modal form with teacher details
- **Form Fields**: Name, Username, Email, Sex, Active status
- **Submit Button Text**: "Save Changes"
- **Submit Action**: API call to `api/update.php` with `type=teacher&id={id}&...fields`
- **Loading State**: Shows "Saving..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Full-width modal with responsive form
- **Status**: ✅ **WORKING**

### Button 3: Delete Teacher
- **Button Text**: "Delete"
- **Button Class**: `.btn-sm.btn-danger`
- **Color**: Red
- **Function**: `deleteTeacher(id)`
- **Action**: API call to `api/delete.php` with `type=teacher&id={id}`
- **Confirmation**: Modal dialog with "Are you sure?" message
- **Loading State**: Button shows "Deleting..." while processing
- **Response**: Shows success or error message, page refresh
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

---

## SECTIONS MANAGEMENT (`/sections.php`)

### Button 1: Add Section
- **Button Text**: "Add Section"
- **Button Class**: `.btn.btn-primary`
- **Color**: Navy Blue
- **Location**: Top right of page (page-header-actions)
- **Function**: `openAddModal()`
- **Action**: Opens modal form for new section
- **Form Fields**: Name (required), Department, Grade Level, Strand, Capacity
- **Submit Button Text**: "Add Section"
- **Submit Action**: API call to `api/add.php` with `type=section&name={}&...fields`
- **Loading State**: Shows "Adding..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Full-width button on mobile, full-width modal
- **Status**: ✅ **WORKING**

### Button 2: Edit Section
- **Button Text**: "Edit"
- **Button Class**: `.btn-sm.btn-primary`
- **Color**: Navy Blue
- **Function**: `editSection(id)`
- **Action**: Opens modal form with section details
- **Form Fields**: Name (required), Capacity
- **Submit Button Text**: "Save Changes"
- **Submit Action**: API call to `api/update.php` with `type=section&id={id}&...fields`
- **Loading State**: Shows "Saving..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Full-width modal
- **Status**: ✅ **WORKING**

### Button 3: Delete Section
- **Button Text**: "Delete"
- **Button Class**: `.btn-sm.btn-danger`
- **Color**: Red
- **Function**: `deleteSection(id)`
- **Action**: API call to `api/delete.php` with `type=section&id={id}`
- **Confirmation**: Modal dialog with "Are you sure?" message
- **Loading State**: Button shows "Deleting..." while processing
- **Response**: Shows success or error message, page refresh
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

---

## SUBJECT LOADS MANAGEMENT (`/subject_loads.php`)

### Button 1: Add Load
- **Button Text**: "Add Load"
- **Button Class**: `.btn.btn-primary`
- **Color**: Navy Blue
- **Location**: Top right of page (page-header-actions)
- **Function**: `openAddModal()`
- **Action**: Opens modal form for new subject load
- **Form Fields**: Teacher (required), Subject (required), Section (required), School Year (required), Semester
- **Submit Button Text**: "Add Load"
- **Submit Action**: API call to `api/add.php` with `type=load&teacher_id={}&...fields`
- **Loading State**: Shows "Adding..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Full-width button on mobile, full-width modal
- **Status**: ✅ **WORKING**

### Button 2: Delete Load
- **Button Text**: "Delete"
- **Button Class**: `.btn-sm.btn-danger`
- **Color**: Red
- **Function**: `deleteLoad(id)`
- **Action**: API call to `api/delete.php` with `type=load&id={id}`
- **Confirmation**: Modal dialog with "Are you sure?" message
- **Loading State**: Button shows "Deleting..." while processing
- **Response**: Shows success or error message, page refresh
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

---

## APPLICANTS MANAGEMENT (`/applicants.php`)

### Button 1: View Applicant
- **Button Text**: "View"
- **Button Class**: `.btn-sm.btn-outline`
- **Color**: Gray (border)
- **Function**: `viewApplicant(id)`
- **Action**: Opens modal with full applicant details
- **Modal Content**: Loads from `applicant_view.php?id={id}&ajax=1`
- **Mobile**: ✅ Full-width modal on mobile
- **Status**: ✅ **WORKING**

### Button 2: Approve Applicant
- **Button Text**: "Approve"
- **Button Class**: `.btn-sm.btn-success`
- **Color**: Green
- **Function**: `approve(id)`
- **Action**: API call to `api/applicant.php` with `action=approve&id={id}`
- **Confirmation**: Modal dialog confirming approval
- **Loading State**: Button shows "Approving..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

### Button 3: Decline Applicant
- **Button Text**: "Decline"
- **Button Class**: `.btn-sm.btn-danger`
- **Color**: Red
- **Function**: `decline(id)`
- **Action**: API call to `api/applicant.php` with `action=decline&id={id}`
- **Confirmation**: Modal dialog confirming decline
- **Loading State**: Button shows "Declining..." while processing
- **Response**: Success message, page refresh
- **Mobile**: ✅ Responsive confirmation dialog
- **Status**: ✅ **WORKING**

---

## BUTTON GROUP LAYOUT

All action buttons are grouped in a `<div class="btn-group">` container:

### Desktop Layout (>992px)
- Buttons display horizontally in a row
- Grouped appearance with rounded corners on outer buttons
- Proper spacing between button groups

### Tablet Layout (768px - 992px)
- Buttons still horizontal
- Reduced padding and font size
- Wrapping if needed

### Mobile Layout (<768px)
- Buttons stack vertically or in responsive grid
- Each button takes appropriate width
- Full-width on very small screens
- Proper spacing between buttons (var(--spacing-1) to var(--spacing-2))

---

## ERROR HANDLING

All buttons implement consistent error handling:

1. **Client-side validation**: 
   - Check required fields
   - Validate password length/matching
   - Show immediate feedback

2. **API validation**:
   - Input sanitization
   - Type checking
   - ID validation

3. **User feedback**:
   - Error modal with message
   - Success confirmation
   - Automatic page refresh on success

---

## ACCESSIBILITY FEATURES

✅ All buttons have clear, descriptive text
✅ Proper button types (`type="button"`)
✅ Disabled state for loading
✅ Proper contrast ratios
✅ Touch-friendly sizes (minimum 44x44px)
✅ Keyboard accessible
✅ ARIA labels where applicable

---

## RESPONSIVE BEHAVIOR SUMMARY

| Device | Button Size | Layout | Confirmation | Modal |
|--------|-------------|--------|--------------|-------|
| Desktop (>1200px) | Regular | Horizontal groups | Full modal | 600px width |
| Tablet (768-992px) | Small | Horizontal with wrap | Full modal | 80% width |
| Mobile (576-768px) | Small | Grid/stacked | Full modal | 90% width |
| Small Mobile (<576px) | Smaller | Vertical stack | Full modal | 95% width |

---

## API ENDPOINTS USED

| Operation | Endpoint | Parameters | Response |
|-----------|----------|-----------|----------|
| Delete any | `api/delete.php` | `type`, `id` | `{success, message, data}` |
| Add teacher/section/load | `api/add.php` | `type`, field values | `{success, message, data}` |
| Update teacher/section | `api/update.php` | `type`, `id`, field values | `{success, message, data}` |
| Approve/Decline applicant | `api/applicant.php` | `action`, `id` | `{success, message, data}` |
| Create/Edit student account | `api/student.php` | `action`, `id`, fields | `{success, message, data}` |
| Assign adviser | `api/adviser.php` | `teacher_id`, `section_id` | `{success, message, data}` |

---

## QUICK TROUBLESHOOTING

**If a button isn't working:**
1. Check browser console for JavaScript errors
2. Check network tab for API response
3. Verify user has admin role (most actions require admin)
4. Check if API file exists in `/api` folder
5. Verify utility files exist in `/utils` folder

**If buttons don't look right on mobile:**
1. Check viewport meta tag in header.php
2. Check CSS media queries at 768px and 576px
3. Ensure `.btn-group` class is applied to button containers
4. Check for inline styles overriding CSS

---

**All buttons are production-ready and fully tested! ✅**
