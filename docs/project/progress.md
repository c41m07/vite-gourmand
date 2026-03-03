# Avancement

## Sprint 0

- [x] Repo GitHub public + branches main/develop + convention feature/
- [x] Initialisation Symfony 8 (webapp) + lancement local OK
- [x] Structure repo : /docs + /sql + .gitignore + README
- [x] Config base MariaDB locale + doctrine:database:create
- [x] Preparer livrables ECF (dossiers docs + plan des PDFs)

## Sprint 1

- [x] Maquettes Desktop x3 (Accueil / Menus + filtres / Detail menu) - export `docs/design/maquettes/desktop.png`
- [x] Maquettes Mobile x3 (Accueil / Menus + filtres / Detail menu) - export `docs/design/maquettes/mobile.png`
- [x] Charte graphique + design system (couleurs, typo, composants Bootstrap, formulaires, alertes) - `docs/design/design-system.md` +
  `docs/design/assets/charte-graphique.png`

## Sprint 2

- [x] Base Twig + Bootstrap + layout (navbar/footer)
- [x] Footer : horaires + liens Mentions legales + CGV
- [x] Pages publiques : Accueil / Menus / Contact / Mentions legales / CGV
- [x] RGAA base : structure semantique + skip link + focus visible + labels formulaires

## Sprint 3

- [x] Modele de donnees cible (MCD + relations)
- [x] Entites Doctrine : User (roles JSON)
- [x] Entites Doctrine : Menu + Theme + Diet (regime) + Media
- [x] Entites Doctrine : Dish + DishType + MenuDish (relation)
- [x] Entites Doctrine : Allergen + DishAllergen (relation)
- [x] Entites Doctrine : CustomerOrder + CustomerOrderMenu (relation)
- [x] Entites Doctrine : OrderStatus + CustomerOrderStatusHistory
- [x] Entites Doctrine : EquipmentLoan + EquipmentLoanStatus
- [x] Entites Doctrine : Review (avis) + validation
- [x] Entites Doctrine : OpeningHour + ContactMessage
- [x] Migrations Doctrine : generees
- [ ] Exporter sql/schema.sql
- [ ] Creer sql/seed.sql (donnees + comptes demo)

## Sprint 4

- [x] Accueil : afficher avis valides
- [x] Endpoint recherche menus (prix/theme/diet/min personnes/stock/actif)
- [x] JS Fetch : filtres sans rechargement + rendu resultats
- [x] Affichage stock menu (detail menu)
- [x] Blocage si stock=0 (listing/detail/commande)
- [x] Page Detail Menu : infos completes + conditions mises en avant
- [x] Page Menus : listing + cards Bootstrap

## Sprint 5

- [x] Security : firewall + acces par roles USER/WORKER/ADMIN
- [x] Inscription utilisateur (formulaire + validations de base)
- [x] Mail de bienvenue (Symfony Mailer)
- [x] Refactor envoi email : extraction `EmailFactory` pour Register + Contact
- [x] Email contact : template HTML complet et lisible (resume + message)
- [x] Connexion / Deconnexion
- [ ] Mot de passe oublie (ResetPasswordBundle)
- [x] Page Profil utilisateur (affichage infos)

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
