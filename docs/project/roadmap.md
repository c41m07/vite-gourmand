# Roadmap

Document de pilotage versionne unique pour suivre l'etat du projet, les priorites et l'ordre de finition.

Les notes de session et checklists locales a fort churn ne doivent plus servir de point d'entree principal.

## Ce qui semble deja en place

- Pages publiques: accueil, menus, contact, mentions legales, CGV
- UI Twig + Bootstrap avec base accessibilite (skip link, focus visible, labels)
- Recherche de menus avec filtres dynamiques sans reload
- Entites Doctrine et migrations pour le coeur metier
- Authentification utilisateur et base de reset password
- Contact avec persistence + envoi de mail
- Parcours commande cote utilisateur avec calcul du prix, de la remise et de la livraison
- Previsualisation / confirmation de commande en deux etapes avant persistence
- Retour a l'edition apres previsualisation avec recapitulatif dedie
- Dashboards `worker` et `admin` en lecture avec UI deja presente

## Ce qui reste partiel

- Edition de commande cote utilisateur: route et ecran presents, formulaire metier absent
- Annulation de commande cote utilisateur: cas `pending` livre, alignement cahier des charges incomplet
- Couverture de tests commande encore partielle sur les cas metier et cas limites
- Parcours avis apres commande terminee
- Actions reelles worker/admin sur menus, plats, horaires, avis et statuts: UI visible mais actions non branchees
- Gestion employee cote admin: liste presente, actions non branchees
- Horaires du footer: repository present mais rendu Twig encore commente
- Statistiques MongoDB: aucune brique d'integration visible dans le repo actuel
- Livrables SQL et deploiement

## Priorites dev

### Haute

- Journaliser les echecs d'envoi mail du formulaire de contact
- Finaliser le parcours commande: edition, annulation, securisation complementaire et tests
- Rendre effectives les actions worker/admin aujourd'hui surtout visibles dans l'UI
- Limiter l'autofill de connexion de demo a un usage dev explicite
- Rebrancher les horaires du footer sur les donnees `OpeningHour`

### Moyenne

- Completer les tests sur l'edition de commande, les refus metier, le contact et le back-office
- Clarifier le flux de mise a jour du profil utilisateur dans un service dedie
- Corriger le nommage des accesseurs `DateTime` sur `ContactMessage`
- Nettoyer le code commente genere dans certains repositories

### Basse

- Remplacer a terme `ORDER BY RAND()` si le volume des avis grossit
- Regrouper les controles de finition RGAA et securite avant mise en ligne

## Parcours et domaines encore a valider

### Parcours public

- Confirmer l'accueil complet contre le cahier des charges
- Confirmer la couverture complete des filtres menus
- Finaliser l'affichage des horaires dans le footer a partir des donnees `OpeningHour`

### Authentification et compte utilisateur

- Finaliser la modification complete des informations personnelles utilisateur

### Parcours commande client

- Finaliser l'ecran de modification de commande, sans changement de menu
- Aligner totalement les regles de modification / annulation sur la regle `non accepted`
- Completer le suivi de commande avec historique des statuts cote utilisateur
- Renforcer les tests commande sur les refus metier et les cas limites

### Parcours avis

- Permettre le depot d'avis apres commande terminee
- Ajouter la notification de fin de commande qui invite au depot d'avis
- Relier la moderation employee/admin au back-end

### Back-office employe

- Relier les filtres commandes et le changement de statut au back-end
- Finaliser l'historique des statuts
- Finaliser les CRUD menus, plats et horaires
- Relier la moderation des avis au back-end
- Imposer les contraintes metier avant annulation / modification

### Back-office administrateur

- Permettre la creation et la desactivation de comptes employe
- Verifier explicitement qu'aucun compte admin ne peut etre cree depuis l'application
- Livrer les statistiques NoSQL attendues

### Qualite et livraison

- Completer la couverture de tests sur les parcours critiques
- Finaliser la CI, l'audit RGAA et la verification finale
- Livrer les exports SQL, la doc finale, le manuel utilisateur et les liens de rendu

## Ordre recommande pour terminer

1. Finaliser le parcours commande utilisateur: edition, annulation et couverture de tests
2. Relier le workflow des statuts et les actions du back-office employe
3. Rebrancher les horaires du footer et retirer l'autofill de demo hors dev
4. Terminer le parcours avis utilisateur puis sa moderation
5. Finaliser le back-office administrateur et les statistiques
6. Boucler qualite, accessibilite, deploiement et livrables ECF

## Livrables techniques encore ouverts

- `sql/schema.sql`
- `sql/seed.sql`
- Documentation de deploiement finale
- Comptes de demonstration et jeu de donnees coherent
- Liens de rendu ECF a confirmer: depot public, app deployee, board projet

## Organisation projet

- Board Trello: <https://trello.com/invite/b/6971ee5dd9c0bce3e87fa0db/ATTId544b769ee4c66e71b1db522ef22581f07368E5B/vite-gourmand-ecf>
- Convention historique documentee: `main`, `develop`, `feature/*`
- Definition minimale de done:
  - correction ciblee
  - references coherentes
  - verification concrete
  - tests ou justification explicite si non lances
