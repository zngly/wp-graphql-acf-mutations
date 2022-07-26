<?php


namespace Zngly\ACFM\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use WP_Taxonomy;
use WPGraphQL\AppContext;
use Zngly\ACFM\Config;
use Zngly\ACFM\Mutations;

class Taxonomy
{

    public function __construct()
    {
        self::filter_terms_input();

        // add_action('graphql_update_term', function (int $term_id, WP_Taxonomy $taxonomy, array $args, string $mutation_name, AppContext $context, ResolveInfo $info) {
        // $test = 'test';

        // $post_type_object = new WP_Post_Type('');
        // $post_type_object->graphql_single_name = $taxonomy->name;

        // $Mutations::post_object_mutation_action($term_id, $args, $post_type_object,  new Config());

        // }, 10, 6);
    }

    public static function filter_terms_input()
    {
        add_filter('graphql_term_object_insert_term_args', function (array $insert_args, array $input, WP_Taxonomy $taxonomy, string $mutation_name) {

            $tets = 'asdf;klj';
        }, 10, 4);
    }
}
