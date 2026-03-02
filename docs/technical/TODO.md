Review technique - 2026-03-02

Etat actuel:

- make test: OK (14 tests, 88 assertions).

Priorite haute:

- Mail config: ajouter un garde-fou applicatif si `NO_REPLY_ADDRESS` ou `OWNER_ADDRESS` est vide (eviter les 500 en runtime).

Priorite moyenne:

- ReviewRepository: remplacer ORDER BY RAND() par une strategie moins couteuse.
- Les reviews peuvent apparaitre en doublon dans la liste.

Reste a faire (finalisation ECF - base `docs/references/ecf-studi.md`):

- [ ] Implementer le parcours complet de commande (formulaire, pre-remplissage depuis le menu detail, validation du min personnes, recap prix).
- [ ] Implementer le calcul livraison (Bordeaux = 0 EUR, sinon 5 EUR + 0,59 EUR/km).
- [ ] Implementer la reduction -10% pour `min_personnes + 5`.
- [ ] Envoyer un mail de confirmation apres creation de commande.
- [ ] Terminer l'espace utilisateur (liste/detail commandes, suivi des statuts date/heure, modification/annulation selon regles).
- [ ] Ajouter la fonctionnalite "mot de passe oublie" (demande par mail + reinitialisation).
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
