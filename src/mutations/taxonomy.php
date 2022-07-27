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

        self::action_terms_update();
    }

    public static function filter_terms_input()
    {
        add_filter('graphql_term_object_insert_term_args', function (array $insert_args, array $input, WP_Taxonomy $taxonomy, string $mutation_name) {

            $config = new Config();

            // loop through all the acf field groups and find the one that matches the taxonomy name
            foreach ($config->field_groups as $field_group) {
                foreach ($field_group['graphql_types'] as $graphql_type) {
                    if ($taxonomy->labels->singular_name === $graphql_type) {
                        foreach ($field_group['fields'] as $acf_field) {
                            $acf_graphql_name = $acf_field['graphql_name'];


                            if (isset($input[$acf_graphql_name]))
                                $insert_args[$acf_graphql_name] = $input[$acf_graphql_name];
                        }
                    }
                }
            }

            return $insert_args;
        }, 10, 4);
    }

    public static function action_terms_update()
    {
        add_action('graphql_update_term', function (int $term_id, WP_Taxonomy $taxonomy, array $args, string $mutation_name, AppContext $context, ResolveInfo $info) {
            Mutations::updater($term_id, $args, $taxonomy->name);
        }, 10, 6);
    }
}
