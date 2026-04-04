# _DEV_ARCHITECTURE

Photo utile du projet au 2026-04-04, centree sur ce qui est present dans le repo.

## Stack

- Symfony 8 / PHP 8.4
- Doctrine ORM + migrations
- Twig + Webpack Encore + Stimulus + Bootstrap
- MariaDB
- Docker Compose + Make
- MailHog pour les mails en local

## Perimetre applicatif visible

- Pages publiques: accueil, menus, detail menu, contact, mentions legales, CGV
- Securite: inscription, connexion, profil utilisateur, base de reset password
- Commandes: formulaire, creation, historique et suivi cote utilisateur
- Back-office: espaces `worker` et `admin` presents dans les controllers et templates
- API front: recherche / filtres menus

Plusieurs briques restent partielles ou cibles uniquement. Voir `docs/project/_DEV_ROADMAP.md`.

## Arborescence utile

```text
app/
|-- assets/
|   |-- controllers/
|   |-- images/
|   `-- styles/
|-- config/
|   |-- packages/
|   `-- routes/
|-- migrations/
|-- public/
|-- src/
|   |-- Controller/
|   |   |-- Admin/
|   |   |-- Api/
|   |   |-- Order/
|   |   |-- Security/
|   |   |-- User/
|   |   `-- Worker/
|   |-- DataFixtures/
|   |-- Dto/
|   |   |-- Admin/
|   |   |-- MenuApi/
|   |   |-- Order/
|   |   `-- User/
|   |-- Entity/
|   |-- Exception/
|   |-- Form/
|   |   |-- Contact/
|   |   |-- Order/
|   |   |-- Security/
|   |   `-- User/
|   |-- Handler/
|   |   |-- Order/
|   |   `-- User/
|   |-- Repository/
|   |-- Security/
|   |-- Service/
|   |   |-- Admin/
|   |   |-- Mail/
|   |   |-- Order/
|   |   `-- User/
|   `-- Twig/
|-- templates/
|   |-- admin/
|   |-- contact/
|   |-- emails/
|   |-- menu/
|   |-- order/
|   |-- pages/
|   |-- partials/
|   |-- security/
|   |-- shared/
|   |-- user/
|   `-- worker/
`-- tests/
    |-- Controller/
    |-- Functional/
    |-- Integration/
    |-- Support/
    `-- Unit/
```

## Repartition des responsabilites

- `Controller/`: HTTP, routing, rendu Twig, redirections
- `Form/`: mapping + validation des formulaires
- `Handler/` et `Service/`: logique applicative et metier
- `Repository/`: acces Doctrine
- `Dto/`: structures de transfert pour l'API et certaines couches metier
- `templates/`: rendu UI et composants Twig
- `tests/`: couverture fonctionnelle, integration et unitaire

## Regles metier a garder coherentes

- Filtres menus dynamiques sans rechargement de page
- Livraison: Bordeaux = 0 EUR, sinon 5 EUR + 0.59 EUR/km
- Reduction: -10% si le nombre de personnes atteint `minimum + 5`
- Workflow cible des statuts commande:
  `acceptee -> en preparation -> en cours de livraison -> livree -> en attente du retour de materiel -> terminee`
- Avis visibles sur l'accueil uniquement apres validation employee/admin

## Securite et qualite

- Roles attendus: `ROLE_USER`, `ROLE_WORKER`, `ROLE_ADMIN`
- Configuration securite: `app/config/packages/security.yaml`
- Adresses mail techniques injectees via `NO_REPLY_ADDRESS` et `OWNER_ADDRESS`
- Verification standard:
  - `make test`
  - `make check`

## Limites visibles dans le repo

- La partie MongoDB / statistiques admin reste une cible, pas une brique clairement finalisee dans la configuration actuelle
- Plusieurs actions worker/admin semblent encore surtout preparees cote UI ou backlog
- `sql/schema.sql` et `sql/seed.sql` restent des livrables a produire
