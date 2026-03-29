# Mini Event - Event Reservation Management System

A modern web application for managing event reservations with WebAuthn (Passkey) authentication and a professional admin dashboard.

## ⚡ Quick Start (TL;DR)

**Windows PowerShell**:

```powershell
# 1. Clone and navigate
git clone <repository-url>
cd miniEvent1

# 2. Set up environment
cp .env .env.local
# Edit .env.local with your settings

# 3. Generate JWT keys
New-Item -ItemType Directory -Force -Path var/jwt
openssl genpkey -out var/jwt/private.pem -aes256 -algorithm RSA -pkeyopt rsa_keygen_bits:4096
openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout

# 4. Start Docker containers
docker-compose up -d

# 5. Install dependencies and setup database
docker-compose exec php composer install
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

# 6. Create admin user
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \`"user\`" (email, roles, password) VALUES ('admin@example.com', '[\`"ROLE_ADMIN\`"]', NULL);"

# 7. Access the application
# Open http://localhost:8080/home.html in your browser (NOT just localhost:8080)
```

**Linux/Mac**:

```bash
# 1. Clone and navigate
git clone <repository-url>
cd miniEvent1

# 2. Set up environment
cp .env .env.local
# Edit .env.local with your settings

# 3. Generate JWT keys
mkdir -p var/jwt
openssl genpkey -out var/jwt/private.pem -aes256 -algorithm RSA -pkeyopt rsa_keygen_bits:4096
openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout

# 4. Start Docker containers
docker-compose up -d

# 5. Install dependencies and setup database
docker-compose exec php composer install
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

# 6. Create admin user
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@example.com', '[\"ROLE_ADMIN\"]', NULL);"

# 7. Access the application
# Open http://localhost:8080/home.html in your browser (NOT just localhost:8080)
```

**Done!** 🎉 Visit **http://localhost:8080/home.html** to see your application running.

---

## 🎯 Overview

Mini Event is a full-stack event management system that allows users to browse and book events while providing administrators with a secure dashboard to manage events and reservations. The application features modern UI design with glassmorphism effects, real event images, and smooth animations.

## ✨ Key Features

### For Users

- **Event Discovery**: Browse events with advanced filters (search, location, date)
- **Modern UI**: Professional design with gradient backgrounds and real images from Unsplash
- **Easy Booking**: Simple reservation process with email confirmation
- **WebAuthn Login**: Secure passwordless authentication using Passkey (fingerprint, Face ID, security key)
- **Responsive Design**: Optimized for mobile and desktop devices

### For Administrators

- **Secure Dashboard**: Protected with authentication and role-based access control
- **Event Management**: Create, edit, and delete events with real-time updates
- **Reservation Management**: View, edit, and delete user reservations
- **XSS Protection**: All user inputs are properly escaped and validated
- **Error Handling**: Robust error handling with user-friendly messages

## 🛠 Technology Stack

### Backend

- **Symfony 7.4** - PHP framework
- **PostgreSQL 15** - Database
- **Doctrine ORM** - Object-relational mapping
- **Firebase JWT** - JSON Web Token authentication
- **Symfony Mailer** - Email notifications
- **WebAuthn Library** - Passwordless authentication

### Frontend

- **Vanilla JavaScript** - No framework dependencies
- **HTML5 / CSS3** - Modern web standards
- **Fetch API** - HTTP requests
- **Google Fonts (Inter)** - Typography
- **Unsplash API** - Event images

### Infrastructure

- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration
- **Nginx** - Reverse proxy and web server
- **PHP 8.2-FPM** - PHP FastCGI Process Manager

### Testing

- **PHPUnit 11.5** - Unit testing framework
- **Symfony Testing Tools** - Integration testing

## 📋 Prerequisites

