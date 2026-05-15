---
name: project-agro-baara
description: Agro Eco BAARA — état du projet Laravel/Livewire back-office, modules construits, restants
metadata:
  type: project
---

Projet Agro Eco BAARA — back-office Laravel 12 + Livewire 3 + Tailwind v4 + Alpine.js.

**Chemin**: `c:\Users\HP\Desktop\agro\baara`
**Plan**: `PLAN.md` dans le projet
**Login test**: admin@agroecobaara.bf / Admin@2027!

## État mai 2026 — TERMINÉ

- Infrastructure, auth, landing page (publique) : 100% OK
- Dashboard, CandidateIndex, CandidateCreate : OK
- **CandidateShow**, **CandidateEdit** : construits (mai 2026)
- **CompanyIndex**, **CompanyCreate**, **CompanyShow** : construits
- **OfferIndex**, **OfferCreate**, **OfferShow** : construits
- **MatchIndex**, **MatchShow** : construits
- **UserIndex**, **UserCreate** : construits
- **RoleIndex**, **AuditLog** : construits
- CompanyEdit, OfferEdit : PHP stubs incomplets — vues placeholder en place
- CSS base réduit à 15px (html { font-size: 93.75% }) pour densité back-office

## Restant

- CompanyEdit et OfferEdit : compléter les PHP components avec les champs de form
- LandingConfigurator : éditeur de sections
- Upload avatar, forgot/reset password, change password, notifications in-app

**Why:** Toutes les vues "stub" ont été remplacées par des vues fonctionnelles en cohérence avec les PHP Livewire components existants.
**How to apply:** Consulter PLAN.md pour la liste complète.