<?php

class oCookingRestApi
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'authorField']);
        add_action('rest_api_init', [$this, 'thumbnailField']);
        add_action('rest_api_init', [$this, 'metaField']);
        add_action('rest_api_init', [$this, 'taxoField']);
    }

    public function authorField()
    {
        // Je souhaite étendre(=modifier) le retour de la REST Api de WP
        register_rest_field(
            // Type(s) de contenu(s) que je souhaite modifier
            // (ici les articles & les recettes)
            ['post', 'recipe'],
            // Nom du champ/clé à ajouter
            // (si la clé existe, elle est modifiée)
            'author',
            // La liste des méthodes à executer pour
            // traiter mon champ
            [
                // fonction à appeler lors d'un GET
                'get_callback' => [$this, 'getAuthorName'],
                //  fonction à appeler lors d'un POST
                'update_callback' => null,
                // Structure de ma donnée
                'schema' => null
            ]
        );
    }

    /**
     * Callback appelé lors d'un GET (ex: wp/v2/posts) pour modifier la clé "author"
     *
     * @param array $object Le post courant (tableau avec toutes les données du contenu AVANT la modification en cours)
     * @param string $field_name Le nom du champ/clé ciblé (ou du nouveau champ/clé créé).
     * @param WP_REST_Request $request Requête courante
     *
     * @return void
     */
    public function getAuthorName($object, $field_name, $request)
    {
        return [
            'id' => $object['author'],
            'name' => get_the_author_meta('nickname', $object['author'])
        ];
    }

    public function thumbnailField()
    {
        // Je souhaite étendre(=modifier) le retour de la REST Api de WP
        register_rest_field(
            // Type(s) de contenu(s) que je souhaite modifier
            // (ici les articles & les recettes)
            ['post', 'recipe'],
            // Nom du champ/clé à ajouter
            // (si la clé existe, elle est modifiée)
            'thumbnail',
            // La liste des méthodes à executer pour
            // traiter mon champ
            [
                // fonction à appeler lors d'un GET
                'get_callback' => [$this, 'getThumbnailDetails'],
                //  fonction à appeler lors d'un POST
                'update_callback' => null,
                // Structure de ma donnée
                'schema' => null
            ]
        );
    }

    /**
     * Callback appelé lors d'un GET (ex: wp/v2/posts) pour modifier la clé "author"
     *
     * @param array $object Le post courant (tableau avec toutes les données du contenu AVANT la modification en cours)
     * @param string $field_name Le nom du champ/clé ciblé (ou du nouveau champ/clé créé).
     * @param WP_REST_Request $request Requête courante
     *
     * @return void
     */
    public function getThumbnailDetails($object, $field_name, $request)
    {
        // Je vérifie si mon contenu à une image de mise en avant
        if (!has_post_thumbnail($object['id'])) {

            return false;
        }

        // https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/

        // return 'Le détail de mon thumbnail...';

        $thumbnail_details = wp_get_attachment_image_src($object['featured_media']);

        // Ma clé thumbnail dans ma rest api va contenir:
        return [
            // une clé id
            'id' => $object['featured_media'],
            // une clé url
            'url' => $thumbnail_details[0],
            // une clé width
            'width' => $thumbnail_details[1],
            // une clé height
            'height' => $thumbnail_details[2],
        ];
    }

    public function metaField()
    {
        register_rest_field(
            'recipe',
            'meta',
            [
                // fonction à appeler lors d'un GET
                'get_callback' => [$this, 'getMetaCf'],
                //  fonction à appeler lors d'un POST
                'update_callback' => null,
                // Structure de ma donnée
                'schema' => null
            ]
        );
    }

    /**
     * Callback appelé lors d'un GET (ex: wp/v2/posts) pour modifier la clé "author"
     *
     * @param array $object Le post courant (tableau avec toutes les données du contenu AVANT la modification en cours)
     * @param string $field_name Le nom du champ/clé ciblé (ou du nouveau champ/clé créé).
     * @param WP_REST_Request $request Requête courante
     *
     * @return void
     */
    public function getMetaCf($object, $field_name, $request)
    {
        // Récupération des méta du contenu (= ici de la recette)

        // Je peux ainsi tout retourner d'un coup:
        // return get_post_meta($object['id']);

        // Seulement on constate qu'il y a quelques meta informations non pertinante pour notre besoin.

        // Nous allons donc filtrer les meta informations à envoyer :

        $all_post_meta = get_post_meta($object['id']);

        // Variable qui va contenir les meta que je vais renvoyer
        $array_return = [];

        foreach ($all_post_meta as $meta_name => $meta_value) {

            // Si le premier caractères du nom de la méta
            // commence par un _
            // Je choisi de ne pas l'envoyer
            if (substr($meta_name, 0, 1) == '_') {

                // Je demande à PHP de passer à l'itération suivante dès à présent
                continue;
            }

            $array_return[$meta_name] = $meta_value[0];
        }

        return $array_return;

    }

    public function taxoField()
    {
        register_rest_field(
            'recipe',
            'ingredient',
            [
                // fonction à appeler lors d'un GET
                'get_callback' => [$this, 'getTaxo'],
                //  fonction à appeler lors d'un POST
                'update_callback' => null,
                // Structure de ma donnée
                'schema' => null
            ]
        );

        register_rest_field(
            'recipe',
            'type',
            [
                // fonction à appeler lors d'un GET
                'get_callback' => [$this, 'getTaxo'],
                //  fonction à appeler lors d'un POST
                'update_callback' => null,
                // Structure de ma donnée
                'schema' => null
            ]
        );
    }

    /**
     * Callback appelé lors d'un GET (ex: wp/v2/posts) pour modifier la clé "author"
     *
     * @param array $object Le post courant (tableau avec toutes les données du contenu AVANT la modification en cours)
     * @param string $field_name Le nom du champ/clé ciblé (ou du nouveau champ/clé créé).
     * @param WP_REST_Request $request Requête courante
     *
     * @return void
     */
    public function getTaxo($object, $field_name, $request)
    {
        /*
            Pour rappel:

            Dans WP, chaque taxonomie est continue dans un objet WP_Term.

            Lorsque show_in_rest est présent dans la déclaration de la taxo,
            celle-ci apparait dans le détail du contenu.
            Seulement, c'est l'ID du term est qui envoyé et non pas son détail.
        */

        // Je créé ma variable qui va contenir le tableau à envoyer
        $array_return = [];

        // Je boucle sur chaque taxonomie actuellement fournie dans la REST API
        foreach ($object[$field_name] as $term_id) {

            // Je connais l'ID du term ainsi que son type.
            // Je demande donc à WordPress de me fournir l'objet WP_Term instancié
            // Ainsi je vais avoir accès au détail du WP_Term
            $wp_term = get_term($term_id, $field_name);

            $array_return[] = [
                'id' => $wp_term->term_id,
                'name' => $wp_term->name,
                'slug' => $wp_term->slug,
                'description' => $wp_term->description
            ];
        }

        return $array_return;
    }
}