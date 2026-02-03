# Documentation technique — Vite & Gourmand

## 0. État actuel (03/02/2026)
- Pages publiques intégrées en Twig : Accueil, Menus, Contact, Mentions légales, CGV.
- UI + styles en place (Bootstrap + Sass), focus visible et skip-link.
- BDD/entités, authentification, commandes et stats : en cours d’implémentation.

## 1. Choix techniques (et justifications)
- Back-end : Symfony 8 (PHP 8.4) pour la structure MVC, la sécurité et la rapidité de mise en place.
- Front : Twig + Bootstrap 5.3 + Sass pour une intégration rapide et accessible.
- JS : Webpack Encore + Stimulus pour les interactions (filtres menus, formulaires).
- BDD relationnelle : MariaDB (Doctrine ORM) pour les entités métier (prévu).
- BDD non relationnelle : MongoDB pour les statistiques admin (prévu).

## 2. Environnement de travail
- Pré-requis : Docker + Docker Compose.
- Démarrage :
  - `make boot` (containers)
  - `make connect` (shell dans le container)
  - `make start` (install dépendances + build assets)
- Commandes utiles :
  - `make check` (fixer + phpstan)
  - `make watch` (assets)
- Variables d’environnement : voir `.env` (racine) et `app/.env` (sans secrets).

## 3. Modélisation (relationnel) — cible
Entités principales :
- User (roles, infos perso)
- Menu (titre, description, thème, régime, min personnes, prix min, stock, conditions)
- Plat (entrée/plat/dessert, allergènes)
- Commande (menu, client, date/heure, adresse, statut, prix livraison)
- Avis (note 1–5, commentaire, validé)

Relations clés :
- Un menu possède plusieurs plats ; un plat peut appartenir à plusieurs menus.
- Un utilisateur possède plusieurs commandes et avis.

## 4. Architecture applicative — cible
- Controllers : routing + logique de présentation.
- Services : règles métier (prix, livraison, statuts).
- Repositories : accès aux données (Doctrine).
- Templates Twig : UI.
- Rôles : `ROLE_USER`, `ROLE_EMPLOYEE`, `ROLE_ADMIN`.

## 5. Règles métier (issues du cahier des charges)
- Filtres menus dynamiques (prix, thème, régime, min personnes).
- Livraison Bordeaux : +5,00 EUR hors Bordeaux : +0,59 EUR/km.
- Remise : -10% si nb personnes >= (min + 5).
- Workflow statuts : accepté -> en préparation -> en cours de livraison -> livré -> (en attente retour matériel) -> terminée.
- Avis visibles sur l’accueil uniquement après validation employé/admin.

## 6. Sécurité & RGPD — cible
- Mots de passe hashés (Symfony Security + bcrypt/argon2id).
- CSRF sur formulaires sensibles.
- Validation des champs (constraints Symfony).
- Contrôle d’accès RBAC (séparation USER/EMPLOYEE/ADMIN).
- RGPD : minimisation, finalités claires (compte, commande, contact).

## 7. Accessibilité (RGAA)
- Structure sémantique (header/nav/main/footer).
- Navigation clavier + focus visible.
- Labels explicites + messages d’erreur.
- Contrastes conformes.

## 8. NoSQL (MongoDB) — cible
- Collection `menu_stats` (menu_id, nbCommandes, chiffreAffaires, période).
- Agrégations : top menus + CA par menu, filtrable par période.

## 9. Déploiement (procédure) — cible
- Construire l’image Docker.
- Définir variables d’environnement (APP_ENV, DATABASE_URL, MAILER_DSN).
- Importer le schéma SQL + seed.
- Vérifier : pages publiques, auth, filtres, commandes, mails.