- **Docker Desktop** (v20.10+) - [Download here](https://www.docker.com/products/docker-desktop/)
    - ⚠️ **Important**: Docker Desktop must be **running** before executing any `docker-compose` commands
    - On Windows: Look for the Docker icon in the system tray (it should show "Docker Desktop is running")
    - On Mac: Look for the Docker whale icon in the menu bar
- **Docker Compose** (v2.0+) - Included with Docker Desktop
- **Git** for version control
- **OpenSSL** for JWT key generation (usually pre-installed on Windows 10+, Mac, and Linux)

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd miniEvent1
```

### 2. Environment Configuration

```bash
cp .env .env.local
```

Edit `.env.local` with your configuration:

```env
# Database
POSTGRES_DB=app
POSTGRES_USER=app
POSTGRES_PASSWORD=!ChangeMe!

# JWT Configuration
JWT_SECRET_KEY=%kernel.project_dir%/var/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/var/jwt/public.pem
JWT_PASSPHRASE=your-secure-passphrase-here

# Application
APP_ENV=dev
APP_SECRET=your-app-secret-key-minimum-32-characters

# Email (Mailtrap for development, configure SMTP for production)
MAILER_DSN=smtp://mailtrap.io:2525?username=your-username&password=your-password
MAILER_FROM=noreply@localhost
```

### 3. Generate JWT Keys

**Windows PowerShell**:

```powershell
# Create directory (ignore error if it already exists)
New-Item -ItemType Directory -Force -Path var/jwt

# Generate private key (you'll be prompted for passphrase)
openssl genpkey -out var/jwt/private.pem -aes256 -algorithm RSA -pkeyopt rsa_keygen_bits:4096

# Generate public key
openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout
```

**Linux/Mac**:

```bash
# Create directory
mkdir -p var/jwt

# Generate private key (you'll be prompted for passphrase)
openssl genpkey -out var/jwt/private.pem -aes256 -algorithm RSA -pkeyopt rsa_keygen_bits:4096

# Generate public key
openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout

# Set permissions
chmod 600 var/jwt/private.pem
chmod 644 var/jwt/public.pem
```

### 4. Start Docker Containers

```bash
docker-compose up -d
```

Verify containers are running:

```bash
docker-compose ps
```

You should see: `php`, `nginx`, and `postgres` containers running.

### 5. Install Dependencies & Run Migrations

```bash
# Install Composer dependencies
docker-compose exec php composer install

# Run database migrations
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

# (Optional) Clear cache
docker-compose exec php bin/console cache:clear
```

### 6. Create Admin User

```bash
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@example.com', '[\"ROLE_ADMIN\"]', NULL);"
```

Or use the helper script:

```bash
docker-compose exec php php create_admin.php
```

## 🚀 Running the Project

### Quick Start (After Installation)

Once you've completed the installation steps above, follow these steps to run the project:

#### 1. Start All Services

```bash
docker-compose up -d
```

This command starts all containers in detached mode (background):

- **PHP-FPM** container (application server)
- **Nginx** container (web server)
- **PostgreSQL** container (database)

#### 2. Verify Services are Running

```bash
docker-compose ps
```

Expected output:

```
NAME                  STATUS    PORTS
mini-event-php        Up        0.0.0.0:9000->9000/tcp
mini-event-nginx      Up        0.0.0.0:8080->80/tcp
mini-event-postgres   Up        0.0.0.0:5432->5432/tcp
```

#### 3. Check Application Logs (Optional)

```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f postgres
```

Press `Ctrl+C` to exit log viewing.

#### 4. Access the Application

Open your web browser and navigate to:

**🏠 Homepage**: http://localhost:8080

You should see the Mini Event landing page with the list of events.

#### 5. Test the Application

**For Regular Users**:

1. Browse events at http://localhost:8080
2. Click on an event to view details
3. Fill out the reservation form to book a seat
4. Check for confirmation (in logs or Mailtrap if configured)

**For Administrators**:

1. Go to http://localhost:8080/login.html
2. Login with your admin account (e.g., `admin@example.com`)
3. Access admin dashboard at http://localhost:8080/admin.html
4. Create, edit, or delete events
5. Manage reservations

### Stopping the Project

To stop all running containers:

```bash
docker-compose stop
```

To stop and remove all containers (data persists in volumes):

```bash
docker-compose down
```

To stop and remove containers AND volumes (⚠️ deletes all data):

```bash
docker-compose down -v
```

### Restarting the Project

If containers are stopped but not removed:

```bash
docker-compose start
```

If you need to rebuild after code changes:

```bash
docker-compose restart
```

To rebuild containers completely:

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Viewing Application Status

Check if the application is responding:

```bash
# Test homepage
curl http://localhost:8080

# Test API endpoint
curl http://localhost:8080/api/events

# Test database connection
docker-compose exec postgres psql -U app -d app -c "SELECT COUNT(*) FROM event;"
```

### Common Startup Issues

#### Docker Desktop Not Running ⚠️

If you see an error like `failed to connect to the docker API` or `The system cannot find the file specified`:

**Windows**:

1. Open **Docker Desktop** from Start Menu or Desktop
2. Wait until you see "Docker Desktop is running" in the system tray (bottom-right corner)
3. The Docker icon should be stable (not animating)
4. Try the command again: `docker-compose up -d`

**Mac**:

1. Open **Docker Desktop** from Applications or Spotlight
2. Wait until the Docker whale icon in the menu bar is stable
3. Click the icon - it should say "Docker Desktop is running"
4. Try the command again: `docker-compose up -d`

**Verify Docker is running**:

```bash
# Test Docker connection
docker version

# If it works, you should see Client and Server information
```

#### Port Already in Use

If you see an error like `bind: address already in use`:

**Windows PowerShell**:

```powershell
# Check what's using port 8080
netstat -ano | findstr :8080

# Change the port in docker-compose.yml:
# nginx:
#   ports:
#     - "8081:80"  # Changed from 8080 to 8081
```

**Linux/Mac**:

```bash
# Check what's using port 8080
lsof -i :8080

# Change the port in docker-compose.yml
```

#### Containers Won't Start

```bash
# View error logs
docker-compose logs

# Remove old containers and restart
docker-compose down
docker-compose up -d
```

#### Database Connection Errors

```bash
# Wait for PostgreSQL to fully start
docker-compose exec postgres pg_isready -U app

# If not ready, wait a few seconds and try again
# Then run migrations again
docker-compose exec php bin/console doctrine:migrations:migrate
```

#### Permission Errors (Linux/Mac)

```bash
# Fix permissions on var directory
sudo chown -R $USER:$USER var/
chmod -R 775 var/
```

#### JWT Key Errors

```bash
# Verify JWT keys exist
ls -la var/jwt/

# If missing, regenerate them (see Installation section)
# Make sure JWT_PASSPHRASE in .env.local matches what you used
```

#### Seeing Symfony Welcome Page Instead of Application

If you see "Welcome to Symfony 7" when visiting `http://localhost:8080`:

**This is normal!** The root URL shows Symfony's welcome page. Access the actual application at:

- **http://localhost:8080/home.html** (Main landing page)
- **http://localhost:8080/index.html** (Events list)
- **http://localhost:8080/login.html** (Login page)
- **http://localhost:8080/admin.html** (Admin dashboard)

The HTML files are in the `public/` directory and are served directly by Nginx.

## 🌐 Accessing the Application

**⚠️ Important Note**: When you first visit `http://localhost:8080`, you'll see the Symfony welcome page. This is normal! You need to access the specific HTML pages:

| Page                | URL                                              | Description                   |
| ------------------- | ------------------------------------------------ | ----------------------------- |
| **🏠 Homepage**     | http://localhost:8080/home.html                  | **START HERE** - Landing page |
| **Event List**      | http://localhost:8080/index.html                 | Browse all events             |
| **Event Detail**    | http://localhost:8080/event.html?id=1            | View event details & reserve  |
| **Login**           | http://localhost:8080/login.html                 | WebAuthn authentication       |
| **Admin Dashboard** | http://localhost:8080/admin.html                 | Manage events & reservations  |
| **Modern Versions** | `/index-modern.html`, `/event-modern.html`, etc. | Updated UI versions           |

### First Time Setup

After starting Docker containers and running migrations, access the application at:

👉 **http://localhost:8080/home.html** 👈

If you see a Symfony welcome page at `http://localhost:8080`, that's expected - just add `/home.html` to the URL.

## 👤 User Accounts

### Test Administrator

- **Email**: `admin@example.com`
- **Authentication**: WebAuthn Passkey (simulated in dev)
- **Access**: Full admin dashboard access

### Creating Additional Users

Users can self-register via the login page using WebAuthn, or you can create them via SQL:

```bash
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('user@example.com', '[\"ROLE_USER\"]', NULL);"
```

## 📚 API Documentation

### Authentication Endpoints

#### Register New User

```http
POST /api/auth/register/options
Content-Type: application/json

{
  "email": "user@example.com"
}

Response: WebAuthn challenge options
```

```http
POST /api/auth/register/verify
Content-Type: application/json

{
  "email": "user@example.com",
  "credential": { /* WebAuthn credential response */ }
}

Response: { "token": "jwt-token", "user": {...} }
```

#### Login

```http
POST /api/auth/login/options
Content-Type: application/json

{
  "email": "user@example.com"
}

Response: WebAuthn authentication options
```

```http
POST /api/auth/login/verify
Content-Type: application/json

{
  "email": "user@example.com",
  "credential": { /* WebAuthn assertion response */ }
}

Response: { "token": "jwt-token", "refreshToken": "...", "user": {...} }
```

#### Refresh Token

```http
POST /api/token/refresh
Content-Type: application/json

{
  "refreshToken": "your-refresh-token"
}

Response: { "token": "new-jwt-token" }
```

### Event Endpoints

#### List All Events (Public)

```http
GET /api/events

Response: [
  {
    "id": 1,
    "title": "Event Title",
    "location": "Paris, France",
    "date": "2026-04-15T19:00:00+00:00",
    "availableSeats": 100,
    "description": "Event description",
    "imageUrl": "https://..."
  }
]
```

#### Get Event Details (Public)

```http
GET /api/events/{id}

Response: { "id": 1, "title": "...", ... }
```

#### Create Event (Admin Only)

```http
POST /api/events
Authorization: Bearer {jwt-token}
Content-Type: application/json

{
  "title": "New Event",
  "location": "Lyon, France",
  "date": "2026-05-20T18:00:00",
  "availableSeats": 150,
  "description": "Event description",
  "imageUrl": "https://images.unsplash.com/..."
}

Response: 201 Created
```

#### Update Event (Admin Only)

```http
PUT /api/events/{id}
Authorization: Bearer {jwt-token}
Content-Type: application/json

{
  "title": "Updated Event",
  "availableSeats": 200
}

Response: 200 OK
```

#### Delete Event (Admin Only)

```http
DELETE /api/events/{id}
Authorization: Bearer {jwt-token}

Response: 204 No Content
```

### Reservation Endpoints

#### Create Reservation (Public)

```http
POST /api/events/{eventId}/reservations
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+33612345678"
}

Response: 201 Created
```

#### List Event Reservations (Admin Only)

```http
GET /api/admin/events/{eventId}/reservations
Authorization: Bearer {jwt-token}

Response: [
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+33612345678",
    "createdAt": "2026-03-29T10:30:00+00:00"
  }
]
```

#### Update Reservation (Admin Only)

```http
PUT /api/admin/reservations/{id}
Authorization: Bearer {jwt-token}
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "+33698765432"
}

Response: 200 OK
```

#### Delete Reservation (Admin Only)

```http
DELETE /api/admin/reservations/{id}
Authorization: Bearer {jwt-token}

Response: 204 No Content
```

## 🧪 Testing

### Run Unit Tests

```bash
docker-compose exec php bin/phpunit
```

### Run Specific Test Suite

```bash
docker-compose exec php bin/phpunit tests/Controller
```

### Manual Testing Checklist

#### User Flow

1. ✅ Visit homepage
2. ✅ Browse events with filters
3. ✅ Click on event to view details
4. ✅ Fill reservation form and submit
5. ✅ Receive confirmation email

#### Admin Flow

1. ✅ Login with Passkey at `/login.html`
2. ✅ Access admin dashboard at `/admin.html`
3. ✅ Create a new event
4. ✅ Edit existing event
5. ✅ View event reservations
6. ✅ Edit/delete reservations
7. ✅ Delete event
8. ✅ Logout

#### Security Tests

- ✅ XSS protection: Try `<script>alert('XSS')</script>` in reservation name
- ✅ Authentication: Access `/admin.html` without login (should redirect)
- ✅ Authorization: Access admin endpoints without ROLE_ADMIN
- ✅ JWT expiration: Test with expired token

## 📁 Project Structure

```
miniEvent1/
├── bin/                      # Symfony console scripts
├── config/                   # Symfony configuration
│   ├── packages/             # Bundle configuration
│   ├── routes/               # Routing configuration
│   └── services.yaml         # Service container
├── docker/                   # Docker configuration
│   └── nginx/                # Nginx config
├── migrations/               # Database migrations
├── public/                   # Public web files
│   ├── *.html                # Frontend pages
│   └── index.php             # Symfony entry point
├── src/                      # PHP source code
│   ├── Controller/           # Controllers
│   │   └── Api/              # API controllers
│   ├── Entity/               # Doctrine entities
│   │   ├── Event.php
│   │   ├── Reservation.php
│   │   ├── User.php
│   │   └── WebauthnCredential.php
│   ├── Repository/           # Doctrine repositories
│   ├── Security/             # Security components
│   ├── Service/              # Business logic services
│   └── Kernel.php            # Application kernel
├── templates/                # Twig templates
├── tests/                    # PHPUnit tests
├── var/                      # Generated files
│   ├── cache/                # Application cache
│   ├── jwt/                  # JWT keys
│   └── log/                  # Application logs
├── vendor/                   # Composer dependencies
├── .env                      # Environment template
├── composer.json             # PHP dependencies
├── docker-compose.yml        # Docker services
├── Dockerfile                # PHP container image
└── README.md                 # This file
```

## 🔧 Development

### Useful Commands

#### Docker Management

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f postgres

# Restart a service
docker-compose restart php

# Execute commands in containers
docker-compose exec php bash
docker-compose exec postgres psql -U app -d app
```

#### Symfony Console

```bash
# Clear cache
docker-compose exec php bin/console cache:clear

# Create migration
docker-compose exec php bin/console make:migration

# Run migrations
docker-compose exec php bin/console doctrine:migrations:migrate

# Check database schema
docker-compose exec php bin/console doctrine:schema:validate

# Debug routes
docker-compose exec php bin/console debug:router

# List services
docker-compose exec php bin/console debug:container
```

#### Database

```bash
# Access PostgreSQL
docker-compose exec postgres psql -U app -d app

# Dump database
docker-compose exec postgres pg_dump -U app app > backup.sql

# Restore database
cat backup.sql | docker-compose exec -T postgres psql -U app app
```

### Development Workflow

1. **Make code changes** in `src/` or `public/`
2. **Clear cache** if needed: `docker-compose exec php bin/console cache:clear`
3. **Run tests**: `docker-compose exec php bin/phpunit`
4. **Check logs**: `docker-compose logs -f php`
5. **Create migration** if entities changed: `docker-compose exec php bin/console make:migration`

## 🔒 Security Features

### Implemented Security Measures

1. **XSS Protection**: All user inputs are escaped before rendering
2. **CSRF Protection**: Symfony's CSRF tokens on forms
3. **SQL Injection Protection**: Doctrine ORM with parameterized queries
4. **JWT Authentication**: Secure token-based authentication
5. **Role-Based Access Control**: Admin vs User permissions
6. **WebAuthn**: Passwordless authentication standard
7. **Input Validation**: Server-side validation on all inputs
8. **HTTPS Ready**: Configure reverse proxy for SSL in production

### Security Best Practices for Production

1. **Change all default passwords** in `.env`
2. **Use strong JWT passphrase** (minimum 32 characters)
3. **Enable HTTPS** with valid SSL certificates
4. **Configure CORS** properly in `config/packages/nelmio_cors.yaml`
5. **Set secure cookie options** in `config/packages/framework.yaml`
6. **Use production SMTP** service (SendGrid, Mailgun, Amazon SES)
7. **Enable rate limiting** on authentication endpoints
8. **Configure proper WebAuthn origins** for your domain
9. **Regular security updates** for all dependencies
10. **Monitor logs** for suspicious activity

## 🚀 Production Deployment

### Pre-Deployment Checklist

- [ ] Update `.env` with production values
- [ ] Generate production JWT keys
- [ ] Set `APP_ENV=prod`
- [ ] Configure production database
- [ ] Set up SMTP service
- [ ] Configure domain and SSL certificates
- [ ] Update CORS settings for production domain
- [ ] Run security audit: `composer audit`
- [ ] Run all tests: `docker-compose exec php bin/phpunit`
- [ ] Optimize autoloader: `composer dump-autoload --optimize`

### Deployment Steps

1. **Build production image**

```bash
docker build -t mini-event:latest .
```

2. **Run database migrations**

```bash
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

3. **Clear and warm up cache**

```bash
docker-compose exec php bin/console cache:clear --env=prod
docker-compose exec php bin/console cache:warmup --env=prod
```

4. **Set proper permissions**

```bash
docker-compose exec php chown -R www-data:www-data var/
```

5. **Configure reverse proxy** (Nginx/Apache) with SSL

### Environment Variables for Production

```env
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL=postgresql://user:pass@db-host:5432/dbname?serverVersion=15
MAILER_DSN=smtp://smtp.sendgrid.net:587?username=apikey&password=your-api-key
JWT_SECRET_KEY=/path/to/jwt/private.pem
JWT_PUBLIC_KEY=/path/to/jwt/public.pem
```

## 🐛 Troubleshooting

### Common Issues

#### "Class not found" errors

```bash
docker-compose exec php composer dump-autoload
docker-compose exec php bin/console cache:clear
```

#### Database connection failed

- Check PostgreSQL is running: `docker-compose ps postgres`
- Verify credentials in `.env`
- Check database logs: `docker-compose logs postgres`

#### JWT errors

- Verify JWT keys exist in `var/jwt/`
- Check key permissions: `chmod 600 var/jwt/private.pem`
- Verify JWT_PASSPHRASE matches

#### Admin dashboard won't load

- Verify admin user has ROLE_ADMIN
- Check browser console for JavaScript errors
- Clear browser localStorage
- Verify token in localStorage is valid

#### Events/Reservations not loading

- Check API endpoint responses in Network tab
- Verify authentication token is included
- Check backend logs: `docker-compose logs php`
- Test API directly: `curl http://localhost:8080/api/events`

#### Email not sending

- Check Mailtrap or SMTP configuration
- View logs: `docker-compose logs php`
- Test mailer: `docker-compose exec php php test_mail.php`

## 📈 Performance Optimization

- **Database Indexes**: Already configured on foreign keys and common query fields
- **Doctrine Query Cache**: Enabled in production
- **Asset Optimization**: Minify CSS/JS for production
- **HTTP/2**: Enable in Nginx for better performance
- **CDN**: Serve static assets from CDN in production
- **Redis Cache**: Consider adding Redis for session storage

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'Add amazing feature'`
4. **Push** to the branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

### Code Style

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Write tests for new features

## 📄 License

This project is proprietary software developed for academic purposes.

## 🙏 Acknowledgments

- **Symfony** for the excellent PHP framework
- **Doctrine** for the powerful ORM
- **Unsplash** for free event images
- **WebAuthn Community** for passwordless authentication standards

---

## 📞 Support

For issues, questions, or contributions:

- Check existing issues on GitHub
- Review documentation carefully
- Check logs: `docker-compose logs`
- Test in incognito/private browser window
- Clear cache and localStorage

---

**Mini Event** - Modern Event Reservation Management System  
**Version**: 2.0  
**Last Updated**: March 2026  
**Status**: Production Ready ✅

---

**Developed with ❤️ for academic project**
