Review technique - 2026-03-02

Etat actuel:

- make test: OK (14 tests, 88 assertions).

Priorite haute:

- Mail config: fiabiliser les adresses expediteur/destinataire (NOREPLY_ADRESS, OWNER_ADRESS) et eviter les 500 si variable vide.

Priorite moyenne:

- ReviewRepository::findFiveRandomReviews: le nom indique 5 mais la requete limite a 3; harmoniser.
- ReviewRepository: remplacer ORDER BY RAND() par une strategie moins couteuse.
- Contact mail: utiliser une adresse interne en from + replyTo(contact) pour une meilleure delivrabilite.

Dette technique:

- Extraire une fabrique/service d'emails pour eviter la duplication entre RegisterController et ContactController.
