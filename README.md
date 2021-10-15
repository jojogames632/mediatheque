# Médiathèque (click & collect)

La médiathèque moderne

## Prérequi

*Prérequis sur votre machine pour le bon fonctionnement de ce projet : 
- PHP Version 7.4.23 [Installer PHP](https://www.php.net/manual/fr/install.php) --  [Mettre à jour PHP en 7.4 (Ubuntu)](https://www.cloudbooklet.com/upgrade-php-version-to-php-7-4-on-ubuntu/)
- MySQL [Installer MySQL](https://doc.ubuntu-fr.org/mysql) ou [Installer MariaDB](https://doc.ubuntu-fr.org/mariadb)
- Symfony version 5.3 minimum [Installer Symfony](https://symfony.com/doc/current/setup.html) 
- Composer [Installer Composer](https://getcomposer.org/download/) 
- Npm  [Installer Npm](https://www.npmjs.com/get-npm)  

## Installation

Après avoir cloné le projet avec ``git clone https://github.com/jojogames632/mediatheque.git``

Exécutez la commande ``cd mediatheque`` pour vous rendre dans le dossier depuis le terminal.

Ensuite, dans l'ordre taper les commandes dans votre terminal : 

- 1 ``composer install`` afin d'installer toutes les dépendances composer du projet.

- 2 ``npm install``      afin d'installer toutes les dépendances npm du projet.

- 3 installer la base de donnée MySQL. 
   Pour paramétrer la création de votre base de donnée, rdv dans le fichier .env du projet, et modifier la variable d'environnement selon vos paramètres : 

  ``DATABASE_URL=mysql://User:Password@127.0.0.1:3306/nameDatabasse?serverVersion=5.7``
  
   Puis exécuter la création de la base de donnée avec la commande : ``symfony console doctrine:database:create``


- 4 Exécuter la migration en base de donnée :                                        ``symfony console doctrine:migration:migrate``

- 5 Exécuter les dataFixtures avec la commande :                                     ``php bin/console doctrine:fixtures:load``

- 6 Vous pouvez maintenant accéder à votre portfolio en vous connectant au serveur : ``symfony server:start``


## Démarrage

Une fois sur l'application, il ne vous reste plus qu'a vous créer un compte ``/register``.
L'application ne crée que des rôles REGISTRANT (inscrit)
Puis loger vous ``/login``

### Création d'un mot de passe crypté

- Allez sur le site bcrypt : https://www.bcrypt.fr/
- Saisissez le mot de passe désiré dans l'encart "Texte à hasher"
- Cliquez sur le bouton "Convertir avec bcrypt!"
- Votre mot de passe est maintenant hashé, ne fermez pas la page. Vous aurez besoin de ce hash pour créer votre compte admin

### Création d'un compte admin

- Pour commencer vous aurez besoin d'utiliser MySqlWorkbench (https://www.mysql.com/fr/products/workbench/)

- Une fois lancé, cliquez sur le petit + à droite de MySQL Connections pour ajouter une nouvelle connexion à un projet

- Remplissez les valeurs suivantes : 

* > Connection Name : mediatheque
* > Hostname : nnsgluut5mye50or.cbetxkdyhwsb.us-east-1.rds.amazonaws.com
* > Username : tk7601ug3s8owmi7
* > Password : yrpmt1b3kecagjvg

- Cliquez sur ok puis double cliquez sur la nouvelle connexion créé. 

### Insertion d'un compte admin en base de données

- Sur le logiciel MySQLWorkbench, cliquez sur l'onglet "Query 1".
- Copiez/collez l'instruction suivante en remplaçant les valeurs en majuscule : 
- INSERT INTO user (email, roles, password, is_active, lastname, firstname, birthdate, address) VALUES ('EMAIL', '["ROLE_ADMIN"]', 'MOT DE PASSE HASHÉ', true, LASTNAME, FIRSTNAME, BIRTHDATE, ADDRESS);
- Cliquez sur le premier logo éclair (à droite de la disquette pour enregistrer) afin d'exécuter la requête.
- Votre admin est créé !