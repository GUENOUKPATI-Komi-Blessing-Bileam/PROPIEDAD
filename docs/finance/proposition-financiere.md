# Proposition financière indicative — MVP Patrimoine de l'État

**Version :** estimation pédagogique initiale  
**Devise :** franc CFA BCEAO (XOF), à confirmer selon le cadre du commanditaire  
**Périmètre estimé :** MVP décrit dans `docs/analyse-besoin.md`  
**Statut :** hypothèses de travail, non assimilables à un devis fournisseur

## 1. Objet de la proposition

Estimer les efforts et coûts de conception, de développement et de mise en service d'un MVP de gestion des biens de l'État, organisé selon les services prévus :

- Gateway et authentification ;
- gestion des biens, mouvements et campagnes d'inventaire ;
- historique d'audit et rapports ;
- interface web responsive ;
- conteneurisation Docker, tests, documentation technique et guide utilisateur.

Le périmètre est celui du MVP. Les intégrations aux systèmes nationaux, le mode hors ligne, l'application mobile native, les circuits de signature et les règles comptables avancées sont exclus.

## 2. Hypothèses de chiffrage

1. Équipe non encore définie ; le scénario de référence est **une personne full-stack à temps plein**, avec revue ponctuelle du commanditaire.
2. Le tarif pédagogique est fixé à **35 000 XOF par jour-personne**. Il s'agit d'une hypothèse de calcul et non d'un tarif de marché vérifié.
3. Le travail de conception déjà amorcé (analyse, modèles UML/Merise, maquettes SVG et contrat OpenAPI) est considéré comme disponible. Les jours associés couvrent leur revue et leur ajustement, pas une reprise complète.
4. Les services Laravel existants sont des squelettes ; l'estimation suppose que les dépendances et environnements de développement restent utilisables.
5. L'hébergement de démonstration est provisionné à **50 000 XOF par mois pendant trois mois**, soit une enveloppe de 150 000 XOF. Ce montant est une réserve indicative à remplacer par des devis adaptés à l'hébergement retenu.
6. La charge totale de référence est de 80 jours-personnes. Elle exclut les délais d'attente de validation par le ministère.
7. Les montants ne comprennent pas les taxes éventuelles, licences payantes, matériels, déplacements, frais de formation externes ni maintenance après la période de démonstration.

## 3. Estimation de charge et coût de réalisation

| Lot | Livrable principal | Charge indicative | Coût à 35 000 XOF/jour |
|---|---|---:|---:|
| Cadrage et validation | Besoin, rôles, règles métier et critères d'acceptation confirmés | 4 j | 140 000 XOF |
| UX et maquettes | Parcours MVP, revue des écrans et prototype Figma | 6 j | 210 000 XOF |
| Socle technique et Docker | Configuration des services, variables d'environnement et exécution locale | 4 j | 140 000 XOF |
| Authentification et accès | Auth, rôles, permissions et contrôle de périmètre | 7 j | 245 000 XOF |
| Gestion des biens | Référentiels, fiches, recherche, filtres et QR code | 16 j | 560 000 XOF |
| Mouvements et inventaires | Demandes, validation, campagnes, contrôles et écarts | 11 j | 385 000 XOF |
| Historique | Outbox, réception idempotente et consultation autorisée | 4 j | 140 000 XOF |
| Rapports | Indicateurs du tableau de bord et export CSV autorisé | 5 j | 175 000 XOF |
| Interface web | Écrans dynamiques responsive reliés aux API | 12 j | 420 000 XOF |
| Intégration et qualité | Tests fonctionnels, corrections et vérification des parcours | 7 j | 245 000 XOF |
| Documentation et transfert | Fiche technique, guide utilisateur, déploiement et présentation | 4 j | 140 000 XOF |
| **Total réalisation** |  | **80 j** | **2 800 000 XOF** |

Cette décomposition est un ordre de grandeur. Les lots peuvent être réestimés une fois les règles métier et les choix d'authentification confirmés.

## 4. Budget indicatif

| Poste | Montant estimé |
|---|---:|
| Réalisation — 80 jours-personnes | 2 800 000 XOF |
| Réserve pour aléas — 10 % du coût de réalisation | 280 000 XOF |
| Enveloppe hébergement de démonstration — 3 mois | 150 000 XOF |
| **Budget indicatif total** | **3 230 000 XOF** |

