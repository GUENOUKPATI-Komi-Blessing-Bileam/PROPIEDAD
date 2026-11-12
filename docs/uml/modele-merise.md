# Modélisation Merise — Patrimoine de l'État

## Aperçu graphique

Ouvrir [modele-merise.puml](./modele-merise.puml) dans VS Code avec l'extension PlantUML, puis lancer **PlantUML: Preview Current Diagram** depuis la palette de commandes (`Ctrl+Shift+P`). Le diagramme montre les entités, les cardinalités et la séparation des données par service.

## 1. Principes et périmètre

Le modèle logique est séparé selon les propriétaires de données de l'architecture :

- **Asset Service** : biens, référentiels patrimoniaux, mouvements et inventaires.
- **Auth Service** : utilisateurs, rôles et permissions.
- **History Service** : événements d'audit reçus des services.
- **Report Service** : aucun référentiel métier maître dans le MVP ; il lit les API ou événements autorisés et peut maintenir des agrégats reconstruisibles.
- **Gateway** : sans données métier persistantes.

Il n'y a pas de clé étrangère entre bases de services. Une référence vers un service externe (par exemple `utilisateur_id` ou `service_id`) est un identifiant UUID vérifié par API ou par la règle d'accès appropriée. Les associations et cardinalités ci-dessous s'appliquent aux données appartenant au même service.

Notation Merise : `(min,max)` indique la cardinalité minimale et maximale d'une association. Les noms de champs terminés par `_id` sont des clés étrangères uniquement lorsqu'ils pointent dans la même base.

## 2. MCD — Asset Service

### Entités

- **CATEGORIE** (`#id_categorie`, libelle, description, actif)
- **SERVICE_ADMINISTRATIF** (`#id_service`, code, libelle, parent_id?, actif)
- **SITE** (`#id_site`, code, libelle, adresse, actif)
- **BIEN** (`#id_bien`, code_inventaire, libelle, description, date_acquisition?, valeur_acquisition?, devise?, etat, statut, date_creation, date_modification, version)
- **MOUVEMENT** (`#id_mouvement`, type, statut, date_effet, motif, soumis_le, valide_le?, rejete_le?)
- **CAMPAGNE_INVENTAIRE** (`#id_campagne`, libelle, statut, date_debut, date_fin?, cree_le)
- **LIGNE_INVENTAIRE** (`#id_ligne`, resultat, site_observe_id?, etat_observe?, commentaire?, observe_le?)
- **OUTBOX_EVENT** (`#event_id`, type_evenement, aggregate_type, aggregate_id, payload, cree_le, publie_le?)

`parent_id` dans SERVICE_ADMINISTRATIF est une relation réflexive interne à Asset Service. Les utilisateurs sont gérés par Auth Service ; les propriétés `cree_par`, `soumis_par`, `valide_par` et `observe_par` sont des références externes, sans FK.

### Associations et cardinalités

| Association | Cardinalités | Interprétation |
|---|---|---|
| Classer (CATEGORIE — BIEN) | CATEGORIE `(0,n)` — BIEN `(1,1)` | Un bien a une catégorie ; une catégorie peut classer plusieurs biens. |
| Responsable (SERVICE_ADMINISTRATIF — BIEN) | SERVICE `(0,n)` — BIEN `(1,1)` | Chaque bien a un service responsable courant. |
| Localiser (SITE — BIEN) | SITE `(0,n)` — BIEN `(1,1)` | Chaque bien possède un site courant ; prévoir un site « non renseigné » si le métier l'autorise. |
| Concerner (BIEN — MOUVEMENT) | BIEN `(0,n)` — MOUVEMENT `(1,1)` | Un mouvement concerne exactement un bien. |
| Origine (SERVICE — MOUVEMENT) | SERVICE `(0,n)` — MOUVEMENT `(0,1)` | Un mouvement peut ne pas avoir d'origine, par exemple une entrée initiale. |
| Destination (SERVICE — MOUVEMENT) | SERVICE `(0,n)` — MOUVEMENT `(0,1)` | Une sortie définitive peut ne pas avoir de destination interne. |
| Origine / destination de site | SITE `(0,n)` — MOUVEMENT `(0,1)` pour chaque rôle | Les sites d'origine et de destination sont optionnels selon le type de mouvement. |
| Planifier (CAMPAGNE_INVENTAIRE — LIGNE_INVENTAIRE) | CAMPAGNE `(0,n)` — LIGNE `(1,1)` | Une ligne appartient à une seule campagne. |
| Vérifier (BIEN — LIGNE_INVENTAIRE) | BIEN `(0,n)` — LIGNE `(1,1)` | Une ligne porte sur un bien. Une seule ligne par couple campagne/bien. |
| Émettre (transaction métier — OUTBOX_EVENT) | métier `(0,n)` — événement `(1,1)` | Les événements sont conservés dans la base du service émetteur jusqu'à confirmation de livraison. |

