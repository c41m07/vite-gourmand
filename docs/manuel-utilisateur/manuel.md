# Manuel utilisateur — Vite & Gourmand

## 0. État actuel (03/02/2026)
- Pages publiques disponibles : Accueil, Menus, Contact, Mentions légales, CGV.
- Menus : listing statique + filtres UI (données non connectées à la BDD).
- Authentification, commandes, back-office, mails : en cours d’implémentation.

## 1. Présentation
Application web permettant de consulter des menus, créer un compte, commander, suivre une commande et laisser un avis.

## 2. Accès à l’application
- URL (prod) : [à compléter avec l’URL de déploiement]
- URL (local) : http://localhost (selon configuration Traefik)

## 3. Comptes de démonstration
> À fournir dans le PDF final.

- ADMIN :
  - Email : [à compléter]
  - Mot de passe : [à compléter]
- EMPLOYÉ :
  - Email : [à compléter]
  - Mot de passe : [à compléter]
- UTILISATEUR :
  - Email : [à compléter]
  - Mot de passe : [à compléter]

## 4. Parcours Visiteur
- Accéder à l’accueil (présentation, avis).
- Voir la liste des menus.
- Utiliser les filtres (prix max, fourchette, thème, régime, min personnes).
- Voir le détail d’un menu (à venir).
- Contacter l’entreprise via le formulaire.

## 5. Parcours Utilisateur (à venir)
- Créer un compte (validation mot de passe fort).
- Se connecter / mot de passe oublié (lien par mail).
- Commander un menu (formulaire pré-rempli si clic depuis un menu).
- Voir ses commandes / suivi.
- Annuler/modifier une commande tant qu’elle n’est pas “acceptée”.
- Laisser un avis après commande “terminée”.

## 6. Parcours Employé (à venir)
- Gérer menus / plats / horaires.
- Filtrer les commandes (statut ou client).
- Mettre à jour les statuts (acceptée -> préparation -> livraison -> terminée).
- Valider/refuser les avis.

## 7. Parcours Admin (à venir)
- Créer/désactiver des comptes employé.
- Accéder aux statistiques (commandes par menu + CA).

## 8. FAQ / Dépannage
- Problèmes de connexion : vérifier email/mot de passe et statut du compte.
- Mails non reçus : vérifier dossier spam.
