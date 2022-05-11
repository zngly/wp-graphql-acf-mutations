<?php

/**
 * Config for WPGraphQL ACF
 *
 * @package wp-graphql-acf
 */

namespace WPGraphQL\ACF\Mutations;

use WP_Post_Type;
use WPGraphQL\Utils\Utils as WpGraphqlUtils;

// add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $mutation_name, $context, $info) {
//     if ($mutation_name->name === "my_post_type") {
//         if (isset($input['customInput'])) update_post_meta($post_id, 'custom_input', $input['customInput']);
//     }
// }, 10, 5);

/**
 * PostObject class.
 * Maps the above comment to acf fields

 */
class PostObject
{
    /**
     * @var Config <string> List of field groups and fields
     */
    protected $config;

    public function __construct()
    {
        add_action('graphql_post_object_mutation_update_additional_data', function ($post_id, $input, $mutation, $context, $info) {
            $this->config = new Config();
            $this->post_object_mutation_action($post_id, $input, $mutation, $context, $info);
        }, 10, 5);
    }

    /**
     * updates the acf values from the inputs passed
     * @todo: refactor code so that it can recursively update nested fields
     * @todo: delete acf metadate if the value is null
     */
    protected function post_object_mutation_action($post_id, $input, WP_Post_Type $mutation, $context, $info)
    {
        $type_name = ucfirst($mutation->graphql_single_name);

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
                                        $key = $field['name'] . "_" . $sub_field['name'];
                                        $value = $input[$field['graphql_name']][$graphql_name];

                                        update_post_meta($post_id, $key, $value);
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
                        if (isset($input[$field['graphql_name']])) {
                            $curr_input = $input[$field['graphql_name']];
                            // if file types are images or file, make sure an ID is passed
                            // accept guid or ids
                            if (in_array($field['type'], ['image', 'file']))
                                $input[$field['graphql_name']] = self::mapPostIdsFromGids($curr_input);

                            // accept guid or ids
                            if ($field['type'] === 'post_object')
                                $input[$field['graphql_name']] = self::mapPostIdsFromGids($curr_input);

                            /**
                             * update a standalone acf field
                             */
                            update_post_meta($post_id, $field['name'], $input[$field['graphql_name']]);
                        }
                    }
                }
            }
    }

    public static function mapPostIdsFromGids($gids)
    {
        if (is_array($gids)) {
            $ids = [];
            foreach ($gids as $gid) {
                $ids[] = WpGraphqlUtils::get_database_id_from_id($gid);
            }
            return $ids;
        } else {
            return WpGraphqlUtils::get_database_id_from_id($gids);
        }
    }


    public static function getPostIdsFromGids($gids)
    {
        $postIds = [];
        foreach ($gids as $gid) {
            $postIds[] = WpGraphqlUtils::get_database_id_from_id($gid);
        }

        return $postIds;
    }

    public static function getPostIdFromGid($gids)
    {
        return WpGraphqlUtils::get_database_id_from_id($gids);
    }
}
