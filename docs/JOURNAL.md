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


## Phase 2 — Les factories et les seeders

Branche : feat/02-seeders
Dates : 22 août 2026

### Ce que j'ai fait
Écriture du DatabaseSeeder générant deux promotions cloisonnées avec 8 membres chacune, des
publications et questions avec réponses, et les 4 comptes de démonstration obligatoires.

### Pourquoi je l'ai fait ainsi
J'ai utilisé recycle() pour réutiliser les membres déjà créés plutôt que d'en générer de
nouveaux à chaque publication, évitant ainsi des dizaines d'utilisateurs fantômes inutiles.

### La difficulté rencontrée
Le fichier DatabaseSeeder.php gardait le contenu par défaut de Laravel malgré mes modifications
précédentes, ce qui faisait échouer silencieusement le seeding (un seul utilisateur créé au
lieu de vingt). Une ligne DB_DATABASE=laravel oubliée dans le .env pointait aussi vers un
fichier SQLite parasite au lieu de la vraie base.

### Comment je l'ai résolue
Vérification du contenu réel du fichier avec Get-Content avant de conclure, remplacement complet
du DatabaseSeeder.php, et nettoyage du .env pour ne garder que DB_CONNECTION=sqlite.


## Phase 3 — L'authentification avec Laravel Fortify

Branche : feat/03-authentification-fortify
Dates : 22 août 2026

### Ce que j'ai fait
Installation de Laravel Fortify, activation des fonctionnalités d'inscription, connexion et
réinitialisation de mot de passe, écriture des vues Blade correspondantes, et validation du
code d'invitation directement dans l'action CreateNewUser pour rattacher automatiquement le
nouvel utilisateur à sa promotion.

### Pourquoi je l'ai fait ainsi
J'ai choisi Fortify plutôt que Breeze car il ne fournit que la logique métier sécurisée
(hachage, limitation des tentatives, jetons de réinitialisation) sans imposer de vues, ce qui
me permet d'écrire moi-même les formulaires tout en bénéficiant des mécanismes de sécurité
éprouvés.

### La difficulté rencontrée
Une erreur de syntaxe PHP dans config/fortify.php causée par un reste de code (passkeys) non
supprimé lors de la modification de la section features. Le gabarit layouts/app.blade.php
plantait aussi car il référençait des routes (feed.index, etc.) qui n'existent pas encore à
ce stade du projet.

### Comment je l'ai résolue
Nettoyage du fichier fortify.php en supprimant le bloc orphelin. Utilisation de Route::has()
dans le gabarit pour vérifier l'existence d'une route avant de générer un lien vers elle,
permettant au projet de rester fonctionnel phase après phase sans erreur 500.