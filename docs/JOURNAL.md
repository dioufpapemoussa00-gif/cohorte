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