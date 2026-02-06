# Avancement

## Sprint 0
- [x] Repo GitHub public + branches main/develop + convention feature/
- [x] Initialisation Symfony 8 (webapp) + lancement local OK
- [x] Structure repo : /docs + /sql + .gitignore + README
- [x] Config base MariaDB locale + doctrine:database:create
- [x] Preparer livrables ECF (dossiers docs + plan des PDFs)

## Sprint 1
- [x] Maquettes Desktop x3 (Accueil / Menus + filtres / Detail menu)
- [x] Maquettes Mobile x3 (Accueil / Menus + filtres / Detail menu)
- [x] Charte graphique PDF (couleurs, typo, composants Bootstrap, formulaires, alertes)

## Sprint 2
- [x] Base Twig + Bootstrap + layout (navbar/footer)
- [x] Footer : horaires + liens Mentions legales + CGV
- [x] Pages publiques : Accueil / Menus / Contact / Mentions legales / CGV
- [x] RGAA base : structure semantique + focus visible + labels formulaires

## Sprint 3
- [x] Modele de donnees cible (MCD + relations)
- [ ] Entites Doctrine : User + Role
- [ ] Entites Doctrine : Menu + Theme + Regime
- [ ] Entites Doctrine : Dish + MenuDish (relation)
- [ ] Entites Doctrine : Allergen + DishAllergen (relation)
- [ ] Entites Doctrine : Order + OrderStatusHistory
- [ ] Entites Doctrine : Review (avis) + validation
- [ ] Entites Doctrine : OpeningHour + ContactMessage
- [ ] Migrations Doctrine : generate + migrate
- [ ] Exporter sql/schema.sql
- [ ] Creer sql/seed.sql (donnees + comptes demo)

## Sprint 4
- [ ] Accueil : afficher avis valides
- [ ] Endpoint recherche menus (prix/theme/regime/min personnes)
- [ ] Gestion stock menu (affichage + blocage si stock=0)
- [ ] JS Fetch : filtres sans rechargement + rendu resultats
- [ ] Page Detail Menu : infos completes + conditions mises en avant
- [x] Page Menus : listing + cards Bootstrap

## Sprint 5
- [ ] Security : firewall + acces par roles USER/EMPLOYEE/ADMIN
- [ ] Inscription utilisateur (mdp fort + validations)
- [ ] Mail de bienvenue (Symfony Mailer)
- [ ] Connexion / Deconnexion
- [ ] Mot de passe oublie (ResetPasswordBundle)
- [ ] Page Profil utilisateur (affichage infos)

## Sprint 6
- [ ] Formulaire commande (pre-rempli si connecte)
- [ ] Pre-selection menu depuis detail menu
- [ ] Calcul livraison (Bordeaux=0 sinon 5 EUR + 0,59 EUR/km)
- [ ] Validation nb personnes >= min menu
- [ ] Reduction -10% si nb personnes >= min+5
- [ ] Recap prix avant validation (menu + livraison + remise)
- [ ] Creation commande + statut initial + historique statuts
- [ ] Mail confirmation commande

## Sprint 7
- [ ] Dashboard user : liste commandes
- [ ] Detail commande : infos + historique statuts
- [ ] Modifier commande si statut != accepte (menu non modifiable)
- [ ] Annuler commande si statut != accepte
- [ ] Mail avis a commande "terminee" (lien + instructions)
- [ ] Formulaire avis (note 1-5 + commentaire) + statut "en attente"

## Sprint 8
- [ ] Back-office employe : acces + navigation
- [ ] CRUD Menus + stock
- [ ] CRUD Plats + liaison menus (MenuDish)
- [ ] CRUD Horaires (OpeningHour)
- [ ] Liste commandes employe + filtres (statut/client)
- [ ] Changement statut commande (workflow + historique)
- [ ] Statut "attente retour materiel" : mail avertissement
- [ ] Annulation/modif employe : motif + mode contact obligatoire
- [ ] Moderation avis : valider/refuser

## Sprint 9
- [ ] Admin : acces + navigation
- [ ] Admin : creer employe + mail "compte cree" sans mdp
- [ ] Admin : desactiver employe
- [ ] MongoDB : modele documents stats
- [ ] MongoDB : insertion stat a passage "acceptee"
- [ ] Stats : nb commandes par menu (agregation)
- [ ] Stats : CA par menu + filtres (menu + dates)
- [ ] Graphique Chart.js (dashboard stats)

## Sprint 10
- [ ] RGAA : audit + correctifs (labels/focus/erreurs/contraste)
- [ ] Securite : durcissement (CSRF/validation/acces/upload)
- [ ] Pages legales : Mentions legales + CGV + donnees personnelles
- [ ] Deploiement : mise en ligne + variables env + BDD
- [ ] Donnees finales : seed coherent + comptes demo
- [ ] MongoDB prod : connexion + verif stats
- [ ] PDF Manuel utilisateur final (avec identifiants)
- [ ] PDF Charte graphique finale (avec exports maquettes)
- [ ] Doc gestion de projet finale (captures Trello + methode)
- [ ] Doc technique finale (schemas + deploiement + justifs)

## Sans sprint
- [x] Methode de travail
- [x] Definition of Done
