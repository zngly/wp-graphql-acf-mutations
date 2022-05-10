<?php

/**
 * Config for WPGraphQL ACF
 *
 * @package wp-graphql-acf
 */

namespace WPGraphQL\ACF\Mutations;

use WPGraphQL\Utils\Utils;
use WPGraphQL\ACF\LocationRules;

/**
 * Config class.
 */
class Config
{
    /**
     * Stores the location rules for back compat
     * @var array
     */
    public $location_rules = [];

    /**
     * @var array List of field groups and fields
     */
    public $field_groups;


    public function __construct()
    {

        /**
         * Gets the location rules for backward compatibility.
         *
         * This allows for ACF Field Groups that were registered before the "graphql_types"
         * field was respected can still work with the old GraphQL Schema rules that mapped
         * from the ACF Location rules.
         */
        $this->location_rules = $this->generate_location_rules();

        /**
         * Gets the supported field groups combined with the acf fields
         */
        $this->field_groups = $this->get_field_groups();
    }

    /**
     * Determines whether a field group should be exposed to the GraphQL Schema. By default, field
     * groups will not be exposed to GraphQL.
     *
     * @param array $field_group Undocumented.
     *
     * @return bool
     */
    protected function should_field_group_show_in_graphql($field_group)
    {

        /**
         * By default, field groups will not be exposed to GraphQL.
         */
        $show = false;

        /**
         * If
         */
        if (isset($field_group['show_in_graphql']) && true === (bool) $field_group['show_in_graphql']) {
            $show = true;
        }

        /**
         * Determine conditions where the GraphQL Schema should NOT be shown in GraphQL for
         * root groups, not nested groups with parent.
         */
        if (!isset($field_group['parent'])) {
            if (
                (isset($field_group['active']) && true != $field_group['active']) ||
                (empty($field_group['location']) || !is_array($field_group['location']))
            ) {
                $show = false;
            }
        }

        /**
         * Whether a field group should show in GraphQL.
         */
        return $show;
    }

    /**
     * Take a string and converts it to camel case
     *
     * @todo: This may be a good utility to add to WPGraphQL Core? May even have something already?
     *
     * @param string $str      Unknown.
     * @param array  $no_strip Unknown.
     *
     * @return mixed|null|string|string[]
     */
    public static function camel_case($str, array $no_strip = [])
    {
        // non-alpha and non-numeric characters become spaces.
        $str = preg_replace('/[^a-z0-9' . implode('', $no_strip) . ']+/i', ' ', $str);
        $str = trim($str);
        // Lowercase the string
        $str = strtolower($str);
        // uppercase the first character of each word.
        $str = ucwords($str);
        // Replace spaces
        $str = str_replace(' ', '', $str);
        // Lowecase first letter
        $str = lcfirst($str);

        return $str;
    }

    /**
     * Gets the location rules
     * @return array
     */
    protected function generate_location_rules()
    {

        if (!empty($this->location_rules) && isset($this->location_rules) && count($this->location_rules) > 0)
            return $this->location_rules;

        $field_groups = acf_get_field_groups();
        if (empty($field_groups) || !is_array($field_groups)) {
            return [];
        }


        if (empty($field_groups) || !is_array($field_groups)) {
            return [];
        }

        $rules = [];

        // Each field group that doesn't have GraphQL Types explicitly set should get the location
        // rules interpreted.
        foreach ($field_groups as $field_group) {
            if (!isset($field_group['graphql_types']) || !is_array($field_group['graphql_types'])) {
                $rules[] = $field_group;
            }
        }

        if (empty($rules)) {
            return [];
        }

        // If there are field groups with no graphql_types field set, inherit the rules from
        // ACF Location Rules
        $rules = new LocationRules();
        $rules->determine_location_rules();
        return $rules->get_rules();
    }

    /**
     * Gets the acf fields, with location and graphql types
     * @todo: refactor code so that it can recursively update nested fields
     */
    protected function get_field_groups()
    {
        // store field groups
        $updated_field_groups = [];


        //   Get all the field groups
        $field_groups = acf_get_field_groups();

        /**
         * If there are no acf field groups, bail
         */
        if (empty($field_groups) || !is_array($field_groups)) {
            return;
        }

        foreach ($field_groups as $field_group) {

            $field_group_name = isset($field_group['graphql_field_name']) ? $field_group['graphql_field_name'] : $field_group['title'];
            $field_group_name = Utils::format_field_name($field_group_name);

            $manually_set_graphql_types = isset($field_group['map_graphql_types_from_location_rules']) ? (bool) $field_group['map_graphql_types_from_location_rules'] : false;
            if (false === $manually_set_graphql_types)
                if (!isset($field_group['graphql_types']) || empty($field_group['graphql_types'])) {
                    $field_group['graphql_types'] = [];
                    $location_rules = $this->location_rules;
                    if (isset($location_rules[$field_group_name]))
                        $field_group['graphql_types'] = $location_rules[$field_group_name];
                }

            if (!is_array($field_group['graphql_types']) || empty($field_group['graphql_types']))
                continue;

            /**
             * Determine if the field group should be exposed
             * to graphql
             */
            if (!$this->should_field_group_show_in_graphql($field_group))
                continue;

            $graphql_types = array_unique($field_group['graphql_types']);
            $graphql_types = array_filter($graphql_types);
            $field_group['graphql_types'] = $graphql_types;

            $fields = acf_get_fields($field_group['key']);

            foreach ($fields as $key => $_value) {
                $fields[$key]['graphql_name'] = self::camel_case($fields[$key]['name']);

                $description = isset($fields[$key]['instructions']) && $fields[$key]['instructions'] != "" ? $fields[$key]['instructions'] . " | " : "";
                $description .= "ACF Type: {$fields[$key]['type']} | Graphql Type: ";
                $fields[$key]['description'] = $description;
            }
            $field_group['fields'] = $fields;

            $updated_field_groups[] = $field_group;
        }
        return $updated_field_groups;
    }

