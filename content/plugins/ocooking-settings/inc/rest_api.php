<?php

class oCookingRestApi
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'authorField']);
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
}