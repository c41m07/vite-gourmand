# Documentation technique - Vite & Gourmand

## Etat actuel (mars 2026)
- Pages publiques integrees en Twig : Accueil, Menus, Contact, Mentions legales, CGV.
- Authentification de base : login/register + page profil utilisateur.
- Contact : formulaire + stockage en BDD (ContactMessage) + envoi de mail.
- UI + styles en place (Bootstrap + Sass), focus visible et skip-link.
- Page 404 personnalisee activee via TwigBundle (`error404.html.twig`).
- Commandes, stats et back-office : en cours d'implementation.

## Choix techniques (et justifications)
- Back-end : Symfony 8 (PHP 8.4) pour la structure MVC, la securite et la rapidite de mise en place.
- Front : Twig + Bootstrap 5.3 + Sass pour une integration rapide et accessible.
- JS : Webpack Encore + Stimulus pour les interactions (filtres menus, formulaires).
- BDD relationnelle : MariaDB (Doctrine ORM) pour les entites metier (cible).
- BDD non relationnelle : MongoDB pour les statistiques admin (cible).

## Environnement de travail
- Prerequis : Docker + Docker Compose.
- Demarrage : voir `docs/technical/setup.md`.
- Variables d'environnement : `app/.env` (base versionnee) + overrides locaux `app/.env.local` et `app/.env.*.local` (non versionnes).

## Modelisation (relationnel) - cible
- MCD : `docs/database/mcd.md`.
- Relations et conventions : `docs/database/relations.md`.
- Entites principales : User, Menu, Dish, CustomerOrder, Review.

## Architecture applicative - cible
- Controllers : routing + logique de presentation.
- Services : regles metier (prix, livraison, statuts).
- Repositories : acces aux donnees (Doctrine).
- Templates Twig : UI.
- Roles : `ROLE_USER`, `ROLE_WORKER`, `ROLE_ADMIN`.

## Regles metier (issues du cahier des charges)
- Filtres menus dynamiques (prix, theme, regime, min personnes).
- Livraison : Bordeaux = 0 EUR, hors Bordeaux = 5 EUR + 0,59 EUR/km.
- Reduction : -10% si nb personnes >= (min + 5).
- Workflow statuts : acceptee -> en preparation -> en cours de livraison -> livree -> (attente retour materiel) -> terminee.
- Avis visibles sur l'accueil uniquement apres validation employe/admin.

## Securite & RGPD - cible
- Mots de passe hashes (Symfony Security + bcrypt/argon2id).
- CSRF sur formulaires sensibles.
- Validation des champs (constraints Symfony).
- Controle d'acces RBAC (separation USER/WORKER/ADMIN).
- RGPD : minimisation, finalites claires (compte, commande, contact).

## Accessibilite (RGAA)
- Structure semantique (header/nav/main/footer).
- Navigation clavier + focus visible.
- Labels explicites + messages d'erreur.
- Contrastes conformes.

## NoSQL (MongoDB) - cible
- Collection `menu_stats` (menu_id, nbCommandes, chiffreAffaires, periode).
- Agregations : top menus + CA par menu, filtrable par periode.

## Deploiement (procedure) - cible
- Construire l'image Docker.
- Definir variables d'environnement (APP_ENV, DATABASE_URL, MAILER_DSN).
- Importer le schema SQL + seed.
- Verifier : pages publiques, auth, filtres, commandes, mails.
