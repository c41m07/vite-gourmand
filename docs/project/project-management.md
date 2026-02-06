# Gestion de projet - Vite & Gourmand

## 1. Outil utilise
- Trello : https://trello.com/invite/b/6971ee5dd9c0bce3e87fa0db/ATTId544b769ee4c66e71b1db522ef22581f07368E5B/vite-gourmand-ecf

## 2. Methode
- Kanban (visualisation continue, pas de timeboxing strict).
- Colonnes : Backlog / A faire / En cours / A relire / Tester / Termine / Blocage.
- 1 carte = 1 fonctionnalite ou 1 tache technique.
- Regle WIP : 1 carte max en "En cours" par personne.
- Definition of Done (DoD) :
  - Code sur une branche `feature/*`.
  - Tests manuels effectues (parcours utilisateur).
  - Conformite securite + RGAA verifiee.
  - Merge vers `develop` valide.

## 3. Organisation Git
- Branches : `main` / `develop` / `feature/*`.
- Merge `feature/*` -> `develop` (apres tests).
- Merge `develop` -> `main` (apres stabilisation).

## 4. Suivi & preuves
- Captures d'ecran Trello (sprints 0-1-2 + avancement).
- Captures PR / merges (si applicable).
- Suivi d'avancement : `docs/project/progress.md`.

## 5. Planning (previsionnel puis reel)
- Sprint 0 : cadrage, environnements, MCD, Trello.
- Sprint 1 : maquettes + charte graphique.
- Sprint 2 : integration UI (Twig + Bootstrap).
- Sprints suivants : dev back/front + tests + deploiement.
