<?php

/**
 * Config for WPGraphQL ACF
 *
 * @package wp-graphql-acf
 */

namespace Zngly\ACFM;

use GraphQL\Type\Definition\ResolveInfo;
use WP_Post_Type;
use WP_Taxonomy;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils as WpGraphqlUtils;
use Zngly\ACFM\Utils as ZnglyUtils;

// add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $post_type_object) {
//     if ($post_type_object->name === "my_post_type") {
//         if (isset($input['customInput'])) update_post_meta($post_id, 'custom_input', $input['customInput']);
//     }
// }, 10, 3);

/**
 * Mutations class.
 * Maps the above comment to acf fields

 */
class Mutations
{
    /**
     * @var Config <string> List of field groups and fields
     */
    protected $config;

    public function __construct()
    {
        add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $post_type_object) {
            $this->config = new Config();
            $this->post_object_mutation_action($post_id, $input, $post_type_object);
        }, 10, 3);

        add_action('graphql_media_item_mutation_update_additional_data', function ($media_item_id, $input, $post_type_object) {
            $this->config = new Config();
            $this->post_object_mutation_action($media_item_id, $input, $post_type_object);
        }, 10, 3);

        // add_filter('graphql_term_object_insert_term_args', function (array $insert_args, array $input, WP_Taxonomy $taxonomy, string $mutation_name) {

        //     $tets = 'asdf;klj';
        // }, 10, 4);

        // add_action('graphql_update_term', function (int $term_id, WP_Taxonomy $taxonomy, array $args, string $mutation_name, AppContext $context, ResolveInfo $info) {
        //     $test = 'test';

        //     $post_type_object = new WP_Post_Type('');
        //     $post_type_object->graphql_single_name = $taxonomy->name;

        //     $this->config = new Config();
        //     // $this->post_object_mutation_action($term_id, $args, $post_type_object);

        // }, 10, 6);
    }

    /**
     * updates the acf values from the inputs passed
     * @todo: refactor code so that it can recursively update nested fields
     * @todo: delete acf metadate if the value is null
     */
    protected function post_object_mutation_action($post_id, $input, WP_Post_Type $post_type_object)
    {
        $type_name = ucfirst($post_type_object->graphql_single_name);

        foreach ($this->config->field_groups as $field_group)
            foreach ($field_group['graphql_types'] as $graphql_type) {
                if ($type_name === $graphql_type) {
                    foreach ($field_group['fields'] as $field) {

                        // check for sub fields 
                        if (isset($field['sub_fields']) && is_array($field['sub_fields']) && isset($input[$field['graphql_name']])) {

                            // group update
                            if ($field['type'] === 'group') {
                                foreach ($field['sub_fields'] as $sub_field) {
                                    $graphql_name = $this->config::camel_case($sub_field['name']);

                                    if (isset($input[$field['graphql_name']][$graphql_name]) && $field['type'] === 'group') {
                                        $field_name = $field['name'] . "_" . $sub_field['name'];
                                        $value = $input[$field['graphql_name']][$graphql_name];

                                        self::updateField($post_id, $value, $field_name, $sub_field['type']);
                                    }
                                }
                            }

                            // repeater update
                            if ($field['type'] === 'repeater' && isset($input[$field['graphql_name']])) {
                                // delete all meta rows and set repeater field to 0
                                if (
                                    $input[$field['graphql_name']] == null ||
                                    $input[$field['graphql_name']] == "" ||
                                    (is_array($input[$field['graphql_name']]) && count($input[$field['graphql_name']]) == 0)
                                ) {
                                    $acf_field_values = get_field($field['key'], $post_id);

                                    foreach ($acf_field_values as $acf_key => $acf_value) {
                                        foreach ($field['sub_fields'] as $sub_field) {
                                            $graphql_name = $this->config::camel_case($sub_field['name']);

                                            if (isset($acf_field_values[$acf_key][$graphql_name])) {
                                                $key = $field['name'] . "_" . $acf_key . "_" . $sub_field['name'];
                                                delete_post_meta($post_id, $key);
                                                delete_post_meta($post_id, "_" . $key);
                                            }
                                        }
                                    }
                                    update_post_meta($post_id, $field['name'], 0);

                                    continue;
                                }

                                // set repeater fields
                                foreach ($input[$field['graphql_name']] as $r_key => $r_value) {
                                    foreach ($field['sub_fields'] as $sub_field) {
                                        $graphql_name = $this->config::camel_case($sub_field['name']);

                                        if (isset($r_value[$graphql_name])) {
                                            $post_key = $field['name'] . "_" . $r_key . "_" . $sub_field['name'];
                                            $post_value = $r_value[$graphql_name];

                                            update_post_meta($post_id, $post_key, $post_value);
                                            update_post_meta($post_id, "_" . $post_key, $sub_field['key']);
                                        }
                                    }
                                }

                                update_post_meta($post_id, $field['name'], count($input[$field['graphql_name']]));
                            }

                            // continue loop
                            continue;
                        }

                        // check if the field exists
                        if (isset($input[$field['graphql_name']]))
                            self::updateField($post_id, $input[$field['graphql_name']], $field['name'], $field['type']);
                    }
                }
            }
    }



    public static function updateField($post_id, $value, $field_name, $field_type)
    {
        // if file types are images or file, make sure an ID is passed
        // accept guid or ids
        if (in_array($field_type, ['image', 'file']))
            $value = ZnglyUtils::mapPostIdsFromGids($value);

        // accept guid or ids
        if ($field_type === 'post_object')
            $value = ZnglyUtils::mapPostIdsFromGids($value);

        /**
         * update a standalone acf field
         */
        update_post_meta($post_id, $field_name, $value);
    }
}
