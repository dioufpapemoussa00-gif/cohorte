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


## Phase 4 — Rejoindre une promotion et le profil

Branche : feat/04-adhesion-promotion
Dates : 22 août 2026

### Ce que j'ai fait
Création du middleware ExigePromotion qui redirige un utilisateur sans promotion vers un
formulaire de saisie de code d'invitation, du contrôleur AdhesionController pour traiter cette
adhésion après coup, et d'une page de profil simple affichant les informations du membre.

### Pourquoi je l'ai fait ainsi
Le middleware garantit qu'aucune route du groupe promotion ne peut recevoir un utilisateur sans
promotion_id, évitant ainsi des erreurs de type TypeError dans les scopes qui attendent un
entier. C'est une invariante vérifiée une fois en amont plutôt que dans chaque contrôleur.

### La difficulté rencontrée
Le middleware original du guide redirigeait l'enseignant vers une route enseignant.promotions.index
qui n'est pas détaillée dans ce guide et sort du périmètre actuel du projet.

### Comment je l'ai résolue
J'ai simplifié la condition : un enseignant traverse simplement le middleware sans redirection
spécifique, en attendant une éventuelle implémentation future du module enseignant. Ce choix est
documenté dans DECISIONS.md.


## Phase 5 — Le fil de promotion et le cloisonnement

Branche : feat/05-fil-promotion
Dates : 22 août 2026

### Ce que j'ai fait
Création du contrôleur de ressource PublicationController avec ses cinq actions (index, create,
store, show, destroy), de la policy PublicationPolicy qui protège l'accès à chaque publication,
d'un FormRequest dédié pour la validation, et des vues du fil avec pagination.

### Pourquoi je l'ai fait ainsi
J'ai combiné deux mécanismes de protection distincts : le scope deLaPromotion() qui filtre la
liste des publications visibles dans le fil, et la policy qui protège l'accès direct à une
publication par son URL. Les deux sont nécessaires, car protéger uniquement l'un des deux
laisse une brèche : un utilisateur pourrait deviner un identifiant et y accéder directement
même s'il n'apparaît jamais dans son fil.

### La difficulté rencontrée
La méthode authorizeResource() du contrôleur provoquait une erreur "Call to undefined method
middleware()" avec Laravel 13, car cette méthode s'appuyait en interne sur un mécanisme retiré
des versions récentes du framework.

### Comment je l'ai résolue
J'ai remplacé authorizeResource() par des appels explicites à $this->authorize() au début de
chaque méthode du contrôleur, ce qui produit exactement le même comportement de sécurité de
façon plus explicite. J'ai testé le cloisonnement en créant une publication avec le compte Awa
(Groupe A), puis en tentant d'y accéder directement par URL avec le compte Fatou (Groupe B) :
l'application a correctement renvoyé une erreur 403.


## Phase 6 — L'entraide : questions et réponses

Branche : feat/06-entraide
Dates : 22 août 2026

### Ce que j'ai fait
Création des contrôleurs QuestionController, ReponseController et ReponseRetenueController,
avec les vues associées permettant de poser une question, d'y répondre, et de désigner une
réponse comme retenue avec crédit automatique de points à son auteur.

