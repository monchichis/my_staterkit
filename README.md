# 🚀 CodeIgniter 3 Starter Kit with RBAC & CRUD Generator

<div align="center">

![Project Cover](assets/images/manual_book_cover.png)

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-777bb4.svg?style=flat-square)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.1.11-ef4b23.svg?style=flat-square)](https://codeigniter.com/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![RBAC](https://img.shields.io/badge/Auth-RBAC-green.svg?style=flat-square)](#)

**A powerful, production-ready Starter Kit designed to jumpstart your Web Applications.**
Equipped with Role-Based Access Control, Dynamic CRUD Generator, and a Web Installer.

[Features](#-key-features) •
[Installation](#-installation) •
[Documentation](#-documentation) •
[Contributing](#-contributing)

</div>

---

## 📖 About The Project

This **Starter Kit** is built on **CodeIgniter 3** and enhanced with a robust **Role-Based Access Control (RBAC)** system. It is designed to minimize setup time for developers by providing essential tools out of the box.

Whether you are building a simple administration panel or a complex enterprise application, this starter kit provides the solid foundation you need.

### Why use this starter kit?
*   **Save Time:** Don't reinvent the wheel. Auth, RBAC, and System Management are ready to go.
*   **Code Faster:** Use the **CRUD Generator** to create modules (Controller, Model, View) in seconds.
*   **Easy Deploy:** Comes with a **Web-Based Installer** for effortless setup on any server.
*   **Secure:** Built-in protection against common vulnerabilities, plus detailed audit logging.

## ✨ Key Features

### 🛡️ Core Security & RBAC
*   **Advanced RBAC**: Granular control with Roles, Permissions, and Modules.
*   **Menu Management**: Dynamic sidebar menus based on user permissions.
*   **Audit Log**: Track every critical action within the system.

### ⚡ Developer Tools
*   **CRUD Generator**: Generate full CRUD modules (with validation, file uploads, and relationships) instantly from your database tables.
*   **Smart Uploads**: Automatic handling of upload directories for generated modules.
*   **Database Management**: Create tables, backup database, and manage structure from the UI.

### 📊 Dashboard & Analytics
*   **Summary Widgets**: configurable widgets to show key metrics (Count, Sum, Avg).
*   **Chart Generator**: Create dynamic charts (Line, Bar, Pie) without writing code.
*   **System Monitor**: Real-time server info, internet speed test, and network diagnostic tools.

### ⚙️ System Features
*   **Environment Modes**: Switch between Development, Testing, and Production modes easily.
*   **Maintenance Mode**: One-click maintenance mode to block non-admin access.
*   **Web Installer**: User-friendly wizard for database and admin configuration.

## 🛠️ Technology Stack

*   **Framework**: CodeIgniter 3.x
*   **Language**: PHP 7.4+ (Compatible with 5.6+)
*   **Database**: MySQL / MariaDB
*   **Frontend**: Bootstrap 4, Inspinia Template, jQuery
*   **Libraries**: DomPDF (Export PDF), PhpSpreadsheet (Export Excel), Chart.js

## 🚀 Installation

### Option 1: Web Installer (Recommended)

1.  **Clone the repository** to your web server directory.
    ```bash
    git clone https://github.com/monchichis/my_staterkit.git
    ```
2.  **Access the URL** in your browser (e.g., `http://localhost/my_staterkit`).
3.  **Follow the Installation Wizard**:
    *   **Database Check**: Enter your database credentials.
    *   **Import Schema**: The system will automatically import the RBAC schema (`installer_rbac_schema.sql`).
    *   **Admin Setup**: Create your Super Admin account.
4.  **Login** and start coding!

### Option 2: Manual Installation

1.  Import `installer_rbac_schema.sql` into your database.
2.  Rename `application/config/database.php.example` to `database.php` and configure your DB credentials.
3.  Create an `application/config/installed.lock` file to lock the installer.
4.  Default Login:
    *   Email: `admin@admin.com` (or as configured)
    *   Password: `password` (or as configured)

## 📂 Project Structure

```
my_staterkit/
├── application/
│   ├── controllers/     # Application Logic & Generated CRUDs
│   ├── models/          # Database Models
│   ├── views/           # UI Templates
|   └── ...
├── assets/
│   ├── dist/            # Compiled Assets
│   ├── images/          # System Images
│   └── ...
├── uploads/             # User Uploaded Files (Smart Managed)
├── system/              # CI3 Core
└── ...
```

## 🤝 Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Support

If you encounter any issues or have questions, please check standard CodeIgniter documentation or open an issue in this repository.

---

<div align="center">
    <small>Built with ❤️ using CodeIgniter 3 Stater Kit</small>
</div>
