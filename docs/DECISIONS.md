
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