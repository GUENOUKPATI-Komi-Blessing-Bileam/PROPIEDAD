# Démarrage local avec Docker Compose

## Périmètre

La configuration racine démarre le frontend et les applications backend :

- Frontend Nginx : `http://localhost:3000`
- API Gateway Laravel : `http://localhost:8000/api/v1`
- Auth Service : interne au réseau Compose
- Asset Service : interne au réseau Compose
- History Service : interne au réseau Compose
- Report Service : interne au réseau Compose

Le frontend statique est indépendant du backend Laravel. Nginx sert les fichiers de `apps/frontend` et relaie les appels `/api/` au Gateway sur le réseau Docker. Le navigateur reste ainsi sur une seule origine, sans configuration CORS. Le Gateway relaie les routes vers les services propriétaires. Le endpoint Laravel `/up` sert au contrôle de santé.

Le Gateway présente les modules et relaie leurs appels, mais les API métier Biens, Historique et Rapports restent à implémenter dans leurs services. Ces modules afficheront explicitement l'erreur retournée tant que leurs endpoints ne sont pas disponibles. Le présent fichier est un socle de développement local, pas une configuration de production. Il utilise le serveur intégré `artisan serve` et active le mode debug.

## Prérequis

- Docker Desktop installé et démarré sous Windows, avec Docker Compose v2.
- Une clé Laravel `APP_KEY` générée pour chacun des services.

## Préparation des fichiers `.env`

Chaque application Laravel doit avoir un fichier local `.env`, ignoré par Git et exclu des images Docker. Le frontend statique n'en a pas besoin. S'il n'existe pas, depuis la racine du projet, copier les exemples :

```powershell
Copy-Item apps\asset-service\.env.example apps\asset-service\.env
Copy-Item apps\history-service\.env.example apps\history-service\.env
Copy-Item apps\report-service\.env.example apps\report-service\.env
Copy-Item apps\auth\.env.example apps\auth\.env
Copy-Item apps\gateway\.env.example apps\gateway\.env
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

Push-Location apps\auth
php artisan key:generate
Pop-Location

Push-Location apps\gateway
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

Les services attendent que leur base de données soit prête, lancent les migrations Laravel et, pour le service Biens, les références locales de démonstration, puis démarrent le serveur HTTP. Vérifier les contrôles de santé :

```powershell
docker compose ps
Invoke-WebRequest http://localhost:8000/up
Invoke-WebRequest http://localhost:3000
```

Ouvrir ensuite `http://localhost:3000` pour l'interface. L'API du Gateway est accessible sur `http://localhost:8000/api/v1`; le frontend la rejoint via le proxy Nginx. Ne pas appeler directement les conteneurs métier.

Consulter les journaux si un service ne démarre pas :

```powershell
docker compose logs --tail 100 asset-service
docker compose logs --tail 100 history-service
docker compose logs --tail 100 report-service
docker compose logs --tail 100 gateway
docker compose logs --tail 100 auth-service
```

Arrêter les conteneurs sans supprimer les données :

```powershell
docker compose down
```

Les données persistent dans les volumes nommés. La suppression des volumes détruirait les bases locales ; ne la faire que si les données peuvent être perdues et que cela est explicitement souhaité.

## Configuration des bases

| Application | Rôle | Port HTTP hôte |
|---|---|---:|
| Frontend | Interface Nginx, proxy `/api/` | 3000 |
| Gateway | API backend et routage des services | 8000 |
| Asset Service | API métier des biens | interne |
| History Service | API métier de l'historique | interne |
| Report Service | API métier des rapports | interne |
| Auth Service | API d'authentification | interne |

Chaque service métier possède un conteneur PostgreSQL et un volume de données distincts. Les bases et les services métier ne sont pas publiés sur les ports de l'hôte.

Compose surcharge `DB_CONNECTION`, l'hôte, le port et la base dans chaque conteneur. Les variables locales de base SQLite des `.env` ne remplacent pas cette configuration au runtime. Les secrets Laravel `APP_KEY` proviennent du `.env` propre à chaque application.

## Dépannage

- **Docker n'est pas reconnu :** installer Docker Desktop, le démarrer, puis ouvrir un nouveau terminal.
- **Erreur concernant `.env` :** créer les cinq fichiers depuis `.env.example`, puis générer les clés Laravel.
- **Base indisponible :** vérifier l'état `healthy` de `auth-db`, `asset-db`, `history-db` et `report-db`, puis examiner leurs logs.
- **Port déjà utilisé :** libérer le port concerné ou modifier le port de gauche dans `docker-compose.yml` (3000 pour le frontend, 8000 pour l'API).
- **Image obsolète après changement :** relancer `docker compose up --build -d`.
- **Migrations en échec :** consulter les logs de l'application ; ne supprimer pas les volumes comme première solution.

## Suite du travail

Les API métier Biens, Historique et Rapports restent à implémenter selon le contrat [API](../api/README.md). Le frontend séparé, le Gateway et le service Auth sont intégrés à Compose.
