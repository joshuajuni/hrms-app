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
- Manager approval workflow
- Automatic leave calculation (excludes weekends & holidays)
- Email notifications for all leave actions

### ⏰ **Attendance System**
- Daily attendance tracking
- Check-in/check-out functionality
- Automatic status determination (present/late/absent)
- Late detection (after 9:00 AM)
- Monthly attendance reports
- Auto-generation for bulk periods

### 🎉 **Events & Holidays**
- Public holiday management
- Company events calendar
- Calendar view with FullCalendar
- Holiday exclusion in leave calculations

### 🔐 **Role-Based Access Control**
- **Admin**: Full system access, manage all employees and departments
- **Manager**: Manage department employees, approve/reject leaves
- **Employee**: Apply for leave, view own attendance, update profile

### 📧 **Email Notifications**
- Leave application submitted (to employee)
- Leave approval required (to manager)
- Leave approved/rejected (to employee)
- Beautiful HTML email templates

### 📱 **RESTful API**
- Authentication with Laravel Sanctum
- Mobile-ready endpoints
- Check-in/check-out via API
- Leave application via API
- Token-based security

### 📊 **Reporting & Analytics**
- Monthly attendance summary
- Leave usage reports
- Department-wise statistics
- Activity logging for audit trail

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12.41.1
- **PHP**: 8.5.0
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5.3.2, Blade Templates
- **Authentication**: Laravel Breeze + Sanctum
- **Calendar**: FullCalendar.js
- **Icons**: Bootstrap Icons
- **Email**: Mailpit (for local testing)

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
- **Access**: Full system control

### Manager Account
- **Email**: manager@hrms.com
- **Password**: password
- **Access**: IT Department management

### Employee Account
- **Email**: employee@hrms.com
- **Password**: password
- **Access**: Personal leave and attendance

---

## 📖 User Guide

### 🎯 For Employees

#### 1. **View Dashboard**
- Login with employee credentials
- See your leave balance (Annual & Sick leave)
- View today's attendance status
- Check upcoming events/holidays

#### 2. **Apply for Leave**
1. Click **"Apply for Leave"** button on dashboard
2. Select leave type (Annual/Sick/Emergency)
3. Choose start and end dates
4. Enter reason for leave
5. Click **"Submit Application"**
6. ✅ Email notification sent to you and your manager

#### 3. **View Attendance**
1. Go to **Attendance** menu
2. Filter by date to see specific records
3. View check-in/check-out times
4. See monthly attendance summary

#### 4. **Update Profile**
1. Click your name → **"My Profile"**
2. Click **"Edit Profile"**
3. Update personal information
4. Upload profile photo (max 5MB)
5. Click **"Update Profile"**

---

### 👔 For Managers

#### 1. **Approve/Reject Leave**
1. Go to **Leave Applications**
2. Filter by "Pending" status
3. Click **"View"** on any application
4. Review details and reason
5. Click **"Approve"** or **"Reject"**
6. Add approval notes (optional for approve, required for reject)
7. ✅ Email notification sent to employee

#### 2. **View Department Attendance**
1. Go to **Attendance**
2. Select employee from dropdown
3. Choose date range
4. View attendance report

#### 3. **Manage Department Employees**
1. Go to **Employees**
2. View all employees in your department
3. Click employee name to view full profile
4. See leave balance and attendance history

---

### 👨‍💼 For Admins

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
4. Click **"Create Department"**
5. Edit/delete existing departments

#### 3. **Add Events/Holidays**
1. Go to **Events** → **"Add Event"**
2. Select event type (Public Holiday, Company Holiday, Company Event)
3. Enter title and description
4. Set start and end dates
5. Check "Affects Attendance" for holidays
6. Check "Recurring Annually" if applicable
7. Click **"Create Event"**

#### 4. **Generate Bulk Attendance**
1. Go to **Attendance**
2. Click **"Generate Attendance"**
3. Select date range
4. Choose employee (or leave blank for all)
5. Click **"Generate"**
6. Records created with "absent" status (excludes weekends/holidays)

#### 5. **View Attendance Reports**
1. Go to **Attendance** → **"View Report"**
2. Select month and year
3. Choose employee (optional)
4. See summary: Present, Late, Absent, On Leave

---

## 📧 Testing Email Notifications

### Using Mailpit (Local - Recommended)

1. **Start Mailpit** (included with Laragon)
   - Automatically runs on port 8025

