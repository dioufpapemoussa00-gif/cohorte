# Journal d'utilisation de l'IA — Projet Cohorte

Ce fichier documente, phase par phase, mes échanges avec Claude (Anthropic) pour ce projet.

## Phase 0 — Installation

**Demandé** : aide pour installer le projet Laravel et résoudre des erreurs de permission
Windows avec Composer.

**Retenu** : l'utilisation de Laravel Herd comme environnement PHP/Composer isolé pour
contourner les erreurs de permission rencontrées avec composer create-project seul.

**Rejeté** : aucune proposition de code métier à ce stade, uniquement de l'aide à l'installation.

## Phase 1 — Modèle de données et relations Eloquent

**Demandé** : le contenu des migrations, modèles Eloquent et relations selon le guide fourni.

**Retenu** : la structure des migrations et des relations telle que proposée par le guide,
adaptée à la syntaxe Laravel 13 (attributs #[Fillable] et #[Hidden] au lieu de la propriété
protected $fillable classique utilisée par Laravel 12).

**Rejeté** : rien de spécifique rejeté, mais j'ai dû corriger moi-même un oubli de commit des
modèles (ils avaient été écrits mais jamais réellement suivis par Git), repéré en vérifiant
git log --oneline sur les fichiers concernés.

## Phase 2 — Factories et seeders

**Demandé** : le contenu du DatabaseSeeder pour générer les deux promotions et les comptes de
démonstration.

**Retenu** : la structure proposée avec recycle() pour éviter de générer des utilisateurs
fantômes inutiles.

**Rejeté** : rien — mais j'ai identifié moi-même que le fichier DatabaseSeeder.php gardait son
contenu par défaut malgré mes modifications précédentes (vérifié avec Get-Content avant de
relancer le seeder), ce qui m'a appris à toujours vérifier le contenu réel d'un fichier plutôt
que de supposer qu'une modification a été appliquée.

## Phase 3 — Authentification Fortify

**Demandé** : le code de CreateNewUser.php pour valider le code d'invitation, et le
FortifyServiceProvider pour déclarer les vues et la limitation de connexion.

**Retenu** : la validation du code d'invitation directement dans l'action CreateNewUser,
conforme au guide.

**Rejeté** : j'ai dû corriger une erreur de syntaxe PHP dans config/fortify.php provoquée par
un reste de code (passkeys) non supprimé lors d'une modification — j'ai identifié et retiré
ce bloc orphelin moi-même après avoir lu le message d'erreur PHP.

## Phase 4 — Adhésion à une promotion et profil

**Demandé** : le middleware ExigePromotion tel que décrit par le guide, qui redirige un
enseignant vers une route enseignant.promotions.index.

**Rejeté** : cette route n'étant pas détaillée dans le guide fourni, j'ai choisi de simplifier
le comportement (l'enseignant traverse simplement le middleware sans redirection spéciale)
plutôt que d'inventer un module enseignant complet hors du périmètre demandé. Ce choix est
documenté dans DECISIONS.md.

**Retenu** : le reste du middleware et le contrôleur d'adhésion tels que proposés.

## Phase 5 — Fil de promotion et cloisonnement

**Demandé** : le contrôleur de ressource avec authorizeResource() dans le constructeur, comme
décrit par le guide (conçu pour Laravel 12).

**Rejeté** : authorizeResource() provoquait une erreur "Call to undefined method middleware()"
avec Laravel 13. J'ai remplacé cette approche par des appels explicites à $this->authorize()
au début de chaque méthode du contrôleur, qui produisent le même comportement de sécurité.
Ce choix est documenté dans DECISIONS.md.

**Retenu** : la policy