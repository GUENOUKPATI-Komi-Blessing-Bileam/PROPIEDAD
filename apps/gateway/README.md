# API Gateway

Le Gateway Laravel expose l'API backend en développement local :

- API versionnée : `http://localhost:8000/api/v1`
- Interface séparée : `http://localhost:3000` (application `apps/frontend`)
- Contrat des routes : [`../../docs/api/README.md`](../../docs/api/README.md)
- Démarrage des services : [`../../docs/technique/docker-local.md`](../../docs/technique/docker-local.md)

Nginx sert les fichiers statiques de `apps/frontend` et relaie les appels `/api/` au Gateway sur le réseau Docker. Le navigateur utilise ainsi la même origine pour l'interface et l'API, sans configuration CORS. Le jeton d'authentification reste conservé uniquement dans l'onglet courant.

Les services Auth, Biens, Historique et Rapports communiquent sur le réseau privé Docker. L'interface peut afficher les données d'un module dès que son API est disponible. Les API métier des services Biens, Historique et Rapports ne sont pas encore implémentées : leurs erreurs sont affichées dans l'interface au lieu d'être masquées.

## Vérification

Depuis ce dossier :

```powershell
php artisan test
```