2. **Access Mailpit Web Interface**
   - Open browser: http://localhost:8025
   - You'll see all emails sent by the application

3. **Test Email Flow**
```
   Step 1: Login as Employee (employee@hrms.com)
   Step 2: Apply for leave
   Step 3: Check Mailpit → See email to employee
   Step 4: Check Mailpit → See email to manager
   
   Step 5: Login as Manager (manager@hrms.com)
   Step 6: Approve the leave
   Step 7: Check Mailpit → See approval email to employee
```

### Using Real Email (Gmail - Optional)

1. **Update `.env`**
```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="your-email@gmail.com"
   MAIL_FROM_NAME="${APP_NAME}"
```

2. **Get Gmail App Password**
   - Go to Google Account → Security
   - Enable 2-Step Verification
   - Generate App Password
   - Use that password in `.env`

3. **Clear Cache**
```bash
   php artisan config:clear
   php artisan cache:clear
```

4. **Test Again**
   - Apply for leave
   - **Check your inbox** (might go to spam first time)
   - Check spam/promotions folder if not in inbox

---

## 🔌 API Testing

### 1. **Get Access Token**
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
    "user": {...},
    "employee": {...}
}
```

### 2. **Get Current User**
```http
GET http://localhost:8000/api/me
Authorization: Bearer 1|abc123xyz...
```

### 3. **Check In (Mobile)**
```http
POST http://localhost:8000/api/attendances/check-in
Authorization: Bearer 1|abc123xyz...
Content-Type: application/json

{
    "notes": "Checked in via mobile app"
}
```

### 4. **Check Out (Mobile)**
```http
POST http://localhost:8000/api/attendances/check-out
Authorization: Bearer 1|abc123xyz...
Content-Type: application/json

{
    "notes": "Checked out via mobile app"
}
```

### 5. **Apply for Leave (Mobile)**
```http
POST http://localhost:8000/api/leaves
Authorization: Bearer 1|abc123xyz...
Content-Type: application/json

{
    "leave_type_id": 1,
    "start_date": "2025-12-10",
    "end_date": "2025-12-12",
    "reason": "Personal matters"
}
```

### 6. **Get Leave Balance**
```http
GET http://localhost:8000/api/leave-balance
Authorization: Bearer 1|abc123xyz...
```

**Use Postman or Thunder Client (VS Code Extension) for API testing.**

---

## 🗂️ Database Structure

### Tables (11 Total)

1. **users** - User accounts
2. **roles** - User roles (Admin, Manager, Employee)
3. **employees** - Employee profiles
4. **departments** - Company departments
5. **leave_types** - Types of leave
6. **leave_applications** - Leave requests
7. **attendances** - Daily attendance records
8. **event_types** - Event categories
9. **events** - Holidays and events
10. **activity_logs** - Audit trail
11. **notifications** - System notifications

### Key Relationships

- User → Employee (One-to-One)
- Employee → Department (Many-to-One)
- Employee → Leave Applications (One-to-Many)
- Employee → Attendances (One-to-Many)
- Leave Application → Leave Type (Many-to-One)

---

## 🐛 Troubleshooting

### Issue: "SQLSTATE[HY000] [2002] Connection refused"
**Solution**: Make sure MySQL is running
```bash
# Check MySQL status in Laragon
# Or restart Laragon
```

### Issue: "Class 'PDO' not found"
**Solution**: Enable PHP extensions
```ini
# In php.ini, uncomment:
extension=pdo_mysql
```

### Issue: "Storage link not found"
**Solution**: Create storage link
```bash
php artisan storage:link
```

### Issue: "Emails not sending"
**Solution**: Check Mailpit is running
- Access: http://localhost:8025
- If using Laragon, Mailpit starts automatically

### Issue: "PHP 8.5 Deprecation Warnings"
**Solution**: Already fixed in `config/database.php`

---

## 📊 System Requirements Checklist

- ✅ Database management and manipulation (11 tables, relationships, CRUD)
- ✅ File/image upload (Employee photos with validation)
- ✅ Email notifications (Leave applications, approvals, rejections)
- ✅ Logging (Activity logs for audit trail)
- ✅ Clean, responsive UI (Bootstrap 5 with custom styling)
- ✅ RESTful API (Sanctum authentication, mobile-ready)
- ✅ Role-based access control (Admin, Manager, Employee)

---

## 📁 Project Structure
```
hrms-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API Controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── LeaveApplicationController.php
│   │   │   └── ...
│   │   ├── Middleware/           # Custom middleware
│   │   └── Policies/             # Authorization policies
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic
│   ├── Helpers/                  # Helper classes
│   └── Notifications/            # Email notifications
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   └── views/                    # Blade templates
├── routes/
│   ├── web.php                   # Web routes
│   └── api.php                   # API routes
└── storage/
    └── app/public/employees/     # Employee photos
