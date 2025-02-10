# Knowledge-Learning-project

Creer avec :
- Symfony - Le framework PHP.
- Doctrine - ORM pour la gestion de la base de données.
- Twig - Moteur de templates
- Webpack Encore - Pour la gestion des assets front-end

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **PHP** : Version 8 ou supérieure.
- **Composer** : Gestionnaire de dépendances pour PHP.
- **Node.js et npm** : Pour la gestion des packages frontend.
- **Serveur Web** : Apache.
- **Base de données** : MySQL, PostgreSQL ou SQLite.

## Installation

Suivez ces étapes pour installer et configurer l'application :

1. Cloner le dépôt.

   git clone https://github.com/ILince/Knowledge-Learning-project-2/tree/Knowledge-Learning-project
   cd /Knowledge-Learning-project


2. Installer les dépendances.

   composer install


3. Installer les dépendances JavaScript.
    
    npm install


4. Configurer les variables d'environnement.

Dupliquez le fichier .env en .env.local et modifiez les paramètres selon votre environnement, notamment la connexion à la base de données.
   
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"


5. Créer la base de données et exécuter les migrations.

   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate


6. Charger les fixtures.

   php bin/console doctrine:fixtures:load


7. Compiler les assets front-end.
   
   npm run dev

8. Lancer le serveur de développement Symfony.
   
   symfony server:start

Pour arrêter le serveur :
   symfony server:stop


