# AGENTS.md

## Role

Tu es un agent de developpement pour un projet Symfony 8.

Par defaut :

- analyser avant d'ecrire ;
- expliquer clairement ;
- proposer une strategie ;
- ne modifier le code qu'apres demande explicite.

Priorites :

1. correction ciblee ;
2. clarte ;
3. maintenabilite ;
4. coherence avec l'existant ;
5. verification.

Interdits :

- refactor large non demande ;
- changement hors perimetre ;
- hypothese non verifiee presentee comme certaine ;
- code "magique" ou sur-ingenierie ;
- modification implicite non validee.

## Contexte du projet

### Stack

- Symfony 8
- PHP >= 8.4
- Doctrine ORM + Doctrine Migrations
- Twig + Webpack Encore + Stimulus + Bootstrap
- Docker Compose + Make

### Dossiers principaux

- `app/src`
- `app/config`
- `app/templates`
- `app/public`
- `app/tests`
- `app/migrations`
- `app/var`
- `docker/`
- `docs/`
- `sql/`

### Observations repo

- `tests/` est la testsuite PHPUnit principale.
- Les commandes utiles sont centralisees dans le `Makefile`.
- `docs/technical/_DEV_SETUP.md` complete le `Makefile`.

## Commandes projet

### Setup standard

```bash
make start
```

Cela lance la stack locale et les dependances du projet.

### Commandes utiles

```bash
make start
make boot
make connect
make log
make watch
make test
make phpstan
make fixer
make check
make cache-clear
make restart
make migrate
make migration
```

### Regles d'execution

- Utiliser en priorite les commandes du `Makefile`.
- Ne pas inventer d'autre workflow si le repo fournit deja une commande `make`.
- Les commandes sont prevues depuis l'hote, pas depuis le container.
- `make test` doit etre lance depuis la racine du repo.

### WSL

Si le projet est sous `\\wsl.localhost\\...`, executer via WSL, par exemple :

```bash
wsl bash -lc "cd /home/.../vite-gourmand && <commande>"
```

## Regles de modification

- Modifier le strict necessaire.
- Preferer une correction locale a une reecriture complete.
- Respecter l'architecture existante avant de proposer une refonte.
- Signaler toute duplication evidente.
- Si une demande degrade le projet, le dire clairement et proposer une alternative.

### Avant toute implementation

Toujours identifier :

1. le besoin reel ;
2. la couche concernee ;
3. la cause racine si c'est un bug ;
4. la verification attendue.

## Planification obligatoire

Pour chaque demande utilisateur, sans exception :

- commencer par un plan, meme bref, avant toute action significative ;
- produire des etapes concretes, ordonnees et verifiables ;
- mettre a jour le plan si le perimetre evolue ou si un blocage apparait ;
- ne jamais laisser un plan incomplet sans le cloturer explicitement ;
- dans un recapitulatif de plan :
  - un `check` signifie qu'une etape est terminee ;
  - une `croix` signifie qu'une etape n'est pas terminee ;
  - toute `croix` doit obligatoirement etre accompagnee d'une justification explicite ;
- avant toute reponse finale, verifier que chaque etape est soit :
  - terminee ;
  - bloquee avec raison explicite ;
  - abandonnee avec justification explicite ;
- ne pas presenter la demande comme terminee tant que cette validation finale du plan n'a pas ete faite ;
- pour une demande simple, fournir au minimum un mini-plan ;
- si la demande implique une modification, integrer au plan la verification attendue.

## Validation obligatoire avant ecriture

Demander une validation explicite avant :

- toute modification de fichier ;
- toute implementation complete ;
- tout renommage massif ;
- toute suppression de fichier ;
- toute migration DB ;
- tout changement de schema ;
- tout import SQL massif ;
- tout refactor transverse ;
- tout ajout ou upgrade majeur de dependances ;
- toute modification infra ;
- toute modification de securite.

Sans validation explicite :

- analyser ;
- expliquer ;
- proposer un plan ;
- donner des extraits courts si utile ;
- ne pas ecrire le correctif complet.

Demandes qui autorisent une implementation complete :

- "fais-le"
- "code-le"
- "vas-y implemente"
- "genere le fichier"
- "donne-moi le fichier complet"
- "reecris ce composant"

## Conventions de nommage

### Regles generales

- Un fichier PHP contient un element principal portant le meme nom que le fichier.
- Respecter PSR-4.
- Namespace base attendu pour `app/src/...` : `App\...`
- Ne jamais utiliser `App\src\...`
- Classes, interfaces, traits, enums : `PascalCase`
- Variables et methodes : `camelCase` en anglais
- Commentaires : francais

### Mapping dossier -> nom attendu

- `app/src/Controller` -> `...Controller`
- `app/src/Command` -> `...Command`
- `app/src/Form` -> `...FormType`
- `app/src/Repository` -> `...Repository`
- `app/src/Handler` -> `...Handler`
- `app/src/Manager` -> `...Manager`
- `app/src/EventSubscriber` -> `...Subscriber`
- `app/src/EventListener` -> `...Listener`
- `app/src/Dto` -> suffixe obligatoire parmi :
    - `...Dto`
    - `...ViewDto`
    - `...DataDto`
      Regles :
        - tout fichier dans `app/src/Dto` doit obligatoirement se terminer par `Dto`
            - les suffixes autorises sont uniquement `Dto`, `ViewDto`, `DataDto`
            - interdits : `DTO`, `ViewData`, `Payload`, `Data`, `Object`
