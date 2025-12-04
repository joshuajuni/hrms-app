# 🏢 Laravel 12 HRMS - Human Resource Management System

A comprehensive web-based Human Resource Management System built with Laravel 12, featuring employee management, leave applications, attendance tracking, and more.

![Laravel](https://img.shields.io/badge/Laravel-12.41.1-red)
![PHP](https://img.shields.io/badge/PHP-8.5.0-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-purple)

---

## ✨ Features

### 👥 **Employee Management**
- Complete employee profiles with photo upload
- Department assignment and role management
- Employee directory with search and filters
- Manager designation for departments

### 📅 **Leave Management**
- Apply for annual leave, sick leave, and emergency leave
- Leave balance tracking (14 days annual, 14 days sick)
- Manager approval workflow (department-based)
- Edit pending leave applications before approval
- Cancel approved leaves (before leave start date)
- Automatic leave calculation (excludes weekends & holidays)
- Email notifications for all leave actions
- Four leave statuses: Pending, Approved, Rejected, Cancelled

### ⏰ **Attendance System**
- Daily attendance tracking
- Check-in/check-out functionality (Web & Mobile API)
- Automatic status determination (present/late/absent)
- Late detection (after 9:00 AM)
- Monthly attendance reports
- Auto-generation for bulk periods
- Integration with leave applications

### 🎉 **Events & Holidays**
- Public holiday management (Malaysia holidays included)
- Company events calendar
- Interactive calendar view with FullCalendar
- Holiday exclusion in leave calculations
- View approved leaves in calendar (all employees)

### 🔐 **Role-Based Access Control**
- **Admin**: System administration, view all data (no leave approval rights)
- **Manager**: Department management, approve/reject department leaves
- **Employee**: Personal leave management, attendance tracking, profile updates

### 📧 **Email Notifications**
- Leave application submitted (to employee & manager)
- Leave approval required (to manager)
- Leave approved (to employee)
- Leave rejected (to employee)
- Leave cancelled (to employee)
- Beautiful HTML email templates with full details

### 📱 **RESTful API**
- Full-featured REST API for mobile applications
- Token-based authentication (Laravel Sanctum)
- Complete leave management (view, create, edit, delete, cancel)
- Check-in/check-out functionality
- Leave balance tracking
- Approved leaves calendar endpoint

### 📊 **Reporting & Analytics**
- Monthly attendance summary
- Leave usage reports by employee
- Department-wise statistics
- Activity logging for complete audit trail
- Export-ready data structure

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12.41.1
- **PHP**: 8.5.0
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5.3.2, Blade Templates
- **Authentication**: Laravel Breeze + Sanctum
- **Calendar**: FullCalendar.js
- **Icons**: Bootstrap Icons
- **Email**: Mailpit (local testing), SMTP support

---

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL 8.0 or higher
- Node.js & NPM
- Git

**For Windows Users**: We recommend [Laragon](https://laragon.org/download/) which includes all requirements.

---

## 🚀 Installation & Setup

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/laravel-hrms.git
cd laravel-hrms
```

### 2️⃣ Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3️⃣ Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4️⃣ Configure Database

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrms_db
DB_USERNAME=root
DB_PASSWORD=
```

Create database:
```sql
CREATE DATABASE hrms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5️⃣ Configure Email (Local Testing with Mailpit)

Add to `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@hrms.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Note**: If using Laragon, Mailpit is included. Access at: http://localhost:8025

### 6️⃣ Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with test data
php artisan db:seed
```

### 7️⃣ Create Storage Link
```bash
php artisan storage:link
```

### 8️⃣ Build Assets
```bash
npm run build
```

### 9️⃣ Start Development Server
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔑 Test Credentials

### Admin Account
- **Email**: admin@hrms.com
- **Password**: password
- **Access**: Full system administration (no leave approval rights)

### Manager Account
- **Email**: manager@hrms.com
- **Password**: password
- **Access**: IT Department management, approve/reject department leaves

### Employee Account
- **Email**: employee@hrms.com
- **Password**: password
- **Access**: Personal leave and attendance management

---

## 📖 User Guide

### 🎯 For Employees

#### 1. **View Dashboard**
- Login with employee credentials
- See your leave balance (Annual & Sick leave)
- View today's attendance status
- Check upcoming events/holidays
- Quick access to apply for leave

#### 2. **Apply for Leave**
1. Click **"Apply for Leave"** button on dashboard
2. Select leave type (Annual/Sick/Emergency)
3. Choose start and end dates
4. Enter reason for leave
5. Click **"Submit Application"**
6. ✅ Email notification sent to you and your manager

**Leave Calculation:**
- Automatically excludes weekends (Saturday & Sunday)
- Automatically excludes public holidays
- Shows total working days required

#### 3. **Manage Your Leave Applications**

**View Leaves:**
- Go to **Leave Applications** menu
- See all your leave applications with status
- Filter by status (Pending, Approved, Rejected, Cancelled)

**Edit Pending Leave:**
- Click **Edit** button on pending applications
- Modify dates, leave type, or reason
- Save changes (can edit until manager approves/rejects)

**Delete Pending Leave:**
- Click **Delete** button on pending applications
- Confirm deletion
- Application removed from system

**Cancel Approved Leave:**
- Click **Cancel** button on approved leaves (only if not started)
- Confirm cancellation
- Leave balance automatically restored
- Attendance records removed
- ✅ Email notification sent

**Important Rules:**
- ✅ Can edit: Only PENDING leaves
- ✅ Can delete: Only PENDING leaves
- ✅ Can cancel: Only APPROVED leaves (before start date)
- ❌ Cannot edit: APPROVED or REJECTED leaves
- ❌ Cannot cancel: APPROVED leaves after start date
- ❌ Cannot modify: REJECTED leaves (must apply new)

#### 4. **View Other Employees' Leaves (Calendar Only)**
- Go to **Events** → **Calendar View**
- See approved leaves of all employees
- Helps plan team availability
- Cannot see pending/rejected leaves of others

#### 5. **View Attendance**
1. Go to **Attendance** menu
2. Filter by date to see specific records
3. View check-in/check-out times
4. See monthly attendance summary

#### 6. **Update Profile**
1. Click your name → **"My Profile"**
2. Click **"Edit Profile"**
3. Update personal information
4. Upload profile photo (max 5MB)
5. Click **"Update Profile"**

---

### 👔 For Managers

#### 1. **Approve/Reject Leave Applications**

**View Department Leaves:**
1. Go to **Leave Applications**
2. See all leave applications from your department
3. Filter by "Pending" to see awaiting approvals

**Approve Leave:**
1. Click **"View"** on a pending application
2. Review employee details, dates, and reason
3. Click **"Approve"** button
4. Add optional approval notes
5. ✅ System automatically:
   - Deducts leave balance
   - Creates attendance records (status: on_leave)
   - Sends email to employee

**Reject Leave:**
1. Click **"View"** on a pending application
2. Click **"Reject"** button
3. **Must provide rejection reason** (required)
4. ✅ Email sent to employee with reason

**Important Notes:**
- ✅ Can only approve/reject: Department employees
- ✅ Can only approve/reject: PENDING leaves
- ❌ Cannot edit: Leave details (dates, reason)
- ❌ Cannot delete: Any leave applications

#### 2. **View Department Attendance**
1. Go to **Attendance**
2. See attendance for all department employees
3. Filter by employee and date range
4. Generate monthly reports

#### 3. **Manage Department Employees**
1. Go to **Employees**
2. View all employees in your department
3. Click employee name to view:
   - Complete profile
   - Leave balance
   - Attendance history
   - Leave application history

---

### 👨‍💼 For Admins

**Admin Role:** System administration only, no leave approval rights

#### 1. **Add New Employee**
1. Go to **Employees** → **"Add Employee"**
2. Fill in user account details (email, password, role)
3. Complete personal information
4. Select department and position
5. Upload photo (optional)
6. Set as department manager (if applicable)
7. Click **"Create Employee"**

#### 2. **Manage Departments**
1. Go to **Departments**
2. Click **"Add Department"** to create new
3. Enter department name, code, and description
4. Set department status (Active/Inactive)
5. Edit/view existing departments

**Note:** Cannot delete departments with existing employees

#### 3. **Add Events/Holidays**
1. Go to **Events** → **"Add Event"**
2. Select event type:
   - **Public Holiday** (affects leave calculation)
   - **Company Holiday**
   - **Company Event**
3. Enter title and description
4. Set start and end dates
5. Check "Affects Attendance" for holidays
6. Check "Recurring Annually" if applicable
7. Click **"Create Event"**

#### 4. **Generate Bulk Attendance**
1. Go to **Attendance**
2. Click **"Generate Attendance"**
3. Select date range
4. Choose employee (or leave blank for all employees)
5. Click **"Generate"**
6. System creates records with "absent" status
7. Automatically excludes weekends and holidays

#### 5. **View Attendance Reports**
1. Go to **Attendance** → **"View Report"**
2. Select month and year
3. Choose employee (optional for all employees)
4. See comprehensive summary:
   - Total Days
   - Present
   - Late
   - Absent
   - On Leave

#### 6. **View All Leave Applications**
- Access to view all leave applications (read-only)
- Cannot approve/reject (not a manager)
- Can view statistics and reports

---

## 📧 Testing Email Notifications

### Using Mailpit (Local - Recommended)

1. **Start Mailpit** (included with Laragon)
   - Automatically runs on port 8025

2. **Access Mailpit Web Interface**
   - Open browser: http://localhost:8025
   - You'll see all emails sent by the application

3. **Test Leave Application Flow**
```
   Step 1: Login as Employee (employee@hrms.com)
   Step 2: Apply for leave
   Step 3: Check Mailpit → See email to employee (confirmation)
   Step 4: Check Mailpit → See email to manager (approval request)
   
   Step 5: Login as Manager (manager@hrms.com)
   Step 6: Approve the leave
   Step 7: Check Mailpit → See approval email to employee
   
   Step 8: Login as Employee
   Step 9: Cancel the approved leave
   Step 10: Check Mailpit → See cancellation email to employee
```

## 🔌 API Testing

### Authentication

#### 1. **Login and Get Token**
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "employee@hrms.com",
    "password": "password"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "token": "1|abc123xyz...",
    "user": {
        "id": 3,
        "name": "Jane Employee",
        "email": "employee@hrms.com",
        "role": "employee"
    },
    "employee": {
        "id": 3,
        "employee_code": "EMP003",
        "full_name": "Jane Employee",
        "department": "IT",
        "position": "Software Developer",
        "photo": "http://localhost:8000/storage/employees/photo.jpg"
    }
}
```

#### 2. **Get Current User**
```http
GET http://localhost:8000/api/me
Authorization: Bearer {your-token}
```

#### 3. **Logout**
```http
POST http://localhost:8000/api/logout
Authorization: Bearer {your-token}
```

---

### Attendance API

#### 1. **Check In**
```http
POST http://localhost:8000/api/attendances/check-in
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "notes": "Checked in via mobile app"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Checked in successfully at 08:45 AM",
    "attendance": {
        "id": 123,
        "date": "2025-12-04",
        "check_in": "08:45:00",
        "status": "present"
    }
}
```

#### 2. **Check Out**
```http
POST http://localhost:8000/api/attendances/check-out
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "notes": "Checked out via mobile app"
}
```

#### 3. **Get Today's Attendance**
```http
GET http://localhost:8000/api/attendances/today
Authorization: Bearer {your-token}
```

**Response:**
```json
{
    "attendance": {
        "id": 123,
        "date": "2025-12-04",
        "check_in": "08:45:00",
        "check_out": "17:30:00",
        "status": "present",
        "notes": null
    },
    "can_check_in": false,
    "can_check_out": false
}
```

#### 4. **Get Attendance History**
```http
GET http://localhost:8000/api/attendances?month=12&year=2025
Authorization: Bearer {your-token}
```

---

### Leave Management API

#### 1. **Get Leave Applications**
```http
GET http://localhost:8000/api/leaves
Authorization: Bearer {your-token}
```

Optional filters:
```http
GET http://localhost:8000/api/leaves?status=pending
```

**Response:**
```json
{
    "leaves": [
        {
            "id": 1,
            "leave_type": {
                "id": 1,
                "name": "Annual Leave",
                "code": "AL"
            },
            "start_date": "2025-12-20",
            "end_date": "2025-12-22",
            "total_days": 3,
            "reason": "Family vacation",
            "status": "pending",
            "approved_at": null,
            "approval_notes": null,
            "created_at": "2025-12-04 10:30:00",
            "can_edit": true,
            "can_delete": true,
            "can_cancel": false
        }
    ]
}
```

#### 2. **View Single Leave**
```http
GET http://localhost:8000/api/leaves/1
Authorization: Bearer {your-token}
```

#### 3. **Apply for Leave**
```http
POST http://localhost:8000/api/leaves
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "leave_type_id": 1,
    "start_date": "2025-12-20",
    "end_date": "2025-12-22",
    "reason": "Family vacation"
}
```

**Response:**
```json
{
    "message": "Leave application submitted successfully. Email notification sent.",
    "leave": {
        "id": 15,
        "status": "pending",
        "total_days": 3,
        "start_date": "2025-12-20",
        "end_date": "2025-12-22"
    }
}
```

#### 4. **Edit Leave (Pending Only)**
```http
PUT http://localhost:8000/api/leaves/1
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "leave_type_id": 1,
    "start_date": "2025-12-21",
    "end_date": "2025-12-23",
    "reason": "Updated: Extended vacation"
}
```

#### 5. **Delete Leave (Pending Only)**
```http
DELETE http://localhost:8000/api/leaves/1
Authorization: Bearer {your-token}
```

**Response:**
```json
{
    "message": "Leave application deleted successfully."
}
```

#### 6. **Cancel Approved Leave (Not Started)**
```http
POST http://localhost:8000/api/leaves/2/cancel
Authorization: Bearer {your-token}
```

**Response (Success):**
```json
{
    "message": "Leave application cancelled successfully. Leave balance restored.",
    "leave": {
        "id": 2,
        "status": "cancelled",
        "restored_days": 3
    }
}
```

**Response (Error - Already Started):**
```json
{
    "message": "This leave cannot be cancelled. Either it has already started or it is not approved.",
    "status": "approved",
    "has_started": true
}
```

#### 7. **Get Leave Types**
```http
GET http://localhost:8000/api/leave-types
Authorization: Bearer {your-token}
```

#### 8. **Get Leave Balance**
```http
GET http://localhost:8000/api/leave-balance
Authorization: Bearer {your-token}
```

**Response:**
```json
{
    "annual_leave_balance": 11.00,
    "sick_leave_balance": 14.00,
    "total_leave_balance": 25.00
}
```

#### 9. **Get Approved Leaves Calendar (All Employees)**
```http
GET http://localhost:8000/api/leaves/calendar/approved
Authorization: Bearer {your-token}
```

Optional date filter:
```http
GET http://localhost:8000/api/leaves/calendar/approved?start_date=2025-12-01&end_date=2025-12-31
```

**Response:**
```json
{
    "leaves": [
        {
            "id": 5,
            "employee": {
                "id": 2,
                "full_name": "John Manager",
                "department": "IT"
            },
            "leave_type": {
                "name": "Annual Leave",
                "code": "AL"
            },
            "start_date": "2025-12-15",
            "end_date": "2025-12-17",
            "total_days": 3
        }
    ]
}
```

---

### Employee API

#### 1. **List Employees**
```http
GET http://localhost:8000/api/employees
Authorization: Bearer {your-token}
```

#### 2. **Get Employee Details**
```http
GET http://localhost:8000/api/employees/3
Authorization: Bearer {your-token}
```

---

**Use Postman or Thunder Client (VS Code Extension) for API testing.**

---

## 🗂️ Database Structure

### Tables (11 Total)

1. **users** - User accounts with authentication
2. **roles** - User roles (Admin, Manager, Employee)
3. **employees** - Complete employee profiles
4. **departments** - Company departments
5. **leave_types** - Types of leave (Annual, Sick, Emergency)
6. **leave_applications** - Leave requests with 4 statuses
7. **attendances** - Daily attendance records
8. **event_types** - Event categories
9. **events** - Holidays and company events
10. **activity_logs** - Complete audit trail
11. **notifications** - System notifications

### Key Relationships

- User → Employee (One-to-One)
- User → Role (Many-to-One)
- Employee → Department (Many-to-One)
- Employee → Leave Applications (One-to-Many)
- Employee → Attendances (One-to-Many)
- Leave Application → Leave Type (Many-to-One)
- Leave Application → Approver (Many-to-One, Employee)

---

## 📊 System Requirements Checklist

- ✅ Database management and manipulation (11 tables, relationships, CRUD)
- ✅ File/image upload (Employee photos with validation, max 5MB)
- ✅ Email notifications (Leave applications, approvals, rejections, cancellations)
- ✅ Logging (Activity logs for complete audit trail)
- ✅ Clean, responsive UI (Bootstrap 5 with custom gradient styling)
- ✅ RESTful API (Sanctum authentication, mobile-ready, full CRUD)
- ✅ Role-based access control (Admin, Manager, Employee with proper policies)

---

## 📁 Project Structure
```
hrms-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API Controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── EmployeeController.php
│   │   │   │   └── LeaveApplicationController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── DepartmentController.php
│   │   │   ├── LeaveApplicationController.php
│   │   │   ├── AttendanceController.php
│   │   │   ├── EventController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   └── CheckEmployeeProfile.php
│   │   └── Policies/
│   │       ├── EmployeePolicy.php
│   │       ├── LeaveApplicationPolicy.php
│   │       ├── AttendancePolicy.php
│   │       ├── DepartmentPolicy.php
│   │       └── EventPolicy.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Employee.php
│   │   ├── Department.php
│   │   ├── LeaveType.php
│   │   ├── LeaveApplication.php
│   │   ├── Attendance.php
│   │   ├── Event.php
│   │   ├── EventType.php
│   │   └── ActivityLog.php
│   ├── Services/
│   │   ├── LeaveService.php
│   │   └── AttendanceService.php
│   ├── Helpers/
│   │   └── ActivityLogger.php
│   └── Notifications/
│       └── LeaveApplicationNotification.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── dashboard/
│       ├── employees/
│       ├── departments/
│       ├── leaves/
│       ├── attendances/
│       ├── events/
│       └── profile/
├── routes/
│   ├── web.php
│   └── api.php
└── storage/
    └── app/public/employees/
```

---

## 🔒 Security Features

- ✅ CSRF Protection (all web forms)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade templating)
- ✅ Password Hashing (Bcrypt)
- ✅ API Token Authentication (Sanctum)
- ✅ Role-based Authorization (Laravel Policies)
- ✅ Soft Deletes (Data retention for audit)
- ✅ Activity Logging (Complete audit trail with IP tracking)
- ✅ Input Validation (Form requests)
- ✅ Rate Limiting (API endpoints)

---

## 🚀 Quick Start Commands
```bash
# Fresh installation with sample data
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Or clear individually
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Start development server
php artisan serve

# Check routes
php artisan route:list

# Run tests (if implemented)
php artisan test
```

---

## 📝 Complete API Endpoints

### Authentication
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/login` | Login and get token | No |
| POST | `/api/logout` | Logout and revoke token | Yes |
| GET | `/api/me` | Get current user info | Yes |

### Employees
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/employees` | List employees | Yes |
| GET | `/api/employees/{id}` | Get employee details | Yes |

### Attendance
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/attendances` | Get attendance history | Yes |
| GET | `/api/attendances/today` | Get today's attendance | Yes |
| POST | `/api/attendances/check-in` | Check in | Yes |
| POST | `/api/attendances/check-out` | Check out | Yes |

### Leave Applications
| Method | Endpoint | Description | Auth Required | Notes |
|--------|----------|-------------|---------------|-------|
| GET | `/api/leaves` | Get leave applications | Yes | Own leaves only |
| GET | `/api/leaves/{id}` | Get single leave | Yes | Own leaves only |
| POST | `/api/leaves` | Apply for leave | Yes | - |
| PUT | `/api/leaves/{id}` | Update leave | Yes | Pending only |
| DELETE | `/api/leaves/{id}` | Delete leave | Yes | Pending only |
| POST | `/api/leaves/{id}/cancel` | Cancel approved leave | Yes | Not started only |
| GET | `/api/leave-types` | Get leave types | Yes | - |
| GET | `/api/leave-balance` | Get leave balance | Yes | - |
| GET | `/api/leaves/calendar/approved` | Get approved leaves (all) | Yes | Calendar view |

---

## 👥 Detailed Permission Matrix

### Leave Application Permissions

| Action | Employee (Own) | Employee (Others) | Manager (Dept) | Manager (Other Dept) | Admin |
|--------|---------------|-------------------|----------------|---------------------|-------|
| **View List** | ✅ Own only | ❌ | ✅ Dept only | ❌ | ✅ All |
| **View Details** | ✅ Own | ❌ | ✅ Dept only | ❌ | ✅ All |
| **View Calendar (Approved)** | ✅ All approved | ✅ All approved | ✅ All approved | ✅ All approved | ✅ All approved |
| **Apply/Create** | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Edit** | ✅ Pending only | ❌ | ❌ | ❌ | ❌ |
| **Delete** | ✅ Pending only | ❌ | ❌ | ❌ | ❌ |
| **Cancel** | ✅ Approved (not started) | ❌ | ❌ | ❌ | ❌ |
| **Approve** | ❌ | ❌ | ✅ Pending only | ❌ | ❌ |
| **Reject** | ❌ | ❌ | ✅ Pending only | ❌ | ❌ |

### Other Module Permissions

| Feature | Admin | Manager | Employee |
|---------|-------|---------|----------|
| **Dashboard** | ✅ Full stats | ✅ Dept stats | ✅ Own stats |
| **Employees - View** | ✅ All | ✅ Dept only | ❌ |
| **Employees - Create** | ✅ | ❌ | ❌ |
| **Employees - Edit** | ✅ | ❌ | Own profile only |
| **Employees - Delete** | ✅ | ❌ | ❌ |
| **Departments - View** | ✅ | ✅ | ✅ |
| **Departments - Manage** | ✅ | ❌ | ❌ |
| **Attendance - View** | ✅ All | ✅ Dept | Own only |
| **Attendance - Create** | ✅ | ✅ | ❌ |
| **Attendance - Edit** | ✅ | ❌ | ❌ |
| **Attendance - Generate** | ✅ | ❌ | ❌ |
| **Attendance - Check-in/out** | ✅ | ✅ | ✅ |
| **Events - View** | ✅ | ✅ | ✅ |
| **Events - Manage** | ✅ | ❌ | ❌ |
| **Reports** | ✅ All | ✅ Dept | Own only |

---

## 🔄 Complete System Workflows

### Leave Application Workflow (Detailed)
```
1. Employee applies for leave
   Status: PENDING
   ↓
2. System validates:
   - Sufficient leave balance?
   - No overlapping leaves?
   - Excludes weekends & holidays
   ↓
3. Email notifications sent:
   - To Employee: "Application submitted"
   - To Manager: "New application requires approval"
   ↓
4. Employee can:
   - Edit (change dates, type, reason)
   - Delete (remove application)
   ↓
5. Manager reviews and decides:
   
   Option A: APPROVE
   - Leave balance deducted
   - Attendance records created (status: on_leave)
   - Email sent to employee
   Status: APPROVED
   
   Employee can now:
   - Cancel (if not started yet)
     → Restores leave balance
     → Removes attendance records
     → Status changes to CANCELLED
   
   Option B: REJECT
   - Must provide reason
   - No balance deduction (never deducted)
   - Email sent to employee with reason
   Status: REJECTED
   
   Employee must:
   - Apply new leave (cannot edit rejected)
```

### Attendance Workflow (Detailed)
```
1. Employee checks in (Web/Mobile API)
   ↓
2. System validates:
   - Not a holiday?
   - Not on approved leave?
   - Not already checked in today?
   ↓
3. Determines status:
   - Before 9:00 AM → "present"
   - After 9:00 AM → "late"
   ↓
4. Records check-in time
   ↓
5. Activity logged (audit trail)
   ↓
6. Employee checks out
   ↓
7. System validates:
   - Already checked in?
   - Not already checked out?
   ↓
8. Records check-out time
   ↓
9. Attendance record complete
```

### Email Notification Workflow
```
Trigger Events:
├── Leave Applied
│   └── Sends to: Employee (confirmation) + Manager (approval request)
├── Leave Approved
│   └── Sends to: Employee (approval confirmation)
├── Leave Rejected
│   └── Sends to: Employee (rejection notice with reason)
└── Leave Cancelled
    └── Sends to: Employee (cancellation confirmation)

Each email includes:
- Employee details
- Leave type and dates
- Total days
- Current status
- Relevant notes
- Action button (link to view)
```

---

## 📈 Future Enhancements

Potential features for future versions:

- [ ] Payroll integration with leave deductions
- [ ] Document management (upload contracts, certificates)
- [ ] Performance reviews and appraisals
- [ ] Shift scheduling and roster management
- [ ] Mobile app (React Native / Flutter)
- [ ] Real-time notifications (Pusher/WebSockets)
- [ ] Advanced reporting with charts (Chart.js)
- [ ] Export to Excel/PDF (Laravel Excel)
- [ ] Multi-language support (i18n)
- [ ] Dark mode theme
- [ ] Two-factor authentication (2FA)
- [ ] Biometric attendance (fingerprint/face recognition)
- [ ] Leave carry-forward policy
- [ ] Overtime tracking
- [ ] Training and development module

---
