
## Décision — Simplification de la redirection enseignant dans ExigePromotion

Le guide prévoit que le middleware ExigePromotion redirige un enseignant vers une route
enseignant.promotions.index, listant toutes les promotions. Cette route et son contrôleur ne
sont pas détaillés ailleurs dans le guide fourni.

**Choix retenu** : l'enseignant traverse simplement le middleware sans redirection spéciale,
ce qui lui permet d'accéder aux routes protégées comme n'importe quel utilisateur, sans jamais
être bloqué par l'absence de promotion_id (qui est nul pour lui par design).

**Alternative écartée** : créer un module enseignant complet avec sa propre route et vue de
listing des promotions, qui aurait ajouté de la complexité hors du périmètre strictement
demandé par les phases numérotées de ce guide.

## Décision — Remplacement de authorizeResource() par des appels authorize() explicites

Le guide utilise authorizeResource() dans le constructeur du contrôleur pour lier automatiquement
chaque méthode à la policy correspondante. Cette méthode a échoué avec Laravel 13 (version
utilisée dans ce projet plutôt que Laravel 12 prévu par le guide) car elle dépend d'un mécanisme
de middleware au niveau du contrôleur qui a été retiré dans les versions récentes du framework.

**Choix retenu** : appeler $this->authorize('action', $modele) explicitement au début de chaque
méthode du contrôleur (index, create, store, show, destroy).

**Alternative écartée** : downgrader le projet vers Laravel 12 pour coller exactement au guide,
ce qui aurait ajouté de la complexité inutile et potentiellement d'autres incompatibilités avec
les autres packages installés (Fortify notamment).


## Décision — Comportement en cas de panne d'OpenRouter (fail-closed)

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


## Décision — Le quota ne bloque pas la modération, seulement la détection de doublon

Pourquoi le quota d'IA ne bloque-t-il pas la publication normale (modération automatique) mais
seulement la détection de doublon ?

**Choix retenu** : la modération est une contrainte imposée par l'application elle-même, pas un
service rendu au membre — la lui refuser parce qu'il a épuisé son quota reviendrait à l'empêcher
de s'exprimer alors que la modération protège la promotion, pas l'utilisateur qui publie. La
détection de doublon, elle, est une assistance facultative : on peut la retirer sans dommage,
l'utilisateur peut toujours poser sa question normalement.

**Implémentation** : les deux fonctionnalités comptent leurs appels dans la table appels_ia
(contexte "moderation" ou "doublon"), mais seule la détection de doublon vérifie peutAppelerIa()
avant de s'exécuter (directement dans QuestionController::store(), sans middleware séparé, car
la vérification est déjà simple à cet endroit précis).

**Alternative écartée** : appliquer un middleware quota.ia générique sur la route de création de
question, ce qui aurait bloqué la publication de la question elle-même en cas de quota épuisé,
alors que seule l'assistance de détection devrait être désactivée.