# Manuel utilisateur - Vite & Gourmand

## 0. Etat actuel (mars 2026)
- Pages publiques disponibles: Accueil, Menus, Contact, Mentions legales, CGV.
- Pages d'authentification disponibles: inscription (`/register`), connexion (`/login`) et profil (`/user/profile`).
- Menus: listing dynamique + filtres connectes a la BDD (API `GET /api/menus`).
- Detail menu: route `/menu/{id}` disponible uniquement pour les menus actifs (menu inactif => 404).
- Contact: formulaire persiste en BDD + envoi d'email + redirection vers `/contact/success`.
- Non disponible a ce jour: commandes, espace employe/admin, reset mot de passe, statistiques.

## 1. Presentation
Application web permettant actuellement de consulter des menus, creer un compte, se connecter, consulter son profil et contacter l'entreprise.

## 2. Acces a l'application
- URL (prod) : a completer avec l'URL de deploiement.
- URL (local) : via Traefik (variable `TRAEFIK_URL_APP`).
- En local, l'environnement est pilote via `app/.env.local` (`APP_ENV=dev` ou `APP_ENV=prod`).

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
- Voir la liste des menus (`/menu/`).
- Utiliser les filtres (prix max, fourchette, theme, regime, min personnes) via l'API `/api/menus`.
- Voir le detail d'un menu actif (`/menu/{id}`).
- Contacter l'entreprise via le formulaire (`/contact/`), puis etre redirige vers la page de confirmation.

## 5. Parcours utilisateur (etat reel)
- Creer un compte (validation de base + email de bienvenue) - disponible.
- Se connecter via la page `/login` - disponible.
- Acceder a `/user/profile` quand on est authentifie avec `ROLE_USER` - disponible.
- Si non connecte, l'acces a `/user/profile` est refuse et redirige vers `/login`.
- Mot de passe oublie, commandes, suivi commandes et avis - a venir.

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
