# _DEV_ROADMAP

Roadmap fusionnee depuis l'ancien suivi, les TODOs techniques et la doc projet.

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
- Dashboards `worker` et `admin`

## Ce qui reste partiel

- Edition et annulation de commande cote utilisateur
- Couverture de tests commande encore partielle sur les cas metier et cas limites
- Parcours avis apres commande terminee
- Actions reelles worker/admin sur menus, plats, horaires et statuts
- Gestion employee cote admin
- Statistiques MongoDB
- Livrables SQL et deploiement

## Priorites dev

### Haute

- Journaliser les echecs d'envoi mail du formulaire de contact
- Finaliser le parcours commande: edition, annulation, securisation complementaire et tests
- Rendre effectives les actions worker/admin aujourd'hui surtout visibles dans l'UI
- Limiter l'autofill de connexion de demo a un usage dev explicite

### Moyenne

- Completer les tests sur l'edition de commande, les refus metier, le contact et le back-office
- Clarifier le flux de mise a jour du profil utilisateur dans un service dedie
- Corriger le nommage des accesseurs `DateTime` sur `ContactMessage`
- Nettoyer le code commente genere dans certains repositories

### Basse

- Remplacer a terme `ORDER BY RAND()` si le volume des avis grossit
- Regrouper les controles de finition RGAA et securite avant mise en ligne

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
