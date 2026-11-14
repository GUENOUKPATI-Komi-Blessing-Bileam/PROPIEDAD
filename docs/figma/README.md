# Maquettes MVP — Patrimoine de l'État

## Importer les écrans dans Figma

Les fichiers SVG de ce dossier sont des maquettes statiques vectorielles, conçues pour servir de base modifiable dans Figma.

1. Dans Figma, créer un nouveau fichier de design.
2. Glisser-déposer un SVG de ce dossier dans la page, ou utiliser **File > Place image**.
3. Créer une page Figma par parcours si souhaité ; chaque écran mesure `1440 × 1000 px`.
4. Répéter pour chaque SVG. Les textes et formes peuvent être sélectionnés et adaptés après import. Selon la version de Figma, utiliser **Ungroup** pour modifier des éléments séparément.
5. Pour le prototype, créer les liens entre les boutons et les frames correspondantes avec l'onglet **Prototype**.

Les maquettes ne sont pas un fichier `.fig` et ne sont pas publiées dans un espace Figma : il faut les importer dans le compte Figma de l'étudiant. Elles sont des propositions visuelles statiques, pas des écrans fonctionnels.

## Écrans inclus

| Fichier | Objectif | Actions principales à prototyper |
|---|---|---|
| [connexion.svg](./connexion.svg) | Accès sécurisé à la plateforme | « Se connecter » → Tableau de bord |
| [tableau-de-bord.svg](./tableau-de-bord.svg) | Vue synthétique du patrimoine | « Voir tous les biens » → Liste ; alertes → Inventaire |
| [liste-biens.svg](./liste-biens.svg) | Rechercher et filtrer les biens | Sélection d'une ligne → Fiche ; « Enregistrer un bien » → Formulaire |
| [fiche-bien.svg](./fiche-bien.svg) | Consulter le détail d'un bien | « Modifier », « Nouveau mouvement », onglets |
| [formulaire-bien.svg](./formulaire-bien.svg) | Créer une fiche de bien | « Enregistrer » → Fiche du bien |
| [mouvement.svg](./mouvement.svg) | Demander un transfert de bien | « Soumettre » → confirmation et liste des mouvements |
| [inventaire.svg](./inventaire.svg) | Suivre une campagne et ses écarts | « Scanner / saisir un code », ligne → détail, « Exporter » |
| [rapports.svg](./rapports.svg) | Filtrer et exporter des rapports | Filtres → « Générer » → « Exporter CSV » |
| [historique-audit.svg](./historique-audit.svg) | Consulter le journal centralisé des événements d'audit | Filtrer, consulter le payload, exporter CSV |

## Langage visuel proposé

- **Format :** desktop `1440 × 1000 px`, contenu organisé pour adaptation responsive ultérieure.
- **Typographie :** Inter avec repli sans-serif.
- **Couleurs :** bleu nuit pour la navigation, vert pétrole pour l'action principale, fond gris très clair, couleurs d'état différenciées.
- **Composants :** navigation latérale, barre supérieure, cartes KPI, tableaux, filtres, badges d'état, boutons et champs.
- **Accessibilité à vérifier lors de l'intégration :** contraste des textes, libellés de champs, navigation clavier et messages d'erreur.

## Parcours MVP recommandé

Connexion → Tableau de bord → Liste des biens → Création / fiche du bien → Demande de mouvement ou inventaire → Rapport.

Les textes et données affichés sont fictifs pour la maquette. Avant usage réel, confirmer les libellés, les rôles, les couleurs de l'identité visuelle officielle et les champs obligatoires avec le ministère.