```

---

## 🔒 Security Features

- ✅ CSRF Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade templating)
- ✅ Password Hashing (Bcrypt)
- ✅ API Token Authentication (Sanctum)
- ✅ Role-based Authorization (Policies)
- ✅ Soft Deletes (Data retention)
- ✅ Activity Logging (Audit trail)

---

## 🚀 Quick Start Commands
```bash
# Fresh installation
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Start development server
php artisan serve

# Run tests
php artisan test

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
```

---

## 📝 API Endpoints

### Authentication
- `POST /api/login` - Login and get token
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user

### Employees
- `GET /api/employees` - List employees
- `GET /api/employees/{id}` - Get employee details

### Attendance
- `GET /api/attendances` - Get attendance history
- `GET /api/attendances/today` - Get today's attendance
- `POST /api/attendances/check-in` - Check in
- `POST /api/attendances/check-out` - Check out

### Leave Applications
- `GET /api/leaves` - Get leave applications
- `POST /api/leaves` - Apply for leave
- `GET /api/leave-types` - Get leave types
- `GET /api/leave-balance` - Get leave balance

---

## 🎨 Screenshots Guide

To see the application in action:

1. **Login Page**: Clean, modern authentication
2. **Admin Dashboard**: Comprehensive statistics and quick actions
3. **Employee Dashboard**: Personal leave balance and attendance
4. **Leave Application**: Simple, intuitive form with validation
5. **Attendance Tracking**: Visual status indicators and reports
6. **Calendar View**: Interactive event calendar
7. **Profile Management**: Photo upload and personal details

---

## 👥 Team Roles & Permissions

| Feature | Admin | Manager | Employee |
|---------|-------|---------|----------|
| View Dashboard | ✅ | ✅ | ✅ |
| Manage Employees | ✅ | ✅ (Dept) | ❌ |
| Manage Departments | ✅ | ❌ | ❌ |
| Apply Leave | ✅ | ✅ | ✅ |
| Approve/Reject Leave | ✅ | ✅ (Dept) | ❌ |
| View All Attendance | ✅ | ✅ (Dept) | Own Only |
| Generate Attendance | ✅ | ❌ | ❌ |
| Manage Events | ✅ | ❌ | ❌ |
| View Reports | ✅ | ✅ (Dept) | Own Only |

---

## 🔄 System Workflow

### Leave Application Workflow
```
Employee applies for leave
    ↓
Email sent to Employee (confirmation)
    ↓
Email sent to Manager (approval request)
    ↓
Manager reviews application
    ↓
Manager approves/rejects
    ↓
Leave balance deducted (if approved)
    ↓
Email sent to Employee (result)
    ↓
Attendance auto-updated (if approved)
```

### Attendance Workflow
```
Employee checks in (Web/Mobile)
    ↓
System validates (not holiday, not on leave)
    ↓
Determines status (present/late based on time)
    ↓
Records check-in time
    ↓
Activity logged
    ↓
Employee checks out
    ↓
System records check-out time
    ↓
Attendance record complete
```

---

## 📞 Support & Contact

For issues, questions, or contributions:

- **GitHub Issues**: [Create an issue](https://github.com/YOUR_USERNAME/laravel-hrms/issues)
- **Email**: your-email@example.com
- **Documentation**: This README file

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Acknowledgments

- **Laravel Framework** - The PHP framework for web artisans
- **Bootstrap** - The world's most popular front-end toolkit
- **FullCalendar** - The most popular JavaScript calendar
- **Laravel Breeze** - Minimal, simple authentication scaffolding
- **Laravel Sanctum** - API authentication made simple

---

## 📈 Future Enhancements

Potential features for future versions:

- [ ] Payroll integration
- [ ] Document management
- [ ] Performance reviews
- [ ] Shift scheduling
- [ ] Mobile app (React Native)
- [ ] Real-time notifications (Pusher)
- [ ] Advanced reporting (Charts.js)
- [ ] Export to Excel/PDF
- [ ] Multi-language support
- [ ] Dark mode theme

---

**Built with ❤️ using Laravel 12**

---