Le budget ne vaut pas engagement contractuel. La réserve n'est utilisée qu'après accord sur un changement ou un aléa constaté. Le montant d'hébergement est une provision, non un prix fournisseur confirmé. À titre d'ordre de grandeur, l'enveloppe hors hébergement est de **3 080 000 XOF**, réserve incluse.

### Dépenses récurrentes après la démonstration

L'hébergement, le nom de domaine, les sauvegardes, la supervision, le support et les mises à jour devront faire l'objet d'un choix et d'un budget séparés. Une provision de 50 000 XOF/mois a été retenue uniquement pour les trois mois de démonstration ; ce montant ne doit pas être extrapolé sans devis d'hébergement.

## 5. Calendrier prévisionnel

Pour une personne à temps plein, 80 jours-personnes représentent environ 16 semaines de travail (cinq jours par semaine), hors congés et délais de validation.

| Période indicative | Activités / jalon |
|---|---|
| Semaines 1–2 | Validation du besoin, des rôles et des contrats API ; revue des maquettes |
| Semaines 3–4 | Socle Docker, bases de données par service, authentification et contrôle d'accès |
| Semaines 5–8 | Registre des biens, référentiels, recherche et fiche détaillée |
| Semaines 9–11 | Mouvements et campagnes d'inventaire |
| Semaines 12–13 | Historique, rapports et exports |
| Semaines 14–15 | Interface, intégration et tests des parcours MVP |
| Semaine 16 | Corrections finales, documentation, démonstration et transfert |

Avec plusieurs personnes, le délai calendaire peut diminuer, mais le coût ne baisse pas automatiquement : les tâches doivent être parallélisables, les responsabilités réparties et l'intégration coordonnée.

## 6. Risques, impacts et mesures

| Risque | Impact possible | Mesure proposée |
|---|---|---|
| Règles de validation, rôles ou référentiels non confirmés | Reprise de données et de fonctionnalités | Valider les acteurs, les workflows et les champs avant le développement concerné |
| Volume ou qualité des données inconnus | Recherche lente ou migration plus coûteuse | Obtenir un échantillon de données anonymisé et les volumes cibles |
| Authentification et hébergement publics non décidés | Retard d'intégration et coût récurrent imprécis | Faire valider les exigences informatiques et de souveraineté des données |
| Dépendance réseau lors des inventaires | Saisie terrain impossible dans les zones sans connexion | Confirmer la couverture réseau ; chiffrer le mode hors ligne séparément |
| Services multiples trop complexes pour l'équipe disponible | Retard, difficultés de déploiement et de maintenance | Limiter le MVP, documenter les contrats, séparer les bases sans introduire prématurément un broker |
| Retard dans les validations du commanditaire | Décalage de calendrier | Organiser des démonstrations régulières et faire approuver chaque jalon |
| Exports ou journaux contenant des données trop sensibles | Risque de confidentialité et de conformité | Contrôle d'accès serveur, minimisation des données, règles de conservation et revue des exports |
| Prix réels d'hébergement ou taxes différents de l'hypothèse | Dépassement du budget | Remplacer les provisions par des devis et préciser les taxes avant engagement |

## 7. Conditions de révision du budget

Une nouvelle estimation est nécessaire si le périmètre inclut notamment :

- plusieurs circuits d'approbation ou signatures électroniques ;
- une application mobile hors ligne et synchronisation ;
- cartographie SIG ou gestion détaillée du patrimoine immobilier ;
- import massif, nettoyage ou rapprochement de données existantes ;
- intégrations avec les systèmes financiers, cadastraux ou d'identité ;
- disponibilité élevée, hébergement certifié, supervision ou support permanent ;
- exigence de migration ou de reprise des archives.

## 8. Validation attendue du commanditaire

Avant de transformer cette proposition indicative en devis :

1. confirmer le pays, la devise et le régime fiscal applicable ;
2. confirmer la composition de l'équipe et le tarif journalier réel ;
3. valider les fonctionnalités incluses et les exclusions du MVP ;
4. confirmer l'hébergement, la durée de démonstration et les exigences de sauvegarde ;
5. approuver les règles d'accès, de transfert, d'inventaire et de conservation ;
6. réviser les charges avec les personnes qui réaliseront effectivement le projet.

