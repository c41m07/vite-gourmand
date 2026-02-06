# Manuel utilisateur - Vite & Gourmand

## 0. Etat actuel (fevrier 2026)
- Pages publiques disponibles : Accueil, Menus, Contact, Mentions legales, CGV.
- Pages d'authentification disponibles : inscription + connexion.
- Menus : listing statique + filtres UI (donnees non connectees a la BDD).
- Commandes, back-office, mails : en cours d'implementation.

## 1. Presentation
Application web permettant de consulter des menus, creer un compte, commander, suivre une commande et laisser un avis.

## 2. Acces a l'application
- URL (prod) : a completer avec l'URL de deploiement.
- URL (local) : via Traefik (variable `TRAEFIK_URL_APP`).

## 3. Comptes de demonstration
> A fournir dans le PDF final.

- ADMIN :
  - Email : a completer
  - Mot de passe : a completer
- EMPLOYE :
  - Email : a completer
  - Mot de passe : a completer
- UTILISATEUR :
  - Email : a completer
  - Mot de passe : a completer

## 4. Parcours visiteur
- Acceder a l'accueil (presentation, avis).
- Voir la liste des menus.
- Utiliser les filtres (prix max, fourchette, theme, regime, min personnes).
- Voir le detail d'un menu (a venir).
- Contacter l'entreprise via le formulaire.

## 5. Parcours utilisateur (partiel)
- Creer un compte (validation de base) - disponible.
- Se connecter (page de connexion) - disponible.
- Mot de passe oublie (lien par mail) - a venir.
- Commander un menu (formulaire pre-rempli si clic depuis un menu).
- Voir ses commandes / suivi.
- Annuler/modifier une commande tant qu'elle n'est pas "acceptee".
- Laisser un avis apres commande "terminee".

## 6. Parcours employe (a venir)
- Gerer menus / plats / horaires.
- Filtrer les commandes (statut ou client).
- Mettre a jour les statuts (acceptee -> preparation -> livraison -> terminee).
- Valider/refuser les avis.

## 7. Parcours admin (a venir)
- Creer/desactiver des comptes employe.
- Acceder aux statistiques (commandes par menu + CA).

## 8. FAQ / Depannage
- Problemes de connexion : verifier email/mot de passe et statut du compte.
- Mails non recus : verifier dossier spam.
