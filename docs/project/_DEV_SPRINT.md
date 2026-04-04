# _DEV_SPRINT

Plan de sprint pour finaliser le projet, base sur l'etat du repo au 2026-04-04.

## Sprint 1 - Socle projet

- [x] Stack Symfony 8 + Doctrine + Twig + Stimulus en place
- [x] Docker Compose + Make operationnels
- [x] Base relationnelle et migrations initiales presentes
- [x] Fixtures de base disponibles
- [x] Documentation de setup et d'architecture disponible

## Sprint 2 - Parcours public

- [x] Page d'accueil publique
- [x] Liste des menus accessible aux visiteurs
- [x] Filtres dynamiques de menus sans rechargement
- [x] Vue detaillee d'un menu
- [x] Contact avec persistence et envoi de mail
- [x] Mentions legales et CGV disponibles

## Sprint 3 - Authentification et espace utilisateur de base

- [x] Inscription utilisateur avec mail de bienvenue
- [x] Connexion utilisateur
- [x] Reinitialisation du mot de passe
- [x] Controle d'acces par role user / worker / admin
- [x] Consultation du profil utilisateur
- [x] Consultation du detail des commandes utilisateur
- [x] Annulation d'une commande tant qu'elle est encore `pending`
- [ ] Modification complete d'une commande hors choix du menu
- [ ] Notification de fin de commande pour inviter au depot d'avis
- [ ] Depot d'un avis utilisateur avec note de 1 a 5 et commentaire

## Sprint 4 - Parcours commande metier

- [x] Acces a la commande depuis la fiche menu
- [x] Formulaire de commande pre-rempli avec les informations utilisateur
- [x] Calcul du prix, de la remise et de la livraison
- [x] Envoi du mail de confirmation apres creation
- [ ] Recapitulatif de commande confirme cote serveur avant persistence
- [ ] Ecran de modification de commande fonctionnel
- [ ] Regles completes de modification / annulation alignees sur le cahier des charges
- [ ] Couverture de tests complete sur les cas commande

## Sprint 5 - Back-office employe

- [x] Dashboard employe disponible
- [x] Vues de gestion commandes / menus / avis presentes
- [ ] Filtre de commandes par statut
- [ ] Filtre de commandes par client
- [ ] Changement de statut de commande relie au back-end
- [ ] Workflow complet des statuts (`accepted` -> `preparing` -> `delivering` -> `delivered` -> `waiting_equipment_return` -> `completed`)
- [ ] Historique de statuts exploitable dans le back-office
- [ ] Motif + mode de contact obligatoires avant annulation / modification
- [ ] CRUD menus cote employe
- [ ] CRUD plats cote employe
- [ ] CRUD horaires cote employe
- [ ] Validation / refus des avis relie au back-end
- [ ] Mail d'avertissement pour retour de materiel sous 10 jours / 600 EUR

## Sprint 6 - Back-office administrateur

- [x] Dashboard administrateur disponible
- [x] Hierarchie de roles admin -> worker active
- [ ] Creation de compte employe depuis l'application
- [ ] Mail de notification de creation de compte employe
- [ ] Desactivation d'un compte employe
- [ ] Garantie explicite qu'aucun compte admin ne peut etre cree depuis l'application
- [ ] Statistiques via une base NoSQL
- [ ] Nombre de commandes par menu avec comparaison graphique
- [ ] Chiffre d'affaires par menu avec filtre menu + periode

## Sprint 7 - Qualite, securite et finition

- [x] Tests PHPUnit en place
- [x] Analyse statique PHPStan en place
- [ ] Journalisation des echecs d'envoi mail contact
- [ ] Encadrement de l'autofill de demo en mode dev-only
- [ ] CI projet pour tests + phpstan + lint
- [ ] Audit RGAA final
- [ ] Corrections finales accessibilite / securite
- [ ] Verification complete des parcours critiques avant livraison

## Sprint 8 - Deploiement et livrables ECF

- [x] README local disponible
- [x] Documentation dev de base disponible
- [x] Cahier des charges ECF reintegre dans `docs/references`
- [ ] `sql/schema.sql` final
- [ ] `sql/seed.sql` final
- [ ] Documentation technique finale complete avec diagrammes et procedure de deploiement
- [ ] Manuel utilisateur PDF
- [ ] Charte graphique PDF finalisee
- [ ] Documentation de gestion de projet finalisee
- [ ] Depot GitHub public confirme
- [ ] Application deployee et lien de rendu disponible
- [ ] Lien du board projet confirme

## Ordre recommande pour terminer

- [ ] Sprint 4 - Parcours commande metier
- [ ] Sprint 5 - Back-office employe
- [ ] Sprint 3 - Parcours avis utilisateur
- [ ] Sprint 6 - Back-office administrateur
- [ ] Sprint 7 - Qualite, securite et finition
- [ ] Sprint 8 - Deploiement et livrables ECF
