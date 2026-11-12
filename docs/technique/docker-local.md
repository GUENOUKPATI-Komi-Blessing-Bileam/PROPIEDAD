# Démarrage local avec Docker Compose

## Périmètre

La configuration racine démarre les trois applications Laravel déjà présentes :

- Asset Service : `http://localhost:8001`
- History Service : `http://localhost:8002`
- Report Service : `http://localhost:8003`

Chacune possède un conteneur PostgreSQL et un volume de données distincts. Les bases ne sont pas publiées sur les ports de l'hôte. Le endpoint Laravel `/up` sert au contrôle de santé.

`apps/auth` et `apps/gateway` sont encore des dossiers vides : ils ne sont donc pas démarrés par Compose. Le présent fichier est un socle de développement local, pas une configuration de production. Il utilise le serveur intégré `artisan serve` et active le mode debug.

## Prérequis

- Docker Desktop installé et démarré sous Windows, avec Docker Compose v2.
- Une clé Laravel `APP_KEY` générée pour chacun des services.

Docker n'était pas disponible sur la machine au moment de la rédaction de ce guide ; la configuration devra être validée par `docker compose config` et un démarrage réel une fois Docker installé.

## Préparation des fichiers `.env`

Chaque service doit avoir un fichier local `.env`, ignoré par Git et exclu des images Docker. S'il n'existe pas, depuis la racine du projet, le copier depuis son exemple :

```powershell
Copy-Item apps\asset-service\.env.example apps\asset-service\.env
Copy-Item apps\history-service\.env.example apps\history-service\.env
Copy-Item apps\report-service\.env.example apps\report-service\.env
```

Ne pas remplacer un fichier `.env` déjà configuré. Générer une clé différente pour chaque service :

```powershell
Push-Location apps\asset-service
php artisan key:generate
Pop-Location

Push-Location apps\history-service
php artisan key:generate
Pop-Location

Push-Location apps\report-service
php artisan key:generate
Pop-Location
```

Ne jamais publier ces fichiers ni utiliser les identifiants locaux par défaut en production. La variable `POSTGRES_PASSWORD` peut être définie dans le fichier `.env` racine, utilisé par Compose pour l'interpolation, afin de remplacer le mot de passe de démonstration.

## Lancer les services

Depuis la racine du dépôt :

```powershell
docker compose config
docker compose up --build -d
docker compose ps
```

Les services attendent que leur base de données soit prête, lancent les migrations Laravel, puis démarrent le serveur HTTP. Vérifier les contrôles de santé :

```powershell
docker compose ps
Invoke-WebRequest http://localhost:8001/up
Invoke-WebRequest http://localhost:8002/up
Invoke-WebRequest http://localhost:8003/up
```

Consulter les journaux si un service ne démarre pas :

```powershell
docker compose logs --tail 100 asset-service
docker compose logs --tail 100 history-service
docker compose logs --tail 100 report-service
```

Arrêter les conteneurs sans supprimer les données :

```powershell
docker compose down
```

Les données persistent dans les volumes nommés. La suppression des volumes détruirait les bases locales ; ne la faire que si les données peuvent être perdues et que cela est explicitement souhaité.

## Configuration des bases

| Service applicatif | Hôte PostgreSQL Compose | Base | Port HTTP hôte |
|---|---|---|---:|
| Asset Service | `asset-db` | `asset_service` | 8001 |
| History Service | `history-db` | `history_service` | 8002 |
| Report Service | `report-db` | `report_service` | 8003 |

Compose surcharge `DB_CONNECTION`, l'hôte, le port et la base dans chaque conteneur. Les variables locales de base SQLite des `.env` ne remplacent pas cette configuration au runtime. Les secrets Laravel `APP_KEY` proviennent du `.env` propre à chaque application.

## Dépannage

- **Docker n'est pas reconnu :** installer Docker Desktop, le démarrer, puis ouvrir un nouveau terminal.
- **Erreur concernant `.env` :** créer les trois fichiers depuis `.env.example`, puis générer les clés Laravel.
- **Base indisponible :** vérifier l'état `healthy` de `asset-db`, `history-db` et `report-db`, puis examiner leurs logs.
- **Port déjà utilisé :** libérer le port concerné ou modifier le port de gauche dans `docker-compose.yml`.
- **Image obsolète après changement :** relancer `docker compose up --build -d`.
- **Migrations en échec :** consulter les logs de l'application ; ne supprimer pas les volumes comme première solution.

## Suite du travail

Après validation du démarrage, créer les tests des endpoints `/up`, définir les premières migrations métier et implémenter chaque service selon le contrat [API](../api/README.md). Scaffolder `auth` et `gateway` quand leurs responsabilités et contrats seront confirmés, puis les intégrer à Compose.

