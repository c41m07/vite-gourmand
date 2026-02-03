# Gestion de projet — Vite & Gourmand

## 1. Outil utilisé
- Trello : https://trello.com/invite/b/6971ee5dd9c0bce3e87fa0db/ATTId544b769ee4c66e71b1db522ef22581f07368E5B/vite-gourmand-ecf

## 2. Méthode
- Kanban (visualisation continue, pas de timeboxing strict).
- Colonnes : Backlog / À faire / En cours / À relire / Tester / Terminé🎉 / Blocage.
- 1 carte = 1 fonctionnalité ou 1 tâche technique.
- Règle de WIP : 1 carte max en "En cours" par personne.
- Definition of Done (DoD) :
  - Code sur une branche `feature/*`.
  - Tests manuels effectués (parcours utilisateur).
  - Conformité sécurité + RGAA vérifiée.
  - Merge vers `develop` validé.

## 3. Organisation Git
- Branches : `main` / `develop` / `feature/*`.
- Merge `feature/*` -> `develop` (après tests).
- Merge `develop` -> `main` (après stabilisation).

## 4. Suivi & preuves
- Captures d’écran Trello (sprints 0-1-2 + avancement).
- Captures PR / merges (si applicable).
- Suivi d’avancement : `docs/gestion-projet/avancement.md`.

## 5. Planning (prévisionnel puis réel)
- Sprint 0 : cadrage, environnements, MCD, Trello.
- Sprint 1 : maquettes + charte graphique.
- Sprint 2 : intégration UI (Twig + Bootstrap).
- Sprints suivants : dev back/front + tests + déploiement.
