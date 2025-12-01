# Evelio AMS - Academic Management System

## Overview
A comprehensive PHP-based school management system with a modern, premium UI redesign. The system serves administrators, teachers, and students with role-based access control and full academic management functionality.

## Project State
- **Status**: UI Redesign Complete
- **Last Updated**: December 2024
- **PHP Version**: 8.2
- **Database**: MySQL (with demo mode fallback)

## Design System
- **Primary Color**: Navy (#1e3a5f)
- **Typography**: Inter font family
- **Style**: Modern, clean, professional, school-friendly
- **Responsiveness**: 100% mobile-responsive design

## Demo Accounts (When Database Unavailable)
- **Admin**: admin@evelio.edu / admin123
- **Teacher**: teacher@evelio.edu / teacher123
- **Student**: student@evelio.edu / student123

## Project Structure
```
FINAL-EVELIO-AMS/
├── assets/
│   ├── css/
│   │   ├── design-system.css    # Core design tokens
│   │   ├── components.css       # Reusable UI components
│   │   └── layout.css           # Page layouts
│   └── js/
│       └── app.js               # Frontend interactions
├── includes/
│   ├── header.php               # HTML head section
│   ├── sidebar.php              # Navigation sidebar
│   ├── topbar.php               # Top navigation bar
│   └── footer.php               # Page footer
├── db/
│   └── evelio_ams_db.sql        # Database schema
└── [PHP files]                   # Page files
```

## Key Pages
- **Authentication**: index.php (login), apply_consent.php, apply_register.php
- **Admin Portal**: admin_dashboard.php, applicants.php, students.php, teachers.php, sections.php
- **Teacher Portal**: teacher_dashboard.php, advisory_class.php, grades.php, attendance.php
- **Student Portal**: student_dashboard.php, student_profile.php, student_grades.php, student_attendance.php

## Architecture Decisions
1. **CSS-first approach**: No external CSS frameworks - custom design system
2. **Progressive Enhancement**: JavaScript enhances but not required for core functionality
3. **Demo Mode**: Graceful fallback when database unavailable
4. **Session Management**: Role-based authentication with session regeneration
5. **Layout Components**: Reusable PHP includes for consistent design

## Recent Changes
- Complete UI redesign with premium, modern styling
- 9-step multi-step student application form with progress indicator
- Collapsible sidebar navigation for all authenticated pages
- Modal dialogs for CRUD operations
- Responsive design for mobile/tablet devices
- Demo mode with sample accounts for development

## User Preferences
- Clean, elegant design that doesn't look AI-generated
- Soft neutral color palette (navy, gray, white, light blue)
- School-friendly professional appearance
- Inter font family for modern typography

## Development Notes
- Server runs on port 5000
- Use Cache-Control headers to prevent caching issues
- All database operations use PDO with prepared statements
- AJAX endpoints return JSON responses for modals/forms
