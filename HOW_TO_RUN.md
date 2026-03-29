# How to Run Mini Event Project - Complete Guide

## ✅ Quick Start (5 minutes)

### Step 1: Start Docker Containers

```bash
docker-compose up -d
```

Wait 10-15 seconds for containers to be ready.

### Step 2: Run Database Migrations

```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

### Step 3: Create Admin User

```bash
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@test.com', '[\"ROLE_ADMIN\"]', NULL);"
```

### Step 4: Access the Application

**Open in your browser:**

- 🏠 **Homepage**: http://localhost:8080/home.html ⭐ START HERE
- 📋 **Browse Events**: http://localhost:8080/index.html
- 👤 **Login**: http://localhost:8080/login.html
- 📊 **Admin Dashboard**: http://localhost:8080/admin.html
- 📧 **Test Emails**: http://localhost:8025

**Done!** 🎉

---

## 📋 Detailed Setup Steps

### Prerequisites

- Docker Desktop (must be **running**)
- Git
- Terminal/Command Prompt

### 1. Clone the Repository

```bash
git clone <repository-url>
cd miniEvent1
```

### 2. Start Docker Compose

```bash
docker-compose up -d
```

**Verify containers are running:**

```bash
docker-compose ps
```

Expected output:

```
NAME                  STATUS
mini-event-php        Up
mini-event-nginx      Up
mini-event-postgres   Up
```

### 3. Run Database Migrations

```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

You should see:

```
Migrating up to Version20260329020108
```

### 4. (Optional) Clear Cache

```bash
docker-compose exec php bin/console cache:clear
```

### 5. Create Admin User

```bash
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@test.com', '[\"ROLE_ADMIN\"]', NULL);"
```

Or use the helper script:

```bash
docker-compose exec php php create_admin.php
```

---

## 🌐 Access the Application

After setup, access via these URLs:

| Page                | URL                                   | Purpose                      |
| ------------------- | ------------------------------------- | ---------------------------- |
| **📌 Home (Start)** | http://localhost:8080/home.html       | Landing page & orientation   |
| **Events List**     | http://localhost:8080/index.html      | Browse all events            |
| **Event Details**   | http://localhost:8080/event.html?id=1 | View event & reserve         |
| **Login/Register**  | http://localhost:8080/login.html      | User authentication          |
| **Admin Dashboard** | http://localhost:8080/admin.html      | Manage events & reservations |
| **Email Testing**   | http://localhost:8025                 | View test emails (Mailpit)   |

---

## 👤 Login & Test

### Register as Admin User

1. Go to: **http://localhost:8080/login.html**
2. Click **"Inscription"** tab
3. Enter email: `admin@test.com`
4. Click **"Créer un compte avec Passkey"**
5. Wait for confirmation
6. You'll be redirected to the dashboard

### Access Admin Dashboard

- Go to: **http://localhost:8080/admin.html**
- You should see:
    - ✅ User info: "Connecté en tant que admin@test.com"
    - ✅ Event creation form
    - ✅ Events list
    - ✅ Participants management

### Create a Test Event

In the "Créer un nouvel événement" section:

- **Titre**: Test Event
- **Lieu**: Paris, France
- **Date et heure**: Any future date/time
- **Nombre de places**: 100
- **Description**: Test event description
- Click **"Créer l'événement"**

### Test Making a Reservation

1. Go to: **http://localhost:8080/index.html**
2. Click on the event you just created
3. Fill in the reservation form
4. Submit
5. Check confirmation in Mailpit (http://localhost:8025)

---

## 🛠 Useful Commands

### View Logs

```bash
# All logs
docker-compose logs -f

# PHP logs only
docker-compose logs -f php

# Nginx logs only
docker-compose logs -f nginx

# Database logs only
docker-compose logs -f postgres
```

Press `Ctrl+C` to exit logs.

### Run Tests

```bash
docker-compose exec php bin/phpunit
```

Expected: **6/6 tests passing** ✅

### Access PHP Container

```bash
docker-compose exec php bash
```

### Access Database

```bash
docker-compose exec postgres psql -U app -d app
```

### Clear Application Cache

```bash
docker-compose exec php bin/console cache:clear
```

### Check API Endpoint

```bash
curl http://localhost:8080/api/events
```

---

## ⚙️ Configuration Files

### Environment Variables (.env)

Located at: `C:\Users\mhana\miniEvent1\.env`

Key variables:

- `JWT_SECRET` - JWT authentication secret (already configured)
- `DATABASE_URL` - PostgreSQL connection string
- `MAILER_FROM` - Email sender address
- `MAILER_DSN` - Email service configuration

### Docker Compose

Located at: `docker-compose.yml`

Contains:

- **php** - PHP-FPM service (port 9000)
- **nginx** - Web server (port 8080)
- **postgres** - Database (port 5432)
- **mailpit** (in compose.override.yaml) - Email testing (port 8025)

### Nginx Configuration

Located at: `docker/nginx/default.conf`

Features:

- ✅ Security headers enabled
- ✅ Gzip compression enabled
- ✅ HTML file serving optimized
- ✅ Static asset caching configured

---

## 🚀 Advanced Commands

### Rebuild Containers

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
docker-compose exec php bin/console doctrine:migrations:migrate
```

### Reset Everything (Delete Data)

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec php bin/console doctrine:migrations:migrate
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@test.com', '[\"ROLE_ADMIN\"]', NULL);"
```

### View Database Tables

```bash
docker-compose exec postgres psql -U app -d app -c "\dt"
```

### Stop All Containers

```bash
docker-compose stop
```

### Start Containers (After Stopping)

```bash
docker-compose start
```

### Remove Everything

```bash
docker-compose down -v
```

---

## 🐛 Troubleshooting

### Issue: Containers Won't Start

```bash
# Check logs
docker-compose logs

# Rebuild and restart
docker-compose down
docker-compose up -d
```

### Issue: Database Connection Error

```bash
# Wait for PostgreSQL to start
docker-compose exec postgres pg_isready -U app

# Run migrations again
docker-compose exec php bin/console doctrine:migrations:migrate
```

### Issue: Admin Dashboard Shows "Vérification de l'authentification"

- **Already Fixed!** ✅
- Make sure you:
    1. Login first via login.html
    2. Create an admin user (see Step 5 above)
    3. Have ROLE_ADMIN in your user account

### Issue: Port 8080 Already in Use

Edit `docker-compose.yml`:

```yaml
nginx:
    ports:
        - "8081:80" # Change to different port
```

Then restart:

```bash
docker-compose restart nginx
```

### Issue: Can't Access Admin Dashboard

1. Open browser console (F12)
2. Check for error messages
3. Verify token in localStorage:
    ```javascript
    console.log(localStorage.getItem("token"));
    console.log(localStorage.getItem("user"));
    ```

### Issue: Tests are Failing

```bash
# Clear cache first
docker-compose exec php bin/console cache:clear

# Run tests again
docker-compose exec php bin/phpunit
```

---

## 📊 Testing Checklist

- [ ] Homepage loads at http://localhost:8080/index.html
- [ ] Events are displayed
- [ ] Can click on event to see details
- [ ] Can fill reservation form
- [ ] Login page works at http://localhost:8080/login.html
- [ ] Can register with email
- [ ] Can login with email
- [ ] Admin dashboard shows at http://localhost:8080/admin.html
- [ ] Can create events in admin dashboard
- [ ] Can view reservations
- [ ] Can edit/delete events
- [ ] Tests pass: `docker-compose exec php bin/phpunit`

---

## 📝 Project Status After Updates

✅ **Fixed Issues:**

1. JWT Secret Configuration - Using JWT_SECRET instead of APP_SECRET
2. Docker Environment Variables - Properly configured
3. Nginx Security Headers - Enhanced configuration
4. Admin Dashboard - Fixed authentication display logic
5. Missing showSuccess() function - Added

✅ **All Components Working:**

- Backend API (all 11+ endpoints)
- Database migrations and schema
- JWT authentication
- WebAuthn registration/login (simulated)
- Email service with Mailpit
- Unit tests (6/6 passing)
- Admin dashboard
- Event management
- Reservation management

---

## 🎯 Next Steps

### For Development:

1. Make code changes in `src/` directory
2. Frontend changes in `public/` directory
3. Clear cache if needed: `docker-compose exec php bin/console cache:clear`
4. Refresh browser to see changes

### For Production:

1. Update `.env` with production values
2. Configure real email service (SendGrid, Mailgun, etc.)
3. Enable HTTPS
4. Set `APP_ENV=prod`
5. Run: `docker-compose exec php bin/console cache:clear --env=prod`

---

## 📚 Important Files

| File                        | Purpose                     |
| --------------------------- | --------------------------- |
| `config/services.yaml`      | JWT_SECRET configuration ✅ |
| `.env`                      | Environment variables ✅    |
| `docker-compose.yml`        | Docker services ✅          |
| `docker/nginx/default.conf` | Web server config ✅        |
| `public/admin.html`         | Admin dashboard ✅          |
| `public/index.html`         | Events listing              |
| `public/login.html`         | Authentication              |
| `src/Entity/`               | Database entities           |
| `src/Controller/Api/`       | API endpoints               |
| `migrations/`               | Database schema             |

---

## 🎉 You're All Set!

Your Mini Event application is ready to use. Start with:

```bash
docker-compose up -d
docker-compose exec php bin/console doctrine:migrations:migrate
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@test.com', '[\"ROLE_ADMIN\"]', NULL);"
```

Then visit: **http://localhost:8080/home.html** to get started!

**Questions?** Check the logs with:

```bash
docker-compose logs -f php
```

---

**Happy coding!** 🚀
