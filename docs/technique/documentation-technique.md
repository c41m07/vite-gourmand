# Documentation technique — Vite & Gourmand

## 1. Choix techniques (et justifications)
- Back : Symfony (version du projet)
- Front : Twig + Bootstrap minimal + JS (fetch)
- BDD relationnelle : MySQL/MariaDB
- BDD non relationnelle : MongoDB (stats admin)

## 2. Environnement de travail
- Pré-requis
- Installation (Docker / commandes make)
- Variables d’environnement (sans secrets)
- Lancement local

## 3. Modélisation
- MCD / diagramme de classes (à intégrer)
- Dictionnaire de données (tables principales)
- Contraintes (relations, cardinalités)

## 4. Architecture applicative
- Organisation (Controllers, Services, Repositories, Templates)
- Gestion des rôles : USER / EMPLOYEE / ADMIN

## 5. Règles métier
- Filtres menus sans rechargement
- Commande (livraison Bordeaux vs hors Bordeaux + 0,59/km ; -10% si min+5 ; statut)
- Workflow statuts commande
- Avis : création / validation

## 6. Sécurité & RGPD
- Hash mdp + règles complexité
- CSRF, validation, contrôle d’accès (RBAC)
- Protection XSS / upload images
- Données collectées et finalités

## 7. Accessibilité (RGAA)
- Structure sémantique
- Navigation clavier / focus
- Contrastes / labels / erreurs

## 8. NoSQL (MongoDB)
- Modèle des documents de stats
- Agrégations : nb commandes par menu + CA par menu + filtres

## 9. Déploiement (procédure)
- Hébergeur/plateforme
- Variables d’environnement en prod
- Import SQL (schema + seed)
- Mise en ligne + vérifications