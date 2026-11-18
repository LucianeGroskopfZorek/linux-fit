# 🏋️ Linux Fit

> A gym website built with Laravel — class schedules, plans, teachers, class booking, BMI calculator and a student area with registration and login.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)

---

## 📋 About the Project

Linux Fit started as a front-end landing page for a fictional gym and evolved into a full-stack Laravel application, developed as part of my studies at **SENAC-SC**.

The project practices routes, controllers, Blade views, form validation, database access and containerization with Docker.

---

## ✨ Features

- 🏠 **Public pages** — home, about, modalities, schedules, plans and teachers
- 💳 **Plans** — plan list and detail page for each plan
- 🗓️ **Class booking** — book and cancel classes
- ⚖️ **BMI calculator** — body mass index calculation
- 📬 **Contact form** — message sending by e-mail
- 🎓 **Student area** — registration with password, login, personal page and logout
- 🔒 **Security** — hashed passwords, CSRF protection and login attempt limit

---

## 🎓 Student Area

| Route          | Method | Description                                |
| -------------- | ------ | ------------------------------------------ |
| `/alunos`      | GET    | Student registration form                  |
| `/alunos`      | POST   | Saves the student (password stored hashed) |
| `/login`       | GET    | Login form                                 |
| `/login`       | POST   | Authenticates (limited to 5 tries/minute)  |
| `/minha-area`  | GET    | Personal page (only for logged-in students)|
| `/sair`        | POST   | Logout                                     |

---

## ⚙️ Tech Stack

| Technology | Usage                                  |
| ---------- | -------------------------------------- |
| Laravel    | Routes, controllers, Blade views       |
| PHP        | Back-end language                      |
| MySQL      | Database (`academia`)                  |
| Docker     | Container to run the application       |
| HTML/CSS/JS| Front-end                              |

---

## 🚀 How to Run Locally

**Requirements:** Docker Desktop and MySQL running on the host machine.

```bash
# 1. Clone the repository
git clone https://github.com/LucianeGroskopfZorek/linux-fit.git
cd linux-fit

# 2. Create your environment file
cp .env.example .env
# Edit .env and set the database data (DB_DATABASE=academia, DB_USERNAME, DB_PASSWORD)

# 3. Start the container
docker compose up -d
```

Then create the database and the students table in MySQL:

```sql
CREATE DATABASE academia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE academia.alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NULL,
    password VARCHAR(255) NULL
);
```

Open **http://localhost:8000** in your browser.

> ⚠️ Never commit your `.env` file. It contains passwords.

---

## 👩‍💻 About

Built by **Luciane Groskopf Zorek** as part of the SENAC-SC program.  
Part of a growing portfolio of front-end and full-stack projects.

🔗 [GitHub Profile](https://github.com/LucianeGroskopfZorek)