# Plan de développement — Agro Eco BAARA

## État au 14 mai 2026

---

## ✅ TERMINÉ ET FONCTIONNEL

### Infrastructure
- [x] Laravel 12 + Livewire 3 + Alpine.js + Tailwind CSS v4
- [x] MySQL 8 — toutes les migrations appliquées
- [x] `server.php` corrigé (fix Laravel 12)
- [x] `activity_log` migrée avec `nullableUuidMorphs` (UUID compatibility)
- [x] Seeders : RolesPermissionsSeeder + ReferentialsSeeder

### Authentification
- [x] **Login** — Controller classique HTML (GET/POST) — 100% fiable
- [x] **Logout** — LogoutController
- [x] **AuthService** — tentative, bruteforce, verrouillage compte
- [x] **Middlewares** — RequireRole, RequirePermission, CheckAccountStatus
- [x] **RBAC** — HasRoles trait, Gate::before super-admin bypass

### Landing Page (publique)
- [x] Hero section avec photo
- [x] Section "Pour qui ?" (Jeunes / Entreprises)
- [x] Section "Nos Services" (grille bento asymétrique)
- [x] Section "Comment ça marche ?" (3 étapes)
- [x] Section Partenaires (YELEMANI, CRIC, MAAH, BFA-OPS)
- [x] Section Contact + formulaire Livewire
- [x] Footer complet
- [x] Logo réel intégré (`public/images/logo.jpeg`)

### Back-office — Layout
- [x] Sidebar responsive (mobile + desktop)
- [x] TopBar avec avatar, nom, notifications
- [x] `wire:navigate` sur tous les liens → navigation SPA instantanée
- [x] Barre de progression verte lors des navigations
- [x] 38 routes enregistrées

### Candidats
- [x] `CandidateIndex` — liste, recherche, filtres, pagination
- [x] `CandidateCreate` — formulaire multi-sections (5 étapes)

---

## ✅ MODULES TERMINÉS (mai 2026)

### Dashboard ✅
- [x] KPIs : nb candidats, entreprises, offres actives, matching réussis
- [x] Actions rapides (nouveau candidat, entreprise, offre)
- [x] Dernières mises en relation + dernières offres
- [x] Outils d'audit interne (modérateur/super-admin)

### Candidats ✅
- [x] `CandidateIndex` — liste, filtres, pagination
- [x] `CandidateCreate` — formulaire multi-sections (5 étapes)
- [x] `CandidateShow` — fiche complète (identité, contacts, formation, compétences, matching)
- [x] `CandidateEdit` — formulaire édition hydraté (5 étapes)

### Entreprises ✅
- [x] `CompanyIndex` — liste avec search, pagination
- [x] `CompanyCreate` — formulaire avec sites d'activité
- [x] `CompanyShow` — fiche + offres liées
- [ ] `CompanyEdit` — formulaire édition (PHP stub, vue en attente)

### Offres ✅
- [x] `OfferIndex` — liste avec filtres statut + search, actions publish/archive
- [x] `OfferCreate` — formulaire complet (compétences, lieux, contrat)
- [x] `OfferShow` — détail + candidats suggérés + publish/archive
- [ ] `OfferEdit` — formulaire édition (PHP stub, vue en attente)

### Matching ✅
- [x] `MatchIndex` — tableau avec filtres par statut
- [x] `MatchShow` — détail candidat ↔ offre + mise à jour statut + notes

### Administration ✅
- [x] `UserIndex` — liste + toggle actif/inactif + déverrouillage + suppression
- [x] `UserCreate` — créer utilisateur + rôle + forcePasswordChange
- [x] `RoleIndex` — éditer permissions par rôle (groupées par catégorie)
- [x] `AuditLog` — journal filtrable (search, événement, dates)
- [x] `LandingConfigurator` — aperçu sections + notice développement

---

## 🟡 À DÉVELOPPER

### Priorité 1 — Édition entreprises/offres
- [ ] `CompanyEdit` — compléter PHP component + formulaire édition
- [ ] `OfferEdit` — compléter PHP component + formulaire édition

### Priorité 2 — Fonctionnalités transverses
- [ ] Upload avatar utilisateur (storage link)
- [ ] Réinitialisation mot de passe (ForgotPassword + ResetPassword vues)
- [ ] ChangePassword vue (1ère connexion)
- [ ] Notifications in-app

### Priorité 3 — Landing configurateur
- [ ] Éditeur de sections (textes, images, ordre)
- [ ] Prévisualisation temps réel

---

## 🔧 IDENTIFIANTS DE TEST

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Administrateur | `admin@agroecobaara.bf` | `Admin@2027!` |

---

## 🛠 COMMANDES UTILES

```bash
# Démarrer le serveur
php artisan serve --port=8000

# Reset complet DB + seed
php artisan migrate:fresh --seed

# Build CSS (avec contournement SSL local)
$env:NODE_TLS_REJECT_UNAUTHORIZED="0"; npm run build

# Vider tous les caches
php artisan optimize:clear
```

---

## 📁 STRUCTURE CLÉS

```
app/
  Http/Controllers/Auth/   LoginController, LogoutController
  Livewire/                Tous les composants Livewire
  Services/                AuthService, MatchingService, ReferenceService
  Models/                  User, Candidate, Company, JobOffer, Match...
  Enums/                   UserRole, UserStatus, OfferStatus, MatchStatus...
  Traits/                  HasRoles
resources/views/
  auth/login.blade.php     Formulaire login (HTML classique)
  components/layouts/      app.blade.php, auth.blade.php, landing.blade.php
  livewire/                Toutes les vues des composants
public/images/logo.jpeg    Logo officiel
```
