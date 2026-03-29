# Mini Event - Application de Gestion de Réservations d'Événements

Une application web moderne pour la gestion de réservations d'événements avec authentification WebAuthn (Passkey).

## 🚀 Fonctionnalités

- **Gestion des événements** : Créer, modifier, supprimer des événements
- **Système de réservations** : Les utilisateurs peuvent réserver des places pour les événements
- **Authentification WebAuthn** : Connexion sécurisée avec Passkey (empreinte digitale, Face ID, clé de sécurité)
- **Authentification JWT** : Tokens d'accès et refresh tokens pour l'API
- **Emails de confirmation** : Envoi automatique d'emails de confirmation de réservation
- **Interface d'administration** : Panneau admin pour gérer les événements et voir les réservations
- **Design responsive** : Interface moderne adaptée mobile/desktop

## 🛠 Stack Technique

### Backend
- **Symfony 7.4** - Framework PHP
- **PostgreSQL 15** - Base de données
- **Doctrine ORM** - Mapping objet-relationnel
- **WebAuthn** - Authentification par Passkey
- **JWT** - Tokens d'authentification
- **Symfony Mailer** - Envoi d'emails

### Frontend
- **HTML5 / JavaScript natif** - Sans framework frontend
- **Fetch API** - Appels HTTP
- **CSS3 moderne** - Design responsive

### Infrastructure
- **Docker** - Conteneurisation
- **Docker Compose** - Orchestration des services
- **Nginx** - Serveur web reverse proxy
- **PHP 8.2-FPM** - Serveur PHP

## 📋 Prérequis

- Docker et Docker Compose
- Git

## 🚀 Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-depot>
   cd miniEvent1
   ```

2. **Configurer les variables d'environnement**
   ```bash
   cp .env .env.local
   ```
   
   Éditez le fichier `.env.local` et configurez les variables suivantes :
   ```env
   # Database
   POSTGRES_DB=app
   POSTGRES_USER=app
   POSTGRES_PASSWORD=!ChangeMe!
   
   # JWT
   JWT_SECRET_KEY=%kernel.project_dir%/var/jwt/private.pem
   JWT_PUBLIC_KEY=%kernel.project_dir%/var/jwt/public.pem
   JWT_PASSPHRASE=your-jwt-passphrase
   
   # App
   APP_SECRET=your-app-secret-key-here
   
   # Mailer (Mailtrap pour le développement)
   MAILER_DSN=smtp://mailtrap.io:2525?username=votre-username&password=votre-password
   ```

3. **Générer les clés JWT**
   ```bash
   mkdir -p var/jwt
   openssl genpkey -out var/jwt/private.pem -aes256 -algorithm RSA -pkeyopt rsa_keygen_bits:4096
   openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout
   ```

4. **Lancer les conteneurs Docker**
   ```bash
   docker-compose up -d
   ```

5. **Installer les dépendances et exécuter les migrations**
   ```bash
   docker-compose exec php composer install
   docker-compose exec php bin/console doctrine:migrations:migrate
   ```

6. **Créer un utilisateur administrateur**
   ```bash
   docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@example.com', '[\"ROLE_ADMIN\"]', NULL);"
   ```

## 🌐 Accès à l'application

- **Site public** : http://localhost:8080
- **Interface d'administration** : http://localhost:8080/admin.html
- **Page de connexion** : http://localhost:8080/login.html

## 📝 Comptes de test

### Administrateur
- **Email** : admin@example.com
- **Authentification** : Passkey (simulation)

### Utilisateur standard
- **Email** : user@example.com  
- **Authentification** : Passkey (simulation)

*Note : Dans cette version de démonstration, l'authentification WebAuthn est simulée. Dans un environnement de production, vous devrez configurer les vraies clés WebAuthn.*

## 📚 Documentation API

### Authentification

#### Inscription
```http
POST /api/auth/register/options
POST /api/auth/register/verify
```

#### Connexion
```http
POST /api/auth/login/options
POST /api/auth/login/verify
```

#### Refresh Token
```http
POST /api/token/refresh
```

### Événements

#### Public
```http
GET /api/events - Lister tous les événements
GET /api/events/{id} - Détail d'un événement
```

#### Admin (nécessite ROLE_ADMIN)
```http
POST /api/events - Créer un événement
PUT /api/events/{id} - Mettre à jour un événement
DELETE /api/events/{id} - Supprimer un événement
```

### Réservations

#### Public
```http
POST /api/events/{eventId}/reservations - Créer une réservation
```

#### Admin (nécessite ROLE_ADMIN)
```http
GET /api/admin/events/{eventId}/reservations - Lister les réservations d'un événement
```

## 🧪 Tests

### Exécuter les tests unitaires
```bash
docker-compose exec php bin/phpunit
```

### Tests de réservation
Les tests vérifient notamment qu'il est impossible de réserver plus de places que disponibles.

## 📁 Structure du projet

```
miniEvent1/
├── src/
│   ├── Controller/Api/          # Contrôleurs API
│   ├── Entity/                   # Entités Doctrine
│   ├── Repository/               # Repositories
│   ├── Service/                  # Services métier
│   └── Security/                # Configuration sécurité
├── public/                       # Fichiers publics (HTML, CSS, JS)
├── config/                       # Configuration Symfony
├── docker/                       # Configuration Docker
├── migrations/                    # Migrations Doctrine
└── tests/                        # Tests unitaires
```

## 🔧 Développement

### Commandes utiles
```bash
# Lancer les conteneurs en mode développement
docker-compose up

# Exécuter une commande dans le conteneur PHP
docker-compose exec php <command>

# Voir les logs
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f postgres

# Créer une nouvelle migration
docker-compose exec php bin/console make:migration

# Charger les fixtures (si disponible)
docker-compose exec php bin/console doctrine:fixtures:load
```

### Configuration de WebAuthn
Pour une implémentation complète de WebAuthn en production, vous devrez :

1. Configurer les vraies options WebAuthn
2. Mettre en place la validation des assertions
3. Configurer les origines autorisées
4. Gérer le stockage sécurisé des clés

## 📝 Notes importantes

- Cette version utilise une implémentation simplifiée de WebAuthn pour démonstration
- Les tokens JWT sont simulés en développement
- Configurez toujours des clés JWT fortes en production
- Utilisez un vrai service d'emails (SendGrid, Mailgun, etc.) en production
- Activez HTTPS pour l'authentification WebAuthn en production

## 🤝 Contribuer

1. Fork le projet
2. Créer une branche feature
3. Committer vos changements
4. Pusher vers la branche
5. Créer une Pull Request

## 📄 Licence

Ce projet est sous licence propriétaire.

---

**Développé avec ❤️ pour le projet académique Mini Event**
