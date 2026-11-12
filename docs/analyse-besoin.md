# Analyse du besoin — Gestion du patrimoine de l'État

## 1. Contexte

Le ministère chargé de la protection du patrimoine de l'État a besoin d'un outil centralisé pour connaître les biens publics, leur localisation, leur état et les opérations effectuées sur ceux-ci. La solution doit faciliter l'inventaire, la traçabilité, le contrôle et la production de rapports, tout en respectant les responsabilités des différents services.

Le périmètre juridique, le pays, les règles comptables applicables et les volumes de données ne sont pas encore précisés. Les règles métier ci-dessous constituent donc des hypothèses à valider avec le commanditaire avant une mise en production.

## 2. Problème à résoudre

Sans référentiel commun, les informations sur les biens peuvent être dispersées dans des fichiers ou des services distincts. Cela rend difficile :

- l'identification fiable d'un bien et la détection des doublons ;
- la connaissance de son détenteur, de son emplacement et de son état ;
- le suivi des acquisitions, affectations, transferts, réparations et sorties ;
- la justification des modifications et la préparation des contrôles ;
- la production de statistiques cohérentes pour la prise de décision.

## 3. Objectifs

1. Constituer un registre numérique unique et consultable des biens de l'État.
2. Assurer la traçabilité des mouvements et des modifications.
3. Contrôler les accès selon le rôle et le périmètre organisationnel.
4. Faciliter les inventaires et la vérification physique des biens.
5. Produire des tableaux de bord et des rapports exportables.
6. Fournir une base extensible, conteneurisée et organisée en microservices.

## 4. Utilisateurs et responsabilités

| Acteur | Responsabilités principales |
|---|---|
| Administrateur de la plateforme | Gérer les comptes, rôles, paramètres et référentiels autorisés. |
| Gestionnaire central du patrimoine | Créer et valider les fiches de biens, superviser les mouvements et consulter les rapports nationaux. |
| Gestionnaire d'un service ou d'une direction | Consulter les biens de son périmètre, signaler un changement et préparer les inventaires. |
| Agent d'inventaire | Rechercher ou scanner un bien, vérifier sa présence, son emplacement et son état, puis soumettre le résultat du contrôle. |
| Auditeur / contrôleur | Consulter les informations et l'historique en lecture seule et exporter les éléments autorisés. |

Les rôles sont une proposition initiale. La séparation réelle entre création, validation et consultation doit être confirmée par le ministère.

## 5. Périmètre fonctionnel proposé

### Inclus dans le MVP

- Authentification et gestion des comptes, rôles et permissions.
- Référentiels de base : catégories de biens, directions/services, sites et états.
- Fiche d'un bien avec identifiant unique, description, catégorie, date et valeur d'acquisition si disponibles, état, détenteur, service responsable et emplacement.
- Création, modification contrôlée, consultation, recherche et filtres.
- Enregistrement des affectations et transferts, avec date, origine, destination, acteur et motif.
- Historique consultable des opérations importantes, sans modification par les utilisateurs ordinaires.
- Préparation et clôture d'une campagne d'inventaire avec résultat de vérification.
- Tableau de bord simple et rapports filtrables, imprimables ou exportables en CSV.
- Étiquette avec QR code contenant un identifiant non sensible permettant d'ouvrir la fiche après authentification.
- Journalisation des actions importantes et gestion des erreurs.

### Hors MVP, à envisager après validation

- Cartographie SIG avancée et géolocalisation précise.
- Fonctionnement hors ligne avec synchronisation sur appareils mobiles.
- Gestion de marchés publics, d'amortissements ou de règles comptables complexes.
- Intégration avec des systèmes financiers, cadastraux ou d'identité nationaux.
- Signature électronique et circuit d'approbation configurable.
- Notifications SMS ou courriel, reconnaissance automatique de documents et analyses prédictives.

Cette limite permet de démontrer une solution complète sans introduire trop tôt des intégrations ou des règles administratives encore inconnues.

## 6. Exigences fonctionnelles initiales

- **EF-01 — Accès :** un utilisateur se connecte et ne voit que les fonctions et données autorisées par son rôle.
- **EF-02 — Référentiel :** un gestionnaire autorisé crée et maintient les catégories, services et sites.
- **EF-03 — Fiche de bien :** un gestionnaire enregistre un bien et obtient un identifiant unique et un QR code.
- **EF-04 — Recherche :** un utilisateur autorisé recherche un bien par identifiant, QR code, catégorie, état, service ou emplacement.
- **EF-05 — Mouvement :** un gestionnaire soumet un transfert ou une affectation ; le système conserve les informations de l'opération et met à jour la situation courante après validation.
- **EF-06 — Inventaire :** un agent vérifie les biens attendus et soumet les résultats (présent, absent, emplacement ou état à corriger).
- **EF-07 — Historique :** les utilisateurs autorisés consultent les événements associés à un bien ; les événements d'audit ne sont pas éditables depuis l'interface métier.
- **EF-08 — Rapports :** les gestionnaires et auditeurs génèrent des rapports selon leurs permissions et filtres.
- **EF-09 — Traçabilité :** les opérations sensibles enregistrent l'acteur, la date, l'action et l'objet concerné.

## 7. Exigences non fonctionnelles

