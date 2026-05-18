# AEPES – Automated Employee Performance Evaluation System

AEPES (Automated Employee Performance Evaluation System) is a web-based system developed to automate and improve employee performance evaluation processes. The system streamlines submission, assessment, monitoring, and reporting of employee performance records using an integrated workflow.

The project minimizes manual paperwork and improves efficiency, transparency, and monitoring through role-based access and automated reporting.

---

## System Overview

Traditional employee evaluation processes often involve manual forms, repeated paperwork, and difficult tracking of records. AEPES centralizes the process through a role-based web application where employees, HR personnel, department heads, and auditors can access features relevant to their responsibilities.

---

## Features

### Employee Module

- Submit IPCRF forms
- View submitted performance records
- Track evaluation status
- View performance history
- Generate IPCRF documents

Directory:

```text
employee/
```

Files detected:

```text
dashboard.php
generate_ipcrf_word.php
ipcrf_form.php
ipcrf_status.php
performance_history.php
submit_ipcrf.php
view_ipcrf.php
```

---

### Human Resource Module

- Create and manage employee records
- Manage evaluation cycles
- Review audit logs
- Generate certified IPCRF reports
- Manage employee evaluations

Directory:

```text
hr/
```

Files detected include:

```text
create_employee.php
manage_ipcrf.php
certified_ipcrf.php
audit_logs.php
close_ipcrf.php
generate_certified_ipcrf_pdf.php
```

---

### Department Head Module

- Review employee submissions
- Evaluate employee performance
- View summaries
- Finalize evaluations

Directory:

```text
head/
```

Files detected include:

```text
evaluate_ipcrf.php
employees.php
evaluations.php
finalize_ipcrf.php
performance_summary.php
pending_submissions.php
```

---

### Auditor Module

- Audit trail management
- Compliance monitoring
- Review submitted records
- Track activities

Directory:

```text
auditor/
```

Files detected include:

```text
audit_logs.php
audit_trail.php
compliance_reports.php
review_ipcrf.php
submit_to_hr.php
view_ipcrf.php
```

---

## Technologies Used

Backend:

- PHP

Database:

- MySQL

Frontend:

- HTML5
- CSS3
- JavaScript

Dependency Management:

- Composer

Development Environment:

- XAMPP

---

## Project Structure

```text
AEPES/
│
├── assets/
│   └── Images, logos, system resources
│
├── auditor/
│   └── Auditor module files
│
├── employee/
│   └── Employee module files
│
├── head/
│   └── Department Head module files
│
├── hr/
│   └── Human Resource module files
│
├── includes/
│   └── Shared components
│
├── config/
│   └── Database configuration
│
├── libs/
│   └── External libraries
│
├── docs/
│   ├── screenshots/
│   └── diagrams/
│
├── database/
│   └── aepes-db.sql
│
├── composer.json
├── composer.lock
└── README.md
```

---

## Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/AEPES.git
```

---

### Step 2: Move Project

Move the project folder into:

```text
xampp/htdocs/
```

---

### Step 3: Install Dependencies

```bash
composer install
```

---

### Step 4: Create Database

Open phpMyAdmin and create:

```sql
aepes-db
```

Import:

```text
database/aepes-db.sql
```

---

### Step 5: Configure Database

Open:

```text
config/database.php
```

Configure:

```php
$host="localhost";
$user="root";
$password="";
$database="aepes-db";
```

---

### Step 6: Run System

Open:

```text
http://localhost/AEPES
```

---

## Screenshots

Add screenshots in:

```text
docs/screenshots/
```

Recommended screenshots:

- Login Page
- Dashboard
- Employee Portal
- HR Dashboard
- Department Head Dashboard
- Auditor Dashboard
- Evaluation Form
- Reports

---

## Future Improvements

- Email notifications
- Analytics dashboard
- Mobile responsiveness improvements
- Multi-department support
- Export reports to Excel
- Performance graphs and charts

---

## Authors

Developer:

**John Aldrin Anasis**

Program:

**Bachelor of Science in Computer Science (BSCS)**

---

## License

This project is intended for educational and academic purposes.

MIT License

---
