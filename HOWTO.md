# How To

## Installation

- Installation de nos dépendances avec composer `composer install`
- Création de la BDD
- Import de la BDD d'exemple, située dans le dossier `data`
- Changer dans la BDD, table `wp_options`:
    - `siteurl` saisir l'url locale du projet (en terminant par `/wp`)
    - `home` saisir l'url locale du projet
- Création du fichier `wp-config.php` à partir du fichier `wp-config-sample.php`
    - Renseigner les informations de connexion à la BDD
    - Renseigner les clés de salage [URL pratique](https://api.wordpress.org/secret-key/1.1/salt/)
    - Renseigner la constante `WP_CONTENT_URL` avec notre url locale
- Changer les droits des fichiers/dossier de mon projet (penser à ajuster `<user>`)
    ```bash
    sudo chown -R <user>:www-data .
    sudo find . -type f -exec chmod 664 {} +
    sudo find . -type d -exec chmod 775 {} +
    sudo chmod 644 .htaccess
    ```
- Le thème installé est `oCooking`. Le front étant généré via Webpack, il faut le reconstruire.
    - Se rendre dans `content/themes/ocooking`
    - `npm install`
    - `npm run build:prod`
  
## Connexion

- Dans cette base de données d'exemples, l'administrateur du site est `ocooking` le mot de passe du compte est `ocooking`
- Dans cette base de données d'exemples, le cuisinier du site est `jean` le mot de passe du compte est `bonbeurre`