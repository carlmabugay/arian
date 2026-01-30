# Arian
#### Demo URL: https://arian.carlmabugay.dev/

**Arian** is a modern **Asset & Inventory Management System** built for small to mid-sized teams that want to move away from spreadsheets and manual tracking.

It is designed as a **real-world internal tool**, focusing on clarity, auditability, and ease of use — the kind of system companies actually pay for.

---

## Features

### Asset Management
- Create and manage assets (equipment, devices, tools, etc.)
- Categorize assets
- Track serial numbers and statuses (available, assigned, retired)

### Assignment & Tracking
- Assign assets to employees
- Return and reassign assets
- Full assignment history per asset

### Employee Management
- Manage employees and departments
- Role-based access control

### Audit Logs
- Track who did what and when
- Transparent history for accountability

### Reports
- Assets by status
- Assets per employee
- Exportable reports (CSV/PDF-ready)

---

## Tech Stack

- **Laravel**
- **Filament** (Admin Panel)
- **Livewire** (Interactive UI)
- **MySQL**
- **Tailwind CSS**

This project intentionally avoids over-engineering and focuses on **production-ready patterns** commonly used in B2B systems.

---

## Purpose

This project serves as:
- A **portfolio demo** showcasing real-world system design
- A reference implementation for internal tools
- A foundation that can be extended into:
    - HR systems
    - Inventory management
    - SaaS / multi-tenant platforms

---

## Getting Started

### Requirements
- Docker
- Git

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/arian.git
cd arian

# Copy example environment file.
cp .env.example .env


# Build and start containers (app, nginx, mysql)
docker compose build

# Generate Key
docker compose exec app php artisan key:generate
Note: copy the key on your .env file

# Run Laravel migrations
docker compose exec app php artisan migrate --seed

# Install Node pacakges & build assets (for hot reload):
npm install
npm run dev


# Access the App
http://localhost:8000
