# Contrat d'API — MVP

## Objectif

Ce contrat décrit l'API consommée par l'interface via `apps/gateway`. Il sert de référence commune pour le front-end et les services. C'est une proposition de conception : les règles d'accès, les champs obligatoires et les workflows doivent être confirmés avant implémentation.

La spécification détaillée est [openapi.yaml](./openapi.yaml) (OpenAPI 3.1).

## Principes

- Préfixe de toutes les routes : `/api/v1`.
- Le navigateur appelle le Gateway, jamais les bases de données ni directement les services internes.
- Le Gateway route les requêtes ; chaque service vérifie aussi l'identité, les permissions et le périmètre métier.
- Les données sont au format JSON, sauf les exports explicitement retournés en CSV.
- Les dates sont en ISO 8601 avec fuseau pour les horodatages ; les jours calendaires utilisent `YYYY-MM-DD`.
- Les identifiants sont des UUID. Les références à des données d'un autre service ne sont pas des clés étrangères.
- La liste des biens et des historiques est paginée. `page` commence à 1 ; `per_page` est limité à 100.
- Les écritures importantes doivent être idempotentes lorsqu'un client renvoie la requête après une erreur réseau : le client transmet une clé `Idempotency-Key` unique pour l'opération.
- Les erreurs suivent `application/problem+json`, avec un `request_id` pour faciliter le diagnostic.

## Répartition des routes

| Route | Service propriétaire |
|---|---|
| `/auth/*` | `apps/auth` |
| `/assets/*`, `/movements/*`, `/inventory-campaigns/*` | `apps/asset-service` |
| `/history` | `apps/history-service` |
| `/reports/*` | `apps/report-service` |

`apps/gateway` expose ces routes sur `http://localhost:8000` et relaie les appels. L'interface séparée `apps/frontend`, servie sur `http://localhost:3000`, utilise Nginx pour transférer `/api/` au Gateway sur le réseau Docker. Les URL internes des services métier ne sont pas exposées au navigateur.

## Endpoints MVP

| Méthode et route | Fonction |
|---|---|
| `GET /assets/references` | Charger les catégories, services et sites actifs pour les formulaires |
| `POST /auth/login` | Authentifier un utilisateur |
| `POST /auth/logout` | Révoquer la session / le jeton selon la stratégie choisie |
| `GET /auth/me` | Obtenir le profil et les rôles courants |
| `GET /assets` | Rechercher et paginer les biens autorisés |
| `POST /assets` | Créer une fiche ; l'identifiant de stock est attribué par le service |
| `GET /assets/{assetId}` | Consulter une fiche |
| `PATCH /assets/{assetId}` | Modifier les champs autorisés |
| `GET /movements` | Lister les mouvements visibles |
| `POST /movements` | Soumettre un mouvement en attente |
| `POST /movements/{movementId}/approve` | Valider un mouvement en attente |
| `POST /movements/{movementId}/reject` | Rejeter un mouvement avec motif |
| `GET /inventory-campaigns` | Lister les campagnes |
| `POST /inventory-campaigns` | Créer une campagne |
| `GET /inventory-campaigns/{campaignId}` | Consulter une campagne et sa progression |
| `POST /inventory-campaigns/{campaignId}/checks` | Soumettre une observation de bien |
| `GET /history` | Consulter les événements d'audit autorisés |
| `GET /reports/summary` | Obtenir les indicateurs du tableau de bord |
| `GET /reports/assets.csv` | Exporter les biens correspondant aux filtres permis |

## Changements métier et événements

Lorsqu'une action métier doit être auditée, le service propriétaire écrit sa donnée et un événement d'outbox dans la même transaction. Un worker livre ensuite l'événement au History Service avec reprises. Il ne faut pas effectuer d'appel synchrone au service d'historique au milieu de cette transaction.

Enveloppe d'événement proposée :

```json
{
  "event_id": "UUID",
  "event_type": "asset.movement.approved",
  "occurred_at": "2026-11-12T10:15:00Z",
  "source": "asset-service",
  "aggregate_type": "movement",
  "aggregate_id": "UUID",
  "actor_id": "UUID",
  "correlation_id": "UUID",
  "data": {
    "asset_id": "UUID",
    "movement_type": "transfer",
    "status": "approved"
  }
}
```

Le History Service déduplique avec `event_id`, garde une trace append-only et n'enregistre pas de secret, jeton ou donnée personnelle inutile. Les consommateurs doivent tolérer la livraison répétée d'un même événement.

## Décisions à prendre avant implémentation

- Stratégie d'authentification : jeton signé avec révocation, OAuth2 ou session sécurisée.
- Qui peut approuver un mouvement et quelle séparation entre demandeur et approbateur est obligatoire.
- États métier définitifs des biens, mouvements, campagnes et contrôles.
- Champs patrimoniaux obligatoires et valeurs des référentiels.
- Règles d'export, limites de données et traçabilité des consultations.
- Mécanisme de génération et de remise des fichiers CSV volumineux.
