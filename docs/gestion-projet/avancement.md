## Sprint 0
- [x] Repo GitHub public + branches main/develop + convention feature/
- [x] Initialisation Symfony 7 (webapp) + lancement local OK
- [x] Structure repo : /docs + /sql + .gitignore + README
- [x] Config base MySQL locale + doctrine:database:create
- [x] Préparer livrables ECF (dossiers docs + plan des PDFs)

## Sprint 1
- [x] Maquettes Desktop x3 (Accueil / Menus+filtres / Détail menu)
- [x] Maquettes Mobile x3 (Accueil / Menus+filtres / Détail menu)
- [x] Charte graphique PDF (couleurs, typo, composants Bootstrap, formulaires, alertes)

## Sprint 2
- [x] Base Twig + Bootstrap + layout (navbar/footer)
- [x] Footer : horaires + liens Mentions légales + CGV
- [x] Pages publiques : Accueil / Menus / Contact / Mentions légales / CGV
- [x] RGAA base : structure sémantique + focus visible + labels formulaires

## Sprint 3
- [ ] Modèle de données final (MCD -> tables cibles)
- [ ] Entités Doctrine : User + Role
- [ ] Entités Doctrine : Menu + Theme + Regime
- [ ] Entités Doctrine : Dish + MenuDish (relation)
- [ ] Entités Doctrine : Allergen + DishAllergen (relation)
- [ ] Entités Doctrine : Order + OrderStatusHistory
- [ ] Entités Doctrine : Review (avis) + validation
- [ ] Entités Doctrine : OpeningHour + ContactMessage
- [ ] Migrations Doctrine : generate + migrate
- [ ] Exporter sql/schema.sql
- [ ] Créer sql/seed.sql (données + comptes démo)

## Sprint 4
- [ ] Accueil : afficher avis validés
- [ ] Endpoint recherche menus (prix/thème/régime/min personnes)
- [ ] Gestion stock menu (affichage + blocage si stock=0)
- [ ] JS Fetch : filtres sans rechargement + rendu résultats
- [ ] Page Détail Menu : infos complètes + conditions mises en avant
- [x] Page Menus : listing + cards Bootstrap

## Sprint 5
- [ ] Security : firewall + accès par rôles USER/EMPLOYEE/ADMIN
- [ ] Inscription utilisateur (mdp fort + validations)
- [ ] Mail de bienvenue (Symfony Mailer)
- [ ] Connexion / Déconnexion
- [ ] Mot de passe oublié (ResetPasswordBundle)
- [ ] Page Profil utilisateur (affichage infos)

## Sprint 6
- [ ] Formulaire commande (pré-rempli si connecté)
- [ ] Pré-sélection menu depuis détail menu
- [ ] Calcul livraison (Bordeaux=0 sinon 5 EUR + 0,59 EUR/km)
- [ ] Validation nb personnes >= min menu
- [ ] Réduction -10% si nb personnes >= min+5
- [ ] Récap prix avant validation (menu + livraison + remise)
- [ ] Création commande + statut initial + historique statuts
- [ ] Mail confirmation commande

## Sprint 7
- [ ] Dashboard user : liste commandes
- [ ] Détail commande : infos + historique statuts
- [ ] Modifier commande si statut != accepté (menu non modifiable)
- [ ] Annuler commande si statut != accepté
- [ ] Mail avis à commande "terminée" (lien + instructions)
- [ ] Formulaire avis (note 1-5 + commentaire) + statut "en attente"

## Sprint 8
- [ ] Back-office employé : accès + navigation
- [ ] CRUD Menus + stock
- [ ] CRUD Plats + liaison menus (MenuDish)
- [ ] CRUD Horaires (OpeningHour)
- [ ] Liste commandes employé + filtres (statut/client)
- [ ] Changement statut commande (workflow + historique)
- [ ] Statut "attente retour matériel" : mail avertissement
- [ ] Annulation/modif employé : motif + mode contact obligatoire
- [ ] Modération avis : valider/refuser

## Sprint 9
- [ ] Admin : accès + navigation
- [ ] Admin : créer employé + mail "compte créé" sans mdp
- [ ] Admin : désactiver employé
- [ ] MongoDB : modèle documents stats
- [ ] MongoDB : insertion stat à passage "acceptée"
- [ ] Stats : nb commandes par menu (agrégation)
- [ ] Stats : CA par menu + filtres (menu + dates)
- [ ] Graphique Chart.js (dashboard stats)

## Sprint 10
- [ ] RGAA : audit + correctifs (labels/focus/erreurs/contraste)
- [ ] Sécurité : durcissement (CSRF/validation/accès/upload)
- [ ] Pages légales : Mentions légales + CGV + données personnelles
- [ ] Déploiement : mise en ligne + variables env + BDD
- [ ] Données finales : seed cohérent + comptes démo
- [ ] MongoDB prod : connexion + vérif stats
- [ ] PDF Manuel utilisateur final (avec identifiants)
- [ ] PDF Charte graphique final (avec exports maquettes)
- [ ] Doc gestion de projet final (captures Trello + méthode)
- [ ] Doc technique final (schémas + déploiement + justifs)

## Sans sprint
- [ ] test
- [x] Méthode de travail
- [x] Definition of Done
