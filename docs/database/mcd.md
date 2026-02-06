# MCD - Vite & Gourmand

## Vue d'ensemble
Ce MCD decrit le modele cible de la base relationnelle. Les noms sont alignes sur les conventions de Doctrine (snake_case en base).

## Diagramme (Mermaid)
```mermaid
erDiagram
  USER ||--o{ CUSTOMER_ORDER : passe
  USER ||--o{ REVIEW : ecrit
  USER o|--o{ CUSTOMER_ORDER_STATUS_HISTORY : change

  CUSTOMER_ORDER ||--|{ CUSTOMER_ORDER_MENU : contient
  CUSTOMER_ORDER ||--o{ CUSTOMER_ORDER_STATUS_HISTORY : historique
  ORDER_STATUS ||--o{ CUSTOMER_ORDER_STATUS_HISTORY : type
  CUSTOMER_ORDER o|--o| EQUIPMENT_LOAN : materiel

  EQUIPMENT_LOAN_STATUS ||--o{ EQUIPMENT_LOAN : statut

  MEDIA o|--o{ MENU : illustre

  MENU ||--o{ MENU_DISH : compose
  DISH ||--o{ MENU_DISH : inclus
  DISH_TYPE o|--o{ DISH : categorie
  DISH ||--o{ DISH_ALLERGEN : contient
  ALLERGEN ||--o{ DISH_ALLERGEN : allergene

  MENU ||--o{ CUSTOMER_ORDER_MENU : commande
  REVIEW o|--o{ CUSTOMER_ORDER_MENU : lie

  THEME o|--o{ MENU : theme
  DIET o|--o{ MENU : regime
```

## Regles de modelisation
- `customer_order_menu` est la table associative entre `customer_order` et `menu` (quantite, prix unitaire, review_id optionnel).
- `menu_dish` et `dish_allergen` sont des tables de liaison many-to-many.
- `customer_order` peut lier 0 ou 1 `equipment_loan` (contrainte d'unicite cote commande).
- `menu` reference 0 ou 1 `media` (relation directe via `media_id`).
- `theme`, `diet`, `dish_type` et `changed_by_user` sont optionnels (nullable).
- `opening_hour` et `contact_message` sont des tables autonomes (pas de relation directe).

## Entites principales (indicatif)
- User : comptes, roles, infos personnelles.
- Menu : titre, description, theme, regime, min personnes, prix, stock, conditions.
- Dish : entree/plat/dessert, allergenes.
- CustomerOrder : commande client, adresse, livraison, statuts.
- Review : note 1-5, commentaire, validation.
