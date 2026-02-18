# 🌱 SmartFarm — Smart Irrigation System

A full-stack web application for **smart irrigation management** built with PHP, MySQL, and JavaScript. The platform helps farmers optimize water usage through automated scheduling, real-time weather data, water usage analytics, and an AI-powered agricultural chatbot.

---

## ✨ Features

### 🌾 Farmer Dashboard
- **Irrigation Zone Management** — View/toggle irrigation zones on or off
- **Schedule Irrigation** — Create, view, and delete time-based irrigation schedules
- **Water Usage Analytics** — Interactive charts showing daily water consumption trends
- **Weather Integration** — Real-time weather data via OpenWeatherMap API
- **Download Reports** — Export irrigation and water usage data as downloadable reports

### 🏭 Manufacturer Dashboard
- Product and equipment management interface
- Profile management with password update

### 🔧 Service Provider Dashboard
- Service listings and management
- Profile management with password update

### 🤖 AI Chatbot
- Powered by **Google Gemini API**
- Agriculture-focused assistant (crop advice, irrigation tips, pest control)
- Bilingual support (Hindi + English)
- Embedded as an iframe popup across all pages

### 🔐 Authentication & Security
- Role-based login/registration (Farmer, Manufacturer, Service Provider)
- Password hashing with `password_hash()` / `password_verify()`
- Prepared statements (SQL injection protection)
- Welcome & login notification emails via **PHPMailer** (Gmail SMTP)
- All API keys and credentials stored in `.env` (never exposed to browser)
- Server-side API proxies for weather and chatbot (keys hidden from frontend)

---

## 📁 Project Structure

```
SmartFarm/
├── auth/                    # Authentication
│   ├── login.php            # Login & registration page
│   ├── auth.php             # Form handler (login/register logic)
│   └── logout.php           # Session destroy & redirect
│
├── chatbot/                 # AI Chatbot module
│   ├── chatbot.html         # Chatbot UI (embedded via iframe)
│   ├── chatbot.css          # Chatbot styles
│   ├── chatbot.js           # Frontend chat logic
│   ├── chatbot_api.php      # Server-side Gemini API proxy
│   └── upload.php           # File upload handler
│
├── css/                     # Stylesheets
│   ├── farmer.css           # Farmer dashboard styles
│   ├── manufacturer.css     # Manufacturer dashboard styles
│   ├── service.css          # Service provider dashboard styles
│   ├── login.css            # Login page styles
│   ├── hamburger.css        # Mobile hamburger menu
│   └── index.css            # Landing page styles
│
├── src/                     # PHPMailer library
│   ├── PHPMailer.php
│   ├── SMTP.php
│   ├── Exception.php
│   └── ...
│
├── media/                   # Images, videos, uploads
│
├── .env                     # Environment variables (secrets)
├── .htaccess                # Apache security rules
├── .gitignore               # Git exclusions
├── env_loader.php           # Native PHP .env parser
├── db.php                   # Database singleton (auto-init)
├── connect.php              # DB connection wrapper
├── queries                  # SQL schema + seed data
├── seed.php                 # Database seeder script
│
├── index.php                # Landing page
├── farmer.php               # Farmer dashboard
├── farmerIrrigation.php     # Irrigation management page
├── farmerWater.php          # Water usage analytics page
├── farmerProfile.php        # Farmer profile page
├── manufacturer.php         # Manufacturer dashboard
├── manuProfile.php          # Manufacturer profile page
├── service.php              # Service provider dashboard
├── serviceProfile.php       # Service provider profile page
│
├── weather_api.php          # Server-side OpenWeatherMap proxy
├── download_report.php      # CSV report generator
├── update_profile.php       # Profile update handler
├── submit_feedback.php      # Feedback form handler
├── getDashboardWaterData.php # Dashboard chart data API
├── getWaterData.php         # Water analytics data API
├── getSchedule.php          # Irrigation schedule API
├── getZones.php             # Irrigation zones API
├── addSchedule.php          # Add schedule API
├── deleteSchedule.php       # Delete schedule API
├── updateZoneStatus.php     # Toggle zone status API
│
├── FAQs.html                # FAQ page
├── Feedback.html            # Feedback form page
└── index.js                 # Landing page scripts
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Frontend** | HTML, CSS, JavaScript, Tailwind CSS (CDN) |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL (via XAMPP) |
| **Email** | PHPMailer (Gmail SMTP) |
| **AI Chatbot** | Google Gemini API |
| **Weather** | OpenWeatherMap API |
| **Charts** | Chart.js |
| **Icons** | Font Awesome 6 |

---

## ⚡ Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)
- PHP 7.4 or higher
- A Gmail account with [App Password](https://myaccount.google.com/apppasswords) for email
- API keys:
  - [OpenWeatherMap](https://openweathermap.org/api) (free tier)
  - [Google Gemini](https://ai.google.dev/) (free tier)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/ananyasingh207/SmartFarm.git
```

### 2. Place in XAMPP

Move or clone the project into your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\SmartFarm
```

### 3. Configure Environment

Edit the `.env` file in the project root with your credentials:

```env
GEMINI_API_KEY=your_gemini_api_key
OPENWEATHER_API_KEY=your_openweathermap_key
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_FROM_NAME=Smart Irrigation
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=irrigation
```

### 4. Start XAMPP

- Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 5. Open in Browser

```
http://localhost/SmartFarm/
```

The database and tables are **automatically created** on first load via `db.php`. No manual SQL setup needed.

### 6. Seed Sample Data (Optional)

To populate the database with sample irrigation data, visit:

```
http://localhost/SmartFarm/seed.php
```

---

## 🔒 Security

- **No API keys in JavaScript** — all external API calls go through server-side PHP proxies
- **`.env` protected** — blocked from browser access via `.htaccess`
- **`.env` excluded** from Git via `.gitignore`
- **Passwords hashed** using PHP's `password_hash()` with `PASSWORD_DEFAULT`
- **SQL injection prevented** with prepared statements (`bind_param`)
- **Session-based authentication** with role checks on every protected page

---

## 👥 User Roles

| Role | Dashboard | Features |
|---|---|---|
| **Farmer** | `farmer.php` | Irrigation, water analytics, weather, reports, chatbot |
| **Manufacturer** | `manufacturer.php` | Equipment management, chatbot |
| **Service Provider** | `service.php` | Service management, chatbot |

---

## 📄 License

This project is for educational purposes.
