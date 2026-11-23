🌍 TravelExplorer – Travel & Tourism Website

A dynamic Travel & Tourism website developed using **PHP and MySQL**. The project allows users to explore tourist destinations, view destination details with image sliders, and submit travel enquiries. It includes an admin panel for managing destinations and customer enquiries.

## 📌 Project Overview

TravelExplorer is a database-driven travel website designed to provide users with an interactive platform for exploring travel destinations. The website uses PHP for backend processing and MySQL for data storage.

## ✨ Features

- Responsive travel website UI
- Dynamic destination listing from MySQL
- Country-wise destination options
- Destination search and filtering
- Destination details page
- Bootstrap image carousel for destination photos
- Travel enquiry form
- Enquiry data stored in MySQL
- Admin login with session authentication
- Admin dashboard
- Add, edit and delete destinations
- Upload destination images
- View customer enquiries
- Secure database queries using prepared statements
- Password hashing for admin authentication

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Backend development |
| MySQL | Database management |
| HTML5 | Website structure |
| CSS3 | Styling |
| JavaScript | Interactive functionality |
| Bootstrap 5 | Responsive design |
| XAMPP | Local development server |
| phpMyAdmin | Database administration |

## 📂 Project Structure

```text
travel_website/
│
├── admin/
│   ├── login.php
│   ├── auth.php
│   ├── index.php
│   ├── destinations.php
│   ├── add_destination.php
│   ├── edit_destination.php
│   ├── destination_images.php
│   └── enquiries.php
│
├── includes/
│   └── navbar.php
│
├── assets/
│   └── images/
│
├── db.php
├── index.php
├── destinations.php
├── destination_details.php
├── submit_enquiry.php
├── style.css
├── travel_db.sql
└── README.md
```

## 🗄️ Database Structure

Database name: `travel_db`

### Tables

- **destinations** – Stores destination information, prices, locations, descriptions and main images.
- **destination_images** – Stores multiple images for destination image sliders.
- **enquiries** – Stores customer travel enquiries.
- **admins** – Stores admin authentication information.

## 🌐 Website Workflow

1. User opens the TravelExplorer website.
2. User browses destinations by country.
3. User selects a destination.
4. Destination details and image slider are displayed.
5. User fills out the travel enquiry form.
6. PHP validates and processes the submitted form.
7. The enquiry is stored in the MySQL database.
8. Admin logs into the dashboard to manage destinations and view enquiries.

## 🔐 Security Features

- PHP sessions for admin authentication
- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- MySQL prepared statements to prevent SQL injection
- Input validation
- Output escaping using `htmlspecialchars()`

## 🚀 Installation & Setup

### 1. Install XAMPP

Install XAMPP and start:

- Apache
- MySQL

### 2. Clone the Repository

```bash
git clone https://github.com/YOUR-USERNAME/travel_website.git
```

Or download the repository as ZIP and extract it.

### 3. Move the Project

Place the project folder inside:

```text
C:\xampp\htdocs\
```

### 4. Create the Database

Open:

```text
http://localhost/phpmyadmin/
```

Create/import the database using the provided SQL file `travel_db.sql`.

### 5. Configure Database Connection

Check `db.php` and configure your local MySQL credentials if required.

### 6. Run the Website

Open your browser:

```text
http://localhost/travel_website/
```

### 7. Admin Dashboard

```text
http://localhost/travel_website/admin/login.php
```

Use the admin credentials configured locally.

## 📸 Project Modules

- Home page
- Destination browsing
- Destination details and image slider
- Travel enquiry management
- Admin dashboard
- Destination management

## 🎓 Academic Project

**Project Title:** Travel Website – Backend Development using PHP & MySQL

**Course:** Web Development

**Project Type:** Major Project

## 👩‍💻 Developed By

Computer Science and Engineering Student

## 📄 License

This project was developed for educational purposes.
