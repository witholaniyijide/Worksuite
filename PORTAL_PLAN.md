# Elpis View Portal System - Implementation Plan

## Context
Elpis View is a 1-on-1 online tutoring service for school subjects (Math, English, Chemistry, Physics, Yoruba, French, Coding, Bible Study, IJMB prep). Tutors are education graduates (B.Sc. Ed). Classes held via Zoom. Monthly progress reports sent to parents. Pay-per-session pricing (from $5/hr). Serves UK, US, Canada regions.

## Architecture: 6 Role-Based Portals

### 1. ADMIN PORTAL (prefix: admin)
Full system administration.
- **Dashboard**: Student/tutor counts, today's classes, pending attendance, pending reports, recent activity
- **Students**: Full CRUD, assign tutors, manage status (active/inactive/graduated/withdrawn)
- **Tutors**: Full CRUD, manage pay rates, view performance
- **Attendance**: Review/approve/reject, bulk approve, export
- **Reports**: View all, read-only
- **Notices**: Full CRUD (announcements to all roles)
- **Settings**: System configuration

### 2. DIRECTOR PORTAL (prefix: director)
Strategic oversight & final approvals.
- **Dashboard**: Revenue trends, enrollment stats, approval queue, attendance rates
- **Students**: Full CRUD with guardian/parent info
- **Tutors**: Full CRUD with performance metrics
- **Attendance**: Full CRUD, approve, edit, delete
- **Reports**: Review & final approve (submitted → manager-approved → director-approved)
- **Finance**: Track income/expenses per region
- **Analytics**: Enrollment, performance, attendance, revenue charts
- **Notices**: Full CRUD

### 3. MANAGER PORTAL (prefix: manager, location-based: UK, US, Canada)
Regional management & coordination.
- **Dashboard**: Regional stats (cached), pending approvals, today's schedule
- **Students**: Read-only list (filtered by manager's region), view progress/attendance/reports
- **Tutors**: Read-only list (filtered by region), view performance
- **Attendance**: Monitor attendance, view pending, calendar view
- **Reports**: Review & approve tutor reports (first approval before director)
- **Notices**: CRUD (visible to their region)

### 4. TUTOR PORTAL (prefix: tutor)
Teaching operations.
- **Dashboard**: Active students, pending attendance, report status, today's classes
- **My Students**: View assigned students with progress
- **Attendance**: Submit (regular & stand-in), view history, monthly count
- **Reports**: Create, edit drafts, submit for review
- **Notices**: Read-only
- **Profile**: Edit own profile, availability

### 5. STUDENT PORTAL (prefix: student) [Future Phase]
- **Dashboard**: Progress overview, next milestone
- **Reports**: View director-approved reports
- **Attendance**: View own attendance history
- **Profile**: View/edit basic info

### 6. PARENT PORTAL (prefix: parent)
Parental oversight.
- **Dashboard**: Children overview, recent reports
- **My Children**: View child profiles, progress
- **Reports**: View director-approved reports per child
- **Attendance**: View child's attendance history

---

## Implementation Phases

### Phase 1: Profile Pages & Core Views (Current Session)
1. Student profile page (show.blade.php) - view student details, attendance, reports
2. Tutor profile page (show.blade.php) - view tutor details, students, earnings
3. Student edit page
4. Tutor edit page

### Phase 2: Tutor Portal
1. Tutor-specific dashboard (my students, my stats)
2. Tutor sidebar/navigation
3. Tutor attendance submission (already done)
4. Tutor report creation (already done)

### Phase 3: Manager Portal (Location-Based)
1. Manager dashboard (regional stats)
2. Manager sidebar with regional context
3. Manager student list (filtered by region)
4. Manager tutor list (filtered by region)
5. Manager attendance monitoring
6. Manager report review & approval

### Phase 4: Director Portal
1. Director dashboard (strategic overview)
2. Director sidebar
3. Director student/tutor CRUD
4. Director attendance management
5. Director report final approval
6. Director analytics

### Phase 5: Admin Portal
1. Admin dashboard
2. Admin sidebar
3. Admin student/tutor management
4. Admin attendance review
5. Admin notices

### Phase 6: Parent Portal Enhancement
1. Enhanced parent dashboard
2. Child attendance viewing
3. Child report viewing

---

## Key Subjects for Elpis View (not coding courses)
- Mathematics
- English Language
- Chemistry
- Physics
- Yoruba
- French
- Igbo
- Coding & Animation
- Web & App Development
- Bible Study
- IJMB Preparation
- UTME/GCE/Cambridge Exam Prep

## Database Changes Needed
- Update subjects seeder with Elpis View subjects
- Add `role` middleware support
- Add manager region filtering
- Add report approval chain (submitted → manager_approved → director_approved)