- **Sécurité :** HTTPS en déploiement, mots de passe hachés, contrôle d'accès côté serveur, validation des entrées, secrets hors du dépôt et limitation des droits des comptes techniques.
- **Confidentialité :** ne pas placer de données personnelles ou sensibles dans le QR code ; limiter les exports aux rôles autorisés.
- **Intégrité :** identifiants uniques, validations métier et historique des changements importants.
- **Disponibilité et sauvegarde :** définir avec le ministère la disponibilité cible, la fréquence des sauvegardes et les délais de restauration.
- **Performance :** les recherches et consultations courantes doivent rester utilisables sur le volume cible, à préciser lors du cadrage.
- **Ergonomie :** interface responsive, formulaires compréhensibles, navigation clavier et messages d'erreur explicites.
- **Maintenabilité :** services isolés, API documentées, migrations versionnées et tests automatisés.
- **Déploiement :** environnement reproductible avec Docker Compose pour le projet et procédure documentée de configuration.

Les objectifs chiffrés (volume de biens, utilisateurs simultanés, temps de réponse et disponibilité) restent à établir avec le commanditaire.

## 8. Parcours métier principaux

### Enregistrer un bien

Le gestionnaire se connecte → crée une fiche → renseigne les informations disponibles et le service responsable → le système valide les données et génère un identifiant/QR code → la fiche et l'événement de création deviennent consultables selon les permissions.

### Transférer un bien

Le gestionnaire sélectionne le bien → indique la destination, le responsable, la date et le motif → le transfert est validé selon les règles à confirmer → le détenteur et l'emplacement courants sont mis à jour → l'opération est ajoutée à l'historique.

### Réaliser un inventaire

Le gestionnaire prépare une campagne sur un périmètre → l'agent consulte la liste attendue et scanne les QR codes → il signale présence, absence ou anomalie → le gestionnaire examine les écarts → le rapport de campagne est généré.

## 9. Critères d'acceptation du MVP

1. Un gestionnaire autorisé peut créer une fiche et le système empêche les identifiants en double.
2. Un utilisateur non autorisé ne peut ni consulter ni modifier les données hors de son périmètre.
3. Un transfert validé met à jour l'emplacement courant et laisse une trace consultable.
4. Un agent peut enregistrer le résultat d'une vérification d'inventaire et le gestionnaire peut consulter les écarts.
5. Un auditeur peut générer un rapport en lecture seule sans modifier les données.
6. Les services et leurs dépendances peuvent être démarrés selon une procédure Docker documentée.

## 10. Architecture cible alignée sur le dépôt

Le dépôt contient déjà `apps/asset-service`, `apps/history-service` et `apps/report-service`, qui sont des squelettes Laravel. `apps/auth` et `apps/gateway` sont présents mais ne contiennent pas encore d'application visible. Le dossier `packages/` est vide ; les livrables de conception se trouvent dans `docs/uml/`, `docs/figma/` et `docs/api/`.

| Composant | Responsabilité proposée |
|---|---|
| `apps/gateway` | Point d'entrée API, routage vers les services, identifiant de requête et politiques transversales. Ne porte pas les règles métier. |
| `apps/auth` | Identités, rôles, permissions et émission/validation des jetons d'accès. |
| `apps/asset-service` | Biens, catégories, sites, services responsables, affectations, mouvements et campagnes d'inventaire. |
| `apps/history-service` | Historique d'audit en ajout uniquement, consultable par les rôles habilités. |
| `apps/report-service` | Agrégats et rapports, sans devenir propriétaire des données métier des autres services. |

Chaque service devrait posséder ses données et ses migrations. Éviter une base de données partagée et les jointures directes entre bases de services ; échanger par API ou par événements documentés. Pour un premier livrable pédagogique, les échanges HTTP documentés sont suffisants. Une file d'événements peut être ajoutée si le besoin de traitement asynchrone est confirmé.

Ne pas partager les modèles Eloquent ou la logique métier au moyen de `packages/`. Si un besoin réel apparaît, réserver ce dossier à des contrats versionnés (schémas API, événements) ou à de très petites bibliothèques techniques stables. Cela évite de coupler les services.

## 11. Hypothèses et questions à valider

- Quels types de biens sont concernés : immobilier, véhicules, mobilier, matériel informatique, terrains, ou tous ?
- Quel ministère, pays et référentiel administratif servent de cadre ?
- Qui peut créer, valider, transférer et déclarer la sortie d'un bien ?
- Un transfert doit-il être validé par une ou plusieurs personnes ?
- Quels champs sont obligatoires et quelles règles comptables ou patrimoniales s'appliquent ?
- Quel volume initial de biens, d'utilisateurs et de documents est attendu ?
- L'application doit-elle fonctionner sur le terrain sans connexion ?
- Quels formats de rapports et d'exports sont requis ?
- Quelles sont les règles de conservation, de sauvegarde et d'hébergement des données ?
- Quelle devise, quels taux journaliers et quel niveau de détail sont attendus pour la proposition financière ?

## 12. Prochaines étapes

1. Faire valider les acteurs, les types de biens, les workflows et le modèle provisoire dans `docs/uml/`.
2. Valider le contrat API et les événements proposés dans `docs/api/`.
3. Importer les maquettes de `docs/figma/` dans Figma, créer les liens du prototype et valider les parcours MVP.
4. Faire valider puis réviser l'estimation de charges, les coûts et les risques de `docs/finance/`.
5. Valider le socle Docker local de `docs/technique/docker-local.md`, puis implémenter les migrations, API et tests du MVP.
6. Scaffolder `auth` et `gateway` avant de compléter l'orchestration Docker de toute l'architecture.
7. Rédiger la fiche technique et le guide utilisateur à partir du fonctionnement réellement livré.