### Règles d'intégrité à porter dans le modèle

1. `code_inventaire` est unique dans Asset Service.
2. `valeur_acquisition` est positive ou nulle et `devise` est obligatoire si une valeur est renseignée.
3. Un mouvement approuvé ne peut être modifié ou approuvé une seconde fois.
4. Le mouvement, la mise à jour de la situation courante du bien et l'événement d'outbox sont enregistrés dans une même transaction.
5. Un transfert approuvé met à jour le service et/ou le site courant du bien ; les sorties définitives suivent une règle distincte à confirmer.
6. `UNIQUE(campagne_id, bien_id)` évite la duplication d'un bien dans une campagne.
7. Le résultat d'une ligne d'inventaire ne peut être soumis que par un utilisateur autorisé sur le périmètre de la campagne.
8. Les références externes vers des identités et organisations sont contrôlées par l'application ; elles ne sont pas des clés étrangères inter-bases.

## 3. MCD — Auth Service

### Entités et associations

- **UTILISATEUR** (`#id_utilisateur`, nom, email, mot_de_passe_hash, actif, cree_le)
- **ROLE** (`#id_role`, code, libelle, actif)
- **PERMISSION** (`#id_permission`, code, description)
- **UTILISATEUR_ROLE** (`#id_utilisateur`, `#id_role`, attribue_le, attribue_par)
- **ROLE_PERMISSION** (`#id_role`, `#id_permission`)

Cardinalités : UTILISATEUR `(0,n)` — ROLE `(0,n)` ; ROLE `(0,n)` — PERMISSION `(0,n)`. Les tables d'association portent les clés étrangères locales et les clés primaires composées. `attribue_par` est un identifiant d'utilisateur, pas une relation obligatoire vers une seconde base.

Contraintes : email normalisé unique ; mot de passe stocké uniquement sous forme de hash ; code de rôle et code de permission uniques.

## 4. MCD — History Service

### Entité

- **EVENEMENT_AUDIT** (`#event_id`, service_source, aggregate_type, aggregate_id, type_evenement, acteur_id?, date_evenement, correlation_id?, payload, recu_le)

Les événements sont append-only pour les rôles applicatifs. `event_id` est unique pour rendre la réception idempotente. `acteur_id` est l'identifiant externe d'Auth Service. Le payload doit être minimisé : ne pas y recopier de mot de passe, jeton, secret ou donnée personnelle inutile. Les règles de conservation et d'archivage sont à valider avec le ministère.

## 5. MLD relationnel initial

Types indicatifs compatibles avec PostgreSQL ; Laravel peut utiliser UUID et JSON selon le pilote retenu. Les noms précis peuvent suivre les conventions Laravel si les contraintes restent explicites.

### Base `asset`

```text
categories(
  id UUID PK, libelle VARCHAR(120) UNIQUE, description TEXT NULL,
  actif BOOLEAN NOT NULL DEFAULT TRUE, created_at, updated_at
)

services_administratifs(
  id UUID PK, code VARCHAR(40) UNIQUE, libelle VARCHAR(160) NOT NULL,
  parent_id UUID NULL FK -> services_administratifs.id,
  actif BOOLEAN NOT NULL DEFAULT TRUE, created_at, updated_at
)

sites(
  id UUID PK, code VARCHAR(40) UNIQUE, libelle VARCHAR(160) NOT NULL,
  adresse TEXT NULL, actif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at, updated_at
)

biens(
  id UUID PK, code_inventaire VARCHAR(80) NOT NULL UNIQUE,
  libelle VARCHAR(200) NOT NULL, description TEXT NULL,
  date_acquisition DATE NULL, valeur_acquisition DECIMAL(18,2) NULL,
  devise CHAR(3) NULL, etat VARCHAR(30) NOT NULL, statut VARCHAR(30) NOT NULL,
  categorie_id UUID NOT NULL FK -> categories.id,
  service_responsable_id UUID NOT NULL FK -> services_administratifs.id,
  site_id UUID NOT NULL FK -> sites.id,
  cree_par UUID NOT NULL, version INTEGER NOT NULL DEFAULT 1,
  created_at, updated_at
)

mouvements(
  id UUID PK, bien_id UUID NOT NULL FK -> biens.id,
  type VARCHAR(30) NOT NULL, statut VARCHAR(30) NOT NULL,
  service_origine_id UUID NULL FK -> services_administratifs.id,
  site_origine_id UUID NULL FK -> sites.id,
  service_destination_id UUID NULL FK -> services_administratifs.id,
  site_destination_id UUID NULL FK -> sites.id,
  soumis_par UUID NOT NULL, valide_par UUID NULL,
  date_effet TIMESTAMP NOT NULL, motif TEXT NOT NULL,
  soumis_le TIMESTAMP NOT NULL, valide_le TIMESTAMP NULL,
  created_at, updated_at
)

campagnes_inventaire(
  id UUID PK, libelle VARCHAR(180) NOT NULL, statut VARCHAR(30) NOT NULL,
  service_id UUID NULL FK -> services_administratifs.id,
  site_id UUID NULL FK -> sites.id,
  date_debut DATE NOT NULL, date_fin DATE NULL, cree_par UUID NOT NULL,
  created_at, updated_at
)

lignes_inventaire(
  id UUID PK, campagne_id UUID NOT NULL FK -> campagnes_inventaire.id,
  bien_id UUID NOT NULL FK -> biens.id, resultat VARCHAR(30) NOT NULL,
  site_observe_id UUID NULL FK -> sites.id, etat_observe VARCHAR(30) NULL,
  observe_par UUID NULL, observe_le TIMESTAMP NULL, commentaire TEXT NULL,
  UNIQUE(campagne_id, bien_id), created_at, updated_at
)

outbox_events(
  event_id UUID PK, type_evenement VARCHAR(120) NOT NULL,
  aggregate_type VARCHAR(80) NOT NULL, aggregate_id UUID NOT NULL,
  payload JSON NOT NULL, cree_le TIMESTAMP NOT NULL, publie_le TIMESTAMP NULL,
  tentatives INTEGER NOT NULL DEFAULT 0, derniere_erreur TEXT NULL
)
```

