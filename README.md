<div align="center">

<img src="https://images.unsplash.com/photo-1576765607924-3f7b07f3f9c5?w=1200&h=400&q=85&fit=crop" width="100%" style="border-radius:12px"/>

<br/><br/>

# 🏥 HOSPITAL MANAGEMENT SYSTEM (PHP)

**A hospital web application for appointments and role-based dashboards**

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Apache](https://img.shields.io/badge/Apache-2.4-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-UI-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 👤 User Portal
- 🏠 Public landing pages (Home, About, Doctors, Departments, Press Desk, Gallery, Contact, Feedback)
- 🚨 Register & login with activation + password reset
- 📅 Book an appointment (date + time slot)
- 📋 View appointment history
- 👤 Manage profile and change password

</td>
<td width="50%">

### 🛡️ Admin Panel
| Module | Description |
|---|---|
| 📊 Dashboard | Overview cards and appointment stats |
| 📋 Appointments | View/manage appointments |
| 🚑 Doctors | Add/edit/remove doctors |
| 🏥 Departments | Add/edit departments |
| 👥 Patients | Add/edit/view patient records |
| 💬 Feedback | View and review feedback |
| 🧾 Profile | Admin profile management |

</td>
</tr>
</table>

---

## 🧑‍⚕️ Module Preview

<table>
<tr>
<td align="center" width="33%">
<img src="https://images.unsplash.com/photo-1580281658223-9b93f18ae9ae?w=400&h=220&q=80&fit=crop" width="100%" style="border-radius:10px"/>
<br/><b>Doctor Dashboard</b>
<br/><sub>Manage visits & appointments</sub>
</td>
<td align="center" width="33%">
<img src="https://images.unsplash.com/photo-1551076805-e1869033e561?w=400&h=220&q=80&fit=crop" width="100%" style="border-radius:10px"/>
<br/><b>Admin Panel</b>
<br/><sub>Doctors • Departments • Patients</sub>
</td>
<td align="center" width="33%">
<img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=220&q=80&fit=crop" width="100%" style="border-radius:10px"/>
<br/><b>User Portal</b>
<br/><sub>Appointments • Profile • History</sub>
</td>
</tr>
</table>

---

## 📸 App Screenshots

### 🏠 Home / Landing Page
<img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=900&h=450&q=80&fit=crop" width="100%" style="border-radius:10px"/>

<br/>

### 🔐 User Login / Registration
<img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?w=900&h=450&q=80&fit=crop" width="100%" style="border-radius:10px"/>

<br/>

### 📅 Appointment Booking
<img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=900&h=450&q=80&fit=crop" width="100%" style="border-radius:10px"/>

<br/>

### 📊 Admin Dashboard
<img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=900&h=450&q=80&fit=crop" width="100%" style="border-radius:10px"/>

<br/>

### 🧑‍⚕️ Doctor Dashboard
<img src="https://images.unsplash.com/photo-1584516150909-c43483ee7932?w=900&h=450&q=80&fit=crop" width="100%" style="border-radius:10px"/>

---

## ✅ Animated Demo (Optional)
These are built-in animated SVGs that you can embed locally (they will show on GitHub once committed):

<p align="left">
  <img src="readme-assets/loading-spinner.svg" width="220" alt="Loading spinner" />
  <img src="readme-assets/bounce-heart.svg" width="240" alt="Bounce heart" />
  <img src="readme-assets/appointment-pulse.svg" width="260" alt="Appointment pulse" />
</p>

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (mysqli, sessions) |
| Database | MySQL / MariaDB |
| Web Server | Apache (Laragon/XAMPP supported) |
| UI | Bootstrap + custom CSS |
| Email (OTP/Reset) | PHPMailer in `vendor/` |
| Routing | `.htaccess` + direct PHP page entry points |

---

## 📁 Project Structure

```
HOSPITAL MANAGEMENT SYSTEM USING PHP/
├── admin/                 # Admin dashboard pages
├── Doctor/                # Doctor dashboard pages
├── User/                  # User portal pages
├── Database/connection.php # DB connection (mysqli)
├── kk_patel_hospital2.sql # SQL dump for tables + sample data
├── assets2/              # Public website CSS/JS/images
└── vendor/               # PHPMailer (already included)
```

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.x
- MySQL 8.x
- Apache enabled (Laragon/XAMPP)

### Run Locally (Laragon/XAMPP)
1. Put this project folder inside your web server root (for example, Laragon `www/`).
2. Import database:
   - phpMyAdmin -> create database `KK_PATEL_HOSPITAL2`
   - Import `kk_patel_hospital2.sql`
3. Ensure DB credentials match `Database/connection.php`
   - Default in this repo: host `localhost`, user `root`, empty password, DB `KK_PATEL_HOSPITAL2`
4. Open:
   - `http://localhost/<your-folder-name>/`

---

## 🔐 Key Pages (Login)

| URL | Page |
|---|---|
| `login.php` | User Login / Register entry |
| `admin/kkadminlogin.php` | Admin Login |
| `Doctor/doctorlogin.php` | Doctor Login |
| `User/index.php` | User dashboard |
| `admin/index-2.php` | Admin dashboard |
| `Doctor/index-2.php` | Doctor dashboard |

---

## 👥 Why Choose This System?

| Feature | Description |
|---|---|
| 📅 Appointment Management | Book appointments with saved history |
| 🧑‍⚕️ Role-Based Dashboards | Separate User, Admin, Doctor workflows |
| 🛠️ DB Included | SQL dump provided in the repo |
| 🔐 Auth & Recovery | Login, activation, OTP/reset flows (via PHPMailer) |
| 💬 Feedback | Contact & feedback modules for hospital communication |

---

## 📄 License
This project is for academic purposes.

<div align="center">
<sub>Built with ❤️ using PHP · MySQL · Apache</sub>
</div>

