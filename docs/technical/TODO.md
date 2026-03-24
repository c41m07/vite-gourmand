Review technique - 2026-03-24

Etat des verifications:

- PHPUnit complet: OK (32 tests, 179 assertions).
- Symfony `check:security`: OK (aucune vulnerabilite connue sur les dependances).
- PHPStan: OK.

Priorite haute:

- [ ] Journaliser les echecs d'envoi mail contact pour conserver une trace exploitable.
  Source: `app/src/Controller/ContactController.php`.

Priorite moyenne:

- [ ] Fiabiliser le recapitulatif de commande. Le visuel existe, mais `orderPreview` n'est jamais alimente et la commande est persistee
  directement sans vraie etape de confirmation metier.
  Source: `app/src/Controller/Order/OrderController.php` + `app/templates/order/index.html.twig`.
- [ ] Rendre effectives les actions employee/admin actuellement seulement visuelles: changement de statut, CRUD menu, desactivation
  d'employe.
  Source: `app/templates/worker/components/_orders_panel.html.twig`, `app/templates/worker/components/_menus_panel.html.twig`,
  `app/templates/admin/components/_employees_panel.html.twig`.
- [ ] Encadrer l'autofill de connexion pour l'usage dev (`dev-only` ou variable locale), au lieu de le laisser expose globalement.
  Source: `app/assets/app.js`, `app/assets/DEMO-login-autofill.js`, `app/templates/security/login.html.twig`.

Priorite basse:

- [ ] Incoherence de nommage des accesseurs mutateurs DateTime (`getcreatedAt`/`setcreatedAt`).
  Source: `app/src/Entity/ContactMessage.php` et usage `ContactController.php`.
- [ ] Nettoyer le code commente genere dans les repositories pour clarifier la lecture.
  Source: `app/src/Repository/ReviewRepository.php` et repositories generes Doctrine.
- [ ] `ORDER BY RAND()` restera vite couteux si les avis grossissent.
  Source: `app/src/Repository/ReviewRepository.php`.

Dette technique:

- [ ] Completer la couverture des routes commandes utilisateur avec le cas `edit`.
- [ ] Ajouter des tests sur le cas d'echec d'envoi mail contact (`/contact/failed`) et la strategie de persistance associee.
- [ ] Ajouter des tests sur les futures actions worker/admin (statuts, moderation, gestion employes).
- [ ] Mettre en place une CI (tests + phpstan + lint twig/php) pour eviter les regressions.
- [ ] Refactor propre du flux `ProfileController::edit()` : extraire la logique metier dans un service dedie (`ProfileUpdateService` + DTO resultat),
  garder un controleur HTTP thin (form/redirect/render), et couvrir les regles metier par tests unitaires.

Pages a creer / finaliser (certaines encore liees a `app_under_construction`):

- [ ] Page "Modifier une commande client".
  Source: `app/templates/user/order_edit.html.twig`.
- [ ] Page "Worker - Creer un nouveau menu".
  Source: `app/templates/worker/dashboard.html.twig` (bouton "Nouveau menu").
- [ ] Page "Worker - Modifier un menu".
  Source: `app/templates/worker/dashboard.html.twig` (bouton "Modifier").
- [ ] Actions admin de gestion des employes.
  Source: `app/templates/admin/components/_employees_panel.html.twig`.

Reste a faire (finalisation ECF - base `docs/references/ecf-studi.md`):

- [ ] Fiabiliser le parcours commande deja en place (recap final, validations, edition/annulation, tests).
- [ ] Finaliser l'espace utilisateur (modification/annulation selon regles, puis parcours avis).
- [ ] Implementer le parcours avis utilisateur (note 1-5 + commentaire) apres commande terminee.
- [ ] Back-office employe: CRUD menus/plats/horaires et filtres commandes (statut/client).
- [ ] Back-office employe: workflow de statuts commande (`acceptee` -> `en preparation` -> `en cours de livraison` -> `livree` ->
  `en attente du retour de materiel` -> `terminee`) avec historique.
- [ ] Back-office employe: imposer motif + mode de contact (mail/GSM) avant annulation/modification commande.
- [ ] Envoyer le mail d'avertissement "retour materiel sous 10 jours ou 600 EUR".
- [ ] Moderation des avis par employe/admin (valider/refuser) pour affichage accueil.
- [ ] Espace admin: creation compte employe + mail de notification (sans mot de passe dans le mail).
- [ ] Espace admin: desactivation d'un compte employe.
- [ ] Verifier qu'aucune creation de compte admin n'est possible depuis l'application.
- [ ] Implementer la base NoSQL (MongoDB) pour les stats demandees.
- [ ] Exposer dans l'espace admin: nombre de commandes par menu + comparaison graphique.
- [ ] Exposer dans l'espace admin: chiffre d'affaires par menu avec filtres (menu + periode).
- [ ] Finaliser l'accessibilite RGAA (audit, corrections contraste/clavier/erreurs/formulaires).
- [ ] Finaliser la securite applicative (validation stricte, CSRF, controle d'acces, protection des routes back-office).
- [ ] Produire `sql/schema.sql` et `sql/seed.sql` conformes a la BDD finale.
- [ ] Preparer des comptes de demonstration (user/worker/admin) et jeux de donnees coherents.
- [ ] Deployer l'application en ligne et verifier les parcours critiques en production.
- [ ] Mettre a jour `README.md` avec la procedure locale + deploiement.
- [ ] Fournir le lien du depot GitHub public et verifier la strategie Git (main/develop/feature).
- [ ] Fournir le lien de l'application deployee.
- [ ] Fournir le lien de l'outil de gestion de projet (Trello/Jira/Notion).
- [ ] Livrable: manuel utilisateur en PDF avec presentation + identifiants de test.
- [ ] Livrable: charte graphique en PDF (palette, typo, exports maquettes desktop/mobile).
- [ ] Livrable: documentation de gestion de projet.
- [ ] Livrable: documentation technique complete (choix techno, setup, MCD/diagramme de classes, diagramme d'utilisation, diagramme de sequence,
  procedure de deploiement).