### Pourquoi je l'ai fait ainsi
J'ai séparé la désignation de la réponse retenue dans son propre contrôleur plutôt que d'ajouter
une méthode au QuestionController, car ce n'est pas une modification de la question mais une
action à part entière avec ses propres droits (seul l'auteur de la question peut le faire).

### La difficulté rencontrée
Aucune difficulté technique majeure sur cette phase, elle consolidait surtout des patterns déjà
mis en place en phase 5 (policy, scope, FormRequest).

### Comment je l'ai résolue
J'ai réutilisé la policy PublicationPolicy existante en y ajoutant simplement la méthode
designerReponse(), et testé le parcours complet avec trois comptes différents (auteur de la
question, répondant, puis retour à l'auteur pour valider la réponse).


## Phase 7 — Intégration OpenRouter et modération automatique

Branche : feat/07-moderation-ia
Dates : 26 août 2026

### Ce que j'ai fait
Création du client OpenRouterClient isolant tous les appels HTTP externes, de l'énumération
VerdictModeration à quatre états, du ServiceModeration avec parsing défensif de la réponse IA,
et branchement de la modération automatique sur la création de publication.

### Pourquoi je l'ai fait ainsi
J'ai isolé la communication HTTP dans une classe dédiée (app/Services/OpenRouterClient.php) afin
qu'aucun contrôleur ne contienne d'appel réseau direct : si je change de fournisseur d'IA un jour,
un seul fichier est à modifier. Le parsing de la réponse du modèle est volontairement défensif
(regex pour extraire le JSON, json_decode avec vérification, tryFrom sur l'enum) car un modèle de
langage ne garantit jamais un format de sortie strict.

### La difficulté rencontrée
Le premier modèle gratuit choisi (google/gemma-4-26b-a4b-it:free) renvoyait une erreur 429
"temporarily rate-limited upstream" à chaque appel, rendant le service inutilisable en pratique.

### Comment je l'ai résolue
J'ai basculé sur l'identifiant spécial openrouter/free, un routeur automatique fourni par
OpenRouter qui sélectionne lui-même un modèle gratuit disponible parmi tous ceux du catalogue,
évitant ainsi de dépendre de la disponibilité d'un seul modèle nommé. J'ai testé les quatre
scénarios possibles (verdict acceptable réel, verdict inacceptable simulé, panne réseau simulée,
réponse illisible simulée) avec Http::fake() pour valider la robustesse sans consommer de vrais
appels API.


## Phase 8 — Les signalements et le masquage automatique

Branche : feat/08-signalements
Dates : 28 août 2026

### Ce que j'ai fait
Création du SignalementController qui gère le signalement d'une publication avec interdiction
du double signalement, du masquage automatique au seuil configuré, et du FileModerationController
permettant au délégué de valider ou refuser les publications en attente.

### Pourquoi je l'ai fait ainsi
La policy signaler() (créée en phase 5) empêche déjà l'auto-signalement et le signalement hors
promotion au niveau de l'autorisation. Le contrôleur ne gère donc que la vérification du double
signalement, en complément de la contrainte unique déjà posée en base de données sur
(publication_id, user_id) : la contrainte protège les données quoi qu'il arrive, la vérification
PHP permet d'afficher un message compréhensible plutôt qu'une erreur SQL brute.

### La difficulté rencontrée
En testant le masquage automatique via tinker, j'ai simulé plusieurs connexions avec Auth::login()
dans une boucle, ce qui a produit un comportement incohérent où le masquage ne se déclenchait pas
malgré trois signalements enregistrés en base.

### Comment je l'ai résolue
J'ai isolé le test en reproduisant manuellement, étape par étape, la logique exacte de
masquerSiSeuilAtteint() (comptage, comparaison au seuil, mise à jour du statut), ce qui a confirmé
que la logique métier elle-même était correcte. Le problème venait uniquement de la simulation
artificielle de plusieurs sessions utilisateur


## Phase 9 — Le quota d'IA et la détection de doublon

Branche : feat/09-quota-et-doublon
Dates : 28 août 2026

### Ce que j'ai fait
Ajout des méthodes de calcul de quota au modèle User (appelsIaAujourdhui, quotaIaRestant,
peutAppelerIa), création du ServiceDetectionDoublon qui compare une nouvelle question aux
questions existantes de la promotion via OpenRouter, et affichage du quota restant dans le
gabarit principal.

### Pourquoi je l'ai fait ainsi
Le service de détection refiltre systématiquement les identifiants renvoyés par l'IA avec
deLaPromotion() avant de les utiliser, car un modèle de langage peut halluciner un identifiant
qui ne faisait même pas partie du catalogue envoyé initialement.

### La difficulté rencontrée
Une erreur de nom de fichier (ServiceDetectionDoublon mal orthographié) a empêché la classe
d'être trouvée par le conteneur d'injection de dépendances de Laravel.

### Comment je l'ai résolue
Vérification du contenu réel du dossier app/Services avec la commande dir avant de conclure
à un bug de logique, correction du nom de fichier, puis validation du fonctionnement avec un
test en tinker créant une question de référence et vérifiant qu'une reformulation proche était
bien détectée comme similaire, tandis qu'une question totalement différente ne l'était pas.


## Phase 10 — La réputation et les finitions

Branche : feat/10-reputation
Dates : 28 août 2026

### Ce que j'ai fait
Création de la commande cohorte:recalculer-reputation qui calcule le score de chaque membre à
partir de son activité utile, ajout du droit d'épingler dans la policy au-delà du seuil configuré,
tri portable du fil (compatible SQLite et MySQL), page d'erreur 403 personnalisée, et finalisation
du README et de .env.example.

### Pourquoi je l'ai fait ainsi
J'ai choisi de stocker le score de réputation dans la colonne points plutôt que de le recalculer
à chaque affichage, ce qui est rapide à lire. La commande de recalcul permet de remettre les
compteurs d'aplomb si un incrément a été manqué quelque part, combinant ainsi rapidité et
possibilité de correction.

### La difficulté rencontrée
Le tri par défaut des publications épinglées avec orderByDesc('epingle_le') fonctionne
différemment selon le moteur de base de données (NULL en premier ou en dernier selon SQLite ou
PostgreSQL notamment).

### Comment je l'ai résolue
Ajout d'un orderByRaw('epingle_le IS NULL') avant le tri principal, qui garantit un comportement
identique quel que soit le moteur de base de données utilisé pour exécuter le projet.