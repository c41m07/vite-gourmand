# Schema relationnel - relations

## 1. Conventions
- Strategie de nommage Doctrine : underscore_number_aware (snake_case en base).
- PK : `id` (int auto-increment).
- FK : `<propriete>_id` par defaut (ex : `user_id`).
- Nullabilite selon `JoinColumn(nullable=...)`.

## 2. Resume des relations (cardinalites)
- user 1..N customer_order (customer_order.user_id, NOT NULL)
- user 1..N review (review.user_id, NOT NULL)
- user 0..N customer_order_status_history (changed_by_user_id, NULL)
- customer_order 1..N customer_order_menu (customer_order_id, NOT NULL)
- customer_order 1..N customer_order_status_history (customer_order_id, NOT NULL)
- order_status 1..N customer_order_status_history (order_status_id, NOT NULL)
- customer_order 0..1 equipment_loan (equipment_loan_id, NULL, one-to-one)
- equipment_loan_status 1..N equipment_loan (status_id, NOT NULL)
- menu 1..N menu_media (menu_id, NOT NULL)
- media 1..N menu_media (media_id, NOT NULL)
- menu 1..N menu_dish (menu_id, NOT NULL)
- dish 1..N menu_dish (dish_id, NOT NULL)
- dish_type 0..N dish (dish_type_id, NULL)
- dish 1..N dish_allergen (dish_id, NOT NULL)
- allergen 1..N dish_allergen (allergen_id, NOT NULL)
- menu 1..N customer_order_menu (menu_id, NOT NULL)
- review 0..N customer_order_menu (review_id, NULL)
- theme 0..N menu (theme_id, NULL)
- diet 0..N menu (diet_id, NULL)
- opening_hour : table autonome
- contact_message : table autonome

## 3. Detail des tables et cles

### user
- PK : id
- Unique : email
- FKs sortantes : aucune
- FKs entrantes :
  - customer_order.user_id
  - review.user_id
  - customer_order_status_history.changed_by_user_id (optionnel)

### customer_order
- PK : id
- FKs sortantes :
  - user_id -> user.id (NOT NULL)
  - equipment_loan_id -> equipment_loan.id (NULL, one-to-one)
- FKs entrantes :
  - customer_order_menu.customer_order_id
  - customer_order_status_history.customer_order_id

### customer_order_status_history
- PK : id
- FKs sortantes :
  - customer_order_id -> customer_order.id (NOT NULL)
  - order_status_id -> order_status.id (NOT NULL)
  - changed_by_user_id -> user.id (NULL)
- FKs entrantes : aucune
- Role : historique des statuts par commande (plusieurs lignes possibles par commande).

### order_status
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - customer_order_status_history.order_status_id
- Role : catalogue des statuts (code/label).

### equipment_loan
- PK : id
- FKs sortantes :
  - status_id -> equipment_loan_status.id (NOT NULL)
- FKs entrantes :
  - customer_order.equipment_loan_id (NULL, one-to-one)

### equipment_loan_status
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - equipment_loan.status_id

### menu
- PK : id
- FKs sortantes :
  - theme_id -> theme.id (NULL)
  - diet_id -> diet.id (NULL)
- FKs entrantes :
  - menu_media.menu_id
  - menu_dish.menu_id
  - customer_order_menu.menu_id

### menu_media
- PK : id
- FKs sortantes :
  - menu_id -> menu.id (NOT NULL)
  - media_id -> media.id (NOT NULL)
- FKs entrantes : aucune
- Role : medias d'un menu avec position et is_cover.

### media
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - menu_media.media_id

### menu_dish
- PK : id
- FKs sortantes :
  - menu_id -> menu.id (NOT NULL)
  - dish_id -> dish.id (NOT NULL)
- FKs entrantes : aucune
- Role : table de liaison menu <-> dish (many-to-many).

### dish
- PK : id
- FKs sortantes :
  - dish_type_id -> dish_type.id (NULL)
- FKs entrantes :
  - menu_dish.dish_id
  - dish_allergen.dish_id

### dish_type
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - dish.dish_type_id

### dish_allergen
- PK : id
- FKs sortantes :
  - dish_id -> dish.id (NOT NULL)
  - allergen_id -> allergen.id (NOT NULL)
- FKs entrantes : aucune
- Role : table de liaison dish <-> allergen (many-to-many).

### allergen
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - dish_allergen.allergen_id

### review
- PK : id
- FKs sortantes :
  - user_id -> user.id (NOT NULL)
- FKs entrantes :
  - customer_order_menu.review_id (NULL)

### customer_order_menu
- PK : id
- FKs sortantes :
  - customer_order_id -> customer_order.id (NOT NULL)
  - menu_id -> menu.id (NOT NULL)
  - review_id -> review.id (NULL)
- FKs entrantes : aucune
- Role : ligne de commande (quantite, prix) liant commande et menu.

### theme
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - menu.theme_id

### diet
- PK : id
- FKs sortantes : aucune
- FKs entrantes :
  - menu.diet_id

### opening_hour
- PK : id
- FKs sortantes : aucune
- FKs entrantes : aucune

### contact_message
- PK : id
- FKs sortantes : aucune
- FKs entrantes : aucune

## 4. Notes d'integrite
- La relation one-to-one `customer_order` <-> `equipment_loan` implique une contrainte UNIQUE sur `customer_order.equipment_loan_id`.
- Les tables de liaison (menu_dish, dish_allergen, menu_media, customer_order_menu) peuvent avoir un index/unique composite selon les besoins fonctionnels.