    public function get_acf_type(string $type_name, array $config, array $field_group = null)
    {
        $final_type = null;
        $acf_type  = isset($config['type']) ? $config['type'] : null;

        if (empty($acf_type))
            return null;

        $field_type_name = "";
        if (!empty($field_group))
            $field_type_name = $type_name . '_' . self::camel_case($field_group['graphql_field_name']) . '_' . ucfirst(self::camel_case($config['name']));

        switch ($acf_type) {
            case 'button_group':
            case 'color_picker':
            case 'email':
            case 'text':
            case 'message':
            case 'oembed':
            case 'password':
            case 'wysiwyg':
            case 'url':
                // Even though Selects and Radios in ACF can _technically_ be an integer
                // we're choosing to always cast as a string because with
                // GraphQL we can't return different types
                $final_type = 'String';
                break;
            case 'textarea':
                $final_type = 'String';
                break;
            case 'select':

                /**
                 * If the select field is configured to not allow multiple values
                 * the field will return a string, but if it is configured to allow
                 * multiple values it will return a list of strings, and an empty array
                 * if no values are set.
                 *
                 * @see: https://github.com/wp-graphql/wp-graphql-acf/issues/25
                 */
                if (empty($config['multiple']))
                    if ('array' === $config['return_format'])
                        $final_type = ['list_of' => 'String'];
                    else
                        $final_type = 'String';
                else
                    $final_type = ['list_of' => 'String'];

                break;
            case 'radio':
                $final_type = 'String';
                break;
            case 'number':
            case 'range':
                $final_type = 'Float';
                break;
            case 'true_false':
                $final_type = 'Boolean';
                break;
            case 'date_picker':
            case 'time_picker':
            case 'date_time_picker':
                $final_type = 'String';
                break;
            case 'relationship':
                if (isset($config['post_type']) && is_array($config['post_type'])) {
                    // if ($this->type_registry->get_type($field_type_name) == $field_type_name)
                    //     $type = $field_type_name;
                    // else {
                    $type_names = [];
                    foreach ($config['post_type'] as $post_type)
                        if (in_array($post_type, get_post_types(['show_in_graphql' => true]), true))
                            $type_names[$post_type] = get_post_type_object($post_type)->graphql_single_name;

                    if (empty($type_names))
                        $type = 'PostObjectUnion';
                    else
                        $type = $field_type_name;
                    // }
                } else
                    $type = 'PostObjectUnion';

                $final_type = ['list_of' => $type];
                break;
            case 'page_link':
            case 'post_object':
                isset($config['post_type']) && is_array($config['post_type'])
                    ? $type = $field_type_name
                    : $type = 'PostObjectUnion';

                if (isset($config['return_format']) && $config['return_format'] === 'id')
                    $type = "ID";

                // If the field is allowed to be a multi select
                if ($config['multiple'] === 1)
                    $type = ['list_of' => $type];

                $final_type = $type;
                break;
            case 'link':
                $final_type = 'AcfLink';
                break;
            case 'image':
            case 'file':
                $type = 'ID';

                if (isset($config['multiple']) && 1 === $config['multiple'])
                    $type = ['list_of' => $type];

                $final_type = $type;
                break;
            case 'checkbox':
                $final_type = ['list_of' => 'String'];
                break;
            case 'gallery':
                $final_type = ['list_of' => 'MediaItem'];
                break;
            case 'user':
                $type = 'User';

                if (isset($config['multiple']) &&  1 === $config['multiple'])
                    $type = ['list_of' => $type];

                $final_type = $type;
                break;
            case 'taxonomy':

                $type = 'TermObjectUnion';

                if (isset($config['taxonomy'])) {
                    $tax_object = get_taxonomy($config['taxonomy']);
                    if (isset($tax_object->graphql_single_name)) {
                        $type = $tax_object->graphql_single_name;
                    }
                }

                $is_multiple = isset($config['field_type']) && in_array($config['field_type'], array('checkbox', 'multi_select'));

                $final_type = $is_multiple ? ['list_of' => $type] : $type;
                break;

                // Accordions are not represented in the GraphQL Schema.
            case 'accordion':
                break;
            case 'group':
                $field_type_name = $type_name . '_' . ucfirst(self::camel_case($field_group['graphql_field_name'])) . '_' . ucfirst(self::camel_case($config['name']));
                $final_type = $field_type_name . "_GroupInput";
                break;
            case 'google_map':
                $final_type = $field_type_name;
                break;
            case 'repeater':
                $field_type_name = $type_name . '_' . ucfirst(self::camel_case($field_group['graphql_field_name'])) . '_' . ucfirst(self::camel_case($config['name']));
                $final_type = ['list_of' => $field_type_name . "_RepeaterInput"];
                break;
            case 'flexible_content':
                if (!empty($config['layouts']) && is_array($config['layouts']))
                    $final_type = ['list_of' => $field_type_name];
                break;
            default:
                $final_type = null;
                break;
        }

        return $final_type;
    }
}