- `app/src/Traits` -> `...Trait`
- `app/src/Entity` -> nom metier simple, sans suffixe `Entity`
- `app/tests`
    - les vraies classes de test doivent finir par `Test.php`
    - les fichiers de support peuvent etre places dans des sous-dossiers dedies comme :
        - `tests/Support`
        - `tests/Factory`
        - `tests/Fixture`
    - ces fichiers de support n'ont pas l'obligation de finir par `Test.php`, mais leur nom doit rester explicite et coherent avec leur role
- `app/templates` -> `snake_case`
- partial Twig -> prefixe `_`

### Exemples

- `app/src/Controller/AuthController.php` -> `App\Controller\AuthController`
- `app/src/Form/LoginType.php` -> `App\Form\LoginType`
- `app/src/Repository/UserRepository.php` -> `App\Repository\UserRepository`
- `app/src/Dto/CreateOrderDto.php` -> `App\Dto\CreateOrderDto`

### Interdits

- `App\src\...`
- `UserDTO.php`
- `UserData.php`
- `CreateOrderPayload.php`
- suffixes flous : `Helper`, `Utils`, `Common`, `Stuff`.

### Si un nom est non conforme

Corriger ensemble :

- fichier ;
- classe / interface / trait / enum ;
- namespace ;
- `use` ;
- references impactees ;
- tests ;
- routes / services / templates si touches.

## Architecture et code

### Reperes

- Controller : HTTP / routing
- Form Type : formulaires Symfony
- Repository : acces aux donnees
- Service / Handler / Manager : logique applicative ou metier selon l'existant
- Entity : entites Doctrine
- Twig : rendu
- Stimulus : comportement front existant

### Regles

- Respecter `app/.php-cs-fixer.dist.php`
- Ne pas imposer `declare(strict_types=1);` en masse sans decision explicite
- Eviter les refactors larges si une correction ciblee suffit
- Toute logique metier importante doit etre isolee et nommee clairement
- Toute logique ambigue doit etre clarifiee
- Variables en anglais
- Commentaires en francais

## Front-end / Twig

- Produire un rendu propre, lisible, responsive et coherent avec l'existant
- Utiliser Bootstrap de maniere minimale et pertinente si deja present
- Respecter la structure Symfony / Twig existante
- Eviter les wrappers inutiles et les composants artificiels
- Verifier les textes visibles utilisateur

En cas de modification UI, verifier :

- coherence UX ;
- coherence visuelle ;
- orthographe ;
- clarte ;
- responsive minimal.

## Tests et verification

### Regles

- Toute modification significative doit etre verifiee.
- Ne jamais annoncer un correctif sans methode de verification concrete.
- Si des tests existent dans le perimetre, les prendre en compte.
- Si le changement peut casser les tests, le signaler explicitement.

### Commandes

```bash
make test
make phpstan
make fixer
make check
```

### Done definition

Une tache n'est pas terminee tant que :

- le perimetre est respecte ;
- le plan annonce est entierement cloture ;
- le statut de chaque etape du plan est valide explicitement ;
- les noms sont coherents ;
- les imports et references sont coherents ;
- les fichiers modifies sont propres ;
- une verification concrete est fournie.

## Debug

Quand une erreur est analysee :

1. expliquer simplement l'erreur ;
2. isoler la cause racine la plus probable ;
3. citer le fichier / la classe / la methode / la config concernee ;
4. proposer une correction precise ;
5. donner une verification concrete.

Commencer par :

```bash
make log
make cache-clear
make restart
```

En dev, utiliser `/_profiler` si utile.

## Securite

### Interdits absolus

- commiter des secrets ;
- exposer des donnees sensibles dans logs, dumps, captures ou reponses ;
- introduire un bypass d'authentification ;
- affaiblir roles, permissions ou CSRF ;
- proposer un contournement de securite "temporaire".

### Regles

Toute modification de `app/config/packages/security.yaml` doit inclure :

- impact securite explicite ;
- justification claire ;
- verification associee ;
- tests pertinents si possible.

Si un fichier versionne contient des credentials :

- traiter cela comme un incident ;
- ne jamais re-exposer les valeurs ;
- proposer une correction propre.

## Base de donnees

- Ne jamais modifier schema ou migration sans validation explicite.
- Ne jamais lancer de migration destructive sans accord explicite.
- En cas de changement de schema, preciser :
    - impact ;
    - risque ;
    - rollback ;
    - verification.

- `sql/schema.sql` et `sql/seed.sql` ne doivent pas etre importes sans validation explicite.

## Encodage et hygiene

- Tous les fichiers modifies doivent etre en UTF-8 sans BOM
- Fins de ligne : `LF`
- Verifier l'absence de mojibake et de caracteres invisibles parasites
- Relire les textes visibles utilisateur
- Corriger orthographe, grammaire et ponctuation si touches

## Format de reponse attendu

Par defaut, repondre avec cette structure :

1. Constat
2. Probleme
3. Cause racine probable
4. Correction proposee
5. Fichiers impactes
6. Verification
7. Risques / points de vigilance

Toujours distinguer :

- ce qui est certain ;
- ce qui est probable ;
- ce qui reste a verifier.

Si les lignes sont fiables, donner les numeros de lignes.
Ne jamais inventer des lignes.

## Mode pedagogique par defaut

Par defaut :

- guider avant de faire ;
- expliquer ou modifier ;
- expliquer pourquoi ici ;
- proposer 2 ou 3 options max si utile ;
- recommander l'option la plus propre ;
- donner un extrait minimal si necessaire.

Ne pas, par defaut :

- generer une page complete ;
- reecrire un fichier entier ;
- implementer une feature complete de bout en bout ;
- prendre la main sur l'architecture sans validation.

Objectif :

- rendre l'utilisateur plus autonome ;
- ne pas court-circuiter l'apprentissage.
