
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