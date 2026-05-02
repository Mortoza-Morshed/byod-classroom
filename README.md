# BYOD Classroom Management System

A web-based system built to enhance productivity and security in Bring Your Own Device (BYOD) classroom environments. Teachers can manage live class sessions, monitor student focus, share resources, and enforce content policies — all from a browser with no software installation required on student devices.

Built with Laravel 13, Livewire 4, Flux UI, and Tailwind CSS.

---

## Features

### Admin
- School-wide dashboard with live stats and activity feed
- User management — activate and deactivate accounts
- Device approval and blocking
- Full audit log with search and filters
- School-wide reports with violation breakdowns

### Teacher
- Create and manage classrooms with auto-generated join codes
- Configure content policies per classroom (allowed URLs, blocked keywords, internet kill-switch)
- Start and end live class sessions
- Real-time session dashboard showing all connected student devices
- Lock and unlock individual devices or all devices at once
- Share links and PDF files directly to student screens during sessions
- Make announcements that appear on all student screens instantly
- View detailed post-session reports with PDF export

### Student
- Register device for approval
- Join classrooms using a teacher-provided code
- Attend live sessions in a focused browser workspace
- View resources shared by the teacher inline — no tab switching required
- PDF files render inside the session using PDF.js
- Receive escalating warnings for focus violations

### Focus Enforcement
- Detects tab switching, window blur, and fullscreen exit
- Three-level warning system with toast notifications
- All violations logged in real time and visible to the teacher
- Focus monitoring pauses automatically when teacher shares an external link — no false positives

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Frontend | Livewire 4 + Volt |
| UI Components | Flux UI |
| Styling | Tailwind CSS |
| Auth | Laravel Fortify |
| Roles | spatie/laravel-permission |
| PDF Export | barryvdh/laravel-dompdf |
| PDF Viewer | PDF.js (CDN) |
| Database | MySQL |
| Testing | Pest |

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher and npm
- MySQL 8.0 or higher

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/byod-classroom.git
cd byod-classroom
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies and build assets

```bash
npm install
npm run build
```

### 4. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and update the database section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=byod_classroom
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Create the database

```sql
CREATE DATABASE byod_classroom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run migrations and seed

```bash
php artisan migrate
php artisan db:seed
```

### 7. Create storage symlink

```bash
php artisan storage:link
```

### 8. Start the development server

```bash
composer run dev
```

Visit `http://localhost:8000`

---

## Demo Accounts

All accounts use the password: `password`

| Email | Role |
|---|---|
| admin@school.com | Admin |
| sharma@school.com | Teacher |
| priya@school.com | Teacher |
| verma@school.com | Teacher |
| aarav.singh@student.com | Student |
| ananya.gupta@student.com | Student |
| rohan.mehta@student.com | Student |
| priya.sharma@student.com | Student |
| arjun.patel@student.com | Student |
| sneha.rao@student.com | Student |
| kabir.nair@student.com | Student |
| ishaan.joshi@student.com | Student |
| diya.kapoor@student.com | Student |
| vivaan.kumar@student.com | Student |
| anika.reddy@student.com | Student |
| advait.mishra@student.com | Student |
| saanvi.iyer@student.com | Student |
| reyansh.das@student.com | Student |
| myra.tiwari@student.com | Student |

---

## Usage — Quick Demo Flow

### As a teacher
1. Log in as `sharma@school.com`
2. Go to My Classrooms — two classrooms are pre-seeded
3. Open a classroom and click **Start Session**
4. Give the session a title and start it
5. You are now on the live session dashboard

### As a student (in a second browser or incognito)
1. Log in as `aarav.singh@student.com`
2. The dashboard shows a **Join Now** banner for the active session
3. Click Join Now to enter the session workspace
4. Switch to another tab — a warning toast appears
5. Switch back — the teacher dashboard shows the violation

### As admin
1. Log in as `admin@school.com`
2. Dashboard shows live stats and pending device approvals
3. Go to Audit Logs to see every action recorded in the system
4. Go to Reports for school-wide session and violation analytics

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── SessionViolationController.php
│   │   └── Teacher/SessionReportController.php
│   └── Middleware/
│       └── EnsureRole.php
├── Livewire/
│   ├── Admin/
│   │   ├── ActivityLogViewer.php
│   │   ├── AdminDashboard.php
│   │   ├── DeviceManager.php
│   │   ├── ReportsOverview.php
│   │   ├── UserManager.php
│   │   └── UserShow.php
│   ├── Student/
│   │   ├── ClassroomIndex.php
│   │   ├── ClassroomShow.php
│   │   ├── DeviceRegistration.php
│   │   ├── JoinClassroom.php
│   │   └── LiveSession.php
│   └── Teacher/
│       ├── ClassroomCreate.php
│       ├── ClassroomIndex.php
│       ├── ClassroomShow.php
│       ├── DeviceList.php
│       ├── LiveSession.php
│       ├── PolicyManager.php
│       └── SessionReport.php
└── Models/
    ├── ActivityLog.php
    ├── Classroom.php
    ├── ClassSession.php
    ├── Device.php
    ├── Policy.php
    ├── Resource.php
    ├── SessionDevice.php
    └── User.php
```

---

## Key Design Decisions

**No network-level filtering** — true OS-level content blocking requires a dedicated lockdown browser or network proxy. This system enforces focus and access control within the browser session itself, which works without any installation on student devices.

**Focus detection not prevention** — browsers cannot prevent tab switching by design. Instead the system detects it instantly via the Page Visibility API, logs it, warns the student, and alerts the teacher in real time. This creates an audit trail and a deterrent.

**Resources open inline** — links and PDFs shared by the teacher open inside the session workspace using iframes and PDF.js, so students never navigate away and no false violations are triggered. External links that cannot be embedded pause focus monitoring automatically for 30 seconds.

**Livewire polling over WebSockets** — the live session dashboard and student workspace use Livewire's built-in polling rather than a WebSocket setup. This keeps the infrastructure simple while still providing near-real-time updates every 3-5 seconds.

---

## License

This project was built as a student assignment.