Index conseillés à confirmer par les requêtes réelles : `biens(categorie_id)`, `biens(service_responsable_id)`, `biens(site_id)`, `biens(etat, statut)`, `mouvements(bien_id, date_effet)`, `lignes_inventaire(campagne_id, resultat)` et un index de recherche sur `code_inventaire`/`libelle`.

### Base `auth`

```text
utilisateurs(
  id UUID PK, nom VARCHAR(160) NOT NULL, email VARCHAR(254) NOT NULL UNIQUE,
  mot_de_passe_hash VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at, updated_at
)
roles(
  id UUID PK, code VARCHAR(60) NOT NULL UNIQUE, libelle VARCHAR(120) NOT NULL,
  actif BOOLEAN NOT NULL DEFAULT TRUE, created_at, updated_at
)
permissions(
  id UUID PK, code VARCHAR(100) NOT NULL UNIQUE, description TEXT NULL,
  created_at, updated_at
)
utilisateur_role(
  utilisateur_id UUID NOT NULL FK -> utilisateurs.id,
  role_id UUID NOT NULL FK -> roles.id, attribue_par UUID NULL,
  attribue_le TIMESTAMP NOT NULL,
  PRIMARY KEY(utilisateur_id, role_id)
)
role_permission(
  role_id UUID NOT NULL FK -> roles.id,
  permission_id UUID NOT NULL FK -> permissions.id,
  PRIMARY KEY(role_id, permission_id)
)
```

Le mode d'émission des jetons (sessions, OAuth2 ou jeton signé) et les besoins de révocation sont des décisions techniques à prendre avant de construire `apps/auth`.

### Base `history`

```text
evenements_audit(
  event_id UUID PK, service_source VARCHAR(80) NOT NULL,
  aggregate_type VARCHAR(80) NOT NULL, aggregate_id UUID NOT NULL,
  type_evenement VARCHAR(120) NOT NULL, acteur_id UUID NULL,
  date_evenement TIMESTAMP NOT NULL, correlation_id UUID NULL,
  payload JSON NOT NULL, recu_le TIMESTAMP NOT NULL
)
```

Index conseillés : `(aggregate_type, aggregate_id, date_evenement)` et `(acteur_id, date_evenement)`. Une politique de rétention doit être décidée avant la mise en production.

## 6. Décisions restant à valider

- Le ministère fournit-il un référentiel officiel pour les directions, services, catégories et sites ?
- Un bien peut-il avoir plusieurs détenteurs ou plusieurs localisations simultanés ?
- Quels sont les états et statuts autorisés, et quels mouvements nécessitent approbation ?
- La sortie définitive d'un bien signifie-t-elle archivage, réforme, cession ou destruction ?
- Les valeurs d'acquisition sont-elles obligatoires, dans quelle devise et avec quelle règle de précision ?
- Les agents terrain ont-ils besoin du mode hors ligne ?
- Quelle durée de conservation des journaux et inventaires est imposée ?
- PostgreSQL est proposé pour la cohérence et les contraintes relationnelles ; le choix final dépend de l'environnement de déploiement.

## 7. Correspondance avec les fichiers UML

- [Diagramme graphique Merise — organisation des bases](./modele-merise.puml)
- [Cas d'utilisation](./cas-utilisation.puml)
- [Architecture microservices](./architecture-microservices.puml)
- [Classes du domaine](./classes-domaine.puml)
- [Séquence de validation d'un mouvement](./sequence-validation-mouvement.puml)
