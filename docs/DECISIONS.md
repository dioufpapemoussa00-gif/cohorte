# Décisions de conception — Projet Cohorte

## Décision 1 — Comportement en cas de panne d'OpenRouter (fail-closed)

Que se passe-t-il si OpenRouter est en panne au moment où un membre publie ? Deux positions
sont possibles : fail-open (publier quand même, sans contrôle) ou fail-closed (envoyer en file
de modération humaine, rien ne passe sans contrôle).

**Choix retenu** : fail-closed (COHORTE_MODERATION_FAIL_OPEN=false par défaut). En cas de panne,
la publication part dans la file de modération du délégué plutôt que d'être publiée directement.

**Raisonnement** : dans un contexte scolaire où le contenu modéré peut inclure des propos
inappropriés entre apprenants, je préfère un léger désagrément (attente de validation humaine
en cas de panne, situation rare) plutôt qu'un risque de laisser passer un contenu réellement
problématique sans aucun filtre. Le délégué reste en mesure de valider rapidement les
publications en attente.

**Alternative écartée** : fail-open, qui garderait l'application entièrement fluide même en cas
de panne, mais qui exposerait la promotion à des publications non contrôlées si la panne
survient précisément au moment où quelqu'un tente de publier un contenu problématique.

## Décision 2 — Stockage du score de réputation plutôt que recalcul à la volée

Le score de contribution (points) doit-il être stocké dans la colonne points de la table users,
ou recalculé à chaque affichage à partir des réponses retenues, réponses écrites et questions
posées ?

**Choix retenu** : stockage du score dans la colonne points, avec une commande artisan
(cohorte:recalculer-reputation) qui permet de remettre tous les compteurs d'aplomb à la demande.

**Raisonnement** : le stockage est rapide à lire (aucune agrégation nécessaire à chaque
affichage du profil ou du fil), ce qui compte pour une application qui affiche le score sur
plusieurs pages. Le risque de désynchronisation (si un incrément est oublié quelque part dans
le code) est compensé par la commande de recalcul, qui peut être relancée à tout moment pour
garantir l'exactitude des scores.

**Alternative écartée** : recalculer le score à chaque affichage via une requête d'agrégation.
Cette approche est toujours exacte mais coûte une requête supplémentaire à chaque affichage,
et devient plus lente à mesure que le volume de publications et réponses grossit.

## Décision 3 — Dénormalisation du promotion_id sur la table publications

La colonne promotion_id sur publications pourrait être déduite indirectement via l'auteur
(publication → user → promotion). Pourquoi la dupliquer directement sur la table publications ?

**Choix retenu** : ajout explicite d'une colonne promotion_id sur publications, indexée avec
statut et created_at.

**Raisonnement** : cette duplication rend la requête de cloisonnement (scope deLaPromotion())
une simple condition sur une colonne indexée, sans jointure vers la table users. Elle garantit
aussi qu'une publication reste rattachée à la promotion dans laquelle elle a été écrite, même
si son auteur venait à changer de promotion plus tard — ce qui correspond au comportement
attendu (une publication appartient à son contexte de création, pas à l'état actuel de son
auteur).

**Alternative écartée** : ne pas dupliquer la colonne et déduire la promotion via une jointure
sur l'auteur à chaque requête du fil. Plus normalisé, mais plus lent (jointure systématique) et
moins robuste si l'auteur change un jour de promotion.

## Décision 4 — Seuil de signalement fixé à 3

Le nombre de signalements à partir duquel une publication est masquée automatiquement du fil
est configurable via COHORTE_SEUIL_SIGNALEMENT.

**Choix retenu** : seuil par défaut de 3 signalements.

**Raisonnement** : un seuil bas favorise une réaction rapide de la communauté face à un contenu
problématique, ce qui est adapté à des promotions de taille réduite (8 membres dans le seeder
de démonstration) où réunir 3 signalements distincts représente déjà une part significative du
groupe. Un seuil trop élevé retarderait le masquage d'un contenu manifestement inapproprié dans
un petit groupe.

**Alternative écartée** : un seuil proportionnel à la taille de la promotion (par exemple 20%
des membres), qui aurait été plus juste sur le principe mais plus complexe à calculer et à
justifier simplement pour ce projet.

## Décision 5 — Le quota d'IA ne bloque pas la modération, seulement la détection de doublon

Pourquoi le quota d'IA ne bloque-t-il pas la publication normale (modération automatique) mais
seulement la détection de doublon ?

**Choix retenu** : la modération est une contrainte imposée par l'application elle-même, pas un
service rendu au membre — la lui refuser parce qu'il a épuisé son quota reviendrait à l'empêcher
de s'exprimer alors que la modération protège la promotion, pas l'utilisateur qui publie. La
détection de doublon, elle, est une assistance facultative : on peut la retirer sans dommage,
l'utilisateur peut toujours poser sa question normalement.

**Implémentation** : les deux fonctionnalités comptent leurs appels dans la table appels_ia
(contexte "moderation" ou "doublon"), mais seule la détection de doublon vérifie peutAppelerIa()
avant de s'exécuter, directement dans QuestionController::store().

**Alternative écartée** : appliquer un middleware quota.ia générique sur la route de création de
question, ce qui aurait bloqué la publication de la question elle-même en cas de quota épuisé,
alors que seule l'assistance de détection devrait être désactivée.

---

## Décisions complémentaires (hors périmètre strict du guide)

### Simplification de la redirection enseignant dans ExigePromotion

Le guide prévoit que le middleware ExigePromotion redirige un enseignant vers une route
enseignant.promotions.index, listant toutes les promotions. Cette route et son contrôleur ne
sont pas détaillés ailleurs dans le guide fourni.

**Choix retenu** : l'enseignant traverse simplement le middleware sans redirection spéciale.

**Alternative écartée** : créer un module enseignant complet, hors du périmètre strictement
demandé par les phases numérotées de ce guide.

### Remplacement de authorizeResource() par des appels authorize() explicites

Le guide utilise authorizeResource() dans le constructeur du contrôleur. Cette méthode a échoué
avec Laravel 13 (version utilisée dans ce projet plutôt que Laravel 12 prévu par le guide) car
elle dépend d'un mécanisme retiré dans les versions récentes du framework.

**Choix retenu** : appeler $this->authorize('action', $modele) explicitement au début de chaque
méthode du contrôleur.

**Alternative écartée** : downgrader le projet vers Laravel 12, ce qui aurait ajouté de la
complexité inutile et potentiellement d'autres incompatibilités.