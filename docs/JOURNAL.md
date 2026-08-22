## Phase 0 — Installation et mise en place du dépôt

Branche : feat/00-installation
Dates : 20-21 août 2026

### Ce que j'ai fait
Installation du projet Laravel 12 via Laravel Herd, configuration de SQLite comme base de
données, mise en place du dépôt Git avec .env.example, création de la configuration métier
centralisée (config/cohorte.php) et du gabarit de base.

### Pourquoi je l'ai fait ainsi
J'ai utilisé Herd car composer create-project seul provoquait une erreur de permission Windows.
Herd fournit un environnement PHP/Composer isolé qui évite ce problème. J'ai choisi SQLite
plutôt que MySQL car c'est recommandé par le guide et ça simplifie l'installation pour le
correcteur.

### La difficulté rencontrée
Erreurs de permission avec Composer sous Windows, le lien http://cohorte.test qui refusait de
s'ouvrir par moments, et une faute de frappe dans le nom du dossier components (écrit
"compenents") qui a bloqué un commit.

### Comment je l'ai résolue
Installation de Herd pour contourner le problème Composer. Utilisation de php artisan serve et
http://127.0.0.1:8000 comme solution de secours pour le lien Herd. Renommage du dossier avec
Rename-Item une fois la faute de frappe repérée grâce au message d'erreur Git.

## Phase 1 — Le modèle de données et les relations Eloquent

Branche : feat/01-modele-donnees
Dates : 21 août 2026

### Ce que j'ai fait
Création des 6 tables (promotions, publications, reponses, signalements, appels_ia, et
l'extension de users), des modèles Eloquent avec leurs relations et scopes, et des factories
associées.

### Pourquoi je l'ai fait ainsi
J'ai suivi la structure du guide en une seule table publications pour les posts et les questions,
distingués par une colonne type, afin d'éviter de dupliquer la logique de modération et de
signalement.

### La difficulté rencontrée
Le nom de table généré automatiquement pour AppelIa était appel_ias au lieu de appels_ia attendu,
et les factories généraient une erreur de classe introuvable faute d'imports explicites des
modèles Promotion et User. La syntaxe des modèles diffère aussi légèrement du guide car mon
projet utilise Laravel 13 (attributs #[Fillable] et #[Hidden]) au lieu de Laravel 12.

### Comment je l'ai résolue
J'ai forcé le nom de table avec protected $table = 'appels_ia' dans le modèle, ajouté les
imports use App\Models\Promotion et use App\Models\User dans les factories concernées, et
adapté la syntaxe #[Fillable] pour ajouter promotion_id, role et points.