# Agro Eco BAARA

Plateforme de mise en relation pour l'emploi agroécologique au Burkina Faso.  
Construite avec **Laravel 13**, **Livewire 4** et **SQLite** (configurable MySQL/PostgreSQL).

---

## Prérequis

| Outil | Version minimale |
|---|---|
| PHP | 8.3 |
| Composer | 2.x |
| Node.js | 18.x |
| npm | 9.x |

---

## Installation

### 1. Cloner le dépôt

```bash
git clone <url-du-repo>
cd agro
```

### 2. Installer les dépendances PHP

```bash
cd baara
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrir `.env` et ajuster si besoin :

```env
APP_NAME="Agro Eco BAARA"
APP_URL=http://localhost:8000

# Base de données — SQLite par défaut (aucune config supplémentaire)
DB_CONNECTION=sqlite

# Pour utiliser MySQL à la place :
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=agro_baara
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Créer la base de données et lancer les migrations

```bash
# SQLite — crée le fichier automatiquement
php artisan migrate --seed
```

> Le seeder crée les rôles, permissions, sections de la landing page et un compte super-admin par défaut.

### 5. Créer le lien symbolique pour les fichiers publics

```bash
php artisan storage:link
```

---

## Démarrage

Ouvrir **deux terminaux** depuis le dossier `baara/` :

**Terminal 1 — Serveur Laravel**
```bash
php artisan serve
```

> L'application est accessible sur [http://localhost:8000](http://localhost:8000)

**Terminal 2 — Assets front-end** *(si Vite est utilisé)*
```bash
npm install
npm run dev
```

---

## Compte administrateur par défaut

Après le seeder, un super-admin est disponible :

| Champ | Valeur |
|---|---|
| Email | *(défini dans `RolesPermissionsSeeder`)*  |
| URL admin | http://localhost:8000/admin |

> Changer le mot de passe dès la première connexion.

---

## Commandes utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Relancer les migrations depuis zéro + seeder
php artisan migrate:fresh --seed

# Accéder au REPL interactif
php artisan tinker

# Lancer les tests
php artisan test
```

---

## Structure du projet

```
agro/
└── baara/                  # Application Laravel
    ├── app/
    │   ├── Livewire/       # Composants Livewire (Landing, Admin...)
    │   └── Models/         # Modèles Eloquent
    ├── database/
    │   ├── migrations/
    │   └── seeders/        # Données initiales (sections landing, rôles...)
    ├── public/
    │   └── images/         # Images statiques et uploads
    ├── resources/
    │   └── views/
    │       ├── components/layouts/   # Layouts (landing, app admin)
    │       └── livewire/             # Vues des composants
    └── routes/
        └── web.php
```

---

## Configurateur de la landing page

Connecté en tant que super-admin, accéder à :

```
http://localhost:8000/admin/administration/landing
```

Chaque section (Hero, Guichet, Partenaires, Médiathèque…) peut être éditée, réordonnée et activée/désactivée depuis cette interface.
